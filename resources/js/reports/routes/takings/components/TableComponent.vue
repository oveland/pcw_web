<template>
	<div>
		<table v-if="report.length"
			   class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
			<thead>
			<tr class="inverse">
				<th class="">
					<i class="fa fa-car text-muted"></i><br>
					{{ $t('Vehicle') }}<br>
					<small>{{ $t('Date') }}</small>
				</th>
				<th class="">
					<i class="fa fa-flag text-muted"></i><br>
					{{ $t('Route') }}
				</th>
				<th>
					<i class="fa fa-retweet text-muted"></i><br>
					{{ $t('Round Trip') }}
				</th>
				<th class="text-center">
					<i class="fa fa-users text-muted"></i><br>
					<small><i class="fa fa-compass text-muted"></i></small> {{ $t('Passengers') }}<br>
					<small class="text-muted">
						{{ $t('Recorder') }}
					</small>
				</th>
				<th width="40%">
					<i class="icon-briefcase"></i><br>
					{{ $t('Takings') }}
				</th>
			</tr>
			</thead>

			<tbody>
			<tr v-for="r in report" :class="r.passengers.recorders.count < 0 ? 'bg-danger' : ''">
				<th width="5%" class="bg-inverse text-white text-center">
					<span>{{ r.vehicle.number }}</span>
					<br>
					<small>{{ r.date }}</small>
				</th>
				<th v-if="r.forNormalTakings" width="5%" class="bg-inverse text-white text-center">
					{{ r.route.name }}
				</th>
				<th v-if="r.forNormalTakings" width="5%" class="bg-inverse text-white text-center">{{ r.roundTrip }}</th>
				<th v-if="r.forNormalTakings" width="5%" class="text-center">
					<small>{{ r.passengers.recorders.start }}</small><br>
					<small>{{ r.passengers.recorders.end }}</small>
					<hr class="m-0">
					<span>{{ r.passengers.recorders.count }}</span>
				</th>

				<td v-if="r.onlyControlTakings" colspan="3" class="text-center">
					<h4>
						<i class="icon-briefcase faa-ring"></i>
						<i class="fa fa-dollar faa-vertical"></i>
						{{ $t('Takings without dispatch turns') }}
					</h4>
				</td>

				<th width="40%">
					<takings-details-component :takings="r.takings" :hide-net="true"></takings-details-component>
				</th>
			</tr>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="12" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
				</tr>
				<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
					<td colspan="2" class="text-right text-bold">
						<i class="fa fa-sliders"></i> {{ $t('Average') }}
					</td>
					<td class="text-center text-bold">
						<i class="icon-clock"></i> {{ averages.routeTime }}
					</td>
					<td class="text-center text-bold">
						<i class="icon-users"></i> {{ $t('Passengers') }}: {{ averages.passengers }}
					</td>
					<td class="text-center" width="40%">
						<takings-details-component :takings="averages" type="averages"></takings-details-component>
					</td>
				</tr>
				<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
					<td colspan="3" class="text-right text-bold uppercase" style="font-size: 1.1em !important;">
						<i class="icon-layers"></i> {{ $t('Totals') }}
					</td>
					<td class="text-center text-bold hide">
						<i class="icon-clock"></i> {{ totals.routeTime }}
					</td>
					<td class="text-center text-bold" style="font-size: 1.1em !important;">
						<i class="icon-users"></i> {{ $t('Passengers') }}: {{ totals.passengers }}
					</td>
					<td class="text-center" width="40%">
						<takings-details-component :takings="totals" type="totals"></takings-details-component>
					</td>
				</tr>
			</tfoot>
		</table>

		<div v-if="!report.length" class="row">
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

.table-report .bg-danger{
	background-color: #f7e6e6 !important;
}
</style>