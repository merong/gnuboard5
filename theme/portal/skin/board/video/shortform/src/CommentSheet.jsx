import { useEffect, useMemo, useRef, useState } from 'react';
import { formatWhen, goLogin, postForm } from './api.js';
import { IconClose, IconSend, IconComment } from './icons.jsx';

function groupThreads(rows) {
  const order = [];
  const map = new Map();
  (rows || []).forEach((r) => {
    const key = r.thread != null ? String(r.thread) : `id-${r.wr_id}`;
    if (!map.has(key)) {
      const g = { key, root: null, replies: [] };
      map.set(key, g);
      order.push(g);
    }
    const g = map.get(key);
    if (!r.reply) g.root = r;
    else g.replies.push(r);
  });
  return order
    .map((g) => {
      if (g.root) return g;
      if (!g.replies.length) return null;
      return { ...g, root: g.replies[0], replies: g.replies.slice(1) };
    })
    .filter(Boolean);
}

function CommentRow({ row, reply, onReply }) {
  return (
    <article className={`sf-cmt${reply ? ' is-reply' : ''}`}>
      <div className="sf-cmt-avatar" aria-hidden="true">{(row.name || '?').slice(0, 1)}</div>
      <div className="sf-cmt-body">
        <p className="sf-cmt-name">{row.name}</p>
        <p className="sf-cmt-text">{row.content}</p>
        <p className="sf-cmt-meta">
          <span>{formatWhen(row.datetime)}</span>
          <button type="button" className="sf-cmt-reply" onClick={() => onReply(row)}>답글</button>
        </p>
      </div>
    </article>
  );
}

export default function CommentSheet({ item, commentUrl, loginUrl, isMember, onClose, onCount }) {
  const [rows, setRows] = useState([]);
  const [loading, setLoading] = useState(true);
  const [text, setText] = useState('');
  const [busy, setBusy] = useState(false);
  const [replyTo, setReplyTo] = useState(null);
  const [open, setOpen] = useState({});
  const inputRef = useRef(null);

  useEffect(() => {
    if (!item || !commentUrl) return undefined;
    let stop = false;
    setLoading(true);
    setReplyTo(null);
    const url = new URL(commentUrl, window.location.origin);
    url.searchParams.set('sf_ajax', 'comment');
    url.searchParams.set('action', 'list');
    url.searchParams.set('sf_wr_id', String(item.wr_id));
    fetch(url.toString(), { credentials: 'same-origin', headers: { Accept: 'application/json' } })
      .then((r) => r.json())
      .then((data) => {
        if (stop) return;
        setRows(Array.isArray(data.comments) ? data.comments : []);
        if (onCount && data.count != null) onCount(data.count);
      })
      .catch(() => {})
      .finally(() => { if (!stop) setLoading(false); });
    return () => { stop = true; };
  }, [item && item.wr_id, commentUrl]);

  const threads = useMemo(() => groupThreads(rows), [rows]);

  const startReply = (row) => {
    if (!isMember) {
      goLogin(loginUrl, '회원만 작성 가능합니다.');
      return;
    }
    setReplyTo(row);
    window.setTimeout(() => { if (inputRef.current) inputRef.current.focus(); }, 0);
  };

  const submit = (e) => {
    e.preventDefault();
    if (!isMember) {
      goLogin(loginUrl, '회원만 작성 가능합니다.');
      return;
    }
    const body = text.trim();
    if (!body || busy) return;
    setBusy(true);
    const fields = { sf_ajax: 'comment', action: 'write', sf_wr_id: item.wr_id, wr_content: body };
    if (replyTo && replyTo.wr_id) fields.comment_id = replyTo.wr_id;
    postForm(commentUrl, fields)
      .then((data) => {
        if (data && data.need_login) {
          goLogin(data.login_url || loginUrl, data.error || '회원만 작성 가능합니다.');
          return;
        }
        if (!data || data.ok === false) {
          window.alert((data && data.error) || '댓글을 남기지 못했습니다.');
          return;
        }
        setRows(Array.isArray(data.comments) ? data.comments : []);
        setText('');
        if (replyTo && replyTo.thread != null) {
          setOpen((prev) => ({ ...prev, [String(replyTo.thread)]: true }));
        }
        setReplyTo(null);
        if (onCount && data.count != null) onCount(data.count);
      })
      .catch(() => window.alert('댓글을 남기지 못했습니다.'))
      .finally(() => setBusy(false));
  };

  return (
    <div className="sf-sheet" role="dialog" aria-label="댓글">
      <div className="sf-sheet-head">
        <strong>댓글 {rows.length}</strong>
        <button type="button" className="sf-sheet-x" onClick={onClose} aria-label="댓글 닫기"><IconClose size={22} /></button>
      </div>
      <div className="sf-sheet-list">
        {loading ? <p className="sf-sheet-empty">불러오는 중</p> : null}
        {!loading && rows.length === 0 ? (
          <div className="sf-sheet-empty">
            <IconComment />
            <p>첫 댓글을 남겨 보세요</p>
          </div>
        ) : null}
        {threads.map((g) => {
          const opened = !!open[g.key];
          return (
            <div key={g.key} className="sf-cmt-thread">
              <CommentRow row={g.root} onReply={startReply} />
              {g.replies.length > 0 && !opened ? (
                <button
                  type="button"
                  className="sf-cmt-more"
                  onClick={() => setOpen((prev) => ({ ...prev, [g.key]: true }))}
                >
                  답글 {g.replies.length}개 보기
                </button>
              ) : null}
              {opened ? g.replies.map((r) => (
                <CommentRow key={r.wr_id} row={r} reply onReply={startReply} />
              )) : null}
              {opened && g.replies.length > 0 ? (
                <button
                  type="button"
                  className="sf-cmt-more"
                  onClick={() => setOpen((prev) => ({ ...prev, [g.key]: false }))}
                >
                  답글 숨기기
                </button>
              ) : null}
            </div>
          );
        })}
      </div>
      {isMember ? (
        <form className="sf-sheet-form" onSubmit={submit}>
          {replyTo ? (
            <div className="sf-sheet-replying">
              <span>{replyTo.name}에게 답글</span>
              <button type="button" onClick={() => setReplyTo(null)} aria-label="답글 취소"><IconClose size={16} /></button>
            </div>
          ) : null}
          <div className="sf-sheet-compose">
            <input
              ref={inputRef}
              type="text"
              value={text}
              onChange={(e) => setText(e.target.value)}
              placeholder={replyTo ? `${replyTo.name}에게 답글...` : '댓글 추가...'}
              maxLength={2000}
              disabled={busy}
            />
            <button type="submit" className="sf-sheet-send" disabled={busy || !text.trim()} aria-label="댓글 등록"><IconSend /></button>
          </div>
        </form>
      ) : (
        <button
          type="button"
          className="sf-sheet-login"
          onClick={() => goLogin(loginUrl, '회원만 작성 가능합니다.')}
        >
          로그인하고 댓글 달기
        </button>
      )}
    </div>
  );
}
