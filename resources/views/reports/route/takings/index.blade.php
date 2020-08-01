@extends( $hideMenu ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Takings')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-area-chart" aria-hidden="true"></i> @lang('Reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Takings')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="reports-takings-container" class="row" url="{{ route('reports.routes.takings.search') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('reports.routes.takings.params.get', ['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->

        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet light p-15">
                <div class="portlet-body">

                    <div class="portlet-title">
                        <div class="">
                            <div class="btn-group btn-group-devided width-full btn-group-modules" data-toggle="buttons">
                                <label class="btn btn-tab btn-transparent yellow-crusta btn-outline pull-left btn-circle uppercase active" data-toggle="tab" data-target="#report-list"
                                       onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                    <i class="icon-layers"></i> <span class="hidden-xs">@lang('List')</span>
                                </label>
                                <label class="btn btn-tab btn-transparent blue btn-outline pull-left btn-circle uppercase" data-toggle="tab" data-target="#report-graphs"
                                       onclick="$('.btn-tab').removeClass('active');$(this).addClass('active')">
                                    <i class="fa fa-suitcase"></i> <span class="hidden-xs">@lang('Graphs')</span> <i class="fa fa-cog fa-spin"></i>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="portlet-body row">
                        <div class="tab-content" style="border-top: 1px solid #e7e4e4">
                            <div id="report-list" class="tab-pane fade active in">
                                <table-component :report="report" :totals="totals" :averages="averages"></table-component>
                            </div>
                            <div id="report-graphs" class="tab-pane fade row" >
                                <div class="col-md-6 col-md-offset-3">
                                    <graph-carrousel-component class="col-md-12"></graph-carrousel-component>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-example" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4><i class="fa fa-camera"></i> @lang('Example modal')</h4>
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
    <script src="{{ mix('resources/js/reports/routes/takings/main.js') }}" type="application/ecmascript"></script>

    <script type="application/ecmascript">
        $('.menu-routes, .menu-routes-takings').addClass('active-animated');
    </script>
@endsection