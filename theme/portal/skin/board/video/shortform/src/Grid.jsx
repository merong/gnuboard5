import { formatDuration } from './utils.js';

export default function Grid({ items, onOpen }) {
  return (
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
              <span className="sf-card-shade" />
              <span className="sf-card-title">{item.subject || ''}</span>
            </span>
          </button>
        );
      })}
    </div>
  );
}
