@extends('layout')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Mixed report')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Mixed report')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-passengers-mixed-search') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
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
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
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
                        <div class="col-md-3">
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
                        <div class="col-md-3">
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

    <div class="modal modal-message fade" id="modal-route-report">
        <div class="modal-dialog" style="height: 1000px !important;">
            <div class="modal-content">
                <div class="modal-header" style="width: 100%">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <blockquote class="m-0">
                            <h3 class="m-3">@lang('Route report')</h3>
                        </blockquote>
                        <hr class="col-md-12 col-xs-12 col-sm-12 p-0">
                        <h4 class="modal-title">
                            <i class="fa fa-area-chart"></i> @lang('Historic time chart')
                        </h4>
                        <div class="col-md-12 p-5">
                            <div id="chart-route-report" style="height: 80px"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-body" style="width:100%;">
                    <h4>
                        <i class="fa fa-map-marker text-primary fa-fw"></i> @lang('Track on map')
                        <span class="pull-right"><img src="{{ asset('img/control-point-1.png') }}"> @lang('Control point return')</span>                        &nbsp;&nbsp;
                        <span class="pull-right p-r-20"><img src="{{ asset('img/control-point-0.png') }}"> @lang('Control point going')</span>
                        <a href="" class="btn-primary btn btn-show-off-road-report pull-right">
                            <i class="ion-merge m-r-5 fa-fw"></i> @lang('See off road report')
                        </a>
                    </h4>
                    <div class="row">
                        <div class="col-md-3 col-sm-4 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- begin widget -->
                                <div class="widget widget-stat widget-stat-right bg-success-dark text-white">
                                    <div class="widget-stat-btn"><a href="javascript:;" data-click="widget-reload"><i
                                                    class="fa fa-repeat"></i></a></div>
                                    <div class="widget-stat-icon"><i class="ion-clipboard fa-fw"></i></div>
                                    <div class="widget-stat-info">
                                        <div class="widget-stat-title">@lang('Route info')</div>
                                        <div class="widget-stat-number modal-report-route-name report-info"></div>
                                    </div>
                                    <div class="widget-stat-progress">
                                        <div class="progress progress-striped progress-xs active">
                                            <div class="progress-bar progress-bar-lime modal-report-route-percent-progress report-info"
                                                 style="width: 50%"></div>
                                        </div>
                                    </div>
                                    <div class="widget-stat-footer text-left">
                                        <i class="fa fa-flag-checkered" aria-hidden="true"></i> <span
                                                class="modal-report-route-percent report-info"></span>% @lang('of the route')
                                    </div>
                                </div>
                                <!-- end widget -->
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- begin widget -->
                                <div class="widget widget-stat widget-stat-right bg-inverse text-white">
                                    <div class="widget-stat-btn"><a href="javascript:;" data-click="widget-reload"><i
                                                    class="fa fa-repeat"></i></a></div>
                                    <div class="widget-stat-icon"><i class="fa fa-bus"></i></div>
                                    <div class="widget-stat-info">
                                        <div class="widget-stat-title">@lang('Vehicle current status')</div>
                                        <div class="widget-stat-number modal-report-vehicle report-info"></div>
                                    </div>
                                    <div class="widget-stat-progress">
                                        <div class="progress progress-striped progress-xs active">
                                            <div class="progress-bar progress-success modal-report-vehicle-speed-progress report-info"
                                                 style="width: 50%"></div>
                                        </div>
                                    </div>
                                    <div class="widget-stat-footer text-left">
                                        <i class="fa fa-tachometer" aria-hidden="true"></i> <span
                                                class="modal-report-vehicle-speed report-info"></span> Km/h
                                    </div>
                                </div>
                                <!-- end widget -->
                            </div>
                        </div>
                        <div class="col-md-9 col-sm-8 col-xs-12">
                            <div class="col-md-12 p-5">
                                <div id="google-map-light-dream" class="height-lg"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer hide" style="width:90%;">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-off-road-report" style="background: #535353;opacity: 0.96;">
        <div class="modal-dialog modal-lg" style="width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h4 class="modal-title">@lang('Off road report')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal-off-road-report-table"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>
    <script type="application/javascript">
        $('.menu-passengers, .menu-passengers-mixed, .menu-passengers-mixed-recorder-vs-sensor').addClass('active');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                var form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                loadRouteReport($(this).val());
            });

            $('#company-report').change();

            $('#route-report, #date-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            var clipboard = new Clipboard('.btn-copy');

            clipboard.on('success', function (e) {
                gsuccess("@lang('Text copied'):" + e.text);
                e.clearSelection();
            });

            @if(!Auth::user()->isAdmin())
                loadRouteReport(null);
            @endif

            setTimeout(function(){
                $('.btn-show-off-road-report').click();
            },500);
        });

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('route-ajax-action') }}', {
                option: 'loadRoutes',
                company: company
            }, function () {
                routeSelect.find('option[value=""]').remove();
                routeSelect.prepend("<option value='all'>@lang('All Routes')</option>");
                routeSelect.val('all').change();
            });
        }
    </script>
@endsection
