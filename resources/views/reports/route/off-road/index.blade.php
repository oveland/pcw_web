@extends('layout')

@section('stylesheets')
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
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:void(0);">@lang('Reports')</a></li>
        <li><a href="javascript:void(0);">@lang('Routes')</a></li>
        <li class="active">@lang('Off road report')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Off road report')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-off-road-search') }}">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="type-report" class="control-label field-required">@lang('Type report')</label>
                                <div class="form-group">
                                    <select name="type-report" id="type-report" class="default-select2 form-control col-md-12">
                                        <option value="vehicle">@lang('By vehicle')</option>
                                        <option value="route">@lang('By route')</option>
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

    <div class="modal modal-message fade" id="modal-route-report">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="width: 90%">
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
                <div class="modal-body" style="width:90%;">
                    <h4>
                        <i class="fa fa-map-marker text-primary fa-fw"></i> @lang('Track on map')
                        <span class="pull-right"><img src="{{ asset('img/control-point-1.png') }}"> @lang('Control point return')</span>                        &nbsp;&nbsp;
                        <span class="pull-right p-r-20"><img src="{{ asset('img/control-point-0.png') }}"> @lang('Control point going')</span>
                        <a href="" class="btn-primary btn btn-show-off-road-report pull-right">
                            <i class="ion-merge m-r-5 fa-fw"></i> @lang('See off road report')
                        </a>
                    </h4>
                    <div class="row">
                        <div class="col-md-4 col-sm-6 col-xs-12">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                                <!-- begin widget -->
                                <div class="widget widget-stat widget-stat-right bg-success-dark text-white">
                                    <div class="widget-stat-btn"><a href="javascript:void(0);" data-click="widget-reload"><i
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
                                    <div class="widget-stat-btn"><a href="javascript:void(0);" data-click="widget-reload"><i
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
                        <div class="col-md-8 col-sm-6 col-xs-12">
                            <div class="col-md-12 p-5">
                                <div id="google-map-light-dream" class="height-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer hide" style="width:90%;">
                    <a href="javascript:void(0);" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
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
                    <a href="javascript:void(0);" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    @include('template.google.maps')
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <script type="application/javascript">
        $('.menu-routes, .menu-off-road-report').addClass('active');
        let form = $('.form-search-report');
        let mainContainer = $('.report-container');
        let busMarker = null;
        let iconBus = '{{ asset('img/bus.png') }}';

        let controlPointIcon = [
            '{{ asset('img/control-point-0.png') }}',
            '{{ asset('img/control-point-1.png') }}'
        ];

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                mainContainer.slideUp();
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
                }
            });

            $('#date-report, #type-report, #company-report').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
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
            })
            .on('click', '.btn-show-off-road-report', function (e) {
                e.preventDefault();
                let url = $(this).attr('href');
                let tableOffRoadReport = $('.modal-off-road-report-table');
                tableOffRoadReport.empty().html(loading);
                if (is_not_null(url)) {
                    $('#modal-off-road-report').modal('show');
                    $.ajax({
                        url: url,
                        success: function (data) {
                            tableOffRoadReport.hide().html(data).slideDown();
                        },
                        error: function () {
                            $('.modal').modal('hide');
                            gerror('@lang('Oops, something went wrong!')');
                        }
                    });
                }
            })
            .on('click', '.btn-show-chart-route-report', function () {
                //map.clearAllMarkers();
                $('.btn-show-off-road-report').attr('href', $(this).data('url-off-road-report'));
                let chartRouteReport = $("#chart-route-report");
                chartRouteReport.html(loading);
                $('.report-info').html(loading);
                $.ajax({
                    url: $(this).data('url'),
                    success: function (data) {
                        if (!data.empty) {
                            $('.modal-report-vehicle').html(data.vehicle + ' <i class="fa fa-hand-o-right" aria-hidden="true"></i> ' + data.plate);
                            $('.modal-report-vehicle-speed').html(data.vehicleSpeed);
                            $('.modal-report-vehicle-speed-progress').css('width', parseInt(data.vehicleSpeed) + '%');

                            $('.modal-report-route-name').html(data.route);
                            $('.modal-report-route-percent').html(data.routePercent);
                            $('.modal-report-route-percent-progress').css('width', parseInt(data.routePercent) + '%');

                            data.controlPoints.forEach(function (cp, i) {
                                new google.maps.Marker({
                                    title: cp.name,
                                    map: map,
                                    icon: controlPointIcon[cp.trajectory],
                                    animation: google.maps.Animation.DROP,
                                    position: {lat: parseFloat(cp.latitude), lng: parseFloat(cp.longitude)}
                                });
                            });

                            new google.maps.KmlLayer({
                                url: data.urlLayerMap,
                                map: map
                            });

                            let dataDates = [];
                            let dataTimes = [];
                            let dataValues = [];
                            let dataDistances = [];
                            let dataPercentDistances = [];
                            let latitudes = [];
                            let longitudes = [];
                            let offRoads = [];

                            data.reports.forEach(function (report, i) {
                                dataDates[i] = report.date;
                                dataTimes[i] = report.time;
                                dataValues[i] = report.value * 60;
                                dataDistances[i] = report.distance / 1000;
                                dataPercentDistances[i] = ((report.distance / data.routeDistance) * 100).toFixed(1);
                                latitudes[i] = report.latitude;
                                longitudes[i] = report.longitude;
                                offRoads[i] = report.offRoad ? '' : 'hide';
                            });

                            chartRouteReport.empty().hide().sparkline(dataValues, {
                                type: 'line',
                                width: '1180px',
                                height: '80px',
                                fillColor: 'transparent',
                                spotColor: '#f0eb54',
                                lineColor: '#68a8b6',
                                minSpotColor: '#F04B46',
                                maxSpotColor: '#259bf0',
                                lineWidth: 3.5,
                                spotRadius: 7,
                                normalRangeMin: -50, normalRangeMax: 50,
                                tooltipFormat: '<?="'+
                            '<div class=\"info-route-report\">'+
                                '<span class=\"{{offset:offRoads}}\"><span class=\"label label-danger f-s-10 m-b-10\"><i class=\"ion-merge m-r-5 fs-12 fa-fw\"></i> Vehículo fuera de la Ruta</span><hr class=\"m-5\"></span>'+
                                '<b class=\"m-t-10\">Estado:</b> {{offset:times}} <br>'+
                                '<b>Hora:</b> {{offset:dates}} <br>'+
                                '<b>Distancia:</b> {{offset:distance}} Km <br>'+
                                '<b>Recorrido:</b> {{offset:percent}}% <br>'+
                                '<span class=\"hide latitude\">{{offset:latitude}}</span><br>'+
                                '<span class=\"hide longitude\">{{offset:longitude}}</span>'+
                            '</div>'+
                        '"?>',
                                tooltipValueLookups: {
                                    'offRoads': offRoads,
                                    'times': dataTimes,
                                    'dates': dataDates,
                                    'distance': dataDistances,
                                    'percent': dataPercentDistances,
                                    'latitude': latitudes,
                                    'longitude': longitudes
                                }
                            }).slideDown();

                            chartRouteReport.bind('sparklineRegionChange', function (ev) {
                                setTimeout(function () {
                                    let t = $('.info-route-report');
                                    let latitude = t.find('.latitude').html();
                                    let longitude = t.find('.longitude').html();

                                    if (!busMarker) {
                                        busMarker = new google.maps.Marker({
                                            map: map,
                                            icon: iconBus,
                                            animation: google.maps.Animation.DROP
                                        });
                                    }
                                    busMarker.setPosition({lat: parseFloat(latitude), lng: parseFloat(longitude)})
                                }, 10);
                            }).bind('mouseleave', function () {
                                busMarker ? busMarker.setMap(null) : null;
                                busMarker = null;
                            });
                        } else {
                            gerror('@lang('No report found for this vehicle')');
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

            setTimeout(function () {
                $('.btn-show-off-road-report').click();
            }, 500);
        });
    </script>
@endsection
