<template>
    <div class="row">
        <div class="col-md-3 col-sm-4 col-xs-12">
            <div class="col-md-12" v-if="vehicles">
                <multiselect v-model="vehicle" :placeholder="$t('Select a vehicle')" label="number" track-by="id" :options="vehicles"></multiselect>
            </div>
        </div>
        <div class="col-md-9 col-sm-8 col-xs-12" v-if="vehicle">
            <div class="tab-content">
                <div class="tab-pane fade active in" :id="'vehicle-'+vehicle.id">
                    <div class="">
                        <ul class="nav nav-pills">
                            <li v-for="(route, indexRoute) in routes" :class="indexRoute === 0 ? 'active' : ''" @click="loadTrajectories(route)">
                                <a :href="'#tab-' + vehicle.id" data-toggle="tab" aria-expanded="true" @click="discounts = []">
                                    <i class="fa fa-flag"></i> {{ route.name }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane fade active in" :id="'tab-' + vehicle.id">
                                <table class="table table-bordered table-condensed table-report">
                                    <thead>
                                    <tr class="inverse">
                                        <th class="col-md-4">
                                            <i class="fa fa-flag text-muted"></i><br> {{ $t('Trajectory') }}
                                        </th>
                                        <th class="col-md-8">
                                            <i class="icon-tag text-muted"></i><br> {{ $t('Discounts') }}
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-center col-md-4">
                                            <ul class="nav nav-tabs tabs-left">
                                                <li v-for="(trajectory, indexTrajectory) in trajectoriesByRoute" :class="trajectory.id === selectedTrajectory.id ? 'active' : ''" @click="loadDiscounts(vehicle, trajectory)">
                                                    <a href=".tab-discounts" data-toggle="tab">
                                                        {{ trajectory.name }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="discounts text-center col-md-8">
                                            <div class="tab-content">
                                                <div class="tab-discounts tab-pane fade active in">
                                                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                                                        <thead>
                                                        <tr class="inverse">
                                                            <th class="col-md-1">
                                                                <i class="fa fa-list-ol text-muted"></i><br>
                                                            </th>
                                                            <th class="col-md-2">
                                                                <i class="icon-tag text-muted"></i><br> {{ $t('Name') }}
                                                            </th>
                                                            <th class="col-md-2">
                                                                <i class="icon-tag text-muted"></i><br> {{ $t('Description') }}
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
                                                        <tr v-for="(discount, indexDiscount) in discounts">
                                                            <td class="text-center">{{ indexDiscount + 1 }}</td>
                                                            <td class="text-center">
                                                                <i :class="discount.discount_type.icon"></i> {{ discount.discount_type.name | capitalize }}
                                                            </td>
                                                            <td class="text-center">{{ discount.discount_type.description }}</td>
                                                            <td class="text-center">
                                                                {{ discount.value | numberFormat('$0,0') }}
                                                            </td>
                                                            <td class="text-center">
                                                                <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Edit')" @click="editDiscount(discount)"
                                                                data-toggle="modal" data-target="#modal-admin-discount-edit">
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
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-admin-discount-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="saveDiscount()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="editingDiscount && editingDiscount.discount_type">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-discount-name" readonly type="text" class="form-control" :placeholder="$t('Namme')" v-model="editingDiscount.discount_type.name">
                                        <label for="edit-discount-name">{{ $t('Name') }}</label>
                                        <i :class="editingDiscount.discount_type.icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-discount-value" type="text" class="form-control" :placeholder="$t('Value')" autofocus v-model="editingDiscount.value">
                                        <label for="edit-discount-value">{{ $t('Value') }}</label>
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-discount-description" readonly type="text" class="form-control" :placeholder="$t('Description')" v-model="editingDiscount.discount_type.description">
                                        <label for="edit-discount-description">{{ $t('Description') }}</label>
                                        <i :class="editingDiscount.discount_type.icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="col-md-12">

                        <div class="col-md-12 text-center">
                            <h2 class="text-muted">
                                <i class="fa fa-save"></i> {{ $t('Save options') }}
                            </h2>
                        </div>

                        <div class="col-md-12 text-left no-padding" v-if="vehicles">
                            <div class="col-md-12 no-padding">
                                <span class="col-md-3">
                                    <label class="typo__label">
                                        <i class="fa fa-car"></i> {{ $t('Vehicles') }}
                                    </label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="default-vehicles" value="default" name="for-vehicles" v-model="options.for.vehicles">
                                    <label for="default-vehicles">{{ $t('By default') }}</label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="all-vehicles" value="all" name="for-vehicles" v-model="options.for.vehicles">
                                    <label for="all-vehicles">{{ $t('All') }}</label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="custom-vehicles" value="custom" name="for-vehicles" v-model="options.for.vehicles">
                                    <label for="custom-vehicles">{{ $t('Custom') }}</label>
                                </span>

                                <div class="col-md-12" v-if="options.for.vehicles === 'custom'">
                                    <multiselect v-model="options.vehicles" :placeholder="$t('Select vehicles')" label="number" track-by="id" :options="vehicles" :multiple="true"></multiselect>
                                </div>
                            </div>

                            <hr class="col-md-12 no-padding">

                            <div class="col-md-12 no-padding">
                                <span class="col-md-3">
                                    <label class="typo__label">
                                        <i class="fa fa-retweet"></i> {{ $t('Trajectory') }}
                                    </label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="default-trajectories" value="default" name="for-trajectories" v-model="options.for.trajectories">
                                    <label for="default-trajectories">{{ $t('By default') }}</label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="all-trajectories" value="all" name="for-trajectories" v-model="options.for.trajectories">
                                    <label for="all-trajectories">{{ $t('All') }}</label>
                                </span>
                                <span class="col-md-3 text-center">
                                    <input type="radio" id="custom-trajectories" value="custom" name="for-trajectories" v-model="options.for.trajectories">
                                    <label for="custom-trajectories">{{ $t('Custom') }}</label>
                                </span>

                                <div class="col-md-12" v-if="options.for.trajectories === 'custom'">
                                    <multiselect v-model="options.trajectories" :options="trajectoriesForMultiselect" :placeholder="$t('Select trajectories')" group-values="trajectories" group-label="route" label="name" track-by="id" :multiple="true"></multiselect>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12 text-center">
                        <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" :title="$t('Cancel')" onclick="$('#modal-admin-discount-edit').modal('hide')">
                            <i class="fa fa-times"></i>
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
        name: "AdminDiscountComponent",
        props: {
            vehicles: Array,
            routes: Array,
            trajectories: Array,
        },
        data: function(){
          return {
              vehicle: Object,
              selectedRoute: Object,
              selectedTrajectory: Object,
              editingDiscount: Object,
              options:{
                  for:{
                      vehicles: String,
                      trajectories: String,
                  },
                  vehicles: Array,
                  trajectories: Array,
              },
              trajectoriesByRoute: Array,
              discounts: Array,
              editing: false,
          }
        },
        mounted() {
            this.editingDiscount = null;
            this.selectedRoute = null;
            this.selectedTrajectory = null;
            this.options.for.vehicles = 'default';
            this.options.for.trajectories = 'default';
        },
        watch: {
            vehicle: function(){
                this.setParamToEdit('vehicle',this.vehicle);

                if (this.selectedRoute) {
                    this.loadTrajectories(this.selectedRoute);
                }else{
                    this.loadTrajectories(_.head(this.routes));
                }

                console.log(this.allTrajectoriesByRoutes);
            }
        },
        computed:{
            trajectoriesForMultiselect: function(){
                let allTrajectoriesByRoutes = [];
                _.forEach(_.groupBy(this.trajectories,'routeName'), function(routeTrajectories, routeName) {
                    allTrajectoriesByRoutes.push({
                        route: routeName,
                        trajectories: routeTrajectories
                    });
                });

                return allTrajectoriesByRoutes;
            }
        },
        methods: {
            loadTrajectories: function(route){
                if (!route) return false;
                this.selectedRoute = route;
                this.trajectoriesByRoute = _.filter(this.trajectories, function(t){
                    return t.route_id === route.id;
                });

                this.loadDiscounts(this.vehicle, _.head(this.trajectoriesByRoute));
            },
            loadDiscounts: function (vehicle, trajectory) {
                if (!trajectory) return false;
                this.selectedTrajectory = trajectory;
                this.setParamToEdit('trajectory',trajectory);
                this.discounts = [];
                App.blockUI({target: '#discounts-params-tab', animate: true});

                axios.get('parametros/descuentos', {
                    params: {
                        vehicle: vehicle.id,
                        trajectory: trajectory.id,
                    }
                }).then(r => {
                    this.discounts = _.sortBy(r.data, function(d){
                        return d.discount_type.name;
                    });
                }).catch(function (error) {
                    console.log(error);
                }).then(function () {
                    App.unblockUI('#discounts-params-tab');
                });
            },
            setParamToEdit: function(param, data){
                switch (param) {
                    case 'vehicle':
                        this.options.vehicles = [data];
                        this.options.trajectories = [];
                        break;
                    case 'trajectory':
                        this.options.trajectories = [data];
                        break;
                }
            },
            editDiscount: function(discount){
                this.editingDiscount = discount;
            },
            saveDiscount: function(){
                App.blockUI({target: '#discounts-params-tab', animate: true});
                App.blockUI({target: '#modal-admin-discount-edit .modal-content', animate: true});
                axios.post('parametros/descuentos/guardar', {
                    discount: this.editingDiscount,
                    options: {
                        for: this.options.for,
                        vehicles: _.map(this.options.vehicles, 'id'),
                        trajectories: _.map(this.options.trajectories, 'id'),
                    },
                }).then(r => {
                    this.editing = false;
                    if(r.data.error){
                        gerror(r.data.message);
                    }else{
                        gsuccess(r.data.message);
                        this.$emit('refresh-report');
                    }
                }).catch(function (error) {
                    console.log(error);
                    gerror("An error occurred in the process. Please contact your admin");
                }).then(function () {
                    App.unblockUI('#discounts-params-tab');
                    App.unblockUI('#modal-admin-discount-edit .modal-content');
                    $("#modal-admin-discount-edit").modal('hide');
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