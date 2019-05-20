<template>
    <div>
        <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> Route / Trajectory
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Locks
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Auxiliaries
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Boarded
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> BEA
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> Total BEA
                </th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> Penalty by Turn
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="" v-for="mark in marks">
                <td class="col-md-2 text-center">
                    <span>{{ mark.turn.route.name }}</span><br>
                    <span class="label span-full" :class="mark.trajectory.name == 'IDA' ? 'label-success':'label-warning'">
                        {{ mark.trajectory.name }}
                    </span>
                </td>
                <td class="text-center">{{ mark.locks }}</td>
                <td class="text-center">{{ mark.auxiliaries }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center col-md-3">
                    <span class="tooltips span-penalty" :title="getPenaltyTitle(mark.penalty)">
                        <i :class="getPenaltyIconClass(mark.penalty)"></i> {{ mark.penalty.value | numberFormat('$0,0') }}
                    </span><br>
                </td>
            </tr>
            <tr>
                <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr class="totals">
                <td class="text-right">
                    <i class="icon-layers"></i> Totals
                </td>
                <td class="text-center">{{ totals.totalLocks }}</td>
                <td class="text-center">{{ totals.totalAuxiliaries }}</td>
                <td class="text-center">{{ totals.totalBoarded }}</td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    {{ totalPenaltiesByTurn | numberFormat('$0,0') }}
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-7">
                    <span class="pull-right text-bold">
                        <i class="icon-tag"></i> Total penalties:
                    </span>
                </div>
                <div class="col-md-5 text-center">
                    <span class="text-bold">{{ totalPenalties | numberFormat('$0,0') }}</span>
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
            totals: Object,
            liquidation: Object
        },
        methods: {
            getPenaltyTitle: function(penalty){
                return penalty.type === 'boarding' ? 'Valor fijo por abordado: $'+penalty.baseValue : 'Valor fijo por abordado: '+penalty.baseValue+'%';
            },
            getPenaltyIconClass: function(penalty){
                return penalty.type === 'boarding' ? 'icon-user': 'icon-user';
            }
        },
        computed: {
            totalPenaltiesByTurn: function () {
                return _.sumBy(this.marks, function (mark) {
                    return mark.penalty.value;
                });
            },
            totalPenalties: function () {
                return this.liquidation.totalPenalties = this.totalPenaltiesByTurn;
            }
        },
        created() {
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