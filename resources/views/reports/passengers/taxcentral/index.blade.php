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
        <li class="active">@lang('Register historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Register historic')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-passengers-taxcentral-search-report') }}">
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
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-3">
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
                        <div class="col-md-3">
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
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header hide">
                    <button type="button" class="close" >
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <h3 class="m-3">@lang('Seating report')</h3>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 p-5">
                            <div id="google-map-light-dream" class="height-sm hide"></div>
                            <div id="passengers-route-report" class="height-sm"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer hide">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>

    <div class="modal modal-message fade" id="modal-report-seat">
        <div class="modal-dialog modal-full">
            <div class="modal-content">
                <div class="modal-header">
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
                <div class="modal-body">
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
                <div class="modal-footer hide">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/numeral.js/2.0.6/numeral.min.js"></script>
    @include('template.google.maps')

    <script type="application/javascript">
        $('.menu-passengers, .menu-passengers-taxcentral').addClass('active-animated');
        var busMarker = null;
        var iconbus = '{{ asset('img/bus.png') }}';

        var seatPointIcon = [
            '{{ asset('img/location/svg/Flag_6.svg') }}',
            '{{ asset('img/location/svg/Flag_8.svg') }}'
        ];

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                var form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                loadSelectRouteReport($(this).val());
            });

            $('#date-report, #route-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
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
                        $('.report-info').empty();
                        $('.modal').modal('hide');
                        gerror('@lang('Oops, something went wrong!')');
                    }
                });
            });

            $('#modal-report-seat').on('shown.bs.modal', function () {
                initializeMap();
            });

            @if(!Auth::user()->isAdmin())
                loadSelectRouteReport(null);
            @endif
        });
    </script>
@endsection
