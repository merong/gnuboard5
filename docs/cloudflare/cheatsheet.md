# Cheatsheet — 무엇을 고를까

## 업로드 방법

파일이 어디에 있나?

- HTTP URL (R2/S3/GCS) → POST /stream/copy
- 우리 서버, 200 MB 미만, 안정 → multipart POST /stream
- 우리 서버, 200 MB 초과 또는 불안정 → tus POST /stream
- 엔드유저, 토큰 숨김, 200 MB 미만 → POST /stream/direct_upload → upload.videodelivery.net
- 엔드유저, 200 MB 초과 또는 불안정 → POST /stream?direct_user=true (tus Location)

항상: 지원 포맷, 최대 30GB, DCU는 maxDurationSeconds 필수 (스토리지 예약).

## 플레이어

- 웹 + 최소 공수 → Stream Player iframe
- 웹 + 커스텀 UI → hls.js / dash.js / Video.js / Shaka / Vidstack
- Chromecast → DASH (.mpd)
- iOS/tvOS/macOS → AVPlayer + HLS
- Android → ExoPlayer + HLS (또는 DASH)
- 네이티브 sub-1s 라이브 → SRT/RTMPS playback via ffmpeg (AVPlayer/Exo 미지원)
- 브라우저 sub-1s 라이브 → WebRTC WHEP (베타, WHIP와만)

매니페스트 캐시 금지. signed면 UID 자리에 token.

## 라이브 vs WebRTC vs Transform

| 필요 | 선택 |
|------|------|
| 일반 라이브, 녹화, HLS, 시뮬캐스트 | Live input RTMPS/SRT, recording.mode=automatic |
| 채널 상시 페이지 | 재생 URL에 input uid |
| 회차/다시보기 페이지 | video uid (방송마다 새로) |
| 시크 가능한 라이브 | dvrEnabled=true (HLS/Player, Video ID 권장) |
| 하이라이트, 스토리지 추가 없음 | Live instant clip (video id) |
| VOD 잘라 새 에셋 | POST /stream/clip |
| 서브초, 양방향 | WebRTC WHIP/WHEP (녹화/시뮬캐스트/분석 아직 없음) |
| origin 파일만 리사이즈/클립, Stream에 안 넣음 | Media Transformations /cdn-cgi/media/ |

## Signed URL 언제

| 상황 | 조치 |
|------|------|
| 공개 | 기본. uid로 재생 |
| 로그인 유저만 / 시간 제한 / 국가·IP | requireSignedURLs=true + 토큰 |
| 하루 1000 미만 토큰, 테스트 | POST .../stream/{uid}/token |
| 대량 또는 WebRTC | signing key로 로컬 JWT |
| Workers | generateToken() (커스텀 claims는 key) |
| 비공개 MP4 | 토큰에 downloadable=true + downloads 활성화 |
| 도메인 제한만 | allowedOrigins (토큰 없이도 가능) |

exp는 서명 시각 + 최대 24h. accessRules 최대 5.

## 웹훅

| 이벤트 | 어디 |
|--------|------|
| VOD 인코딩 완료/실패 | PUT /stream/webhook + Webhook-Signature |
| 라이브 connect/disconnect/error | Notifications → Stream Live Input |
| 로컬 테스트 | Quick Tunnel 예제 |

## 상태 폴링

GET /stream/{uid} → readyToStream 또는 웹훅. 최고 화질: state=ready AND pctComplete=100.

라이브 종료 → 약 60초 → 녹화 ready.

## 자주 빠지는 함정

- 200MB+ 를 basic POST로 보냄 → tus 필요
- DCU에 maxDuration 없음 → 예약/거절
- 매니페스트를 CDN에 캐시
- 라이브 과거 회차를 input uid로 재생 (안 됨)
- 라이브에 워터마크 (미지원)
- 4시간+ 라이브 녹화 MP4 (불가)
- HDR 원본 기대 (SDR로 나감)
- WebRTC와 HLS 혼용 (베타에서 불가)
- Transform을 Stream uid처럼 취급
- /token 으로 WebRTC 토큰
