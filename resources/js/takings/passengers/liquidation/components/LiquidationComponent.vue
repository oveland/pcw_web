<template>
    <div class="">
        <table-component :marks="marks" :totals="totals"></table-component>

        <div class="modal fade" id="modal-generate-liquidation" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header hide">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h5 class="modal-title">
                            <i class="fa fa-dollar"></i> Generate liquidation
                        </h5>
                    </div>
                    <div class="modal-body" style="">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet light portlet-fit bordered">
                                    <div class="portlet-title hide">
                                        <div class="caption">
                                            <i class="fa fa-dollar font-green"></i>
                                            <span class="caption-subject font-green bold uppercase">
                                    Generate liquidation
                                </span>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="mt-element-step">
                                            <div class="row step-line">
                                                <div class="mt-step-desc text-center hide">
                                                    <div class="font-dark bold uppercase">
                                                        Generate liquidation
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
                                                    <div class="mt-step-title uppercase font-grey-cascade">Discounts</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                     href="#step-commissions" data-active="active">
                                                    <div class="mt-step-number bg-white">
                                                        <i class=" icon-user-follow"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">Commissions</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab"
                                                     href="#step-penalties" data-active="error">
                                                    <div class="mt-step-number bg-white">
                                                        <i class="icon-shield"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">Penalties</div>
                                                    <div class="mt-step-content font-grey-cascade hide"></div>
                                                </div>
                                                <div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab"
                                                     href="#step-liquidate" data-active="done">
                                                    <div class="mt-step-number bg-white">
                                                        <i class="icon-calculator"></i>
                                                    </div>
                                                    <div class="mt-step-title uppercase font-grey-cascade">Liquidate</div>
                                                    <div class="mt-step-content font-grey-cascade"></div>
                                                </div>
                                            </div>
                                            <hr/>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="tab-content">
                                                    <div id="step-discounts" class="tab-pane fade in active">
                                                        <div class="portlet light bordered phase-container col-md-12 m-t-10">
                                                            <discount-component :marks="marks" :totals="totals" :liquidation.sync="liquidation"></discount-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-commissions" class="tab-pane fade">
                                                        <div class="portlet light bordered phase-container col-md-12 m-t-10">
                                                            <commission-component :marks="marks" :totals="totals" :liquidation.sync="liquidation"></commission-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-penalties" class="tab-pane fade">
                                                        <div class="portlet light bordered phase-container col-md-12 m-t-10">
                                                            <penalty-component :marks="marks" :totals="totals" :liquidation.sync="liquidation"></penalty-component>
                                                        </div>
                                                    </div>
                                                    <div id="step-liquidate" class="tab-pane fade">
                                                        <div class="portlet light bordered phase-container col-md-6 col-md-offset-3 m-t-10">
                                                            <preview-component :liquidation.sync="liquidation" :search="search" v-on:liquidate="liquidate()"></preview-component>
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
                    <div class="modal-footer hide">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn green">Siguiente</button>
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
    import PreviewComponent from './PreviewComponent';

    export default {
        name: 'LiquidationComponent',
        props: {
            urlLiquidate: String,
            search: Object,
            marks: Array,
            totals: Object
        },
        data: function () {
            return {
                liquidation: {
                    otherDiscounts: [],
                    totalBea: 0,
                    totalGrossBea: 0,
                    totalDiscounts: 0,
                    totalDiscountsDetail: {},

                    totalCommissions: 0,

                    totalPenalties: 0,

                    total: 0,
                    observations : ""
                }
            };
        },
        components: {
            TableComponent,
            DiscountComponent,
            CommissionComponent,
            PenaltyComponent,
            PreviewComponent
        },
        watch: {
            totals: function () {
                this.liquidation.totalBea = this.totals.totalBea;
                this.liquidation.totalGrossBea = this.totals.totalGrossBea;
            }
        },
        methods: {
            liquidate: function () {
                App.blockUI({target: '.preview', animate: true});
                axios.post(this.urlLiquidate, {
                    vehicle: this.search.vehicle.id,
                    liquidation: this.liquidation,
                    totals: this.totals,
                    marks: _.map(this.marks, 'id'),
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
                }).then(function () {
                    App.unblockUI('.preview');
                });
            }
        }
    }
</script>