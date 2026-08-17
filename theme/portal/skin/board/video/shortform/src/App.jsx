import { useCallback, useMemo, useRef, useState } from 'react';
import Grid from './Grid.jsx';
import Player from './Player.jsx';

function mergeItems(prev, next) {
  const seen = new Set(prev.map((it) => String(it.wr_id)));
  const add = (Array.isArray(next) ? next : []).filter((it) => it && !seen.has(String(it.wr_id)));
  return add.length ? prev.concat(add) : prev;
}

export default function App({
  items = [],
  startIndex = 0,
  ajaxUrl = '',
  goodUrl = '',
  commentUrl = '',
  loginUrl = '',
  isMember = false,
  page: initialPage = 1,
  totalPage: initialTotal = 1,
}) {
  const [list, setList] = useState(() => (Array.isArray(items) ? items : []));
  const [page, setPage] = useState(Number(initialPage) || 1);
  const [totalPage, setTotalPage] = useState(Number(initialTotal) || 1);
  const [loading, setLoading] = useState(false);
  const [open, setOpen] = useState(false);
  const [playerIndex, setPlayerIndex] = useState(0);
  const loadingRef = useRef(false);
  const playable = useMemo(() => list.filter((it) => it && it.uid), [list]);
  const hasMore = page < totalPage && Boolean(ajaxUrl);
  const api = { goodUrl, commentUrl, loginUrl, isMember };

  const loadMore = useCallback(() => {
    if (!ajaxUrl || loadingRef.current || page >= totalPage) return;
    loadingRef.current = true;
    setLoading(true);
    const url = new URL(ajaxUrl, window.location.origin);
    url.searchParams.set('sf_ajax', '1');
    url.searchParams.set('page', String(page + 1));
    fetch(url.toString(), { credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((data) => {
        if (!data || data.ok === false) return;
        setList((prev) => mergeItems(prev, data.items));
        if (data.page) setPage(Number(data.page) || page + 1);
        else setPage((p) => p + 1);
        if (data.total_page) setTotalPage(Number(data.total_page) || totalPage);
      })
      .catch(() => {})
      .finally(() => { loadingRef.current = false; setLoading(false); });
  }, [ajaxUrl, page, totalPage]);

  const onItemPatch = useCallback((wrId, patch) => {
    setList((prev) => prev.map((it) => (String(it.wr_id) === String(wrId) ? { ...it, ...patch } : it)));
  }, []);

  const onOpen = useCallback((gridIndex) => {
    const item = list[gridIndex];
    if (!item) return;
    if (!item.uid) {
      if (item.href) window.location.href = item.href;
      return;
    }
    const idx = playable.findIndex((p) => String(p.wr_id) === String(item.wr_id));
    setPlayerIndex(idx >= 0 ? idx : 0);
    setOpen(true);
  }, [list, playable]);

  return (
    <div className="sf-root">
      <Grid items={list} onOpen={onOpen} onNeedMore={hasMore ? loadMore : undefined} loading={loading} hasMore={hasMore} />
      {open && playable.length > 0 ? (
        <Player
          items={playable}
          startIndex={Math.min(playerIndex, playable.length - 1)}
          onClose={() => setOpen(false)}
          onNeedMore={hasMore ? loadMore : undefined}
          onItemPatch={onItemPatch}
          api={api}
        />
      ) : null}
    </div>
  );
}
