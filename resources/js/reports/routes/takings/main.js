import SearchComponent from './components/SearchComponent';
import TableComponent from './components/TableComponent';
import GraphCarrouselComponent from './components/GraphCarrouselComponent';
import VueJsonPretty from 'vue-json-pretty'

import NumberFormat from 'vue-filter-number-format';
import numeral from 'numeral';

import i18n from "./../../../lang/i18n";
import VueI18n from 'vue-i18n'

Vue.filter('numberFormat', NumberFormat(numeral));

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

Vue.use(VueI18n);


let camerasReportView = new Vue({
    el: '#reports-takings-container',
    i18n,
    components: {
        SearchComponent,
        TableComponent,
        VueJsonPretty,
        GraphCarrouselComponent
    },
    data: {
        urlList: String,
        urlExport: String,
        report: [],
        totals: {},
        averages: {},
        search: {
            companies: [],
            company: {},
            routes: [],
            route: {},
            vehicles: [],
            vehicle: {},
            date: moment().format("YYYY-MM-DD"),
            dateRange: false,
            type: 'detailed'
        },
    },
    computed: {
        searchParams: function () {
            return {
                company: this.search.company.id,
                date: this.search.date,
                route: this.search.route ? this.search.route.id : null,
                vehicle: this.search.vehicle ? this.search.vehicle.id : null,
                type: this.search.type
            }
        },
    },
    methods: {
        searchReport() {
            const form = $('.form-search-report');
            form.find('.btn-search-report').addClass(loadingClass);

            axios.get(this.urlList, {
                params: this.searchParams
            }).then((r) => {
                return r.data;
            }).then((data) => {
                this.report = data.report;
                this.totals = data.totals;
                this.averages = data.averages;
            }).catch(function (error) {
                console.log(error);
            }).then(function () {
                $('.report-container').hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
            });
        },
        exportReport() {
            window.open(`${this.urlExport}?${$.param(this.searchParams)}`, '_blank');
        }
    },
    mounted: function () {
        this.urlList = this.$el.attributes.url.value;
        this.urlExport = this.$el.attributes.export.value;
    },
});