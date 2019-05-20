<template>
    <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="tab-content">
                <div class="">
                    <div class="">
                        <ul class="nav nav-pills">
                            <li v-for="(route, indexRoute) in routes" :class="indexRoute === 0 ? 'active' : ''">
                                <a :href="'#tab-commission-' + route.id" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-flag"></i> {{ route.name }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div v-for="(route, indexRoute) in routes" class="tab-pane fade" :class="indexRoute === 0 ? 'active in' : ''" :id="'tab-commission-' + route.id">
                                <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                                    <thead>
                                    <tr class="inverse">
                                        <th class="col-md-1">
                                            <i class="fa fa-list-ol text-muted"></i><br>
                                        </th>
                                        <th class="col-md-2">
                                            <i class="icon-tag text-muted"></i><br> Type
                                        </th>
                                        <th class="col-md-2">
                                            <i class="fa fa-dollar text-muted"></i><br> Value
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="" v-for="(commission, indexCommission) in commissionsFor(route.id)">
                                        <td class="text-center">{{ indexCommission + 1 }}</td>
                                        <td class="text-center">{{ commission.type | capitalize }}</td>
                                        <td class="text-center">
                                            {{ commission.value | numberFormat('0,0') }}
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
        name: "AdminCommissionComponent",
        props: {
            routes: Array,
            commissions: Array,
        },
        computed:{

        },
        methods: {
            commissionsFor(routeId){
                return _.filter(this.commissions, {
                    'route_id': routeId,
                });
            }
        }
    }
</script>

<style scoped>

</style>