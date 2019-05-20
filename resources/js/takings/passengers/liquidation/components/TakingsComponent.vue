<template>
    <div class="">
        <div class="">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th class="col-md-1">
                                <i class="fa fa-calendar text-muted"></i><br> Date
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-users text-muted"></i><br> Passengers
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> Total BEA
                            </th>
                            <th class="col-md-1">
                                <i class="icon-tag text-muted"></i><br> Total Discounts
                            </th>
                            <th class="col-md-1">
                                <i class=" icon-user-follow text-muted"></i><br> Total Commissions
                            </th>
                            <th class="col-md-1">
                                <i class="icon-shield text-muted"></i><br> Total Penalties
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-dollar text-muted"></i><br> Total Liquidated
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-user text-muted"></i><br> Responsable
                            </th>
                            <th class="col-md-1">
                                <i class="fa fa-rocket text-muted"></i><br> Details
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="liquidation in liquidations">
                            <td class="text-center">{{ liquidation.date }}</td>
                            <td class="text-center">{{ liquidation.totals.totalPassengersBea }}</td>
                            <td class="text-center">{{ liquidation.liquidation.totalBea | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.liquidation.totalDiscounts | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.liquidation.totalCommissions | numberFormat('$0,0') }}</td>
                            <td class="text-center">{{ liquidation.liquidation.totalPenalties | numberFormat('$0,0') }}</td>
                            <td class="text-center text-bold">{{ liquidation.liquidation.total | numberFormat('$0,0') }}</td>

                            <td class="text-center hide">
                                <pre class="language-json">
                                    <code>{{ liquidation.totals }}</code>
                                </pre>
                            </td>
                            <td class="text-center">{{ liquidation.user.name }}</td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" title="Details" @click="seeLiquidationDetail(liquidation.id)" data-toggle="modal" data-target="#modal-takings-liquidated-marks">
                                    <i class="fa fa-eye"></i>
                                </button>
                                <button class="btn btn-tab btn-transparent yellow-crusta btn-outline btn-circle tooltips" title="Recaudar" onclick="ginfo('Feature on development')">
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
                    <div class="portlet light bordered">
                        <div class="portlet-title tabbable-line">
                            <div class="caption">
                                <i class="fa fa-dollar"></i>
                                <span class="caption-subject font-dark bold uppercase">Marks liquidated</span>
                            </div>
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#detail-marks" data-toggle="tab"> Marks </a>
                                </li>
                                <li>
                                    <a href="#detail-liquidation" data-toggle="tab"> Liquidation </a>
                                </li>
                            </ul>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content row">
                                <div id="detail-marks" class="tab-pane fade active in">
                                    <table-component :marks="liquidationDetail.marks" :to-takings="true" :totals="liquidationDetail.totals"></table-component>
                                </div>
                                <div id="detail-liquidation" class="tab-pane fade">
                                    <div class="portlet light bordered phase-container col-md-6 col-md-offset-3 m-t-10">
                                        <a :href="urlExportLink" target="_blank" class="pull-left">
                                            <i class="fa fa-download"></i> Export
                                        </a>
                                        <span class="pull-right">#{{ liquidationDetail.id }}</span>

                                        <preview-component :liquidation="liquidationDetail.liquidation" :search="search" :only-details="true" ></preview-component>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import TableComponent from './TableComponent';
    import PreviewComponent from './PreviewComponent';

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
        computed: {
            urlExportLink: function(){
                return this.urlExport + '?id=' + this.liquidationDetail.id;
            }
        },
        watch: {
            searchParams: function () {
                console.log("Refresh Takings");
                this.searchLiquidationReport();
            }
        },
        methods: {
            seeLiquidationDetail(liquidationId) {
                this.liquidationDetail = _.find(this.liquidations, function(liquidation){
                    return liquidation.id === liquidationId
                });

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
            PreviewComponent
        }
    }
</script>