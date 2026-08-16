# 11 — Live API

공통 envelope·LiveInput·Output은 [11-api-reference.md](11-api-reference.md)에 있다. 단건 get/create/update는 `result: LiveInput`. 목록 항목은 전체 LiveInput이 아니다.

Auth: `Authorization: Bearer $CLOUDFLARE_API_TOKEN`

---

## 라이브 입력 목록 {#list-live-inputs}

**List live inputs**

`GET /accounts/{account_id}/stream/live_inputs`

계정에 만든 라이브 입력을 나열한다. 특정 입력으로 송출할 자격 증명은 **단건 조회**에서 받는다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `include_counts` | query | boolean | 아니오 | 제출한 query에 맞는 비디오 총 개수를 포함. |

### 응답

`result`: optional object (**LiveInput이 아님**).

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `liveInputs` | array of object | 아니오 | 요약 항목. 아래 하위 필드만. |
| `liveInputs[].created` | string | 아니오 | 라이브 입력 생성 일시. |
| `liveInputs[].deleteRecordingAfterDays` | number | 아니오 | 녹화 삭제까지 일수. 방송 종료·녹화 ready 후 scheduled deletion 계산. 생략 = 변경 없음. `null` = 예약 제거. |
| `liveInputs[].enabled` | boolean | 아니오 | 입력이 켜져 있고 스트림을 받을 수 있는지. |
| `liveInputs[].meta` | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `liveInputs[].modified` | string | 아니오 | 마지막 수정 일시. |
| `liveInputs[].uid` | string | 아니오 | 라이브 입력 unique identifier. |
| `range` | number | 아니오 | cursor 위치 기준 남은 라이브 입력 수. |
| `total` | number | 아니오 | 필터에 맞는 라이브 입력 총 수. |

목록 항목에 없는 LiveInput 필드: `keysRotatedAt`, `preferLowLatency`, `recording`, `rtmps`, `rtmpsPlayback`, `srt`, `srtPlayback`, `status`, `webRTC`, `webRTCPlayback`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 라이브 입력 상세 {#get-live-input}

**Retrieve a live input**

`GET /accounts/{account_id}/stream/live_inputs/{live_input_identifier}`

기존 라이브 입력의 상세를 가져온다. 송출 자격 증명(`rtmps`/`srt`/`webRTC`)은 여기 있다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |

### 응답

`result`: optional **LiveInput**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 라이브 입력 생성 {#create-live-input}

**Create a live input**

`POST /accounts/{account_id}/stream/live_inputs`

라이브 입력을 만들고, 본인 또는 사용자가 Cloudflare Stream으로 라이브 비디오를 송출할 자격 증명을 반환한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `defaultCreator` | body | string | 아니오 | 이 라이브 입력에 연결할 creator ID. |
| `deleteRecordingAfterDays` | body | number | 아니오 | 녹화 삭제까지 일수. 방송 종료·녹화 ready 후 scheduled deletion 계산. 생략 = 변경 없음. `null` = 예약 제거. |
| `enabled` | body | boolean | 아니오 | 입력이 켜져 있고 스트림을 받을 수 있는지. |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `preferLowLatency` | body | boolean | 아니오 | 켜면 LL-HLS로 전달. 지연은 줄고 플레이어 호환은 줄어든다. |
| `recording` | body | object | 아니오 | 입력을 Stream 비디오로 녹화. |
| `recording.allowedOrigins` | body | array of string | 아니오 | 이 입력으로 만든 비디오를 표시할 origin. `*` 와일드카드. 빈 배열이면 모든 origin. |
| `recording.hideLiveViewerCount` | body | boolean | 아니오 | `true`이면 라이브 시청자 수 보고를 끈다. |
| `recording.mode` | body | enum | 아니오 | `"off"` = 녹화 안 함. `"automatic"` = 녹화 시작, 입력이 멈추면 on-demand로 전환. |
| `recording.requireSignedURLs` | body | boolean | 아니오 | 이 입력을 쓰는 비디오에 `requireSignedURLs` 설정. 해당 입력의 라이브 녹화에도 접근 제어 강제. |
| `recording.timeoutSeconds` | body | number | 아니오 | `automatic`에서 라이브→on-demand 전환 전 대기. 대부분 `0`(플랫폼 기본) 권장. |

공식 스키마에서 body 필드는 모두 optional이다.

### 응답

`result`: optional **LiveInput**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "meta": { "name": "test stream 1" },
          "recording": { "mode": "off" }
        }'
```

(공식 example에는 `deleteRecordingAfterDays`, `enabled`, `preferLowLatency`, `recording.hideLiveViewerCount`/`requireSignedURLs`/`timeoutSeconds`도 있다.)

---

## 라이브 입력 수정 {#update-live-input}

**Update a live input**

`PUT /accounts/{account_id}/stream/live_inputs/{live_input_identifier}`

지정한 라이브 입력을 수정한다. body 필드는 생성과 동일하다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |
| `defaultCreator` | body | string | 아니오 | 이 라이브 입력에 연결할 creator ID. |
| `deleteRecordingAfterDays` | body | number | 아니오 | 녹화 삭제까지 일수. 생략 = 변경 없음. `null` = 예약 제거. |
| `enabled` | body | boolean | 아니오 | 입력이 켜져 있고 스트림을 받을 수 있는지. |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `preferLowLatency` | body | boolean | 아니오 | 켜면 LL-HLS로 전달. |
| `recording` | body | object | 아니오 | 생성과 동일. `allowedOrigins`, `hideLiveViewerCount`, `mode` (`"off"`\|`"automatic"`), `requireSignedURLs`, `timeoutSeconds`. |

### 응답

`result`: optional **LiveInput**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER \
    -X PUT \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{"enabled": false}'
```

(제품 문서 start-stream-live의 enable/disable 예. API index example도 동일 body 필드를 쓴다.)

---

## 라이브 입력 삭제 {#delete-live-input}

**Delete a live input**

`DELETE /accounts/{account_id}/stream/live_inputs/{live_input_identifier}`

해당 입력으로 더 이상 송출하지 못하게 하고, 이후 API 호출에서도 접근할 수 없게 한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |

### 응답

API index에 Returns 필드 표 없음. 공식 example 본문은 `{}`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 방송 키 회전 {#rotate-keys}

**Rotate broadcast keys** — `출처: 제품 문서` (API index에 이 path의 독립 operation 없음)

`POST /accounts/{account_id}/stream/live_inputs/{input_id}/rotate_keys`

자격 증명이 잘못된 청중과 공유됐거나, 클라이언트 코드/스크린셰어에 노출됐거나, 보안 절차상 갱신이 필요할 때 방송 자격 증명을 회전한다. 회전해도 live input ID와 다른 설정은 바뀌지 않는다.

키가 회전되면 이전 자격 증명은 폐기되고, 낡은 자격 증명으로 하던 방송은 끊기며, 갱신된 자격 증명이 API 응답으로 돌아온다.

Live input 응답의 `keysRotatedAt`은 마지막 회전 시각이다. 한 번도 회전하지 않은 입력에서는 이 필드가 생략된다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | 제품 경로 `{account_id}`. |
| `input_id` | path | string | 예 | 제품 경로 `{input_id}`. |

제품 페이지는 Query/Header/Body 파라미터를 나열하지 않는다. 예는 Authorization 헤더만 있다.

### 응답

제품 페이지에 `result` 필드 표 없음. 문구상 "refreshed credentials are returned in the API response". 이후 get live input의 `keysRotatedAt`이 채워진다.

### 예

```bash
curl --request POST \
  https://api.cloudflare.com/client/v4/accounts/{account_id}/stream/live_inputs/{input_id}/rotate_keys \
  --header "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 라이브 입력의 비디오 목록 {#live-input-videos}

**List videos for a live input** — `출처: 제품 문서` (API index에 이 path의 독립 operation 없음)

`GET /accounts/{account_id}/stream/live_inputs/{live_input_uid}/videos`

플레이어 코드나 manifest URL을 API로 받으려면 이 목록을 조회한다. 한 live input에는 방송마다 비디오가 생긴다. 진행 중 방송이 있으면 응답의 첫 비디오 `status.state`가 `live-inprogress`다. 나머지는 on-demand로 재생할 녹화다.

각 비디오(진행 중 방송 포함)에 HLS/DASH URL과 Stream player 링크가 있다. 제품 페이지가 강조하는 속성: `preview`, `playback.hls`, `playback.dash`.

녹화만 보려면 `state`가 `ready`인 항목을 필터한다 (replay-recordings).

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | 제품 경로. |
| `live_input_uid` | path | string | 예 | 제품 경로 `<LIVE_INPUT_UID>`. |

제품 페이지는 Query/Body를 나열하지 않는다.

### 응답

제품 example의 `result`는 **Video 배열**. 필드 표는 없고 Video 예시만 있다 (`uid`, `thumbnail`, `status`, `meta`, `created`, `modified`, `size`, `preview`, `playback` 등). 전체 스키마는 [Video](11-api-reference.md#video-객체).

주의: replay-recordings 페이지 example host는 `https://dash.cloudflare.com/api/v4/...` 이다. watch-live-stream 페이지는 `https://api.cloudflare.com/client/v4/...` 를 쓴다. REST base는 후자.

### 예

```bash
curl -X GET \
  -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
  https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_UID/videos
```

---

## Output 목록 {#list-outputs}

**List all outputs associated with a specified live input**

`GET /accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs`

지정한 라이브 입력에 연결된 모든 output을 가져온다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |

### 응답

`result`: optional array of **Output**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER/outputs \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## Output 추가 {#create-output}

**Create a new output, connected to a live input**

`POST /accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs`

다른 RTMP 또는 SRT destination으로 시뮬캐스트/restream할 output을 만든다. output은 항상 특정 live input에 연결된다. 한 입력에 output을 여러 개 둘 수 있다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |
| `streamKey` | body | string | 예 | output target 인증용 streamKey. |
| `url` | body | string | 예 | restream에 쓰는 URL. |
| `enabled` | body | boolean | 아니오 | 켜면 해당 입력의 라이브를 output URL로 보낸다. 끄면 송출 중이어도 보내지 않음. |

### 응답

`result`: optional **Output**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER/outputs \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "streamKey": "uzya-f19y-g2g9-a2ee-51j2",
          "url": "rtmp://a.rtmp.youtube.com/live2",
          "enabled": true
        }'
```

---

## Output 수정 {#update-output}

**Update an output**

`PUT /accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs/{output_identifier}`

output의 상태를 수정한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |
| `output_identifier` | path | string | 예 | output unique identifier. |
| `enabled` | body | boolean | 예 | 켜면 해당 입력의 라이브를 output URL로 보낸다. 끄면 보내지 않음. |

### 응답

`result`: optional **Output**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER/outputs/$OUTPUT_IDENTIFIER \
    -X PUT \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{"enabled": true}'
```

---

## Output 삭제 {#delete-output}

**Delete an output**

`DELETE /accounts/{account_id}/stream/live_inputs/{live_input_identifier}/outputs/{output_identifier}`

output을 삭제하고 연결된 라이브 입력에서 제거한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `live_input_identifier` | path | string | 예 | 라이브 입력 unique identifier. |
| `output_identifier` | path | string | 예 | output unique identifier. |

### 응답

API index에 Returns 필드 표 없음. 공식 example 본문은 `{}`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/live_inputs/$LIVE_INPUT_IDENTIFIER/outputs/$OUTPUT_IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```
