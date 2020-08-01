<template>
	<div class="takings-details text-left col-md-12" :class="type">
		<p class="text-right">
			<span class="pull-left">{{ $t('Total production') }}:</span>
			<span>{{ takings.totalProduction | numberFormat('$0,0') }}</span>
		</p>
		<p class="text-right">
			<span class="pull-left">{{ $t('Control') }}:</span>
			<span>{{takings.control | numberFormat('$0,0')}}</span>
		</p>
		<p class="text-right">
			<span class="pull-left">{{ $t('Fuel') }}:</span>
			<span>{{ takings.fuel | numberFormat('$0,0') }}</span>
		</p>
		<p class="text-right">
			<span class="pull-left">{{ $t('Others') }}:</span>
			<span>{{ takings.others | numberFormat('$0,0') }}</span>
		</p>
		<p class="text-bold text-right">
			<span class="pull-left">{{ $t('Net production') }}:</span>
			<span>{{ takings.netProduction | numberFormat('$0,0') }}</span>
		</p>
		<p v-if="takings.observations" class="text-info">
			<span class="pull-left">{{ $t('Observations') }}:</span>
			<br>
			<span>{{ takings.observations }}</span>
		</p>
	</div>
</template>

<script>
export default {
	name: "TakingsDetailsComponent",
	props: {
		takings: Object,
		type: String
	}
}
</script>

<style scoped>
.takings-details p {
	margin: 0;
	font-size: 0.9em !important;
	color: #464444 !important;
}

.takings-details p > span {
	font-weight: bold !important;
}

.takings-details.info p {
	color: #464444 !important;
}

.takings-details p.text-info {
	color: #dd7100 !important;
}

.takings-details.averages p {
	color: #1e569c !important;
	font-size: 1em !important;
}

.takings-details.totals p {
	color: #093e19 !important;
	font-size: 1.1em !important;
}
</style>