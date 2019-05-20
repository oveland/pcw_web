import NumberFormat from 'vue-filter-number-format';

import SearchComponent from './components/SearchComponent';
import AdminComponent from './components/AdminComponent';
import LiquidationComponent from './components/LiquidationComponent';
import TakingsComponent from './components/TakingsComponent';

Vue.filter('numberFormat', NumberFormat);

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

let liquidationView = new Vue({
    el: '#liquidation',
    components: {
        SearchComponent,
        AdminComponent,
        LiquidationComponent,
        TakingsComponent
    },
    data: {
        flag: false,
        urlList: String,
        vehicles: [],
        search: {
            vehicles: [],
            vehicle: {},
            date: String,
        },
        allMarks: [],
    },
    computed: {
        searchParams: function () {
            return {
                flag: this.flag,
                date: this.search.date,
                vehicle: this.search.vehicle.id,
            }
        },
        liquidatedMarks: function () {
            return _.filter(this.allMarks, {
                liquidated: true,
                taken: false,
            });
        },
        marks: function () {
            return _.filter(this.allMarks, {
                liquidated: false,
                taken: false,
            });
        },
        totals: function () {
            return {
                // Totals
                totalBea: _.sumBy(this.marks, 'totalBEA'),
                totalGrossBea: _.sumBy(this.marks, 'totalGrossBEA'),
                totalPassengersBea: _.sumBy(this.marks, 'passengersBEA'),
                totalBoarded: _.sumBy(this.marks, 'boarded'),
                totalPassengersDown: _.sumBy(this.marks, 'passengersDown'),
                totalPassengersUp: _.sumBy(this.marks, 'passengersUp'),
                totalLocks: _.sumBy(this.marks, 'locks'),
                totalAuxiliaries: _.sumBy(this.marks, 'auxiliaries'),
                // Averages
                averageBea: _.meanBy(this.marks, 'totalBEA'),
                averagePassengersBea: _.meanBy(this.marks, 'passengersBEA'),
                averageBoarded: _.meanBy(this.marks, 'boarded'),
                averagePassengersDown: _.meanBy(this.marks, 'passengersDown'),
                averagePassengersUp: _.meanBy(this.marks, 'passengersUp'),
                averageLocks: _.meanBy(this.marks, 'locks'),
                averageAuxiliaries: _.meanBy(this.marks, 'auxiliaries'),
            };
        },
    },
    methods: {
        searchReport: function () {
            this.flag = !this.searchParams.flag;
            const form = $('.form-search-report');
            form.find('.btn-search-report').addClass(loadingClass);

            axios.get(this.urlList, {params: this.searchParams}).then(data => {
                this.allMarks = data.data;
            }).catch(function (error) {
                console.log(error);
            }).then(function () {
                $('.report-container').hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
            });
        },
        updateVehicles: function (params) {
            this.vehicles = params.vehicles;
        }
    },
    mounted: function () {
        this.urlList = this.$el.attributes.url.value;
    },
});

$(document).ready(function () {
    $('body').on('click', '.phases', function () {
        const el = $(this);
        $('.phases').removeClass('done active error warning');
        el.addClass($(this).data('active'));
    });
});