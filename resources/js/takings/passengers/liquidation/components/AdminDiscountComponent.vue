<template>
    <div class="row" v-if="vehicle">
        <div class="col-md-2 col-sm-3 col-xs-3">
            <ul class="nav nav-tabs tabs-left">
                <li class="active">
                    <a :href="'#vehicle-'+vehicle.id" data-toggle="tab">
                        <i class="fa fa-car"></i> {{ vehicle.number }}
                    </a>
                </li>
            </ul>
        </div>
        <div class="col-md-10 col-sm-9 col-xs-9">
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
                                                <li v-for="(trajectory, indexTrajectory) in trajectoriesByRoute" :class="indexTrajectory === 0 ? 'active trajectory-0' : ''" @click="loadDiscounts(vehicle, trajectory)">
                                                    <a href=".tab-discounts" data-toggle="tab">
                                                        {{ trajectory.name }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                        <td class="discounts text-center">
                                            <div class="tab-content">
                                                <div class="tab-discounts tab-pane fade active in">
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
                                                        <tr v-if="!editing" v-for="(discount, indexDiscount) in discounts">
                                                            <td class="text-center">{{ indexDiscount + 1 }}</td>
                                                            <td class="text-center">
                                                                <i :class="discount.discount_type.icon"></i> {{ discount.discount_type.name | capitalize }}
                                                            </td>
                                                            <td class="text-center">{{ discount.discount_type.description }}</td>
                                                            <td class="text-center">
                                                                {{ discount.value | numberFormat('$0,0') }}
                                                            </td>
                                                        </tr>
                                                        <tr v-if="editing" v-for="(discount, indexDiscount) in discounts">
                                                            <td class="text-center">{{ indexDiscount + 1 }}</td>
                                                            <td class="text-center">
                                                                <i :class="discount.discount_type.icon"></i> {{ discount.discount_type.name | capitalize }}
                                                            </td>
                                                            <td class="text-center">{{ discount.discount_type.description }}</td>
                                                            <td class="text-center">
                                                                <input type="number" class="form-control input-sm" v-model="discount.value">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="11" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
                                                        </tr>
                                                        </tbody>
                                                    </table>
                                                    <hr class="hr">
                                                    <div class="text-center">
                                                        <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Edit" @click="editDiscounts()">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </button>
                                                        <button v-if="editing" class="btn btn-sm green-haze btn-outline sbold uppercase btn-circle tooltips" title="Save" @click="saveDiscounts()">
                                                            <i class="fa fa-edit"></i> Save
                                                        </button>
                                                    </div>
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
              trajectoriesByRoute: Array,
              discounts: Array,
              editing: false,
          }
        },
        computed:{

        },
        methods: {
            loadTrajectories: function(route){
                this.trajectoriesByRoute = _.filter(this.trajectories, function(t){
                    return t.route_id === route.id;
                });
            },
            loadDiscounts: function (vehicle, trajectory) {
                this.discounts = [];
                App.blockUI({target: '#discounts-params-tab', animate: true});

                axios.get('parametros/descuentos', {
                    params: {
                        vehicle: vehicle.id,
                        route: trajectory.route_id,
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
            editDiscounts: function(){
              this.editing = true;
            },
            saveDiscounts: function(){
                App.blockUI({target: '#discounts-params-tab', animate: true});
                axios.get('parametros/descuentos/guardar', {
                    params: {
                        discounts: this.discounts
                    }
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
                });
            }
        }
    }
</script>

<style scoped>

</style>