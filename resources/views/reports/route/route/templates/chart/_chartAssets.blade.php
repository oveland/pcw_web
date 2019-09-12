@section('templateStyles')
<style type="text/css">
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
        let iconBus = '{{ asset('img/bus.png') }}';

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

        const iconPathSVG = 'M511.2,256c0-8.6-5.2-16.3-13.1-19.7L30.5,40.2c-8.7-3.7-18.9-1.1-24.8,6.3c-6,7.4-6.4,17.8-1,25.6l127.4,184L4.7,440 c-5.4,7.8-5,18.2,1,25.6c0.5,0.6,1,1.1,1.5,1.6c6.1,6.1,15.3,8,23.4,4.6l467.6-196.1C506,272.3,511.2,264.6,511.2,256z';
        const iconPowerOffSVG = 'M400 54.1c63 45 104 118.6 104 201.9 0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 173.1 48.9 99.3 111.8 54.2c11.7-8.3 28-4.8 35 7.7L162.6 90c5.9 10.5 3.1 23.8-6.6 31-41.5 30.8-68 79.6-68 134.9-.1 92.3 74.5 168.1 168 168.1 91.6 0 168.6-74.2 168-169.1-.3-51.8-24.7-101.8-68.1-134-9.7-7.2-12.4-20.5-6.5-30.9l15.8-28.1c7-12.4 23.2-16.1 34.8-7.8zM296 264V24c0-13.3-10.7-24-24-24h-32c-13.3 0-24 10.7-24 24v240c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24z';
        const iconParkedOffSVG = 'M326.3 218.8c0 20.5-16.7 37.2-37.2 37.2h-70.3v-74.4h70.3c20.5 0 37.2 16.7 37.2 37.2zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-128.1-37.2c0-47.9-38.9-86.8-86.8-86.8H169.2v248h49.6v-74.4h70.3c47.9 0 86.8-38.9 86.8-86.8z';
        const iconWithOutGPSSVG = 'M216 288h-48c-8.84 0-16 7.16-16 16v192c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V304c0-8.84-7.16-16-16-16zM88 384H40c-8.84 0-16 7.16-16 16v96c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16v-96c0-8.84-7.16-16-16-16zm256-192h-48c-8.84 0-16 7.16-16 16v288c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V208c0-8.84-7.16-16-16-16zm128-96h-48c-8.84 0-16 7.16-16 16v384c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V112c0-8.84-7.16-16-16-16zM600 0h-48c-8.84 0-16 7.16-16 16v480c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V16c0-8.84-7.16-16-16-16z';


        function processSVGIcon(reportLocation){
            let rotation = parseInt(reportLocation.orientation);
            rotation = rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation);

            let scale = .02;
            let zIndex = 10;
            let pathSVG = iconPathSVG;
            let fillColor = '#04bf8a';
            let strokeColor = '#0f678a';
            let x = 220;
            let y = 250;

            if (reportLocation.trajectoryOfReturn) {
                fillColor = '#bfa017';
                strokeColor = '#008a54';
            }

            if (reportLocation.offRoad) {
                fillColor = '#6a000e';
                strokeColor = '#ba0046';
            }

            const dr = reportLocation.dispatchRegister;

            if(reportLocation.vehicleStatus.id === 6 && !dr){
                rotation = 0;
                pathSVG = iconPowerOffSVG;
                fillColor = '#bf1308';
                strokeColor = '#c2c2c2';
                scale = .035;
                zIndex = 100;
                x = 250;
                y = 280;
            }

            if(reportLocation.vehicleStatus.id === 3){
                rotation = 0;
                pathSVG = iconParkedOffSVG;
                fillColor = '#1300ce';
                strokeColor = 'rgb(181,181,181)';
                scale = .038;
                zIndex = 100;
                x = 250;
                y = 280;
            }else if(reportLocation.vehicleStatus.id === 5){
                rotation = 0;
                pathSVG = iconWithOutGPSSVG;
                fillColor = '#fffd06';
                strokeColor = '#d4760a';
                scale = .03;
                zIndex = 100;
                x = 250;
                y = 280;
            }
            else if (reportLocation.speeding) {
                fillColor = '#ffe415';
                strokeColor = '#d44200';
                zIndex = 100;
            }

            return {
                path: pathSVG,
                rotation: rotation,
                fillColor : fillColor,
                strokeColor : strokeColor,
                scale: scale,
                zIndex: zIndex,
                anchor:{
                    x: x,
                    y: y,
                }
            };
        }

        $(document).ready(function () {
            initializeMap(() => {
                /*loadScript("https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js", function(){
                    loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js", function(){
                        loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js", function(){
                            SlidingMarker.initializeGlobally();
                            //$('.map-report-historic').css('height', (window.innerHeight - 150));
                        });
                    });
                });*/
            });
            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            }).on('hidden.bs.modal', function () {
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

                try{
                    chartRouteReport.sparkline('destroy')
                }catch(e){
                    console.log('error destroy sparkline');
                }

                chartRouteReport.empty().html(loading);
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
                                    title: cp.name+" > "+cp.distance_from_dispatch+" m",
                                    map: map,
                                    icon: controlPointIcon[cp.trajectory],
                                    animation: google.maps.Animation.DROP,
                                    position: {lat: parseFloat(cp.latitude), lng: parseFloat(cp.longitude)}
                                });
                            });

                            @if(Auth::user()->isAdmin())
                            data.routeCoordinates.forEach(function (rc, i) {
                                new google.maps.Marker({
                                    title: rc.index + " > " + rc.distance + " m",
                                    map: map,
                                    icon: routeCoordinateIcon,
                                    position: {lat: parseFloat(rc.latitude), lng: parseFloat(rc.longitude)}
                                });
                            });
                            @endif

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
                            let orientations = [];
                            let vehicleStatusId = [];
                            let offRoads = [];
                            let speeding = [];
                            let speedingLabel = [];
                            let speed = [];
                            let averageSpeed = [];

                            @if(!Auth::user()->isSuperAdmin2())
                                let historicPath = new google.maps.Polyline({
                                    path: [],
                                    geodesic: true,
                                    strokeColor: 'rgba(118,0,255,0.58)',
                                    strokeOpacity: 0.8,
                                    strokeWeight: 5,
                                    map: map
                                });

                                let path = historicPath.getPath();
                            @endif


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
                                orientations[i] = report.orientation;
                                vehicleStatusId[i] = '0'+report.vehicleStatus.id;
                                offRoads[i] = report.offRoad ? '' : 'hide';
                                speed[i] = report.speed;
                                averageSpeed[i] = report.averageSpeed;
                                speeding[i] = report.speeding ? 'speeding':'none';
                                speedingLabel[i] = report.speeding ? 'label-danger' : '';

                                const svg = processSVGIcon(report);

                                const icon = {
                                    path: svg.path,
                                    fillOpacity: 1,
                                    fillColor: svg.fillColor,
                                    strokeColor: svg.strokeColor,
                                    scale: svg.scale,
                                    strokeWeight: 1,
                                    anchor: new google.maps.Point(svg.anchor.x, svg.anchor.y),
                                    rotation: svg.rotation
                                };

                                let marker = new google.maps.Marker({
                                    title: report.controlPointName + " | " + report.time + "("+report.timeReport+") | " + routeDistance + " m. | " + "  " + percent + "%",
                                    map: map,
                                    icon: icon,
                                    zIndex: svg.zIndex,
                                    position: {lat: parseFloat(report.latitude), lng: parseFloat(report.longitude)}
                                });
                                @if(!Auth::user()->isSuperAdmin2())
                                    path.insertAt(i, marker.position);
                                @endif
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
                                tooltipFormat: "<?='<div class=\'info-route-report\' style=\'position: static !important;z-index: auto !important;\'>"+
                                        "<span class=\'{{offset:offRoads}}\'><span class=\'label label-danger f-s-10 m-b-10\'>"+
                                        "    <i class=\'ion-merge m-r-5 fs-12 fa-fw\'></i> '.__('Off road vehicle').'</span><hr class=\'m-5\'>"+
                                        "</span>"+
                                        "<b class=\'m-t-10\'>'.__('Status').':</b> {{offset:timesReports}} <br>"+
                                        "<b>'.__('Time').':</b> {{offset:times}} <br>"+
                                        "<b>'.__('Traveled').':</b> {{offset:distance}} m <br>"+
                                        "<b>'.__('Completed').':</b> <span class=\'route-percent\'>{{offset:percent}}</span>%<br>"+
                                        "<span class=\'label p-0 {{offset:speedingLabel}} speed\' data-speed=\'{{offset:speed}}\' data-average=\'{{offset:averageSpeed}}\'><b>'.__('Speed').':</b> {{offset:speed}} Km/h <br></span>"+
                                        "<span class=\'hide latitude\'>{{offset:latitude}}</span><br>"+
                                        "<span class=\'hide longitude\'>{{offset:longitude}}</span>"+
                                        "<span class=\'hide orientation\'>{{offset:orientation}}</span>"+
                                        "<span class=\'hide speeding\'>{{offset:speeding}}</span>"+
                                        "<span class=\'hide off-road\'>{{offset:offRoads}}</span>"+
                                        "<span class=\'hide vehicle-status-id\'>{{offset:vehicleStatusId}}</span>"+
                                    "</div>'?>",
                                tooltipValueLookups: {
                                    'offRoads': offRoads,
                                    'timesReports': dataTimesReports,
                                    'times': dataTimes,
                                    'distance': dataDistances,
                                    'speed': speed,
                                    'averageSpeed': averageSpeed,
                                    'speeding': speeding,
                                    'speedingLabel': speedingLabel,
                                    'percent': completedPercent,
                                    'latitude': latitudes,
                                    'longitude': longitudes,
                                    'orientation': orientations,
                                    'vehicleStatusId': vehicleStatusId,
                                }
                            }).slideDown();

                            chartRouteReport.bind('sparklineRegionChange', function (ev) {
                                //let sparkline = ev.sparklines[0],info = sparkline.getCurrentRegionFields();
                                setTimeout(function () {
                                    let t = $('.info-route-report');
                                    let latitude = t.find('.latitude').html();
                                    let longitude = t.find('.longitude').html();
                                    let orientation = t.find('.orientation').html();
                                    let speed = t.find('.speed').data('speed');
                                    let speeding = t.find('.speeding').text() === 'speeding';
                                    let offRoad = t.find('.off-road').text() === '';
                                    let averageSpeed = t.find('.speed').data('average');
                                    let routePercent = t.find('.route-percent').html();
                                    let vehicleStatusId = t.find('.vehicle-status-id').html();

                                    if(latitude === undefined || longitude === undefined)return false;

                                    const svg = processSVGIcon({
                                        orientation: parseInt(orientation),
                                        offRoad: offRoad,
                                        speeding: speeding,
                                        vehicleStatus: {
                                            id: parseInt(vehicleStatusId)
                                        },
                                        dispatchRegister: true
                                    });

                                    const icon = {
                                        path: svg.path,
                                        fillOpacity: 1,
                                        fillColor: svg.fillColor,
                                        strokeColor: svg.strokeColor,
                                        scale: .045,
                                        strokeWeight: 2,
                                        anchor: new google.maps.Point(svg.anchor.y, svg.anchor.y),
                                        rotation: svg.rotation
                                    };

                                    if (!busMarker) {
                                        busMarker = new google.maps.Marker({
                                            map: map,
                                            icon: icon,
                                            duration: 150,
                                            zIndex: 200,
                                        });
                                    }
                                    busMarker.setPosition({lat: parseFloat(latitude), lng: parseFloat(longitude)});
                                    busMarker.setIcon(icon);

                                    if( !map.getBounds().contains(busMarker.getPosition()) ){
                                        //map.setCenter(busMarker.getPosition());
                                    }

                                    if( !isNaN(parseInt(speed)) && !isNaN(parseInt(averageSpeed))){
                                        $('.modal-report-vehicle-speed').html(parseInt(speed)+" Km/h");
                                        $('.modal-report-vehicle-speed-average').html(parseInt(averageSpeed)+" Km/h");
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