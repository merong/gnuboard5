import { useEffect, useRef } from 'react';
import { formatDuration } from './utils.js';

export default function Grid({ items, onOpen, onNeedMore, loading, hasMore }) {
  const sentinelRef = useRef(null);

  useEffect(() => {
    const el = sentinelRef.current;
    if (!el || !onNeedMore) return undefined;
    const io = new IntersectionObserver((entries) => {
      if (entries[0] && entries[0].isIntersecting) onNeedMore();
    }, { root: null, rootMargin: '480px 0px', threshold: 0 });
    io.observe(el);
    return () => io.disconnect();
  }, [onNeedMore, items.length]);

  return (
    <div className="sf-grid-wrap">
      <div className="sf-grid" role="list">
        {items.map((item, index) => {
          const playable = Boolean(item.uid);
          const dur = formatDuration(item.duration);
          return (
            <button
              key={item.wr_id || index}
              type="button"
              role="listitem"
              className={`sf-card${playable ? '' : ' is-dead'}`}
              onClick={() => onOpen(index)}
            >
              <span className="sf-card-media">
                {item.thumb ? (
                  <img src={item.thumb} alt="" loading="lazy" />
                ) : (
                  <span className="sf-card-empty" />
                )}
                {playable ? (
                  <span className="sf-card-play" aria-hidden="true">
                    <svg viewBox="0 0 24 24" width="22" height="22">
                      <path fill="currentColor" d="M8 5v14l11-7z" />
                    </svg>
                  </span>
                ) : (
                  <span className="sf-card-dead">바로가기</span>
                )}
                {dur ? <span className="sf-card-dur">{dur}</span> : null}
                <span className="sf-card-stats">
                  <span>♥ {item.good || 0}</span>
                  <span>댓글 {item.comment || 0}</span>
                </span>
                <span className="sf-card-shade" />
                <span className="sf-card-title">{item.subject || ''}</span>
              </span>
            </button>
          );
        })}
      </div>
      <div ref={sentinelRef} className="sf-sentinel" aria-hidden="true" />
      {loading ? <p className="sf-more">더 불러오는 중</p> : null}
      {!hasMore && items.length > 0 ? <p className="sf-more is-end">마지막 영상입니다</p> : null}
    </div>
  );
}
