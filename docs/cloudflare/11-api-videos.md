# 11 — Videos API

공통 envelope·Video 객체는 [11-api-reference.md](11-api-reference.md)에 한 번만 있다. 여기서는 `result: Video`라고만 하고 extra/missing만 적는다.

Auth: `Authorization: Bearer $CLOUDFLARE_API_TOKEN`

---

## 비디오 목록 {#list}

**List videos**

`GET /accounts/{account_id}/stream`

한 요청에서 최대 1000개 비디오를 나열한다. 특정 구간은 optional query를 쓴다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `id` | query | string | 아니오 | video ID로 필터. 단일 ID 또는 comma-separated ID 목록. |
| `after` | query | string | 아니오 | `start`의 alias. 이 일시 이후에 생성된 비디오 (RFC 3339). |
| `asc` | query | boolean | 아니오 | 생성 시각 오름차순. |
| `before` | query | string | 아니오 | `end`의 alias. 이 일시 이전에 생성된 비디오 (RFC 3339). |
| `creator` | query | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `end` | query | string | 아니오 | 지정 일시 이전에 생성된 비디오. |
| `include_counts` | query | boolean | 아니오 | 제출한 query에 맞는 비디오 총 개수를 포함. |
| `limit` | query | number | 아니오 | 반환 최대 개수 (default 1000, max 1000). |
| `live_input_id` | query | string | 아니오 | 특정 라이브 스트림과 연관된 비디오. |
| `name` | query | string | 아니오 | 비디오 name/UID로 필터. 단일 또는 comma-separated 목록. |
| `search` | query | string | 아니오 | `meta.name`에 대한 부분 단어 일치. 중·대규모 라이브러리에서는 느림. 매우 큰 라이브러리에서는 사용 불가일 수 있음. |
| `start` | query | string | 아니오 | 지정 일시 이후에 생성된 비디오. |
| `status` | query | enum | 아니오 | 모든 quality level의 처리 상태. `"pendingupload"` \| `"downloading"` \| `"queued"` \| `"inprogress"` \| `"ready"` \| `"error"` \| `"live-inprogress"` |
| `type` | query | string | 아니오 | `vod` 또는 `live`. |
| `video_name` | query | string | 아니오 | `meta.name`에 대한 빠른 정확 문자열 일치. |

### 응답

`result`: optional array of **Video**.

envelope extra (Video가 아님):

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `range` | number | 아니오 | cursor 위치 기준 남은 비디오 수. |
| `total` | number | 아니오 | 필터에 맞는 비디오 총 수. |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 비디오 상세 {#get}

**Retrieve video details**

`GET /accounts/{account_id}/stream/{identifier}`

단일 비디오의 상세를 가져온다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional **Video**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## tus 업로드 개시 {#create-tus}

**Initiate video uploads using TUS**

`POST /accounts/{account_id}/stream`

TUS 프로토콜로 비디오 업로드를 시작한다. 성공 시 **HTTP 201**과 `location` 헤더(업로드할 URL). 프로토콜 상세는 https://tus.io . JSON `result` 스키마는 API index에 없다.

`direct_user=true`이면 API token을 클라이언트에 노출하지 않고 엔드유저가 직접 업로드할 URL을 발급한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `direct_user` | query | boolean | 아니오 | 엔드유저가 API token 없이 Stream에 직접 업로드할 URL을 발급. |
| `Tus-Resumable` | header | `"1.0.0"` | 예 | TUS 프로토콜 버전. 모든 업로드 요청에 포함. 지원 버전은 `1.0.0`만. |
| `Upload-Length` | header | number | 예 | 전체 업로드 크기(바이트). 음이 아닌 정수. |
| `Upload-Creator` | header | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `Upload-Metadata` | header | string | 아니오 | TUS 규격 comma-separated key-value. 값은 Base-64. 지원 키: `name`, `requiresignedurls`, `allowedorigins`, `thumbnailtimestamppct`, `watermark`, `scheduleddeletion`, `maxdurationseconds`. |

### 응답

JSON `result` 없음. 공식: status **201** + `location` 헤더.

제품 문서(resumable-uploads)는 응답 헤더 `stream-media-id`가 video uid라고 한다. `출처: 제품 문서`

### 예

공식 example은 Authorization만 보여 준다. 필수 tus 헤더는 위 표를 따른다.

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream \
    -X POST \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -H "Tus-Resumable: 1.0.0" \
    -H "Upload-Length: 52428800"
```

---

## 기본 multipart 업로드 {#create-multipart}

**Basic video uploads** — `출처: 제품 문서` (API index의 POST `/stream`은 TUS만 기술)

`POST /accounts/{account_id}/stream`

200 MB 미만 파일은 form 업로드를 쓸 수 있다. `Content-Type: multipart/form-data`, 미디어 input 이름은 `file`.

제품 페이지는 응답 필드 표를 주지 않는다. 성공 시 Video가 온다는 공식 Returns 블록도 없다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | account identifier (제품 경로 `{account_id}`). |
| `file` | body (form) | file | 예 | 업로드할 미디어. input name = `file`. |

### 응답

제품 문서에 `result` 필드 표 없음.

### 예

```bash
curl --request POST \
  --header "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
  --form file=@/Users/user_name/Desktop/my-video.mp4 \
  https://api.cloudflare.com/client/v4/accounts/{account_id}/stream
```

cURL `--form`은 `content-type`을 자동 설정하고 파일을 `file` input에 매핑한다.

---

## 비디오 수정 {#edit}

**Edit video details**

`POST /accounts/{account_id}/stream/{identifier}`

단일 비디오의 상세를 수정한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `allowedOrigins` | body | array of AllowedOrigins | 아니오 | 표시 허용 origin. `*` 와일드카드. 빈 배열이면 모든 origin. |
| `creator` | body | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `maxDurationSeconds` | body | number | 아니오 | 아직 업로드되지 않은 비디오의 최대 길이(초). 초과 시 처리 중 실패. `-1`이면 모름. |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `publicDetails` | body | object | 아니오 | 공개 상세. 하위: `channel_link?`, `logo?`, `share_link?`, `title?` (모두 optional string). 요청 스키마에는 `media_id`가 없다 (Video 응답에는 있음). |
| `requireSignedURLs` | body | boolean | 아니오 | `true`이면 UID 접근 불가. signed token 필요. |
| `scheduledDeletion` | body | string | 아니오 | 삭제 예정 일시. 생략 = 변경 없음. `null` = 예약 제거. 지정 시 업로드로부터 최소 30일. |
| `thumbnailTimestampPct` | body | number | 아니오 | 썸네일 시각을 길이 대비 비율로. 미설정이면 0초. |
| `uid` | body | string | 아니오 | 업데이트 대상 비디오 확인용 unique identifier. |
| `uploadExpiry` | body | string | 아니오 | direct user upload URL 만료 일시. |

### 응답

`result`: optional **Video**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER \
    -X POST \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "allowedOrigins": ["example.com"],
          "creator": "creator-id_abcde12345",
          "meta": { "name": "video12345.mp4" },
          "requireSignedURLs": true
        }'
```

---

## 비디오 삭제 {#delete}

**Delete video**

`DELETE /accounts/{account_id}/stream/{identifier}`

비디오와 그 복사본을 Cloudflare Stream에서 삭제한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

API index에 Returns 블록 없음. example 응답 본문도 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## URL에서 가져오기 {#copy}

**Upload videos from a URL**

`POST /accounts/{account_id}/stream/copy`

제공한 URL에서 비디오를 Stream으로 업로드한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `Upload-Creator` | header | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `allowedOrigins` | body | array of AllowedOrigins | 아니오 | 표시 허용 origin. 빈 배열이면 모든 origin. |
| `creator` | body | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `input` | body | string | 아니오 | 비디오 URL. 서버는 공개 라우팅 가능해야 하고 `HTTP HEAD`와 `HTTP GET` range를 지원해야 한다. HEAD는 파일 크기를 담은 `content-range`로 응답해야 한다. **`url`보다 이 필드를 선호.** |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `name` | body | string | 아니오 | 비디오 이름. 레거시 호환. |
| `requireSignedURLs` | body | boolean | 아니오 | `true`이면 UID 접근 불가. signed token 필요. |
| `scheduledDeletion` | body | string | 아니오 | 삭제 예정 일시. 생략 = 변경 없음. `null` = 예약 제거. 최소 30일. |
| `thumbnailTimestampPct` | body | number | 아니오 | 썸네일 시각 비율. 미설정이면 0초. |
| `url` | body | string | 아니오 | 비디오 URL. `input`과 동일 제약. **deprecated. `input`을 쓸 것.** |
| `watermark` | body | object | 아니오 | `{ uid?: string }` 워터마크 프로필 unique identifier. |

공식 스키마에서 `input`과 `url` 모두 optional이다. 어느 쪽이 실질 필수인지는 문서가 강제하지 않는다. example은 둘 다 넣는다.

### 응답

`result`: optional **Video**. extra/missing 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/copy \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "input": "https://example.com/myvideo.mp4",
          "meta": { "name": "video12345.mp4" }
        }'
```

---

## Direct upload URL {#direct-upload}

**Upload videos via direct upload URLs**

`POST /accounts/{account_id}/stream/direct_upload`

API key 없이 비디오를 올릴 수 있는 direct upload를 만든다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `Upload-Creator` | header | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `maxDurationSeconds` | body | number | 예 | 아직 업로드되지 않은 비디오의 최대 길이(초). 초과 시 처리 중 실패. `-1`이면 모름. |
| `allowedOrigins` | body | array of AllowedOrigins | 아니오 | 표시 허용 origin. 빈 배열이면 모든 origin. |
| `creator` | body | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `expiry` | body | string | 아니오 | 이 시각 이후에는 업로드를 받지 않음. |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `requireSignedURLs` | body | boolean | 아니오 | `true`이면 UID 접근 불가. signed token 필요. |
| `scheduledDeletion` | body | string | 아니오 | 삭제 예정 일시. 생략 = 변경 없음. `null` = 예약 제거. 최소 30일. |
| `thumbnailTimestampPct` | body | number | 아니오 | 썸네일 시각 비율. 미설정이면 0초. |
| `watermark` | body | object | 아니오 | `{ uid?: string }` 워터마크 프로필 unique identifier. |

### 응답

`result`: optional object (**Video가 아님**).

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `scheduledDeletion` | string | 아니오 | 삭제 예정 일시. 생략 = 변경 없음. `null` = 예약 제거. 최소 30일. |
| `uid` | string | 아니오 | Cloudflare가 생성한 미디어 unique identifier. |
| `uploadURL` | string | 아니오 | 비인증 업로드가 쓰는 단일 `HTTP POST multipart/form-data` URL. |
| `watermark` | Watermark | 아니오 | [Watermark](11-api-reference.md#watermark). |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/direct_upload \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "maxDurationSeconds": 3600,
          "expiry": "2021-01-02T02:20:00Z"
        }'
```

---

## 클립 {#clip}

**Clip videos given a start and end time**

`POST /accounts/{account_id}/stream/clip`

초 단위 start/end로 비디오를 클립한다. Returns는 **Video**. Domain Type `Clip`은 요청 쪽 필드 집합(아래 참고)이며, 응답 인라인 스키마는 Video와 같다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `clippedFromVideoUID` | body | string | 예 | 원본 비디오 UID. |
| `endTimeSeconds` | body | number | 예 | 클립 종료 시각(초). |
| `startTimeSeconds` | body | number | 예 | 클립 시작 시각(초). |
| `allowedOrigins` | body | array of AllowedOrigins | 아니오 | 표시 허용 origin. 빈 배열이면 모든 origin. |
| `creator` | body | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `input` | body | string | 아니오 | 비디오 URL. `url`보다 선호. |
| `meta` | body | unknown | 아니오 | 사용자 수정 가능 key-value store. |
| `name` | body | string | 아니오 | 비디오 이름. |
| `requireSignedURLs` | body | boolean | 아니오 | `true`이면 UID 접근 불가. signed token 필요. |
| `scheduledDeletion` | body | string | 아니오 | 삭제 예정 일시. 생략 = 변경 없음. `null` = 예약 제거. 최소 30일. |
| `thumbnailTimestampPct` | body | number | 아니오 | 썸네일 시각 비율. 미설정이면 0초. |
| `url` | body | string | 아니오 | 비디오 URL (legacy. `input`을 쓸 것). |
| `watermark` | body | object | 아니오 | `{ uid?: string }` 워터마크 프로필 unique identifier. |

### 응답

`result`: optional **Video**. 클립이면 `clippedFrom`이 원본 uid를 가리킨다.

Domain Type `Clip`에만 있고 Video 인라인 Returns에는 없는 이름: `clippedFromVideoUID`, `startTimeSeconds`, `endTimeSeconds`. 응답은 Video를 따른다.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/clip \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "clippedFromVideoUID": "023e105f4ecef8ad9ca31a8372d0c353",
          "endTimeSeconds": 0,
          "startTimeSeconds": 0
        }'
```

(공식 example의 필수 세 필드. 같은 example에는 optional `allowedOrigins`/`creator`/`input`/`meta`/`name`/`requireSignedURLs`/`scheduledDeletion`/`thumbnailTimestampPct`/`url`도 있다.)

---

## Embed HTML {#embed}

**Retrieve embed Code HTML**

`GET /accounts/{account_id}/stream/{identifier}/embed`

웹 페이지에 넣을 HTML snippet을 가져온다. 성공 시 비디오를 표시하는 HTML fragment. 실패 시 JSON 본문.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`EmbedGetResponse = string` (HTML). JSON `result` 객체 스키마 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/embed \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## Signed token {#token}

**Create signed URL tokens for videos**

`POST /accounts/{account_id}/stream/{identifier}/token`

비디오용 signed URL token을 만든다. body가 없으면 기본값으로 token을 만든다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `id` | body | string | 아니오 | Stream signing key의 optional ID. 있으면 `pem`도 필수. |
| `accessRules` | body | array of object | 아니오 | token 접근 제약. IP, IP range, 국가로 allow/block. 앞에서 뒤로 평가. 매칭되면 해당 action을 적용하고 이후 규칙은 보지 않음. |
| `accessRules[].action` | body | enum | 아니오 | `"allow"` \| `"block"`. `block`이면 규칙에 맞는 시청자의 조회를 막음. |
| `accessRules[].country` | body | array of string | 아니오 | ISO 3166-1 Alpha-2 2글자 국가 코드. |
| `accessRules[].ip` | body | array of string | 아니오 | IPv4/IPv6 주소 또는 CIDR. |
| `accessRules[].type` | body | enum | 아니오 | `"any"` \| `"ip.src"` \| `"ip.geoip.country"`. `any`는 모든 요청에 매칭(기본 action용 wildcard). |
| `downloadable` | body | boolean | 아니오 | signed token으로 MP4 다운로드 링크에 접근할 수 있게 함. |
| `exp` | body | number | 아니오 | token을 더 이상 받지 않는 unix epoch. 발급 시각부터 최대 24시간. 미설정이면 발급 후 1시간. |
| `flags` | body | object | 아니오 | `{ original?: boolean }` — 변환 없이 원본 비디오를 반환할지. |
| `nbf` | body | number | 아니오 | 이 시각 이전에는 token을 받지 않음 (unix epoch). 미설정이면 발급 1시간 전. |
| `pem` | body | string | 아니오 | Stream signing key에 연결된 PEM private key (base64). 있으면 `id`도 필수. |

### 응답

`result`: optional object (**Video가 아님**).

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `token` | string | 아니오 | signed URLs 기능에 쓰는 signed token. |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/token \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{}'
```

body 생략도 가능(공식: body가 없으면 기본값으로 token 생성). 공식 example은 `id`+`pem`+`accessRules`를 넣는다. example `pem`은 시크릿처럼 재사용하지 말 것.

---

## 스토리지 사용량 {#storage-usage}

**Storage use**

`GET /accounts/{account_id}/stream/storage-usage`

계정의 스토리지 사용 정보를 반환한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `creator` | query | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |

### 응답

`result`: optional object (**Video가 아님**).

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `creator` | string | 아니오 | 미디어 creator의 사용자 정의 identifier. |
| `totalStorageMinutes` | number | 아니오 | 계정에 저장된 비디오 콘텐츠 총 분. 소수 가능. |
| `totalStorageMinutesLimit` | number | 아니오 | 계정에 할당된 스토리지 용량. |
| `videoCount` | number | 아니오 | 계정과 연관된 비디오 총 수. |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/storage-usage \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```
