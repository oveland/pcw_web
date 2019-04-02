<script type="application/javascript">
    'use strict';

    class ReportRouteHistoric {
        constructor(map) {
            this.map = map;

            this.currentLocation = null;
            this.historicLocations = [];
            this.markerBus = null;

            this.historicPath = null;
            this.kmlLayer = null;

            this.iconPathSVG = 'M511.2,256c0-8.6-5.2-16.3-13.1-19.7L30.5,40.2c-8.7-3.7-18.9-1.1-24.8,6.3c-6,7.4-6.4,17.8-1,25.6l127.4,184L4.7,440 c-5.4,7.8-5,18.2,1,25.6c0.5,0.6,1,1.1,1.5,1.6c6.1,6.1,15.3,8,23.4,4.6l467.6-196.1C506,272.3,511.2,264.6,511.2,256z';

            this.showInfo = $('.show-info');
        }

        processHistoricReportData(report) {
            $('.map-report-historic').css('height', (window.innerHeight - 100));

            this.clearMap();

            $.each(report.historic, (i, reportLocation) => {
                let historicInfoWindow = this.createInfoWindow(reportLocation);
                let marker = this.addHistoricMarker(reportLocation);
                marker.addListener('click', () => {
                    historicInfoWindow.open(this.map, marker);
                    $('.historic-info-map').parent().css('overflow', 'hidden');
                });

                this.historicLocations.push({
                    marker: marker,
                    shadowMarker: this.addShadowMarker(reportLocation),
                    infoWindow: historicInfoWindow,
                    reportLocation: reportLocation
                });
            });

            this.historicPath = new google.maps.Polyline({
                path: [],
                geodesic: true,
                strokeColor: 'rgba(118,0,255,0.58)',
                strokeOpacity: 0.9,
                strokeWeight: 5,
                map: this.map
            });

            const kmzUrl = $('#route-report').find('option:selected').data('kmz-url');
            if (kmzUrl) {
                this.kmlLayer = new google.maps.KmlLayer({
                    url: kmzUrl,
                    map: this.map
                });
            }

            this.showInfo.find('.time-from').text(report.from);
            this.showInfo.find('.time-to').text(report.to);
            this.showInfo.find('.total').text(report.total);

            $('#range_reports').data("ionRangeSlider").update({
                min: 0,
                max: report.total,
                from: 0
            });

            if (this.historicLocations.length) {
                this.updateBusMarker(0);
            } else {
                gwarning("@lang("No registers found")");
            }

            setTimeout(() => {
                if (this.markerBus) {
                    this.map.panTo(this.markerBus.getPosition());
                }
                window.scrollTo(0, document.body.scrollHeight);
            }, 1500);
        }

        addHistoricMarker(r) {
            let rotation = parseInt(r.orientation);

            const icon = {
                path: this.iconPathSVG,
                fillOpacity: 0.9,
                fillColor: '#a1bf00',
                strokeColor: '#008a54',
                scale: .02,
                strokeWeight: 1,
                anchor: new google.maps.Point(220, 250),
                rotation: rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation)
            };

            return new google.maps.Marker({
                title: r.vehicleStatus.status + " " + r.time,
                map: null,
                icon: icon,
                position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}
            });
        }

        addShadowMarker(r) {
            let rotation = parseInt(r.orientation);

            const icon = {
                path: this.iconPathSVG,
                fillOpacity: 0.1,
                fillColor: '#5d1800',
                scale: .02,
                strokeWeight: 0,
                anchor: new google.maps.Point(220, 250),
                rotation: rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation)
            };

            return new google.maps.Marker({
                title: r.vehicleStatus.status + " " + r.time,
                map: this.map,
                icon: icon,
                position: {lat: parseFloat(r.latitude), lng: parseFloat(r.longitude)}
            });
        }

        clearMap() {
            this.historicLocations.forEach((historicLocation) => {
                historicLocation.marker.setMap(null);
                historicLocation.shadowMarker.setMap(null);
            });

            this.historicLocations = [];

            if (this.markerBus) {
                this.markerBus.setMap(null);
                this.markerBus = null;
            }

            if (this.kmlLayer) {
                this.kmlLayer.setMap(null);
                this.kmlLayer = null;
            }

            if (this.historicPath) {
                this.historicPath.setMap(null);
                this.historicPath = null;
            }
        }

        paintHistoricPathTo(index) {
            let path = this.historicPath.getPath();

            this.historicLocations.forEach((historicLocation, i) => {
                path.removeAt(i);

                if (i <= index) {
                    historicLocation.marker.setMap(this.map);
                    historicLocation.shadowMarker.setMap(null);

                    //if (!path.getAt(i))
                    path.insertAt(i, historicLocation.marker.position);
                } else {
                    historicLocation.marker.setMap(null);
                    historicLocation.shadowMarker.setMap(this.map);
                }
            });

            path.forEach((p, i) => {
                if( i > index ){
                    path.removeAt(i);
                }
            });
        }

        updateBusMarker(index) {
            const historicLocation = this.historicLocations[index];
            if (!historicLocation) return false;

            this.paintHistoricPathTo(index);

            const marker = historicLocation.marker;
            const infoWindow = historicLocation.infoWindow;
            const reportLocation = historicLocation.reportLocation;

            let rotation = parseInt(reportLocation.orientation);

            const icon = {
                path: this.iconPathSVG,
                fillOpacity: 1,
                fillColor: '#04bf8a',
                strokeColor: '#0f678a',
                scale: .045,
                strokeWeight: 2,
                anchor: new google.maps.Point(220, 250),
                rotation: rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation)
            };

            if (this.markerBus) {
                this.markerBus.setPosition(marker.getPosition());
                this.markerBus.setIcon(icon);
            } else {

                this.markerBus = new google.maps.Marker({
                    map: this.map,
                    position: marker.getPosition(),
                    icon: icon,
                    duration: 500,
                    easing: "swing",
                    title: marker.getTitle(),
                    shadow: ""
                });
            }

            if (this.currentLocation && this.currentLocation.infoWindow.getMap()) {
                this.currentLocation.infoWindow.close();
                infoWindow.open(this.map, this.markerBus);
            } else {
                /*this.markerBus.addListener('click', () => {
                    infoWindow.open(this.map, this.markerBus);
                });*/
            }

            if (!map.getBounds().contains(this.markerBus.getPosition())) {
                this.map.panTo(this.markerBus.getPosition());
            }

            this.currentLocation = historicLocation;

            this.showInfo.find('.time').text(reportLocation.time);
            this.showInfo.find('.speed').text(reportLocation.speed);
            this.showInfo.find('.status-vehicle').html(
                "<small class='text-" + reportLocation.vehicleStatus.mainClass + "'>" +
                "<i class='" + reportLocation.vehicleStatus.iconClass + "'></i> " + reportLocation.vehicleStatus.status +
                "</small>"
            );

            $('.gm-style-iw-c, .gm-style-iw-d').css('max-height', '300px').css('height', '270px');
        }

        createInfoWindow(r) {
            let infoDispatchRegister = '';
            let height = '200px';
            if (r.dispatchRegister) {
                let dr = r.dispatchRegister;
                infoDispatchRegister = "" +
                    "<small class='text-bold'><i class='fa fa-flag text-muted'></i> @lang('Route'): " + dr.route.name + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-retweet text-muted'></i> @lang('Round Trip'): " + dr.round_trip + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-list-ol text-muted'></i> @lang('Turn'): " + dr.turn + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-clock-o text-muted'></i> @lang('Dispatched'): " + dr.departure_time + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-user text-muted'></i> @lang('Driver'): " + dr.driver_name + "</small><br>" +
                    "<hr class='hr'>";

                let height = '250px';
            }
            let infoAddress = "";
            if (r.address) {
                infoAddress = "" +
                    "<small class='text-bold'><i class='fa fa-map-o text-muted'></i> " + r.address + "</small><br>" +
                    "";
            }

            let contentString =
                "<div class='historic-info-map' style='width: 200px'>" +
                "<div class='col-md-12' style='height:" + height + "'>" +
                "<div class=''>" +
                "<h5 class='text-info'><i class='fa fa-bus'></i> <b>" + r.vehicle.number + "</b></h5>" +
                infoAddress +
                "<hr class='hr'>" +
                "</div>" +
                "<div class=''>" +
                "<small class='text-bold'><i class='fa fa-calendar text-muted'></i> " + r.date + "</small><br>" +
                "<small class='text-bold'><i class='fa fa-clock-o text-muted'></i> " + r.time + "</small><br>" +
                "<small class='text-bold text-" + (r.speeding ? 'danger' : '') + "'><i class='fa fa-tachometer text-muted'></i> @lang('Speed'): " + r.speed + " Km/h</small><br>" +
                "<small class='text-bold'><i class='fa fa-road text-muted'></i> " + r.currentMileage + " Km</small><br>" +
                "<hr class='hr'>" + infoDispatchRegister +
                "<small class='text-bold text-" + r.vehicleStatus.mainClass + "'><i class='" + r.vehicleStatus.iconClass + "'></i> " + r.vehicleStatus.status + "</small><br>" +
                "</div>" +
                "</div>" +
                "</div>";

            return new google.maps.InfoWindow({
                content: contentString
            });
        }
    }
</script>