@extends(Auth::user()->isProprietary() || $hideMenu ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <style>
        .range-reports {
            z-index: 1;
            padding-top: 10px;
            padding-bottom: 5px;
            background: rgba(0, 12, 35, 0.59);
            color: white;
        }

        .range-reports .irs-bar, .range-reports .irs-bar-edge, .range-reports .irs-single {
            background: #f57c1e;
        }

        .btn-historic-info {
            padding-left: 10px !important;
            padding-right: 10px !important;
            font-size: 0.8em !important;
            margin-bottom: 5px !important;
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
                            <div class="col-md-2">
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

                        @if(Auth::user()->canSelectRouteReport())
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.routes', compact('routes'), ['withAll' => "true"])
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12">
                                        @include('partials.selects.vehicles', compact('vehicles'))
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
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ $dateReport ? $dateReport : date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6 col-xs-6">
                            <div class="form-group">
                                <label class="control-label"><br></label>
                                <div class="form-group">
                                    <button id="search" type="submit" onclick="$('#export').val('')" class="btn btn-success btn-search-report">
                                        <i class="fa fa-map-o"></i> @lang('Search')
                                    </button>
                                    <a href="#" class="btn btn-lime btn-export form-export" style="display: none">
                                        <i class="fa fa-file-excel-o"></i> @lang('Export')
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12 col-sm-12">
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
            <div class="col-md-12 col-sm-12 col-xs-12 range-reports">
                <div class="text-center" style="position: absolute;width: 100%">
                    <label for="range_reports">
                        <small class="text-muted">Deslice para reproducir recorrido</small>
                    </label>
                </div>
                <input id="range_reports" type="text" />
                <p class="help-block text-white show-info">
                    <small class="col-md-4 col-sm-12 col-xs-12 p-0 text-left">
                        <span><i class="fa fa-map-o"></i> <span class="total">0</span> @lang('reports')</span>
                        <span class="hidden-xs">
                            @lang('between') <i class="fa fa-clock-o"></i> <span class="time-from">--:--:--</span> - <i class="fa fa-clock-o"></i> <span class="time-to">--:--:--</span>
                        </span>
                    </small>
                    <small class="col-md-8 col-sm-12 col-xs-12 p-0 text-right">
                        <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" data-title="@lang('Route') | @lang('Mileage') @lang('route')"><i class="fa fa-flag"></i> <span class="route"></span> | <span class="mileage-route">0</span> Km</span>
                        <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Time')">
                            <i class="fa fa-clock-o"></i> <span class="time"></span>
                        </span>

                        @if(Auth::user()->isAdmin())
                        <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Period') | @lang('Average') (s)">
                            <i class="ion-android-stopwatch"></i> <span class="period"></span>s | <span class="average-period"></span>s
                        </span>
                        @endif

                        <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Speed')"><i class='fa fa-tachometer'></i> <span class="speed">0</span> Km/h</span>
                        <span class="btn btn-default btn-xs btn-circle btn-historic-info tooltips" title="@lang('Mileage') @lang('in the day')"><i class='fa fa-road'></i> <span class="current-mileage">0</span> Km</span>
                        <span class="btn btn-default btn-xs btn-circle btn-historic-info status-vehicle tooltips" title="@lang('Vehicle status')"><i class='fa fa-send'></i></span>
                    </small>
                </p>
            </div>
            <div id="google-map-light-dream" class="col-md-12 col-sm-12 col-xs-12 p-0 map-report-historic" style="height: 1000px"></div>
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

    <template id="marker-animation-scripts"></template>

    <script type="application/javascript">
        let reportRouteHistoric = null;
        let reportContainer = $('.report-container');
        $('.menu-routes, .menu-report-route-historic').addClass('active-animated');

        const vehicleReport = '{{ $vehicleReport }}';
        const companyReport = '{{ $companyReport }}';
        let form = $('.form-search-report');

        function loadScript(url, callback)
        {
            // Adding the script tag to the head as suggested before
            var head = document.head;
            var script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = url;

            // Then bind the event to the callback function.
            // There are several events for cross browser compatibility.
            script.onreadystatechange = callback;
            script.onload = callback;

            // Fire the loading
            head.appendChild(script);
        }

        const autoLoad = function(){
            //const vehicleReport = $('#vehicle-report').val();
            setTimeout(()=>{
                if (vehicleReport) form.submit();
            },1400);
        };

        $(document).ready(function () {
            initializeMap(() => {
                reportRouteHistoric = new ReportRouteHistoric(map);
                loadScript("https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js", function(){
                    loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js", function(){
                        loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js", function(){
                            SlidingMarker.initializeGlobally();
                            $('.map-report-historic').css('height', (window.innerHeight - 150));
                        });
                    });
                });
            });

            form.submit(function (e) {
                let btnExport = $('.btn-export').fadeOut();
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.slideUp(100);
                    reportRouteHistoric.clearMap();
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (report) {
                            reportRouteHistoric.processHistoricReportData(report);

                            $('#range_reports').data("ionRangeSlider").update({
                                min: 0,
                                max: report.total,
                                from: report.total
                            });

                            setTimeout(()=>{
                                if( report.total > 0 )btnExport.fadeIn();
                                btnExport.attr('href', report.exportLink);
                            },1000);
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                            reportContainer.slideDown();
                        }
                    });
                }
            });

            $('#route-report').change(function () {
                loadSelectVehicleReportFromRoute($(this).val(), vehicleReport, autoLoad);
                reportContainer.slideUp(100);
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), false, vehicleReport, autoLoad);
                    loadSelectRouteReport($(this).val());
                }).val(companyReport ? companyReport : 14).change();
            @else
                $('#route-report').change();
            @endif

            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for(let min = 0; min <= (24*60-2); min+=5){
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            const initialTime = parseInt('{{ $initialTime ? $initialTime : 60 }}');
            const finalTime = parseInt('{{ $finalTime ? $finalTime : 144 }}');

            $("#time-range-report").ionRangeSlider({
                type: "double",
                from: initialTime,
                to: finalTime,
                values: timeRange,
                drag_interval: true,
                //max_interval: 48,
                prefix: "<i class='fa fa-clock-o'></i> ",
                skin: "modern",
                grid: false,
                decorate_both: true,
                prettify: true,
                keyboard: true,
                grid_num: 10,
                values_separator: " → ",
                onChange: function (slider) {

                }
            });

            $('#range_reports').ionRangeSlider({
                keyboard: true,
                min: 0,
                max: 1,
                from: 0,
                step: 1,
                onChange: function(slide){
                    reportRouteHistoric.updateBusMarker(slide.from);
                },
                onFinish: function(slide){
                    setTimeout(()=>{
                        reportRouteHistoric.updateBusMarker(slide.from);
                        setTimeout(()=>{
                            reportRouteHistoric.updateBusMarker(slide.from);
                        },500);
                    },100);
                }
            });
        });
    </script>
@endsection
