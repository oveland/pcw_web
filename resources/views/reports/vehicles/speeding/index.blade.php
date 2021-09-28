@extends('layout')

@section('stylesheets')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.css') }}" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS -->

    <style>
        .accordion-toggle[aria-expanded="true"]{
            background:rgba(213, 208, 208, 0.14) !important;
        }
        .accordion-toggle[aria-expanded="true"]:after{
            content:'➤';
            color:rgba(211, 211, 211, 0.17);
            font-size:150%;
            position:relative;
            float:right;
            bottom:30px;
            right:8px;
        }
        .icon-vehicle-list i{
            font-size: 120% !important;
            margin: 10px;
        }
        .info-vehicle-list{
            margin-left: 70px !important;
        }
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
            color: #cabf52 !important;
        }
        .btn-clear-search{
            position: absolute !important;
            right: 55px !important;
            color: rgba(0, 0, 0, 0.14) !important;
            z-index: 1 !important;
        }
    </style>

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
        google.charts.load('current', {packages: ['corechart', 'bar']});
        // google.charts.setOnLoadCallback(drawStacked);
    </script>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:void(0);">@lang('Reports')</a></li>
        <li><a href="javascript:void(0);">@lang('Routes')</a></li>
        <li class="active">@lang('Speeding')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Speeding')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">

        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-vehicle-speeding-search-report') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:void(0);" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="company-report"
                                           class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->canSelectRouteReport())
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.routes', compact('routes'), ['withAll' => true])
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                        @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date-report"
                                       class="control-label field-required">@lang('Date')
                                </label>
                                <label class="text-bold">
                                    &nbsp;| <input id="with-end-date" name="with-end-date" type="checkbox"> @lang('Range')
                                </label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 date-end-container" style="display: none;">
                            <div class="form-group">
                                <label for="date-end-report" class="control-label">@lang('Date end')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-end-report" id="date-end-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 options">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="only-max" name="only-max" type="checkbox" value="only-max"> @lang('Only max')
                                            </label>
                                        </div>
                                    </div>
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
        <!-- begin content report -->

        <div class="col-md-12">
            <div id="chart_div"></div>
        </div>

        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->

    <!-- Include template for show modal report with char and historic route coordinates -->
    @include('reports.route.route.templates.chart._chartModal')
    <!-- end template -->
@endsection


@section('scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/lib/markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.min.js') }}" type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <script type="application/javascript">
        $('.menu-report-vehicles, .menu-report-vehicles-speeding').addClass('active-animated');
        let form = $('.form-search-report');
        let mainContainer = $('.report-container');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                mainContainer.show().empty().hide().html($('#animated-loading').html()).show();
                if (form.isValid()) {

                    form.find('.btn-search-report').addClass(loadingClass);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            mainContainer.empty().hide().html(data).fadeIn();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });

                    $.ajax({
                        url: form.attr('action') + '?chart=true',
                        data: form.serialize(),
                        success: function (data) {
                            drawChart(data);

                            // [
                            //     ['speed', '60 - 79', '80 - 90', '91 - 110', '111 - 120', '> 120'],
                            //     ['9084', 10, 24, 20, 32, 51],
                            //     ['9011', 16, 22, 23, 30, 66],
                            //     ['9089', 28, 19, 29, 30, 55]
                            // ]
                        }
                    });
                }
            });

            $('#route-report').change(function () {
                loadSelectVehicleReportFromRoute($(this).val());
                mainContainer.slideUp(100);
            });

            $('#vehicle-report, #only-max, #date-report, #date-end-report').change(function () {
                mainContainer.slideUp(100);
            });

            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });

            $('body').on('click', '.btn-show-address', function () {
                let el = $(this);
                el.attr('disabled', true);
                el.find('span').hide();
                el.find('i').removeClass('hide');
                $($(this).data('target')).load($(this).data('url'), function (response, status, xhr) {
                    console.log(status);
                    el.attr('disabled', false);
                    if (status === "error") {
                        if (el.hasClass('second-time')) {
                            el.removeClass('second-time');
                        } else {
                            el.addClass('second-time', true).click();
                        }
                    } else {
                        el.fadeOut(1000);
                    }
                });
            })
                .on('click', '.accordion-vehicles', function () {
                    $($(this).data('parent'))
                        .find('.collapse').collapse('hide')
                        .find($(this).data('target')).collapse('show');
                })
                .on('keyup', '.search-vehicle-list', function () {
                    let vehicle = $(this).val();
                    if (is_not_null(vehicle)) {
                        $('.vehicle-list').slideUp("fast", function () {
                            $('#vehicle-list-' + vehicle).slideDown();
                        });
                    } else {
                        $('.vehicle-list').slideDown();
                    }
                });

            @if(Auth::user()->isAdmin())
            $('#company-report').change(function () {
                loadSelectVehicleReport($(this).val(), true);
                loadSelectRouteReport($(this).val());
                mainContainer.slideUp(100);
            }).change();
            @else
                loadSelectRouteReport(null);
            @endif

            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for(let min = 0; min <= (24*60-2); min+=5){
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            const initialTime = parseInt(0);
            const finalTime = parseInt(288);

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
                    mainContainer.slideUp(100);
                }
            });
        });

        $('#with-end-date').change(function(){
            const dec =  $('.date-end-container').slideUp();
            if ($(this).is(':checked')) {
                dec.slideDown();
            }
        });

        function drawChart(report) {
            if(!report.length) return false;

            var data = new google.visualization.DataTable();

            var data = google.visualization.arrayToDataTable(report);

            var options = {
                animation: {duration: 200, easing: 'in', startup: true},
                width: 'auto',
                height: 400,
                legend: { position: 'top', maxLines: 3 },
                bar: { groupWidth: '40%' },
                colors: ['#d8ff00', 'orange', '#ff5200', '#d00000'],
                isStacked: true,
                dataOpacity: 0.8,
                title: 'Rango de velocidades en Km/h',
                tooltip: {showColorCode: true},
                alwaysOutside: true,
                axisTitlesPosition: 'in',
                trendlines: {
                    0: {
                        type: 'linear',
                        color: 'green',
                        lineWidth: 3,
                        opacity: 0.3,
                        showR2: true,
                        visibleInLegend: true
                    },
                    1: {
                        type: 'linear',
                        color: 'green',
                        lineWidth: 10,
                        opacity: 0.3,
                        showR2: true,
                        visibleInLegend: false
                    },
                }
            };

            var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
            chart.draw(data, options);
        }

        setTimeout(function (){
            drawChart(JSON.parse('[["speed","80 - 90","91 - 110","111 - 120","> 120"],["4546",0,0,0,1],["4534",0,4,7,0],["4082",0,5,4,0],["4559",0,0,3,0],["4087",0,0,6,0],["4556",0,4,9,0],["4090",0,1,7,0],["4526",0,0,5,0],["4081",0,3,10,0],["4085",0,0,1,0],["4561",0,3,6,0],["4086",0,2,7,0],["4089",0,0,4,0],["4538",0,1,5,0],["4084",0,1,0,0],["4554",0,0,5,0],["4083",0,0,1,0]]'));
        }, 500);
    </script>
@endsection
