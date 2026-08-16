# Cloudflare Stream — Video docs for agents

**제품 (5줄):** Cloudflare Stream는 서버리스 온디맨드·라이브 비디오 플랫폼이다. 업로드/인제스트 → 인코딩(H.264 ABR, 360p–1080p) → 글로벌 전송까지 한 API로 처리한다. 비디오는 `uid`로 식별되고, 재생은 `customer-<CODE>.cloudflarestream.com` 의 iframe / HLS / DASH URL을 쓴다. Stream 라이브러리에 저장하는 비디오와, origin에 두고 URL로 변환만 하는 Media Transformations는 별개 제품이다. 라이브는 RTMPS/SRT 인제스트(또는 WebRTC WHIP/WHEP 베타)로 들어온다.

**언제 쓰는가:** 사이트/앱에 VOD·라이브를 넣고, 인코딩·CDN·플레이어를 직접 운영하고 싶지 않을 때. origin 파일만 리사이즈/클립하고 Stream 스토리지에 넣고 싶지 않으면 [07-transform.md](07-transform.md).

## 세 가지 모델을 혼동하지 말 것

| 모델 | 저장 위치 | 식별자 | 재생 |
|------|-----------|--------|------|
| 1) Stream-hosted VOD | Stream 라이브러리 | video `uid` | iframe / HLS / DASH |
| 2) Live inputs | 인제스트 + 선택적 녹화 | live input `uid` + broadcast video `uid` | 동일 재생 URL; 녹화는 별 video |
| 3) Media Transformations | origin (Stream 밖) | zone + `/cdn-cgi/media/...` | 변환된 MP4/이미지/오디오 |

## 읽는 순서 / Reading order

| 파일 | 한국어 안내 | When to load |
|------|-------------|--------------|
| [01-overview.md](01-overview.md) | 제품 모델, 코덱, 한도 | 처음 / 한도 확인 |
| [02-get-started.md](02-get-started.md) | 첫 업로드·첫 라이브 | 빠른 시작 |
| [03-upload.md](03-upload.md) | 업로드 방법 선택 | 파일/링크/tus/creator upload |
| [04-playback.md](04-playback.md) | 플레이어, HLS/DASH, 썸네일, 다운로드, signed URL | 재생·보안 |
| [05-edit.md](05-edit.md) | 클립, 캡션, 오디오, 워터마크, 플레이어 브랜딩 | 편집 |
| [06-live.md](06-live.md) | RTMPS/SRT, 녹화, DVR, 클립, 시뮬캐스트, 웹훅 | 라이브 |
| [07-transform.md](07-transform.md) | origin 비디오 변환 (저장 아님) | Media Transformations |
| [08-manage.md](08-manage.md) | 라이브러리, 검색, creator, 웹훅, Workers binding | 관리 |
| [09-analytics.md](09-analytics.md) | GraphQL, 라이브 시청자 수 | 분석 |
| [10-webrtc.md](10-webrtc.md) | WHIP/WHEP 서브초 지연 (베타) | 초저지연 라이브 |
| [11-api-reference.md](11-api-reference.md) | REST 허브: envelope, Video, 메서드 인덱스 | 호출 작성 전 공통 타입 |
| [11-api-videos.md](11-api-videos.md) | list/get/create/edit/delete/copy/direct_upload/clip/embed/token/storage | 비디오 REST 파라미터·응답 |
| [11-api-live.md](11-api-live.md) | live_inputs CRUD, rotate_keys, videos, outputs | 라이브 REST |
| [11-api-edit.md](11-api-edit.md) | captions, audio, watermarks, downloads | 편집 REST |
| [11-api-keys-webhooks.md](11-api-keys-webhooks.md) | signing keys, VOD webhook | 키·웹훅 REST |
| [12-examples.md](12-examples.md) | 재생 레시피 | 플레이어 코드 |
| [13-faq-pricing.md](13-faq-pricing.md) | FAQ·가격 (출처 사실만) | 한도/과금 |
| [cheatsheet.md](cheatsheet.md) | 의사결정 레이어 | 방법 고르기 |
| [SOURCES.md](SOURCES.md) | 원문 URL | 출처 확인 |
| [SKILL.md](SKILL.md) | 에이전트 로더 | 어떤 파일을 열지 |

## Auth / Base URL

- **REST base:** `https://api.cloudflare.com/client/v4/accounts/{account_id}/stream`
- **Header:** `Authorization: Bearer <API_TOKEN>` (Cloudflare API token)
- **Account:** `{account_id}` 는 Cloudflare account identifier
- **Playback host:** `https://customer-<CODE>.cloudflarestream.com/<UID_OR_TOKEN>/...` (`<CODE>` = 계정 customer code, 대시보드 Stream 페이지)
- **Direct upload host:** `https://upload.videodelivery.net/{uid}`
- **Live ingest:** `rtmps://live.cloudflare.com:443/live/` + `streamKey` (SRT도 지원; caller mode만)
- **Dashboard:** `https://dash.cloudflare.com/?to=/:account/stream/videos` · live inputs: `.../stream/inputs`

Workers Stream binding (`env.STREAM`) 은 REST와 같은 라이브러리를 다루지만, tus(>200MB) 는 REST가 필요하다.

원문 덤프는 `/workspace/api-docs/_raw/cloudflare-stream/` 에 있다. 엔드포인트·한도·가격을 추측하지 말 것 — 모르면 「문서에 명시되지 않음」.
