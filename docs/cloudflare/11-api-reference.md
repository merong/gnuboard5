# 11 — REST API reference (허브)

**Base:** `https://api.cloudflare.com/client/v4`

**Auth:** `Authorization: Bearer $CLOUDFLARE_API_TOKEN` (모든 REST)

경로의 `{account_id}` = account identifier tag. `{identifier}` / `{video_uid}` = media uid. `{live_input_identifier}` = live input uid.

원문 전체 스키마: [API Stream index](https://developers.cloudflare.com/api/resources/stream/) (raw: `/workspace/api-docs/_raw/cloudflare-stream/api/resources/stream/index.md`).

이 파일은 **공통 envelope + Video 객체 + 공유 타입 + 메서드 인덱스**만 둔다. 엔드포인트별 Path/Query/Header/Body/Returns 표는 아래 상세 파일에 있다.

| 파일 | 내용 |
|------|------|
| [11-api-videos.md](11-api-videos.md) | list / get / create(tus·multipart) / edit / delete / copy / direct_upload / clip / embed / token / storage-usage |
| [11-api-live.md](11-api-live.md) | live_inputs CRUD, rotate_keys, videos, outputs |
| [11-api-edit.md](11-api-edit.md) | captions, audio, watermarks, downloads |
| [11-api-keys-webhooks.md](11-api-keys-webhooks.md) | signing keys, VOD webhook |

`출처: 제품 문서` 표시가 있는 엔드포인트는 API index에 없고 제품 페이지에 method+path(+params)가 있다.

---

## 공통 응답 envelope

모든 JSON REST 응답(성공/실패)은 이 래퍼를 쓴다. **이후 엔드포인트에서는 반복하지 않는다.** 각 엔드포인트 Returns 표는 `result`(및 list의 `range`/`total`)만 적는다.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `success` | `true` | 예 | API 호출 성공 여부. 공식 스키마 값은 `true`. |
| `errors` | array of object | 예 | `{ code: number, message: string, documentation_url?: string, source?: { pointer?: string } }` |
| `messages` | array of object | 예 | `errors`와 동일 형상 `{ code, message, documentation_url?, source? }` |
| `result` | (엔드포인트별) | 아니오 | 성공 페이로드. 타입은 각 엔드포인트에 적는다. |

리스트 전용(비디오 목록): envelope에 추가로 `range`, `total`이 올 수 있다. 라이브 입력 목록은 이 두 값이 `result` 안에 있다.

실패 시 embed는 JSON 본문, 성공 시 HTML fragment(아래 Embed).

---

## Video 객체

비디오를 반환하는 엔드포인트는 `result: Video`(또는 `result: Video[]`)만 적고, **여기 필드를 반복하지 않는다.** extra/missing만 엔드포인트에 적는다.

`AllowedOrigins` = `string` (허용 origin 도메인. `*` 로 서브도메인 와일드카드).

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `allowedOrigins` | array of AllowedOrigins | 아니오 | 비디오를 표시할 수 있는 origin. 빈 배열이면 모든 origin에서 시청 가능. |
| `clippedFrom` | string | 아니오 | 이 비디오가 클립된 원본 비디오의 unique identifier. |
| `created` | string | 아니오 | 미디어 생성 일시. |
| `creator` | string | 아니오 | 미디어 creator에 대한 사용자 정의 identifier. |
| `duration` | number | 아니오 | 초 단위 길이. `-1`이면 아직 모름. 업로드 후, ready 전에 채워진다. |
| `input` | object | 아니오 | `{ height?: number, width?: number }`. 픽셀. `-1`이면 모름. 업로드 후, ready 전에 채워진다. |
| `liveInput` | string | 아니오 | Stream Live로 올린 비디오의 live input ID. |
| `maxDurationSeconds` | number | 아니오 | 아직 업로드되지 않은 비디오의 최대 길이(초). 초과 업로드는 처리 중 실패. `-1`이면 모름. |
| `maxSizeBytes` | number | 아니오 | 비디오 업로드 최대 크기(바이트). |
| `meta` | unknown | 아니오 | 다른 시스템 레코드를 참조하기 위한 사용자 수정 가능 key-value store. |
| `modified` | string | 아니오 | 마지막 수정 일시. |
| `playback` | object | 아니오 | `{ dash?: string, hls?: string }`. DASH MPD / HLS manifest URL. |
| `preview` | string | 아니오 | 미리보기 페이지 URI. 인코딩 완료 전에는 생략. |
| `publicDetails` | object | 아니오 | 공개 상세. 아래 하위 필드. |
| `publicDetails.channel_link` | string | 아니오 | (하위 필드 설명 없음) |
| `publicDetails.logo` | string | 아니오 | (하위 필드 설명 없음) |
| `publicDetails.media_id` | number | 아니오 | (하위 필드 설명 없음) |
| `publicDetails.share_link` | string | 아니오 | (하위 필드 설명 없음) |
| `publicDetails.title` | string | 아니오 | (하위 필드 설명 없음) |
| `readyToStream` | boolean | 아니오 | 재생 가능 여부. 아직 볼 수 없거나 라이브가 진행 중이면 비어 있음. |
| `readyToStreamAt` | string | 아니오 | 재생 가능해진 시각. 아직 볼 수 없거나 라이브가 진행 중이면 비어 있음. |
| `requireSignedURLs` | boolean | 아니오 | `true`이면 UID로 접근 불가. signing key로 signed token을 만들어야 시청 가능. |
| `scheduledDeletion` | string | 아니오 | 삭제 예정 일시. 필드 생략 = 변경 없음. `null` = 기존 예약 제거. 지정 시 업로드 시각으로부터 최소 30일. |
| `size` | number | 아니오 | 미디어 크기(바이트). |
| `status` | object | 아니오 | 상세 처리 상태. 아래 하위 필드. `state`가 `inprogress` 또는 `error`이면 설명 문구상 `step`이 `encoding` 또는 `manifest`를 반환한다고 되어 있으나, 스키마 필드 목록에는 `step`이 없다. |
| `status.errorReasonCode` | string | 아니오 | 인코딩 실패 이유. `error`가 아니면 비어 있음. 프로그램용. |
| `status.errorReasonText` | string | 아니오 | 인코딩 실패 이유(영어 사람용). `error`가 아니면 비어 있음. |
| `status.pctComplete` | string | 아니오 | 진행률 0–100. |
| `status.state` | enum | 아니오 | 모든 quality level의 처리 상태. |
| `thumbnail` | string | 아니오 | 썸네일 URI. 인코딩 완료 전에는 생략. |
| `thumbnailTimestampPct` | number | 아니오 | 썸네일 시각을 영상 길이 대비 비율(0–1)로. 초 → 비율은 원하는 초 / 전체 길이. 미설정이면 0초. |
| `uid` | string | 아니오 | Cloudflare가 생성한 미디어 unique identifier. |
| `uploaded` | string | 아니오 | 업로드 일시. |
| `uploadExpiry` | string | 아니오 | direct user upload URL이 더 이상 유효하지 않은 일시. |
| `watermark` | Watermark | 아니오 | 적용된 워터마크 프로필. 아래 Watermark 타입. |

`status.state` enum: `"pendingupload"` \| `"downloading"` \| `"queued"` \| `"inprogress"` \| `"ready"` \| `"error"` \| `"live-inprogress"`

---

## 공유 타입

엔드포인트 Returns가 이 타입을 가리키면 필드를 다시 펼치지 않는다.

### Watermark

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `created` | string | 아니오 | 워터마크 프로필 생성 일시. |
| `downloadedFrom` | string | 아니오 | 다운로드한 이미지의 source URL. direct upload로 만들면 `null`. |
| `height` | number | 아니오 | 이미지 높이(픽셀). |
| `name` | string | 아니오 | 워터마크 프로필 짧은 설명. |
| `opacity` | number | 아니오 | 투명도. `0.0` 완전 투명, `1.0` 완전 불투명. 이미지가 이미 반투명이면 `1.0`이어도 완전 불투명이 되지 않을 수 있음. |
| `padding` | number | 아니오 | 영상 가장자리와 이미지 사이 여백. `0.0` 없음, `1.0`은 알고리즘이 정한 방향의 전체 폭/길이. |
| `position` | string | 아니오 | 위치. 유효값: `upperRight`, `upperLeft`, `lowerLeft`, `lowerRight`, `center`. `center`는 `padding`을 무시. |
| `scale` | number | 아니오 | 영상 대비 이미지 크기. 가로/세로 영상에 자동 적응. `0.0` 스케일 없음(원본 크기), `1.0` 영상 전체. |
| `size` | number | 아니오 | 이미지 크기(바이트). |
| `uid` | string | 아니오 | 워터마크 프로필 unique identifier. |
| `width` | number | 아니오 | 이미지 너비(픽셀). |

### Audio

추가 오디오 트랙. 원본 업로드에 붙은 오디오는 list API가 반환하지 않는다.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `default` | boolean | 아니오 | 플레이어에서 기본 재생되는 트랙인지. |
| `label` | string | 아니오 | 해당 비디오의 다른 오디오 트랙 label 중 고유한 문자열. |
| `status` | enum | 아니오 | `"queued"` \| `"ready"` \| `"error"`. 처리 상태. |
| `uid` | string | 아니오 | Cloudflare가 생성한 미디어 unique identifier. |

### Caption

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `generated` | boolean | 아니오 | AI로 생성된 캡션인지. |
| `label` | string | 아니오 | 사용자에게 원어로 표시되는 언어 라벨. |
| `language` | string | 아니오 | BCP 47 language tag. |
| `status` | enum | 아니오 | 생성된 캡션 상태. `"ready"` \| `"inprogress"` \| `"error"`. |

### LiveInput

단건 get/create/update 응답. **목록 항목은 이 전체 객체가 아니다** — [11-api-live.md](11-api-live.md#list-live-inputs)의 list `result.liveInputs[]` 필드를 본다.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `created` | string | 아니오 | 라이브 입력 생성 일시. |
| `deleteRecordingAfterDays` | number | 아니오 | 녹화 삭제까지 일수. 방송 종료·녹화 ready 후 scheduled deletion 계산에 사용. 생략 = 변경 없음. `null` = 기존 예약 제거. |
| `enabled` | boolean | 아니오 | 입력이 켜져 있고 스트림을 받을 수 있는지. |
| `keysRotatedAt` | string | 아니오 | 키를 마지막으로 회전한 일시. 한 번도 회전하지 않은 입력에서는 생략. |
| `meta` | unknown | 아니오 | 라이브 입력을 관리하기 위한 사용자 수정 가능 key-value store. |
| `modified` | string | 아니오 | 마지막 수정 일시. |
| `preferLowLatency` | boolean | 아니오 | 켜면 LL-HLS로 전달. glass-to-glass 지연은 줄고 플레이어 호환은 줄어든다. |
| `recording` | object | 아니오 | 입력을 Stream 비디오로 녹화. 모드에 따라 동작. 대개 처음엔 라이브로 보이다가 조건 충족 후 on-demand로 전환. |
| `recording.allowedOrigins` | array of string | 아니오 | 이 입력으로 만든 비디오를 표시할 origin. `*` 와일드카드. 빈 배열이면 모든 origin. |
| `recording.hideLiveViewerCount` | boolean | 아니오 | `true`이면 라이브 시청자 수 보고를 끈다. |
| `recording.mode` | enum | 아니오 | `"off"` = 녹화 안 함. `"automatic"` = 녹화 시작, Stream Live가 입력을 멈추면 on-demand로 전환. |
| `recording.requireSignedURLs` | boolean | 아니오 | 이 입력을 쓰는 비디오에 `requireSignedURLs`를 설정. 해당 입력의 라이브 녹화에도 접근 제어를 강제. |
| `recording.timeoutSeconds` | number | 아니오 | `automatic` 모드에서 라이브→on-demand 전환 전 대기 시간. 대부분 `0`(플랫폼 기본) 권장. |
| `rtmps` | object | 아니오 | RTMPS 송출. `{ streamKey?: string, url?: string }` |
| `rtmpsPlayback` | object | 아니오 | RTMPS 재생. `{ streamKey?: string, url?: string }` |
| `srt` | object | 아니오 | SRT 송출. `{ passphrase?: string, streamId?: string, url?: string }` |
| `srtPlayback` | object | 아니오 | SRT 재생. `{ passphrase?: string, streamId?: string, url?: string }` |
| `status` | enum | 아니오 | 연결 상태. `"connected"` \| `"reconnected"` \| `"reconnecting"` \| `"client_disconnect"` \| `"ttl_exceeded"` \| `"failed_to_connect"` \| `"failed_to_reconnect"` \| `"new_configuration_accepted"` |
| `uid` | string | 아니오 | 라이브 입력 unique identifier. |
| `webRTC` | object | 아니오 | WebRTC 송출. `{ url?: string }` |
| `webRTCPlayback` | object | 아니오 | WebRTC 재생. `{ url?: string }` |

### Output (simulcast)

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `enabled` | boolean | 아니오 | 켜면 해당 live input으로 들어온 라이브를 output URL로 보낸다. 끄면 송출 중이어도 그 destination으로는 보내지 않음. |
| `streamKey` | string | 아니오 | output target 인증용 streamKey. |
| `uid` | string | 아니오 | output unique identifier. |
| `url` | string | 아니오 | restream에 쓰는 URL. |

### Download 항목

`result.default` / `result.audio` 각각. 해당 타입이 생성된 경우에만 존재.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `percentComplete` | number | 예 | 진행률 0–100. |
| `status` | enum | 예 | `"ready"` \| `"inprogress"` \| `"error"` |
| `url` | string | 아니오 | 생성된 다운로드에 접근하는 URL. |

---

## 메서드 인덱스

표의 링크는 상세 파일의 앵커로 간다. **파라미터/응답 표는 허브에 두지 않는다.**

### Video / upload / library

| Method | Path | 상세 |
|--------|------|------|
| GET | `/accounts/{account_id}/stream` | [비디오 목록](11-api-videos.md#list) |
| GET | `/accounts/{account_id}/stream/{identifier}` | [비디오 상세](11-api-videos.md#get) |
| POST | `/accounts/{account_id}/stream` | [tus 업로드 개시](11-api-videos.md#create-tus) |
| POST | `/accounts/{account_id}/stream` (`multipart/form-data`) | [기본 파일 업로드](11-api-videos.md#create-multipart) `출처: 제품 문서` |
| POST | `/accounts/{account_id}/stream?direct_user=true` | [엔드유저 tus Location](11-api-videos.md#create-tus) (`direct_user` query) |
| POST | `/accounts/{account_id}/stream/{identifier}` | [비디오 수정](11-api-videos.md#edit) |
| DELETE | `/accounts/{account_id}/stream/{identifier}` | [비디오 삭제](11-api-videos.md#delete) |
| GET | `/accounts/{account_id}/stream/storage-usage` | [스토리지 사용량](11-api-videos.md#storage-usage) |
| POST | `/accounts/{account_id}/stream/copy` | [URL에서 가져오기](11-api-videos.md#copy) |
| POST | `/accounts/{account_id}/stream/direct_upload` | [Direct upload URL](11-api-videos.md#direct-upload) |
| POST | `/accounts/{account_id}/stream/clip` | [클립](11-api-videos.md#clip) |
| GET | `/accounts/{account_id}/stream/{identifier}/embed` | [embed HTML](11-api-videos.md#embed) |
| POST | `/accounts/{account_id}/stream/{identifier}/token` | [signed token](11-api-videos.md#token) |

### Live

| Method | Path | 상세 |
|--------|------|------|
| GET | `/accounts/{account_id}/stream/live_inputs` | [입력 목록](11-api-live.md#list-live-inputs) |
| POST | `/accounts/{account_id}/stream/live_inputs` | [입력 생성](11-api-live.md#create-live-input) |
| GET | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}` | [입력 상세](11-api-live.md#get-live-input) |
| PUT | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}` | [입력 수정](11-api-live.md#update-live-input) |
| DELETE | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}` | [입력 삭제](11-api-live.md#delete-live-input) |
| POST | `/accounts/{account_id}/stream/live_inputs/{input_id}/rotate_keys` | [키 회전](11-api-live.md#rotate-keys) `출처: 제품 문서` |
| GET | `/accounts/{account_id}/stream/live_inputs/{live_input_uid}/videos` | [방송+녹화 목록](11-api-live.md#live-input-videos) `출처: 제품 문서` |
| GET | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs` | [output 목록](11-api-live.md#list-outputs) |
| POST | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs` | [output 추가](11-api-live.md#create-output) |
| PUT | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs/{output_identifier}` | [output 수정](11-api-live.md#update-output) |
| DELETE | `/accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs/{output_identifier}` | [output 삭제](11-api-live.md#delete-output) |

### Edit — captions, audio, watermarks, downloads

| Method | Path | 상세 |
|--------|------|------|
| GET | `/accounts/{account_id}/stream/{identifier}/captions` | [캡션 목록](11-api-edit.md#list-captions) |
| GET | `/accounts/{account_id}/stream/{identifier}/captions/{language}` | [언어 상세](11-api-edit.md#get-caption) |
| POST | `/accounts/{account_id}/stream/{identifier}/captions/{language}/generate` | [AI 캡션](11-api-edit.md#generate-captions) |
| PUT | `/accounts/{account_id}/stream/{identifier}/captions/{language}` | [VTT 업로드](11-api-edit.md#upload-captions) |
| DELETE | `/accounts/{account_id}/stream/{identifier}/captions/{language}` | [캡션 삭제](11-api-edit.md#delete-captions) |
| GET | `/accounts/{account_id}/stream/{identifier}/captions/{language}/vtt` | [WebVTT](11-api-edit.md#get-vtt) |
| GET | `/accounts/{account_id}/stream/{identifier}/audio` | [오디오 목록](11-api-edit.md#list-audio) |
| POST | `/accounts/{account_id}/stream/{identifier}/audio` | [파일로 트랙 추가](11-api-edit.md#add-audio-file) `출처: 제품 문서` |
| POST | `/accounts/{account_id}/stream/{identifier}/audio/copy` | [URL로 트랙 추가](11-api-edit.md#copy-audio) |
| PATCH | `/accounts/{account_id}/stream/{identifier}/audio/{audio_identifier}` | [트랙 수정](11-api-edit.md#edit-audio) |
| DELETE | `/accounts/{account_id}/stream/{identifier}/audio/{audio_identifier}` | [트랙 삭제](11-api-edit.md#delete-audio) |
| GET | `/accounts/{account_id}/stream/watermarks` | [워터마크 목록](11-api-edit.md#list-watermarks) |
| POST | `/accounts/{account_id}/stream/watermarks` | [워터마크 생성](11-api-edit.md#create-watermark) |
| GET | `/accounts/{account_id}/stream/watermarks/{identifier}` | [워터마크 상세](11-api-edit.md#get-watermark) |
| DELETE | `/accounts/{account_id}/stream/watermarks/{identifier}` | [워터마크 삭제](11-api-edit.md#delete-watermark) |
| GET | `/accounts/{account_id}/stream/{identifier}/downloads` | [다운로드 상태](11-api-edit.md#list-downloads) |
| POST | `/accounts/{account_id}/stream/{identifier}/downloads` | [MP4 생성](11-api-edit.md#create-downloads) |
| POST | `/accounts/{account_id}/stream/{identifier}/downloads/audio` | [M4A 생성](11-api-edit.md#create-download-audio) `출처: 제품 문서` |
| DELETE | `/accounts/{account_id}/stream/{identifier}/downloads` | [다운로드 삭제](11-api-edit.md#delete-downloads) |
| DELETE | `/accounts/{account_id}/stream/{identifier}/downloads/default` | [MP4 삭제](11-api-edit.md#delete-download-default) `출처: 제품 문서` |

### Keys / VOD webhooks

| Method | Path | 상세 |
|--------|------|------|
| GET | `/accounts/{account_id}/stream/keys` | [signing key 목록](11-api-keys-webhooks.md#list-keys) |
| POST | `/accounts/{account_id}/stream/keys` | [키 생성](11-api-keys-webhooks.md#create-keys) |
| DELETE | `/accounts/{account_id}/stream/keys/{identifier}` | [키 폐기](11-api-keys-webhooks.md#delete-keys) |
| GET | `/accounts/{account_id}/stream/webhook` | [VOD 웹훅 조회](11-api-keys-webhooks.md#get-webhook) |
| PUT | `/accounts/{account_id}/stream/webhook` | [VOD 웹훅 설정](11-api-keys-webhooks.md#update-webhook) |
| DELETE | `/accounts/{account_id}/stream/webhook` | [VOD 웹훅 삭제](11-api-keys-webhooks.md#delete-webhook) |

라이브 웹훅은 Notifications 대시보드다. REST `PUT /stream/webhook`이 아니다. 근거: 제품 문서 [Receive Live Webhooks](https://developers.cloudflare.com/stream/stream-live/webhooks/). 상세는 [11-api-keys-webhooks.md](11-api-keys-webhooks.md#live-webhooks).

---

## Playback / ingest (비 REST, 호스트 다름)

| Method | URL | Purpose |
|--------|-----|---------|
| GET | `https://customer-<CODE>.cloudflarestream.com/<UID>/iframe` | Player |
| GET | `.../<UID>/watch` | preview |
| GET | `.../<UID>/manifest/video.m3u8` | HLS (`?protocol=llhls`, `?dvrEnabled=true`, `?clientBandwidthHint=`, `?duration=`) |
| GET | `.../<UID>/manifest/video.mpd` | DASH |
| GET | `.../<UID>/thumbnails/thumbnail.jpg` | 썸네일 |
| GET | `.../<UID>/thumbnails/thumbnail.gif` | GIF 썸네일 |
| GET | `.../<UID>/downloads/default.mp4` | MP4 |
| GET | `.../<UID>/downloads/audio.m4a` | M4A |
| GET | `.../<INPUT_ID>/views` | `{ liveViewers }` |
| GET | `.../<UID>/metadata/playerEnhancementInfo.json` | publicDetails |
| POST | `https://upload.videodelivery.net/{uid}` | DCU basic 업로드 |
| — | `rtmps://live.cloudflare.com:443/live/` + streamKey | 라이브 인제스트 |
| — | `.../<SECRET>/webRTC/publish` | WHIP |
| — | `.../<INPUT_UID>/webRTC/play` | WHEP |

## GraphQL (Analytics)

Cloudflare GraphQL Analytics API. 노드 `streamMinutesViewedAdaptiveGroups`. Stream REST base 아님.

## 헤더 치트

| 헤더 | 어디서 |
|------|--------|
| `Authorization: Bearer` | 모든 REST |
| `Tus-Resumable: 1.0.0` | tus (공식 지원 버전은 1.0.0만) |
| `Upload-Length` | tus 생성. 전체 업로드 바이트. 음이 아닌 정수 |
| `Upload-Metadata` | tus 옵션. comma-separated, 값은 Base-64. 지원 키: `name`, `requiresignedurls`, `allowedorigins`, `thumbnailtimestamppct`, `watermark`, `scheduleddeletion`, `maxdurationseconds` |
| `Upload-Creator` | tus / copy / direct_upload creator |
| `stream-media-id` | tus 응답 → video uid (제품 문서 resumable-uploads) |
| `Location` | tus 업로드 URL (공식: 201 + `location` 헤더) |
| `Webhook-Signature` | VOD 웹훅 요청 (제품 문서 using-webhooks) |
| `preview-start-seconds` / `stream-media-id` | live preview manifest |

공식 필드명·타입·경로·헤더명은 영어 그대로 유지한다. 한도/타입을 추측하지 말 것.
