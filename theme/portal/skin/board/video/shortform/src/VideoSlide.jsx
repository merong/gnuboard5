import { useEffect, useRef, useState } from 'react';
import Hls from 'hls.js';
import { formatDuration, formatHit, hlsUrl, iframeUrl } from './utils.js';
import { formatCount, goLogin, postForm } from './api.js';
import { IconHeart, IconComment, IconMuted, IconSound, IconFullscreen } from './icons.jsx';
import { enterFs, exitFs, isFsFor, onFsChange } from './fullscreen.js';

function fmtTime(sec) {
  const n = Math.max(0, Math.floor(Number(sec) || 0));
  const m = Math.floor(n / 60);
  const s = n % 60;
  return `${m}:${String(s).padStart(2, '0')}`;
}

export default function VideoSlide({
  item, active, muted, onToggleMute, onLike, onOpenComments, api,
}) {
  const slideRef = useRef(null);
  const videoRef = useRef(null);
  const iframeRef = useRef(null);
  const hlsRef = useRef(null);
  const hideRef = useRef(null);
  const seekingRef = useRef(false);
  const holdRef = useRef(false);
  const [useIframe, setUseIframe] = useState(false);
  const [paused, setPaused] = useState(false);
  const [showCtrl, setShowCtrl] = useState(false);
  const [now, setNow] = useState(0);
  const [dur, setDur] = useState(Number(item.duration) || 0);
  const [isFs, setIsFs] = useState(false);
  const src = hlsUrl(item);
  const frame = iframeUrl(item);

  const bumpCtrl = () => {
    setShowCtrl(true);
    window.clearTimeout(hideRef.current);
    hideRef.current = window.setTimeout(() => {
      if (!seekingRef.current && !holdRef.current) setShowCtrl(false);
    }, 3200);
  };

  useEffect(() => { setUseIframe(false); }, [item.uid, item.customer]);
  useEffect(() => {
    if (!active) {
      setPaused(false);
      setShowCtrl(false);
      setNow(0);
    }
  }, [active]);

  useEffect(() => () => window.clearTimeout(hideRef.current), []);

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
      hls.on(Hls.Events.ERROR, (_e, data) => { if (data?.fatal) fail(); });
    } else fail();
    return () => {
      if (hlsRef.current) { hlsRef.current.destroy(); hlsRef.current = null; }
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
    } else video.pause();
  }, [active, muted, useIframe, paused]);

  useEffect(() => {
    const video = videoRef.current;
    if (!video || useIframe) return undefined;
    const onTime = () => {
      if (!seekingRef.current) setNow(video.currentTime || 0);
      const d = video.duration;
      if (d && Number.isFinite(d)) setDur(d);
    };
    const onMeta = () => {
      const d = video.duration;
      if (d && Number.isFinite(d)) setDur(d);
    };
    video.addEventListener('timeupdate', onTime);
    video.addEventListener('loadedmetadata', onMeta);
    video.addEventListener('durationchange', onMeta);
    return () => {
      video.removeEventListener('timeupdate', onTime);
      video.removeEventListener('loadedmetadata', onMeta);
      video.removeEventListener('durationchange', onMeta);
    };
  }, [src, useIframe]);

  useEffect(() => {
    const sync = () => {
      const video = videoRef.current;
      setIsFs(isFsFor([slideRef.current, video, iframeRef.current]) || !!(video && video.webkitDisplayingFullscreen));
    };
    const off = onFsChange(sync);
    const video = videoRef.current;
    if (video) {
      video.addEventListener('webkitbeginfullscreen', sync);
      video.addEventListener('webkitendfullscreen', sync);
    }
    return () => {
      off();
      if (video) {
        video.removeEventListener('webkitbeginfullscreen', sync);
        video.removeEventListener('webkitendfullscreen', sync);
      }
    };
  }, [useIframe, src]);

  const onSlideClick = (e) => {
    if (e.target.closest('a, button, .sf-ctrl')) return;
    setPaused((v) => !v);
    bumpCtrl();
  };

  const onSeek = (e) => {
    const video = videoRef.current;
    const val = Number(e.target.value);
    setNow(val);
    if (video && Number.isFinite(val)) video.currentTime = val;
  };

  const onSeekStart = (e) => {
    e.stopPropagation();
    seekingRef.current = true;
    window.clearTimeout(hideRef.current);
    setShowCtrl(true);
  };

  const onSeekEnd = (e) => {
    e.stopPropagation();
    seekingRef.current = false;
    const video = videoRef.current;
    const val = Number(e.target.value);
    if (video && Number.isFinite(val)) video.currentTime = val;
    bumpCtrl();
  };

  const onFullscreen = (e) => {
    e.stopPropagation();
    const video = videoRef.current;
    const iframe = iframeRef.current;
    const slide = slideRef.current;
    const overlay = slide && slide.closest('.sf-overlay');
    if (isFsFor([slide, video, iframe, overlay]) || (video && video.webkitDisplayingFullscreen)) {
      if (video && video.webkitDisplayingFullscreen && video.webkitExitFullscreen) {
        video.webkitExitFullscreen();
      } else {
        exitFs();
      }
      bumpCtrl();
      return;
    }
    const targets = useIframe ? [iframe, slide, overlay] : [video, slide, overlay];
    const run = (i) => {
      if (i >= targets.length) return;
      enterFs(targets[i]).catch(() => run(i + 1));
    };
    run(0);
    bumpCtrl();
  };

  const onCtrlEnter = () => {
    holdRef.current = true;
    window.clearTimeout(hideRef.current);
    setShowCtrl(true);
  };
  const onCtrlLeave = () => {
    holdRef.current = false;
    bumpCtrl();
  };

  const like = (e) => {
    e.stopPropagation();
    if (!api.isMember) {
      goLogin(api.loginUrl, '회원만 작성 가능합니다.');
      return;
    }
    if (!api.goodUrl) return;
    postForm(api.goodUrl, { sf_ajax: 'good', sf_wr_id: item.wr_id })
      .then((data) => {
        if (data && data.need_login) {
          goLogin(data.login_url || api.loginUrl, data.error || '회원만 작성 가능합니다.');
          return;
        }
        if (!data || data.ok === false) {
          window.alert((data && data.error) || '좋아요를 반영하지 못했습니다.');
          return;
        }
        onLike({ liked: !!data.liked, good: data.count });
      })
      .catch(() => window.alert('좋아요를 반영하지 못했습니다.'));
  };

  const meta = [item.name, formatDuration(item.duration), item.hit != null ? `조회 ${formatHit(item.hit)}` : '']
    .filter(Boolean)
    .join(' · ');
  const max = dur > 0 ? dur : 0;

  return (
    <div ref={slideRef} className={`sf-slide${showCtrl ? ' is-ctrl' : ''}${isFs ? ' is-fs' : ''}`} onClick={onSlideClick}>
      {item.thumb ? <div className="sf-slide-bg" style={{ backgroundImage: `url(${item.thumb})` }} /> : null}
      {useIframe && frame && active && !paused ? (
        <iframe ref={iframeRef} className="sf-iframe" src={frame} title={item.subject || 'video'} allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture; fullscreen" allowFullScreen />
      ) : (
        <video ref={videoRef} className="sf-video" playsInline muted={muted} loop poster={item.thumb || undefined} preload={active ? 'auto' : 'metadata'} />
      )}
      <div className="sf-slide-ui">
        <div className="sf-slide-meta">
          {item.href ? <a className="sf-slide-title" href={item.href}>{item.subject || ''}</a> : <p className="sf-slide-title">{item.subject || ''}</p>}
          {meta ? <p className="sf-slide-sub">{meta}</p> : null}
        </div>
        <div className="sf-rail">
          <button type="button" className={`sf-act${item.liked ? ' is-liked' : ''}`} onClick={like} aria-label="좋아요">
            <IconHeart on={!!item.liked} />
            <em>{formatCount(item.good)}</em>
          </button>
          <button type="button" className="sf-act" onClick={(e) => { e.stopPropagation(); onOpenComments(); }} aria-label="댓글">
            <IconComment />
            <em>{formatCount(item.comment)}</em>
          </button>
          <button
            type="button"
            className={`sf-act sf-act-mute${muted ? ' is-off' : ' is-on'}`}
            onClick={(e) => { e.stopPropagation(); onToggleMute(); }}
            aria-label={muted ? '소리 켜기' : '소리 끄기'}
          >
            {muted ? <IconMuted /> : <IconSound />}
            <em>{muted ? '음소거' : '소리'}</em>
          </button>
        </div>
      </div>
      <div
        className="sf-ctrl"
        onClick={(e) => e.stopPropagation()}
        onPointerEnter={onCtrlEnter}
        onPointerLeave={onCtrlLeave}
        onPointerDown={(e) => { if (e.target.closest('.sf-ctrl-bar')) e.stopPropagation(); }}
      >
        <div className="sf-ctrl-seek">
          <span className="sf-ctrl-time">{fmtTime(now)}</span>
          <input
            type="range"
            className="sf-ctrl-bar"
            min="0"
            max={max || 0}
            step="0.1"
            value={Math.min(now, max || 0)}
            disabled={!max || useIframe}
            aria-label="재생 위치"
            onChange={onSeek}
            onPointerDown={onSeekStart}
            onPointerUp={onSeekEnd}
            onTouchStart={onSeekStart}
            onTouchEnd={onSeekEnd}
          />
          <span className="sf-ctrl-time is-end">{fmtTime(max || item.duration)}</span>
        </div>
        <button
          type="button"
          className="sf-ctrl-fs"
          aria-label={isFs ? '전체보기 종료' : '전체 보기'}
          onClick={onFullscreen}
        >
          <IconFullscreen on={isFs} />
        </button>
      </div>
    </div>
  );
}
