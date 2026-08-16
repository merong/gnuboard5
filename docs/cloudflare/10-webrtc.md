# 10 — WebRTC (beta) / 서브초 지연

WHIP로 송출, WHEP로 재생. 동시 시청자 수 제한 없음(문서). **베타**.

언제: 결과가 시간 민감(스포츠, 금융), 양방향(Q&A, 경매), 브라우저/앱에서 쉽게 방송.

## 기존 RTMPS 라이브와 차이

| | RTMPS/SRT Live | WebRTC beta |
|--|----------------|-------------|
| 프로토콜 | RTMPS / SRT → HLS/DASH | WHIP → WHEP |
| 지연 | 초~수십 초 (LL-HLS 베타로 단축) | < 1초 |
| 녹화 | automatic 모드 | **아직 없음** |
| 시뮬캐스트 | outputs | **아직 없음** |
| 시청자 수 / GraphQL | 있음 | **아직 없음** |
| 혼용 | HLS/DASH 재생 | **WHIP+WHEP만 함께**. RTMP→WHEP 또는 WHIP→HLS **아직 없음** |
| signed URL `/token` | 가능 | `/token` 엔드포인트 **미지원**. signing key 사용 |

GA 시 가격은 Stream과 같이 저장 분 + 전송 분 (문서).

## 사용

1. Live input 생성 (대시보드 또는 `POST /stream/live_inputs`)
2. 응답:

```json
{
  "webRTC": { "url": "https://customer-<CODE>.cloudflarestream.com/<SECRET>/webRTC/publish" },
  "webRTCPlayback": { "url": "https://customer-<CODE>.cloudflarestream.com/<INPUT_UID>/webRTC/play" }
}
```

`webRTC.url`은 **방송자만**. 있으면 누구나 이 입력으로 송출 가능.

3. WHIP 클라이언트가 publish URL로 송출 (카메라 권한 후 자동).
4. WHEP 클라이언트가 play URL로 수신.

예제 코드는 공식 페이지의 `WHIPClient` / `WHEPClient`. 네이티브: WkWebView / Android WebView / react-native-webrtc / Google native WebRTC.

## 코덱

- VP9 (최고 화질 권장)
- VP8
- H.264 Constrained Baseline Level 3.1 (`profile-level-id` `42e01f`)

## 호환 클라이언트 (문서 테스트)

WHIP: OBS, `@eyevinn/whip-web-client`, whip-go, gst-plugins-rs, Larix Broadcaster.

WHEP: `@eyevinn/webrtc-player`, `@eyevinn/wrtc-egress`, gst-plugins-rs.

WHIP/WHEP 전체 스펙: Trickle ICE, WHEP server/client offer. `protocol-version` 헤더: `draft-ietf-wish-whip-06`, `draft-murillo-whep-01`.

## 디버그

Chrome `chrome://webrtc-internals`, Firefox `about:webrtc`, Safari inspector WebRTC logging Verbose.
