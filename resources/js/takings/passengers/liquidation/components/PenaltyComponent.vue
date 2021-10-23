<template>
    <div class="">
        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report m-b-0">
            <thead>
            <tr class="inverse">
                <th width="3%">
                    <i class="fa fa-list-ol text-muted"></i>
                </th>
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> {{ $t('Route') }} / {{ $t('Trajectory') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Locks') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Auxiliaries') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Boarded') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total gross') }}
                </th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Penalties') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total') }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="" v-for="mark in marks">
                <td class="text-center">{{ mark.number }}</td>
                <td class="col-md-2 text-center">
                    <small class="span-full badge badge-info" v-if="mark.trajectory">
                        {{ mark.trajectory.name }}
                    </small>
                    <small class="tooltips" :data-title="$t('Initial time')">{{ mark.initialTime }}</small> - <small class="tooltips" :data-title="$t('Final time')">{{ mark.finalTime }}</small>
                </td>
                <td class="text-center">{{ mark.locks }}</td>
                <td class="text-center">{{ mark.auxiliaries }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center col-md-3">
                    <span class="tooltips span-penalty" :data-original-title="getPenaltyTitle(mark.penalty)">
                        <i :class="getPenaltyIconClass(mark.penalty)"></i> {{ mark.penalty.value | numberFormat('$0,0') }}
                    </span><br>
                </td>
                <td class="text-center" style="font-weight: bold !important;font-size: 1.3em !important;">{{ mark.totalGrossBEA + mark.penalty.value | numberFormat('$0,0') }}</td>
            </tr>
            <tr>
                <td colspan="10" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr class="totals">
                <td colspan="2" class="text-right">
                    <i class="icon-layers"></i> {{ $t('Totals') }}
                </td>
                <td class="text-center">{{ totals.totalLocks }}</td>
                <td class="text-center">{{ totals.totalAuxiliaries }}</td>
                <td class="text-center">{{ totals.totalBoarded }}</td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totals.totalGrossBea | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totals.totalPenalties | numberFormat('$0,0') }}</td>
                <td class="text-center text-bold" style="font-weight: bold !important;font-size: 1.3em !important;">{{ totals.totalTurns | numberFormat('$0,0') }}</td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 text-right">
                    <span class="text-bold">
                        <i class="icon-tag"></i> {{ $t('Total penalties') }}:
                    </span>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-3 col-xs-12 text-right">
                    <span class="text-bold">{{ this.totals.totalPenalties | numberFormat('$0,0') }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "PenaltyComponent",
        props: {
            marks: Array,
            totals: Object
        },
        methods: {
            getPenaltyTitle: function(penalty){
                return penalty.type === 'boarding' ? 'Valor fijo por abordado: $'+penalty.baseValue : 'Porcentaje: '+penalty.baseValue+'%';
            },
            getPenaltyIconClass: function(penalty){
                return penalty.type === 'boarding' ? 'icon-user': 'icon-user';
            }
        }
    }
</script>

<style scoped>
    .totals span {
        font-size: 1.1em !important;
    }

    .total-discount-by-turn span {
        font-size: 1.2em !important;
    }

    .total-discount span {
        font-size: 1.6em !important;
    }

    .input-icon > i {
        margin: 8px 2px 4px 10px !important;
    }
    .span-penalty i{
        margin-right: 10px !important;
    }
</style>