<template>
    <div class="">
        <div v-if="liquidations.length">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="col-md-1">
                                <i class="fa fa-calendar text-muted"></i><br> {{ $t('Date') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-users text-muted"></i><br> {{ $t('Passengers') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total turns') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> {{ $t('Subtotal') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-fa-dollar text-muted"></i><br> {{ $t('Total dispatch') }}
                            </th>
                            <th class="col-md-1">
                                <i class="icon-dollar text-muted"></i><br> {{ $t('Balance') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-retweet text-muted"></i><br> {{ $t('Turns liquidated') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-user text-muted"></i><br> {{ $t('Responsible') }}<br>{{ $t('Liquidation') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-rocket text-muted"></i><br> {{ $t('Details') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="liquidation in liquidations">
                            <td class="text-center">{{ liquidation.date }}</td>
                            <td class="text-center">{{ liquidation.totals.totalPassengersBea }}</td>
                            <td class="text-center">{{ liquidation.totals.totalTurns | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.totals.subTotalTurns | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.totals.totalDispatch | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.totals.balance | numberFormat('$0,0') }}</td>
                            <td class="text-center text-bold">
                                <a class="link" @click="seeLiquidation(liquidation, true)" data-toggle="modal" data-target="#modal-liquidation-detail">
                                    {{ liquidation.marks.length }} turns
                                </a>
                            </td>

                            <td class="text-center hide">
                                <pre class="language-json">
                                    <code>{{ liquidation.totals }}</code>
                                </pre>
                            </td>
                            <td class="text-center">
                                {{ liquidation.liquidationUser.name }}<br>
                                <small><i class="fa fa-calendar"></i> {{ liquidation.liquidationDate }}</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" :title="$t('Details')" @click="seeLiquidation(liquidation)" data-toggle="modal" data-target="#modal-liquidation-detail">
                                    <i class="fa fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-liquidation-detail" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption">
                                <i class="icon-layers"></i>
                                <span class="caption-subject font-dark bold uppercase">{{ $t('Liquidation details') }}</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li>
                                    <a href="#liquidation-detail" data-toggle="tab"> {{ $t('Liquidation') }} </a>
                                </li>
                                <li class="active">
                                    <a href="#detail-marks-liquidated" data-toggle="tab"> {{ $t('Turnos') }} BEA </a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content row">
                                <div id="liquidation-detail" class="tab-pane fade">
                                    <div class="">
                                        <div class="portlet-title hide">
                                            <div class="caption">
                                                <i class="fa fa-dollar font-green"></i>
                                                <span class="caption-subject font-green bold uppercase">
                                                    {{ $t('Liquidation details') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <div class="tabbable-custom hidden-lg hidden-md">
                                                <ul class="nav nav-tabs ">
                                                    <li class="active">
                                                        <a href="#step-discounts-detail" data-toggle="tab" aria-expanded="true"> <i class="icon-tag"></i> <span class="hidden-xs">{{ $t('Discounts') }}</span> </a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#step-penalties-detail" data-toggle="tab" aria-expanded="false"> <i class="icon-shield"></i> <span class="hidden-xs">{{ $t('Penalties') }}</span> </a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#step-commissions-detail" data-toggle="tab" aria-expanded="false"> <i class=" icon-user-follow"></i> <span class="hidden-xs">{{ $t('Commissions') }}</span> </a>
                                                    </li>
                                                    <li class="">
                                                        <a href="#step-liquidate-detail" data-toggle="tab" aria-expanded="false"> <i class="icon-layers"></i> <span class="hidden-xs">{{ $t('Liquidation') }}</span> </a>
                                                    </li>
                                                </ul>
                                            </div>

                                            <div class="mt-element-step hidden-sm hidden-xs">
                                                <div class="row step-line">
                                                    <div class="phases col-md-3 mt-step-col first phase-inventory warning"
                                                         data-toggle="tab" href="#step-discounts-detail" data-active="warning">
                                                        <div class="mt-step-number bg-white">
                                                            <i class="icon-tag"></i>
                                                        </div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Discounts') }}</div>
                                                        <div class="mt-step-content font-grey-cascade hide"></div>
                                                    </div>
                                                    <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                         href="#step-penalties-detail" data-active="error">
                                                        <div class="mt-step-number bg-white">
                                                            <i class="icon-shield"></i>
                                                        </div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Penalties') }}</div>
                                                        <div class="mt-step-content font-grey-cascade hide"></div>
                                                    </div>
                                                    <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                         href="#step-commissions-detail" data-active="active">
                                                        <div class="mt-step-number bg-white">
                                                            <i class=" icon-user-follow"></i>
                                                        </div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Commissions') }}</div>
                                                        <div class="mt-step-content font-grey-cascade hide"></div>
                                                    </div>
                                                    <div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab"
                                                         href="#step-liquidate-detail" data-active="done">
                                                        <div class="mt-step-number bg-white">
                                                            <i class="icon-layers"></i>
                                                        </div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Liquidation') }}</div>
                                                        <div class="mt-step-content font-grey-cascade"></div>
                                                    </div>
                                                </div>
                                                <hr/>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tab-content">
                                                        <div id="step-discounts-detail" class="tab-pane fade in active">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <discount-component :url-update-liquidate="urlUpdateLiquidate" :control.sync="control" :marks="liquidation.marks" :totals.sync="liquidation.totals" :liquidation.sync="liquidation.liquidation" v-on:update-liquidation="updateLiquidation"></discount-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-penalties-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <penalty-component :marks="liquidation.marks" :totals="totals" :liquidation="liquidation.liquidation"></penalty-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-commissions-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <commission-component :readonly="true" :marks="liquidation.marks" :totals="totals" :liquidation="liquidation.liquidation"></commission-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-liquidate-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <summary-component :url-export="urlExport.replace('ID', liquidation.id)" :readonly="true" :marks="liquidation.marks" :totals="totals" :liquidation.sync="liquidation.liquidation" :search="search"></summary-component>
                                                                <div class="text-center col-md-12 col-sm-12 col-xs-12 m-t-10">
                                                                    <button v-if="!control.enableSaving" class="btn btn-circle blue btn-outline f-s-13 uppercase" @click="takings()" :disabled="liquidation.taken">
                                                                        <i class="fa fa-suitcase"></i> {{ $t('Taking') }}
                                                                    </button>
                                                                    <button v-if="control.enableSaving" class="btn btn-circle green btn-outline f-s-13 uppercase" @click="updateLiquidation">
                                                                        <i class="fa fa-save"></i> {{ $t('Save') }}
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="detail-marks-liquidated" class="tab-pane fade active in">
                                    <div class="table-responsive">
                                        <table-component :readonly="true" :marks="liquidation.marks" :totals="totals"></table-component>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">{{ $t('Close') }}</button>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="!liquidations.length" class="row">
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
    import DiscountComponent from './DiscountComponent.vue';
    import CommissionComponent from './CommissionComponent';
    import PenaltyComponent from './PenaltyComponent';
    import SummaryComponent from "./SummaryComponent";
    import VueFriendlyIframe from 'vue-friendly-iframe';
    import TableComponent from "./TableComponent";


    export default {
        name: 'TakingsComponent',
        props: {
            urlList: String,
            urlUpdateLiquidate: String,
            urlTakings: String,
            urlExport: String,
            searchParams: Object,
            search: Object
        },
        data: function () {
            return {
                control: {
                    enableSaving: false
                },
                showPrintArea: false,
                linkToPrintLiquidation: false,
                liquidations: [],
                liquidation: {
                    id: 0,
                    vehicle: {},
                    date: '',
                    liquidation: {},
                    totals: {},
                    user: {},
                    marks: [],
                    taken: false
                },
            };
        },
        mounted(){
            this.control.enableSaving = false;
        },
        watch: {
            searchParams: function () {
                this.showPrintArea = false;
                this.linkToPrintLiquidation = '';
                this.searchTakingsReport();
            }
        },
        computed:{
            totals: function () {
                let totals = this.liquidation.totals;

                totals.totalOtherDiscounts = _.sumBy(this.liquidation.liquidation.otherDiscounts, function (other) {
                    return (Number.isInteger(other.value) ? other.value : 0);
                });

                const totalDiscounts = parseInt(totals.totalDiscountsByTurns) + parseInt(totals.totalOtherDiscounts);
                if(totalDiscounts !== totals.totalDiscounts){
                    this.control.enableSaving = true;
                }

                const totalDispatch = totals.totalTurns - ( totalDiscounts - totals.totalDiscountByFuel - totals.totalDiscountByMobilityAuxilio) - totals.totalCommissions;
                const balance = totalDispatch - totals.totalPayFall + totals.totalGetFall - totals.totalDiscountByFuel;

                totals.totalDiscounts = totalDiscounts;
                totals.totalDispatch = totalDispatch;
                totals.balance = balance;

                this.liquidation.totals = totals;
                return this.liquidation.totals;
            }
        },
        methods: {
            updateLiquidation: function () {
                App.blockUI({target: '.liquidation-detail', animate: true});
                axios.post(this.urlUpdateLiquidate.replace('ID', this.liquidation.id), {
                    liquidation: this.liquidation.liquidation,
                    totals: this.totals,
                }).then(response => {
                    const data = response.data;
                    if( data.success ){
                        this.control.enableSaving = false;
                        gsuccess(data.message);
                    }else{
                        gerror(data.message);
                    }
                }).catch(function (error) {
                    gerror('Error in liquidation process!');
                    console.log(error);
                }).then(function () {
                    App.unblockUI('.preview');
                });
            },
            seeLiquidation(liquidation, showMarksFirst) {
                this.control.enableSaving = false;
                this.liquidation = liquidation;

                showMarksFirst ? $('a[href="#detail-marks-liquidated"]').tab('show') : $('a[href="#liquidation-detail"]').tab('show');
                setTimeout(() => {
                    $('.tooltips').tooltip();
                    setTimeout(() => {
                        $('.tooltips').tooltip();
                    }, 4000);
                }, 1000);
            },
            searchTakingsReport: function () {
                axios.get(this.urlList, {params: this.searchParams}).then(response => {
                    this.liquidations = response.data;
                }).catch(function (error) {
                    console.log(error);
                }).then(function () {
                });
            },
            takings: function () {
                axios.post(this.urlTakings.replace('ID', this.liquidation.id)).then(response => {
                    const data = response.data;
                    if (data.success) {
                        gsuccess(data.message);
                        this.$emit('refresh-report');
                        $('#modal-liquidation-detail').modal('hide');
                    } else {
                        gerror(data.message);
                    }
                }).catch(function (error) {
                    gerror(this.$t('Error at generate taking register')+'!');
                    console.log(error);
                }).then(function () {

                });
            }
        },
        components: {
            TableComponent,
            DiscountComponent,
            CommissionComponent,
            PenaltyComponent,
            VueFriendlyIframe,
            SummaryComponent
        }
    }
</script>

<style>
    .pdf-container iframe{
        width: 100%;
        height: 600px;
    }
    .header-preview{
        font-size: 1.2em !important;
    }
</style>