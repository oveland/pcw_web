@extends('layout')

@section('stylesheets')
    <!-- FUELUX - WIZARD -->
    <link href="{{ asset('assets/fuelux/css/fuelux.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .nav.nav-pills>li>a {
            color: #0b465a;
            border: none !important;
        }
        .nav.nav-pills>li.active>a {
            color:white !important;
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
    <div id="liquidation" class="row" url="{{ route('takings-passengers-liquidation-search') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component url-params="{{ route('takings-passengers-liquidation-params-search') }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet light ">
                <div class="portlet-title">
                    <div class="">
                        <div class="btn-group btn-group-devided width-full" data-toggle="buttons">
                            <label class="btn btn-tab btn-transparent green-sharp btn-outline pull-left btn-circle uppercase active" data-toggle="tab" data-target="#table-liquidations"
                                onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                <i class="fa fa-file-text"></i> @lang('Liquidation')
                            </label>
                            <label class="btn btn-tab btn-transparent yellow-crusta btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings"
                                onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                <i class="fa fa-suitcase"></i> @lang('Takings')
                            </label>
                            <label class="btn blue-hoki btn-outline btn-circle pull-right uppercase" data-toggle="modal" data-target="#modal-params-manager">
                                <i class="fa fa-cogs"></i> @lang('Admin')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="portlet-body">
                    <div class="tab-content panel p-0">
                        <div id="table-liquidations" class="tab-pane fade active in">
                            <liquidation-component url-liquidate="{{ route('takings-passengers-liquidation-liquidate') }}" :marks="marks" :search="search" :totals="totals" v-on:refresh-report="searchReport($event)"></liquidation-component>
                        </div>
                        <div id="table-takings" class="tab-pane fade">
                            <takings-component :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search') }}" url-takings="" url-export="{{ route('takings-passengers-liquidation-export') }}"></takings-component>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-params-manager" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <admin-component url-params="{{  route('takings-passengers-liquidation-params') }}"></admin-component>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script src="{{ mix('resources/js/takings/passengers/liquidation/main.js') }}" type="application/ecmascript"></script>

    <script type="application/ecmascript">
        $('.menu-takings-passengers, .menu-takings-passengers-liquidation').addClass('active-animated');
        @if(!Auth::user()->isAdmin())
            //$(document).ready(loadSelectRouteReport(null));
        @endif
    </script>
@endsection