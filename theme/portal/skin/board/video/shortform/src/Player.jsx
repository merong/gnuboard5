import { useEffect, useState } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Keyboard, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import VideoSlide from './VideoSlide.jsx';
import CommentSheet from './CommentSheet.jsx';
import { isMobileMq } from './utils.js';
import { IconClose } from './icons.jsx';

export default function Player({ items, startIndex, onClose, onNeedMore, onItemPatch, api }) {
  const [mobile, setMobile] = useState(() => isMobileMq().matches);
  const [active, setActive] = useState(startIndex);
  const [muted, setMuted] = useState(true);
  const [sheet, setSheet] = useState(false);
  const current = items[active] || items[0];

  useEffect(() => {
    const mq = isMobileMq();
    const onChange = (e) => setMobile(e.matches);
    if (mq.addEventListener) mq.addEventListener('change', onChange);
    else mq.addListener(onChange);
    return () => {
      if (mq.removeEventListener) mq.removeEventListener('change', onChange);
      else mq.removeListener(onChange);
    };
  }, []);

  useEffect(() => {
    const prevBody = document.body.style.overflow;
    const prevHtml = document.documentElement.style.overflow;
    document.body.style.overflow = 'hidden';
    document.documentElement.style.overflow = 'hidden';
    document.body.classList.add('sf-player-open');
    const onKey = (e) => {
      if (e.key === 'Escape') {
        if (sheet) setSheet(false);
        else onClose();
      }
    };
    window.addEventListener('keydown', onKey);
    return () => {
      document.body.style.overflow = prevBody;
      document.documentElement.style.overflow = prevHtml;
      document.body.classList.remove('sf-player-open');
      window.removeEventListener('keydown', onKey);
    };
  }, [onClose, sheet]);

  const onOverlay = (e) => {
    if (!mobile && e.target === e.currentTarget) onClose();
  };

  return (
    <div
      className={`sf-overlay${mobile ? ' is-mobile' : ' is-pc'}${sheet ? ' is-sheet' : ''}`}
      onClick={onOverlay}
      role="dialog"
      aria-modal="true"
      aria-label="숏폼 재생"
    >
      <div className="sf-frame">
        <button type="button" className="sf-close" onClick={onClose} aria-label="닫기"><IconClose /></button>
        <Swiper
          className="sf-swiper"
          direction="vertical"
          slidesPerView={1}
          spaceBetween={0}
          speed={280}
          initialSlide={startIndex}
          observer
          observeParents
          mousewheel={{ forceToAxis: true, releaseOnEdges: true }}
          keyboard={{ enabled: true }}
          modules={[Mousewheel, Keyboard]}
          onSlideChange={(sw) => {
            setActive(sw.activeIndex);
            setSheet(false);
            if (onNeedMore && sw.activeIndex >= items.length - 1) onNeedMore();
          }}
          onReachEnd={() => { if (onNeedMore) onNeedMore(); }}
        >
          {items.map((item, i) => (
            <SwiperSlide key={item.wr_id || i}>
              <VideoSlide
                item={item}
                active={i === active}
                muted={muted}
                onToggleMute={() => setMuted((v) => !v)}
                api={api}
                onLike={(patch) => onItemPatch(item.wr_id, patch)}
                onOpenComments={() => setSheet(true)}
              />
            </SwiperSlide>
          ))}
        </Swiper>
        {sheet && current ? (
          <>
          <button type="button" className="sf-sheet-dim" onClick={() => setSheet(false)} aria-label="댓글 닫기" />
          <CommentSheet
            item={current}
            commentUrl={api.commentUrl}
            loginUrl={api.loginUrl}
            isMember={api.isMember}
            onClose={() => setSheet(false)}
            onCount={(n) => onItemPatch(current.wr_id, { comment: n })}
          />
          </>
        ) : null}
      </div>
    </div>
  );
}
