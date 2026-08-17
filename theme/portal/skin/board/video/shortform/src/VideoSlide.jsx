import { useEffect, useRef, useState } from 'react';
import Hls from 'hls.js';
import { formatDuration, formatHit, hlsUrl, iframeUrl } from './utils.js';

export default function VideoSlide({ item, active, muted, onToggleMute }) {
  const videoRef = useRef(null);
  const hlsRef = useRef(null);
  const [useIframe, setUseIframe] = useState(false);
  const [paused, setPaused] = useState(false);
  const src = hlsUrl(item);
  const frame = iframeUrl(item);

  useEffect(() => {
    setUseIframe(false);
  }, [item.uid, item.customer]);

  useEffect(() => {
    if (!active) setPaused(false);
  }, [active]);

  useEffect(() => {
    const video = videoRef.current;
    if (!video || useIframe || !src) return undefined;

    const fail = () => setUseIframe(true);

    if (video.canPlayType('application/vnd.apple.mpegurl')) {
      video.src = src;
    } else if (Hls.isSupported()) {
      const hls = new Hls({ enableWorker: true, lowLatencyMode: false });
      hlsRef.current = hls;
      hls.loadSource(src);
      hls.attachMedia(video);
      hls.on(Hls.Events.ERROR, (_e, data) => {
        if (data?.fatal) fail();
      });
    } else {
      fail();
    }

    return () => {
      if (hlsRef.current) {
        hlsRef.current.destroy();
        hlsRef.current = null;
      }
      video.removeAttribute('src');
      video.load();
    };
  }, [src, useIframe]);

  useEffect(() => {
    const video = videoRef.current;
    if (!video || useIframe) return;
    video.muted = muted;
    if (active && !paused) {
      const p = video.play();
      if (p && typeof p.catch === 'function') p.catch(() => {});
    } else {
      video.pause();
    }
  }, [active, muted, useIframe, paused]);

  const onSlideClick = (e) => {
    if (e.target.closest('a, button')) return;
    setPaused((v) => !v);
  };

  const meta = [item.name, formatDuration(item.duration), item.hit != null ? `조회 ${formatHit(item.hit)}` : '']
    .filter(Boolean)
    .join(' · ');

  const title = item.subject || '';

  return (
    <div className="sf-slide" onClick={onSlideClick}>
      {item.thumb ? (
        <div className="sf-slide-bg" style={{ backgroundImage: `url(${item.thumb})` }} />
      ) : null}
      {useIframe && frame && active && !paused ? (
        <iframe
          className="sf-iframe"
          src={frame}
          title={item.subject || 'video'}
          allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
          allowFullScreen
        />
      ) : (
        <video
          ref={videoRef}
          className="sf-video"
          playsInline
          muted={muted}
          loop
          poster={item.thumb || undefined}
          preload={active ? 'auto' : 'metadata'}
        />
      )}
      <div className="sf-slide-ui">
        <div className="sf-slide-meta">
          {item.href ? (
            <a className="sf-slide-title" href={item.href}>{title}</a>
          ) : (
            <p className="sf-slide-title">{title}</p>
          )}
          {meta ? <p className="sf-slide-sub">{meta}</p> : null}
        </div>
        <button
          type="button"
          className="sf-mute"
          onClick={onToggleMute}
          aria-label={muted ? '소리 켜기' : '소리 끄기'}
        >
          {muted ? '소리 켜기' : '소리 끄기'}
        </button>
      </div>
    </div>
  );
}
