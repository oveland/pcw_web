@php
    $marks = $liquidation->marks;

    function asDollars($value) {
      return '$' . number_format($value, 0);
    }
@endphp
<style>
    /* latin-ext */
    @font-face {
        font-family: 'Lato';
        font-style: normal;
        font-weight: 400;
        src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v15/S6uyw4BMUTPHjxAwXjeu.woff2) format('woff2');
        unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
    }

    /* latin */
    @font-face {
        font-family: 'Lato';
        font-style: normal;
        font-weight: 400;
        src: local('Lato Regular'), local('Lato-Regular'), url(https://fonts.gstatic.com/s/lato/v15/S6uyw4BMUTPHjx4wXg.woff2) format('woff2');
        unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
    }
    * {
        font-family: 'Lato', sans-serif !important;
        font-weight: normal;
    }

    .pull-right{
        float: right !important;
    }

    .pull-left{
        float: left !important;
    }

    .text-bold{
        font-weight: bold !important;
    }
    .m-0{
        margin: 0 !important;
    }
    .p-0{
        padding: 0 !important;
    }
    .col{
        width: 100%;
        clear: both;
    }
    .totals{
        font-size: 1em;
        margin: 0;
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
            <span class="text-bold">@lang('Date'):</span> {{ $liquidation->date->toDateString() }}
            <span>({{ $liquidation->firstMark->initialTime->totimeString() }} @lang('to') {{ $liquidation->lastMark->finalTime->totimeString() }})</span>
        </h4>
        <hr class="hr">
        <h5 class="search m-0" style="text-align: right;clear: both;text-align: center">
            <span>@lang('Printed at') {{ \Carbon\Carbon::now() }}</span>
            <br>
            <span>@lang('Liquidated at') {{ $liquidation->created_at }}</span>
        </h5>
        <hr class="col">
        <div class="col">
            <h3 class="totals">
                <span class="text-bold">
                    <i class="fa fa-dollar"></i> @lang('Total BEA')
                </span>
                <span class="pull-right col-md-4 text-bold">{{ asDollars($liquidation->liquidation->totalBea) }}</span>
            </h3>
        </div>
        <hr class="col">
        <div class="col">
            <h3 class="totals">
                <span class="text-bold">
                    <i class="fa fa-dollar"></i> @lang('Total Gross BEA')
                </span>
                <span class="pull-right col-md-4 text-bold">{{ asDollars($liquidation->liquidation->totalGrossBea) }}</span>
            </h3>
        </div>
        <hr class="col">
        <div class="col">
            <h3 class="totals">
                <span class="text-bold">
                    <i class="icon-tag"></i> Total @lang('Discounts')
                </span>
                <span class="pull-right text-bold">
                    - {{ asDollars($liquidation->liquidation->totalDiscounts) }}
                </span>
            </h3>
            <div class="col">
                <hr style="width: 90%;margin: auto">
                <table style="width: 90%;margin: auto">
                    <tbody>
                    @foreach($marks as $mark)
                        @php
                            $mark = $mark->getAPIFields();
                            $discounts = $mark->discounts;
                            $trajectory = (object) $mark->trajectory;
                        @endphp

                        <tr>
                            <td width="30%" rowspan="{{ count($discounts) + 1 }}">
                                <small style="font-size: 0.7em !important;">{{ $trajectory->name }}</small>
                            </td>
                        </tr>

                        @foreach($discounts as $discount)
                            @php
                                $discount = (object) $discount;
                                $discountType = (object) $discount->discount_type;
                            @endphp
                            <tr>
                                <td>
                                    <small>{{ __(ucfirst($discountType->name)) }}</small>
                                </td>
                                <td>
                                    <small class="pull-right">
                                        - {{ asDollars($discount->value) }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="3" style="text-align: center"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <hr style="width: 90%;margin: auto">
                <table style="width: 90%;margin: auto">
                    <thead>
                    <tr>
                        <th colspan="3">
                            <span class="col text-bold" style="text-align: left">
                                <i class="icon-tag"></i> @lang('Other discounts'):
                            </span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($liquidation->liquidation->otherDiscounts as $otherDiscount)
                        @php
                            $otherDiscount = (object) $otherDiscount;
                        @endphp
                        <tr>
                            <td width="30%"></td>
                            <td>
                                <small>{{ __(ucfirst($otherDiscount->name)) }}</small>
                            </td>
                            <td>
                                <small class="pull-right">
                                    - {{ asDollars($otherDiscount->value) }}
                                </small>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="col">
        <div class="col">
            <h3 class="totals">
                <span class="text-bold">
                    <i class="icon-user-follow"></i> Total @lang('Commissions')
                </span>
                <span class="pull-right text-bold">
                    - {{ asDollars($liquidation->liquidation->totalCommissions) }}
                </span>
            </h3>
            <div class="col">
                <hr class="col" style="margin-left: 5%;margin-right: 5%">
                <table style="width: 90%;margin: auto">
                    <tbody>
                    @foreach($marks as $mark)
                        @php
                            $mark = $mark->getAPIFields();
                            $trajectory = (object) $mark->trajectory;
                            $commission = $mark->commission;
                        @endphp

                        <tr>
                            <td width="30%">
                                <small style="font-size: 0.7em !important;">{{ $trajectory->name }}</small>
                            </td>
                            <td>
                                <small>{{ __(ucfirst($commission->type)) }} ({{ $commission->baseValue }})</small>
                            </td>
                            <td>
                                <small class="pull-right">
                                    - {{ asDollars($commission->value) }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="col">
        <div class="col">
            <h3 class="totals">
                <span class="text-bold">
                    <i class="icon-user-follow"></i> Total @lang('Penalties')
                </span>
                <span class="pull-right text-bold">
                    {{ asDollars($liquidation->liquidation->totalPenalties) }}
                </span>
            </h3>
            <div class="col">
                <hr class="col" style="margin-left: 5%;margin-right: 5%">
                <table style="width: 90%;margin: auto">
                    <tbody>
                    @foreach($marks as $mark)
                        @php
                            $mark = $mark->getAPIFields();
                            $trajectory = (object) $mark->trajectory;
                            $penalty = $mark->penalty;
                        @endphp

                        <tr>
                            <td width="30%">
                                <small style="font-size: 0.7em !important;">{{ $trajectory->name }}</small>
                            </td>
                            <td>
                                <small>{{ __(ucfirst($penalty->type)) }} (${{ $penalty->baseValue }})</small>
                            </td>
                            <td>
                                <small class="pull-right">
                                    {{ asDollars($penalty->value) }}
                                </small>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: center"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <hr class="col">
        <h2 class="total-liquidation">
            <span class="text-bold">
                @lang('Total liquidation')
            </span>
            <span class="pull-right text-bold">{{ asDollars($liquidation->total) }}</span>
        </h2>
    </div>
</body>


