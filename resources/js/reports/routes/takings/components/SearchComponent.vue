<template>
    <div class="panel">
        <div class="p-t-20 p-l-30">
			<div class="btn-group btn-group-circle btn-group-solid m-0">
				<button type="button" class="btn blue-hoki btn-search-report btn-outline" @click="searchReport()">
					<i class="fa fa-search"></i> {{ $t('Search') }}
				</button>
				<div class="btn-group btn-group-solid m-0">
					<button type="button" class="btn blue btn-circle-right btn-outline green-meadow dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						 <i class="fa fa-download"></i> {{ $t('Export') }} <i class="fa fa-angle-down"></i>
					</button>
					<ul class="dropdown-menu menu-export">
						<li>
							<a @click="exportReport('detailed')">
								<i class="icon-list"></i> {{ $t('Detailed') }} <i class="fa fa-file-excel-o pull-right green tooltips" :title="$t('Excel')"></i>
							</a>
						</li>
						<li>
							<a @click="exportReport('totals')">
								<i class="icon-layers"></i> {{ $t('Totals') }} <i class="fa fa-file-excel-o pull-right green tooltips" :title="$t('Excel')"></i>
							</a>
						</li>
						<li>
							<a @click="exportReport('grouped')">
								<i class="fa fa-car"></i> {{ $t('Grouped') }} <i class="fa fa-file-excel-o pull-right green tooltips" :title="$t('Excel')"></i>
							</a>
						</li>
						<li v-if="search.date" class="divider"> </li>
						<li v-if="search.date">
							<a :href="`http://www.pcwserviciosgps.com/pcw_gps/php/despachoDinamico/pdf/crearrecibopdf.php?action=downloadTakingsReceipt&ui=${ui}&vehicle=${search.vehicle ? search.vehicle.id : ''}&user-takings=${search.user ? search.user.id : ''}&fecha_sel=${search.date}`" target="_blank">
								<i class="icon-layers"></i> {{ $t('Receipt consolidated') }} <i class="fa fa-file-pdf-o pull-right red tooltips" :title="$t('PDF')"></i>
							</a>
						</li>
					</ul>
				</div>
			</div>
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
										 :placeholder="$t('Select a route')" @input="hideMainContainer()"
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
                            <date-picker v-model="search.date"  :range="search.dateRange" valueType="format" :first-day-of-week="1" lang="es" @input="hideMainContainer()" width="100%"></date-picker>
                        </div>
                    </div>
                </div>

				<div class="col-md-2">
					<div class="form-group">
						<label class="control-label">{{ $t('Takings user') }}</label>
						<div class="form-group">
							<multiselect track-by="username" label="tag" :options="search.users" :clear-on-select="true"
										 @input="searchReport()" v-model="search.user"
										 :option-height="104" :searchable="true" :allow-empty="true"
										 :placeholder="$t('Select an user')"></multiselect>
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
            admin: "",
			ui: Number
        },
        methods: {
            getSearchParams: function () {
				this.search.route = {};
				this.search.vehicle = {};
				this.search.user = {};

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
                    this.search.users = data.users;

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
			exportReport: function (type) {
            	this.search.type = type;
            	this.$emit('export-report');
            },
			hideMainContainer() {
				$('.report-container').slideUp();
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

	.menu-export .green{
		color: #148500 !important;
	}

	.menu-export .red{
		color: #851f00 !important;
	}
</style>