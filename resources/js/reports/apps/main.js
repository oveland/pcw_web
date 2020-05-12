import SearchComponent from './components/SearchComponent';

import i18n from "../../lang/i18n";
import VueI18n from 'vue-i18n'

Vue.use(VueI18n);

let camerasReportView = new Vue({
    el: '#report-apps',
    i18n,
    components: {
        SearchComponent,
    },
    data: {
        urlList: String,
        report: [],
        search: {
            companies: [],
            company: [],
            vehicles: [],
            vehicle: {},
            date: String
        },
    },
    computed: {
        searchParams: function () {
            return {
                date: this.search.date,
                vehicle: this.search.vehicle.id,
            }
        },
    },
    methods: {
        searchReport: function () {
            const form = $('.form-search-report');
            form.find('.btn-search-report').addClass(loadingClass);

            axios.get(this.urlList, {params: this.searchParams}).then((r) => {
                this.report = r.data;
            }).catch(function (error) {
                console.log(error);
            }).then(function () {
                $('.report-container').hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
            });
        }
    },
    mounted: function () {
        this.urlList = this.$el.attributes.url.value;
    },
});