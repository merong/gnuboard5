# 13 — FAQ & Pricing / 출처 사실만

가격·한도는 공식 페이지 그대로. 추측 금지. 원문: [FAQ](https://developers.cloudflare.com/stream/faq/), [Pricing](https://developers.cloudflare.com/stream/pricing/). 소스 last updated: Apr 21, 2026.

## Stream 과금 두 축

온디맨드와 라이브 **동일**. 인제스트·인코딩 **무료**. 대역폭은 delivered에 포함, 별도 egress 없음.

| 차원 | 방식 | 문서 단가 |
|------|------|-----------|
| Minutes stored | 선불. 파일 크기 무관, 초 단위 올림 | $5 / 1,000분 |
| Minutes delivered | 후불 | $1 / 1,000분 |

### 스토리지에 포함

- 업로드된 원본 비디오
- 라이브 녹화
- 미완료 Direct Creator / tus의 예약 maxDurationSeconds (완료·만료·에러 시 해제, 실제 길이만 남음)

### 스토리지에 미포함

- unplayable / error
- 만료된 DCU 링크
- 삭제된 비디오
- MP4 다운로드 파생 파일
- Stream이 만든 다중 화질

R2 등 다른 스토리지 요금은 Stream 저장에 추가되지 않음. 스토리지 소진 시 신규 업로드/라이브 불가. Enterprise는 계약 쿼타 초과 업로드 가능.

### 전송에 포함

- Stream Player, HLS, DASH 재생
- MP4 다운로드
- SRT/RTMP 시뮬캐스트 출력

세그먼트 HTTP 요청 기준. 클라이언트 프리로드/버퍼 = 과금. 브라우저 캐시 재생은 과금 아님. 일부 모바일 HLS 라이브러리는 세그먼트 캐시 기본 off.

웹 재생 반올림: 업로드 콘텐츠 세그먼트 4초. 라이브/녹화는 키프레임/GOP.

예: 두 명이 30분씩 보면 60분 delivered = $0.06. 시청자 없는 라이브 = 전송 $0, 녹화는 저장. 녹화 삭제 시 저장 해제.

대량: Sales / Enterprise contact.

DCU maxDurationSeconds=600, 1시간 만료: 즉시 10분 예약. 미사용 만료 시 반환. 5분 업로드 성공 시 예약 해제 후 5분 집계. 인코딩 실패 시 예약 해제·저장 0.

## Media Transformations 과금

Image Transformations와 동일 구독.

- 스틸 1장 = 1 transformation
- 비디오/오디오 = 출력 초당 1
- URL: unique (입력+플래그) 달 1회
- $0.50 / 1,000 unique, 월 5,000 무료
- Workers binding: 베타 중 무료. 이후 호출마다 과금 (unique 아님)

문서: 과금 2025-11-01 시작.

## FAQ 한도

- 원본 파일 그대로 다운로드 불가. Downloadable Videos로 인코딩 MP4.
- 업로드 기본 30 GB, 동시 인코딩 120. ready여도 pctComplete<100이면 일부 화질 진행 중. error/ready/pendingupload는 큐 한도 제외. 상향은 support.
- 시청자 수는 이 한도와 무관.
- 구매 스토리지(총 duration) 초과 시 업로드 불가. DCU는 URL 생성 시점에 한도 적용. 초과 시 429 또는 413.
- Cloudflare에 없는 도메인에도 임베드 가능.
- HDR → SDR 재인코딩.
- 구독 미갱신 30일 후 비디오 삭제.
- 권장 신규 인코딩: MP4, AAC, H.264, 30fps 이하, Fast Start, progressive, high profile, closed GOP, 녹화 FPS 유지, 모노/스테레오.

| 해상도 | 권장 bitrate |
|--------|----------------|
| 1080p | 8 Mbps |
| 720p | 4.8 Mbps |
| 480p | 2.4 Mbps |
| 360p | 1 Mbps |

CSP는 04-playback.md. PageSpeed가 플레이어 JS를 인스턴스마다 벌점 → 실제로는 한 번 받고 캐시. 썸네일 링크 또는 iframe lazy-load.

## 라이브 과금 재확인

라이브 페이지도 $5/1000 stored, $1/1000 delivered. 자동 녹화, 인코딩 추가 비용 없음.
