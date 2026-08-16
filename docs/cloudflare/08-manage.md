# 08 — Manage / 라이브러리·웹훅·Workers

## 목록 / 검색 / 필터

`GET /accounts/{account_id}/stream` — 한 요청 최대 **1000**.

| Query | 의미 |
|-------|------|
| `id` | uid, 콤마 구분 |
| `name` | 이름/UID, 콤마 |
| `search` | `meta.name` **부분** 일치. 중·대형 라이브러리에서 느림, 초대형은 불가할 수 있음 |
| `video_name` | `meta.name` **정확** 일치 (빠름) |
| `creator` | creator id |
| `status` | pendingupload / downloading / queued / inprogress / ready / error / live-inprogress |
| `type` | `vod` 또는 `live` |
| `live_input_id` | 해당 라이브의 비디오 |
| `start` / `after` | 이 시각 이후 생성 (RFC 3339) |
| `end` / `before` | 이전 |
| `asc` | 생성 오름차순 |
| `limit` | 기본·최대 1000 |
| `include_counts` | 매칭 총개수 |

검색 예: `GET .../stream?search=puppy`.

상세: `GET .../stream/{identifier}`  
수정: `POST .../stream/{identifier}` (meta, requireSignedURLs, allowedOrigins, thumbnailTimestampPct, publicDetails, scheduledDeletion, creator, …)  
삭제: `DELETE .../stream/{identifier}`  
스토리지: `GET .../stream/storage-usage`

`scheduledDeletion`: 업로드 후 **최소 30일**. `null`이면 기존 예약 제거.

## Creator ID

내부 유저 ID를 `creator`에 넣어 검색·분석.

- copy: body `"creator": "<CREATOR_ID>"`
- tus: 헤더 `Upload-Creator`
- DCU: direct_upload / Upload-Metadata (공식 creator-id 페이지)
- basic POST: **업로드 후** POST edit으로 설정

필터: `GET .../stream?creator=<CREATOR_ID>`. GraphQL dimension `creator`.

## VOD webhooks

라이브 connect/disconnect와 **다름** ([06-live.md](06-live.md)).

계정당 **구독 1개**. `http://` 또는 `https://` only. localhost/사설 IP 불가 — 로컬은 Quick Tunnel. [test-webhooks-locally](https://developers.cloudflare.com/stream/examples/test-webhooks-locally/).

```bash
curl -X PUT -H 'Authorization: Bearer <API_TOKEN>' \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/webhook \
  --data '{"notificationUrl":"<WEBHOOK_NOTIFICATION_URL>"}'
```

응답: `notificationUrl`, `modified`, `secret` (서명 검증용).

처리 **완료 후** POST. body는 video 객체 (`uid`, `readyToStream`, `status`, `playback`, …).

에러 `status.state=error` 코드:

| code | 의미 |
|------|------|
| `ERR_NON_VIDEO` | 비디오 아님 |
| `ERR_DURATION_EXCEED_CONSTRAINT` | DCU 길이 초과 |
| `ERR_FETCH_ORIGIN_ERROR` | URL 다운로드 실패 |
| `ERR_MALFORMED_VIDEO` | 손상 |
| `ERR_DURATION_TOO_SHORT` | < 0.1s |
| `ERR_UNKNOWN` | 기타 |

검증: 헤더 `Webhook-Signature: time=<unix>,sig1=<hex>`. 소스 문자열 = `time` + `.` + **원본 body 바이트**. HMAC-SHA256(`secret`), hex, constant-time 비교. 너무 오래된 `time`은 폐기.

GET/DELETE `.../stream/webhook`.

## Workers Stream binding

wrangler:

```json
{ "stream": { "binding": "STREAM" } }
```

문서된 예:

| 작업 | 호출 |
|------|------|
| URL 업로드 | `env.STREAM.upload(url, { meta })` |
| DCU (POST만) | `env.STREAM.createDirectUpload({ maxDurationSeconds })` |
| 비디오 핸들 | `env.STREAM.video("VIDEO_ID")` |
| 수정 | `video.update({ requireSignedURLs, allowedOrigins })` |
| 토큰 | `video.generateToken()` |
| 다운로드 | `video.downloads.generate()` / `generate("audio")` / `get()` / `delete()` |
| 캡션 | `video.captions.generate("en")` / `upload(lang, stream)` |
| 워터마크 | `env.STREAM.watermarks.generate(readableStream, { name })` |

전체 메서드: [manage-video-library/bindings](https://developers.cloudflare.com/stream/manage-video-library/bindings/). tus >200MB는 REST.
