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
        <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total turns') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalTurns  | numberFormat('$0,0') }}</span>
        </h2>

        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total pay fall') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalPayFall  | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Total get fall') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalGetFall  | numberFormat('$0,0') }}</span>
        </h3>
        <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar hide"></i> {{ $t('Subtotal') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.subTotalTurns  | numberFormat('$0,0') }}</span>
        </h2>

        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total tolls') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByTolls | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total commissions') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalCommissions | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Operative Expenses') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByOperativeExpenses | numberFormat('$0,0') }}</span>
        </h3>
        <h3 class="totals" v-if="totals.totalOtherDiscounts">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total other discounts') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalOtherDiscounts | numberFormat('$0,0') }}</span>
        </h3>

        <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total dispatch') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalDispatch | numberFormat('$0,0') }}</span>
        </h2>

        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Total fuel') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.totalDiscountByFuel | numberFormat('$0,0') }}</span>
        </h3>

        <h2 class="totals">
            <span class="text-bold">
                <i class="fa fa-tachometer hide"></i> {{ $t('Balance') }}
            </span>
            <span class="pull-right col-md-4 p-0">{{ totals.balance | numberFormat('$0,0') }}</span>
        </h2>

        <hr class="m-t-40 m-b-20">

        <div class="" style="font-size: 1.5em !important;">
            <label for="observations" class="control-label">{{ $t('Observations') }}</label>
            <textarea id="observations" rows="2" :readonly="readonly" :disabled="readonly" class="form-control" v-model="liquidation.observations" style="resize: vertical;"></textarea>
        </div>
        <br>
        <div class="text-center" v-if="!readonly">
            <button class="btn btn-circle red btn-outline f-s-13 uppercase" @click="$emit('liquidate')" :disabled="totals.totalBea === 0">
                <i class="icon-layers"></i> {{ $t('Liquidate') }}
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
            totals: Object,
            readonly: Boolean
        }
    }
</script>

<style scoped>
    .search span {
        font-size: 1.6em !important;
    }

    h2.totals{
        border-bottom: 1px solid #c6c6c6;
        padding-bottom: 10px !important;
    }

    h2.totals span {
        font-size: 1.5em !important;
        font-weight: bold !important;
    }

    h2.totals span.pull-right{
        text-align: right;
    }

    h3.totals{
        padding-left: 20px;
        border-bottom: 1px solid rgba(236, 236, 236, 0.53);
        padding-bottom: 5px !important;
    }

    h3.totals span {
        font-size: 1.2em !important;
    }

    h3.totals span.pull-right{
        text-align: right;
    }

    .total-liquidation span {
        font-size: 1.6em !important;
    }
</style>