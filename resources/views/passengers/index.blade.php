@extends('layout')

@section('stylesheets')
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet"/>
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet"/>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Register historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Register historic')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search report')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date-report"
                                       class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control"
                                           placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->isAdmin())
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company-report"
                                       class="control-label field-required">@lang('Company')</label>
                                <div class="form-group">
                                    <select name="company-report" id="company-report"
                                            class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select an option')</option>
                                        @foreach($companies as $company)
                                            <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-4">
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

    <div class="modal modal-message fade" id="modal-passengers-route-report">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="width: 90%">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <blockquote class="m-0">
                            <h3 class="m-3">@lang('Passengers report by route')</h3>
                        </blockquote>
                        <hr class="col-md-12 col-xs-12 col-sm-12 p-0">
                    </div>
                </div>
                <div class="modal-body" style="width:90%;">
                    <div class="row">
                        <div class="col-md-12 p-5">
                            <div id="google-map-light-dream" class="height-sm hide"></div>
                            <div id="passengers-route-report" class="height-sm"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer hide" style="width:90%;">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-message fade" id="modal-report-seat">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="width: 90%">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <blockquote class="m-0">
                            <h3 class="m-3">@lang('Count trajectory')</h3>
                        </blockquote>
                        <hr class="col-md-12 col-xs-12 col-sm-12 p-0">
                    </div>
                </div>
                <div class="modal-body" style="width:90%;">
                    <h4>
                        <i class="fa fa-map-marker text-primary fa-fw"></i> @lang('Track on map')
                    </h4>
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- begin widget -->
                                <div class="widget widget-stat widget-stat-right bg-primary-dark text-white">
                                    <div class="widget-stat-btn">
                                        <a href="javascript:;" data-click="widget-reload"><i class="fa fa-repeat"></i></a>
                                    </div>
                                    <div class="widget-stat-icon">
                                        <img src="{{ asset('img/location/svg/Flag_8.svg') }}"/>
                                    </div>
                                    <div class="widget-stat-info">
                                        <div class="widget-stat-title">@lang('Active seat')</div>
                                        <div class="widget-stat-number modal-report-seat-active-km report-info"></div>
                                    </div>
                                    <div class="widget-stat-footer text-left">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <span class="modal-report-seat-active-time report-info"></span>
                                    </div>
                                </div>
                                <!-- end widget -->
                            </div>
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- begin widget -->
                                <div class="widget widget-stat widget-stat-right bg-success-dark text-white">
                                    <div class="widget-stat-btn">
                                        <a href="javascript:;" data-click="widget-reload">
                                            <i class="fa fa-repeat"></i>
                                        </a>
                                    </div>
                                    <div class="widget-stat-icon">
                                        <img src="{{ asset('img/location/svg/Flag_8.svg') }}"/>
                                    </div>
                                    <div class="widget-stat-info">
                                        <div class="widget-stat-title">@lang('Free seat')</div>
                                        <div class="widget-stat-number modal-report-seat-inactive-km report-info"></div>
                                    </div>
                                    <div class="widget-stat-footer text-left">
                                        <i class="fa fa-clock-o" aria-hidden="true"></i>
                                        <span class="modal-report-seat-inactive-time report-info"></span>
                                    </div>
                                </div>
                                <!-- end widget -->
                            </div>
                        </div>
                        <div class="col-md-8 col-sm-6 col-xs-12">
                            <div class="col-md-12 p-5">
                                <div id="google-map-light-dream" class="height-md"></div>
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
@endsection


@section('scripts')
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js"></script>

    @include('template.google.maps')

    <script type="application/javascript">
        $('.menu-passengers').addClass('active');
        var busMarker = null;
        var iconbus = '{{ asset('img/bus.png') }}';

        var seatPointIcon = [
            '{{ asset('img/location/svg/Flag_6.svg') }}',
            '{{ asset('img/location/svg/Flag_8.svg') }}'
        ];

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if ($(this).isValid()) {
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: '{{ route('passengers-search-report') }}',
                        data: $(this).serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                loadRouteReport($(this).val());
            });

            $('#route-report').change(function () {
                $('.report-container').slideUp();
                if (is_not_null($(this).val())) {
                    $('.form-search-report').submit();
                }
            });

            $('#modal-report-seat').on('shown.bs.modal', function () {
                initializeMap();
            });
        });

        $('body').on('click', '.btn-show-passengers-route-report', function () {
            var passengersRouteReport = $("#passengers-route-report");
            passengersRouteReport.html(loading);
            $('.report-info').html(loading);
            $.ajax({
                url: $(this).data('url'),
                success: function (data) {
                    passengersRouteReport.hide().html(data).fadeIn();
                    if (!data.empty) {

                    } else {
                        gerror('@lang('No passengers report found for this vehicle')');
                        $('.report-info').empty();
                        $('.modal').modal('hide');
                    }
                },
                error: function () {
                    passengersRouteReport.empty();
                    $('.modal').modal('hide');
                    gerror('@lang('Oops, something went wrong!')');
                }
            });
        });

        $('body').on('click', '.btn-show-trajectory-seat-report', function () {
            //map.clearAllMarkers();
            $('.report-info').html(loading);
            $.ajax({
                url: $(this).data('url'),
                success: function (data) {
                    if (!data.empty) {
                        var urlLayerMap = data.urlLayerMap;
                        new google.maps.KmlLayer({
                            url: urlLayerMap,
                            map: map
                        });

                        new google.maps.Marker({
                            title: cp.name,
                            map: map,
                            icon: seatPointIcon[0],
                            animation: google.maps.Animation.DROP,
                            position: {lat: parseFloat(data.active_latitude), lng: parseFloat(data.active_longitude)}
                        });

                        new google.maps.Marker({
                            title: cp.name,
                            map: map,
                            icon: seatPointIcon[1],
                            animation: google.maps.Animation.DROP,
                            position: {lat: parseFloat(data.inactive_latitude), lng: parseFloat(data.inactive_longitude)}
                        });

                    } else {
                        gerror('@lang('No seat report found')');
                        $('.report-info').empty();
                        $('.modal').modal('hide');
                    }
                },
                error: function () {
                    chartRouteReport.empty();
                    $('.report-info').empty();
                    $('.modal').modal('hide');
                    gerror('@lang('Oops, something went wrong!')');
                }
            });
        });

        @if(!Auth::user()->isAdmin())
            loadRouteReport(null);
        @endif

        function loadRouteReport(company) {
            var roouteSelect = $('#route-report');
            roouteSelect.html($('#select-loading').html()).trigger('change.select2');
            roouteSelect.load('{{ route('passengers-ajax',['action'=>'loadRoutes']) }}', {
                company: company
            }, function () {
                roouteSelect.trigger('change.select2');
            });
        }
    </script>
@endsection
