# 11 — Edit API (captions, audio, watermarks, downloads)

공통 envelope·Caption·Audio·Watermark·Download 항목은 [11-api-reference.md](11-api-reference.md)에 있다.

Auth: `Authorization: Bearer $CLOUDFLARE_API_TOKEN`

---

## 캡션 목록 {#list-captions}

**List captions or subtitles**

`GET /accounts/{account_id}/stream/{identifier}/captions`

특정 비디오의 사용 가능한 캡션/자막을 나열한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional array of **Caption**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 언어별 캡션 상세 {#get-caption}

**List captions or subtitles for a provided language**

`GET /accounts/{account_id}/stream/{identifier}/captions/{language}`

지정한 언어의 캡션/자막을 나열한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `language` | path | string | 예 | BCP 47 language tag. |

### 응답

`result`: optional **Caption**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions/$LANGUAGE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## AI 캡션 생성 {#generate-captions}

**Generate captions or subtitles for a provided language via AI**

`POST /accounts/{account_id}/stream/{identifier}/captions/{language}/generate`

지정한 언어의 캡션/자막을 AI로 생성한다. Body 파라미터 없음.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `language` | path | string | 예 | BCP 47 language tag. |

### 응답

`result`: optional **Caption**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions/$LANGUAGE/generate \
    -X POST \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 캡션 업로드 {#upload-captions}

**Upload captions or subtitles**

`PUT /accounts/{account_id}/stream/{identifier}/captions/{language}`

특정 BCP47 언어 엔드포인트에 캡션/자막 파일을 업로드한다. 언어당 파일 하나.

API index에 Body Parameters 섹션은 없다. 공식 example은 `Content-Type: multipart/form-data`와 `-F file=@...`를 쓴다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `language` | path | string | 예 | BCP 47 language tag. |
| `file` | body (form) | file | 예 (example) | 공식 example의 form field. 스키마 Body Parameters 목록에는 없음. |

### 응답

`result`: optional **Caption**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions/$LANGUAGE \
    -X PUT \
    -H 'Content-Type: multipart/form-data' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -F file=@/Users/kyle/Desktop/tr.vtt
```

---

## 캡션 삭제 {#delete-captions}

**Delete captions or subtitles**

`DELETE /accounts/{account_id}/stream/{identifier}/captions/{language}`

비디오에서 캡션/자막을 제거한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `language` | path | string | 예 | BCP 47 language tag. |

### 응답

`result`: optional string. 공식 example 값은 `""`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions/$LANGUAGE \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## WebVTT {#get-vtt}

**Return WebVTT captions for a provided language**

`GET /accounts/{account_id}/stream/{identifier}/captions/{language}/vtt`

지정한 언어의 WebVTT 캡션을 반환한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `language` | path | string | 예 | BCP 47 language tag. |

### 응답

`VttGetResponse = string` (WebVTT 텍스트). JSON `result` 객체 스키마 없음.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/captions/$LANGUAGE/vtt \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 오디오 트랙 목록 {#list-audio}

**List additional audio tracks on a video**

`GET /accounts/{account_id}/stream/{identifier}/audio`

비디오의 추가 오디오 트랙을 나열한다. **원본 업로드에 붙은 오디오는 반환하지 않는다.**

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional object.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `audio` | array of Audio | 아니오 | 비디오의 오디오 트랙 배열. |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/audio \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 파일로 오디오 트랙 추가 {#add-audio-file}

**Upload via HTTP** — `출처: 제품 문서` (API index의 POST는 `/audio/copy`만)

`POST /accounts/{account_id}/stream/{identifier}/audio`

오디오 파일을 input name `file`로 포함한다. 오디오 파일 업로드는 **200 MB를 넘을 수 없다**. 더 크면 업로드 전 압축. form input `label`은 필수이며 해당 비디오의 다른 오디오 트랙 label 중 고유해야 한다.

비디오를 먼저 업로드한 뒤에만 추가 트랙을 붙일 수 있다. 길이가 안 맞으면 최선 노력: 오디오가 더 길면 비디오 길이에 맞게 자르고, 더 짧으면 끝에 무음을 붙인다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | 제품 경로. |
| `identifier` | path | string | 예 | 제품의 `VIDEO_UID`. |
| `file` | body (form) | file | 예 | 오디오 파일. input name = `file`. 최대 200 MB. |
| `label` | body (form) | string | 예 | 해당 비디오의 다른 오디오 트랙 label 중 고유. |

### 응답

제품 example `result`:

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `uid` | string | (example) | 오디오 트랙 unique identifier. 수정/삭제에 사용. |
| `label` | string | (example) | 트랙 label. |
| `default` | boolean | (example) | 추가 트랙의 기본값은 `false`. |
| `status` | string | (example) | 업로드·인코딩 성공 후 `ready`. 실패 시 `error`. 직후 example은 `queued`. |

필드 표는 제품 example + 설명에서만. 전체 타입은 [Audio](11-api-reference.md#audio).

### 예

```bash
curl -X POST \
  -H 'Authorization: Bearer $CLOUDFLARE_API_TOKEN' \
  -F file=@/Desktop/audio_file.mp3 \
  -F label='Example Audio Label' \
  https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$VIDEO_UID/audio
```

---

## URL로 오디오 트랙 추가 {#copy-audio}

**Add audio tracks to a video**

`POST /accounts/{account_id}/stream/{identifier}/audio/copy`

제공한 오디오 트랙 URL로 추가 오디오 트랙을 붙인다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `label` | body | string | 예 | 해당 비디오의 다른 오디오 트랙 label 중 고유한 문자열. |
| `url` | body | string | 아니오 | 오디오 트랙 URL. 서버는 공개 라우팅 가능해야 하고 `HTTP HEAD`와 `HTTP GET` range를 지원해야 한다. HEAD는 파일 크기를 담은 `content-range`로 응답해야 한다. |

### 응답

`result`: optional **Audio**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/audio/copy \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "label": "director commentary",
          "url": "https://www.examplestorage.com/audio_file.mp3"
        }'
```

---

## 오디오 트랙 수정 {#edit-audio}

**Edit additional audio tracks on a video**

`PATCH /accounts/{account_id}/stream/{identifier}/audio/{audio_identifier}`

추가 오디오 트랙을 수정한다. 한 트랙의 `default`를 `true`로 바꾸면 그 비디오의 다른 트랙 `default`는 모두 `false`가 된다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `audio_identifier` | path | string | 예 | 추가 오디오 트랙 unique identifier. |
| `default` | body | boolean | 아니오 | 플레이어에서 기본 재생되는 트랙인지. |
| `label` | body | string | 아니오 | 해당 비디오의 다른 오디오 트랙 label 중 고유한 문자열. |

### 응답

`result`: optional **Audio**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/audio/$AUDIO_IDENTIFIER \
    -X PATCH \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "label": "director commentary"
        }'
```

---

## 오디오 트랙 삭제 {#delete-audio}

**Delete additional audio tracks on a video**

`DELETE /accounts/{account_id}/stream/{identifier}/audio/{audio_identifier}`

추가 오디오 트랙을 삭제한다. **default 트랙은 삭제할 수 없다.** 삭제 전에 다른 트랙을 default로 지정해야 한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |
| `audio_identifier` | path | string | 예 | 추가 오디오 트랙 unique identifier. |

### 응답

`result`: optional string. 공식 example 값은 `"ok"`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/audio/$AUDIO_IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 워터마크 목록 {#list-watermarks}

**List watermark profiles**

`GET /accounts/{account_id}/stream/watermarks`

계정의 모든 워터마크 프로필을 나열한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |

### 응답

`result`: optional array of **Watermark**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/watermarks \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 워터마크 상세 {#get-watermark}

**Watermark profile details**

`GET /accounts/{account_id}/stream/watermarks/{identifier}`

단일 워터마크 프로필 상세를 가져온다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | 워터마크 프로필 unique identifier. |

### 응답

`result`: optional **Watermark**.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/watermarks/$IDENTIFIER \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 워터마크 생성 {#create-watermark}

**Create watermark profiles via basic upload**

`POST /accounts/{account_id}/stream/watermarks`

단일 `HTTP POST multipart/form-data` 요청으로 워터마크 프로필을 만든다.

API index Body Parameters에는 `file`이 없다. 제품 문서: 로컬 이미지는 `multipart/form-data`로 `file` 키에 넣고, 나머지 필드는 모두 optional.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `name` | body | string | 아니오 | 워터마크 프로필 짧은 설명. |
| `opacity` | body | number | 아니오 | 투명도. `0.0` 완전 투명, `1.0` 완전 불투명. |
| `padding` | body | number | 아니오 | 영상 가장자리와 이미지 사이 여백. `0.0`–`1.0`. |
| `position` | body | string | 아니오 | `upperRight` \| `upperLeft` \| `lowerLeft` \| `lowerRight` \| `center`. `center`는 `padding` 무시. |
| `scale` | body | number | 아니오 | 영상 대비 이미지 크기. `0.0` 원본, `1.0` 전체. |
| `url` | body | string | 아니오 | 복사할 워터마크 이미지 URL. |
| `file` | body (form) | file | 아니오 | `출처: 제품 문서`. 로컬 이미지. input name = `file`. |

### 응답

`result`: optional **Watermark**.

### 예

API index example (JSON body):

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/watermarks \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "name": "Marketing Videos",
          "opacity": 0.75,
          "padding": 0.1,
          "position": "center",
          "scale": 0.1
        }'
```

제품 문서 로컬 파일 예 (`출처: 제품 문서`):

```bash
curl -X POST -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
  -F file=@{path-to-image-locally} \
  -F name='marketing videos' \
  -F opacity=1.0 \
  -F padding=0.05 \
  -F scale=0.15 \
  -F position=upperRight \
  https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/watermarks
```

---

## 워터마크 삭제 {#delete-watermark}

**Delete watermark profiles**

`DELETE /accounts/{account_id}/stream/watermarks/{identifier}`

워터마크 프로필을 삭제한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `identifier` | path | string | 예 | 워터마크 프로필 unique identifier. |

### 응답

`result`: optional string. 공식 example 값은 `"ok"`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/watermarks/$IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 다운로드 목록 {#list-downloads}

**List downloads**

`GET /accounts/{account_id}/stream/{identifier}/downloads`

비디오에 생성된 다운로드를 나열한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional object. 키는 다운로드 타입. 해당 타입이 생성된 경우에만 존재.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `audio` | object | 아니오 | 오디오 전용 다운로드. [Download 항목](11-api-reference.md#download-항목). |
| `default` | object | 아니오 | 기본 비디오 다운로드. [Download 항목](11-api-reference.md#download-항목). |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/downloads \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## 다운로드 생성 {#create-downloads}

**Create downloads**

`POST /accounts/{account_id}/stream/{identifier}/downloads`

비디오가 재생 가능할 때 다운로드를 만든다. 타입별로는 `/downloads/{download_type}`을 쓴다. 사용 가능 타입: `default`, `audio`.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional object. list와 동일 (`audio?`, `default?` — 각 Download 항목).

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/downloads \
    -X POST \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## M4A 다운로드 생성 {#create-download-audio}

**Generate downloadable M4A files** — `출처: 제품 문서`

`POST /accounts/{account_id}/stream/{identifier}/downloads/audio`

MP4와 같은 절차로, 경로만 `/downloads/audio`. API index create 설명도 타입별 경로 `/downloads/{download_type}` (`default` \| `audio`)을 가리킨다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | 제품 경로. |
| `identifier` | path | string | 예 | 제품의 `<VIDEO_UID>`. |

제품 페이지는 Body를 나열하지 않는다.

### 응답

제품 example `result`:

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `audio` | object | (example) | M4A 다운로드. |
| `audio.status` | string | (example) | example 값 `"inprogress"`. |
| `audio.url` | string | (example) | `https://customer-<CODE>.cloudflarestream.com/<VIDEO_UID>/downloads/audio.m4a` |
| `audio.percentComplete` | number | (example) | example 값 `75.0`. |

API index Download 항목 타입과 맞춘다: `status` enum `"ready"` \| `"inprogress"` \| `"error"`, `percentComplete` number, `url` optional string.

### 예

```bash
curl -X POST \
  -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
  https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$VIDEO_UID/downloads/audio
```

---

## 다운로드 삭제 {#delete-downloads}

**Delete downloads**

`DELETE /accounts/{account_id}/stream/{identifier}/downloads`

비디오의 다운로드를 삭제한다. 타입별로는 `/downloads/{download_type}`. 사용 가능 타입: `default`, `audio`.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Cloudflare가 생성한 미디어 unique identifier. |

### 응답

`result`: optional string.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$IDENTIFIER/downloads \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## MP4 다운로드 삭제 {#delete-download-default}

**Delete downloads (type-specific)** — `출처: 제품 문서`

`DELETE /accounts/{account_id}/stream/{identifier}/downloads/default`

비디오의 다운로드를 삭제한다. 사용 가능 타입은 `default`와 `audio`. 생략 시 기본은 `default`.

API index delete 설명도 `/downloads/{download_type}` (`default` \| `audio`)을 가리킨다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | 제품 경로. |
| `identifier` | path | string | 예 | 제품의 `<VIDEO_UID>`. |
| `download_type` | path | string | 아니오 | 제품: `default` 또는 `audio`. 생략 시 `default`. |

### 응답

제품 페이지에 `result` 필드 표 없음.

### 예

```bash
curl -X DELETE \
  -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
  https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/$VIDEO_UID/downloads/default
```
