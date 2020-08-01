<template>
	<div>
		<carousel autoplay :interval="2500" :data="graphs"></carousel>
	</div>
</template>

<script>

import VueCarousel from '@chenfengyuan/vue-carousel';

export default {
	name: "GraphCarrouselComponent",
	components: {
		VueCarousel
	},
	data() {
		return {
			graphs: [
				'<img src="/img/reports/ex-1.svg" style="height: 400px" class="col-md-12 graph"/>',
				'<img src="/img/reports/ex-2.svg" style="height: 400px" class="col-md-12 graph"/>',
				'<img src="/img/reports/ex-3.svg" style="height: 400px" class="col-md-12 graph"/>',
				'<img src="/img/reports/ex-4.svg" style="height: 400px" class="col-md-12 graph"/>',
				'<img src="/img/reports/ex-5.svg" style="height: 400px" class="col-md-12 graph"/>',
				'<img src="/img/reports/develop.svg" style="height: 400px" class="col-md-12 graph"/>',
			]
		}
	}
}
</script>

<style scoped>
.graph {
	align-items: center;
	background-color: #666;
	color: #999;
	display: flex;
	font-size: 1.5rem;
	justify-content: center;
	min-height: 10rem;
}
</style>