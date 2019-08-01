<template>
    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
        <thead>
        <tr class="inverse">
            <th class="col-md-1">
                <i class="fa fa-folder-open text-muted"></i>
            </th>
            <th class="col-md-2">
                <i class="fa fa-calendar text-muted"></i><br> Date
            </th>
            <th class="col-md-2">
                <i class="fa fa-flag text-muted"></i><br> Route
            </th>
            <th>
                <i class="fa fa-retweet text-muted"></i><br> Trajectory
            </th>
            <th class="col-md-2">
                <i class="fa fa-user text-muted"></i><br> Driver
            </th>
            <th>
                <i class="fa fa-clock-o text-muted"></i><br> From
            </th>
            <th>
                <i class="fa fa-clock-o text-muted"></i><br> To
            </th>
            <th>
                <i class="ion-android-stopwatch"></i><br> Duration
            </th>
            <th>
                <i class="fa fa-users text-muted"></i><br> Ascents
            </th>
            <th>
                <i class="fa fa-users text-muted"></i><br> Descents
            </th>
            <th>
                <i class="fa fa-users text-muted"></i><br> Boarded
            </th>
            <th>
                <i class="fa fa-users text-muted"></i><br> BEA
            </th>
            <th class="col-md-2">
                <i class="fa fa-dollar text-muted"></i><br> Total BEA
            </th>
        </tr>
        </thead>
        <tbody>
        <tr v-for="mark in marks">
            <td class="col-md-1 text-center">
                <i :class="mark.status.icon+' font-'+ mark.status.class" class="tooltips" :data-original-title="mark.status.name"></i>
            </td>
            <td class="col-md-2 text-center">{{ mark.date }}</td>
            <td class="col-md-2 text-center">{{ mark.turn.route.name }}</td>
            <td class="text-center">
                <span class="label span-full" v-if="mark.trajectory" :class="mark.trajectory.name == 'IDA' ? 'label-success':'label-warning'">
                    {{ mark.trajectory.name }}
                </span>
            </td>
            <td class="col-md-2 text-center">
                <span v-if="mark.turn.driver">
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
            <td colspan="13" style="    height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
        </tr>
        <tr>
            <td rowspan="2" colspan="5" class="text-center">
                <button v-if="!readonly" class="btn btn-sm green-haze btn-outline sbold uppercase m-t-5" data-toggle="modal" data-target="#modal-generate-liquidation">
                    <i class="fa fa-dollar"></i> Generate liquidation
                </button>
            </td>
            <td colspan="3" class="text-right">
                <i class="fa fa-sliders"></i> Average
            </td>
            <td class="text-center">{{ totals.averagePassengersUp | numberFormat('0.00') }}</td>
            <td class="text-center">{{ totals.averagePassengersDown | numberFormat('0.00') }}</td>
            <td class="text-center">{{ totals.averageBoarded | numberFormat('0.00') }}</td>
            <td class="text-center">{{ totals.averagePassengersBea | numberFormat('0.00') }}</td>
            <td class="text-center">{{ totals.averageBea | numberFormat('$0,0') }}</td>
        </tr>
        <tr>
            <td colspan="3" class="text-right">
                <i class="icon-layers"></i> Total
            </td>
            <td class="text-center">{{ totals.totalPassengersUp }}</td>
            <td class="text-center">{{ totals.totalPassengersDown }}</td>
            <td class="text-center">{{ totals.totalBoarded }}</td>
            <td class="text-center">{{ totals.totalPassengersBea }}</td>
            <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
        </tr>
        </tbody>
    </table>
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