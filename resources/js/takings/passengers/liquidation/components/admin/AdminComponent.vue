<template>
    <div class="">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="text-center col-md-12" style="position: absolute">
                    <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Close" data-dismiss="modal">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
                <div class="caption">
                    <i class="fa fa-cogs"></i>
                    <span class="caption-subject font-dark bold uppercase">{{ $t('Params manager') }}</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#discounts-params-tab" data-toggle="tab">
                            <i class="icon-tag"></i> {{ $t('Discounts') }}
                        </a>
                    </li>
                    <li>
                        <a href="#penalties-params-tab" data-toggle="tab">
                            <i class="icon-shield"></i> {{ $t('Penalties') }}
                        </a>
                    </li>
                    <li>
                        <a href="#commissions-params-tab" data-toggle="tab">
                            <i class="icon-user-follow"></i> {{ $t('Commissions') }}
                        </a>
                    </li>

                    <li class="divider-menu hidden-sm hidden-xs"></li>

                    <li>
                        <a href="#management-costs-tab" data-toggle="tab">
                            <i class="fa fa-dollar"></i> {{ $t('Costs') }}
                        </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="discounts-params-tab">
                        <admin-discount-component :vehicles="vehicles" :vehicle-selected="vehicle" :routes="routes" :trajectories="trajectories" v-on:refresh-report="$emit('refresh-report')"></admin-discount-component>
                    </div>
                    <div class="tab-pane" id="penalties-params-tab">
                        <admin-penalty-component :vehicles="vehicles" :vehicle-selected="vehicle" :routes="routes" :penalties="penalties" v-on:refresh-report="$emit('refresh-report')"></admin-penalty-component>
                    </div>
                    <div class="tab-pane" id="commissions-params-tab">
                        <admin-commission-component :routes="routes" :commissions="commissions" v-on:refresh-report="$emit('refresh-report')"></admin-commission-component>
                    </div>
                    <div class="tab-pane" id="management-costs-tab">
                        <management-cost-component :vehicles="vehicles" :vehicle-selected="vehicle" :management-costs="managementCosts" v-on:refresh-report="$emit('refresh-report')"></management-cost-component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import AdminDiscountComponent from "./AdminDiscountComponent";
    import AdminCommissionComponent from "./AdminCommissionComponent";
    import AdminPenaltyComponent from "./AdminPenaltyComponent";
    import ManagementCostComponent from "./ManagementCostComponent";

    export default {
        name: "ParamsManagerComponent",
        props: {
            urlParams: String,
            vehicle: Object,
            searchParams: Object,
        },
        data: function () {
            return {
                params: []
            }
        },
        watch: {
            searchParams: function () {
                this.getParams();
            }
        },
        computed: {
            thereAreParams: function () {
                return this.params.length > 0;
            },
            vehicles: function () {
                return this.params.vehicles;
            },
            routes: function () {
                return this.params.routes;
            },
            trajectories: function () {
                return this.params.trajectories;
            },
            discounts: function () {
                return this.params.discounts;
            },
            commissions: function () {
                return this.params.commissions;
            },
            penalties: function () {
                return this.params.penalties;
            },
            managementCosts: function () {
                return this.params.managementCosts;
            }
        },
        methods: {
            getParams: function () {
                axios.get(this.urlParams, {
                    params: this.searchParams
                }).then(data => {
                        this.params = data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {

                    });
            }
        },
        components: {
            AdminDiscountComponent,
            AdminPenaltyComponent,
            AdminCommissionComponent,
            ManagementCostComponent
        }
    }
</script>

<style scoped>
    .divider-menu {
        height: 23px !important;
        border: 1px solid lightblue !important;
        margin: 10px !important;
    }
</style>