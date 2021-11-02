<template>
	<video-player  class="video-player-box"
				   ref="videoPlayer"
				   :options="playerOptions"
				   :playsinline="true"
				   customEventName="customstatechangedeventname"

				   @play="onPlayerPlay($event)"
				   @pause="onPlayerPause($event)"
				   @ended="onPlayerEnded($event)"
				   @waiting="onPlayerWaiting($event)"
				   @playing="onPlayerPlaying($event)"
				   @loadeddata="onPlayerLoadeddata($event)"
				   @timeupdate="onPlayerTimeupdate($event)"
				   @canplay="onPlayerCanplay($event)"
				   @canplaythrough="onPlayerCanplaythrough($event)"

				   @statechanged="playerStateChanged($event)"
				   @ready="playerReadied">
	</video-player>
</template>

<script>

import 'video.js/dist/video-js.css'

import { videoPlayer } from 'vue-video-player'

export default {
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
					src: "http://127.0.0.1:7000/api/v2/files/rocket/get-video?vehicle=1873&date=2021-10-28",
					// src: "https://cdn.theguardian.tv/webM/2015/07/20/150716YesMen_synd_768k_vp8.webm",
				}],
				poster: "/static/images/author.jpg",
			}
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
		}
	}
}
</script>

<style scoped>

</style>