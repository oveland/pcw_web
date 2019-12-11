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
                            <th class="col-md-1">
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total turn') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Subtotal') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total dispatch') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Balance') }}
                            </th>
                            <th v-if="readonly" class="col-md-1">
                                <i class="fa fa-print text-muted"></i><br> {{ $t('Print') }}
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
                            <td class="text-center">{{ mark.passengersBEA }}</td>
                            <td class="text-center">{{ turn.totalTurn | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.subTotalTurn | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.totalDispatch | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ turn.balance | numberFormat('$0,0') }}</td>
                            <td v-if="readonly" class="text-center">
                                <button class="btn btn-sm btn-tab btn-transparent btn-outline btn-circle tooltips" :title="$t('Print')" @click="exportLiquidation(mark)" data-toggle="modal" data-target="#">
                                    <i class="fa fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-right">
                                <i class="icon-layers"></i> {{ $t('Total') }}
                            </td>
                            <td class="text-center">{{ totals.totalPassengersBea }}</td>
                            <td class="text-center">{{ totals.totalTurns | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.subTotalTurns | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.totalDispatch | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ totals.balance | numberFormat('$0,0') }}</td>
                            <td v-if="readonly" class="text-center">
                                <button class="btn btn-sm yellow-crusta btn-tab btn-transparent btn-outline btn-circle tooltips" :title="$t('Print total')" @click="exportLiquidation()" data-toggle="modal" data-target="#">
                                    <i class="fa fa-print"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <modal name="modal-export-print" draggable="true" classes="vue-modal">
                <div class="modal-header">
                    <button type="button" class="close" @click="closeExporter" aria-hidden="true"></button>
                    <h5 class="modal-title">
                        <i class="fa fa-print"></i> {{ $t('Print') }}
                    </h5>
                </div>
                <div class="moal-body">
                    <div class="col-md-12 p-0 m-0 pdf-container">
                        <vue-friendly-iframe :src="linkToPrintLiquidation"></vue-friendly-iframe>
                    </div>
                </div>
            </modal>
        </div>



        <hr class="m-t-10 m-b-10">

        <div class="" style="font-size: 1.1em !important;">
            <label for="observations" class="control-label">{{ $t('Observations') }}</label>
            <textarea id="observations" :readonly="readonly" :disabled="readonly" rows="2" class="form-control" v-model="liquidation.observations" style="resize: vertical;min-height: 30px !important;"></textarea>
        </div>

        <div v-if="search.vehicle && false" class="form form-horizontal preview">
            <h3 class="search p-b-15">
            <span class="text-bold">
                <i class="fa fa-bus"></i> {{ search.vehicle.number }} | {{ search.vehicle.plate }}
            </span>
                <span class="pull-right">
                <i class="fa fa-calendar"></i> {{ search.date }}
            </span>
            </h3>
            <hr class="hr">
            <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total turns') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalTurns  | numberFormat('$0,0') }}</span>
            </h2>

            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total pay fall') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalPayFall  | numberFormat('$0,0') }}</span>
            </h3>
            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total get fall') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalGetFall  | numberFormat('$0,0') }}</span>
            </h3>
            <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Subtotal') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.subTotalTurns  | numberFormat('$0,0') }}</span>
            </h2>

            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total tolls') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByTolls | numberFormat('$0,0') }}</span>
            </h3>
            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total commissions') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalCommissions | numberFormat('$0,0') }}</span>
            </h3>
            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Operative Expenses') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByOperativeExpenses | numberFormat('$0,0') }}</span>
            </h3>
            <h3 class="totals" v-if="totals.totalOtherDiscounts">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total other discounts') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalOtherDiscounts | numberFormat('$0,0') }}</span>
            </h3>

            <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total dispatch') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalDispatch | numberFormat('$0,0') }}</span>
            </h2>

            <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total fuel') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByFuel | numberFormat('$0,0') }}</span>
            </h3>

            <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Balance') }}
            </span>
                <span class="pull-right col-md-4 p-0">{{ totals.balance | numberFormat('$0,0') }}</span>
            </h2>
        </div>
    </div>
</template>

<script>
    import VModal from 'vue-js-modal';
    import VueFriendlyIframe from 'vue-friendly-iframe';
    import TakingsTurnsComponent from "./TakingsTurnsComponent";

    Vue.use(VModal);

    export default {
        name: "SummaryComponent",
        components: {
            TakingsTurnsComponent,
            VueFriendlyIframe
        },
        props: {
            search:Object,
            liquidation: Object,
            urlExport: String,
            totals: Object,
            marks: Array,
            readonly: Boolean
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
            closeExporter: function () {
                console.log(this.exportLink);
                this.$modal.hide('modal-export-print');
            },
            exportLiquidation: function(mark) {
                this.linkToPrintLiquidation = this.urlExport + (mark ? '?mark=' + mark.id : '');
                this.$modal.show('modal-export-print');
            },
            serialize: function(obj, prefix) {
                let str = [], p;
                for (p in obj) {
                    if (obj.hasOwnProperty(p)) {
                        let k = prefix ? prefix + "[" + p + "]" : p,
                            v = obj[p];
                        str.push((v !== null && typeof v === "object") ?
                            this.serialize(v, k) :
                            encodeURIComponent(k) + "=" + encodeURIComponent(v));
                    }
                }
                return str.join("&");
            }
        }
    }
</script>

<style scoped>
    .search span {
        font-size: 1.6em !important;
    }

    h2.totals{
        border-bottom: 1px solid #c6c6c6;
        padding-bottom: 10px !important;
    }

    h2.totals span {
        font-size: 1.5em !important;
        font-weight: bold !important;
    }

    h2.totals span.pull-right{
        text-align: right;
    }

    h3.totals{
        padding-left: 20px;
        border-bottom: 1px solid rgba(236, 236, 236, 0.53);
        padding-bottom: 5px !important;
    }

    h3.totals span {
        font-size: 1.2em !important;
    }

    h3.totals span.pull-right{
        text-align: right;
    }

    .total-liquidation span {
        font-size: 1.6em !important;
    }
</style>