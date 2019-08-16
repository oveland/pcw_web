<template>
    <div class="row">
        <div class="col-md-8 col-sm-12 col-xs-12 col-md-offset-2">
            <div class="tab-content">
                <div class="">
                    <div class="">
                        <ul class="nav nav-pills">
                            <li v-for="(route, indexRoute) in routes" :class="indexRoute === 0 ? 'active' : ''">
                                <a :href="'#tab-penalty-' + route.id" data-toggle="tab" aria-expanded="true">
                                    <i class="fa fa-flag"></i> {{ route.name }}
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div v-for="(route, indexRoute) in routes" class="tab-pane fade" :class="indexRoute === 0 ? 'active in' : ''" :id="'tab-penalty-' + route.id">
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
                                        <th class="col-md-2">
                                            <i class="fa fa-rocket text-muted"></i><br> Options
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr class="" v-for="(penalty, indexPenalty) in penaltiesFor(route.id)">
                                        <td class="text-center">{{ indexPenalty + 1 }}</td>
                                        <td class="text-center">{{ penalty.type | capitalize }}</td>
                                        <td class="text-center">
                                            {{ penalty.value | numberFormat('$0,0') }}
                                        </td>
                                        <td class="text-center">
                                            <button v-if="!editing" class="btn btn-sm blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Edit" @click="editPenalty(penalty)"
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

        <div class="modal fade" id="modal-admin-penalty-edit" tabindex="1" data-backdrop="static">
            <div class="modal-dialog">
                <form class="modal-content row p-40" @submit.prevent="savePenalty()">
                    <div class="modal-body">
                        <div class="col-md-12 text-left no-padding" v-if="editingPenalty">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <select id="edit-penalty-type" readonly type="text" class="form-control" v-model="editingPenalty.type">
                                            <option v-for="(type) in penaltyTypes" :value="type">{{ type }}</option>
                                        </select>
                                        <label for="edit-penalty-type">Penalty type</label>
                                        <i class="fa fa-tag"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input has-success">
                                    <div class="input-icon">
                                        <input id="edit-penalty-value" type="text" class="form-control" placeholder="Value" autofocus v-model="editingPenalty.value">
                                        <label for="edit-penalty-value">Value</label>
                                        <i class="fa fa-dollar"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer col-md-12 text-center">
                        <button type="button" class="btn blue-hoki btn-outline sbold uppercase btn-circle tooltips" title="Cancel" onclick="$('#modal-admin-penalty-edit').modal('hide')">
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
        name: "AdminPenaltyComponent",
        props: {
            routes: Array,
            penalties: Array,
        },
        data: function () {
            return {
                penaltyTypes: Array,
                editingPenalty: Object,
                editing: false,
            }
        },
        mounted() {
            this.penaltyTypes = ['boarding'];
        },
        computed:{

        },
        methods: {
            editPenalty: function(penalty){
                this.editingPenalty = penalty;
            },
            penaltiesFor(routeId) {
                return _.filter(this.penalties, {
                    'route_id': routeId,
                });
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