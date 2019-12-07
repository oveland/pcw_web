import NumberFormat from 'vue-filter-number-format';
import numeral from 'numeral';

import SearchComponent from './components/SearchComponent';
import AdminComponent from './components/AdminComponent';
import LiquidationComponent from './components/LiquidationComponent';
import TakingsComponent from './components/TakingsComponent';

import i18n from "../../../lang/i18n";
import VueI18n from 'vue-i18n'
Vue.use(VueI18n);

Vue.filter('numberFormat', NumberFormat(numeral));

Vue.filter('capitalize', function (value) {
    if (!value) return '';
    value = value.toString();
    return value.charAt(0).toUpperCase() + value.slice(1);
});

let liquidationView = new Vue({
    el: '#liquidation',
    i18n,
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
        liquidation: {
            otherDiscounts: [],
            discountsByTurns: [],
            observations: ""
        }
    },
    computed: {
        searchParams: function () {
            const vehicle = this.search.vehicle;

            return {
                flag: this.flag,
                date: this.search.date,
                vehicle: vehicle? this.search.vehicle.id : null,
                valid: !!(vehicle && this.search.date)
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
            App.blockUI({target: '.report-container', animate: true});
            this.flag = !this.searchParams.flag;
            const form = $('.form-search-report');
            form.find('.btn-search-report').addClass(loadingClass);

            axios.get(this.urlList, {params: this.searchParams}).then(data => {
                this.allMarks = data.data;

                this.liquidation.otherDiscounts = [];
                this.liquidation.observations = "";
            }).catch(function (error) {
                console.log(error);
            }).then(function () {
                $('.report-container').hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
                App.unblockUI('.report-container');
            });
        },
        updateVehicles: function (params) {
            this.vehicles = params.vehicles;
        },
        totalDiscountByFuel: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.discount.discount_type.name.toUpperCase() === "COMBUSTIBLE";
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByTolls: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.discount.discount_type.name.toUpperCase() === "PEAJES";
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByOperativeExpenses: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.discount.discount_type.name.toUpperCase() === "GASTOS OPERATIVOS";
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        },
        totalDiscountByMobilityAuxilio: function () {
            const fuelTotalDiscount = _.head(_.filter(this.liquidation.discountsByTurns, function (detail) {
                return detail.discount.discount_type.name.toUpperCase() === "AUXILIO DE MOVILIDAD";
            }));

            return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
        }
    },
    watch:{
        marks: function () {
            let discountsByTurns = [];
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
                        type_id: discount.discount_type_id,
                        discount: discount,
                        value: totalByTypeDiscount,
                    });
                });
            }

            this.liquidation.discountsByTurns = discountsByTurns;

            return discountsByTurns;
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