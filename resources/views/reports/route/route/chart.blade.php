@section('templateStyles')
    <style type="text/css">
        .widget-stat-number{
            font-size: 15px !important;
            font-weight: 900;
        }
    </style>
@endsection

<div class="modal modal-message fade" id="modal-route-report">
    <div class="modal-dialog" style="height: 1000px !important;">
        <div class="modal-content">
            <div class="modal-header" style="width: 100%">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="fa fa-times"></i>
                </button>
                <div class="row">
                    <blockquote class="m-0">
                        <h3 class="m-3">@lang('Route report') <span id="date-report-details"></span></h3>
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
                                <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
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
                                <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                                <div class="widget-stat-icon"><i class="fa fa-bus"></i></div>
                                <div class="widget-stat-info">
                                    <div class="widget-stat-title">@lang('Vehicle information')</div>
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

@section('templateScripts')
    @include('template.google.maps')

    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <script type="application/javascript">
        let busMarker = null;
        let iconbus = '{{ asset('img/bus.png') }}';

        let controlPointIcon = [
            '{{ asset('img/control-point-0.png') }}',
            '{{ asset('img/control-point-1.png') }}'
        ];

        let routeCoordinateIcon = '{{ asset('img/point-map-blue.png') }}';

        let pointMap = [
            '{{ asset('img/point-map-on-road.png') }}',
            '{{ asset('img/point-map-off-road.png') }}'
        ];

        $(document).ready(function () {
            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });

            $('body').on('click', '.btn-show-off-road-report', function (e) {
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
            });

            $('body').on('click', '.btn-show-chart-route-report', function () {
                //map.clearAllMarkers();
                $('#date-report-details').html($('#date-report').val());
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

                            data.routeCoordinates.forEach(function (rc, i) {
                                new google.maps.Marker({
                                    title: rc.index + ": distance " + rc.distance + " m. (" + rc.latitude + ", " + rc.longitude + ")",
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

                            let dataTimes = [];
                            let dataTimesReports = [];
                            let dataValues = [];
                            let dataDistances = [];
                            let completedPercent = [];
                            let latitudes = [];
                            let longitudes = [];
                            let offRoads = [];
                            let speeding = [];
                            let speed = [];

                            data.reports.forEach(function (report, i) {
                                let percent = report.completedPercent;
                                let routeDistance = report.distance;
                                dataTimes[i] = report.time;
                                dataTimesReports[i] = report.timeReport;
                                dataValues[i] = report.value * 60;
                                dataDistances[i] = routeDistance;
                                completedPercent[i] = percent;
                                latitudes[i] = report.latitude;
                                longitudes[i] = report.longitude;
                                offRoads[i] = report.offRoad ? '' : 'hide';
                                speed[i] = report.speed;
                                speeding[i] = report.speeding ? 'label-danger' : '';

                                new google.maps.Marker({
                                    title: report.date + " | " + report.time + " | " + routeDistance + " Km | " + "  " + percent + "%",
                                    map: map,
                                    icon: pointMap[report.offRoad ? 1 : 0],
                                    position: {lat: parseFloat(report.latitude), lng: parseFloat(report.longitude)}
                                });
                            });

                            speed.forEach(function (sp, i){
                                console.log('Seeed '+sp+' >> '+speeding[i]);
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
                                tooltipFormat: "<?='<div class=\'info-route-report\'>"+
                                        "<span class=\'{{offset:offRoads}}\'><span class=\'label label-danger f-s-10 m-b-10\'>"+
                                        "    <i class=\'ion-merge m-r-5 fs-12 fa-fw\'></i> '.__('Off road vehicle').'</span><hr class=\'m-5\'>"+
                                        "</span>"+
                                        "<b class=\'m-t-10\'>'.__('Status').':</b> {{offset:timesReports}} <br>"+
                                        "<b>'.__('Time').':</b> {{offset:times}} <br>"+
                                        "<b>'.__('Distance').':</b> {{offset:distance}} m <br>"+
                                        "<b>'.__('Completed').':</b> {{offset:percent}}% <br>"+
                                        "<span class=\'label {{offset:speed}}\'><b>'.__('Speed').':</b> {{offset:speed}} Km/h <br></span>"+
                                        "<span class=\'hide latitude\'>{{offset:latitude}}</span><br>"+
                                        "<span class=\'hide longitude\'>{{offset:longitude}}</span>"+
                                    "</div>'?>",
                                tooltipValueLookups: {
                                    'offRoads': offRoads,
                                    'timesReports': dataTimesReports,
                                    'times': dataTimes,
                                    'distance': dataDistances,
                                    'speed': speed,
                                    'speeding': speeding,
                                    'percent': completedPercent,
                                    'latitude': latitudes,
                                    'longitude': longitudes
                                }
                            }).slideDown();

                            chartRouteReport.bind('sparklineRegionChange', function (ev) {
                                //let sparkline = ev.sparklines[0],info = sparkline.getCurrentRegionFields();
                                setTimeout(function () {
                                    let t = $('.info-route-report');
                                    let latitude = t.find('.latitude').html();
                                    let longitude = t.find('.longitude').html();

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
        });
    </script>
@endsection