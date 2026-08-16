# 06 — Live / 라이브

WebRTC(WHIP/WHEP)는 [10-webrtc.md](10-webrtc.md). 여기는 **RTMPS/SRT**.

## 흐름

1. Live input 생성 (대시보드 또는 API)
2. `rtmps.url` + `streamKey` (또는 SRT)를 방송자에게. **키는 비밀**
3. 방송자가 RTMPS 또는 SRT로 송출. SRT는 **caller mode만**. SRT가 최신 코덱·캡션·다중 오디오에 유리
4. Stream이 ABR 인코딩 후 HLS/DASH/Player로 전송
5. `recording.mode=automatic`이면 방송마다 **새 video uid**. 종료 후 **약 60초** 내 녹화

RTMP가 끊겨도 소프트웨어가 재접속하면 인제스트 계속. OBS는 자동 재접속, FFmpeg는 설정 필요.

ABR 비트레이트 추정은 **실제 수신 비트레이트** 기반 (광고 비트레이트 불신). 저복잡도(슬라이드)는 낮은 추정 → 더 많은 시청자가 고화질. 스포츠 등은 높은 추정 → 버퍼 방지.

과금: Stream과 동일 (저장 분 + 전송 분). 라이브는 자동 녹화. 인코딩/패키징 추가 비용 없음. 시청자 0명이면 전송 분 $0, 녹화는 저장 분.

## Live input API

`POST /accounts/{account_id}/stream/live_inputs`

```json
{"meta": {"name":"test stream"}, "recording": {"mode": "automatic"}}
```

응답 필드: `uid`, `rtmps.{url,streamKey}`, `srt`(문서 예시에 따라 존재), `recording`, `enabled`, `deleteRecordingAfterDays`, `preferLowLatency`, `keysRotatedAt`(회전 후에만).

### 파라미터

| 필드 | 기본 | 의미 |
|------|------|------|
| `enabled` | true | false면 RTMPS/SRT 거부 (방송 종료/차단) |
| `preferLowLatency` | false | **Beta** LL-HLS. Player가 가능하면 LL-HLS. `recording.mode` 도 `automatic` 이어야 함 |
| `deleteRecordingAfterDays` | null | 녹화(입력 아님) 삭제. **30–1096**. 방송 종료 시 `scheduledDeletion` 계산. 라이브 중에 넣으면 **이후 방송만** |
| `timeoutSeconds` | 0 | 끊긴 뒤 새 video를 만들기 전까지 대기 |
| `recording.mode` | `off` | `automatic` = HLS/DASH 재생 + 녹화. `off` = 녹화/재생 없음 |
| `recording.requireSignedURLs` | false | 이 입력의 녹화·입력 ID 재생에 적용 (비디오 설정 덮어씀) |
| `recording.allowedOrigins` | null | 동일 |
| `recording.hideLiveViewerCount` | false | 시청자 수 숨김 |

| 동작 | 메서드 | 경로 |
|------|--------|------|
| 목록 | GET | `/stream/live_inputs` |
| 상세 | GET | `/stream/live_inputs/{id}` |
| 수정 | PUT | `/stream/live_inputs/{id}` |
| 삭제 | DELETE | `/stream/live_inputs/{id}` |
| 키 회전 | POST | `/stream/live_inputs/{id}/rotate_keys` |
| 방송/녹화 목록 | GET | `/stream/live_inputs/{id}/videos` |

키 회전: 옛 자격 폐기, 진행 중 방송 끊김, 새 key 반환. input uid는 유지.

## 시청: Input ID vs Video ID

| | Live Input ID | Video ID |
|--|---------------|----------|
| 언제 | 채널/크리에이터 상시 페이지 | 일회 이벤트/회차 |
| 라이브 중 | 현재 방송 | 그 방송 |
| 종료 후 | idle이면 "not started" / HTTP 204. **과거 방송은 input ID로 안 나옴** | 녹화 재생 |
| 안정성 | input uid는 고정 | **방송 시작마다 새 video uid** |

URL:

```
https://customer-<CODE>.cloudflarestream.com/<INPUT_ID|VIDEO_ID>/iframe
https://customer-<CODE>.cloudflarestream.com/<INPUT_ID|VIDEO_ID>/manifest/video.m3u8
```

`GET .../live_inputs/{id}/videos`: 첫 항목이 `live-inprogress`면 현재 방송. 나머지 `ready`는 녹화.

Chromecast: **DASH** 권장.

## 녹화 replay

`GET .../live_inputs/{id}/videos` 에서 `state=ready`. `playback.hls/dash`, `preview`, `liveInput`.

문서 일부 예시는 `https://dash.cloudflare.com/api/v4/accounts/...` 를 쓰나, 표준 베이스는 `https://api.cloudflare.com/client/v4/accounts/...`.

## DVR

`?dvrEnabled=true` (Player iframe 또는 HLS). **DASH 없음**. HLS v8 (다른 맥락은 v6).

Player: 시크 가능한 타임라인, LIVE 표시(뒤처지면 회색, 라이브 엣지면 빨강), pause 후 그 지점부터.

**권장: Video ID + DVR** — 라이브면 방송, 끝나면 녹화.

Input ID + DVR: 로드 시 현재 라이브만. 종료 후에도 계속 볼 수 있으나 **리로드하면** 최신 방송 또는 204. 과거 방송 없음. 종료 후 화질 전환 시 stall 가능.

제한: 7200 세그먼트, 3시간+ 성능 저하 가능. GOP가 세그먼트 길이.

## Live instant clipping

VOD `POST /stream/clip` 과 **다름**. 라이브러리 새 항목·추가 스토리지 없음. `recording.mode=automatic` 필요. **Video ID** 기준 (input ID 아님). 녹화 삭제 시 클립 소멸. 미리보기/클립 생성에 API 키 불필요.

미리보기 매니페스트 (최대 5분):

```
https://customer-<CODE>.cloudflarestream.com/<VIDEO_ID>/manifest/video.m3u8?duration=5m
```

응답 헤더: `preview-start-seconds` (방송 시작 기준 오프셋), `stream-media-id` (video id).

클립 URL 패턴은 원문 후반에 상세. 공식: [Live Instant Clipping](https://developers.cloudflare.com/stream/stream-live/live-instant-clipping/).

## Simulcast (restream)

입력당 **최대 50** outputs. YouTube/Twitch/Facebook 등.

```bash
curl -X POST \
  --data '{"url": "rtmp://a.rtmp.youtube.com/live2","streamKey": "<STREAM_KEY>"}' \
  -H "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream/live_inputs/<INPUT_UID>/outputs
```

방송 중 add/remove 가능. 개별 output enable/disable (대시보드 토글 또는 PUT output). 기본 enabled. DELETE 시 30초 내 끊김.

시뮬캐스트 전송 분은 **minutes delivered**.

| 동작 | 메서드 | 경로 |
|------|--------|------|
| 목록 | GET | `.../live_inputs/{id}/outputs` |
| 생성 | POST | 동일 |
| 수정 | PUT | `.../outputs/{output_id}` |
| 삭제 | DELETE | 동일 |

## Custom ingest domain

기본 `live.cloudflare.com` 대신 커스텀 RTMPS 도메인. **zone hold** 있으면 불가.

대시보드 Live inputs → Settings → Custom Input Domains. DNS **CNAME → `live.cloudflare.com`**. Cloudflare DNS면 **DNS only (grey cloud)**.

## Live webhooks

VOD 웹훅(`PUT /stream/webhook`)과 **다름**. Cloudflare **Notifications** 제품.

이벤트: `live_input.connected` | `live_input.disconnected` | `live_input.errored`.

payload `data`: `notification_name`, `input_id`, `event_type`, `updated_at`. 에러 시 `live_input_errored.error.{code,message}`, `video_codec`, `audio_codec`.

에러 코드: `ERR_GOP_OUT_OF_RANGE`, `ERR_UNSUPPORTED_VIDEO_CODEC`, `ERR_UNSUPPORTED_AUDIO_CODEC`, `ERR_STORAGE_QUOTA_EXHAUSTED`, `ERR_MISSING_SUBSCRIPTION`.

특정 input ID 콤마 목록으로 필터 가능 (빈 칸 = 전부).

## 권장 / 요구 / 제한

권장: 비트레이트 **잘 12 Mbps 미만**, GOP **2–8초**, **CBR**.

LL-HLS: B-frames **0**, GOP 2–4초, 가능하면 RTMP, OBS "ultra low".

요구: **Closed GOP**, 입력 코덱 **H.264 + AAC** (ADTS OK, LATM 미지원). Stream Connect로 릴레이만 하는 입력은 예외. 클라이언트 재접속 필수.

제한: 라이브 **워터마크 불가**. 녹화 **7일**에서 자름.

## 라이브 MP4 다운로드

Ready 이후 대시보드 Enable MP4 Downloads. **4시간 미만** 녹화만. 이상은 재생은 되나 MP4 불가.

## Troubleshooting (요약)

원문: https://developers.cloudflare.com/stream/stream-live/troubleshooting/

버퍼/프리즈/지연 — 대시보드 Live input → Metrics:

- Keyframe Interval: 2-8초 평평한 선. 물결/가변이면 고정값(먼저 4s). 지연이 크면 2s, 버퍼면 8s.
- Upload-to-Duration Ratio: 90% 아래 평평. 100%+ 이면 업로드가 재생보다 느림. 업링크 20 Mbps+, bitrate를 CBR 8 Mbps 또는 회선 70% 중 낮은 쪽, GOP를 늘림.

인코더 연결 실패: RTMPS URL/key 확인, input enabled=false 또는 키 회전 여부.

권장 방송: 12 Mbps 미만, GOP 2-8s, CBR, closed GOP, H.264+AAC.
