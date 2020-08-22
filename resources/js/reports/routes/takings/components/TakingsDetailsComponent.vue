<template>
	<div class="takings-details text-left col-md-12" :class="type">
		<td class="text-right">
			<span class="pull-left">{{ $t('Total production') }}:</span>
			<span>{{ takings.totalProduction | numberFormat('$0,0') }}</span>
		</td>
		<td class="text-right">
			<span class="pull-left">{{ $t('Control') }}:</span>
			<span>{{takings.control | numberFormat('$0,0')}}</span>
		</td>
		<td class="text-right">
			<span class="pull-left">{{ $t('Fuel') }}:</span>
			<span>{{ takings.fuel | numberFormat('$0,0') }}</span>
		</td>
		<td class="text-right">
			<span class="pull-left">{{ $t('Fuel gallons') }}:</span>
			<span>{{ takings.fuelGallons.toFixed(2) }}</span>
		</td>
		<td class="text-right">
			<span class="pull-left">{{ $t('Various') }}:</span>
			<span>{{ takings.bonus | numberFormat('$0,0') }}</span>
		</td>
		<td class="text-right">
			<span class="pull-left">{{ $t('Others') }}:</span>
			<span>{{ takings.others | numberFormat('$0,0') }}</span>
		</td>
		<td class="text-bold text-right" :class="hideNet ? 'hide' : ''">
			<span class="pull-left">{{ $t('Net production') }}:</span>
			<span>{{ takings.netProduction | numberFormat('$0,0') }}</span>
		</td>
		<td v-if="takings.observations && type !== 'totals'" class="text-info">
			<span class="pull-left">{{ $t('Observations') }}:</span>
			<br>
			<span>{{ takings.observations }}</span>
		</td>
	</div>
</template>

<script>
export default {
	name: "TakingsDetailsComponent",
	props: {
		takings: Object,
		hideNet: Boolean,
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