<template>
    <div class="row" style="min-height: 400px">
        <div class="col-lg-8 col-lg-offset-2 col-md-10 col-md-offset-1 col-sm-12 col-xs-12">
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12" v-if="vehicles">
                <multiselect v-model="vehicle" :placeholder="$t('Select a vehicle')" label="number" track-by="id" :options="vehicles"></multiselect>
            </div>
            <div class="col-lg-9 col-md-8 col-sm-6 col-xs-12" v-if="!newCost.enable">
                <button class="btn btn-success btn-outline btn-sm btn-white sbold uppercase btn-circle pull-right tooltips" :title="$t('Create')" @click="createCost()">
                    <i class="fa fa-plus"></i>
                </button>
            </div>
            <hr class="col-md-12 col-sm-12 col-xs-12">
            <div class="table-responsive col-md-12">
                <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                    <thead>
                    <tr class="inverse">
                        <th class="col-md-1">
                            <i class="fa fa-list-ol text-muted"></i><br>
                        </th>
                        <th class="col-md-1">
                            <i class="fa fa-car text-muted"></i><br> {{ $t('Vehicle') }}
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-tag text-muted"></i><br> {{ $t('Name') }}
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-tags text-muted"></i><br> {{ $t('Concept') }}
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-dollar text-muted"></i><br> {{ $t('Value') }}
                        </th>
                        <th class="col-md-1">
                            <span class="tooltips" :data-title="$t('Defines the order in which the required payment should be applied')" data-placement="bottom">
                                <i class="fa fa-list-ol text-muted"></i><br> {{ $t('Priority') }}
                            </span>
                        </th>
                        <th class="col-md-1">
                            <i class="fa fa-check text-muted"></i><br> {{ $t('Status') }}
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-rocket text-muted"></i><br> {{ $t('Options') }}
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-if="vehicle && !newCost.enable" :class="cost.active ? '' : 'text-muted'" v-for="(cost, indexCost) in costsFor(vehicle)">
                        <td class="text-center">{{ indexCost + 1 }}</td>
                        <td class="text-center">{{ vehicle.number }}</td>
                        <td class="text-center">{{ cost.name | capitalize }}</td>
                        <td class="text-center">{{ cost.concept }}</td>
                        <td class="text-center">
                            {{ cost.value | numberFormat('$0,0') }}
                        </td>
                        <td class="text-center">{{ cost.priority }}</td>
                        <td class="text-center">
                            <i v-show="cost.active" class="fa fa-check-circle text-success"></i>
                            <i v-show="!cost.active" class="fa fa-check-circle text-muted"></i>
                        </td>
                        <td class="text-center" style="width: 15%">
                            <button class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Edit')" @click="editCost(cost)">
                                <i class="fa fa-edit"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
                    </tr>

                    <tr v-show="newCost.enable" id="form-new-cost">
                        <td class="text-center">*</td>
                        <td class="text-center">{{ vehicle.number }}</td>
                        <td class="text-center">
                            <input type="text" :placeholder="$t('Name')" class="input-sm form-control field-required" v-model="newCost.name">
                        </td>
                        <td class="text-center">
                            <input type="text" :placeholder="$t('Concept')" class="input-sm form-control field-required" v-model="newCost.concept">
                        </td>
                        <td class="text-center">
                            <input type="number" :placeholder="$t('Value')" class="input-sm form-control field-required" v-model="newCost.value">
                        </td>
                        <td class="text-center">
                            <input type="number" :placeholder="$t('Priority')" class="input-sm form-control field-required" v-model="newCost.priority">
                        </td>
                        <td class="text-center">
                            <button v-show="newCost.active" class="btn btn-sm btn-success btn-outline sbold uppercase btn-circle tooltips" :title="$t('Click for inactivate')" @click="inactivateCost()">
                                <i class="fa fa-check-circle"></i>
                            </button>
                            <button v-show="!newCost.active" class="btn btn-sm btn-grey btn-outline sbold uppercase btn-circle tooltips" :title="$t('Click for activate')" @click="activateCost()">
                                <i class="fa fa-check-circle-o"></i>
                            </button>
                        </td>
                        <td class="text-center" style="width: 15%">
                            <button class="btn btn-sm btn-outline sbold uppercase btn-circle tooltips" :title="$t('Cancel')" @click="cancelNewCost()">
                                <i class="fa fa-times"></i>
                            </button>
                            <button class="btn btn-sm btn-outline sbold uppercase btn-circle tooltips" :class="newCost.create ? 'btn-success' : 'btn-primary'" :data-title="$t('Save')" @click="saveCost()">
                                <i class="fa fa-save"></i>
                            </button>

                            <div v-show="!newCost.create" class="p-l-10" style="display: inline">
                                <button v-show="!newCost.global" class="btn btn-sm btn-danger btn-outline sbold uppercase btn-circle tooltips" :title="$t('Delete')" @click="deleteCost()">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="modal-management-costs-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="saveCost()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="newCost">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-name" type="text" class="form-control" :placeholder="$t('Name')" autofocus v-model="newCost.name">
                                        <label for="edit-cost-name">{{ $t('Name') }}</label>
                                        <i class="fa fa-tag"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-concept" type="text" class="form-control" :placeholder="$t('Concept')" autofocus v-model="newCost.concept">
                                        <label for="edit-cost-concept">{{ $t('Concept') }}</label>
                                        <i class="fa fa-tags"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-cost-value" type="text" class="form-control" :placeholder="$t('Value')" autofocus v-model="newCost.value">
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
    import Swal from 'sweetalert2/dist/sweetalert2.min'

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
                newCost: Object,
            }
        },
        watch: {
            vehicleSelected: function () {
                this.vehicle = this.vehicleSelected;
            }
        },
        mounted() {
            this.newCost = {
                enable: false
            };
        },
        methods: {
            costsFor(vehicle){
                if (!vehicle) return this.managementCosts;

                return _.orderBy(_.filter(this.managementCosts, {
                    'vehicle_id': vehicle.id,
                }),['active', 'priority'], ['desc', 'asc']);
            },
            createCost: function(){
                this.newCost = {
                    enable: true,
                    create : true,
                    vehicleId: this.vehicle.id,
                    name: '',
                    concept: '',
                    value: '',
                    active: true,
                    priority: this.costsFor(this.vehicle).length + 1,
                };
            },
            editCost: function(cost){
                this.newCost = cost;
                this.newCost.enable = true;
            },
            cancelNewCost: function(){
                this.newCost = {
                    enable: false
                }
                this.$emit('refresh-report');
            },
            deleteCost: function(confirm){
                App.blockUI({target: '#management-costs-tab', animate: true});
                axios.post('parametros/costos/eliminar', {
                    cost: this.newCost
                }).then(r => {
                    if(r.data.error){
                        Swal.fire({
                            title: 'Error!',
                            html: r.data.message,
                            icon: 'error',
                            timer: 8000,
                            timerProgressBar: true,
                        });
                    }else{
                        gsuccess(r.data.message);

                        this.newCost = {
                            enable: false
                        }
                        this.$emit('refresh-report');
                    }
                }).catch(function (error) {
                    App.unblockUI('#management-costs-tab');
                    gerror(this.$t('An error occurred in the process. Please contact your admin'));
                }).then( () => {
                    App.unblockUI('#management-costs-tab');
                });
            },
            inactivateCost: function() {
                this.newCost.active = false;
                this.saveCost();
            },
            activateCost: function() {
                this.newCost.active = true;
                this.saveCost();
            },
            saveCost: function(){
                App.blockUI({target: '#management-costs-tab', animate: true});
                axios.post('parametros/costos/guardar', {
                    cost: this.newCost
                }).then(r => {
                    if(r.data.error){
                        Swal.fire({
                            title: 'Error!',
                            html: r.data.message,
                            icon: 'error',
                            timer: 8000,
                            timerProgressBar: true,
                        });
                    }else{
                        gsuccess(r.data.message);

                        this.newCost = {
                            enable: false
                        }
                        this.$emit('refresh-report');
                    }
                }).catch(function (error) {
                    App.unblockUI('#management-costs-tab');
                    gerror(this.$t('An error occurred in the process. Please contact your admin'));
                }).then( () => {
                    App.unblockUI('#management-costs-tab');
                });
            },
            info: function(message){
                ginfo(message);
            }
        },
        components: {
            Multiselect
        },
    }
</script>

<style scoped>

</style>