<template>
    <div class="">
        <div v-if="liquidations.length">

            <div class="row" v-if="report">
                <div class="table-responsive phase-container col-md-12 m-t-10">
                    <daily-table-component :readonly="true" :marks="report.marks" :totals="report.totals" :liquidation="report.details"></daily-table-component>

                    <hr>
                    <button class="btn btn-default btn-sm pull-right" @click="exportReport">
                        <i class="fa fa-print"></i> {{ $t('Print') }}
                    </button>
                </div>
            </div>


            <modal name="modal-daily-report-print" draggable="true" classes="vue-modal" width="90%" style="z-index: 1000">
                <div class="modal-header" style="margin-top: 30px">
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
    import VModal from 'vue-js-modal';
    import VueFriendlyIframe from 'vue-friendly-iframe';
    import DailyTableComponent from './DailyTableComponent';

    Vue.use(VModal);


    export default {
        name: 'DailyReportComponent',
        props: {
            urlList: String,
            urlReport: String,
            urlTakings: String,
            urlExport: String,
            searchParams: Object,
            search: Object
        },
        data: function () {
            return {
                report: {
                    marks: [],
                    details: [],
                    totals: {},
                },
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
                this.linkToPrintLiquidation = '';
                this.searchReport();
            }
        },
        methods: {
            exportReport: function() {
                this.linkToPrintLiquidation = this.urlExport + ('?date=' + this.searchParams.date + '&vehicle='+this.searchParams.vehicle);
                this.$modal.show('modal-daily-report-print');
            },
            closeExporter: function () {
                this.$modal.hide('modal-daily-report-print');
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
            searchReport: function () {
                if (this.searchParams.valid) {
                    this.liquidations = [];
                    this.report = {};

                    axios.get(this.urlReport, {params: this.searchParams}).then(response => {
                        const report = response.data;

                        if(!report.empty){
                            this.liquidations = report.liquidations;
                            this.report = {
                                marks: report.marks,
                                details: report.details,
                                totals: report.totals,
                            };
                        }
                    }).catch(function (error) {
                        console.log(error);
                    }).then(function () {
                    });
                }
            }
        },
        components: {
            DailyTableComponent,
            VueFriendlyIframe
        },
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