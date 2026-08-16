# 04 — Playback / 재생·보안

## Stream Player vs 자체 플레이어

| | Stream Player | 자체 플레이어 (HLS/DASH) |
|--|---------------|--------------------------|
| 언제 | 웹에 바로 임베드, 엔지니어링 최소 | 네이티브 앱, 커스텀 UI, Chromecast는 DASH 권장 |
| 넣는 법 | `iframe` `.../<UID>/iframe` | `.../<UID>/manifest/video.m3u8` 또는 `.mpd` |
| 패키지 | `@cloudflare/stream-react`, `@cloudflare/stream-angular` | hls.js, dash.js, Video.js, Shaka, Vidstack, AVPlayer, ExoPlayer |
| JS 제어 | `https://embed.cloudflarestream.com/embed/sdk.latest.js` → `Stream(iframe)` | 플레이어 라이브러리 API |

Chromium 단독은 H.264 미지원이라 Stream Player 불가. Chromecast는 분리된 A/V HLS에 약함 → **DASH**.

매니페스트는 **캐시/프록시/저장 금지**.

## Stream Player

```html
<iframe
  src="https://customer-<CODE>.cloudflarestream.com/<VIDEO_UID>/iframe"
  style="border: none" height="720" width="1280"
  allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
  allowfullscreen="true"></iframe>
```

반응형: 컨테이너 `padding-top: 56.25%` (16:9), iframe `position: absolute; height/width 100%`.

### iframe query params

| 파라미터 | 기본 | 설명 |
|----------|------|------|
| `autoplay` | false | 있으면 시도. `autoplay=false` 넣지 말고 **생략**. 모바일은 보통 탭 필요. 오디오 있으면 `muted=true` |
| `controls` | true | 컨트롤 표시 |
| `defaultTextTrack` | — | BCP-47. 초기화 시에만 |
| `letterboxColor` | — | CSS color, URI-encode. `transparent` 가능 |
| `loop` | false | |
| `muted` | false | |
| `preload` | none | `auto` / `metadata`. 힌트. 플레이어는 항상 소량 메타 로드 |
| `poster` | 첫 프레임 | URI-encoded 이미지 URL |
| `primaryColor` | — | URI-encoded CSS color |
| `startTime` | — | 초(`123`) 또는 `1h12m27s` |
| `ad-url` | — | VAST URI, encodeURIComponent |
| `dvrEnabled` | — | 라이브 DVR (`true`) |

디버그: 재생 중 `Shift-D`.

브라우저: Chrome 88+, Firefox 87+, Edge 89+, Safari 14+, Opera 75+. 모바일: Chrome Android 90, UC 12.12+, Samsung Internet 13+, iOS Safari 13.4+.

라이브 종료 후 녹화는 **약 60초**. 시청 중 끝나면 60초 후 리로드. API `state=ready`면 매니페스트로 녹화 재생. 생성 중 `not-found` / `not-started` 가능.

LL-HLS 베타가 켜진 live input은 플레이어가 가능하면 저지연 모드.

### Player JS SDK

```html
<script src="https://embed.cloudflarestream.com/embed/sdk.latest.js"></script>
<script>
  const player = Stream(document.getElementById('stream-player'));
  player.addEventListener('play', () => {});
  player.play().catch(() => { player.muted = true; player.play(); });
</script>
```

메서드: `play()` Promise, `pause()`.

속성: `autoplay`, `buffered` (TimeRanges), `controls`, `currentTime`, `defaultTextTrack`, `duration`, `ended`, `letterboxColor`, `loop`, `muted`, `paused`, `played`, `preload`, `primaryColor`, `volume` (0.0–1.0).

이벤트: 표준 media (`abort`, `canplay`, `canplaythrough`, `durationchange`, `ended`, `error`, `loadeddata`, `loadedmetadata`, `loadstart`, `pause`, `play`, …). `error`는 인코딩 미완료 또는 잘못된 signed URL 등.

iframe 속성(업로드 문서 player-api와 동일 계열): 공식 페이지 [Player API](https://developers.cloudflare.com/stream/uploading-videos/player-api/) · [using-the-player-api](https://developers.cloudflare.com/stream/viewing-videos/using-the-stream-player/using-the-player-api/).

## 자체 플레이어

```
https://customer-<CODE>.cloudflarestream.com/<UID>/manifest/video.m3u8
https://customer-<CODE>.cloudflarestream.com/<UID>/manifest/video.mpd
```

LL-HLS 베타: `?protocol=llhls`.

대역폭 강제(최후 수단): `?clientBandwidthHint=1.8` (Mbps, 가장 가까운 representation만).

네이티브 앱에서 **글래스-투-글래스 <1s**: SRT 또는 RTMPS **재생**(ffmpeg / ffmpeg-kit). AVPlayer·ExoPlayer는 SRT/RTMPS 네이티브 미지원. Stream SRT는 **caller mode만**. 예: [rtmps_playback](https://developers.cloudflare.com/stream/examples/rtmps_playback/), [srt_playback](https://developers.cloudflare.com/stream/examples/srt_playback/).

플랫폼 가이드: [web](https://developers.cloudflare.com/stream/viewing-videos/using-own-player/web/), [iOS](https://developers.cloudflare.com/stream/viewing-videos/using-own-player/ios/), [Android](https://developers.cloudflare.com/stream/viewing-videos/using-own-player/android/).

## Thumbnails

비정사각 픽셀 비디오는 썸네일 미지원.

온더플라이:

`https://customer-<CODE>.cloudflarestream.com/<UID>/thumbnails/thumbnail.jpg?time=1s&height=270`

| 쿼리 | 기본 | 값 |
|------|------|-----|
| `time` | 0s | `8m`, `5m2s` |
| `height` / `width` | 640 | |
| `fit` | crop | `crop` `clip` `scale` `fill` |

기본 썸네일 시각: `POST .../stream/{uid}` body `{"thumbnailTimestampPct": 0.5}` (0.0–1.0).

애니메이션 GIF (전송 분·Analytics 시청 분에 **미포함**):

`.../thumbnails/thumbnail.gif?time=1s&height=200&duration=4s`

추가: `duration` 기본 5s, `fps` 기본 8.

signed 필요 시 UID 대신 토큰.

## Download (MP4 / M4A)

원본 파일 그대로는 **다운로드 불가**. 인코딩된 MP4/M4A.

비디오가 ready 후:

```bash
# MP4 (default)
curl -X POST -H "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/<VIDEO_UID>/downloads

# M4A
curl -X POST -H "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/<VIDEO_UID>/downloads/audio
```

응답: `result.default|audio`: `status` (`inprogress`/`ready`), `url`, `percentComplete`.

GET `.../downloads` 로 목록. DELETE `.../downloads/default` 또는 `/audio`.

파일명: `?filename=MY_VIDEO.mp4` (최대 120자 `[A-Za-z0-9-_]`, 확장자 자동).

과금: MP4 다운로드 = 비디오 길이 × 횟수 (minutes delivered). 스토리지 추가 없음.

라이브 녹화 MP4는 **4시간 미만**만. [06-live.md](06-live.md).

## Signed URLs

기본은 `uid`만으로 공개. `requireSignedURLs: true` 이면 iframe/watch/매니페스트/공개 링크 전부 토큰 필요.

```bash
curl "https://api.cloudflare.com/client/v4/accounts/{account_id}/stream/{video_uid}" \
  -H "Authorization: Bearer <API_TOKEN>" -H "Content-Type: application/json" \
  --data '{"requireSignedURLs": true}'
```

토큰을 UID 자리에: `...cloudflarestream.com/<TOKEN>/iframe` · `/manifest/video.m3u8` · `/downloads/default.mp4`.

### 토큰 만드는 법 3가지

| 방법 | 언제 | 제한 |
|------|------|------|
| `POST .../stream/{uid}/token` | 테스트 또는 **하루 <1000** 토큰 | API rate limit. 기본 1시간. **Live WebRTC 미지원** |
| Signing key (권장, 대량/WebRTC) | 고볼륨, 커스텀 claims | `POST .../stream/keys` 1회 → pem/jwk **재표시 안 됨**. 로컬 RS256 |
| Workers `video(id).generateToken()` | Workers | 기본 1시간. 커스텀 exp/geo/download은 signing key |

`/token` body 커스터마이즈: `exp` (unix), `downloadable`, `accessRules`.

Signing key JWT payload:

| claim | 의미 |
|-------|------|
| `sub` | video UID |
| `kid` | key id |
| `exp` | unix. 서명 시각 + **최대 24h** |
| `nbf` | 이전엔 무효 |
| `downloadable` | true면 MP4/M4A (다운로드가 켜진 비디오) |
| `accessRules` | 최대 5. 앞에서 뒤로, 매칭 시 중단 |

accessRule: `type` = `any` | `ip.src` | `ip.geoip.country`; `action` = `allow` | `block`; `country` = ISO 3166-1 alpha-2 배열; `ip` = CIDR 배열 (IPv4+IPv6 권장). 재생 중 계속 평가 — 너무 빡센 IP는 모바일에서 끊길 수 있음.

키 폐기: `DELETE .../stream/keys/{key_id}` → 해당 키로 만든 토큰 전부 무효. 최대 1000 키.

알고리즘 예: header `{alg: RS256, kid}`, payload 위 claims, `crypto.subtle` RSASSA-PKCS1-v1_5 SHA-256. jwk/pem은 base64 디코드 후 사용.

비공개 비디오의 다운로드는 토큰에 **`downloadable: true`** 필수.

## Allowed origins (핫링크)

기본: 모든 도메인. 비디오별 `allowedOrigins` 배열. 자체 플레이어에서도 **HLS/DASH와 세그먼트 요청 origin**을 제한.

와일드카드: `*.badtortilla.com` 은 `a.badtortilla.com` 포함, apex `badtortilla.com` **미포함**. `example.com` 은 `www.example.com` 미포함. 경로 없음. `localhost`는 80/443 아니면 포트 필요.

```bash
curl .../stream/{video_uid} --data '{"allowedOrigins": ["example.com"]}'
```

## CSP

| 사용 | 지시 | 호스트 |
|------|------|--------|
| Stream Player | `frame-src` 또는 `default-src` | `videodelivery.net` `*.cloudflarestream.com` |
| 자체 플레이어 | `media-src` `img-src` `connect-src` | `*.videodelivery.net` `*.cloudflarestream.com` |
| Creator upload | `connect-src` | 동일 |

계정만 허용하려면 `*` 를 `customer-<CODE>` 로 교체.

## Embed HTML API

`GET /accounts/{account_id}/stream/{identifier}/embed` — iframe HTML.
