<template>
    <div class="">
        <div class="table-responsive">
            <table-component :search.sync="search" :marks="marks" :totals="totals" v-on:start-liquidation="startLiquidation()"></table-component>

			<div v-if="!marks.length">
				<div class="alert alert-success alert-bordered m-b-10 mb-10 mt-10 col-md-4 col-md-offset-4 offset-md-4">
					<div class="col-md-2" style="padding-top: 10px">
						<i class="fa fa-3x fa-check"></i>
					</div>
					<div class="col-md-10">
						<span class="close pull-right" data-dismiss="alert">×</span>
						<div style="margin-top: 10px">
							<strong>{{ $t('No turns pending for liquidation') }}</strong>
						</div>
					</div>
				</div>
			</div>
        </div>

        <div class="modal fade" id="modal-generate-liquidation" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header hide">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h5 class="modal-title">
                            <i class="fa fa-dollar"></i> {{ $t('Generate liquidation') }}
                        </h5>
                    </div>
                    <div class="modal-body" style="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="">
                                    <div class="portlet-title hide">
                                        <div class="caption">
                                            <i class="fa fa-dollar font-green"></i>
                                            <span class="caption-subject font-green bold uppercase">
                                                {{ $t('Generate liquidation') }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="tabbable-custom hidden-lg hidden-md">
                                            <ul class="nav nav-tabs ">
                                                <li class="active">
                                                    <a href="#step-discounts" data-toggle="tab" aria-expanded="true"> <i class="icon-tag"></i> <span class="hidden-xs">{{ $t('Discounts') }}</span> </a>
                                                </li>
                                                <li class="">
                                                    <a href="#step-penalties" data-toggle="tab" aria-expanded="false"> <i class="icon-shield"></i> <span class="hidden-xs">{{ $t('Penalties') }}</span> </a>
                                                </li>
                                                <li class="">
                                                    <a href="#step-commissions" data-toggle="tab" aria-expanded="false"> <i class=" icon-user-follow"></i> <span class="hidden-xs">{{ $t('Commissions') }}</span> </a>
                                                </li>
                                                <li class="">
                                                    <a href="#step-liquidate" data-toggle="tab" aria-expanded="false"> <i class="icon-layers"></i> <span class="hidden-xs">{{ $t('Liquidation') }}</span> </a>
                                                </li>
                                            </ul>
                                        </div>

                                        <div class="mt-element-step hidden-sm hidden-xs">
                                            <div class="row step-line">
                                                <div class="mt-step-desc text-center hide">
                                                    <div class="font-dark bold uppercase">
                                                        {{ $t('Generate liquidation') }}
                                                    </div>
                                                    <div class="caption-desc font-grey-cascade">
                                                    </div>
                                                    <br/>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col first phase-inventory warning"
                                                     data-toggle="tab" href="#step-discounts" data-active="warning">
                                                    <div class="mt-step-number bg-white">
                                                        <i class="icon-tag"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Discounts') }}</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                     href="#step-penalties" data-active="error">
                                                    <div class="mt-step-number bg-white">
                                                        <i class="icon-shield"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Penalties') }}</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                     href="#step-commissions" data-active="active">
                                                    <div class="mt-step-number bg-white">
                                                        <i class=" icon-user-follow"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Commissions') }}</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab"
                                                     href="#step-liquidate" data-active="done">
                                                    <div class="mt-step-number bg-white">
                                                        <i class="icon-layers"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">{{ $t('Liquidate') }}</div>
                                                    <div class="mt-step-content font-grey-cascade"></div>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="tab-content">
                                                    <div id="step-discounts" class="tab-pane fade in active">
                                                        <div class="table-responsive phase-container col-md-12 m-t-10">
                                                            <discount-component :control.sync="control" :marks.sync="marks" :totals="totals" :liquidation.sync="liquidation"></discount-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-penalties" class="tab-pane fade">
                                                        <div class="table-responsive phase-container col-md-12 m-t-10">
                                                            <penalty-component :marks="marks" :totals="totals" :liquidation.sync="liquidation"></penalty-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-commissions" class="tab-pane fade">
                                                        <div class="table-responsive phase-container col-md-12 m-t-10">
                                                            <commission-component :marks.sync="marks" :totals="totals" :liquidation.sync="liquidation"></commission-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-liquidate" class="tab-pane fade">
                                                        <div class="table-responsive phase-container col-md-12 m-t-10">
                                                            <summary-component :url-export="urlExport" :marks="marks" :liquidation.sync="liquidation" :totals="totals" :search="search"></summary-component>

															<hr class="m-t-10 m-b-10">

															<div class="col-md-10 col-md-offset-1" v-if="liquidation.advances.takings">
																<div class="col-md-4">
																	<label for="advance-summary">{{ $t('Advance') }}</label>
																	<div class="input-group">
																		<span style="position: absolute; z-index: 100; top: 8px; left: 10px;">{{ liquidation.advances.takings | numberFormat('$0,0') }}</span>
																		<input type="number" disabled="disabled" class="form-control" id="advance-summary" style="color: white;caret-color: red;padding-left: 25px">
																	</div>
																</div>

																<div class="col-md-12">
																	<label for="observations" class="control-label">{{ $t('Observations') }}</label>
																	<textarea id="observations" rows="2" class="form-control" v-model="liquidation.observations" style="resize: vertical;min-height: 30px !important;"></textarea>
																</div>
															</div>

                                                            <div class="text-center col-md-12 col-sm-12 col-xs-12 m-10" v-show="!control.processing">
                                                                <button class="btn btn-circle yellow-crusta btn-outline f-s-13 uppercase" @click="liquidate" :disabled="totals.totalBea === 0">
                                                                    <i class="icon-layers"></i> {{ $t('Liquidate') }}
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TableComponent from './TableComponent';
    import DiscountComponent from './DiscountComponent.vue';
    import CommissionComponent from './CommissionComponent';
    import PenaltyComponent from './PenaltyComponent';
    import SummaryComponent from "./SummaryComponent";

    import Swal from 'sweetalert2/dist/sweetalert2.min'

    export default {
        name: 'LiquidationComponent',
        props: {
            urlLiquidate: String,
            urlExport: String,
            urlSetAdvance: String,
            urlGetAdvance: String,
            search: Object,
            marks: Array,
            totals: Object,
            liquidation: Object
        },
        data(){
          return {
              control: {
                  enableSaving: false,
                  processing: false,
                  canUpdate: false
              },
          }
        },
        components: {
            TableComponent,
            DiscountComponent,
            CommissionComponent,
            PenaltyComponent,
            SummaryComponent,
        },
        watch: {
            totals: function () {
                _.forEach(this.marks, (mark) => {
                    const commissionType = mark.commission.type;
                    switch (commissionType) {
                        case 'percent':
                            const getFall = Number.isInteger(parseInt(mark.getFall)) ? parseInt(mark.getFall) : 0;
                            const payFall = Number.isInteger(parseInt(mark.payFall)) ? parseInt(mark.payFall) : 0;
                            mark.commission.value = (mark.totalGrossBEA + mark.penalty.value + getFall - payFall) * (mark.commission.baseValue / 100);
                            break;
                    }
                });
            }
        },
        methods: {
			startLiquidation() {
				$('#modal-generate-liquidation').modal('show');

				this.marks[0].payFall = this.liquidation.advances.payFall;
				this.marks[0].getFall = this.liquidation.advances.getFall;

			},
            liquidate: function () {
                let payFalls = {};
                let getFalls = {};
                _.forEach(this.marks, (mark) => {
                    payFalls[mark.id] = mark.payFall;
                    getFalls[mark.id] = mark.getFall;
                });

                App.blockUI({target: '.preview', animate: true});

                Swal.fire({
                    title: this.$t('Processing'),
                    text: this.$t('Please wait'),
                    onBeforeOpen: () => {
                        Swal.showLoading();
                    },
                    heightAuto: true,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });

                this.control.processing = true;

                axios.post(this.urlLiquidate, {
                    vehicle: this.search.vehicle.id,
                    liquidation: this.liquidation,
                    totals: this.totals,
                    marks: _.mapValues(_.keyBy(this.marks, 'id'), 'discounts'),
                    falls: {
                        get: getFalls,
                        pay: payFalls,
                    }
                }).then(response => {
                    const data = response.data;
                    if( data.success ){
                        gsuccess(data.message);
                        setTimeout(()=>{
                            this.$emit('refresh-report');
                            $('#modal-generate-liquidation').modal('hide');
                        },500);
                    }else{
                        gerror(data.message);
                    }
                }).catch(function (error) {
                    gerror('Error in liquidation process!');
                    console.log(error);
                }).then(() => {
                    App.unblockUI('.preview');
                    Swal.close();
                    setTimeout(() => {
                        this.control.processing = false;
                    }, 500);
                });
            },
        }
    }
</script>