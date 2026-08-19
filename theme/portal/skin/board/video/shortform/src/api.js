export function goLogin(loginUrl, message) {
  window.alert(message || '회원만 작성 가능합니다.');
  if (loginUrl) window.location.href = loginUrl;
}

export function postForm(url, fields) {
  const fd = new FormData();
  Object.keys(fields || {}).forEach((k) => {
    if (fields[k] != null) fd.append(k, fields[k]);
  });
  return fetch(url, {
    method: 'POST',
    body: fd,
    credentials: 'same-origin',
    headers: { Accept: 'application/json' },
  }).then((r) => r.json());
}

export function formatCount(n) {
  const v = parseInt(n, 10) || 0;
  if (v >= 10000) return `${(v / 10000).toFixed(1).replace(/\.0$/, '')}만`;
  if (v >= 1000) return `${(v / 1000).toFixed(1).replace(/\.0$/, '')}천`;
  return String(v);
}

export function formatWhen(dt) {
  if (!dt) return '';
  const t = Date.parse(String(dt).replace(/-/g, '/'));
  if (!t) return String(dt).slice(0, 16);
  const diff = Math.max(0, Date.now() - t);
  const m = Math.floor(diff / 60000);
  if (m < 1) return '방금';
  if (m < 60) return `${m}분 전`;
  const h = Math.floor(m / 60);
  if (h < 24) return `${h}시간 전`;
  const d = Math.floor(h / 24);
  if (d < 7) return `${d}일 전`;
  return String(dt).slice(0, 10);
}
