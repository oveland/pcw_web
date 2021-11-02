import i18n from "../../../lang/i18n";
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

import numeral from "numeral";
import NumberFormat from "vue-filter-number-format";
Vue.filter('numberFormat', NumberFormat(numeral));

import ProfileSeatingComponent from './components/ProfileSeatingComponent';
import ReportPhotoComponent from './components/ReportPhotoComponent';
import SearchComponent from './components/SearchComponent';

import 'sweetalert2/src/sweetalert2.scss';

import VideoComponent from './components/video/VideoComponent';

let adminRocketView = new Vue({
    el: '#vue-container',
    i18n,
    components: {
        SearchComponent,
        ProfileSeatingComponent,
        ReportPhotoComponent,
        VideoComponent
    },
    data: {
        report: [],
        search: {
            companies: [],
            company: {},
            vehicles: [],
            vehicle: {},
            date: moment().format("YYYY-MM-DD"),
            loadPhotos: false
        },
        searchParams: {}
    },
    computed: {

    },
    methods: {
        setSearch: function () {
            this.searchParams = {
                date: this.search.date,
                vehicle: this.search.vehicle.id
            };
        }
    },
    mounted: function () {

    },
});