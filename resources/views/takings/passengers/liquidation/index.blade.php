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
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Takings')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Liquidation')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Takings')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Liquidation')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="liquidation" class="row" url="{{ route('takings-passengers-search-liquidation') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component url-params="{{ route('takings-passengers-liquidation-params',['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12" style="display: none">
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="">
                        <div class="btn-group btn-group-devided width-full" data-toggle="buttons">
                            <label class="btn btn-tab btn-transparent yellow-crusta btn-outline pull-left btn-circle uppercase active" data-toggle="tab" data-target="#table-liquidations"
                                onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                <i class="icon-layers"></i> <span class="hidden-xs">@lang('Liquidation')</span>
                            </label>
                            <label class="btn btn-tab btn-transparent blue btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings"
                                onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                <i class="fa fa-suitcase"></i> <span class="hidden-xs">@lang('Takings')</span>
                            </label>
                            <label class="btn btn-tab btn-transparent green-meadow btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings-list"
                                   onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                <i class="fa fa-check-circle-o"></i> <span class="hidden-xs">@lang('Takings list')</span>
                            </label>
                            <label class="btn blue-hoki btn-outline btn-circle pull-right uppercase" data-toggle="modal" data-target="#modal-params-manager">
                                <i class="fa fa-cogs"></i> <span class="hidden-xs">@lang('Admin')</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-content panel p-0">
                        <div id="table-liquidations" class="tab-pane fade active in">
                            <liquidation-component url-liquidate="{{ route('takings-passengers-liquidation-liquidate') }}" :marks.sync="marks" :liquidation.sync="liquidation" :search="search" :totals="totals" v-on:refresh-report="searchReport($event)"></liquidation-component>
                        </div>
                        <div id="table-takings" class="tab-pane fade">
                            <takings-component :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search-takings') }}" url-takings="{{ route('taking-passengers-takings', ['liquidation' => 'ID']) }}" url-export="{{ route('takings-passengers-liquidation-export') }}" v-on:refresh-report="searchReport($event)"></takings-component>
                        </div>
                        <div id="table-takings-list" class="tab-pane fade">
                            <takings-list-component :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search-takings-list') }}" url-takings="" url-export="{{ route('takings-passengers-liquidation-export') }}"></takings-list-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-params-manager" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content">
                    <admin-component url-params="{{  route('takings-passengers-liquidation-params',['name' => __('all')]) }}" v-on:refresh-report="searchReport($event)" :vehicle="search.vehicle"></admin-component>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <script src="{{ mix('resources/js/takings/passengers/liquidation/main.js') }}" type="application/ecmascript"></script>
    <script src="{{ asset('assets/global/plugins/icheck/icheck.min.js') }}" type="text/javascript"></script>

    <script type="application/ecmascript">
        $('.menu-takings-passengers, .menu-takings-passengers-liquidation').addClass('active-animated');
        @if(!Auth::user()->isAdmin())
            //$(document).ready(loadSelectRouteReport(null));
        @endif
    </script>
@endsection