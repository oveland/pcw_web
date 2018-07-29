<script type="application/javascript">
    'use strict';

    class GeolocationPassengerReport {
        static createMap(data){
            $('.geolocation-map-container').slideUp();
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
                    let lastCount = 0;
                    let totalMarkers = 0;
                    $.each(report.data,function(i, r){
                        if (r.totalSensorRecorder - lastCount > 0) {
                            let passengerInfoWindow = GeolocationPassengerReport.createPassengerInfoWindow(r);
                            let marker = GeolocationPassengerReport.addPassengerMaker(r);
                            marker.addListener('click', function() {
                                passengerInfoWindow.open(map, marker);
                                $('.passenger-info-map').parent().css('overflow','hidden');
                            });
                            lastMarker = marker;
                            lastCount = r.totalSensorRecorder;
                            totalMarkers++;
                        }
                    });

                    new google.maps.KmlLayer({
                        url: report.route.url,
                        map: map
                    });

                    $('.total-recorder').text(report.counterByRecorder.passengersByRoundTrip);
                    $('.total-sensor-recorder').text(report.totalBySensorRecorder);
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
                icon: passengerMapIcon,
                animation: google.maps.Animation.DROP,
                position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}
            });
        }

        static createPassengerInfoWindow(r){
            let contentString =
                "<div class='row passenger-info-map' style='width: 200px'>" +
                "<div class='col-md-12'>"+
                "<div class=''>"+
                "<h5 class='text-info'><i class='fa fa-users'></i> <b>Información de conteo</b></h5>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class='hide'>"+
                "<i class='fa fa-crosshairs text-muted'></i> <b>Sensor: </b>"+r.total+"<br>"+
                "<i class='fa fa-arrow-circle-o-right text-muted'></i> <b>Front door: </b>"+r.totalFrontSensor+"<br>"+
                "<i class='fa fa-arrow-circle-o-left text-muted'></i> <b>Back door: </b>"+r.totalBackSensor+"<br>"+
                "<i class='fa fa-clock-o text-muted'></i> <small class='text-bold'>"+r.time+"</small><br>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class=''>"+
                "<i class='fa fa-compass text-muted'></i> <b>Sensor Recorder: </b>"+r.totalSensorRecorder+"<br>"+
                "<i class='fa fa-clock-o text-muted'></i> <small class='text-bold'>"+r.time+"</small><br>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class='hide'>"+
                "<i class='fa fa-compass text-muted'></i> <b> Recorder: </b>"+r.total+"<br>"+
                "<i class='fa fa-clock-o text-muted'></i> <small class='text-bold'>"+r.time+"</small><br>"+
                "<hr class='hr'>"+
                "</div>"+
                "<div class=''>"+
                "<i class='fa fa-bus text-muted'></i> <b>Estado: </b><span class='text-"+r.vehicleStatus.main_class+"'><i class='"+r.vehicleStatus.icon_class+"'></i> "+r.vehicleStatus.des_status+"</span><br>"+
                "</div>"+
                "</div>"+
                "</div>";
            return new google.maps.InfoWindow({
                content: contentString
            });
        }
    }
</script>