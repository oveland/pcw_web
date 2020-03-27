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
    </style>
@endsection

@section('content')
    <!-- begin row -->
    <div id="liquidation" class="row p-t-10" url="{{ route('takings-passengers-search-liquidation') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('takings-passengers-liquidation-params',['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->

        <div v-show="search.vehicle" class="">
            <div class="tab-content">
                <div class="tab-pane fade active in" id="tab-takings">
                    <div class="report-container col-md-12" style="display: none">
                        <div class="portlet light ">
                            <div class="portlet-title">
                                <div class="">
                                    <div class="btn-group btn-group-devided width-full btn-group-modules" data-toggle="buttons">
                                        @if( Auth::user()->canML('liquidate') )
                                            <label class="btn btn-tab btn-transparent yellow-crusta btn-outline pull-left btn-circle uppercase active" data-toggle="tab" data-target="#table-liquidations"
                                                   onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                                <i class="icon-layers"></i> <span class="hidden-xs">@lang('Liquidation')</span>
                                            </label>
                                        @endif
                                        @if( Auth::user()->canML('takings') )
                                            <label class="btn btn-tab btn-transparent blue btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings"
                                                   onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                                <i class="fa fa-suitcase"></i> <span class="hidden-xs">@lang('Takings')</span>
                                            </label>
                                        @endif
                                        @if( Auth::user()->canML('takings-list') )
                                            <label class="btn btn-tab btn-transparent green-meadow btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings-list"
                                                   onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                                <i class="fa fa-check-circle-o"></i> <span class="hidden-xs">@lang('Takings list')</span>
                                            </label>
                                        @endif
                                        @if( Auth::user()->canML('takings-list') )
                                            <label class="btn btn-tab btn-transparent blue btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-takings-daily-report"
                                                   onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                                <i class="fa fa-file-o"></i> <span class="hidden-xs">@lang('Daily report')</span>
                                            </label>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="tab-content panel p-0">
                                    @if( Auth::user()->canML('liquidate') )
                                        <div id="table-liquidations" class="tab-pane fade active in">
                                            <liquidation-component url-liquidate="{{ route('takings-passengers-liquidation-liquidate') }}" :marks.sync="marks" :liquidation.sync="liquidation" :search="search" :totals="totals" v-on:refresh-report="searchReport($event)"></liquidation-component>
                                        </div>
                                    @endif
                                    @if( Auth::user()->canML('takings') )
                                        <div id="table-takings" class="tab-pane fade">
                                            <takings-component url-update-liquidate="{{ route('takings-passengers-liquidation-update', ['liquidation' => 'ID']) }}" :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search-takings') }}" url-takings="{{ route('taking-passengers-takings', ['liquidation' => 'ID']) }}" url-export="{{ route('takings-passengers-liquidation-export', ['liquidation' => 'ID']) }}" v-on:refresh-report="searchReport($event)"></takings-component>
                                        </div>
                                    @endif
                                    @if( Auth::user()->canML('takings-list') )
                                        <div id="table-takings-list" class="tab-pane fade">
                                            <takings-list-component :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search-takings-list') }}" url-export="{{ route('takings-passengers-liquidation-export', ['liquidation' => 'ID']) }}"></takings-list-component>
                                        </div>
                                    @endif
                                    @if( Auth::user()->canML('takings-list') )
                                        <div id="table-takings-daily-report" class="tab-pane fade">
                                            <daily-report-component :search-params="searchParams" :search="search" url-report="{{ route('takings-passengers-report-daily') }}" url-list="{{ route('takings-passengers-search-takings-list') }}" url-export="{{ route('takings-passengers-report-daily-export') }}"></daily-report-component>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane fade" id="tab-road-safety">
                    <div class="col-md-12">
                        <div class="portlet light ">
                            <div class="portlet-title">
                                <div class="">
                                    <div class="btn-group btn-group-devided width-full btn-group-modules" data-toggle="buttons">
                                        <label class="btn btn-tab btn-transparent green-meadow btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#table-rf-takings-list"
                                               onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                            <i class="fa fa-check-circle-o"></i> <span class="hidden-xs">@lang('Turnos recaudados')</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="portlet-body">
                                <div class="tab-content panel p-0">
                                    <div id="table-rf-takings-list" class="tab-pane fade active in">
                                        <road-safety-takings-turns-component :search-params="searchParams" :search="search" url-report="{{ route('takings-passengers-report-daily') }}" url-list="{{ route('takings-passengers-search-takings-list') }}" url-costs="{{  route('takings-passengers-liquidation-params',['name' => __('costs')]) }}" url-export="{{ route('takings-passengers-report-daily-export') }}"></road-safety-takings-turns-component>
                                        <div class="hide">
                                            <road-safety-takings-component :search-params="searchParams" :search="search" url-list="{{ route('takings-passengers-search-takings-list') }}" url-costs="{{  route('takings-passengers-liquidation-params',['name' => __('costs')]) }}"></road-safety-takings-component>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade" id="modal-params-manager" tabindex="-1" role="basic" aria-hidden="true" data-backdrop="static">
            <div class="modal-dialog modal-lg" style="width: 80%">
                <div class="modal-content">
                    <admin-component :search-params="searchParams" url-params="{{  route('takings-passengers-liquidation-params',['name' => __('all')]) }}" v-on:refresh-report="searchReport($event)" :vehicle="search.vehicle"></admin-component>
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