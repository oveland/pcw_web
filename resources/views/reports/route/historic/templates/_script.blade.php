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
            this.iconPowerOffSVG = 'M400 54.1c63 45 104 118.6 104 201.9 0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 173.1 48.9 99.3 111.8 54.2c11.7-8.3 28-4.8 35 7.7L162.6 90c5.9 10.5 3.1 23.8-6.6 31-41.5 30.8-68 79.6-68 134.9-.1 92.3 74.5 168.1 168 168.1 91.6 0 168.6-74.2 168-169.1-.3-51.8-24.7-101.8-68.1-134-9.7-7.2-12.4-20.5-6.5-30.9l15.8-28.1c7-12.4 23.2-16.1 34.8-7.8zM296 264V24c0-13.3-10.7-24-24-24h-32c-13.3 0-24 10.7-24 24v240c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24z';
            this.iconParkedOffSVG = 'M326.3 218.8c0 20.5-16.7 37.2-37.2 37.2h-70.3v-74.4h70.3c20.5 0 37.2 16.7 37.2 37.2zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-128.1-37.2c0-47.9-38.9-86.8-86.8-86.8H169.2v248h49.6v-74.4h70.3c47.9 0 86.8-38.9 86.8-86.8z';
            this.iconWithOutGPSSVG = 'M216 288h-48c-8.84 0-16 7.16-16 16v192c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V304c0-8.84-7.16-16-16-16zM88 384H40c-8.84 0-16 7.16-16 16v96c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16v-96c0-8.84-7.16-16-16-16zm256-192h-48c-8.84 0-16 7.16-16 16v288c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V208c0-8.84-7.16-16-16-16zm128-96h-48c-8.84 0-16 7.16-16 16v384c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V112c0-8.84-7.16-16-16-16zM600 0h-48c-8.84 0-16 7.16-16 16v480c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V16c0-8.84-7.16-16-16-16z';


            this.showInfo = $('.show-info');
        }

        processSVGIcon(reportLocation){
            let rotation = parseInt(reportLocation.orientation);
            rotation = rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation);

            let scale = .02;
            let zIndex = 10;
            let pathSVG = this.iconPathSVG;
            let fillColor = '#04bf8a';
            let strokeColor = '#0f678a';
            let x = 220;
            let y = 250;

            if (reportLocation.offRoad) {
                fillColor = '#6a000e';
                strokeColor = '#ba0046';
            }

            const dr = reportLocation.dispatchRegister;

            if(reportLocation.vehicleStatus.id === 6 && !dr){
                rotation = 0;
                pathSVG = this.iconPowerOffSVG;
                fillColor = '#bf1308';
                strokeColor = '#c2c2c2';
                scale = .035;
                zIndex = 100;
                x = 250;
                y = 280;
            }
            else if(reportLocation.vehicleStatus.id === 3){
                rotation = 0;
                pathSVG = this.iconParkedOffSVG;
                fillColor = '#1300ce';
                strokeColor = 'rgb(181,181,181)';
                scale = .038;
                zIndex = 100;
                x = 250;
                y = 280;
            }else if(reportLocation.vehicleStatus.id === 5){
                rotation = 0;
                pathSVG = this.iconWithOutGPSSVG;
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

        processHistoricReportData(report) {
            $('.map-report-historic').css('height', (window.innerHeight - 100));

            $.each(report.historic, (i, reportLocation) => {
                // let historicInfoWindow = this.createInfoWindow(reportLocation);
                let marker = this.addHistoricMarker(reportLocation);
                // marker.addListener('click', () => {
                //     historicInfoWindow.open(this.map, marker);
                //     $('.historic-info-map').parent().css('overflow', 'hidden');
                // });

                this.historicLocations.push({
                    marker: marker,
                    //shadowMarker: this.addShadowMarker(reportLocation),
                    // infoWindow: historicInfoWindow,
                    reportLocation: reportLocation
                });
            });

            this.historicPath = new google.maps.Polyline({
                path: [],
                geodesic: true,
                strokeColor: 'rgba(118,0,255,0.58)',
                strokeOpacity: 0.8,
                strokeWeight: 5,
                map: this.map
            });

            this.showInfo.find('.time-from').text(report.from);
            this.showInfo.find('.time-to').text(report.to);
            this.showInfo.find('.total').text(report.total);

            if (this.historicLocations.length) {
                this.updateBusMarker(report.total - 1);
                setTimeout(() => {
                    $("html, body").animate({scrollTop: $(".range-reports").offset().top - 40}, 1000);
                    const kmzUrl = $('#route-report').find('option:selected').data('kmz-url');
                    if (kmzUrl) {
                        this.kmlLayer = new google.maps.KmlLayer({
                            url: kmzUrl,
                            map: this.map
                        });
                    }
                }, 1500);
            } else {
                gwarning("@lang("No registers found")");
            }
        }

        addHistoricMarker(r) {
            const svg = this.processSVGIcon(r);

            const icon = {
                path: svg.path,
                fillOpacity: 1,
                fillColor: svg.fillColor,
                strokeColor: svg.strokeColor,
                scale: svg.scale,
                zIndex: svg.zIndex,
                strokeWeight: 1,
                anchor: new google.maps.Point(svg.anchor.x, svg.anchor.y),
                rotation: svg.rotation
            };

            return new google.maps.Marker({
                title: r.vehicleStatus.status + " " + r.time,
                map: null,
                icon: icon,
                zIndex: svg.zIndex,
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
                //historicLocation.shadowMarker.setMap(null);
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

        async paintHistoricPathTo(index) {
            let path = this.historicPath.getPath();

            this.historicLocations.forEach(async (historicLocation, i) => {
                path.removeAt(i);
                if (i <= index){
                    await path.insertAt(i, historicLocation.marker.position);
                }

                if (i >= (index - 50) && i <= index) {
                    historicLocation.marker.setMap(this.map);
                    //historicLocation.shadowMarker.setMap(null);
                } else {
                    historicLocation.marker.setMap(null);
                    //historicLocation.shadowMarker.setMap(this.map);
                }

                /*if (i <= index) {
                    historicLocation.marker.setMap(this.map);
                    //historicLocation.shadowMarker.setMap(null);
                } else {
                    historicLocation.marker.setMap(null);
                    //historicLocation.shadowMarker.setMap(this.map);
                }*/
            });

            await path.forEach((p, i) => {
                if (i > index) {
                    path.removeAt(i);
                }
            });
        }

        updateBusMarker(index) {
            const historicLocation = this.historicLocations[index];
            if (!historicLocation) return false;

            this.paintHistoricPathTo(index);

            const marker = historicLocation.marker;
            // const infoWindow = historicLocation.infoWindow;
            const reportLocation = historicLocation.reportLocation;

            const svg = this.processSVGIcon(reportLocation);

            const icon = {
                path: svg.path,
                fillOpacity: 1,
                fillColor: svg.fillColor,
                strokeColor: svg.strokeColor,
                scale: .045,
                strokeWeight: 2,
                anchor: new google.maps.Point(svg.anchor.x, svg.anchor.y),
                rotation: svg.rotation
            };

            if (this.markerBus) {
                this.markerBus.setPosition(marker.getPosition());
                this.markerBus.setIcon(icon);
            } else {

                this.markerBus = new google.maps.Marker({
                    map: this.map,
                    position: marker.getPosition(),
                    icon: icon,
                    duration: 200,
                    easing: "swing",
                    title: marker.getTitle(),
                    shadow: ""
                });
            }

            // if (this.currentLocation && this.currentLocation.infoWindow.getMap()) {
            //     this.currentLocation.infoWindow.close();
            //     infoWindow.open(this.map, this.markerBus);
            // } else {
            //     //this.markerBus.addListener('click', () => {
            //     //    infoWindow.open(this.map, this.markerBus);
            //     //});
            // }

            if (!map.getBounds().contains(this.markerBus.getPosition())) {
                this.map.panTo(this.markerBus.getPosition());
            }

            this.currentLocation = historicLocation;

            const routeLabel = this.showInfo.find('.route');
            if (reportLocation.dispatchRegister) {
                const dr = reportLocation.dispatchRegister;
                routeLabel.text(dr.id + " " + dr.route.name).parent().fadeIn();
                this.showInfo.find('.mileage-route').text(reportLocation.routeDistance);
                if (reportLocation.offRoad) {
                    routeLabel.parent().addClass('btn-danger').attr('title', '@lang('Off road vehicle')');
                } else {
                    routeLabel.parent().removeClass('btn-danger').attr('title', '@lang('In route')');
                }
            } else {
                routeLabel.parent().hide();
            }

            this.showInfo.find('.time').text(reportLocation.time);
            this.showInfo.find('.period').text(reportLocation.period);
            this.showInfo.find('.average-period').text(reportLocation.averagePeriod);
            this.showInfo.find('.speed').text(reportLocation.speed);
            this.showInfo.find('.current-mileage').text(reportLocation.currentMileage);
            if (reportLocation.speeding) {
                this.showInfo.find('.speed').parent().addClass('btn-warning');
            } else {
                this.showInfo.find('.speed').parent().removeClass('btn-warning');
            }
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