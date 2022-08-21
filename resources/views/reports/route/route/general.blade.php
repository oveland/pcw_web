@extends('layouts.app')

@section('stylesheets')
    <style>
        .portlet.portlet-fullscreen {
            z-index: 10000 !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light bordered p-5">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa fa-flag"></i>
                        <span class="caption-subject bold font-grey-gallery uppercase">@lang('Route report')</span>
                        <span class="caption-helper"><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('General')</span>
                    </div>
                    <div class="actions">
                        <a class="btn btn-circle btn-icon-only btn-default btn-show-chart-route-report tooltips" href="javascript:;"
                           data-title="@lang('Refresh')" data-placement="bottom"
                           data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}?centerOnLocation={{ $locationId }}"
                           data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}">
                            <i class="fa fa-refresh"></i>
                        </a>

                        <a class="btn btn-circle btn-icon-only btn-default fullscreen" href="javascript:;" data-original-title="@lang('View')" data-placement="bottom"> </a>
                    </div>
                </div>

                <div class="portlet-body p-1">
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
        $('.menu-routes, .menu-route-report').addClass('active-animated');

        $(document).ready(function(){
            initializeMap();

            setTimeout(()=>{
                hideSideBar();
                $('.fullscreen').click();
                setTimeout(()=>{
                    $('.btn-show-chart-route-report').click();
                },1000);
            },500);
        });
    </script>
@endsection
