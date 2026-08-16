# 05 — Edit / 클립·캡션·오디오·워터마크

## Clip (VOD trim)

라이브 instant clip과 **다름** → [06-live.md](06-live.md).

`POST /accounts/{account_id}/stream/clip`

필수:

| 필드 | 의미 |
|------|------|
| `clippedFromVideoUID` | 원본 uid |
| `startTimeSeconds` | 시작 |
| `endTimeSeconds` | 끝 |

선택: `meta.name`, `watermark.uid`, `requireSignedURLs`, `thumbnailTimestampPct`.

클립은 `scheduledDeletion`을 **상속하지 않음**. 클립 후 따로 설정.

처리 중 상태 Queued → Ready. 웹훅으로 완료 알림 가능.

## Captions

비디오가 **ready** 여야 생성 가능. 언어는 **BCP 47**. 언어당 트랙 1개 (영어 두 개 불가 — 기존 삭제 후 재생성).

### AI 생성

`POST /accounts/{account_id}/stream/{identifier}/captions/{language}/generate`

지원 언어: `cs` `nl` `en` `fr` `de` `it` `ja` `ko` `pl` `pt` `ru` `es`. **말한 언어**로 생성. `en-GB` 등 지역 태그 → 라벨 "British English".

응답: `language`, `label` (`English (auto-generated)`), `generated: true`, `status`: `inprogress` | `ready` | `error`. ready면 플레이어·매니페스트에 자동 등장. error면 삭제 후 재시도.

### 파일 업로드

`PUT .../captions/{language}`  `-F file=@caption.vtt`

생성 캡션을 수정하면 `generated` → false, 라벨에서 `(auto-generated)` 제거.

### 기타

| 동작 | 메서드 | 경로 |
|------|--------|------|
| 목록 | GET | `.../stream/{id}/captions` |
| 언어 조회 | GET | `.../captions/{language}` |
| 삭제 | DELETE | `.../captions/{language}` |
| WebVTT | GET | `.../captions/{language}/vtt` |

플레이어 초기 트랙: iframe `defaultTextTrack=<BCP47>`.

## Additional audio tracks

비디오 업로드 **이후**. `label` 필수, 비디오 내 유일.

| 방법 | 제한 | 경로 |
|------|------|------|
| URL copy | Stream이 fetch | `POST .../stream/{id}/audio/copy` body `{"url","label"}` |
| HTTP file | **200 MB** 초과 시 압축 | `POST .../stream/{id}/audio` `-F file=` `-F label=` |

응답: `uid`, `label`, `default` (추가 트랙은 false), `status` queued → ready | error.

길이 불일치: 길면 비디오에 맞게 자르고, 짧으면 무음 패딩. 비디오 길이에 맞추는 것을 권장.

| 동작 | 메서드 | 경로 |
|------|--------|------|
| 목록 | GET | `.../audio` |
| 수정 (label/default) | PATCH | `.../audio/{audio_identifier}` |
| 삭제 | DELETE | `.../audio/{audio_identifier}` |

## Watermarks

라이브 비디오에는 **아직 워터마크 불가**.

1) 프로필 생성: `POST /accounts/{account_id}/stream/watermarks` (`-F file=@logo.png`, 선택 `name`)
2) 업로드 시 지정: tus metadata `watermark`, copy body `watermark.uid`, clip body `watermark.uid`

프로필 필드: `opacity` (0–1), `padding` (0–1), `position` (`upperRight` `upperLeft` `lowerLeft` `lowerRight` `center` — center는 padding 무시), `scale` (0–1, 가로/세로 자동).

| 동작 | 메서드 | 경로 |
|------|--------|------|
| 목록 | GET | `.../stream/watermarks` |
| 상세 | GET | `.../watermarks/{identifier}` |
| 삭제 | DELETE | `.../watermarks/{identifier}` |

## Player enhancements (`publicDetails`)

Stream Player에는 자동 반영. 자체 플레이어는 `publicDetails`를 직접 써야 함.

`POST .../stream/{uid}`:

```json
{
  "publicDetails": {
    "title": "Optional video title",
    "share_link": "https://example.com/share",
    "channel_link": "https://example.com/channel",
    "logo": "https://example.com/logo.png"
  }
}
```

JSON: `https://customer-<ID>.cloudflarestream.com/<VIDEO_ID>/metadata/playerEnhancementInfo.json`

대시보드: Videos → 비디오 → **Public Details**.
