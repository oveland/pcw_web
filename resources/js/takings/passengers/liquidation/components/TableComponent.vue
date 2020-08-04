<template>
    <div>
        <table v-if="marks.length" class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th width="3%" colspan="2">
                    <i class="fa fa-list-ol text-muted"></i>
                </th>
                <th class="col-md-2">
                    <i class="fa fa-calendar text-muted"></i><br> {{ $t('Date') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> {{ $t('Route') }}
                </th>
                <th>
                    <i class="fa fa-retweet text-muted"></i><br> {{ $t('Trajectory') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-user text-muted"></i><br> {{ $t('Driver') }}
                </th>
                <th>
                    <i class="fa fa-clock-o text-muted"></i><br> {{ $t('From') }}
                </th>
                <th>
                    <i class="fa fa-clock-o text-muted"></i><br> {{ $t('To') }}
                </th>
                <th>
                    <i class="ion-android-stopwatch"></i><br> {{ $t('Duration') }}
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Ascents') }}
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Descents') }}
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Boarded') }}
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br> {{ $t('BEA') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total BEA') }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="mark in marks">
                <td class="text-center">
                    <i :class="mark.status.icon+' font-'+ mark.status.class" class="tooltips" data-placement="right" :data-original-title="mark.status.name"></i>
                </td>
                <td class="text-center">
                    <span class="">{{ mark.number }}</span>
                </td>
                <td class="col-md-2 text-center">{{ mark.date }}</td>
                <td class="col-md-2 text-center">{{ mark.turn.route.name }}</td>
                <td class="text-center">
                    <span class="span-full badge badge-info" v-if="mark.trajectory">
                        {{ mark.trajectory.name }}
                    </span>
                </td>
                <td class="col-md-2 text-center">
                    <span v-if="mark.turn.driver && mark.turn.driver.first_name">
                        {{ mark.turn.driver.first_name + (mark.turn.driver.last_name ? (' ' + mark.turn.driver.last_name):'') }}
                    </span>
                </td>
                <td class="text-center">{{ mark.initialTime }}</td>
                <td class="text-center">{{ mark.finalTime }}</td>
                <td class="text-center">{{ mark.duration }}</td>
                <td class="text-center">{{ mark.passengersUp }}</td>
                <td class="text-center">{{ mark.passengersDown }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="col-md-2 text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
            </tr>
            <tr>
                <td colspan="14" style="    height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="6" class="text-center">
                    <button v-if="!readonly && marks.length" class="btn btn-sm yellow-crusta btn-circle btn-outline sbold uppercase m-t-5" data-toggle="modal" data-target="#modal-generate-liquidation">
                        <i class="icon-layers"></i> {{ $t('Generate liquidation') }}
                    </button>
                </td>
                <td colspan="3" class="text-right">
                    <i class="fa fa-sliders"></i> {{ $t('Average') }}
                </td>
                <td class="text-center">{{ totals.averagePassengersUp | numberFormat('0.00') }}</td>
                <td class="text-center">{{ totals.averagePassengersDown | numberFormat('0.00') }}</td>
                <td class="text-center">{{ totals.averageBoarded | numberFormat('0.00') }}</td>
                <td class="text-center">{{ totals.averagePassengersBea | numberFormat('0.00') }}</td>
                <td class="text-center">{{ totals.averageBea | numberFormat('$0,0') }}</td>
            </tr>
            <tr>
                <td colspan="3" class="text-right">
                    <i class="icon-layers"></i> {{ $t('Total') }}
                </td>
                <td class="text-center">{{ totals.totalPassengersUp }}</td>
                <td class="text-center">{{ totals.totalPassengersDown }}</td>
                <td class="text-center">{{ totals.totalBoarded }}</td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
            </tr>
            </tbody>
        </table>

        <div v-if="!marks.length" class="row">
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
    export default {
        name: 'TableComponent',
        props: {
            readonly: Boolean,
            marks: Array,
            totals: Object
        },
        methods: {

        }
    }
</script>