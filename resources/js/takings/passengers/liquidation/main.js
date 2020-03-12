import NumberFormat from 'vue-filter-number-format';
import numeral from 'numeral';

import SearchComponent from './components/SearchComponent';
import AdminComponent from './components/admin/AdminComponent';
import LiquidationComponent from './components/LiquidationComponent';
import TakingsComponent from './components/TakingsComponent';
import TakingsListComponent from './components/TakingsListComponent';
import DailyReportComponent from './components/reports/DailyReportComponent';

import RoadSafetyTakingsComponent from './components/roadSafety/RoadSafetyTakingsComponent';
import RoadSafetyTakingsTurnsComponent from './components/roadSafety/RoadSafetyTakingsTurnsComponent';

import i18n from "../../../lang/i18n";
import VueI18n from 'vue-i18n'

import Swal from 'sweetalert2/dist/sweetalert2.min'
import 'sweetalert2/src/sweetalert2.scss'

Vue.use(VueI18n);

Vue.filter('numberFormat', NumberFormat(numeral));

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

window.ml = {
    discountTypes : {
        auxiliary: 1,
        fuel: 2,
        operative: 3,
        toll: 4
    }
};

let liquidationView = new Vue({
    el: '#liquidation',
    i18n,
    components: {
        SearchComponent,
        AdminComponent,
        LiquidationComponent,
        TakingsComponent,
        TakingsListComponent,
        DailyReportComponent,
        RoadSafetyTakingsComponent,
        RoadSafetyTakingsTurnsComponent
    },
    data: {
        flag: false,
        urlList: String,
        vehicles: [],
        search: {
            companies: [],
            company: [],
            vehicles: [],
            vehicle: {},
            date: String
        },
        marks: [],
        liquidation: {
            byTurns:[],
            otherDiscounts: [],
            discountsByTurns: [],
            observations: ""
        }
    },
    computed: {
        searchParams: function () {
            const vehicle = this.search.vehicle;
            const company = this.search.company;

            return {
                flag: this.flag,
                date: this.search.date,
                vehicle: vehicle ? vehicle.id : null,
                company: company ? company.id : null,
                valid: !!(vehicle && vehicle.id && this.search.date)
            }
        },
        totals: function () {
            /***************** LIQUIDATION BY TURN (MARK) ********************/
            this.liquidation.byTurns = [];
            _.forEach(this.marks, (mark) => {
                this.liquidation.byTurns.push( this.liquidationByTurn(mark) );
            });

            const totalGrossBea = _.sumBy(this.marks, 'totalGrossBEA');

            const totalDiscountsByTurns = _.sumBy(this.liquidation.discountsByTurns, 'value');
            const totalOtherDiscounts = _.sumBy(this.liquidation.otherDiscounts, function (other) {
                return (other.value ? other.value : 0);
            });
            
            const totalDiscounts = totalDiscountsByTurns + totalOtherDiscounts;
            const totalDiscountByFuel = this.totalDiscountByFuel();
            const totalDiscountByMobilityAuxilio = this.totalDiscountByMobilityAuxilio();
            const totalDiscountByTolls = this.totalDiscountByTolls();
            const totalDiscountByOperativeExpenses = this.totalDiscountByOperativeExpenses();

            const totalPenalties = _.sumBy(this.marks, function (mark) {
                return mark.penalty.value;
            });
            const totalCommissions = _.sumBy(this.marks, function (mark) {
                return mark.commission.value;
            });

            const totalPayFall = _.sumBy(this.marks, function (m) {
                return Number.isInteger(parseInt(m.payFall)) ? parseInt(m.payFall) : 0;
            });
            const totalGetFall = _.sumBy(this.marks, function (m) {
                return Number.isInteger(parseInt(m.getFall)) ? parseInt(m.getFall) : 0;
            });

            const totalTurns = totalGrossBea + totalPenalties;
            const subTotalTurns = totalTurns - totalPayFall + totalGetFall;
            const totalDispatch = totalTurns - (totalDiscounts - totalDiscountByFuel - totalDiscountByMobilityAuxilio) - totalCommissions;
            const balance = totalDispatch - totalPayFall + totalGetFall - totalDiscountByFuel;

            return {
                // Totals
                totalBea: _.sumBy(this.marks, 'totalBEA'),
                totalGrossBea,

                totalPassengersBea: _.sumBy(this.marks, 'passengersBEA'),
                totalPassengersDown: _.sumBy(this.marks, 'passengersDown'),
                totalPassengersUp: _.sumBy(this.marks, 'passengersUp'),
                totalLocks: _.sumBy(this.marks, 'locks'),
                totalAuxiliaries: _.sumBy(this.marks, 'auxiliaries'),
                totalBoarded: _.sumBy(this.marks, 'boarded'),

                totalDiscountsByTurns,
                totalOtherDiscounts,
                totalDiscounts,
                totalPenalties,
                totalCommissions,
                totalPayFall,
                totalGetFall,
                totalDiscountByFuel,
                totalDiscountByMobilityAuxilio,
                totalDiscountByTolls,
                totalDiscountByOperativeExpenses,

                totalTurns,
                subTotalTurns,
                totalDispatch,
                balance,
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
            this.marks = [];
            const mainContainer = $('.report-container');
            if (this.searchParams.valid) {
                App.blockUI({target: '.report-container', animate: true});
                this.flag = !this.searchParams.flag;
                const form = $('.form-search-report');
                form.find('.btn-search-report').addClass(loadingClass);

                axios.get(this.urlList, {params: this.searchParams}).then(data => {
                    this.marks = data.data;
                    this.liquidation.otherDiscounts = [];
                    this.liquidation.observations = "";
                }).catch( (error) => {
                    Swal.fire({
                        title: 'Error!',
                        text: this.$t('An error occurred in the process. Contact your administrator'),
                        icon: 'error',
                        timer: 4000,
                        timerProgressBar: true,
                    })
                }).then(function () {
                    mainContainer.hide().fadeIn();
                    form.find('.btn-search-report').removeClass(loadingClass);
                    App.unblockUI('.report-container');
                });
            }else{
                mainContainer.slideUp();
            }
        },
        updateVehicles: function (params) {
            this.vehicles = params.vehicles;
        },

        totalDiscountByMobilityAuxilio: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.type_uid === window.ml.discountTypes.auxiliary;
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByFuel: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.type_uid === window.ml.discountTypes.fuel;
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByOperativeExpenses: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.type_uid === window.ml.discountTypes.operative;
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByTolls: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.type_uid === window.ml.discountTypes.toll;
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },

        /***************** LIQUIDATION BY TURN (MARK) ********************/
        liquidationByTurn: function(mark){
            const penalty = mark.penalty;
            const commission = mark.commission;

            const payFall = (Number.isInteger(mark.payFall) ? mark.payFall : 0);
            const getFall = (Number.isInteger(mark.getFall) ? mark.getFall : 0);
            const turnDiscounts = this.turnDiscounts(mark);
            const totalTurn = mark.totalGrossBEA + (penalty ? penalty.value : 0);
            const subTotalTurn = totalTurn - payFall  + getFall;
            const totalDispatch = totalTurn - ( turnDiscounts.total - turnDiscounts.byFuel - turnDiscounts.byMobilityAuxilio) - (commission ? commission.value : 0);
            const balance = totalDispatch - payFall  + getFall - turnDiscounts.byFuel;

            return {
                markId: mark.id,
                payFall,
                getFall,
                turnDiscounts,
                totalTurn,
                subTotalTurn,
                totalDispatch,
                balance
            };
        },
        turnDiscounts: function (mark) {
            let discounts = {
                byMobilityAuxilio: 0,
                byFuel: 0,
                byOperativeExpenses: 0,
                byTolls: 0,
                total: 0
            };

            _.each(mark.discounts, function (discount) {

                switch (discount.discount_type.uid) {
                    case window.ml.discountTypes.auxiliary:
                        discounts.byMobilityAuxilio = discount.value;
                        break;
                    case window.ml.discountTypes.fuel:
                        discounts.byFuel = discount.value;
                        break;
                    case window.ml.discountTypes.operative:
                        discounts.byOperativeExpenses = discount.value;
                        break;
                    case window.ml.discountTypes.toll:
                        discounts.byTolls = discount.value;
                        break;
                }
                discounts.total += discount.value;
            });

            return discounts;
        },
    },
    watch:{
        marks: function () {
            let discountsByTurns = [];

            if(this.marks.length){
                const markWithMaxDiscounts = _.maxBy(this.marks, function (mark) {
                    return Object.keys(mark.discounts).length;
                });
                if (markWithMaxDiscounts) {
                    _.forEach(markWithMaxDiscounts.discounts, (discount) => {
                        const totalByTypeDiscount = _.sumBy(this.marks, function (mark) {
                            const markDiscount = _.find(mark.discounts, function (discountFilter) {
                                return discountFilter.discount_type.id === discount.discount_type.id
                            });
                            return markDiscount ? markDiscount.value : 0;
                        });

                        discountsByTurns.push({
                            type_uid: discount.discount_type.uid,
                            discount: discount,
                            value: totalByTypeDiscount,
                        });
                    });
                }
            }
            this.liquidation.discountsByTurns = discountsByTurns;
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