@extends( 'layout')

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
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Apps')</a></li>
        <li class="active">@lang('Control')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Apps')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="report-apps" class="row" url="{{ route('report.app.search') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('report.app.params.get', ['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet light ">
                <div class="portlet-body">

                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-apps" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4><i class="fa fa-camera"></i> @lang('Apps modal')</h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button class="btn width-100 btn-default" data-dismiss="modal">
                            @lang('Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <script src="{{ mix('resources/js/reports/apps/main.js') }}" type="application/ecmascript"></script>
    <script src="{{ asset('assets/global/plugins/icheck/icheck.min.js') }}" type="text/javascript"></script>


    <script type="application/ecmascript">
        $('.menu-apps, .menu-apps-report').addClass('active-animated');
        $(document).ready(function () {
            setTimeout(()=>{
                $('.btn-group-modules .btn-tab:first').click();
            },1000);

            $("#modal-params-manager").on('hidden.bs.modal', function () {
                $('.btn-search-report').click();
            });
        });
    </script>
@endsection