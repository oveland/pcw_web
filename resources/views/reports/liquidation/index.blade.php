@extends( $hideMenu ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <!-- FUELUX - WIZARD -->
    <link href="{{ asset('assets/fuelux/css/fuelux.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/icheck/skins/all.css')  }}" rel="stylesheet" type="text/css" />
    <style>
        .nav.nav-pills>li>a {
            color: #0b465a;
            border: none !important;
        }

        .nav.nav-pills>li.active>a {
            color:white !important;
        }

        .fa{
            z-index:1 !important;
        }

        .swal2-container{
            z-index: 10000 !important;
        }

        .v--modal-overlay{
            display: inline-table !important;
        }

        .portlet-title.tabbable-line {
            margin: 0 !important;
        }

        .mt-element-step .step-line .mt-step-col {
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }

        .mt-element-step .step-line .mt-step-title {
            font-size: unset !important;
            font-weight: bold !important;
        }

        .modal-content, .portlet.light {
            border-radius: 30px !important;
        }

        .modal-open .modal {
            background: rgba(0, 0, 0, 0.71) !important;
        }

        .v--modal-overlay{
            background: #111d1ef0 !important;
            border-radius: 30px !important;
        }
    </style>
@endsection

@section('content')
    <!-- begin row -->
    <div id="report-liquidation" class="row p-t-10">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('reports.liquidation.params',['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->

        <div v-show="search.vehicle" class="">
            <div class="col-md-12">
                <report-component ref="report" :search-params="searchParams" :search="search" url-report="{{ route('reports.liquidation.search') }}" url-export="{{ route('reports.liquidation.export') }}"></report-component>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <script src="{{ mix('resources/js/reports/liquidation/main.js') }}" type="application/ecmascript"></script>
    <script src="{{ asset('assets/global/plugins/icheck/icheck.min.js') }}" type="text/javascript"></script>

    <script type="application/ecmascript">
        $('.menu-takings-passengers, .menu-takings-passengers-liquidation').addClass('active-animated');
        $(document).ready(function () {
            setTimeout(()=>{
                $('.btn-group-modules .btn-tab:nth-child(2)').click();
            },1000);

            $("#modal-params-manager").on('hidden.bs.modal', function () {
                // $('.btn-search-report').click();
            });
        });
    </script>
@endsection