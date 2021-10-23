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
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Passengers') }}
                            </th>
                            <th>
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Taken') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-rocket text-muted"></i><br> {{ $t('Actions') }}
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
                            <td class="text-center">{{ mark.passengersBEA }}</td>
                            <td class="text-center">{{ turn.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" :title="$t('Process charge')" @click="processCharge(mark)" data-toggle="modal" data-target="#modal-charge-turn">
                                    <i class="fa fa-user-secret"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">
                                <i class="icon-layers"></i> {{ $t('Total') }}
                            </td>
                            <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalPassengersBea }}</td>
                            <td class="text-center">{{ totals.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="modal fade" id="modal-charge-turn" tabindex="1" data-backdrop="static">
                <div class="modal-dialog">
                    <form class="modal-content row " @submit.prevent="" v-if="liquidationTurn">
                        <div class="portlet light m-0">
                            <div class="portlet-title tabbable-line m-0">
                                <div class="caption  col-md-12">
                                    <i class="fa fa-user-secret"></i>
                                    <span class="caption-subject font-dark bold uppercase">
                                    {{ $t('Process charge') }}
                                </span>
                                    <strong class="pull-right" style="font-size: 1.2em !important;">Total recaudado {{ liquidationTurn.totalDispatch | thousandRound | numberFormat('$0,0') }}</strong>
                                </div>
                            </div>
                            <div class="row portlet-body">
                                <div class="col-md-12 text-left no-padding">
                                    <div class="col-md-6">
                                        <label class="control-label">Valor Conduce</label>
                                        <multiselect v-model="driverCostSelected" :placeholder="$t('Select a vehicle')" label="name" track-by="id" :options="driverCost"></multiselect>
                                    </div>
                                </div>
                                <hr class="col-md-12 no-padding">
                                <div class="col-md-12 table-responsive">
                                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report m-0">
                                        <thead>
                                        <tr class="inverse">
                                            <th class="col-md-2">
                                                <i class="fa fa-tag text-muted"></i><br> {{ $t('Name') }}
                                            </th>
                                            <th class="col-md-2">
                                                <i class="fa fa-tags text-muted"></i><br> {{ $t('Concept') }}
                                            </th>
                                            <th class="col-md-2">
                                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Value') }}
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr v-for="cost in costs">
                                            <td class="text-center">{{ cost.name | capitalize }}</td>
                                            <td class="text-center">{{ cost.concept }}</td>
                                            <td class="text-center">
                                                {{ cost.value | numberFormat('$0,0') }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="text-right" colspan="2">{{ $t('Total') }}</td>
                                            <td class="text-center">{{ totalCosts | numberFormat('$0,0') }}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer col-md-12 text-center">
                            <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Cancel')" data-dismiss="modal">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn btn-success btn-outline sbold uppercase btn-circle tooltips" :title="$t('Save')">
                                <i class="fa fa-save"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        name: "RoadSafetyTakingsTurnsTableComponent",
        props: {
            costsList: Array,
            liquidation: Object,
            urlExport: String,
            totals: Object,
            marks: Array,
            readonly: Boolean,
            searchParams: Object,
            liquidationTurn: {
                id: 0,
                vehicle: {},
                date: '',
                liquidation: {},
                totals: {},
                user: {},
                marks: [],
            },
        },
        computed:{
            totalPayRollCost: function () {
                return _.sumBy(this.marks, 'payRollCost')
            },
            totalNetToCar: function () {
                return this.totals.totalDispatch - this.totalPayRollCost - this.totals.totalDiscountByFuel + this.totals.totalGetFall;
            },
            totalCosts : function () {
                return _.sumBy(this.costs, 'value');
            }
        },
        data(){
            return {
                linkToPrintLiquidation: String,
                costs: [],
                driverCostSelected: {
                    id: 1,
                    name: 'Conduce 1 | $11.000',
                    total: 18,
                },
                driverCost: [
                    {
                        id: 1,
                        name: 'Conduce 1 | $11.000',
                        total: 18,
                    },
                    {
                        id: 2,
                        name: 'Conduce 2 | $9.000',
                        total: 1000,
                    }
                ],
            }
        },
        methods:{
            processCharge(mark) {
                this.liquidationTurn = this.getLiquidationTurn(mark);
            },
            saveCharge(mark) {
                this.liquidationTurn = this.getLiquidationTurn(mark);
            },
            getLiquidationTurn: function (mark) {
                return _.find(this.liquidation.byTurns, {markId: mark.id});
            },
            turnNetToCar: function(mark, turn){
                return turn.totalDispatch - mark.payRollCost - turn.turnDiscounts.byFuel + turn.getFall;
            }
        },
        mounted() {
            this.costs = this.costsList;
        },
        components: {
            Multiselect
        }
    }
</script>

<style scoped>

</style>