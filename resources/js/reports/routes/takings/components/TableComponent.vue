<template>
	<div>
		<table v-if="report && report.length"
			   class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
			<thead>
			<tr class="inverse">
				<th>
					<small>
						<i class="fa fa-calendar text-muted"></i><br>
						<span>{{ $t('Date') }}</span>
					</small>
				</th>
				<th class="">
					<small>
						<i class="fa fa-car text-muted"></i><br>
						{{ $t('Vehicle') }}
					</small>
				</th>
				<th class="" width="1%">
					<small>
						<i class="fa fa-user text-muted"></i><br>
						{{ $t('Driver code') }}
					</small>
				</th>
				<th colspan="2">
					<small>
						<i class="fa fa-flag text-muted"></i><br>
						<span>{{ $t('Route') }} | {{ $t('Round Trip') }}</span>
					</small>
				</th>
				<th class="text-center" colspan="2" v-if="options.showRecorders">
					<small>
						<i class="fa fa-compass text-muted"></i><br>
						<span>{{ $t('Recorders') }}</span>
					</small>
				</th>
				<th class="text-center" colspan="2" v-if="options.showSensor">
					<small>
						<i class="fa fa-compass text-muted"></i><br>
						<span>{{ $t('Tariffs') }}</span>
					</small>
					<div class="col-md-12 p-0">
						<div class="col-md-6 col-sm-6 col-xs-6">
							<small>
								<i class="fa fa-dollar"></i> 1600
							</small>
						</div>
						<div class="col-md-6 col-sm-6 col-xs-6">
							<small>
								<i class="fa fa-dollar"></i> 2100
							</small>
						</div>
					</div>
				</th>
				<th class="text-center">
					<small>
						<i class="fa fa-users text-muted"></i><br>
						<span>{{ $t('Passengers') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Total production') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Control') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Fuel') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Fuel gallons') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Station') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Various') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Others') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Net production') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Advance') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Balance') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="icon-briefcase"></i><br>
						<span class="">{{ $t('Observations') }}</span>
					</small>
				</th>
				<th>
					<small>
						<i class="fa fa-user"></i> <i class="fa fa-edit"></i><br>
						<span class="">{{ $t('Updated at') }}</span>
					</small>
				</th>
			</tr>
			</thead>

			<tbody>
			<tr v-for="r in report" :class="r.passengers.recorders.count < 0 ? 'bg-danger' : ''">
				<th class="bg-inverse text-white text-center">
					<small>{{ r.date }}</small>
					<div>
						<small class="text-muted">{{ r.departureTime }} - {{ r.arrivalTime }}</small>
					</div>
				</th>
				<th class="bg-inverse text-white text-center">
					<small>{{ r.vehicle.number }}</small>
				</th>
				<th class="bg-inverse text-white text-center">
					<small class="tooltips" :title="r.driverName">{{ r.driverCode }}</small>
				</th>
				<th v-if="r.forNormalTakings" class="bg-inverse text-white text-center">
					<small>{{ r.route.name }}</small>
				</th>
				<th v-if="r.forNormalTakings" class="bg-inverse text-white text-center">
					<small><i class="fa fa-retweet"></i> <span>{{ r.roundTrip }}</span></small>
				</th>
				<th v-if="r.forNormalTakings && options.showRecorders" class="text-center">
					<small>{{ r.passengers.recorders.start }}</small>
				</th>
				<th v-if="r.forNormalTakings && options.showRecorders" class="text-center">
					<small>{{ r.passengers.recorders.end }}</small>
				</th>

				<th v-if="r.forNormalTakings && options.showSensor" class="text-center">
					<small v-if="r.passengers.sensor.tariff.a">{{ r.passengers.sensor.tariff.a.totalCounted }}</small> •
					<small class="text-muted" v-if="r.passengers.sensor.tariff.a">{{ r.passengers.sensor.tariff.a.totalCharge | numberFormat('$0,0') }}</small>
				</th>
				<th v-if="r.forNormalTakings && options.showSensor" class="text-center">
					<small v-if="r.passengers.sensor.tariff.b">{{ r.passengers.sensor.tariff.b.totalCounted }}</small> •
					<small class="text-muted" v-if="r.passengers.sensor.tariff.a">{{ r.passengers.sensor.tariff.b.totalCharge | numberFormat('$0,0') }}</small>
				</th>

				<th v-if="r.forNormalTakings" class="text-center">
					<small>{{ r.passengers.recorders.count }}</small>
				</th>
				<td colspan="5" v-if="r.onlyControlTakings" class="text-center" :class="r.onlyControlTakings ? 'text-success' : ''">
					<small>
						<i class="icon-briefcase faa-ring"></i>
						<i class="fa fa-dollar faa-vertical"></i>
						{{ $t('Takings without dispatch turns') }}
					</small>
				</td>

				<td class="text-right">
					<small>{{ r.takings.totalProduction | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ r.takings.control | numberFormat('$0,0')}}</small>
				</td>
				<td class="text-right">
					<small>{{ r.takings.fuel | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ r.takings.fuelGallons.toFixed(2) }}</small>
				</td>
				<td class="text-left">
					<small>{{ r.takings.stationFuel }}</small>
				</td>
				<td class="text-right">
					<small>{{ r.takings.bonus | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ r.takings.others | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ r.takings.netProduction | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ r.takings.advance | numberFormat('$0,0') }}</small>
					<br>
					<small class="text-muted tooltips" :data-title="$t('Passengers advance')">{{ r.takings.passengersAdvance | numberFormat('0.0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ r.takings.balance | numberFormat('$0,0') }}</small>
					<br>
					<small class="text-muted tooltips" :data-title="$t('Passengers balance')">{{ r.takings.passengersBalance | numberFormat('0.0') }}</small>
				</td>
				<td class="text-info p-l-20">
					<small>{{ r.takings.observations }}</small>
				</td>
			</tr>

			<tr>
				<td colspan="20" class="bg-inverse" style="height: 10px !important;;padding: 0;"></td>
			</tr>
			<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
				<td :colspan=" options.showRecorders ? 7 : 5" class="bg-inverse text-white text-right text-bold text-uppercase">
					<small><i class="fa fa-sliders text-muted"></i> {{ $t('Average') }}</small>
				</td>

				<th v-if="options.showSensor" class="text-center">
					<small>{{ averages.passengers.sensor.tariff.a.totalCounted }}</small> •
					<small class="text-muted">{{ averages.passengers.sensor.tariff.a.totalCharge | numberFormat('$0,0') }}</small>
				</th>

				<th v-if="options.showSensor" class="text-center">
					<small>{{ averages.passengers.sensor.tariff.b.totalCounted }}</small> •
					<small class="text-muted">{{ averages.passengers.sensor.tariff.b.totalCharge | numberFormat('$0,0') }}</small>
				</th>

				<td class="text-center text-bold">
					<small>{{ averages.passengers.recorders.count }}</small>
				</td>
				<td class="text-right">
					<small>{{ averages.totalProduction | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ averages.control | numberFormat('$0,0')}}</small>
				</td>
				<td class="text-right">
					<small>{{ averages.fuel | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ averages.fuelGallons.toFixed(2) }}</small>
				</td>
				<td class="text-right">
				</td>
				<td class="text-right">
					<small>{{ averages.bonus | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-right">
					<small>{{ averages.others | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ averages.netProduction | numberFormat('$0,0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ averages.advance | numberFormat('$0,0') }}</small>
					<br>
					<small class="text-muted tooltips" :data-title="$t('Passengers advance')"><i class="fa fa-users"></i> {{ averages.passengersAdvance | numberFormat('0.0') }}</small>
				</td>
				<td class="text-bold text-right">
					<small>{{ averages.balance | numberFormat('$0,0') }}</small>
					<br>
					<small class="text-muted 	tooltips" :data-title="$t('Passengers balance')"> <i class="fa fa-users"></i>{{ averages.passengersBalance | numberFormat('0.0') }}</small>
				</td>
			</tr>
			</tbody>
			<tfoot>
			<tr :class="totals.hasInvalidCounts ? 'bg-danger' : ''">
				<th :colspan=" options.showRecorders ? 7 : 5" class="bg-inverse text-white text-right text-bold uppercase" style="font-size: 1.1em !important;">
					<small><i class="icon-layers"></i> {{ $t('Totals') }}</small>
				</th>

				<th v-if="options.showSensor" class="bg-inverse text-white text-center">
					<small>{{ totals.passengers.sensor.tariff.a.totalCounted }}</small> •
					<small>{{ totals.passengers.sensor.tariff.a.totalCharge | numberFormat('$0,0') }}</small>
				</th>

				<th v-if="options.showSensor" class="bg-inverse text-white text-center">
					<small>{{ totals.passengers.sensor.tariff.b.totalCounted }}</small> •
					<small>{{ totals.passengers.sensor.tariff.b.totalCharge | numberFormat('$0,0') }}</small>
				</th>

				<th class="bg-inverse text-white text-center text-bold" style="font-size: 1.1em !important;">
					<small>{{ totals.passengers.recorders.count }}</small>
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.totalProduction | numberFormat('$0,0') }}</small>
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.control | numberFormat('$0,0')}}</small>
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.fuel | numberFormat('$0,0') }}</small>
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.fuelGallons.toFixed(2) }}</small>
				</th>
				<th class="bg-inverse text-right">
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.bonus | numberFormat('$0,0') }}</small>
				</th>
				<th class="bg-inverse text-white text-right">
					<small>{{ totals.others | numberFormat('$0,0') }}</small>
				</th>
				<th class="bg-inverse text-white text-bold text-right">
					<span>{{ totals.netProduction | numberFormat('$0,0') }}</span>
				</th>
				<th class="bg-inverse text-white text-bold text-right">
					<span>{{ totals.advance | numberFormat('$0,0') }}</span>
					<br>
					<small class="tooltips" :data-title="$t('Passengers advance')"><i class="fa fa-users"></i> {{ totals.passengersAdvance | numberFormat('0.0') }}</small>
				</th>
				<th class="bg-inverse text-white text-bold text-right">
					<span>{{ totals.balance | numberFormat('$0,0') }}</span>
					<br>
					<small class="tooltips" :data-title="$t('Passengers balance')"><i class="fa fa-users"></i> {{ totals.passengersBalance | numberFormat('0.0') }}</small>
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
		averages: Object,
		options: Object
	},
	watch: {
		report(){
			setTimeout(()=>{
				$('.tooltips').tooltip();
			},1000);
		}
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