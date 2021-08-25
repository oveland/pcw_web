<template>
    <div class="">
        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th width="3%">
                    <i class="fa fa-list-ol text-muted"></i>
                </th>
                <th class="col-md-2">
                    <i class="fa fa-flag text-muted"></i><br> {{ $t('Route') }} / {{ $t('Trajectory') }}
                </th>
                <th class="col-md-1 hide">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Locks') }}
                </th>
                <th class="col-md-1 hide">
                    <i class="fa fa-users text-muted"></i><br> {{ $t('Auxiliaries') }}
                </th>
                <th class="col-md-1 hide">
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
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Total by turn') }}
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i> <i class="fa fa-exchange text-muted"></i><br> {{ $t('Falls') }}
                </th>
				<th class="col-md-2">
					<i class="fa fa-dollar text-muted"></i><br> {{ $t('Bonuses') }}
				</th>
                <th class="col-md-3">
                    <i class="fa fa-dollar text-muted"></i><br> {{ $t('Commissions by turn') }}
                </th>
            </tr>
            </thead>
            <tbody>
            <tr class="" v-for="mark in marks">
                <td class="text-center">{{ mark.number }}</td>
                <td class="col-md-2 text-center">
                    <span>{{ mark.turn.route.name }}</span><br>
                    <span class="span-full badge badge-info" v-if="mark.trajectory">
                        {{ mark.trajectory.name }}
                    </span>
                    <span class="tooltips" :data-title="$t('Initial time')">{{ mark.initialTime }}</span> - <span class="tooltips" :data-title="$t('Final time')">{{ mark.finalTime }}</span>
                </td>
                <td class="text-center">{{ mark.passengersBEA }}</td>
                <td class="text-center">{{ mark.totalBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ mark.totalGrossBEA + mark.penalty.value | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    <div class="input-icon tooltips m-b-5" data-placement="left" :data-title="'<i class=\'fa fa-angle-double-right font-green\'></i> ' + $t('Pay fall')" data-html="true">
                        <i class="fa fa-angle-double-right font-green"></i> <input type="number" onFocus="this.select()" min="0" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Pay fall')" v-model.number="mark.payFall">
                    </div>
                    <div class="input-icon tooltips" data-placement="left" :data-title="'<i class=\'fa fa-angle-double-left font-yellow\'></i> ' + $t('Get fall')" data-html="true">
                        <i class="fa fa-angle-double-left font-yellow"></i> <input type="number" onFocus="this.select()" min="0" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Get fall')" v-model.number="mark.getFall">
                    </div>
                </td>
				<td class="text-center">
                    <div class="input-icon m-b-5">
                        <i class="fa fa-dollar font-green"></i> <input type="number" min="0" onFocus="this.select()" :disabled="readonly" class="form-control input-sm" :placeholder="$t('Bonuses')" v-model.number="mark.payFall">
                    </div>
                </td>
                <td class="text-center col-md-3">
                    <span class="tooltips span-commission" :data-original-title="getCommissionTitle(mark.commission)">
                        <i :class="getCommissionIconClass(mark.commission)"></i> {{ mark.commission.value | numberFormat('$0,0') }}
                    </span><br>
                </td>
            </tr>
            <tr>
                <td colspan="9" style="height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr class="totals">
                <td colspan="2" class="text-right">
                    <i class="icon-layers"></i> {{ $t('Totals') }}
                </td>
                <td class="text-center">{{ totals.totalPassengersBea }}</td>
                <td class="text-center">{{ totals.totalBea | numberFormat('$0,0') }}</td>
                <td class="text-center">{{ totals.totalGrossBea | numberFormat('$0,0') }}</td>
                <td class="text-center text-bold">{{ totals.totalGrossBea + totals.totalPenalties | numberFormat('$0,0') }}</td>
                <td class="text-center">
                    <div class="tooltips" :data-title="$t('Pay fall')">
                        <i class="fa fa-angle-double-right font-green"></i> {{ totals.totalPayFall | numberFormat('$0') }}
                    </div>
                    <div class="tooltips" :data-title="$t('Get fall')">
                        <i class="fa fa-angle-double-left font-blue"></i> {{ totals.totalGetFall | numberFormat('$0') }}
                    </div>
                </td>
                <td class="text-center">
                    {{ totals.totalCommissions | numberFormat('$0,0') }}
                </td>
            </tr>
            </tbody>
        </table>

        <div class="form form-horizontal total-discount">
            <hr class="hr">
            <div class="form-group">
                <div class="col-md-9 col-lg-9 col-sm-9 col-xs-12 text-right">
                    <span class="text-bold">
                        <i class="icon-tag"></i> {{ $t('Total commissions') }}:
                    </span>
                </div>
                <div class="col-md-3 col-lg-3 col-sm-3 col-xs-12 text-right">
                    <span class="text-bold">{{ totals.totalCommissions | numberFormat('$0,0') }}</span>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        name: "CommissionComponent",
        props: {
            marks: Array,
            totals: Object,
            readonly: Boolean
        },
        methods: {
            getCommissionTitle: (commission) => {
                return commission.type === 'fixed' ? ('Fixed value per passenger') + ': $' + commission.baseValue : ('Percent of Gross BEA') + ': ' + commission.baseValue + '%';
            },
            getCommissionIconClass: (commission) => {
                return commission.type === 'fixed' ? 'icon-user': 'icon-pie-chart';
            }
        },
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
    .span-commission i{
        margin-right: 5px !important;
    }
</style>