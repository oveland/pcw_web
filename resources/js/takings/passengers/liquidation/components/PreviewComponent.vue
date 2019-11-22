<template>
    <div class="form form-horizontal preview">
        <h3 class="search p-b-15">
            <span class="text-bold">
                <i class="fa fa-bus"></i> {{ search.vehicle.number }} | {{ search.vehicle.plate }}
            </span>
            <span class="pull-right">
                <i class="fa fa-calendar"></i> {{ search.date }}
            </span>
        </h3>
        <hr class="hr">
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar"></i> Total Value
            </span>
            <span class="pull-right col-md-4">{{ liquidation.totalGrossBea + liquidation.totalPenalties  | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer"></i> Total fuel
            </span>
            <span class="pull-right col-md-4">{{ totalDiscountByFuel | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="total-liquidation">
            <span class="text-bold">
                Saldo
            </span>
            <span class="pull-right text-bold">{{ liquidation.totalGrossBea + liquidation.totalPenalties - totalDiscountByFuel | numberFormat('$0,0') }}</span>
        </h3>
        <br><br>

        <h3 class="totals">
            <span class="text-bold">
                <i class="icon-tag"></i> Total discounts (No Fuel, No Aux)
            </span>
            <span class="pull-right col-md-4">- {{ liquidation.totalDiscounts - totalDiscountByFuel - totalDiscountByMobilityAuxilio | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> Total commissions
            </span>
            <span class="pull-right col-md-4">- {{ liquidation.totalCommissions | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals hide">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> Total penalties
            </span>
            <span class="pull-right col-md-4">+ {{ liquidation.totalPenalties | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals hide">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> totalDiscountByMobilityAuxilio
            </span>
            <span class="pull-right col-md-4">- {{ totalDiscountByMobilityAuxilio | numberFormat('$0,0') }}</span>
        </h3>
        <br>
        <h3 class="total-liquidation">
            <span class="text-bold">
                Total Liquidation
            </span>
            <span class="pull-right text-bold">{{ totalToLiquidate | numberFormat('$0,0') }}</span>
        </h3>
        <hr class="hr"><br>

        <div class="" style="font-size: 1.5em !important;">
            <label for="observations" class="control-label">Observations</label>
            <textarea id="observations" rows="2" :readonly="readonly" :disabled="readonly" class="form-control" v-model="liquidation.observations" style="resize: vertical;"></textarea>
        </div>
        <br>
        <div class="text-center" v-if="!readonly">
            <button class="btn btn-circle red btn-outline f-s-13" @click="$emit('liquidate')" :disabled="liquidation.totalBea === 0">
                LIQUIDATE <i class="icon-check"></i>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "PreviewComponent",
        props: {
            search:Object,
            liquidation: Object,
            readonly: Boolean
        },
        computed: {
            totalToLiquidate: function () {
                return this.liquidation.total = this.liquidation.totalBea - this.liquidation.totalDiscounts - this.liquidation.totalCommissions + this.liquidation.totalPenalties;
            },
            totalDiscountByFuel: function () {
                const fuelTotalDiscount = _.head(_.filter(this.liquidation.totalDiscountsDetail, function (detail) {
                    return detail.discount.discount_type.name.toUpperCase() === "COMBUSTIBLE";
                }));

                return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
            },
            totalDiscountByMobilityAuxilio: function () {
                const fuelTotalDiscount = _.head(_.filter(this.liquidation.totalDiscountsDetail, function (detail) {
                    return detail.discount.discount_type.name.toUpperCase() === "AUXILIO DE MOVILIDAD";
                }));

                return fuelTotalDiscount ? fuelTotalDiscount.value : 0;
            }
        }
    }
</script>

<style scoped>
    .search span {
        font-size: 1.6em !important;
    }
    .totals{
        border-bottom: 1px solid lightgray;
        padding-bottom: 5px !important;
    }
    .totals span {
        font-size: 1.3em !important;
    }
    .totals span.pull-right{
        text-align: right;
    }
    .total-liquidation span {
        font-size: 1.6em !important;
    }
</style>