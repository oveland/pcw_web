<template>
    <div>
        <div v-if="marks.length">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th width="2%">
                                <i class="fa fa-list-ol text-muted"></i>
                            </th>
                            <th width="10%">
                                <i class="fa fa-retweet text-muted"></i><br> {{ $t('Trajectory') }}
                            </th>
                            <th width="15%">
                                <i class="fa fa-clock-0 text-muted"></i><br> {{ $t('Time') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('BEA') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Fuel') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Tolls') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Commissions') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Pay fall') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Get fall') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Operative expenses') }}
                            </th>


                            <th>
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                            </th>
                            <th>
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Auxiliaries') }}
                            </th>
                            <th>
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Locks') }}
                            </th>
                            <th>
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Boarded') }}
                            </th>
                            <th>
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Penalty') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Liquidate') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Payroll cost') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Net to car') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="mark in marks" :set="turn = getLiquidationTurn(mark)">
                            <td class="text-center">{{ mark.number }}</td>
                            <td class="text-center hide">
                                <i :class="mark.status.icon+' font-'+ mark.status.class" class="tooltips" data-placement="right" :data-original-title="mark.status.name"></i>
                            </td>
                            <td>
                                <small class="span-full badge badge-info" v-if="mark.trajectory">
                                    {{ mark.trajectory.name }}
                                </small>
                            </td>
                            <td width="15%" class="text-center">
                                <small>{{ mark.initialTime }} - {{ mark.finalTime }}</small>
                            </td>

                            <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.turnDiscounts.byFuel | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.turnDiscounts.byTolls | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ mark.commission.value | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.payFall | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.getFall | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.turnDiscounts.byOperativeExpenses | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ mark.passengersBEA }}</td>
                            <td class="text-center">{{ mark.auxiliaries }}</td>
                            <td class="text-center">{{ mark.locks }}</td>
                            <td class="text-center">{{ mark.boarded }}</td>
                            <td class="text-center">{{ mark.penalty.value | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ mark.payRollCost | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turnNetToCar(mark, turn) | thousandRound | numberFormat('$0,0') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">
                                <i class="icon-layers"></i> {{ $t('Total') }}
                            </td>
                            <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalDiscountByFuel | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalDiscountByTolls | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalCommissions | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalPayFall | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalGetFall | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalDiscountByOperativeExpenses | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalPassengersBea }}</td>
                            <td class="text-center">{{ totals.totalAuxiliaries }}</td>
                            <td class="text-center">{{ totals.totalLocks }}</td>
                            <td class="text-center">{{ totals.totalBoarded }}</td>
                            <td class="text-center">{{ totals.totalPenalties | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totalPayRollCost | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totalNetToCar | thousandRound | numberFormat('$0,0') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "DailyTableComponent",
        props: {
            liquidation: Object,
            urlExport: String,
            totals: Object,
            marks: Array,
            readonly: Boolean,
            searchParams: Object,
        },
        computed:{
            totalPayRollCost: function () {
                return _.sumBy(this.marks, 'payRollCost')
            },
            totalNetToCar: function () {
                return this.totals.totalDispatch - this.totalPayRollCost - this.totals.totalDiscountByFuel + this.totals.totalGetFall;
            }
        },
        data(){
            return {
                linkToPrintLiquidation: String
            }
        },
        methods:{
            getLiquidationTurn: function (mark) {
                return _.find(this.liquidation.byTurns, {markId: mark.id});
            },
            turnNetToCar: function(mark, turn){
                return turn.totalDispatch - mark.payRollCost - turn.turnDiscounts.byFuel + turn.getFall;
            }
        }
    }
</script>

<style scoped>

</style>