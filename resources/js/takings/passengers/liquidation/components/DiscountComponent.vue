<template>
    <div>
        <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> Route / Trajectory
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Locks
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Auxiliaries
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> Boarded
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> BEA
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> Total BEA
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> Gross BEA
                </th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> Discounts by Turn
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="" v-for="mark in marks">
                <td class="col-md-2 text-center">
                    <span>{{ mark.turn.route.name }}</span><br>
                    <span class="label span-full" v-if="mark.trajectory" :class="mark.trajectory.name == 'IDA' ? 'label-success':'label-warning'">
                    {{ mark.trajectory.name }}
                </span>
                </td>
                <td class="text-center">{{ mark.locks }}</td>
                <td class="text-center">{{ mark.auxiliaries }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center col-md-3">
                    <div v-for="discount in mark.discounts" v-if="discount">
                        <span :title="discount.discount_type.description" class="tooltips">
                            <i :class="discount.discount_type.icon"></i> {{ discount.value | numberFormat('$0,0') }}
                        </span><br>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="14" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr class="totals">
                <td class="text-right">
                   <i class="icon-layers"></i> Totals
                </td>
                <td class="text-center">{{ totalLocks }}</td>
                <td class="text-center">{{ totalAuxiliaries }}</td>
                <td class="text-center">{{ totalBoarded }}</td>
                <td class="text-center">{{ totalPassengersBEA }}</td>
                <td class="text-center">{{ totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    <div v-for="totalDiscount in totalDiscounts" v-if="totalDiscount">
                        <span :title="totalDiscount.discount.discount_type.description" class="tooltips">
                            <i :class="totalDiscount.discount.discount_type.icon"></i> {{ totalDiscount.value | numberFormat('$0,0') }}
                        </span><br>
                    </div>
                </td>
            </tr>
            <tr class="total-discount-by-turn">
                <td colspan="7" class="text-right">
                <span class="text-bold">
                    <i class="icon-tag"></i> Total Discount by turns
                </span>
                </td>
                <td class="text-center">
                <span title="Total discount" class="text-bold tooltips">
                    {{ totalDiscountByTurn | numberFormat('$0,0') }}
                </span><br>
                </td>
            </tr>

            <tr class="" v-for="otherDiscount in liquidation.otherDiscounts">
                <td colspan="7" class="text-right">
                <span class="text-bold col-md-6 pull-right">
                    <button class="btn btn-danger btn-sm" style="position: absolute;left: -15px;" @click="removeOtherDiscount(otherDiscount.id)">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="input-icon">
                        <i class="icon-tag font-green"></i> <input type="text" class="form-control input-sm" placeholder="Description" v-model.number="otherDiscount.name">
                    </div>
                </span>
                </td>
                <td class="text-center">
                    <div class="input-icon">
                        <i class="fa fa-dollar font-green"></i> <input type="text" class="form-control input-sm" placeholder="Discount" v-model.number="otherDiscount.value">
                    </div>
                </td>
            </tr>

            <tr class="">
                <td colspan="8" class="text-right">
                    <button class="btn btn-sm btn-outline btn-white" @click="addOtherDiscount()">
                        <i class="fa fa-plus"></i> Add
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-8">
                    <span class="pull-right text-bold">
                        <i class="icon-tag"></i> Total discounts:
                    </span>
                </div>
                <div class="col-md-4 text-center">
                    <span class="text-bold">{{ totalDiscount | numberFormat('$0,0') }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "DiscountComponent",
        props: {
            marks: Array,
            liquidation: Object,
        },
        methods: {
            addOtherDiscount: function () {
                this.liquidation.otherDiscounts.push({
                    id: (new Date).getTime(),
                    name: '',
                    value: ''
                });
            },
            removeOtherDiscount: function (idToRemove) {
                this.liquidation.otherDiscounts = _.filter(this.liquidation.otherDiscounts, function (other) {
                    return other.id !== idToRemove;
                });
            }
        },
        computed: {
            totalBEA: function () {
                return _.sumBy(this.marks, 'totalBEA');
            },
            totalGrossBEA: function () {
                return _.sumBy(this.marks, 'totalGrossBEA');
            },
            totalPassengersUp: function () {
                return _.sumBy(this.marks, 'passengersUp');
            },
            totalPassengersDown: function () {
                return _.sumBy(this.marks, 'passengersDown');
            },
            totalLocks: function () {
                return _.sumBy(this.marks, 'locks');
            },
            totalAuxiliaries: function () {
                return _.sumBy(this.marks, 'auxiliaries');
            },
            totalBoarded: function () {
                return _.sumBy(this.marks, 'boarded');
            },
            totalPassengersBEA: function () {
                return _.sumBy(this.marks, 'passengersBEA');
            },
            totalDiscounts: function(){
                let totalDiscounts = [];
                const markWithMaxDiscounts = _.maxBy(this.marks, function(mark){
                    return Object.keys(mark.discounts).length;
                });

                if(markWithMaxDiscounts){
                    _.forEach(markWithMaxDiscounts.discounts, (discount) => {
                        const totalByTypeDiscount = _.sumBy(this.marks, function(mark){
                            const markDiscount = _.find(mark.discounts, function(discountFilter){
                                return discountFilter.discount_type.id === discount.discount_type.id
                            });
                            return markDiscount ? markDiscount.value : 0;
                        });

                        totalDiscounts.push({
                            discount: discount,
                            value: totalByTypeDiscount,
                        });
                    });
                }

                return totalDiscounts;
            },
            totalDiscountByTurn: function () {
                return _.sumBy(this.totalDiscounts, 'value');
            },
            totalDiscount: function () {
                const totalOtherDiscounts = _.sumBy(this.liquidation.otherDiscounts, function (other) {
                    return (other.value ? other.value : 0);
                });
                return this.liquidation.totalDiscounts = this.totalDiscountByTurn + totalOtherDiscounts;
            }
        }
    }
</script>

<style scoped>
    .totals span {
        font-size: 1.1em !important;
    }

    .total-discount-by-turn span {
        font-size: 1.2em !important;
    }

    .total-discount span {
        font-size: 1.6em !important;
    }

    .input-icon > i {
        margin: 8px 2px 4px 10px !important;
    }
</style>