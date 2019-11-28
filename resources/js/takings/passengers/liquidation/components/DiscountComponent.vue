<template>
    <div>
        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> {{ $t('Route') }} / {{ $t('Trajectory') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Locks') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Auxiliaries') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Boarded') }}
                </th>
                <th class="col-md-1">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('BEA') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total BEA') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Gross BEA') }}
                </th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Discounts by turn') }}
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
                    <span class="tooltips" :data-title="$t('Initial time')">{{ mark.initialTime }}</span> - <span class="tooltips" :data-title="$t('Final time')">{{ mark.finalTime }}</span>
                </td>
                <td class="text-center">{{ mark.locks }}</td>
                <td class="text-center">{{ mark.auxiliaries }}</td>
                <td class="text-center">{{ mark.boarded }}</td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center col-md-3">
                    <div v-for="discount in orderBy(mark.discounts, 'discount_type_id')" v-if="discount">
                        <span :data-original-title="discount.discount_type.description" class="tooltips">
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
                <td class="text-center">{{ totals.totalLocks }}</td>
                <td class="text-center">{{ totals.totalAuxiliaries }}</td>
                <td class="text-center">{{ totals.totalBoarded }}</td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totals.totalGrossBea | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    <div v-for="totalDiscount in orderBy(liquidation.discountsByTurns, 'type_id')" v-if="totalDiscount">
                        <span :data-original-title="totalDiscount.discount.discount_type.description" class="tooltips">
                            <i :class="totalDiscount.discount.discount_type.icon"></i> {{ totalDiscount.value | numberFormat('$0,0') }}
                        </span><br>
                    </div>
                </td>
            </tr>
            <tr class="total-discount-by-turn">
                <td colspan="7" class="text-right">
                <span class="text-bold">
                    <i class="icon-tag"></i> {{ $t('Total Discount by turns') }}
                </span>
                </td>
                <td class="text-center">
                <span :title="$t('Total discount')" class="text-bold tooltips">
                    {{ totals.totalDiscountsByTurns | numberFormat('$0,0') }}
                </span><br>
                </td>
            </tr>

            <tr class="" v-for="otherDiscount in liquidation.otherDiscounts">
                <td colspan="7" class="text-right">
                <span class="text-bold col-md-6 pull-right p-r-0">
                    <button v-if="!readonly" class="btn btn-danger btn-sm tooltips" data-placement="left" :data-title="$t('Delete')" style="position: absolute;left: -15px;" @click="removeOtherDiscount(otherDiscount.id)">
                        <i class="fa fa-trash"></i>
                    </button>
                    <div class="input-icon">
                        <i class="icon-tag font-green"></i> <input type="text" :readonly="readonly" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Description')" v-model="otherDiscount.name">
                    </div>
                </span>
                </td>
                <td class="text-center">
                    <div class="input-icon">
                        <i class="fa fa-dollar font-green"></i> <input type="text" :readonly="readonly" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Discount')" v-model.number="otherDiscount.value">
                    </div>
                </td>
            </tr>

            <tr v-if="!readonly" class="">
                <td colspan="8" class="text-right">
                    <button class="btn btn-sm btn-outline btn-white" @click="addOtherDiscount()">
                        <i class="fa fa-plus"></i> {{ $t('Add') }}
                    </button>
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-9">
                    <span class="pull-right text-bold">
                        <i class="icon-tag"></i> {{ $t('Total discounts') }}:
                    </span>
                </div>
                <div class="col-md-3 text-right">
                    <span class="text-bold">{{ totals.totalDiscounts | numberFormat('$0,0') }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    import Vue2Filters from 'vue2-filters';

    export default {
        name: "DiscountComponent",
        mixins: [Vue2Filters.mixin],
        props: {
            marks: Array,
            readonly: Boolean,
            liquidation: Object,
            totals: Object
        },
        methods: {
            addOtherDiscount: function () {
                this.liquidation.otherDiscounts.push({
                    id: (new Date).getTime(),
                    name: '',
                    value: ''
                });
                setTimeout(() => {
                    $('.tooltips').tooltip();
                }, 500);
            },
            removeOtherDiscount: function (idToRemove) {
                this.liquidation.otherDiscounts = _.filter(this.liquidation.otherDiscounts, function (other) {
                    return other.id !== idToRemove;
                });
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