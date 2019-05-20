@php
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
</style>
<body>
    <div class="form form-horizontal">
        <h4 class="search p-b-15">
            <span class="pull-left">
                BEA | Comprobante de Recaudo
            </span>
            <span class="text-bold pull-right">#{{ $liquidation->id }}</span>
        </h4>
        <br>
        <h3 class="search p-b-15">
            <span class="text-bold">
                <i class="fa fa-bus"></i> {{ $liquidation->vehicle->number }} | {{ $liquidation->vehicle->plate }}
            </span>
            <span class="pull-right">
                <i class="fa fa-calendar"></i> {{ $liquidation->date }}
            </span>
        </h3>
        <hr class="hr">
        <h3 class="totals">
            <span class="text-bold">
                <i class="fa fa-dollar"></i> Total BEA
            </span>
            <span class="pull-right col-md-4">{{ asDollars($liquidation->liquidation->totalBea) }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="icon-tag"></i> Total discounts
            </span>
            <span class="pull-right col-md-4">- {{ asDollars($liquidation->liquidation->totalDiscounts) }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class=" icon-user-follow"></i> Total commissions
            </span>
            <span class="pull-right col-md-4">- {{ asDollars($liquidation->liquidation->totalCommissions) }}</span>
        </h3>
        <h3 class="totals">
            <span class="text-bold">
                <i class="icon-shield"></i> Total penalties
            </span>
            <span class="pull-right col-md-4">{{ asDollars($liquidation->liquidation->totalPenalties) }}</span>
        </h3>
        <hr class="hr">
        <h2 class="total-liquidation">
            <span class="text-bold">
                Total to liquidate
            </span>
            <span class="pull-right text-bold">{{ asDollars($liquidation->total) }}</span>
        </h2>
    </div>
</body>


