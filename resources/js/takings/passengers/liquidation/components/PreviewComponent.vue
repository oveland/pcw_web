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
                <i class="fa fa-dollar"></i> Total BEA
            </span>
            <span class="pull-right col-md-4">{{ liquidation.totalBea | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="icon-tag"></i> Total discounts
            </span>
            <span class="pull-right col-md-4">- {{ liquidation.totalDiscounts | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> Total commissions
            </span>
            <span class="pull-right col-md-4">- {{ liquidation.totalCommissions | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="icon-shield"></i> Total penalties
            </span>
            <span class="pull-right col-md-4">{{ liquidation.totalPenalties | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="total-liquidation">
            <span class="text-bold">
                Total to liquidate
            </span>
            <span class="pull-right text-bold">{{ totalToLiquidate | numberFormat('$0,0') }}</span>
        </h3>
        <hr class="hr">
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