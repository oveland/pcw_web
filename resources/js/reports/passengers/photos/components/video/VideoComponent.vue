<template>
	<video :src="src" class="video-player-box" controls autoplay></video>
</template>

<script>


export default {
	props: ['src'],
}
</script>

<style>
</style>