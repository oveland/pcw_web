import i18n from "../../lang/i18n";
import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

import numeral from "numeral";
import NumberFormat from "vue-filter-number-format";
Vue.filter('numberFormat', NumberFormat(numeral));

import ProfileSeatingComponent from './components/ProfileSeatingComponent';
import ReportPhotoComponent from './components/ReportPhotoComponent';
import SearchComponent from './components/SearchComponent';

import 'sweetalert2/src/sweetalert2.scss';


let adminRocketView = new Vue({
    el: '#vue-container',
    i18n,
    components: {
        SearchComponent,
        ProfileSeatingComponent,
        ReportPhotoComponent
    },
    data: {
        report: [],
        search: {
            companies: [],
            company: {},
            vehicles: [],
            vehicle: {},
            date: moment().format("YYYY-MM-DD"),
            activate: null,
            release: null,
            camera: {
                id: '0',
                name: 'Única'
            },
            cameras: [
                {
                    id: 'all',
                    name: 'Todas'
                },
                {
                    id: '0',
                    name: 'Única'
                },
                {
                    id: '1',
                    name: 'Cámara 1'
                },
                {
                    id: '2',
                    name: 'Cámara 2'
                },
                {
                    id: '3',
                    name: 'Cámara 3'
                },
                {
                    id: '4',
                    name: 'Cámara 4'
                },
                {
                    id: '5',
                    name: 'Cámara 5'
                }
            ]
        },
        searchParams: {}
    },
    computed: {

    },
    methods: {
        setSearch: function () {
            this.searchParams = {
                date: this.search.date,
                vehicle: this.search.vehicle.id,
                activate: this.search.activate,
                release: this.search.release,
                camera: this.search.camera.id
            };
        }
    },
    mounted: function () {

    },
});