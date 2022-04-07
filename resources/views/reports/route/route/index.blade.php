@extends('layout')

@section('stylesheets')
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/global/plugins/ion.rangeslider/css/ion.rangeSlider.skinFlat.css') }}" rel="stylesheet" type="text/css" />

    <style>
        .nav.nav-pills>li>a {
            color: #9ca4aa !important;
            border: 2px solid #3b4b50 !important;
        }
        .bg-warning {
            background-color: #cba528
        }

        a.bg-warning:hover,
        a.bg-warning:focus {
            background-color: #cb840c
        }

        .car-ss {
            position: absolute;
            top: -10px;
            right: -5px;
        }

        .car-nr {
            position: absolute;
            top: -10px;
            right: 20px;
        }

        .form-search-report .form-group {
            margin-bottom: 5px;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Route')</a></li>
        <li class="active">@lang('Dispatch')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Dispatch')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-search') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning tooltips" data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
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
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">
                                    @lang('Date')
                                </label>
                                <label class="with-end-date-container text-bold">
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
                        <div class="col-md-2 date-end-container" style="display: none">
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

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report" data-with-all="true" data-with-none="true" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

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

                        <div class="col-md-{{ Auth::user()->isAdmin() ? 4 : 6 }} options with-route">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="type-report" name="type-report" type="checkbox" value="group-vehicles" checked> @lang('Group')
                                            </label>
                                            <label class="text-bold">
                                                <input id="completed-turns" name="completed-turns" type="checkbox" value="completed-turns"> @lang('Completed turns')
                                            </label>
                                            <label class="text-bold">
                                                <input id="no-taken-turns" name="no-taken-turns" type="checkbox" value="no-taken-turns"> @lang('No taken turns')
                                            </label>
                                            <label class="text-bold">
                                                <input id="last-laps" name="last-laps" type="checkbox" value="true"> @lang('Last laps')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 col-xs-12 col-sm-12">
                            <input id="time-range-report" name="time-range-report" type="text" value="" />
                        </div>

                        <div class="col-md-2 options without-route" style="display: none">
                            <div class="form-group">
                                <label for="threshold-km" class="control-label">@lang('Threshold km')</label>
                                <div class="form-group">
                                    <input id="threshold-km" class="form-control input-sm" name="threshold-km" type="number" value="5" min="5" max="500" step="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->

    <!-- Include template for show modal report with char and historic route coordinates -->
    @include('reports.route.route.templates.chart._chartModal')
    <!-- end template -->

    @if( Auth::user()->belongsToCootransol() )
        <div class="modal fade" id="modal-execute-DAR" style="background: #535353;opacity: 0.96;">
            <div class="modal-dialog modal-lg" style="width: 70%;">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">@lang('Building route report')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <pre class="pre col-md-12"></pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if( Auth::user()->isAdmin() )
    <div class="modal fade" id="modal-report-log" style="background: #535353;opacity: 0.96;">
        <div class="modal-dialog modal-lg" style="width: 98%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">@lang('Report') - @lang('Log')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <iframe id="iframe-report-log" width="100%" height="500px" src=""></iframe>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
    @endif


    @if( Auth::user()->canMakeTakings() )
        <div class="modal fade" id="modal-takings-passengers" style="background: rgba(181, 181, 181, 0.72);">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header p-20">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i>
                        </button>
                        <h4 class="modal-title text-center text-purple">
                            <span><i class="icon-briefcase"></i> @lang('Takings') - @lang('Passengers')</span>
                        </h4>
                    </div>
                    <div class="modal-body p-t-0 p-b-0"></div>
                </div>
            </div>
        </div>
    @endif

    <div class="modal modal-message fade" id="modal-seating-profile" style="background: rgba(0,0,0,0.72)">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
            </div>
        </div>
@endsection

@section('scripts')
    <script src="{{ asset('assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js') }}" type="text/javascript"></script>

    <script type="application/javascript">
        $('.menu-routes, .menu-route-report').addClass('active-animated');

        let form = $('.form-search-report');
        let reportContainer = $('.report-container');
        let modalBinnacle = $('#modal-vehicles-binnacle');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.show();
                    reportContainer.empty().hide().html($('#animated-loading').html()).show();
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            reportContainer.empty().hide().html(data).fadeIn();
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                            modalBinnacle.modal('hide');
                        },
                        error: function (data) {
                            reportContainer.empty().fadeIn();
                        }
                    });
                }
            });

            $('#date-report, #route-report, #vehicle-report, #company-report, #type-report, #completed-turns, #no-taken-turns, #last-laps').change(function () {
                $('.report-container').slideUp();
            });


            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for(let min = 0; min <= (24*60-2); min+=5){
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            $("#time-range-report").ionRangeSlider({
                type: "double",
                values: timeRange,
                drag_interval: true,
                from: 0,
                to: 288,
                prefix: "<i class='fa fa-clock-o'></i> ",
                skin: "modern",
                decorate_both: true,
                prettify: true,
                keyboard: false,
                grid_num: 10,
                values_separator: " → ",
                onChange: function (slider) {
                }
            });
            setTimeRange();

            $('#route-report').change(function () {
                const route = $(this).val();
                loadSelectVehicleReportFromRoute(route);
                reportContainer.slideUp(100);
                setTimeRange();
            });

            @if(Auth::user()->isAdmin())
            $('#company-report').change(function () {
                loadSelectVehicleReport($(this).val(), true);
                loadSelectRouteReport($(this).val());
                reportContainer.slideUp(100);
                setTimeRange();
            }).change();
            @else
            loadSelectRouteReport(null);
            @endif

            setTimeout(function(){
                $('.btn-show-off-road-report').click();
            },500);

            $('body').on('click', '.btn-show-seating-profile', function () {
                let contentReport = $("#modal-seating-profile .modal-content");
                contentReport.html(loading);
                $.ajax({
                    url: $(this).data('url'),
                    success: function (data) {
                        contentReport.hide().html(data).fadeIn();
                        if (!data.empty) {

                        } else {
                            gerror('@lang('No passengers report found for this vehicle')');
                            $('.report-info').empty();
                            $('.modal').modal('hide');
                        }
                    },
                    error: function () {
                        contentReport.empty();
                        $('.modal').modal('hide');
                        gerror('@lang('Oops, something went wrong!')');
                    }
                });
            });
        });

        $('#with-end-date').change(function(){
            const dec =  $('.date-end-container').slideUp();
            if ($(this).is(':checked')) {
                dec.slideDown();
            }
        });

        function setTimeRange() {
            let initialTime = parseInt('{{ $initialTime ?? 0 }}');
            let finalTime = parseInt('{{ $finalTime ?? 288 }}');

            let companyId = $('#company-report').val();
            companyId = parseInt(companyId ? companyId : '{{ Auth::user()->company_id }}');

            let route = $('#route-report').val();

            if(companyId === 30 && route === 'none') {
                initialTime = 264;
            }

            $('#time-range-report').data("ionRangeSlider").update({
                from: initialTime,
                to: finalTime
            });

            let withEndDate = $('#with-end-date');
            let withDateEndC = withEndDate.parents('.with-end-date-container');
            if(route === 'none') {
                withEndDate.prop('checked', false).change();
                withDateEndC.hide();
            }else {
                withEndDate.prop('checked', false).change();
                withDateEndC.show();
            }
        }
    </script>
@endsection
