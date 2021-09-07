import NumberFormat from 'vue-filter-number-format';
import numeral from 'numeral';

import SearchComponent from './components/SearchComponent';
import ReportComponent from './components/reports/ReportComponent';

import i18n from "../../lang/i18n";
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

Vue.filter('thousandRound', function (value) {
    // return value;
    const absValue = Math.abs(value);

    return (value < 0 ? -1 : 1) * Math.round(absValue / 1000) * 1000;
});

window.ml = {
    discountTypes: {
        auxiliary: 1,
        fuel: 2,
        operative: 3,
        toll: 4,
        provisions: 5,
    }
};

let liquidationView = new Vue({
    el: '#report-liquidation',
    i18n,
    components: {
        SearchComponent,
        ReportComponent
    },
    data: {
        flag: false,
        urlGetAdvances: String,
        vehicles: [],
        search: {
            companies: [],
            company: [],
            vehicles: [],
            vehicle: {},
            drivers: [],
            driver: {},
            date: String
        },
        marks: [],
        liquidation: {
            byTurns: [],
            otherDiscounts: [],
            discountsByTurns: [],
            observations: "",
            realTaken: 0,
            pendingBalance: 0,
            forgivableBalance: false,
            advances: {
                takings: 0,
                payFall: 0,
                getFall: 0,
            }
        }
    },
    computed: {
        searchParams: function () {
            const vehicle = this.search.vehicle;
            const company = this.search.company;
            const driver = this.search.driver;

            return {
                flag: this.flag,
                date: this.search.date,
                vehicle: vehicle ? vehicle.id : null,
                driver: driver ? driver.id : null,
                company: company ? company.id : null,
                valid: !!(vehicle && vehicle.id && this.search.date)
            }
        },
        discountsByTurns: function () {
            let discountsByTurns = [];

            if (this.marks.length) {
                const markWithMaxDiscounts = _.maxBy(this.marks, function (mark) {
                    return Object.keys(mark.discounts).length;
                });
                if (markWithMaxDiscounts) {
                    _.forEach(markWithMaxDiscounts.discounts, (discount) => {
                        const totalByTypeDiscount = _.sumBy(this.marks, function (mark) {
                            const markDiscount = _.find(mark.discounts, function (discountFilter) {
                                return discountFilter.discount_type.id === discount.discount_type.id
                            });

                            return markDiscount && markDiscount.required ? markDiscount.value : 0;
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

            return discountsByTurns;
        },
        totals: function () {
            /***************** LIQUIDATION BY TURN (MARK) ********************/
            this.liquidation.byTurns = [];
            _.forEach(this.marks, (mark) => {
                this.liquidation.byTurns.push(this.liquidationByTurn(mark));
            });

            const totalGrossBea = _.sumBy(this.marks, 'totalGrossBEA');

            const totalDiscountsByTurns = _.sumBy(this.discountsByTurns, 'value');
            const totalOtherDiscounts = _.sumBy(this.liquidation.otherDiscounts, function (other) {
                return (other.value ? other.value : 0);
            });

            const totalDiscounts = totalDiscountsByTurns + totalOtherDiscounts;
            const totalDiscountByFuel = this.totalDiscountBy(window.ml.discountTypes.fuel);
            const totalDiscountByMobilityAuxilio = this.totalDiscountBy(window.ml.discountTypes.auxiliary);
            const totalDiscountByTolls = this.totalDiscountBy(window.ml.discountTypes.toll);
            const totalDiscountByOperativeExpenses = this.totalDiscountBy(window.ml.discountTypes.operative);
            const totalDiscountByProvisions = this.totalDiscountBy(window.ml.discountTypes.provisions);

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
            // const balance = totalDispatch - totalPayFall + totalGetFall;

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
                totalDiscountByProvisions,

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
            const mainContainer = $('.report-container');
            App.blockUI({target: '.report-container, .admin-component .tab-pane', animate: true});
            const form = $('.form-search-report, #discounts-params-tab');
            form.find('.btn-search-report').addClass(loadingClass);

            this.$refs.report.searchReport().then(function () {
                mainContainer.hide().fadeIn();
                form.find('.btn-search-report').removeClass(loadingClass);
                App.unblockUI('.report-container, .admin-component .tab-pane');
            });
        },

        totalDiscountBy: function (type) {
            const total = _.head(_.filter(this.discountsByTurns, function (detail) {
                return detail.type_uid === type;
            }));

            return total && total.required ? total.value : 0;
        },

        /***************** LIQUIDATION BY TURN (MARK) ********************/
        liquidationByTurn: function (mark) {
            const penalty = mark.penalty;
            const commission = mark.commission;

            const payFall = (Number.isInteger(mark.payFall) ? mark.payFall : 0);
            const getFall = (Number.isInteger(mark.getFall) ? mark.getFall : 0);
            const turnDiscounts = this.turnDiscounts(mark);
            const totalTurn = mark.totalGrossBEA + (penalty ? penalty.value : 0);
            const subTotalTurn = totalTurn - payFall + getFall;
            const totalDispatch = totalTurn - (turnDiscounts.total - turnDiscounts.byFuel - turnDiscounts.byMobilityAuxilio) - (commission ? commission.value : 0);
            const balance = totalDispatch - payFall + getFall - turnDiscounts.byFuel;

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
                byOthers: 0,
                byProvisions: 0,
                total: 0
            };

            _.each(mark.discounts, function (discount) {
                const value = discount.required ? discount.value : 0;

                switch (discount.discount_type.uid) {
                    case window.ml.discountTypes.auxiliary:
                        discounts.byMobilityAuxilio = value;
                        break;
                    case window.ml.discountTypes.fuel:
                        discounts.byFuel = value;
                        break;
                    case window.ml.discountTypes.operative:
                        discounts.byOperativeExpenses = value;
                        break;
                    case window.ml.discountTypes.toll:
                        discounts.byTolls = value;
                        break;
                    case window.ml.discountTypes.provisions:
                        discounts.byProvisions = value;
                        break;
                }
                discounts.total += value;
            });

            const others = _.filter(this.liquidation.otherDiscounts, function (other) {
                return other.markId === mark.id;
            });

            discounts.byOthers = others.length ? _.sumBy(others, 'value') : 0;
            discounts.total += discounts.byOthers;

            return discounts;
        },
    },
    watch: {},
    mounted: function () {

    },
});

$(document).ready(function () {
    $('body').on('click', '.phases', function () {
        const el = $(this);
        $('.phases').removeClass('done active error warning');
        el.addClass($(this).data('active'));
    });
});