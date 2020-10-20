<template>
    <div class="panel panel-default">
        <div class="panel-heading">
            <button type="button" class="btn btn-success btn-search-report pull-left" @click="searchReport()">
                <i class="fa fa-search"></i> <span class="hidden-xs">{{ $t('Search')}}</span>
            </button>

            <ul class="nav nav-tabs tabs-reversed">
                <li>
                    <a onclick="$('#modal-params-manager').modal('show')">
                        <i class="fa fa-cogs"></i> <span class="hidden-xs">{{ $t('Settings') }}</span>
                    </a>
                </li>
                <li>
                    <a href="#tab-road-safety" data-toggle="tab">
						<i class="fa fa-user-secret"></i> <span class="hidden-xs">{{ $t('Read safety') }}</span>
                    </a>
                </li>
                <li class="active">
                    <a href="#tab-takings" data-toggle="tab">
						<i class="fa fa-briefcase"></i> <span class="hidden-xs">{{ $t('Takings') }}</span>
                    </a>
                </li>
            </ul>
        </div>
        <div class="panel-body p-b-15">
            <div class="form-input-flat">
                <div class="col-md-3" v-if="admin">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Company') }}</label>
                        <div class="form-group">
                            <multiselect track-by="short_name" label="short_name" :options="search.companies" @input="getSearchParams()" v-model="search.company"
                                 :option-height="104" :searchable="true" :allow-empty="true" :placeholder="$t('Select a company')"
                            ></multiselect>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Vehicle') }}</label>
                        <div class="form-group">
                            <multiselect track-by="number" label="number" :options="search.vehicles" @input="searchReport()" v-model="search.vehicle"
                                 :option-height="104" :searchable="true" :allow-empty="true" :placeholder="$t('Select a vehicle')"
                            ></multiselect>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">{{ $t('Date') }}</label>
                        <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <date-picker v-model="search.date" valueType="format" :first-day-of-week="1" lang="es" @change="searchReport()" bootstrap-styling="true" clear-button="true" calendar-button="true"></date-picker>
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

                this.search.date = moment().format("YYYY-MM-DD");
                // this.search.date = '2020-02-27';
                let companySearch = this.search.company;
                // companySearch = {id:30};
                axios.get(this.urlParams, {
                    params: {
                        company: companySearch ? companySearch.id : null,
                    }
                }).then(response => {
                        const data = response.data;
                        this.search.vehicles = data.vehicles;
                        this.search.companies = data.companies;

                        // this.search.company = _.find(this.search.companies, function(c){
                        //     //return c.id === data.company.id;
                        //     return c.id === 30;
                        // });

                        // this.search.vehicle = _.find(this.search.vehicles, function(c){
                        //     return c.id === 1953;
                        // });

                        this.searchReport();
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {

                    });
            },
            searchReport: function () {
                if(this.search.vehicle && this.search.vehicle.id){
                    this.$emit('search-report');
                }else{
                    gerror(this.$t('Select a vehicle'));
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
    .mx-datepicker {
        width: 100% !important;
    }
</style>