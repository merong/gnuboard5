# 07 — Media Transformations / origin 비디오 변환

**Stream 라이브러리에 저장하지 않는다.** origin(또는 허용된 원격 URL)의 파일을 zone의 `/cdn-cgi/media/` 로 변환·캐시한다. Image Transformations와 구독/메트릭을 공유.

Stream-hosted uid 재생이 필요하면 [03-upload.md](03-upload.md) + [04-playback.md](04-playback.md).

## 활성화

대시보드 Stream → [Transformations](https://dash.cloudflare.com/?to=/:account/stream/video-transformations) → zone **Enable**. 이미 Image Transformations가 켜진 zone도 비디오 변환 가능.

과금: 문서상 **2025-11-01** 부터 (페이지마다 will begin / began).

## URL 형식

```
https://example.com/cdn-cgi/media/<OPTIONS>/<SOURCE-VIDEO>
```

- `example.com`: Transformations가 켜진 zone
- `<OPTIONS>`: 콤마 구분
- `<SOURCE-VIDEO>`: `https://` 또는 `http://` 로 시작하는 원본 전체 URL

예: HD R2 소스를 자르고 정사각 크롭, 오디오 제거:

```
https://example.com/cdn-cgi/media/mode=video,time=5s,duration=5s,width=500,height=500,fit=crop,audio=false/https://pub-....r2.dev/aus-mobile-demo.mp4
```

결과는 HTML `<video>`에 바로 넣는 MP4.

## Options

| 옵션 | 값 | 노트 |
|------|-----|------|
| `mode` | `video` `frame` `spritesheet` `audio` | video=H.264/AAC MP4; frame=스틸; spritesheet=JPEG 여러 프레임; audio=AAC M4A |
| `time` | `5s` `2m` | 시작(또는 frame 추출 시각). **0–10m**, 기본 0 |
| `duration` | | video/audio 출력 길이, spritesheet 프레임 범위. **1s–60s**, 기본 min(입력, 60s) |
| `fit` | `contain` `scale-down` `cover` | 비율 유지, 늘어나지 않음 |
| `width` `height` | 10–2000 px | |
| `audio` | true/false | `mode=video`만. 기본 true. `mode=audio`면 false 불가 |
| `format` | frame: `jpg` `png`; audio: `m4a` | |
| `filename` | Content-Disposition | 최대 120자, `^[a-zA-Z0-9-_]+.?[a-zA-Z0-9-_]+$` |

## 소스 요구 / 제한

- 입력 **< 100 MB**, 길이 **≤ 10분**
- 권장: MP4 H.264 + AAC 또는 MP3. animated GIF도 언급. 기타는 미검증
- Origin: HTTP HEAD + range, `Content-Range` 헤더
- **BYOIP 비호환**
- Worker가 같은 zone을 `fetch`하면 `/cdn-cgi/media` 404 가능 → **bindings** 권장, 또는 `global_fetch_strictly_public`

## Source origins

기본: **변환을 서빙하는 zone만** 소스 허용. Workers `fetch`에도 적용.

대시보드 Sources:

- **allowed origins**: 목록만. 루트 `b.com`은 `media.b.com` 미포함. 서브는 따로 또는 `*.b.com` (루트+서브). Path는 **접두사**. 리다이렉트는 따라가며 결과 변환
- **any origin**: 공개 URL 전부. 덜 안전. 전환 시 목록 초기화

## Workers binding

`env` 에 Media Transformations binding. 상세: [bindings](https://developers.cloudflare.com/stream/transform-videos/bindings/). 베타 동안 binding 변환은 **과금 안 함**. 이후 URL 변환과 동일 단가이나 **unique가 아니라 호출마다** 과금. 캐시 권장.

## 과금 (출처)

- 스틸 1장 = 1 transformation
- 비디오/오디오 = 출력 **초당** 1 transformation
- URL 변환: (입력 + 플래그) 조합당 **달 1회**만
- Media+Image: **$0.50 / 1,000** unique ops, 월 **5,000** 무료

에러 코드: [troubleshooting](https://developers.cloudflare.com/stream/transform-videos/troubleshooting/).
