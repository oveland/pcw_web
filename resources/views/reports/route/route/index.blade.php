@extends('layout')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Route times')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Route times')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-search') }}">
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
                        <div class="col-md-3">
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
    @include('template.google.maps')

    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <script type="application/javascript">
        $('.menu-routes, .menu-route-report').addClass('active');
        var busMarker = null;
        var iconbus = '{{ asset('img/bus.png') }}';

        var controlPointIcon = [
            '{{ asset('img/control-point-0.png') }}',
            '{{ asset('img/control-point-1.png') }}'
        ];

        var routeCoordinateIcon = '{{ asset('img/point-map-blue.png') }}';

        var pointMap = [
            '{{ asset('img/point-map-on-road.png') }}',
            '{{ asset('img/point-map-off-road.png') }}'
        ];

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

            $('#route-report, #date-report, #type-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });

            $('body').on('click', '.btn-show-off-road-report', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                var tableOffRoadReport = $('.modal-off-road-report-table');
                tableOffRoadReport.empty().html(loading);
                if(is_not_null(url)){
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
            });

            $('body').on('click', '.btn-show-chart-route-report', function () {
                //map.clearAllMarkers();
                $('.btn-show-off-road-report').attr('href',$(this).data('url-off-road-report'));
                var chartRouteReport = $("#chart-route-report");
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

                            data.routeCoordinates.forEach(function (rc, i) {
                                new google.maps.Marker({
                                    title: rc.index + ": distance "+rc.distance+" m.",
                                    map: map,
                                    icon: routeCoordinateIcon,
                                    //animation: google.maps.Animation.DROP,
                                    position: {lat: parseFloat(rc.latitude), lng: parseFloat(rc.longitude)}
                                });
                            });

                            new google.maps.KmlLayer({
                                url: data.urlLayerMap,
                                map: map
                            });

                            var dataDates = [];
                            var dataTimes = [];
                            var dataValues = [];
                            var dataDistances = [];
                            var dataPercentDistances = [];
                            var latitudes = [];
                            var longitudes = [];
                            var offRoads = [];

                            data.reports.forEach(function (report, i) {
                                var percent = ((report.distance / data.routeDistance) * 100).toFixed(1);
                                var routeDistance = report.distance / 1000;
                                dataDates[i] = report.date;
                                dataTimes[i] = report.time;
                                dataValues[i] = report.value * 60;
                                dataDistances[i] = routeDistance;
                                dataPercentDistances[i] = percent;
                                latitudes[i] = report.latitude;
                                longitudes[i] = report.longitude;
                                offRoads[i] = report.offRoad ? '' : 'hide';

                                new google.maps.Marker({
                                    title: report.date+" | "+report.time+" | "+routeDistance+" Km | "+"  "+percent+"%",
                                    map: map,
                                    icon: pointMap[report.offRoad ? 1 : 0],
                                    position: {lat: parseFloat(report.latitude), lng: parseFloat(report.longitude)}
                                });
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
                                //var sparkline = ev.sparklines[0],info = sparkline.getCurrentRegionFields();
                                setTimeout(function () {
                                    var t = $('.info-route-report');
                                    var latitude = t.find('.latitude').html();
                                    var longitude = t.find('.longitude').html();

                                    if (!busMarker) {
                                        busMarker = new google.maps.Marker({
                                            map: map,
                                            icon: iconbus,
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
