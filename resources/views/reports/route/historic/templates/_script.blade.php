<script type="application/javascript">
    'use strict';

    let markers = [];
    let historicInfoWindows = [];
    let markerBus = null;
    let infoWindowsBus = null;

    class ReportRouteHistoric {

        static processHistoricReportData(report){
            let lastMarker = null;

            markers.forEach(function(m){
                m.setMap(null);
            });
            markers = [];

            if(markerBus){
                markerBus.setMap(null);
                markerBus = null;
            }

            $.each(report.historic,function(i, r){
                let historicInfoWindow = ReportRouteHistoric.createInfoWindow(r);
                let marker = ReportRouteHistoric.addMarker(r);
                marker.addListener('click', function() {
                    historicInfoWindow.open(map, marker);
                    $('.historic-info-map').parent().css('overflow','hidden');
                });
                lastMarker = marker;
                markers.push(marker);
                historicInfoWindows.push(historicInfoWindow);
            });

            /*new google.maps.KmlLayer({
                url: report.route.url,
                map: map
            });*/

            $('.time-from').text(report.from);
            $('.time-to').text(report.to);
            $('.total').text(report.total);

            $('#range_reports').data("ionRangeSlider").update({
                min: 0,
                max: report.total,
                from: 0
            });

            if( markers.length ){
                this.updateBusMarker(markers[0], historicInfoWindows[0]);
            }

            if (markerBus) map.setCenter(markerBus.getPosition());
        }

        static addMarker(r){
            return new google.maps.Marker({
                title: r.vehicleStatus.status+" "+r.time,
                map: map,
                icon: '{{ asset('img/point-map-on-return-road.png') }}',
                //animation: google.maps.Animation.DROP,
                position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}
            });
        }

        static updateBusMarker(marker, historicInfoWindow){
            if( markerBus ){
                markerBus.setPosition(marker.getPosition());
            }else{
                markerBus = new google.maps.Marker({
                    title: marker.getTitle(),
                    map: map,
                    icon: '{{ asset('img/bus.png') }}',
                    animation: google.maps.Animation.DROP,
                    position: marker.getPosition()
                });
            }

            if(infoWindowsBus){
                infoWindowsBus.close();
            }

            historicInfoWindow.open(map, markerBus);
            infoWindowsBus = historicInfoWindow;
        }

        static createInfoWindow(r){
            let infoDispatchRegister = '';
            if(r.dispatchRegister){
                let dr = r.dispatchRegister;
                infoDispatchRegister = ""+
                    "<small class='text-bold'><i class='fa fa-flag text-muted'></i> @lang('Route'): "+dr.route.name+"</small><br>"+
                    "<small class='text-bold'><i class='fa fa-retweet text-muted'></i> @lang('Round Trip'): "+dr.round_trip+"</small><br>"+
                    "<small class='text-bold'><i class='fa fa-list-ol text-muted'></i> @lang('Turn'): "+dr.turn+"</small><br>"+
                    "<small class='text-bold'><i class='fa fa-clock-o text-muted'></i> @lang('Dispatched'): "+dr.departure_time+"</small><br>"+
                    "<small class='text-bold'><i class='fa fa-user text-muted'></i> @lang('Driver'): "+dr.driver_name+"</small><br>"+
                    "<hr class='hr'>";
            }
            let infoAddress = "";
            if(r.address){
                infoAddress = ""+
                    "<small class='text-bold'><i class='fa fa-map-o text-muted'></i> "+r.address+"</small><br>"+
                "";
            }

            let contentString =
                "<div class='historic-info-map' style='width: 200px'>" +
                    "<div class='col-md-12'>"+
                        "<div class=''>"+
                            "<h5 class='text-info'><i class='fa fa-bus'></i> <b>@lang('Information')</b></h5>"+
                            infoAddress +
                            "<hr class='hr'>"+
                        "</div>"+
                        "<div class=''>"+
                            "<small class='text-bold'><i class='fa fa-clock-o text-muted'></i> "+r.time+"</small><br>"+
                            "<small class='text-bold text-"+( r.speeding ?'danger':'')+"'><i class='fa fa-tachometer text-muted'></i> @lang('Speed'): "+r.speed+" Km/h</small><br>"+
                            "<small class='text-bold'><i class='fa fa-road text-muted'></i> "+r.currentMileage+" Km</small><br>"+
                            "<hr class='hr'>"+infoDispatchRegister+
                            "<small class='text-bold text-"+r.vehicleStatus.mainClass+"'><i class='"+r.vehicleStatus.iconClass+"'></i> "+r.vehicleStatus.status+"</small><br>"+
                        "</div>"+
                    "</div>"+
                "</div>";

            return new google.maps.InfoWindow({
                content: contentString
            });
        }
    }
</script>