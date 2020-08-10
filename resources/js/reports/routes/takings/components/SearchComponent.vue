<template>
    <div class="panel">
        <div class="p-t-20 p-l-30">
            <button type="button" class="btn blue-hoki btn-search-report btn-outline btn-circle" @click="searchReport()">
                <i class="fa fa-search"></i> {{ $t('Search') }}
            </button>

			<button type="button" class="btn green-meadow btn-search-report btn-outline btn-circle" @click="exportReport()">
				<i class="fa fa-download"></i> {{ $t('Export') }}
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
						<label class="control-label">{{ $t('Route') }}</label>
						<div class="form-group">
							<multiselect track-by="name" label="name" :options="search.routes" v-model="search.route" :clear-on-select="false"
										 :option-height="104" :searchable="true" :allow-empty="true"
										 :placeholder="$t('Select a route')"
							></multiselect>
						</div>
					</div>
				</div>

                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Vehicle') }}</label>
                        <div class="form-group">
                            <multiselect track-by="number" label="number" :options="search.vehicles" :clear-on-select="true"
                                         @input="searchReport()" v-model="search.vehicle"
                                         :option-height="104" :searchable="true" :allow-empty="true"
                                         :placeholder="$t('Select a vehicle')"></multiselect>
                        </div>
                    </div>
                </div>

				<div class="col-md-2 hide">
					<div class="form-group">
						<label class="control-label">{{ $t('Driver') }}</label>
						<div class="form-group">
							<multiselect track-by="number" label="number" :options="search.vehicles" :clear-on-select="true"
										 @input="searchReport()" v-model="search.vehicle"
										 :option-height="104" :searchable="true" :allow-empty="true"
										 :placeholder="$t('Select a vehicle')"></multiselect>
						</div>
					</div>
				</div>

                <div class="col-md-3">
                    <div class="form-group">
						<label class="control-label">{{ $t('Date') }} | {{ $t('Range') }} <input type="checkbox" v-model="search.dateRange" class="pull-right"></label>
                        <div class="input-group col-md-12">
                            <date-picker v-model="search.date"  :range="search.dateRange" valueType="format" :first-day-of-week="1" lang="es" @change="searchReport()" width="100%"></date-picker>
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
				this.search.route = {};
				this.search.vehicle = {};

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
                    this.search.routes = data.routes;

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
            searchReport: function () {
				this.$emit('search-report');
            },
			exportReport: function () {
				this.$emit('export-report');
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
	.mx-datepicker-popup{
		left: 0 !important;
		right: 0 !important;
	}
</style>