import { useCallback, useMemo, useState } from 'react';
import Grid from './Grid.jsx';
import Player from './Player.jsx';

export default function App({ items = [], startIndex = 0 }) {
  const list = Array.isArray(items) ? items : [];
  const playable = useMemo(() => list.filter((it) => it && it.uid), [list]);
  const [open, setOpen] = useState(false);
  const [playerIndex, setPlayerIndex] = useState(0);

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

  const onClose = useCallback(() => setOpen(false), []);

  return (
    <div className="sf-root">
      <Grid items={list} onOpen={onOpen} />
      {open && playable.length > 0 ? (
        <Player
          items={playable}
          startIndex={Math.min(playerIndex, playable.length - 1)}
          onClose={onClose}
        />
      ) : null}
    </div>
  );
}
