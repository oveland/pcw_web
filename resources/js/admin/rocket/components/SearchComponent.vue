<template>
    <div class="panel panel-inverse">
        <div class="panel-heading hide">
            <button type="button" class="btn btn-success btn-sm btn-search-report" @click="setSearch()">
                <i class="fa fa-search"></i> Search
            </button>
        </div>
        <div class="panel-body p-b-15">
            <div class="form-input-flat">
                <div class="col-md-2" v-if="admin">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Company') }}</label>
                        <div class="form-group">
                            <multiselect track-by="short_name" label="short_name" :options="search.companies" @input="getSearchParams()" v-model="search.company"
                                         :option-height="104" :searchable="true" :allow-empty="true" :placeholder="$t('Select a company')"
                            ></multiselect>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Vehicle') }}</label>
                        <div class="form-group">
                            <multiselect track-by="number" label="number" :options="search.vehicles"
										 v-model="search.vehicle"
                                         :option-height="104" :searchable="true" :allow-empty="true"
                                         :placeholder="$t('Select a vehicle')"></multiselect>
                        </div>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Date') }}</label>
                        <div class="input-group col-md-12">
                            <date-picker v-model="search.date" valueType="format" :first-day-of-week="1" lang="es" width="100%"></date-picker>
                        </div>
                    </div>
                </div>

				<div class="col-md-1 hide">
					<div class="form-group">
						<label class="control-label">Activate</label>
						<div class="form-group">
							<input type="number" name="" v-model="search.activate" class="input form-control">
						</div>
					</div>
				</div>
				<div class="col-md-1 hide">
					<div class="form-group">
						<label class="control-label">Release</label>
						<div class="form-group">
							<input type="number" name="" v-model="search.release" class="input form-control">
						</div>
					</div>
				</div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">{{ $t('Camera') }}</label>
						<div class="form-group">
							<multiselect track-by="id" label="name" :options="search.cameras"
										 v-model="search.camera"
										 :option-height="104" :searchable="false" :allow-empty="false"
										 :placeholder="$t('Select a camera')"></multiselect>
						</div>
					</div>
				</div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">&nbsp;</label>
                        <div class="input-group col-md-12">
                            <button type="button" class="btn green-haze btn-outline btn-light btn-search-report" @click="setSearch()">
                                <i class="fa fa-search"></i> {{ $t('Search') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Multiselect from 'vue-multiselect';
    import DatePicker from 'vue2-datepicker';

    export default {
        name: "SearchComponent",
        props: {
            urlParams: String,
            search: Object,
            admin: ""
        },
        methods: {
            getSearchParams: function () {
                const mainContainer = $('.report-container');
                mainContainer.fadeIn();
                const companySearch = this.search.company;
                axios.get(this.urlParams, {
                    params: {
                        company: companySearch ? companySearch.id : null,
                    }
                }).then(response => {
                    const data = response.data;
                    this.search.vehicles = data.vehicles;
                    this.search.companies = data.companies;

                    this.search.company = _.find(this.search.companies, function (c) {
                        return c.id === data.company.id;
                    });
                })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {

                    });
            },
            setSearch: function () {
                if (this.search.vehicle && this.search.vehicle.id) {
                    this.$emit('set-search');
                } else {
                    gerror("Select a vehicle");
                }
            }
        },
        components: {
            Multiselect,
            DatePicker
        },
        created() {
            this.getSearchParams();
        },
    }
</script>

<style src="vue-multiselect/dist/vue-multiselect.min.css"></style>

<style scoped>
</style>