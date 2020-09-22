<template>
    <div class="row">
		<div class="col-md-12">
            <div class="tab-content">
                <div class="">
                    <div class="">
                        <ul class="nav nav-pills pull-left">
                            <li v-for="(route, indexRoute) in routes" :class="indexRoute === 0 ? 'active' : ''">
                                <a :href="'#tab-penalty-' + route.id" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-flag"></i> {{ route.name }}
                                </a>
                            </li>
                        </ul>

						<div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 pull-right p-0" v-if="vehicles">
							<multiselect v-model="vehicle" :placeholder="$t('Select a vehicle')" label="number" track-by="id" :options="vehicles">
								<template slot="singleLabel" slot-scope="props">
									<span class="option__desc">
										<span class="option__title">
											<i class="fa fa-bus"></i> {{ props.option.number }}
										</span>
									</span>
								</template>
								<template slot="option" slot-scope="props">
									<div class="option__desc">
										<span class="option__title">
											<i class="fa fa-bus"></i> {{ props.option.number }}
										</span>
									</div>
								</template>
							</multiselect>
						</div>

                        <div class="tab-content">
                            <div v-for="(route, indexRoute) in routes" class="tab-pane fade" :class="indexRoute === 0 ? 'active in' : ''" :id="'tab-penalty-' + route.id">
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
                                            <i class="icon-tag text-muted"></i><br> {{ $t('Type') }}
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
                                    <tr class="" v-for="(penalty, indexPenalty) in penaltiesFor(route.id)">
                                        <td class="text-center">{{ indexPenalty + 1 }}</td>
                                        <td class="text-center">{{ penalty.vehicle.number }}</td>
                                        <td class="text-center">{{ $t(penalty.type) | capitalize }}</td>
                                        <td class="text-center">
                                            {{ penalty.value | numberFormat('$0,0') }}
                                        </td>
                                        <td class="text-center">
                                            <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Edit')" @click="editPenalty(penalty)"
                                                    data-toggle="modal" data-target="#modal-admin-penalty-edit">
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
        </div>

        <div class="modal fade" id="modal-admin-penalty-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="savePenalty()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="editingPenalty">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <select id="edit-penalty-type" readonly type="text" class="form-control" v-model="editingPenalty.type">
                                            <option v-for="(type) in penaltyTypes" :value="type">{{ $t(type) | capitalize }}</option>
                                        </select>
                                        <label for="edit-penalty-type">{{ $t('Penalty type') }}</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-penalty-value" type="text" class="form-control" :placeholder="$t('Value')" autofocus v-model="editingPenalty.value">
                                        <label for="edit-penalty-value">{{ $t('Value') }}</label>
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12 text-center">
                        <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Cancel')" onclick="$('#modal-admin-penalty-edit').modal('hide')">
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
        name: "AdminPenaltyComponent",
        props: {
            vehicles: Array,
            vehicleSelected: Object,
            routes: Array,
            penalties: Array,
        },
        data: function () {
            return {
                vehicle: Object,
                penaltyTypes: Array,
                editingPenalty: Object,
                editing: false,
            }
        },
        watch: {
            vehicleSelected: function () {
                this.vehicle = this.vehicleSelected;
            }
        },
        mounted() {
            this.penaltyTypes = ['boarding'];
        },
        methods: {
            editPenalty: function(penalty){
                this.editingPenalty = penalty;
            },
            penaltiesFor(routeId) {
                let filter = {
                    'route_id': routeId,
                };

                if(this.vehicle)filter.vehicle_id = this.vehicle.id;
                
                return _.filter(this.penalties, filter);
            },
            savePenalty: function(){
                App.blockUI({target: '#penalties-params-tab', animate: true});
                axios.post('parametros/sanciones/guardar', {
                    penalty: this.editingPenalty
                }).then(r => {
                    this.editing = false;
                    if(r.data.error){
                        gerror(r.data.message);
                    }else{
                        gsuccess(r.data.message);
                        this.$emit('refresh-report');
                        $('#modal-admin-penalty-edit').modal('hide');
                    }
                }).catch(function (error) {
                    console.log(error);
                    gerror("An error occurred in the process. Please contact your admin");
                }).then(function () {
                    App.unblockUI('#penalties-params-tab');
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