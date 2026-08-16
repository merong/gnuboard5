---
name: cloudflare-stream-video
description: "Cloudflare Stream video docs (VOD, live, playback, edit, transform). Use when uploading/playing/securing Stream videos, live RTMPS/SRT/WebRTC, captions, clips, signed URLs, or Media Transformations."
---

# cloudflare-stream-video

에이전트는 이 파일만 먼저 읽고, 질문에 맞는 **한두 개** 파일을 연다. 트리 전체를 로드하지 말 것.

## Load map

| 질문 | 파일 |
|------|------|
| Stream이 뭔가 / 언제 쓰나 / auth | index.md |
| 한도, 코덱, uid, 상태 | 01-overview.md |
| 첫 업로드/라이브 | 02-get-started.md |
| 업로드 방법, tus, DCU, copy | 03-upload.md |
| Player, HLS/DASH, 썸네일, 다운로드, signed URL | 04-playback.md |
| 클립, 캡션, 오디오, 워터마크, publicDetails | 05-edit.md |
| RTMPS/SRT, 녹화, DVR, 시뮬캐스트, 라이브 웹훅 | 06-live.md |
| origin 변환 (/cdn-cgi/media) — Stream 저장 아님 | 07-transform.md |
| 검색, creator, VOD 웹훅, Workers binding | 08-manage.md |
| GraphQL, live viewers | 09-analytics.md |
| WHIP/WHEP 서브초 (베타) | 10-webrtc.md |
| method + path 인덱스, envelope, Video | 11-api-reference.md |
| 비디오 REST 파라미터·응답 | 11-api-videos.md |
| 라이브 REST | 11-api-live.md |
| captions/audio/watermarks/downloads | 11-api-edit.md |
| signing keys, VOD webhook | 11-api-keys-webhooks.md |
| 플레이어 코드 | 12-examples.md |
| 가격/FAQ | 13-faq-pricing.md |
| 방법 고르기 | cheatsheet.md |
| 원문 URL | SOURCES.md |

## 규칙

- Base: `https://api.cloudflare.com/client/v4/accounts/{account_id}/stream`
- Header: `Authorization: Bearer <API_TOKEN>`
- 재생: `https://customer-<CODE>.cloudflarestream.com/<UID_OR_TOKEN>/...`
- 세 모델 혼동 금지: (1) Stream-hosted VOD (2) Live inputs (3) Media Transformations
- 엔드포인트·한도·가격을 발명하지 말 것. 없으면 "문서에 명시되지 않음"
- 예제 토큰/PEM을 새 시크릿처럼 쓰지 말 것
- 원문: /workspace/api-docs/_raw/cloudflare-stream/

Operations: 허브 11-api-reference.md 인덱스 + 상세 4파일. 각 엔드포인트에 Path/Query/Header/Body/Returns 표. product-doc extras (`rotate_keys`, live_inputs videos, downloads/audio, multipart POST /stream, POST /audio)는 `출처: 제품 문서`.
