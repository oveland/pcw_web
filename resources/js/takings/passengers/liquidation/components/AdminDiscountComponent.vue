<template>
    <div class="row" v-if="vehicle">
        <div class="col-md-3 col-sm-3 col-xs-3">
            <ul class="nav nav-tabs tabs-left">
                <li class="active">
                    <a :href="'#vehicle-'+vehicle.id" data-toggle="tab">
                        <i class="fa fa-car"></i> {{ vehicle.number }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <div class="tab-content">
                <div class="tab-pane fade active in" :id="'vehicle-'+vehicle.id">
                    <div class="">
                        <ul class="nav nav-pills">
                            <li v-for="(route, indexRoute) in routes" :class="indexRoute === 0 ? 'active' : ''">
                                <a :href="'#tab-' + vehicle.id + '-' + route.id" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-flag"></i> {{ route.name }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div v-for="(route, indexRoute) in routes" class="tab-pane fade" :class="indexRoute === 0 ? 'active in' : ''" :id="'tab-' + vehicle.id + '-' + route.id">
                                <table class="table table-bordered table-condensed table-report">
                                    <thead>
                                    <tr class="inverse">
                                        <th class="col-md-2">
                                            <i class="fa fa-flag text-muted"></i><br> Trajectory
                                        </th>
                                        <th class="col-md-2">
                                            <i class="icon-tag text-muted"></i><br> Discounts
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-center">
                                            <ul class="nav nav-tabs tabs-left">
                                                <li v-for="(trajectory, indexTrajectory) in trajectoriesByRoute[route.bea_id]" :class="indexTrajectory === 0 ? 'active' : ''" @click="loadDiscounts(vehicle.id, route.id, trajectory.id)">
                                                    <a :href="'#trajectory-'+trajectory.id" data-toggle="tab">
                                                        {{ trajectory.name }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="discounts text-center">
                                            <div class="tab-content">
                                                <div v-for="(trajectory, indexTrajectory) in trajectories" class="tab-pane fade" :id="'trajectory-'+trajectory.id" :class="indexTrajectory === 0 ? 'active in' : ''">
                                                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                                                        <thead>
                                                        <tr class="inverse">
                                                            <th class="col-md-1">
                                                                <i class="fa fa-list-ol text-muted"></i><br>
                                                            </th>
                                                            <th class="col-md-2">
                                                                <i class="icon-tag text-muted"></i><br> Name
                                                            </th>
                                                            <th class="col-md-2">
                                                                <i class="icon-tag text-muted"></i><br> Description
                                                            </th>
                                                            <th class="col-md-2">
                                                                <i class="fa fa-dollar text-muted"></i><br> Value
                                                            </th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <tr class="" v-for="(discount, indexDiscount) in discountsFor(vehicle.id, route.id)">
                                                            <td class="text-center">{{ indexDiscount + 1 }}</td>
                                                            <td class="text-center">
                                                                <i :class="discount.discount_type.icon"></i> {{ discount.discount_type.name | capitalize }}
                                                            </td>
                                                            <td class="text-center">{{ discount.discount_type.description }}</td>
                                                            <td class="text-center">
                                                                {{ discount.value | numberFormat('$0,0') }}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <hr class="hr">
                                                    <button class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips pull-right" title="Editar" onclick="ginfo('Feature on development')">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </button>
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
    </div>
</template>

<script>
    export default {
        name: "AdminDiscountComponent",
        props: {
            vehicle: Object,
            routes: Array,
            trajectories: Array,
        },
        data: function(){
          return {
              discounts: Array,
          }
        },
        computed:{
            trajectoriesByRoute: function(){
                return _.groupBy(this.trajectories, 'route_id');
            }
        },
        methods: {
            loadDiscounts: function (vehicleId, routeId, trajectoryId) {
                App.blockUI({target: '.discounts', animate: true});

                axios.get('parametros/descuentos', {
                    params: {
                        vehicle: vehicleId,
                        route: routeId,
                        trajectory: trajectoryId,
                    }
                }).then(r => {
                    this.discounts = r.data;
                }).catch(function (error) {
                    console.log(error);
                }).then(function () {
                    App.unblockUI('.discounts');
                });
            },
            discountsFor(vehicleId, routeId) {
                return _.filter(this.discounts, {
                    'vehicle_id': vehicleId,
                    'route_id': routeId,
                });
            }
        }
    }
</script>

<style scoped>

</style>