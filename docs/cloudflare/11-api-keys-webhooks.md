# 11 — Signing keys / VOD webhooks

공통 envelope은 [11-api-reference.md](11-api-reference.md)에 있다.

Auth: `Authorization: Bearer $CLOUDFLARE_API_TOKEN`

---

## Signing key 목록 {#list-keys}

**List signing keys**

`GET /accounts/{account_id}/stream/keys`

signing key가 만들어진 시각과 video ID(식별자)를 나열한다. `pem`/`jwk`는 목록에 없다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |

### 응답

`result`: optional array of object.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `id` | string | 아니오 | Identifier. |
| `created` | string | 아니오 | signing key 생성 일시. |
| `key_id` | string | 아니오 | signing key unique identifier. |

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/keys \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## Signing key 생성 {#create-keys}

**Create signing keys**

`POST /accounts/{account_id}/stream/keys`

RSA private key를 PEM과 JWK 형식으로 만든다. 키 파일은 **생성 직후 한 번만** 표시된다. 키는 비디오와 독립적으로 만들고 쓰고 삭제한다. 모든 키는 어떤 비디오든 서명할 수 있다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `body` | body | unknown | 아니오 | 공식 스키마 타입 `unknown`. example은 `{}`. |

### 응답

`result`: optional **Keys**.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|
| `id` | string | 아니오 | Identifier. |
| `created` | string | 아니오 | signing key 생성 일시. |
| `jwk` | string | 아니오 | JWK 형식 signing key. |
| `pem` | string | 아니오 | PEM 형식 signing key. |

목록 응답의 `key_id`는 이 생성 응답에 없다. 생성 응답의 `jwk`/`pem`은 목록에 없다.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/keys \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{}'
```

공식 example의 `pem`/`jwk` 값은 시크릿처럼 재사용하지 말 것.

---

## Signing key 폐기 {#delete-keys}

**Delete signing keys**

`DELETE /accounts/{account_id}/stream/keys/{identifier}`

signing key를 삭제하고, 그 키로 만든 signed URL을 모두 폐기한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | Identifier. |
| `identifier` | path | string | 예 | Identifier. |

### 응답

`result`: optional string. 공식 example 값은 `"ok"`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/keys/$IDENTIFIER \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## VOD 웹훅 조회 {#get-webhook}

**View webhooks**

`GET /accounts/{account_id}/stream/webhook`

웹훅 목록을 가져온다. (스키마상 단건 객체)

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |

### 응답

`result`: optional object.

| 이름 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `modified` | string | 아니오 | 웹훅 마지막 수정 일시. |
| `notification_url` | string | 아니오 | 웹훅을 보낼 URL. |
| `notificationUrl` | string | 아니오 | 웹훅을 보낼 URL. |
| `secret` | string | 아니오 | 웹훅 서명을 검증하는 secret. |

`notification_url`과 `notificationUrl`은 공식 스키마에 둘 다 있다. 설명 문구는 동일.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/webhook \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## VOD 웹훅 설정 {#update-webhook}

**Create webhooks**

`PUT /accounts/{account_id}/stream/webhook`

웹훅 알림을 만든다.

제품 문서(using-webhooks): 알림 URL에는 프로토콜이 있어야 하고 `http://` 또는 `https://`만 지원한다. 비디오 처리가 끝나 재생 가능해지거나 error 상태가 되면 이 URL로 알린다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |
| `notification_url` | body | string | 아니오 | 웹훅을 보낼 URL. |
| `notificationUrl` | body | string | 아니오 | 웹훅을 보낼 URL. |

공식 스키마에서 둘 다 optional. 제품 example은 `notificationUrl`만 넣는다.

### 응답

`result`: optional object. get과 동일 (`modified`, `notification_url`, `notificationUrl`, `secret`).

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/webhook \
    -X PUT \
    -H 'Content-Type: application/json' \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN" \
    -d '{
          "notificationUrl": "https://example.com"
        }'
```

(공식 API example은 `notification_url`과 `notificationUrl`을 둘 다 넣는다.)

---

## VOD 웹훅 삭제 {#delete-webhook}

**Delete webhooks**

`DELETE /accounts/{account_id}/stream/webhook`

웹훅을 삭제한다.

### 파라미터

| 이름 | 위치 | 타입 | 필수 | 설명 |
|------|------|------|------|------|
| `account_id` | path | string | 예 | The account identifier tag. |

### 응답

`result`: optional string. 공식 example 값은 `"ok"`.

### 예

```http
curl https://api.cloudflare.com/client/v4/accounts/$ACCOUNT_ID/stream/webhook \
    -X DELETE \
    -H "Authorization: Bearer $CLOUDFLARE_API_TOKEN"
```

---

## VOD 웹훅 수신 (제품 문서)

`출처: 제품 문서` using-webhooks. REST 엔드포인트가 아니라 Stream이 보내는 `POST`.

처리가 끝나면 비디오 정보가 담긴 `POST`가 `notificationUrl`로 간다. 본문 필드는 Video에 가깝다. 제품이 강조하는 필드:

| 이름 | 설명 |
|------|------|
| `uid` | 비디오 unique identifier. |
| `readyToStream` | 최소 한 quality level이 인코딩되어 재생 가능하면 `true`. (제품 불릿은 `readytoStream`로 표기) |
| `status.state` | 모든 quality level 인코딩이 끝나면 `ready`. |
| `status.pctComplete` | 처리 진행률. `100`이면 모든 quality level 사용 가능. |
| `meta` | 업로드 파일 메타데이터. |
| `created` | 비디오 레코드 생성 시각. |

error 시 `state`는 `error`. 제품 페이지는 이유 코드를 `errReasonCode`로 적는다 (Video 스키마 필드명은 `errorReasonCode`). 값: `ERR_NON_VIDEO`, `ERR_DURATION_EXCEED_CONSTRAINT`, `ERR_FETCH_ORIGIN_ERROR`, `ERR_MALFORMED_VIDEO`, `ERR_DURATION_TOO_SHORT`, `ERR_UNKNOWN`.

재생하려면 `state`뿐 아니라 `readyToStream`도 `true`여야 한다.

### 서명

요청 헤더 `Webhook-Signature`. 형식 example: `time=1230811200,sig1=60493ec9388b44585a29543bcf0de62e377d4da393246a8b1c901d0e3e672404`.

`time` = 서버가 보낸 UNIX time. `sig1` = request body 서명. 검증용 secret은 웹훅 생성/조회 응답의 `secret`.

---

## 라이브 웹훅 {#live-webhooks}

**Receive Live Webhooks** — `출처: 제품 문서`

라이브 웹훅은 **Notifications 대시보드**다. REST `PUT /stream/webhook`이 아니다.

제품 문구: Stream Live는 Input이 connect / disconnect / error일 때 웹훅을 보낸다. VOD와 다르다. VOD는 위 `/stream/webhook`.

구독: Cloudflare dashboard → Notifications → Destinations → Webhooks 생성 → All Notifications에서 Stream 제품 선택.

기본은 모든 Live Input. 특정 입력만 받으려면 Input ID를 comma-delimited로 넣는다.

이벤트: `live_input.connected`, `live_input.disconnected`. 제품 본문은 `live_input.errored`도 언급.

payload example 필드: `name`, `text`, `data.notification_name`, `data.input_id`, `data.event_type`, `data.updated_at`, `ts`.

이 경로에 대한 REST method+path+params 표는 제품 페이지에 없다 (대시보드 설정).
