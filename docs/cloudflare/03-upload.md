# 03 — Upload / 업로드

## 언제 어떤 방법을 쓰는가

| 방법 | 언제 | 엔드포인트 | 크기 |
|------|------|------------|------|
| Dashboard Quick upload | 코드 없이 | 대시보드 | 문서: basic은 200MB 미만 안내. 전체 한도는 30GB |
| Basic POST (multipart) | 서버가 파일 보유, **< 200 MB**, 연결 안정 | `POST /accounts/{account_id}/stream` `multipart/form-data` field `file` | < 200 MB |
| Upload via link (copy) | R2/S3/GCS 등 HTTP URL | `POST /accounts/{account_id}/stream/copy` | 30 GB 한도. Stream이 fetch |
| tus (resumable) | **> 200 MB** 또는 불안정 연결 | `POST /accounts/{account_id}/stream` + tus headers | 30 GB |
| Direct creator upload (POST) | 엔드유저, **< 200 MB**, 토큰 비노출 | `POST .../stream/direct_upload` → `upload.videodelivery.net/{uid}` | < 200 MB |
| Direct creator upload (tus) | 엔드유저, **> 200 MB** 또는 불안정 | `POST .../stream?direct_user=true` + tus. `Location`이 업로드 URL | 30 GB |

Google Drive 공유 링크는 rate limit/접근 제한 때문에 **비권장**.

## 지원 포맷

MP4, MKV, MOV, AVI, FLV, MPEG-2 TS, MPEG-2 PS, MXF, LXF, GXF, 3GP, WebM, MPG, Quicktime.

권장: MP4, AAC, H.264, ≤60 fps (FAQ 신규 제작: ≤30), closed GOP는 라이브에 필수, 모노/스테레오.

## Basic POST

```bash
curl --request POST \
  --header "Authorization: Bearer <API_TOKEN>" \
  --form file=@/path/my-video.mp4 \
  https://api.cloudflare.com/client/v4/accounts/{account_id}/stream
```

## Upload via link

```bash
curl --data '{"url":"https://example.r2.dev/video.mp4","meta":{"name":"My First Stream Video"}}' \
  --header "Authorization: Bearer <API_TOKEN>" \
  https://api.cloudflare.com/client/v4/accounts/{account_id}/stream/copy
```

`readyToStream`가 false이면 `status.state`가 `downloading`일 수 있다. 웹훅 또는 GET `{uid}` 폴링.

Copy body에 쓸 수 있는 필드(API): `url` (필수), `meta`, `creator`, `thumbnailTimestampPct`, `allowedOrigins`, `requireSignedURLs`, `watermark.uid` 등.

## tus (서버 측, API 토큰)

요구사항:

- 청크 최소 **5,242,880** bytes (파일 전체가 더 작으면 예외)
- 권장 안정 연결: **52,428,800**
- 최대 **209,715,200**
- **256 KiB 배수** (마지막 청크 예외)

```sh
tus-upload --chunk-size 52428800 --header Authorization "Bearer <API_TOKEN>" \
  <PATH_TO_VIDEO> https://api.cloudflare.com/client/v4/accounts/<ACCOUNT_ID>/stream
```

비디오 ID는 `Location` URL을 파싱하지 말고 응답 헤더 **`stream-media-id`** 를 쓴다.

### Upload-Metadata (tus)

key-value, 값은 클라이언트가 인코딩. Stream API `meta`에 임의 키도 들어간다.

| 키 | 효과 |
|----|------|
| `name` | `meta.name`, 대시보드 이름 |
| `requiresignedurls` | 업로드 후 signed URL 필수 |
| `scheduleddeletion` | 삭제 시각. created 기준 **30–1096일** |
| `allowedorigins` | 임베드/매니페스트 허용 origin |
| `thumbnailtimestamppct` | 0.0–1.0 |
| `watermark` | watermark profile UID |

Creator: 헤더 `Upload-Creator`.

## Direct creator uploads

API 토큰을 클라이언트에 주지 않는다. **`maxDurationSeconds`를 반드시 지정** — 예약 스토리지로 차감되고, 실제 업로드/만료/에러 후 정산.

### Basic POST DCU (< 200 MB)

```bash
curl https://api.cloudflare.com/client/v4/accounts/{account_id}/stream/direct_upload \
  --header 'Authorization: Bearer <API_TOKEN>' \
  --data '{"maxDurationSeconds": 3600}'
```

응답: `{ "uploadURL": "https://upload.videodelivery.net/{uid}", "uid": "..." }`.

클라이언트:

```bash
curl --request POST --form file=@video.mp4 https://upload.videodelivery.net/{uid}
```

성공 200. 제약 위반 또는 >200MB → 4xx.

Workers binding `createDirectUpload` 은 **basic POST URL만** 만든다. tus(>200MB)는 REST.

### tus DCU

백엔드가 파일 크기(`Upload-Length`)를 받아:

`POST https://api.cloudflare.com/client/v4/accounts/{ACCOUNT_ID}/stream?direct_user=true`

헤더: `Authorization`, `Tus-Resumable: 1.0.0`, `Upload-Length`, `Upload-Metadata`.

업로드 URL은 **본문이 아니라 `Location` 헤더**. 클라이언트(Uppy tus 등)의 endpoint를 이 백엔드로 둔다. 예: `chunkSize: 150 * 1024 * 1024`.

`Upload-Metadata` 문법: `key <base64value>` 쌍을 콤마로 연결, 등호 없음.

예: `'Upload-Metadata: maxDurationSeconds NjAw,requiresignedurls,expiry MjAyNC0wMi0yN1QwNzoyMDo1MFo='`

- `NjAw` = 600 (10분)
- `expiry` = RFC3339

제약은 **첫 요청(프로비저닝)** 의 metadata만 적용.

진행 추적: 보관해 둔 `uid`로 GET video 또는 VOD 웹훅.

## 업로드 후

`GET /accounts/{account_id}/stream/{identifier}` 로 `readyToStream` 확인. 재생 URL은 `playback.hls` / `playback.dash`.
