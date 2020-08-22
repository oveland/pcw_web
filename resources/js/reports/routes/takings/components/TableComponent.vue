<template>
	<div>
		<table v-if="report && report.length"
			   class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
			<thead>
			<tr class="inverse">
				<th>
					<i class="fa fa-calendar text-muted"></i><br>
					<span>{{ $t('Date') }}</span>
				</th>
				<th class="">
					<i class="fa fa-car text-muted"></i><br>
					{{ $t('Vehicle') }}
				</th>
				<th colspan="2">
					<i class="fa fa-flag text-muted"></i><br>
					<span>{{ $t('Route') }}</span>
					<span><i class="fa fa-retweet"></i> {{ $t('Round Trip') }}</span>
				</th>
				<th class="text-center" colspan="2">
					<i class="fa fa-compass text-muted"></i><br>
						<span>{{ $t('Recorders') }}</span>
				</th>
				<th class="text-center">
					<i class="fa fa-users text-muted"></i><br>
					<span>{{ $t('Passengers') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Total production') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Control') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Fuel') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Fuel gallons') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Various') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Others') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Net production') }}</span>
				</th>
				<th>
					<i class="icon-briefcase"></i><br>
					<span class="">{{ $t('Observations') }}</span>
				</th>
			</tr>
			</thead>

			<tbody>
			<tr v-for="r in report" :class="r.passengers.recorders.count < 0 ? 'bg-danger' : ''">
				<th class="bg-inverse text-white text-center">
					<span>{{ r.date }}</span>
				</th>
				<th class="bg-inverse text-white text-center">
					<span>{{ r.vehicle.number }}</span>
				</th>
				<th v-if="r.forNormalTakings" class="bg-inverse text-white text-center">
					<span>{{ r.route.name }}</span>
				</th>
				<th v-if="r.forNormalTakings" class="bg-inverse text-white text-center">
					<span><i class="fa fa-retweet"></i> <span>{{ r.roundTrip }}</span></span>
				</th>
				<th v-if="r.forNormalTakings" class="text-center">
					<small>{{ r.passengers.recorders.start }}</small>
				</th>
				<th v-if="r.forNormalTakings" class="text-center">
					<small>{{ r.passengers.recorders.end }}</small>
				</th>
				<th v-if="r.forNormalTakings" class="text-center">
					<span>{{ r.passengers.recorders.count }}</span>
				</th>
				<td colspan="5" v-if="r.onlyControlTakings" class="text-center" :class="r.onlyControlTakings ? 'text-success' : ''">
					<small>
						<i class="icon-briefcase faa-ring"></i>
						<i class="fa fa-dollar faa-vertical"></i>
						{{ $t('Takings without dispatch turns') }}
					</small>
				</td>

				<td class="text-right">
					<span>{{ r.takings.totalProduction | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ r.takings.control | numberFormat('$0,0')}}</span>
				</td>
				<td class="text-right">
					<span>{{ r.takings.fuel | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ r.takings.fuelGallons.toFixed(2) }}</span>
				</td>
				<td class="text-right">
					<span>{{ r.takings.bonus | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ r.takings.others | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-bold text-right">
					<span>{{ r.takings.netProduction | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-info p-l-20" width="30%">
					<span>{{ r.takings.observations }}</span>
				</td>
			</tr>

			<tr>
				<td colspan="14" class="bg-inverse" style="height: 10px !important;;padding: 0;"></td>
			</tr>
			<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
				<td colspan="6" class="bg-inverse text-white text-right text-bold text-uppercase">
					<i class="fa fa-sliders text-muted"></i> {{ $t('Average') }}
				</td>
				<td class="text-center text-bold">
					{{ averages.passengers }}
				</td>
				<td class="text-right">
					<span>{{ averages.totalProduction | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ averages.control | numberFormat('$0,0')}}</span>
				</td>
				<td class="text-right">
					<span>{{ averages.fuel | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ averages.fuelGallons.toFixed(2) }}</span>
				</td>
				<td class="text-right">
					<span>{{ averages.bonus | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-right">
					<span>{{ averages.others | numberFormat('$0,0') }}</span>
				</td>
				<td class="text-bold text-right">
					<span>{{ averages.netProduction | numberFormat('$0,0') }}</span>
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
				<th colspan="6" class="bg-inverse text-white text-right text-bold uppercase" style="font-size: 1.1em !important;">
					<i class="icon-layers"></i> {{ $t('Totals') }}
				</th>
				<th class="bg-inverse text-white text-center text-bold" style="font-size: 1.1em !important;">
					{{ totals.passengers }}
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.totalProduction | numberFormat('$0,0') }}</span>
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.control | numberFormat('$0,0')}}</span>
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.fuel | numberFormat('$0,0') }}</span>
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.fuelGallons.toFixed(2) }}</span>
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.bonus | numberFormat('$0,0') }}</span>
				</th>
				<th class="bg-inverse text-white text-right">
					<span>{{ totals.others | numberFormat('$0,0') }}</span>
				</th>
				<th class="bg-inverse text-white text-bold text-right">
					<span>{{ totals.netProduction | numberFormat('$0,0') }}</span>
				</th>
			</tr>
			</tfoot>
		</table>

		<div v-if="!report || !report.length" class="row">
			<div class="alert alert-warning alert-bordered m-b-10 mb-10 mt-10 col-md-6 col-md-offset-3 offset-md-3">
				<div class="col-md-2" style="padding-top: 10px">
					<i class="fa fa-3x fa-exclamation-circle"></i>
				</div>
				<div class="col-md-10">
					<span class="close pull-right" data-dismiss="alert">×</span>
					<h4><strong>{{ $t('Ups!') }}</strong></h4>
					<hr class="hr">
					{{ $t('No registers found') }}
				</div>
			</div>
		</div>
	</div>
</template>

<script>
import TakingsDetailsComponent from './TakingsDetailsComponent';

export default {
	name: 'TableComponent',
	components: {TakingsDetailsComponent},
	comments: {TakingsDetailsComponent},
	props: {
		report: Array,
		totals: Object,
		averages: Object
	},
	methods: {}
}
</script>

<style>
.table-report small {
	font-size: 0.8em !important;
}

.table-report .bg-danger {
	background-color: #f7e6e6 !important;
}

.table-report tfoot td {
	padding-left: 5px !important;
	padding-right: 5px !important;
}

.table-report tfoot th.text-right {
	text-align: right !important;
	padding-left: 5px !important;
	padding-right: 5px !important;
}

.table-report tr.bg-danger th {
	background: #602525 !important;
	color: white !important;
}

</style>