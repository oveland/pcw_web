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
                                <i class="fa fa-user text-muted"></i><br> {{ $t('Responsible') }}<br>{{ $t('Takings') }}
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
                            <td class="text-center">{{ liquidation.totals.totalDispatch | thousandRound | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.totals.balance | thousandRound | numberFormat('$0,0') }}</td>
                            <td class="text-center text-bold">
                                <a class="link" @click="seeLiquidationDetail(liquidation.id, true)" data-toggle="modal" data-target="#modal-takings-liquidated-marks">
                                    {{ liquidation.marks.length }} turns
                                </a>
                            </td>
                            <td class="text-center">
                                {{ liquidation.liquidationUser.name }}<br>
                                <small><i class="fa fa-calendar"></i> {{ liquidation.liquidationDate }}</small>
                            </td>
                            <td class="text-center">
                                {{ liquidation.takingUser.name }}<br>
                                <small><i class="fa fa-calendar"></i> {{ liquidation.takingDate }}</small>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" :title="$t('Details')" @click="seeLiquidationDetail(liquidation.id)" data-toggle="modal" data-target="#modal-takings-liquidated-marks">
                                    <i class="fa fa-eye"></i>
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
                        <div class="portlet-title tabbable-line hide">
                            <div class="caption">
                                <i class="icon-layers"></i>
                                <span class="caption-subject font-dark bold uppercase">{{ $t('Taking details') }}</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li>
                                    <a href="#takings-detail" data-toggle="tab"> {{ $t('Takings') }} </a>
                                </li>
                                <li class="active">
                                    <a href="#detail-marks-taken" data-toggle="tab"> {{ $t('Turns') }} LM </a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body p-b-0 m-0">
                            <div class="tab-content row">
                                <div id="takings-detail" class="tab-pane fade">
                                    <div class="">
                                        <div class="portlet-title hide">
                                            <div class="caption">
                                                <i class="fa fa-dollar font-green"></i>
                                                <span class="caption-subject font-green bold uppercase">
                                                    {{ $t('Liquidation details') }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="portlet-body p-0">

											<header-steps module="history"></header-steps>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tab-content">
                                                        <div id="step-discounts-history" class="tab-pane fade in active">
                                                            <div class="table-responsive phase-container col-md-12">
                                                                <discount-component :control="control" :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></discount-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-penalties-history" class="tab-pane fade">
                                                            <div class="table-responsive phase-container col-md-12">
                                                                <penalty-component :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></penalty-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-commissions-history" class="tab-pane fade">
                                                            <div class="table-responsive phase-container col-md-12">
                                                                <commission-component :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation"></commission-component>
                                                            </div>
                                                        </div>
                                                        <div id="step-liquidate-history" class="tab-pane fade">
                                                            <div class="table-responsive phase-container col-md-12">
                                                                <summary-component :url-export="urlExport.replace('ID', liquidationDetail.id)" :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals" :liquidation="liquidationDetail.liquidation" :search="search"></summary-component>

																<div class="col-md-10 col-md-offset-1">
																	<div class="col-md-12 p-0" v-if="liquidationDetail.liquidation.realTaken > 0">
																		<div class="col-md-3">
																			<label for="real-taken">{{ $t('Real taken') }}</label>
																			<div class="input-icon">
																				<i class="fa fa-dollar font-green"></i>
																				<input id="real-taken" type="number" class="form-control input-other-discount disabled" disabled v-model="liquidationDetail.liquidation.realTaken">
																			</div>
																		</div>
																		<div class="col-md-3" v-if="advances.takings">
																			<label for="advance-summary">{{ $t('Advance') }}</label>
																			<div class="input-group">
																				<span style="position: absolute; z-index: 100; top: 8px; left: 10px;">{{ advances.takings | numberFormat('$0,0') }}</span>
																				<input type="number" disabled="disabled" class="form-control" id="advance-summary" style="color: white;caret-color: red;padding-left: 25px">
																			</div>
																		</div>
																		<div class="col-md-3">
																			<label for="previous-balance">{{ $t('Previous balance') }}</label>
																			<div class="input-icon input-group">
																				<i class="fa fa-dollar font-green" style="z-index: 3 !important;"></i>
																				<input id="previous-balance" disabled type="number" class="form-control input-other-discount disabled" :class=" liquidationDetail.liquidation.forgivableBalance ? 'forgivable' : ''" :value="previousBalance">
																				<span class="input-group-addon bg-green-meadow tooltips" :data-title="$t('Condoned')"
																					  v-if="liquidationDetail.liquidation.forgivableBalance && previousBalance > 0">
																				<i class="icon-present" style="color: white"></i>
																			</span>
																			</div>
																		</div>
																		<div class="col-md-3">
																			<label for="pending-balance">{{ $t('New balance') }}</label>
																			<div class="input-icon">
																				<i class="fa fa-dollar font-green"></i>
																				<input id="pending-balance" disabled type="number" class="form-control input-other-discount disabled" :value="pendingBalance">
																			</div>
																		</div>
																	</div>

																	<div class="col-md-12 p-t-15">
																		<label for="observations" class="control-label">{{ $t('Observations') }}</label>
																		<textarea id="observations" rows="2" class="form-control disabled" disabled v-model="liquidationDetail.liquidation.observations" style="resize: vertical;min-height: 30px !important;"></textarea>
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
                                <div id="detail-marks-taken" class="tab-pane fade active in">
                                    <div class="table-responsive">
                                        <table-component :readonly="true" :marks="liquidationDetail.marks" :totals="liquidationDetail.totals"></table-component>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-show="!liquidations.length" class="row">
			<div class="alert alert-warning alert-bordered m-b-10 mb-10 mt-10 col-md-4 col-md-offset-4 offset-md-4">
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
	import HeaderSteps from "./liquidation/HeaderSteps";

	export default {
        name: 'TakingsListComponent',
        props: {
            urlList: String,
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
                this.searchTakingListReport();
            },
        },
		computed: {
			previousBalance() {
				let prevLiquidation = this.liquidationDetail.prevLiquidation;
				return prevLiquidation ? prevLiquidation.pendingBalance : 0;
			},
			pendingBalance() {
				return this.liquidationDetail.liquidation.pendingBalance;
			},
			advances() {
				const advances = this.liquidationDetail.liquidation.advances;
				return advances ? advances : {};
			}
		},
        methods: {
            exportLiquidation(all){
                this.showPrintArea = true;
                return this.linkToPrintLiquidation = this.urlExport + '?id=' + this.liquidationDetail.id + (all ? '&all=true' : '');
            },
            seeLiquidationDetail(liquidationId, showMarksFirst) {
                this.liquidationDetail = _.find(this.liquidations, function(liquidation){
                    return liquidation.id === liquidationId
                });

                showMarksFirst ? $('a[href="#detail-marks-taken"]').tab('show') : $('a[href="#takings-detail"]').tab('show');
                setTimeout(() => {
                    $('.tooltips').tooltip();
                    setTimeout(() => {
                        $('.tooltips').tooltip();
                    }, 4000);
                }, 1000);
            },
            searchTakingListReport: function () {
                if (this.searchParams.valid) {
                    axios.get(this.urlList, {params: this.searchParams}).then(response => {
                        this.liquidations = response.data;
                    }).catch(function (error) {
                        console.log(error);
                    }).then(function () {
                    });
                }
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
            },
			thousandRound(value) {
				const absValue = Math.abs(value);

				return (value < 0 ? -1 : 1) * Math.round(absValue / 1000) * 1000;
			},
        },
        components: {
            TableComponent,
            DiscountComponent,
            CommissionComponent,
            PenaltyComponent,
            SummaryComponent,
            VueFriendlyIframe,
			HeaderSteps
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