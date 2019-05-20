<template>
    <div class="row">
        <div class="col-md-3 col-sm-3 col-xs-3">
            <ul class="nav nav-tabs tabs-left">
                <li v-for="(vehicle, indexVehicle) in vehicles" :class="indexVehicle === 0 ? 'active' : ''">
                    <a :href="'#vehicle-'+vehicle.id" data-toggle="tab">
                        <i class="fa fa-car"></i> {{ vehicle.number }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-9 col-sm-9 col-xs-9">
            <div class="tab-content">
                <div v-for="(vehicle, indexVehicle) in vehicles" class="tab-pane fade" :id="'vehicle-'+vehicle.id" :class="indexVehicle === 0 ? 'active in' : ''">
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
                                <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                                    <thead>
                                    <tr class="inverse">
                                        <th class="col-md-1">
                                            <i class="fa fa-list-ol text-muted"></i><br>
                                        </th>
                                        <th class="col-md-3">
                                            <i class="fa fa-flag text-muted"></i><br> Trajectory
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
                                        <td class="col-md-2 text-center">
                                            <span class="label span-full" :class="discount.trajectory.name == 'IDA' ? 'label-success':'label-warning'">
                                                {{ discount.trajectory.name }}
                                            </span>
                                        </td>
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
            vehicles: Array,
            routes: Array,
            discounts: Array,
        },
        computed:{

        },
        methods: {
            discountsFor(vehicleId, routeId){
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