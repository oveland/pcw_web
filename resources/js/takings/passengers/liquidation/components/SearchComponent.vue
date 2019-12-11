<template>
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <button type="button" class="btn btn-success btn-sm btn-search-report" @click="searchReport()">
                <i class="fa fa-search"></i> <span class="hidden-xs">{{ $t('Search')}}</span>
            </button>
        </div>
        <div class="panel-body p-b-15">
            <div class="form-input-flat">
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
            search: Object
        },
        methods: {
            getSearchParams: function () {
                this.search.date = moment().format("YYYY-MM-DD");
                this.search.date = '2019-06-17';
                axios.get(this.urlParams)
                    .then(response => {
                        this.search.vehicles = response.data;

                        this.search.vehicle = _.find(response.data, function(v){
                            return v.number === '8060';
                        });

                        this.searchReport();
                    })
                    .catch(function (error) {
                        console.log(error);
                    })
                    .then(function () {

                    });
            },
            searchReport: function () {
                if(this.search.vehicle){
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