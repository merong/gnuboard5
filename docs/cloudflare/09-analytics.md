# 09 — Analytics / 분석

권한: **Analytics** (대시보드/GraphQL). GraphQL 토큰은 **Account Analytics**.

용도: 가장 많이 본 비디오, 지역/시간, creator별 추이.

진입점: [Stream Analytics 대시보드](https://dash.cloudflare.com/?to=/:account/stream/analytics) 또는 GraphQL.

## Server-side minutes

HLS/DASH로 나간 **모든** 라이브·VOD (Stream Player 여부 무관). 노드: `streamMinutesViewedAdaptiveGroups` → `minutesViewed`.

### Dimensions / filters

| Field | 의미 |
|-------|------|
| `date` / `datetime` | 날짜/시각. `gt` `lt` `geq` 등 |
| `uid` | video uid |
| `clientCountryName` | ISO 3166 alpha-2 |
| `creator` | Creator ID |

페이지네이션: `uid_gt` seek. 일반 GraphQL 한도/필터: [GraphQL Analytics API](https://developers.cloudflare.com/analytics/graphql-api).

예 — 국가별 분:

```graphql
query StreamGetMinutesExample($accountTag: string!, $start: Date, $end: Date) {
  viewer {
    accounts(filter: { accountTag: $accountTag }) {
      streamMinutesViewedAdaptiveGroups(
        filter: { date_geq: $start, date_lt: $end }
        orderBy: [sum_minutesViewed_DESC]
        limit: 100
      ) {
        sum { minutesViewed }
        dimensions { uid clientCountryName }
      }
    }
  }
}
```

문서에는 `videoPlaybackEventsAdaptiveGroups` / `timeViewedMinutes` 페이지네이션 예도 있다.

GIF 썸네일은 minutes delivered / Analytics 시청 분에 **안 잡힘**.

## Live viewer count

Stream Player는 기본 지원. 자체 플레이어:

```
GET https://customer-<CODE>.cloudflarestream.com/<INPUT_ID>/views
```

```json
{ "liveViewers": 113 }
```

`recording.hideLiveViewerCount: true` 이면 숨김.

WebRTC 베타: 라이브 시청자 수·Analytics **아직 없음**.
