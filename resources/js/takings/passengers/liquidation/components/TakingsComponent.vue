<template>
    <div class="">
        <div class="">
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
                                <i class="fa fa-user text-muted"></i><br> {{ $t('Responsible') }}
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-calendar text-muted"></i><br> {{ $t('Liquidated on') }}
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
                                <a class="link" @click="seeLiquidationDetail(liquidation.id)" data-toggle="modal" data-target="#modal-takings-liquidated-marks">
                                    {{ liquidation.marks.length }} turns
                                </a>
                            </td>

                            <td class="text-center hide">
                                <pre class="language-json">
                                    <code>{{ liquidation.totals }}</code>
                                </pre>
                            </td>
                            <td class="text-center">{{ liquidation.user.name }}</td>
                            <td class="text-center">{{ liquidation.dateLiquidation }}</td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" :title="$t('Details')" @click="seeLiquidationDetail(liquidation.id)" data-toggle="modal" data-target="#modal-takings-liquidated-marks">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-tab btn-transparent yellow-crusta btn-outline btn-circle tooltips" :title="$t('Take liquidation')" onclick="ginfo('Feature on development')">
                                    <i class="fa fa-suitcase"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-takings-liquidated-marks" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption">
                                <i class="icon-layers"></i>
                                <span class="caption-subject font-dark bold uppercase">{{ $t('Liquidation details') }}</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#detail-marks" data-toggle="tab"> {{ $t('Turnos') }} </a>
                                </li>
                                <li>
                                    <a href="#detail-liquidation" data-toggle="tab"> {{ $t('Liquidation') }} </a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content row">
                                <div id="detail-marks" class="tab-pane fade active in">
                                    <table-component :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals"></table-component>
                                </div>
                                <div id="detail-liquidation" class="tab-pane fade">
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
                                            <div class="mt-element-step">
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
                                                         href="#step-commissions-detail" data-active="active">
                                                        <div class="mt-step-number bg-white">
                                                            <i class=" icon-user-follow"></i>
                                                        </div>
                                                        <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Commissions') }}</div>
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
                                                    <div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab"
                                                         href="#step-liquidate-detail" data-active="done">
                                                        <div class="mt-step-number bg-white">
                                                            <i class="icon-calculator"></i>
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
                                                                <discount-component :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></discount-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-commissions-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <commission-component :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></commission-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-penalties-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-12 m-t-10">
                                                                <penalty-component :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></penalty-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-liquidate-detail" class="tab-pane fade">
                                                            <div class=" phase-container col-md-6 col-md-offset-3 m-t-10">
                                                                <div v-if="!showPrintArea" class="text-center col-md-12">
                                                                    <span class="header-preview">#{{ liquidationDetail.id }}</span>
                                                                </div>
                                                                <div class="text-center col-md-12">
                                                                    <a href="javascript:" v-if="!showPrintArea" target="" class="btn btn-default btn-circle btn-sm" @click="exportLiquidation()">
                                                                        <i class="fa fa-print"></i> | {{ $t('Print') }}
                                                                    </a>

                                                                    <a href="javascript:" v-if="showPrintArea" target="" class="btn btn-default btn-circle btn-sm" @click="showPrintArea = false">
                                                                        <i class="fa fa-undo"></i>
                                                                    </a>

                                                                    <a href="javascript:" target="" class="header-preview hide" @click="exportLiquidation(true)">
                                                                        <i class="fa fa-download"></i> {{ $t('Print detailed') }}
                                                                    </a>
                                                                </div>
                                                                <preview-component v-if="!showPrintArea" :liquidation="liquidationDetail.liquidation" :totals="liquidationDetail.totals" :search="search" :readonly="true"></preview-component>
                                                            </div>
                                                            <div class="phase-container col-md-8 col-md-offset-2 p-0 m-t-10 pdf-container" v-if="showPrintArea">
                                                                <vue-friendly-iframe :src="linkToPrintLiquidation"></vue-friendly-iframe>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
    </div>
</template>

<script>
    import DiscountComponent from './DiscountComponent.vue';
    import CommissionComponent from './CommissionComponent';
    import PenaltyComponent from './PenaltyComponent';
    import PreviewComponent from './PreviewComponent';
    import VueFriendlyIframe from 'vue-friendly-iframe';
    import TableComponent from "./TableComponent";


    export default {
        name: 'TakingsComponent',
        props: {
            urlList: String,
            urlTakings: String,
            urlExport: String,
            searchParams: Object,
            search: Object
        },
        data: function () {
            return {
                showPrintArea: false,
                linkToPrintLiquidation: false,
                liquidations: [],
                liquidationDetail: {
                    id: 0,
                    vehicle: {},
                    date: '',
                    liquidation: {},
                    totals: {},
                    user: {},
                    marks: [],
                },
            };
        },
        watch: {
            searchParams: function () {
                this.showPrintArea = false;
                this.linkToPrintLiquidation = '';
                this.searchLiquidationReport();
            }
        },
        methods: {
            exportLiquidation(all){
                this.showPrintArea = true;
                return this.linkToPrintLiquidation = this.urlExport + '?id=' + this.liquidationDetail.id + (all ? '&all=true' : '');
            },
            seeLiquidationDetail(liquidationId) {
                this.liquidationDetail = _.find(this.liquidations, function(liquidation){
                    return liquidation.id === liquidationId
                });

                $('a[href="#detail-marks"]').tab('show');
                setTimeout(() => {
                    $('.tooltips').tooltip();
                    setTimeout(() => {
                        $('.tooltips').tooltip();
                    }, 4000);
                }, 1000);
            },
            searchLiquidationReport: function () {
                axios.get(this.urlList, {params: this.searchParams}).then(response => {
                    this.liquidations = response.data;
                }).catch(function (error) {
                    console.log(error);
                }).then(function () {
                });
            },
            takings: function () {
                axios.post(this.urlTakings, {
                    vehicle: this.search.vehicle.id,
                    liquidation: this.liquidation,
                    totals: this.totals,
                    marks: _.map(this.marks, 'id'),
                }).then(response => {
                    const data = response.data;
                    if (data.success) {
                        gsuccess(data.message);
                        this.$emit('refresh-report');
                        $('#modal-generate-liquidation').modal('hide');
                    } else {
                        gerror(data.message);
                    }
                }).catch(function (error) {
                    gerror('Error in liquidation process!');
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
            PreviewComponent,
            VueFriendlyIframe
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