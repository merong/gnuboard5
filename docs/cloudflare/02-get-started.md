# 02 — Get started / 빠른 시작

대시보드: [Videos](https://dash.cloudflare.com/?to=/:account/stream/videos), [Live inputs](https://dash.cloudflare.com/?to=/:account/stream/inputs).

## A. 첫 VOD

### 1) 공개 URL에서 복사

```bash
curl -X POST \
  -d '{"url":"https://storage.googleapis.com/stream-example-bucket/video.mp4","meta":{"name":"My First Stream Video"}}' \
  -H "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/copy
```

Workers binding: `env.STREAM.upload(url, { meta: { name } })`. wrangler: `"stream": { "binding": "STREAM" }`.

지원 포맷: [03-upload.md](03-upload.md).

### 2) 준비될 때까지 대기

`readyToStream === true` 까지 GET 폴링하거나 [VOD 웹훅](08-manage.md).

```bash
curl -H "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/<VIDEO_UID>
```

응답 예: `uid`, `preview` (`.../watch`), `thumbnail`, `readyToStream`, `status.state`, `meta`, `created`, `size`.

### 3) 웹에 넣기

```html
<iframe
  src="https://customer-<CODE>.cloudflarestream.com/<VIDEO_UID>/iframe"
  title="Example Stream video"
  frameborder="0"
  allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
  allowfullscreen>
</iframe>
```

자체 플레이어: [04-playback.md](04-playback.md). 캡션/워터마크: [05-edit.md](05-edit.md).

## B. 첫 라이브

### 1) Live input 생성

```bash
curl -X POST \
  -H "Authorization: Bearer <API_TOKEN>" \
  -d '{"meta": {"name":"test stream"},"recording": { "mode": "automatic" }}' \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/live_inputs
```

응답: `uid`, `rtmps.url` (`rtmps://live.cloudflare.com:443/live/`), `rtmps.streamKey`, `recording.mode`.

### 2) 방송 소프트웨어에 URL+key

OBS 권장. 가이드: [First Live Stream with OBS](https://developers.cloudflare.com/stream/examples/obs-from-scratch/).

### 3) 재생

iframe `src`에 **live input `uid`** (또는 해당 방송의 video `uid`)를 넣는다. 형식은 VOD와 같다.

다음: [보안](04-playback.md#signed-urls), [라이브 시청자 수](09-analytics.md).

## 접근성

캡션([05-edit.md](05-edit.md))과 고품질 오디오를 권장.

## Media Transformations 과금 공지

문서 기준: Media Transformations는 GA. 과금은 **2025-11-01** 시작(페이지에 따라 “will begin” / “began” 표기). Stream VOD/라이브 과금과 별개.
