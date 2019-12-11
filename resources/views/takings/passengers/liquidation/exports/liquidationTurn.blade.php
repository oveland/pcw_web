@php
    function asDollars($value) {
      return '$' . number_format($value, 0);
    }

    $totals = $liquidation->totals;
    $liquidationBYTurn = (object) collect($liquidation->liquidation->byTurns)->where('markId', $mark->id)->first();
@endphp
<style>
    @import url('https://fonts.googleapis.com/css?family=Bai+Jamjuree&display=swap');
    * {
        font-family: 'Bai Jamjuree', sans-serif !important;
        font-weight: normal;
    }

    .pull-right{
        float: right !important;
    }

    .pull-left{
        float: left !important;
    }

    .text-bold{

    }
    hr{
        border: rgba(11, 18, 11, 0.36) 1px solid;
    }
    .hr{
        margin-top: 35px !important;
    }
    .m-0{
        margin: 0 !important;
    }
    .p-0{
        padding: 0 !important;
    }
    .totals, .total-liquidation{
        height: 10px;
        width: 100% !important;
        clear: bottom;
    }
    h4.totals{
        padding-left: 20px !important;
    }
    h4.totals span{
        font-size: 0.9em !important;
    }
</style>
<body>
<div class="form form-horizontal">
    <h3 class="text-bold search p-b-15">
            <span class="pull-left">
                @lang('TAKING RECEIPT')
            </span>
        <span class="text-bold pull-right">#{{ $liquidation->id }}</span>
    </h3>
    <h3 class="search m-0" style="text-align: center;clear: both">
            <span class="text-bold p-0" style="margin-bottom: 0">
                @lang('Vehicle') {{ $liquidation->vehicle->number }} | {{ $liquidation->vehicle->plate }}
            </span>
    </h3>
    <h4 class="search m-0" style="text-align: center">
        <span class="text-bold">@lang('Turn'):</span> {{ $mark->number }}
        <span>{{ $mark->trajectory->name }}</span>
    </h4>
    <h4 class="search m-0" style="text-align: center">
        <span class="text-bold">@lang('Date'):</span> {{ $liquidation->date->toDateString() }}
        <span>({{ $mark->initialTime->totimeString() }} @lang('to') {{ $mark->finalTime->totimeString() }})</span>
    </h4>
    <hr class="">
    <h5 class="search m-0" style="text-align: right;clear: both;text-align: center">
        <span>@lang('Printed at') {{ \Carbon\Carbon::now() }}</span>
        <br>
        <span>@lang('Liquidated at') {{ $liquidation->created_at }}</span>
    </h5>

    <hr class="">

    <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar"></i> @lang('Total turns')
            </span>
        <span class="pull-right text-bold col-md-4">{{ asDollars($liquidationBYTurn->totalTurn) }}</span>
    </h3>
    <h4 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total pay fall')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($liquidationBYTurn->payFall) }}</span>
    </h4>
    <h4 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total get fall')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($liquidationBYTurn->getFall) }}</span>
    </h4>


    <h3 class="totals">
            <span class="text-bold">
                <i class="icon-tag"></i> @lang('Subtotal')
            </span>
        <span class="pull-right text-bold col-md-4">{{ asDollars($liquidationBYTurn->subTotalTurn) }}</span>
    </h3>
    <h4 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total tolls')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($liquidationBYTurn->turnDiscounts['byTolls']) }}</span>
    </h4>
    <h4 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total commissions')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($mark->commission->value) }}</span>
    </h4>
    <h4 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total operative expenses')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($liquidationBYTurn->turnDiscounts['byOperativeExpenses']) }}</span>
    </h4>

    <h3 class="totals">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> @lang('Total dispatch')
            </span>
        <span class="pull-right text-bold col-md-4">{{ asDollars($liquidationBYTurn->totalDispatch) }}</span>
    </h3>

    <hr class="hr">
    <h3 class="totals">
            <span class="">
                <i class="fa fa-dollar"></i> @lang('Total fuel')
            </span>
        <span class="pull-right col-md-4">{{ asDollars($liquidationBYTurn->turnDiscounts['byFuel']) }}</span>
    </h3>
    <br>
    <h3 class="total-liquidation">
            <span class="text-bold">
                @lang('Balance')
            </span>
        <span class="pull-right text-bold">{{ asDollars($liquidationBYTurn->balance) }}</span>
    </h3>

    <hr class="hr">

    <h5 class="totals">
            <span class="">
                @lang('Total locks'):
            </span>
        <span class="">{{ $mark->locks }}</span>
        <br>
        <span class="">
                @lang('Total exempts'):
            </span>
        <span class="">{{ $mark->auxiliaries }}</span>
</div>
</body>


