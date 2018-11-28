@section('templateStyles')
<style type="text/css">
    .widget-stat-number{
        font-size: 15px !important;
        font-weight: 900;
    }
    .info-control-points{
        position: absolute;
        z-index: 1;
        right: 100px;
        background: #ffffffd1;
        padding: 5px;
        border-radius: 4px;
        font-size: 100%;
    }
</style>
@endsection

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
            '{{ asset('img/point-map-on-return-road.png') }}',
            '{{ asset('img/point-map-off-road.png') }}'
        ];

        $(document).ready(function () {
            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });

            $('#modal-route-report').on('hidden.bs.modal', function () {
                busMarker = null;
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
            }).on('click', '.btn-show-chart-route-report', function () {
                //map.clearAllMarkers();
                $('.btn-show-off-road-report').attr('href', $(this).data('url-off-road-report'));
                let chartRouteReport = $("#chart-route-report");
                chartRouteReport.html(loading);
                $('.report-info').html(loading);

                let panelOffRoad = $('.panel-off-road').slideUp();
                let panelOffRoadBody = panelOffRoad.find('tbody').empty();
                panelOffRoad.find('.off-road').hide();
                panelOffRoad.find('.no-off-road').hide();

                $.ajax({
                    url: $(this).data('url'),
                    success: function (data) {
                        if (!data.empty) {
                            panelOffRoad.slideDown();
                            $('#date-report-details').html(data.date);
                            $('.modal-report-vehicle').html(data.vehicle + ' <i class="fa fa-hand-o-right" aria-hidden="true"></i> ' + data.plate);
                            $('.modal-report-vehicle-speed').html('...');
                            $('.modal-report-vehicle-speed-average').html('...');
                            $('.modal-report-vehicle-speed-progress').css('width', parseInt(0) + '%');

                            $('.modal-report-dispatch-route-name').html(data.dispatchRegister.route.name);
                            $('.modal-report-dispatch-turn').html(data.dispatchRegister.turn);
                            $('.modal-report-dispatch-round-trip').html(data.dispatchRegister.round_trip);
                            $('.modal-report-dispatch-departure-time').html(data.dispatchRegister.departure_time);
                            $('.modal-report-dispatch-arrival-time').html(data.dispatchRegister.arrival_time);
                            $('.modal-report-dispatch-route-time').html(data.dispatchRegister.route_time);
                            $('.modal-report-dispatch-status').html(data.dispatchRegister.status);

                            $('.modal-report-route-percent').html(data.routePercent);
                            $('.modal-report-route-percent-progress').css('width', parseInt(data.routePercent) + '%');

                            $('.modal-report-driver').html(data.dispatchRegister.driver_name);

                            data.controlPoints.forEach(function (cp, i) {
                                new google.maps.Marker({
                                    title: cp.name+" > "+cp.distance_from_dispatch+" m.",
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
                            let averageSpeed = [];

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
                                averageSpeed[i] = report.averageSpeed;
                                speeding[i] = report.speeding ? 'label-danger' : '';

                                icon = pointMap[report.offRoad ? 2 : (report.trajectoryOfReturn ? 1 : 0)];

                                new google.maps.Marker({
                                    title: report.controlPointName + " | " + report.time + "("+report.timeReport+") | " + routeDistance + " m. | " + "  " + percent + "%",
                                    map: map,
                                    icon: icon,
                                    position: {lat: parseFloat(report.latitude), lng: parseFloat(report.longitude)}
                                });
                            });

                            if( data.center ){
                                setTimeout(()=>{
                                    map.setZoom(15);
                                    map.setCenter(new google.maps.LatLng(data.center.latitude, data.center.longitude));
                                },2000);
                            }

                            if( data.offRoadReport ){
                                panelOffRoad.find('.off-road').slideDown();
                                data.offRoadReport.forEach(function(offRoadReport, i){
                                    let offRoadDate = moment(offRoadReport.date.date);
                                    let tr = $('<tr></tr>');
                                    let tdDate = $('<td></td>').append(offRoadDate.format('hh:mm:ss'));
                                    tdDate.css('width', '40% !important');
                                    tr.append(tdDate);

                                    let optionsOffRoad = $('#template-button-off-road');
                                    let tdOptions = $('<td></td>').append(optionsOffRoad.html());
                                    tdOptions.css('width', '60% !important');

                                    let btnSeeOffRoad = tdOptions.find('.btn-see-off-road');
                                    btnSeeOffRoad.data('latitude', offRoadReport.latitude);
                                    btnSeeOffRoad.data('longitude', offRoadReport.longitude);

                                    let btnFakeOffRoad = tdOptions.find('.btn-fake-off-road');
                                    btnFakeOffRoad.attr('href', btnFakeOffRoad.attr('href') +'/'+offRoadReport.id);

                                    tr.append(tdOptions);
                                    panelOffRoadBody.append(tr);
                                });
                            }else{
                                panelOffRoad.find('.no-off-road').slideDown();
                            }

                            chartRouteReport.empty().hide().sparkline(dataValues, {
                                type: 'line',
                                width: (window.innerWidth-50)+'px',
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
                                        "<b>'.__('Traveled').':</b> {{offset:distance}} m <br>"+
                                        "<b>'.__('Completed').':</b> <span class=\'route-percent\'>{{offset:percent}}</span>%<br>"+
                                        "<span class=\'label p-0 {{offset:speed}} speed\' data-speed=\'{{offset:speed}}\' data-average=\'{{offset:averageSpeed}}\'><b>'.__('Speed').':</b> {{offset:speed}} Km/h <br></span>"+
                                        "<span class=\'hide latitude\'>{{offset:latitude}}</span><br>"+
                                        "<span class=\'hide longitude\'>{{offset:longitude}}</span>"+
                                    "</div>'?>",
                                tooltipValueLookups: {
                                    'offRoads': offRoads,
                                    'timesReports': dataTimesReports,
                                    'times': dataTimes,
                                    'distance': dataDistances,
                                    'speed': speed,
                                    'averageSpeed': averageSpeed,
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
                                    let speed = t.find('.speed').data('speed');
                                    let averageSpeed = t.find('.speed').data('average');
                                    let routePercent = t.find('.route-percent').html();

                                    if (!busMarker) {
                                        busMarker = new google.maps.Marker({
                                            map: map,
                                            icon: iconbus,
                                            animation: google.maps.Animation.DROP
                                        });
                                    }
                                    busMarker.setPosition({lat: parseFloat(latitude), lng: parseFloat(longitude)});

                                    if( !map.getBounds().contains(busMarker.getPosition()) ){
                                        //map.setCenter(busMarker.getPosition());
                                    }

                                    if( !isNaN(parseInt(speed)) && !isNaN(parseInt(averageSpeed))){
                                        $('.modal-report-vehicle-speed').html("@lang("Speed"): "+parseInt(speed)+" Km/h");
                                        $('.modal-report-vehicle-speed-average').html("@lang("Average speed"): "+parseInt(averageSpeed)+" Km/h");
                                        $('.modal-report-vehicle-speed-progress').css('width', parseInt(speed) + '%');
                                    }

                                    $('.modal-report-route-percent-progress').css('width', parseInt(routePercent) + '%');
                                    $('.modal-report-route-percent').html(routePercent);
                                }, 10);
                            }).bind('mouseleave', function () {
                                //busMarker ? busMarker.setMap(null) : null;
                                //busMarker = null;
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
            }).on('click', '.btn-see-off-road', function(e){
                map.setZoom(12);
                setTimeout(()=>{
                    map.setZoom(15);
                    map.panTo(new google.maps.LatLng($(this).data('latitude'), $(this).data('longitude')));
                },800);
            }).on('click', '.btn-fake-off-road', function(e){
                event.preventDefault();
                $.ajax({
                    url: $(this).attr('href'),
                    type: 'POST',
                    success: function(data){
                        console.log(data);
                        $('.btn-show-chart-route-report').click();
                    },
                    error:function(){
                        gerror('@lang('An error occurred in the process. Contact your administrator')');
                    }
                });
            });
        });
    </script>
@endsection