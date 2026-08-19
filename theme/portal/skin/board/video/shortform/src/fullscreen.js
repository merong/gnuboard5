export function fsElement() {
  return document.fullscreenElement
    || document.webkitFullscreenElement
    || document.webkitCurrentFullScreenElement
    || null;
}

export function isFsFor(els) {
  const cur = fsElement();
  if (!cur) return false;
  return (els || []).some((el) => el && (cur === el || (el.contains && el.contains(cur))));
}

export function exitFs() {
  const fn = document.exitFullscreen || document.webkitExitFullscreen || document.webkitCancelFullScreen;
  if (!fn) return Promise.resolve();
  return Promise.resolve(fn.call(document)).catch(() => {});
}

export function enterFs(el) {
  if (!el) return Promise.reject(new Error('no element'));
  if (typeof el.webkitEnterFullscreen === 'function' && !el.requestFullscreen) {
    try {
      el.webkitEnterFullscreen();
      return Promise.resolve();
    } catch (err) {
      return Promise.reject(err);
    }
  }
  const req = el.requestFullscreen || el.webkitRequestFullscreen || el.webkitRequestFullScreen;
  if (!req) return Promise.reject(new Error('no fullscreen'));
  return Promise.resolve(req.call(el));
}

export function onFsChange(cb) {
  const evs = ['fullscreenchange', 'webkitfullscreenchange'];
  evs.forEach((name) => document.addEventListener(name, cb));
  return () => evs.forEach((name) => document.removeEventListener(name, cb));
}
