export function formatDuration(sec) {
  const n = Math.max(0, parseInt(sec, 10) || 0);
  if (!n) return '';
  const h = Math.floor(n / 3600);
  const m = Math.floor((n % 3600) / 60);
  const s = n % 60;
  if (h > 0) {
    return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }
  return `${m}:${String(s).padStart(2, '0')}`;
}

export function hlsUrl(item) {
  const uid = String(item?.uid || '').replace(/[^a-zA-Z0-9_-]/g, '');
  const code = String(item?.customer || '').replace(/^customer-/i, '').replace(/[^a-zA-Z0-9-]/g, '');
  if (!uid || !code) return '';
  return `https://customer-${code}.cloudflarestream.com/${uid}/manifest/video.m3u8`;
}

export function iframeUrl(item) {
  const uid = String(item?.uid || '').replace(/[^a-zA-Z0-9_-]/g, '');
  const code = String(item?.customer || '').replace(/^customer-/i, '').replace(/[^a-zA-Z0-9-]/g, '');
  if (!uid || !code) return '';
  return `https://customer-${code}.cloudflarestream.com/${uid}/iframe?autoplay=true&muted=true`;
}

export function isMobileMq() {
  return window.matchMedia('(max-width: 768px)');
}

export function formatHit(hit) {
  const n = parseInt(hit, 10) || 0;
  return n.toLocaleString('ko-KR');
}
