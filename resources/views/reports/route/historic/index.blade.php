@extends('layout')

@section('stylesheets')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <style>
        .range-reports{
            position:relative;
            z-index:1;
            padding-top:10px;
            background: rgba(0, 12, 35, 0.59);
            color: white;
        }

        .range-reports .irs-bar, .range-reports .irs-bar-edge, .range-reports .irs-single{
            background: #f57c1e;
        }

        #google-map-light-dream{
            position:relative;
            top:-50px
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Historic')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-historic-search') }}">
            <div class="panel panel-inverse">
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company first')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 hide">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report"
                                            class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 hide">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="type-report" name="type-report" type="checkbox" value="group-vehicles" checked> @lang('Group')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="search" class="control-label"></label>
                                <div class="form-group">
                                    <button id="search" type="submit" class="btn btn-success btn-search-report m-t-5">
                                        <i class="fa fa-map-o"></i> @lang('Map')
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="search" class="control-label"></label>
                                <div class="form-group">
                                    <button id="search" type="button" onclick="ginfo('@lang('Feature on development')')" class="btn btn-info btn-search-report-list m-t-5">
                                        <i class="fa fa-list-ul"></i> @lang('List')
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <input id="time-range-report" name="time-range-report" type="text" value="" />
                            <span class="help-block hide"> @lang('Quickly select a time range from 00:00 to 23:59') </span>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="col-md-12 range-reports">
                <input id="range_reports" type="text" />
                <span class="help-block text-white">
                    <i class="fa fa-map-o"></i> <span class="total">0</span> @lang('reports') @lang('between') <i class="fa fa-clock"></i> <span class="time-from">--:--:--</span> - <i class="fa fa-clock"></i> <span class="time-to">--:--:--</span>
                </span>
            </div>
            <div id="google-map-light-dream" class="col-md-12 p-0 map-report-historic" style="height: 1000px"></div>
        </div>
        <!-- end content report -->
    </div>
    <!-- end row -->
@endsection

@section('scripts')

    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/lib/markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    @include('template.google.maps')
    @include('reports.route.historic.templates._script')

    <script type="application/javascript">
        let reportContainer = $('.report-container');
        $('.menu-routes, .menu-report-route-historic').addClass('active-animated');

        $(document).ready(function () {
            initializeMap();

            $('.form-search-report').submit(function (e) {
                let form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (report) {
                            ReportRouteHistoric.processHistoricReportData(report);
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                            reportContainer.slideDown();
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                loadSelectVehicleReport($(this).val(), false);
            });

            $('#route-report, #date-report, #type-report').change(function () {
                /*let form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }*/
                reportContainer.slideUp(100);
            });

            @if(!Auth::user()->isAdmin())
                loadSelectVehicleReport(1, false);
            @else
                $('#company-report').change();
            @endif

            setTimeout(function(){
                $('.btn-show-off-road-report').click();
            },500);

            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for(let min = 0; min <= (24*60-2); min+=5){
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            $("#time-range-report").ionRangeSlider({
                type: "double",
                from: 96,
                to: 144,
                values: timeRange,
                drag_interval: true,
                //max_interval: 48,
                prefix: "<i class='fa fa-clock-o'></i> ",
                skin: "modern",
                grid: false,
                decorate_both: true,
                keyboard: true,
                grid_num: 10,
                values_separator: " → ",
                onChange: function () {
                    
                }
            });

            $('#range_reports').ionRangeSlider({
                keyboard: true,
                min: 0,
                max: 1,
                from: 0,
                step: 1,
                onChange: function(slide){
                    ReportRouteHistoric.updateBusMarker(markers[slide.from], historicInfoWindows[slide.from]);
                },
                onFinish: function(slide){
                    if (markers[slide.from]) map.setCenter(markers[slide.from].getPosition());
                }
            });
        });
    </script>
@endsection
