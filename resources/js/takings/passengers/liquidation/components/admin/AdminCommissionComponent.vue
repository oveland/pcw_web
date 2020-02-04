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
                                    <tr class="" v-for="(commission, indexCommission) in commissionsFor(route.id)">
                                        <td class="text-center">{{ indexCommission + 1 }}</td>
                                        <td class="text-center">{{ commission.type | capitalize }}</td>
                                        <td class="text-center">
                                            {{ commission.value | numberFormat('0,0') }}
                                        </td>
                                        <td class="text-center">
                                            <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Edit" @click="editCommission(commission)"
                                                    data-toggle="modal" data-target="#modal-admin-commission-edit">
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

        <div class="modal fade" id="modal-admin-commission-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="saveCommission()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="editingCommission">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <select id="edit-commission-type" readonly type="text" class="form-control" v-model="editingCommission.type">
                                            <option v-for="(type) in commissionTypes" :value="type">{{ type }}</option>
                                        </select>
                                        <label for="edit-commission-type">{{ $t('Commission type') }}</label>
                                        <i class="fa fa-tag"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-commission-value" type="text" class="form-control" placeholder="Value" autofocus v-model="editingCommission.value">
                                        <label for="edit-commission-value">{{ $t('Value') }}</label>
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12 text-center">
                        <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Cancel" onclick="$('#modal-admin-commission-edit').modal('hide')">
                            <i class="fa fa-times"></i>
                        </button>
                        <button class="btn btn-success btn-outline sbold uppercase btn-circle tooltips" title="Save">
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
        name: "AdminCommissionComponent",
        props: {
            routes: Array,
            commissions: Array,
        },
        data: function () {
            return {
                commissionTypes: Array,
                editingCommission: Object,
                editing: false,
            }
        },
        mounted() {
            this.commissionTypes = ['percent', 'fixed'];
        },
        computed: {},
        methods: {
            editCommission: function(commission){
                this.editingCommission = commission;
            },
            commissionsFor(routeId) {
                return _.filter(this.commissions, {
                    'route_id': routeId,
                });
            },
            saveCommission: function(){
                App.blockUI({target: '#commissions-params-tab', animate: true});
                axios.post('parametros/comisiones/guardar', {
                    commission: this.editingCommission
                }).then(r => {
                    this.editing = false;
                    if(r.data.error){
                        gerror(r.data.message);
                    }else{
                        gsuccess(r.data.message);
                        this.$emit('refresh-report');
                        $('#modal-admin-commission-edit').modal('hide');
                    }
                }).catch(function (error) {
                    console.log(error);
                    gerror("An error occurred in the process. Please contact your admin");
                }).then(function () {
                    App.unblockUI('#commissions-params-tab');
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