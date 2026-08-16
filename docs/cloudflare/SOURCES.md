# Sources

## API reference expansion

Rewritten: 2026-08-16 14:31 KST (2026-08-16T05:31:04Z UTC)

Distillation pass (not a new crawl). Official Path/Query/Header/Body/Returns came from already-on-disk:

- `/workspace/api-docs/_raw/cloudflare-stream/api/resources/stream/index.md` (all operations)
- method/subresource files under `api/resources/stream/`
- product extras (`rotate_keys`, `live_inputs/{id}/videos`, `downloads/audio`, multipart POST `/stream`, POST `/audio`) from `stream/stream-live/start-stream-live.md`, `stream/stream-live/watch-live-stream.md`, `stream/stream-live/replay-recordings.md`, `stream/viewing-videos/download-videos.md`, `stream/uploading-videos/upload-video-file.md`, `stream/edit-videos/adding-additional-audio-tracks.md`, `stream/edit-videos/applying-watermarks.md`, `stream/manage-video-library/using-webhooks.md`, `stream/stream-live/webhooks.md`

No new official method pages were fetched. Stream API index already contained every REST operation. `keys/methods/list` remains the earlier 404 (ops live in the Stream API index).

Deliverable now split:

- `11-api-reference.md` hub
- `11-api-videos.md`
- `11-api-live.md`
- `11-api-edit.md`
- `11-api-keys-webhooks.md`

Copies stay in sync: `/workspace/gnuboard5-custom/docs/cloudflare/` and `/workspace/api-docs/cloudflare-stream-video/`.

---


Fetched: 2026-08-16 03:57 KST (2026-08-15T18:57:58Z UTC)
User-Agent: `web-to-docs/0.1 (+https://github.com/local/web-to-docs)`
Accept: text/markdown. Prefer index.md URLs. Raw copies under `/workspace/api-docs/_raw/cloudflare-stream/`.

Summary: 95 ok, 1 fail, 96 listed.

llms-full.txt downloaded in well under 15s (470,675 bytes) and used as cross-check only, not pasted wholesale.

## Failures

- `fail` HTTP 404 bytes=1727106 https://developers.cloudflare.com/api/resources/stream/subresources/keys/methods/list/index.md

Note: HTTP 404 with ~1.7MB body is the Cloudflare docs SPA fallback, not a markdown page.
`keys/methods/list` 404 — key ops are in the Stream API index (`GET/POST /stream/keys`, `DELETE /stream/keys/{id}`).

## Fetched URLs

| Status | HTTP | Bytes | URL | Raw file |
|--------|------|-------|-----|----------|
| ok | 200 | 3317 | https://developers.cloudflare.com/stream/index.md | `stream.md` |
| ok | 200 | 9711 | https://developers.cloudflare.com/stream/get-started/index.md | `stream/get-started.md` |
| ok | 200 | 3820 | https://developers.cloudflare.com/stream/uploading-videos/index.md | `stream/uploading-videos.md` |
| ok | 200 | 12203 | https://developers.cloudflare.com/stream/uploading-videos/direct-creator-uploads/index.md | `stream/uploading-videos/direct-creator-uploads.md` |
| ok | 200 | 9693 | https://developers.cloudflare.com/stream/uploading-videos/resumable-uploads/index.md | `stream/uploading-videos/resumable-uploads.md` |
| ok | 200 | 6058 | https://developers.cloudflare.com/stream/uploading-videos/upload-via-link/index.md | `stream/uploading-videos/upload-via-link.md` |
| ok | 200 | 2525 | https://developers.cloudflare.com/stream/uploading-videos/upload-video-file/index.md | `stream/uploading-videos/upload-video-file.md` |
| ok | 200 | 9082 | https://developers.cloudflare.com/stream/uploading-videos/player-api/index.md | `stream/uploading-videos/player-api.md` |
| ok | 200 | 7606 | https://developers.cloudflare.com/stream/faq/index.md | `stream/faq.md` |
| ok | 200 | 8098 | https://developers.cloudflare.com/stream/pricing/index.md | `stream/pricing.md` |
| ok | 200 | 3273 | https://developers.cloudflare.com/stream/examples/index.md | `stream/examples.md` |
| ok | 200 | 5738 | https://developers.cloudflare.com/stream/viewing-videos/displaying-thumbnails/index.md | `stream/viewing-videos/displaying-thumbnails.md` |
| ok | 200 | 11757 | https://developers.cloudflare.com/stream/viewing-videos/download-videos/index.md | `stream/viewing-videos/download-videos.md` |
| ok | 200 | 26507 | https://developers.cloudflare.com/stream/viewing-videos/securing-your-stream/index.md | `stream/viewing-videos/securing-your-stream.md` |
| ok | 200 | 6693 | https://developers.cloudflare.com/stream/viewing-videos/using-own-player/index.md | `stream/viewing-videos/using-own-player.md` |
| ok | 200 | 2452 | https://developers.cloudflare.com/stream/viewing-videos/using-own-player/android/index.md | `stream/viewing-videos/using-own-player/android.md` |
| ok | 200 | 2644 | https://developers.cloudflare.com/stream/viewing-videos/using-own-player/ios/index.md | `stream/viewing-videos/using-own-player/ios.md` |
| ok | 200 | 2169 | https://developers.cloudflare.com/stream/viewing-videos/using-own-player/web/index.md | `stream/viewing-videos/using-own-player/web.md` |
| ok | 200 | 10249 | https://developers.cloudflare.com/stream/viewing-videos/using-the-stream-player/index.md | `stream/viewing-videos/using-the-stream-player.md` |
| ok | 200 | 9148 | https://developers.cloudflare.com/stream/viewing-videos/using-the-stream-player/using-the-player-api/index.md | `stream/viewing-videos/using-the-stream-player/using-the-player-api.md` |
| ok | 200 | 6485 | https://developers.cloudflare.com/stream/edit-videos/adding-additional-audio-tracks/index.md | `stream/edit-videos/adding-additional-audio-tracks.md` |
| ok | 200 | 14232 | https://developers.cloudflare.com/stream/edit-videos/adding-captions/index.md | `stream/edit-videos/adding-captions.md` |
| ok | 200 | 22560 | https://developers.cloudflare.com/stream/edit-videos/applying-watermarks/index.md | `stream/edit-videos/applying-watermarks.md` |
| ok | 200 | 4191 | https://developers.cloudflare.com/stream/edit-videos/player-enhancements/index.md | `stream/edit-videos/player-enhancements.md` |
| ok | 200 | 6525 | https://developers.cloudflare.com/stream/edit-videos/video-clipping/index.md | `stream/edit-videos/video-clipping.md` |
| ok | 200 | 6423 | https://developers.cloudflare.com/stream/stream-live/index.md | `stream/stream-live.md` |
| ok | 200 | 2698 | https://developers.cloudflare.com/stream/stream-live/custom-domains/index.md | `stream/stream-live/custom-domains.md` |
| ok | 200 | 2427 | https://developers.cloudflare.com/stream/stream-live/download-stream-live-videos/index.md | `stream/stream-live/download-stream-live-videos.md` |
| ok | 200 | 4822 | https://developers.cloudflare.com/stream/stream-live/dvr-for-live/index.md | `stream/stream-live/dvr-for-live.md` |
| ok | 200 | 6274 | https://developers.cloudflare.com/stream/stream-live/live-instant-clipping/index.md | `stream/stream-live/live-instant-clipping.md` |
| ok | 200 | 3556 | https://developers.cloudflare.com/stream/stream-live/replay-recordings/index.md | `stream/stream-live/replay-recordings.md` |
| ok | 200 | 5673 | https://developers.cloudflare.com/stream/stream-live/simulcasting/index.md | `stream/stream-live/simulcasting.md` |
| ok | 200 | 11793 | https://developers.cloudflare.com/stream/stream-live/start-stream-live/index.md | `stream/stream-live/start-stream-live.md` |
| ok | 200 | 6934 | https://developers.cloudflare.com/stream/stream-live/troubleshooting/index.md | `stream/stream-live/troubleshooting.md` |
| ok | 200 | 8952 | https://developers.cloudflare.com/stream/stream-live/watch-live-stream/index.md | `stream/stream-live/watch-live-stream.md` |
| ok | 200 | 5688 | https://developers.cloudflare.com/stream/stream-live/webhooks/index.md | `stream/stream-live/webhooks.md` |
| ok | 200 | 8077 | https://developers.cloudflare.com/stream/transform-videos/index.md | `stream/transform-videos.md` |
| ok | 200 | 13134 | https://developers.cloudflare.com/stream/transform-videos/bindings/index.md | `stream/transform-videos/bindings.md` |
| ok | 200 | 5748 | https://developers.cloudflare.com/stream/transform-videos/sources/index.md | `stream/transform-videos/sources.md` |
| ok | 200 | 3333 | https://developers.cloudflare.com/stream/transform-videos/troubleshooting/index.md | `stream/transform-videos/troubleshooting.md` |
| ok | 200 | 1449 | https://developers.cloudflare.com/stream/manage-video-library/index.md | `stream/manage-video-library.md` |
| ok | 200 | 31241 | https://developers.cloudflare.com/stream/manage-video-library/bindings/index.md | `stream/manage-video-library/bindings.md` |
| ok | 200 | 14116 | https://developers.cloudflare.com/stream/manage-video-library/creator-id/index.md | `stream/manage-video-library/creator-id.md` |
| ok | 200 | 2199 | https://developers.cloudflare.com/stream/manage-video-library/searching/index.md | `stream/manage-video-library/searching.md` |
| ok | 200 | 10132 | https://developers.cloudflare.com/stream/manage-video-library/using-webhooks/index.md | `stream/manage-video-library/using-webhooks.md` |
| ok | 200 | 2151 | https://developers.cloudflare.com/stream/getting-analytics/index.md | `stream/getting-analytics.md` |
| ok | 200 | 7200 | https://developers.cloudflare.com/stream/getting-analytics/fetching-bulk-analytics/index.md | `stream/getting-analytics/fetching-bulk-analytics.md` |
| ok | 200 | 1947 | https://developers.cloudflare.com/stream/getting-analytics/live-viewer-count/index.md | `stream/getting-analytics/live-viewer-count.md` |
| ok | 200 | 9727 | https://developers.cloudflare.com/stream/webrtc-beta/index.md | `stream/webrtc-beta.md` |
| ok | 200 | 2227 | https://developers.cloudflare.com/stream/examples/hls-js/index.md | `stream/examples/hls-js.md` |
| ok | 200 | 2085 | https://developers.cloudflare.com/stream/examples/dash-js/index.md | `stream/examples/dash-js.md` |
| ok | 200 | 2202 | https://developers.cloudflare.com/stream/examples/video-js/index.md | `stream/examples/video-js.md` |
| ok | 200 | 2284 | https://developers.cloudflare.com/stream/examples/stream-player/index.md | `stream/examples/stream-player.md` |
| ok | 200 | 2933 | https://developers.cloudflare.com/stream/examples/android/index.md | `stream/examples/android.md` |
| ok | 200 | 3138 | https://developers.cloudflare.com/stream/examples/ios/index.md | `stream/examples/ios.md` |
| ok | 200 | 2660 | https://developers.cloudflare.com/stream/examples/shaka-player/index.md | `stream/examples/shaka-player.md` |
| ok | 200 | 2728 | https://developers.cloudflare.com/stream/examples/vidstack/index.md | `stream/examples/vidstack.md` |
| ok | 200 | 8802 | https://developers.cloudflare.com/stream/examples/obs-from-scratch/index.md | `stream/examples/obs-from-scratch.md` |
| ok | 200 | 2444 | https://developers.cloudflare.com/stream/examples/rtmps_playback/index.md | `stream/examples/rtmps_playback.md` |
| ok | 200 | 2395 | https://developers.cloudflare.com/stream/examples/srt_playback/index.md | `stream/examples/srt_playback.md` |
| ok | 200 | 12766 | https://developers.cloudflare.com/api/resources/stream/methods/list/index.md | `api/resources/stream/methods/list.md` |
| ok | 200 | 3174 | https://developers.cloudflare.com/api/resources/stream/subresources/live_inputs/methods/list/index.md | `api/resources/stream/subresources/live_inputs/methods/list.md` |
| ok | 200 | 9712 | https://developers.cloudflare.com/api/resources/stream/subresources/live_inputs/subresources/outputs/index.md | `api/resources/stream/subresources/live_inputs/subresources/outputs.md` |
| ok | 200 | 8818 | https://developers.cloudflare.com/api/resources/stream/subresources/captions/subresources/language/index.md | `api/resources/stream/subresources/captions/subresources/language.md` |
| ok | 200 | 10696 | https://developers.cloudflare.com/api/resources/stream/methods/get/index.md | `api/resources/stream/methods/get.md` |
| ok | 200 | 1478 | https://developers.cloudflare.com/api/resources/stream/methods/create/index.md | `api/resources/stream/methods/create.md` |
| ok | 200 | 13286 | https://developers.cloudflare.com/api/resources/stream/methods/edit/index.md | `api/resources/stream/methods/edit.md` |
| ok | 200 | 470 | https://developers.cloudflare.com/api/resources/stream/methods/delete/index.md | `api/resources/stream/methods/delete.md` |
| ok | 200 | 27966 | https://developers.cloudflare.com/stream/changelog/index.md | `stream/changelog.md` |
| ok | 200 | 2102 | https://developers.cloudflare.com/api/resources/stream/subresources/videos/methods/storage_usage/index.md | `api/resources/stream/subresources/videos/methods/storage_usage.md` |
| ok | 200 | 13348 | https://developers.cloudflare.com/api/resources/stream/subresources/copy/methods/create/index.md | `api/resources/stream/subresources/copy/methods/create.md` |
| ok | 200 | 13170 | https://developers.cloudflare.com/api/resources/stream/subresources/clip/methods/create/index.md | `api/resources/stream/subresources/clip/methods/create.md` |
| ok | 200 | 6555 | https://developers.cloudflare.com/api/resources/stream/subresources/direct_upload/methods/create/index.md | `api/resources/stream/subresources/direct_upload/methods/create.md` |
| ok | 200 | 7320 | https://developers.cloudflare.com/api/resources/stream/subresources/token/methods/create/index.md | `api/resources/stream/subresources/token/methods/create.md` |
| fail | 404 | 1727106 | https://developers.cloudflare.com/api/resources/stream/subresources/keys/methods/list/index.md | `` |
| ok | 200 | 1900 | https://developers.cloudflare.com/api/resources/stream/subresources/webhooks/methods/get/index.md | `api/resources/stream/subresources/webhooks/methods/get.md` |
| ok | 200 | 3117 | https://developers.cloudflare.com/api/resources/stream/subresources/downloads/methods/create/index.md | `api/resources/stream/subresources/downloads/methods/create.md` |
| ok | 200 | 4752 | https://developers.cloudflare.com/api/resources/stream/subresources/watermarks/methods/create/index.md | `api/resources/stream/subresources/watermarks/methods/create.md` |
| ok | 200 | 2731 | https://developers.cloudflare.com/api/resources/stream/subresources/audio_tracks/methods/copy/index.md | `api/resources/stream/subresources/audio_tracks/methods/copy.md` |
| ok | 200 | 10912 | https://developers.cloudflare.com/api/resources/stream/subresources/live_inputs/methods/create/index.md | `api/resources/stream/subresources/live_inputs/methods/create.md` |
| ok | 200 | 208988 | https://developers.cloudflare.com/api/resources/stream/index.md | `api/resources/stream/index.md` |
| ok | 200 | 470675 | https://developers.cloudflare.com/stream/llms-full.txt | `stream-llms-full.txt` |
| ok | 200 | 12452 | https://developers.cloudflare.com/stream/llms.txt | `stream-llms.txt` |
| ok | 200 | 2688 | https://developers.cloudflare.com/api/resources/stream/subresources/videos/index.md | `api/resources/stream/subresources/videos/index.md` |
| ok | 200 | 11354 | https://developers.cloudflare.com/api/resources/stream/subresources/captions/index.md | `api/resources/stream/subresources/captions/index.md` |
| ok | 200 | 10097 | https://developers.cloudflare.com/api/resources/stream/subresources/downloads/index.md | `api/resources/stream/subresources/downloads/index.md` |
| ok | 200 | 14847 | https://developers.cloudflare.com/api/resources/stream/subresources/watermarks/index.md | `api/resources/stream/subresources/watermarks/index.md` |
| ok | 200 | 49855 | https://developers.cloudflare.com/api/resources/stream/subresources/live_inputs/index.md | `api/resources/stream/subresources/live_inputs/index.md` |
| ok | 200 | 10357 | https://developers.cloudflare.com/api/resources/stream/subresources/keys/index.md | `api/resources/stream/subresources/keys/index.md` |
| ok | 200 | 6523 | https://developers.cloudflare.com/api/resources/stream/subresources/webhooks/index.md | `api/resources/stream/subresources/webhooks/index.md` |
| ok | 200 | 10928 | https://developers.cloudflare.com/api/resources/stream/subresources/audio_tracks/index.md | `api/resources/stream/subresources/audio_tracks/index.md` |
| ok | 200 | 8927 | https://developers.cloudflare.com/api/resources/stream/subresources/direct_upload/index.md | `api/resources/stream/subresources/direct_upload/index.md` |
| ok | 200 | 13356 | https://developers.cloudflare.com/api/resources/stream/subresources/copy/index.md | `api/resources/stream/subresources/copy/index.md` |
| ok | 200 | 15784 | https://developers.cloudflare.com/api/resources/stream/subresources/clip/index.md | `api/resources/stream/subresources/clip/index.md` |
| ok | 200 | 7502 | https://developers.cloudflare.com/api/resources/stream/subresources/token/index.md | `api/resources/stream/subresources/token/index.md` |
| ok | 200 | 711 | https://developers.cloudflare.com/api/resources/stream/subresources/embed/index.md | `api/resources/stream/subresources/embed/index.md` |

## Deliverable

Compiled set: `/workspace/api-docs/cloudflare-stream-video/`
Existing `/workspace/api-docs/cloudflare-stream/` HTML crawl was empty (0 operations) and was not reused.
