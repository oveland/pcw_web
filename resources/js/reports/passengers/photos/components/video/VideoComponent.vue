<template>
	<video-player  class="video-player-box"
				   ref="videoPlayer"
				   :options="playerOptions"
				   :playsinline="true"
				   customEventName="customstatechangedeventname"

				   @play="onPlayerPlay($event)"
				   @pause="onPlayerPause($event)"
				   @ended="test($event)"
				   @waiting="test($event)"
				   @playing="test($event)"
				   @loadeddata="test(test)"
				   @timeupdate="test($event)"
				   @canplay="test($event)"
				   @canplaythrough="test($event)"

				   @statechanged="playerStateChanged($event)"
				   @ready="playerReadied">
	</video-player>
</template>

<script>

import 'video.js/dist/video-js.css'
import 'videojs-vjsdownload/dist/videojs-vjsdownload.css'
import 'videojs-vjsdownload/dist/videojs-vjsdownload.min.js'

import { videoPlayer } from 'vue-video-player'

export default {
	props: ['src'],
	components: {
		videoPlayer
	},
	data() {
		return {
			playerOptions: {
				// videojs options
				muted: true,
				language: 'en',
				playbackRates: [0.7, 1.0, 1.5, 2.0],
				sources: [{
					type: "video/mp4",
					src: this.src,
					// src: "https://cdn.theguardian.tv/webM/2015/07/20/150716YesMen_synd_768k_vp8.webm",
				}],
				poster: "https://cdn.dribbble.com/users/2170220/screenshots/6196024/btn_1.gif",
				plugins: {
					vjsdownload:{
						beforeElement: 'playbackRateMenuButton',
						textControl: 'Download video',
						name: 'downloadButton',
						downloadURL: 'https://video_url.mp4' //optional if you need a different download url than the source
					}
				}
			}
		}
	},
	watch: {
		src() {
			this.playerOptions.sources.src = this.src;
		}
	},
	mounted() {
		console.log('this is current player instance object', this.player)
	},
	computed: {
		player() {
			return this.$refs.videoPlayer.player
		}
	},
	methods: {
		// listen event
		onPlayerPlay(player) {
			// console.log('player play!', player)
		},
		onPlayerPause(player) {
			// console.log('player pause!', player)
		},
		// ...player event

		// or listen state event
		playerStateChanged(playerCurrentState) {
			// console.log('player current update state', playerCurrentState)
		},

		// player is ready
		playerReadied(player) {
			console.log('the player is readied', player)
			// you can use it to do something...
			// player.[methods]
		},
		test(event) {
			console.log(event);
		}
	}
}
</script>

<style>
	.video-player-box .video-js {
		margin: auto !important;
		width: 600px;
		height: 500px;
	}

	.video-player-box .vjs-poster {
		width: 600px;
		height: 500px;
	}

	.video-player-box .vjs-big-play-button {
		display: none !important;
	}
</style>