# 12 — Examples / 재생 레시피

공식 인덱스: https://developers.cloudflare.com/stream/examples/
코드는 공식 예제와 동일 패턴. CODE / UID 를 계정 값으로 교체. signed면 UID 자리에 token.

## Stream Player

원문: https://developers.cloudflare.com/stream/examples/stream-player/

```html
<html>
	<head> </head>
	<body>
		<div style="position: relative; padding-top: 56.25%;">
		<iframe
			src="https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/iframe?poster=https%3A%2F%2Fcustomer-f33zs165nr7gyfy4.cloudflarestream.com%2F6b9e68b07dfee8cc2d116e4c51d6a957%2Fthumbnails%2Fthumbnail.jpg%3Ftime%3D%26height%3D600"
			style="border: none; position: absolute; top: 0; left: 0; height: 100%; width: 100%;"
			allow="accelerometer; gyroscope; autoplay; encrypted-media; picture-in-picture;"
			allowfullscreen="true"
		></iframe>
		</div>
	</body>
</html>
```

## hls.js

원문: https://developers.cloudflare.com/stream/examples/hls-js/

```html
<html>
	<head>
		<script src="//cdn.jsdelivr.net/npm/hls.js@latest"></script>
	</head>
	<body>
		<video id="video"></video>
		<script>
			if (Hls.isSupported()) {
				const video = document.getElementById('video');
				const hls = new Hls();
				hls.attachMedia(video);
				hls.on(Hls.Events.MEDIA_ATTACHED, () => {
					hls.loadSource(
						'https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/manifest/video.m3u8'
					);
				});
			}

			video.play();
		</script>
	</body>
</html>
```

## dash.js

원문: https://developers.cloudflare.com/stream/examples/dash-js/

```html
<html>
	<head>
		<script src="https://cdn.dashjs.org/latest/dash.all.min.js"></script>
	</head>
	<body>
		<div>
			<div class="code">
				<video
					data-dashjs-player=""
					autoplay=""
					src="https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/manifest/video.mpd"
					controls="true"
				></video>
			</div>
		</div>
	</body>
</html>
```

## Video.js

원문: https://developers.cloudflare.com/stream/examples/video-js/

```html
<html>
	<head>
		<link
			href="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.10.2/video-js.min.css"
			rel="stylesheet"
		/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/video.js/7.10.2/video.min.js"></script>
	</head>
	<body>
		<video-js id="vid1" controls preload="auto">
			<source
				src="https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/manifest/video.m3u8"
				type="application/x-mpegURL"
			/>
		</video-js>

		<script>
			const vid = document.getElementById('vid1');
			const player = videojs(vid);
		</script>
	</body>
</html>
```

## Android (ExoPlayer)

원문: https://developers.cloudflare.com/stream/examples/android/

```kotlin
implementation 'com.google.android.exoplayer:exoplayer-hls:2.X.X'

SimpleExoPlayer player = new SimpleExoPlayer.Builder(context).build();

// Set the media item to the Cloudflare Stream HLS Manifest URL:
player.setMediaItem(MediaItem.fromUri("https://customer-9cbb9x7nxdw5hb57.cloudflarestream.com/8f92fe7d2c1c0983767649e065e691fc/manifest/video.m3u8"));

player.prepare();
```

## iOS (AVPlayer)

원문: https://developers.cloudflare.com/stream/examples/ios/

```swift
import SwiftUI
import AVKit

struct MyView: View {
    // Change the url to the Cloudflare Stream HLS manifest URL
    private let player = AVPlayer(url: URL(string: "https://customer-9cbb9x7nxdw5hb57.cloudflarestream.com/8f92fe7d2c1c0983767649e065e691fc/manifest/video.m3u8")!)

    var body: some View {
        VideoPlayer(player: player)
            .onAppear() {
                player.play()
            }
    }
}

struct MyView_Previews: PreviewProvider {
    static var previews: some View {
        MyView()
    }
}
```

## Shaka Player

원문: https://developers.cloudflare.com/stream/examples/shaka-player/

```html
<video
	id="video"
	width="640"
	poster="https://customer-f33zs165nr7gyfy4.cloudflarestream.com/6b9e68b07dfee8cc2d116e4c51d6a957/thumbnails/thumbnail.jpg"
	controls
	autoplay
></video>
```

## Vidstack

원문: https://developers.cloudflare.com/stream/examples/vidstack/

```json
{"@context":"https://schema.org","@type":"TechArticle","@id":"https://developers.cloudflare.com/stream/examples/vidstack/#page","headline":"Vidstack · Cloudflare Stream docs","description":"Example of video playback with Cloudflare Stream and Vidstack","url":"https://developers.cloudflare.com/stream/examples/vidstack/","inLanguage":"en","image":"https://developers.cloudflare.com/og-docs.png","dateModified":"2026-04-21","publisher":{"@type":"Organization","name":"Cloudflare","url":"https://www.cloudflare.com/"},"isPartOf":{"@type":"WebSite","@id":"https://developers.cloudflare.com/#website","name":"Cloudflare Docs","url":"https://developers.cloudflare.com/"},"keywords":["Playback"]}
```

## 그 외

| 예 | URL |
|----|-----|
| OBS first live | https://developers.cloudflare.com/stream/examples/obs-from-scratch/ |
| RTMPS playback (ffplay) | https://developers.cloudflare.com/stream/examples/rtmps_playback/ |
| SRT playback (ffplay) | https://developers.cloudflare.com/stream/examples/srt_playback/ |
| Test webhooks locally | https://developers.cloudflare.com/stream/examples/test-webhooks-locally/ |

Chromecast는 DASH 매니페스트를 권장. 매니페스트 캐시 금지.
