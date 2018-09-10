<script type="application/javascript">
    'use strict';

    class GeolocationPassengerReport {
        static createMap(data){
            $('.geolocation-map-container, .counter').slideUp();
            $('.loading-geolocation-map').html($('#loading').html()).slideDown();

            $.ajax({
                url: '{{ route('report-passengers-geolocation-search') }}',
                data: data,
                dataType: 'json',
                success: function (report) {
                    GeolocationPassengerReport.processPassengerReportData(report);
                }
            });
        }

        static processPassengerReportData(report){
            $('.loading-geolocation-map').slideUp();
            $('.geolocation-map-container').hide().slideDown(500, function(){
                initializeMap();
                setTimeout(function(){
                    let lastMarker = null;
                    let lastSensorCount = 0;
                    let lastSensorRecorderCount = 0;
                    let totalMarkers = 0;
                    $.each(report.data,function(i, r){
                        if ( (r.totalSensorRecorder - lastSensorRecorderCount) > 0 || (r.total - lastSensorCount) > 0 ) {
                            let passengerInfoWindow = GeolocationPassengerReport.createPassengerInfoWindow(r, report.displayData);
                            let marker = GeolocationPassengerReport.addPassengerMaker(r);
                            marker.addListener('click', function() {
                                passengerInfoWindow.open(map, marker);
                                $('.passenger-info-map').parent().css('overflow','hidden');
                            });
                            lastMarker = marker;
                            lastSensorCount = r.total;
                            lastSensorRecorderCount = r.totalSensorRecorder;
                            totalMarkers++;
                        }
                    });

                    new google.maps.KmlLayer({
                        url: report.route.url,
                        map: map
                    });

                    if (report.displayData.showRecorderCount) $('.total-recorder').text(report.counterByRecorder.passengersByRoundTrip).parent().slideDown(500);
                    if (report.displayData.showSensorCount) $('.total-sensor').text(report.counterBySensor.totalBySensorByRoundTrip).parent().slideDown(500);
                    if (report.displayData.showSensorRecorderCount) $('.total-sensor-recorder').text(report.counterBySensor.totalBySensorRecorderByRoundTrip).parent().slideDown(500);

                    $('.departure-time').text(report.counterByRecorder.departureTime);
                    $('.arrival-time').text(report.counterByRecorder.arrivalTime);

                    if (lastMarker) map.setCenter(lastMarker.getPosition());
                },500);
            });
        }

        static addPassengerMaker(r){
            return new google.maps.Marker({
                title: "Total: "+r.total+" ("+r.time+")",
                map: map,
                icon: '{{ asset('img/passenger-map.png') }}',
                animation: google.maps.Animation.DROP,
                position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}
            });
        }

        static createPassengerInfoWindow(r, displayData){
            let contentString =
                "<div class='row passenger-info-map' style='width: 200px'>" +
                "<div class='col-md-12'>"+
                "<div class=''>"+
                "<h5 class='text-info'><i class='fa fa-users'></i> <b>@lang('Count information')</b></h5>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class='"+(displayData.showSensorRecorderCount ? '' : 'hide')+"'>"+
                "<i class='fa fa-compass text-muted'></i> <b>@lang('Sensor recorder'): </b>"+r.totalSensorRecorder+"<br>"+
                "<i class='fa fa-clock-o text-muted'></i> <small class='text-bold'>"+r.time+"</small><br>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class='"+(displayData.showSensorCount ? '' : 'hide')+"'>"+
                "<i class='fa fa-crosshairs text-muted'></i> <b>@lang('Sensor'): </b>"+r.total+"<br>"+
                "<div class='hide'>"+
                "<i class='fa fa-arrow-circle-o-right text-muted'></i> <b>Front door: </b>"+r.totalFrontSensor+"<br>"+
                "<i class='fa fa-arrow-circle-o-left text-muted'></i> <b>Back door: </b>"+r.totalBackSensor+"<br>"+
                "</div>"+
                "<i class='fa fa-clock-o text-muted'></i> <small class='text-bold'>"+r.time+"</small><br>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class=''>"+
                "<i class='fa fa-bus text-muted'></i> <b>@lang('Status'): </b><span class='text-"+r.vehicleStatus.main_class+"'><i class='"+r.vehicleStatus.icon_class+"'></i> "+r.vehicleStatus.des_status+"</span><br>"+
                "</div>"+
                "</div>"+
                "</div>";
            return new google.maps.InfoWindow({
                content: contentString
            });
        }
    }
</script>