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
                                <i class="fa fa-rocket text-muted"></i><br> {{ $t('Actions') }}
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
                                {{ liquidation.marks.length }} turns
                            </td>
                            <td class="text-center">
                                <button class="btn btn-tab btn-transparent green-sharp btn-outline btn-circle tooltips" :title="$t('Process charge')" @click="processCharge(liquidation.id)" data-toggle="modal" data-target="#modal-charge">
                                    <i class="fa fa-user-secret"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
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

        <div class="modal fade" id="modal-charge" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row " @submit.prevent="">
                    <div class="portlet light m-0">
                        <div class="portlet-title tabbable-line m-0">
                            <div class="caption  col-md-12">
                                <i class="fa fa-user-secret"></i>
                                <span class="caption-subject font-dark bold uppercase">
                                    {{ $t('Process charge') }}
                                </span>
                                <strong class="pull-right" style="font-size: 1.2em !important;">Total recaudado {{ liquidationCharge.totals.totalDispatch | numberFormat('$0,0') }}</strong>
                            </div>
                        </div>
                        <div class="row portlet-body">
                            <div class="col-md-12 text-left no-padding" v-if="liquidationCharge">
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
</template>

<script>
    import Multiselect from 'vue-multiselect';

    export default {
        name: 'RoadSafetyTakingsComponent',
        props: {
            urlList: String,
            urlCosts: String,
            urlTakings: String,
            searchParams: Object,
            search: Object
        },
        data: function () {
            return {
                control: {
                    enableSaving: false
                },
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
                costs: [],
                liquidations: [],
                liquidationCharge: {
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
                this.searchTakingListReport();
            }
        },
        computed: {
            totalCosts : function () {
                return _.sumBy(this.costs, 'value');
            }
        },
        methods: {
            processCharge(liquidationId, showMarksFirst) {
                this.liquidationCharge = _.find(this.liquidations, function(liquidation){
                    return liquidation.id === liquidationId
                });
            },
            searchTakingListReport: function () {
                if (this.searchParams.valid) {
                    axios.get(this.urlList, {params: this.searchParams}).then(response => {
                        this.liquidations = response.data;
                    }).catch(function (error) {
                        console.log(error);
                    }).then(function () {
                    });

                    axios.get(this.urlCosts, {params: this.searchParams}).then(response => {
                        this.costs = response.data;
                    }).catch(function (error) {
                        console.log(error);
                    }).then(function () {
                    });
                }
            },
            charge: function () {
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
            Multiselect
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