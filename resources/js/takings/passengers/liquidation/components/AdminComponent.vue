<template>
    <div class="">
        <div class="portlet light bordered">
            <div class="portlet-title tabbable-line">
                <div class="caption">
                    <i class="fa fa-cogs"></i>
                    <span class="caption-subject font-dark bold uppercase">Params Manager</span>
                </div>
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#discounts-params-tab" data-toggle="tab">
                            <i class="icon-tag"></i> Discounts
                        </a>
                    </li>
                    <li>
                        <a href="#commissions-params-tab" data-toggle="tab">
                            <i class=" icon-user-follow"></i> Commissions
                        </a>
                    </li>
                    <li>
                        <a href="#penalties-params-tab" data-toggle="tab">
                            <i class="icon-shield"></i> Penalties
                        </a>
                    </li>
                </ul>
            </div>
            <div class="portlet-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="discounts-params-tab">
                        <admin-discount-component :vehicle="vehicle" :routes="routes" :trajectories="trajectories"></admin-discount-component>
                    </div>
                    <div class="tab-pane" id="commissions-params-tab">
                        <admin-commission-component :routes="routes" :commissions="commissions"></admin-commission-component>
                    </div>
                    <div class="tab-pane" id="penalties-params-tab">
                        <admin-penalty-component :routes="routes" :penalties="penalties"></admin-penalty-component>
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

    export default {
        name: "ParamsManagerComponent",
        props: {
            urlParams: String,
            vehicle: Object,
        },
        data: function () {
            return {
                params: []
            }
        },
        computed: {
            thereAreParams: function () {
                return this.params.length > 0;
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
            }
        },
        methods: {
            getParams: function () {
                axios.get(this.urlParams)
                    .then(data => {
                        this.params = data.data;
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {

                    });
            }
        },
        created() {
            this.getParams();
        },
        components: {
            AdminCommissionComponent,
            AdminDiscountComponent,
            AdminPenaltyComponent
        }
    }
</script>

<style scoped>
</style>