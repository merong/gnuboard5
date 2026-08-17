import { useEffect, useState } from 'react';
import { Swiper, SwiperSlide } from 'swiper/react';
import { Keyboard, Mousewheel } from 'swiper/modules';
import 'swiper/css';
import VideoSlide from './VideoSlide.jsx';
import { isMobileMq } from './utils.js';

export default function Player({ items, startIndex, onClose }) {
  const [mobile, setMobile] = useState(() => isMobileMq().matches);
  const [active, setActive] = useState(startIndex);
  const [muted, setMuted] = useState(true);

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
    const prev = document.body.style.overflow;
    document.body.style.overflow = 'hidden';
    const onKey = (e) => {
      if (e.key === 'Escape') onClose();
    };
    window.addEventListener('keydown', onKey);
    return () => {
      document.body.style.overflow = prev;
      window.removeEventListener('keydown', onKey);
    };
  }, [onClose]);

  const onOverlay = (e) => {
    if (!mobile && e.target === e.currentTarget) onClose();
  };

  return (
    <div
      className={`sf-overlay${mobile ? ' is-mobile' : ' is-pc'}`}
      onClick={onOverlay}
      role="dialog"
      aria-modal="true"
      aria-label="숏폼 재생"
    >
      <div className="sf-frame">
        <button type="button" className="sf-close" onClick={onClose} aria-label="닫기">
          닫기
        </button>
        <Swiper
          className="sf-swiper"
          direction="vertical"
          slidesPerView={1}
          spaceBetween={0}
          speed={280}
          initialSlide={startIndex}
          mousewheel={{ forceToAxis: true, releaseOnEdges: true }}
          keyboard={{ enabled: true }}
          modules={[Mousewheel, Keyboard]}
          onSlideChange={(sw) => setActive(sw.activeIndex)}
        >
          {items.map((item, i) => (
            <SwiperSlide key={item.wr_id || i}>
              <VideoSlide
                item={item}
                active={i === active}
                muted={muted}
                onToggleMute={() => setMuted((v) => !v)}
              />
            </SwiperSlide>
          ))}
        </Swiper>
      </div>
    </div>
  );
}
