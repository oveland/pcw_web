@php
    function asDollars($value) {
      return '$' . number_format($value, 0);
    }

    $totals = $report->totals;
    $marks = collect($report->marks);
    $totals->payRollCost = $marks->sum('payRollCost');
@endphp
<style>
    @import url('https://fonts.googleapis.com/css?family=Bai+Jamjuree&display=swap');
    * {
        font-family: Consolas, Monaco, Andale Mono, Ubuntu Mono, monospace;
    }

    .uppercase{
        text-transform: uppercase;
    }

    .m-0{
        margin: 0;
    }

    .pull-right{
        float: right !important;
    }

    .pull-left{
        float: left !important;
    }

    hr{
        border: rgba(11, 18, 11, 0.36) 1px solid;
    }
    .hr{
        margin-top: 35px !important;
    }

    .table-bordered td, .table-bordered th{
        border-bottom: 1px solid lightgrey;
    }

    .text-right{
        text-align: right;
    }

    .text-center, th{
        padding: 10px;
        text-align: center !important;
    }

    tr.inverse th{
        padding: 200px;
        background: #333333;
        color: white;
    }
    table *{
        font-size: 0.8em;
    }
</style>
<body>
<div class="form form-horizontal">
    <hr class="">
    @if(true)
        <h2 class="search m-0" style="text-align: center;clear: both">
            <span class="text-bold p-0 uppercase" style="margin-bottom: 0">
                @lang('Daily report taking')
            </span>
        </h2>
    <h3 class="search m-0" style="text-align: center;clear: both">
            <span class="text-bold p-0" style="margin-bottom: 0">
                @lang('Vehicle') {{ $vehicle->number }} | {{ $vehicle->plate }}
            </span>
    </h3>
    <h4 class="search m-0" style="text-align: center">
        <span class="text-bold">@lang('Date'):</span> {{ $date }}
        <span>({{ $marks->first()->initialTime }} @lang('to') {{ $marks->last()->finalTime }})</span>
    </h4>
    <hr class="">
    <h5 class="search m-0" style="text-align: right;clear: both;text-align: center">
        <span>@lang('Printed at') {{ \Carbon\Carbon::now() }}</span>
    </h5>
    @endif

    <hr class="">

    <div>
        <div v-if="marks.length">
            <div class="row">
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th width="3%">
                                {{ __('Turn')  }}
                            </th>
                            <th width="10%">
                                {{ __('Trajectory') }}
                            </th>
                            <th width="15%">
                                {{ __('Time') }}
                            </th>
                            <th>
                                {{ __('BEA') }}
                            </th>
                            <th>
                                {{ __('Fuel') }}
                            </th>
                            <th>
                                {{ __('Tolls') }}
                            </th>
                            <th>
                                {{ __('Commissions') }}
                            </th>
                            <th>
                                {{ __('Pay fall') }}
                            </th>
                            <th>
                                {{ __('Get fall') }}
                            </th>
                            <th>
                                {{ __('Operative expenses') }}
                            </th>
                            <th>
                                {{ __('Passengers') }}
                            </th>
                            <th>
                                {{ __('Auxiliaries') }}
                            </th>
                            <th>
                                {{ __('Locks') }}
                            </th>
                            <th>
                                {{ __('Boarded') }}
                            </th>
                            <th>
                                {{ __('Penalty') }}
                            </th>
                            <th class="col-md-1">
                                {{ __('Liquidate') }}
                            </th>
                            <th class="col-md-1">
                                {{ __('Payroll cost') }}
                            </th>
                            <th class="col-md-1">
                                {{ __('Net to car') }}
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($marks as $mark)
                            @php
                                $turn = (object) collect($report->details['byTurns'])->filter(function($d) use ($mark){
                                    return $d['markId'] === $mark->id;
                                })->first();

                            @endphp
                        <tr v-for="mark in marks" :set="turn = getLiquidationTurn(mark)">
                            <td class="text-center">
                                <small>{{ $mark->number }}</small>
                            </td>
                            <td class="text-center">
                                <small class="span-full badge badge-info" v-if="$mark->trajectory">
                                    {{ $mark->trajectory->name }}
                                </small>
                            </td>
                            <td class="text-center">
                                <small>{{ $mark->initialTime }}<br>{{ $mark->finalTime }}</small>
                            </td>

                            <td class="text-center">{{ asDollars($mark->totalBEA) }}</td>
                            <td class="text-center">{{ asDollars($turn->turnDiscounts['byFuel']) }}</td>
                            <td class="text-center">{{ asDollars($turn->turnDiscounts['byTolls']) }}</td>
                            <td class="text-center">{{ asDollars($mark->commission->value) }}</td>
                            <td class="text-center">{{ asDollars($turn->payFall) }}</td>
                            <td class="text-center">{{ asDollars($turn->getFall) }}</td>
                            <td class="text-center">{{ asDollars($turn->turnDiscounts['byOperativeExpenses']) }}</td>
                            <td class="text-center">{{ $mark->passengersBEA }}</td>
                            <td class="text-center">{{ $mark->auxiliaries }}</td>
                            <td class="text-center">{{ $mark->locks }}</td>
                            <td class="text-center">{{ $mark->boarded }}</td>
                            <td class="text-center">{{ asDollars($mark->penalty->value) }}</td>
                            <td class="text-center">{{ asDollars($turn->totalDispatch) }}</td>
                            <td class="text-center">{{ asDollars($mark->payRollCost) }}</td>
                            <td class="text-right">{{ asDollars(intval($turn->totalDispatch - $mark->payRollCost - $turn->turnDiscounts['byFuel'] + $turn->getFall) ) }}</td>
                        </tr>
                        @endforeach
                        <tr class="inverse">
                            <th colspan="3" class="text-right">
                                {{ __('Total') }}
                            </th>
                            <th class="text-center">{{ asDollars($totals->totalBea) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalDiscountByFuel) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalDiscountByTolls) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalCommissions) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalPayFall) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalGetFall) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalDiscountByOperativeExpenses) }}</th>
                            <th class="text-center">{{ $totals->totalPassengersBea }}</th>
                            <th class="text-center">{{ $totals->totalAuxiliaries }}</th>
                            <th class="text-center">{{ $totals->totalLocks }}</th>
                            <th class="text-center">{{ $totals->totalBoarded }}</th>
                            <th class="text-center">{{ asDollars($totals->totalPenalties) }}</th>
                            <th class="text-center">{{ asDollars($totals->totalDispatch) }}</th>
                            <th class="text-center">{{ asDollars($totals->payRollCost) }}</th>
                            <th class="text-right" style="background: black !important;font-size: 1em">{{ asDollars(intval($totals->totalDispatch - $totals->payRollCost - $totals->totalDiscountByFuel + $totals->totalGetFall)) }}</th>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</body>


