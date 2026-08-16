# 01 — Overview / 제품 모델

## Stream가 하는 일

Cloudflare Stream는 라이브·온디맨드 비디오를 **업로드/인제스트 → 저장 → H.264 adaptive bitrate 인코딩 → 글로벌 전송 → 재생**까지 한 API로 처리한다. 인프라는 사용자가 구성하지 않는다.

- 코덱/해상도: H.264, ABR, **360p–1080p**
- 재생: Stream Player (`iframe`) 또는 HLS/DASH 호환 자체 플레이어 (웹, iOS AVPlayer, Android ExoPlayer)
- 네트워크: Cloudflare 글로벌 네트워크

## 핵심 객체

| 객체 | 식별 | 역할 |
|------|------|------|
| Video | `uid` | VOD 또는 라이브 방송/녹화 한 편. `status.state`, `readyToStream`, `playback.hls/dash` |
| Live input | `uid` | RTMPS/SRT/WHIP 인제스트 엔드포인트. `rtmps.url` + `streamKey` |
| Watermark profile | `uid` | 업로드 시 적용할 이미지 프로필 |
| Signing key | `id` | signed URL 자체 서명용 PEM/JWK |
| Webhook (VOD) | account당 1개 | 처리 완료/에러 알림 |
| Direct upload | `uid` + `uploadURL` | 엔드유저가 API 토큰 없이 업로드 |

## Video 상태 (`status.state`)

| state | 의미 |
|-------|------|
| `pendingupload` | 업로드 대기 |
| `downloading` | 링크 복사 중 |
| `queued` | 인코딩 대기 |
| `inprogress` | 인코딩 중 (`pctComplete` 0–100) |
| `ready` | 재생 가능. 일부 화질은 아직 인코딩 중일 수 있음 (`pctComplete` 가 100이면 전 화질 완료) |
| `error` | 실패 (`errorReasonCode` / `errorReasonText`) |
| `live-inprogress` | 라이브 방송 중 |

`readyToStream: true` 이면 최소 한 화질은 재생 가능. 최고 화질까지 기다리려면 `state=ready` **그리고** `pctComplete=100`.

## 인코딩 / 프레임레이트

- 업로드는 임의 FPS 허용. 재인코딩 시 **최대 70 FPS**. 원본이 더 낮으면 원본 FPS 유지.
- VFR: 1/30초 창에 프레임이 둘 이상이면 초과분 드롭.
- HDR 업로드 → **SDR로 재인코딩** (호환성).
- 멀티채널 오디오 → **스테레오 다운믹스**.
- 권장 업로드: MP4 + AAC + H.264, ≤60 fps (FAQ는 신규 제작 시 ≤30 fps), Fast Start (`moov` 앞), progressive, high profile, closed GOP.

## 한도 (출처에 명시된 것만)

| 한도 | 값 | 출처 |
|------|-----|------|
| 파일 크기 | 기본 **30 GB** | FAQ, upload |
| 동시 인코딩 큐 | 기본 **120** (`ready`/`error`/`pendingupload` 제외) | FAQ |
| 스토리지 | 구매한 분(minutes stored) 초과 시 업로드/라이브 불가 (Enterprise는 계약 쿼타 초과 업로드 가능) | FAQ, pricing |
| 기본 POST / creator POST | **200 MB** 미만. 이상은 **tus** | upload, DCU |
| tus 청크 | 최소 5,242,880 B (파일 전체가 더 작으면 예외), 최대 209,715,200 B, **256 KiB 배수** | tus |
| 리스트 | 한 요청 최대 **1000** 비디오 | API list |
| signing keys | 최대 **1000** | securing |
| token `exp` | 서명 시점부터 **24시간 이내** | securing |
| accessRules | 토큰당 최대 **5** | securing |
| 라이브 녹화 길이 | **7일** 초과분은 잘림 | start live |
| 라이브 MP4 다운로드 | 녹화 **4시간 미만**만 | download live |
| DVR 매니페스트 | 최대 **7200** 세그먼트; 3시간+ 성능 저하 가능 | DVR |
| 시뮬캐스트 | 라이브 입력당 **최대 50** 동시 destination | simulcast |
| `scheduledDeletion` / `deleteRecordingAfterDays` | 최소 **30**, 최대 **1096**일 | tus, live API |
| Media Transformations 입력 | **100 MB**, 입력 길이 **10분**, `time` 0–10m, `duration` 1s–60s, width/height 10–2000 | transform |
| 오디오 트랙 HTTP 업로드 | **200 MB** | audio tracks |
| 다운로드 파일명 | 최대 120자 `[A-Za-z0-9-_]` | downloads |

초과 업로드는 **429** 또는 **413**. 한도 상향은 Cloudflare support.

## 재생 URL 패턴

`<CODE>` = customer code, `<UID>` = video 또는 live input uid. signed 비디오는 `<UID>` 자리에 **token**.

| 용도 | URL |
|------|-----|
| Stream Player | `https://customer-<CODE>.cloudflarestream.com/<UID>/iframe` |
| Preview/watch | `.../<UID>/watch` |
| HLS | `.../<UID>/manifest/video.m3u8` |
| DASH | `.../<UID>/manifest/video.mpd` |
| LL-HLS (beta) | `.../manifest/video.m3u8?protocol=llhls` |
| Thumbnail | `.../<UID>/thumbnails/thumbnail.jpg` |
| Animated thumb | `.../<UID>/thumbnails/thumbnail.gif` |
| MP4 download | `.../<UID>/downloads/default.mp4` |
| M4A download | `.../<UID>/downloads/audio.m4a` |
| Live viewers | `.../<INPUT_ID>/views` |
| WHIP publish | `.../<SECRET>/webRTC/publish` |
| WHEP play | `.../<INPUT_UID>/webRTC/play` |

**매니페스트는 캐시/프록시/저장하지 말 것** — 동적 자산.

## Video 객체 주요 필드

`allowedOrigins`, `clippedFrom`, `created`, `creator`, `duration` (`-1` = 아직 모름), `input.height/width`, `liveInput`, `maxDurationSeconds`, `maxSizeBytes`, `meta` (임의 KV, `name`이 대시보드 이름), `modified`, `playback.dash/hls`, `preview`, `publicDetails` (`title`, `share_link`, `channel_link`, `logo`), `readyToStream`, `readyToStreamAt`, `requireSignedURLs`, `scheduledDeletion`, `size`, `status`, `thumbnail`, `thumbnailTimestampPct`, `uid`, `uploaded`, `uploadExpiry`, `watermark`.

## 과금 차원 (개요)

Stream: **저장된 분** + **전송된 분**. 인제스트·인코딩은 무료. 온디맨드와 라이브 동일. 상세는 [13-faq-pricing.md](13-faq-pricing.md). Media Transformations는 별도(Image Transformations와 동일 메트릭).
