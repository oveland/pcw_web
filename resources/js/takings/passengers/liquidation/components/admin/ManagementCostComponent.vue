<template>
    <div class="row" style="min-height: 400px">
        <div class="col-md-3 col-sm-12 col-xs-12">
            <div class="col-md-12" v-if="vehicles">
                <multiselect v-model="vehicle" :placeholder="$t('Select a vehicle')" label="number" track-by="id" :options="vehicles"></multiselect>
            </div>
        </div>
        <div class="col-md-9 col-sm-12 col-xs-12">
            <div class="tab-content">
                <div class="">
                    <div class="">
                        <div class="">
                            <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                                <thead>
                                <tr class="inverse">
                                    <th class="col-md-1">
                                        <i class="fa fa-list-ol text-muted"></i><br>
                                    </th>
                                    <th class="col-md-2">
                                        <i class="fa fa-car text-muted"></i><br> {{ $t('Vehicle') }}
                                    </th>
                                    <th class="col-md-2">
                                        <i class="icon-tag text-muted"></i><br> {{ $t('Name') }}
                                    </th>
                                    <th class="col-md-2">
                                        <i class="fa fa-dollar text-muted"></i><br> {{ $t('Value') }}
                                    </th>
                                    <th class="col-md-2">
                                        <i class="fa fa-rocket text-muted"></i><br> {{ $t('Options') }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-if="vehicle" class="" v-for="(cost, indexCost) in costsFor(vehicle)">
                                    <td class="text-center">{{ indexCost + 1 }}</td>
                                    <td class="text-center">{{ vehicle.number }}</td>
                                    <td class="text-center">{{ cost.name | capitalize }}</td>
                                    <td class="text-center">
                                        {{ cost.value | numberFormat('$0,0') }}
                                    </td>
                                    <td class="text-center">
                                        <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Edit')" @click="editCost(cost)"
                                                data-toggle="modal" data-target="#modal-management-costs-edit">
                                            <i class="fa fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-management-costs-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="saveCost()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="editingCost">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-name" type="text" class="form-control" :placeholder="$t('Name')" autofocus v-model="editingCost.name">
                                        <label for="edit-cost-name">{{ $t('Name') }}</label>
                                        <i class="fa fa-tag"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-description" type="text" class="form-control" :placeholder="$t('Description')" autofocus v-model="editingCost.description">
                                        <label for="edit-cost-description">{{ $t('Description') }}</label>
                                        <i class="fa fa-tags"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-value" type="text" class="form-control" :placeholder="$t('Value')" autofocus v-model="editingCost.value">
                                        <label for="edit-cost-value">{{ $t('Value') }}</label>
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12 text-center">
                        <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Cancel')" onclick="$('#modal-management-costs-edit').modal('hide')">
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
        name: "ManagementCostComponent",
        props: {
            vehicles: Array,
            vehicleSelected: Object,
            managementCosts: Array,
        },
        data: function () {
            return {
                vehicle: Object,
                editingCost: Object,
                editing: false,
            }
        },
        watch: {
            vehicleSelected: function () {
                this.vehicle = this.vehicleSelected;
            }
        },
        mounted() {

        },
        methods: {
            costsFor(vehicle){
                if (!vehicle) return this.managementCosts;

                return _.filter(this.managementCosts, {
                    'vehicle_id': vehicle.id,
                });
            },
            editCost: function(cost){
                this.editingCost = cost;
            },
            saveCost: function(){
                App.blockUI({target: '#management-costs-tab', animate: true});
                axios.post('parametros/costos/guardar', {
                    cost: this.editingCost
                }).then(r => {
                    this.editing = false;
                    if(r.data.error){
                        gerror(r.data.message);
                    }else{
                        gsuccess(r.data.message);
                        //this.$emit('refresh-report');
                        $('#modal-management-costs-edit').modal('hide');
                    }
                }).catch(function (error) {
                    console.log(error);
                    gerror("An error occurred in the process. Please contact your admin");
                }).then(function () {
                    App.unblockUI('#management-costs-tab');
                });
            }
        },
        components: {
            Multiselect
        },
    }
</script>

<style scoped>

</style>