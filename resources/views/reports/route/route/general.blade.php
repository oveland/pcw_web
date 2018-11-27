@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('General')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('General')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime pull-right btn-full-screen"
                   style="right: 20px;position: absolute;top: 10px;z-index: 1;"
                   data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-info pull-right btn-show-chart-route-report tooltips"
                   style="right: 55px;position: absolute;top: 10px;z-index: 1;"
                   data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}?centerOnLocation={{ $location->id }}"
                   data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}"
                   data-original-title="@lang('Refresh')"
                   data-placement="bottom">
                    <i class="fa fa-refresh"></i>
                </a>
                <div class="panel-body p-1">
                    <!-- Include template for show modal report with char and historic route coordinates -->
                @include('reports.route.route.templates.chart._chartView')
                @include('reports.route.route.templates.chart._chartAssets')
                <!-- end template -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script type="application/javascript">
        $('.menu-routes, .menu-route-report').addClass('active');

        $(document).ready(function(){
            initializeMap();

            setTimeout(()=>{
                hideSideBar();
                $('.btn-full-screen, .btn-show-chart-route-report').click();
            },500);
        });
    </script>
@endsection
