<script type="application/javascript">
    'use strict';

    class ReportRouteHistoric {
        constructor(map) {
            this.report = null;
            this.map = map;

            this.currentLocation = null;
            this.historicLocations = [];
            this.controlPoints = [];
            this.markerBus = null;

            this.historicPath = null;
            this.kmlLayer = null;

            this.iconPathSVG = 'M511.2,256c0-8.6-5.2-16.3-13.1-19.7L30.5,40.2c-8.7-3.7-18.9-1.1-24.8,6.3c-6,7.4-6.4,17.8-1,25.6l127.4,184L4.7,440 c-5.4,7.8-5,18.2,1,25.6c0.5,0.6,1,1.1,1.5,1.6c6.1,6.1,15.3,8,23.4,4.6l467.6-196.1C506,272.3,511.2,264.6,511.2,256z';
            this.iconPanicSVG = 'M569.517 440.013C587.975 472.007 564.806 512 527.94 512H48.054c-36.937 0-59.999-40.055-41.577-71.987L246.423 23.985c18.467-32.009 64.72-31.951 83.154 0l239.94 416.028zM288 354c-25.405 0-46 20.595-46 46s20.595 46 46 46 46-20.595 46-46-20.595-46-46-46zm-43.673-165.346l7.418 136c.347 6.364 5.609 11.346 11.982 11.346h48.546c6.373 0 11.635-4.982 11.982-11.346l7.418-136c.375-6.874-5.098-12.654-11.982-12.654h-63.383c-6.884 0-12.356 5.78-11.981 12.654z';
            this.iconPowerOffSVG = 'M400 54.1c63 45 104 118.6 104 201.9 0 136.8-110.8 247.7-247.5 248C120 504.3 8.2 393 8 256.4 7.9 173.1 48.9 99.3 111.8 54.2c11.7-8.3 28-4.8 35 7.7L162.6 90c5.9 10.5 3.1 23.8-6.6 31-41.5 30.8-68 79.6-68 134.9-.1 92.3 74.5 168.1 168 168.1 91.6 0 168.6-74.2 168-169.1-.3-51.8-24.7-101.8-68.1-134-9.7-7.2-12.4-20.5-6.5-30.9l15.8-28.1c7-12.4 23.2-16.1 34.8-7.8zM296 264V24c0-13.3-10.7-24-24-24h-32c-13.3 0-24 10.7-24 24v240c0 13.3 10.7 24 24 24h32c13.3 0 24-10.7 24-24z';
            this.iconParkedOffSVG = 'M326.3 218.8c0 20.5-16.7 37.2-37.2 37.2h-70.3v-74.4h70.3c20.5 0 37.2 16.7 37.2 37.2zM504 256c0 137-111 248-248 248S8 393 8 256 119 8 256 8s248 111 248 248zm-128.1-37.2c0-47.9-38.9-86.8-86.8-86.8H169.2v248h49.6v-74.4h70.3c47.9 0 86.8-38.9 86.8-86.8z';
            this.iconWithOutGPSSVG = 'M216 288h-48c-8.84 0-16 7.16-16 16v192c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V304c0-8.84-7.16-16-16-16zM88 384H40c-8.84 0-16 7.16-16 16v96c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16v-96c0-8.84-7.16-16-16-16zm256-192h-48c-8.84 0-16 7.16-16 16v288c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V208c0-8.84-7.16-16-16-16zm128-96h-48c-8.84 0-16 7.16-16 16v384c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V112c0-8.84-7.16-16-16-16zM600 0h-48c-8.84 0-16 7.16-16 16v480c0 8.84 7.16 16 16 16h48c8.84 0 16-7.16 16-16V16c0-8.84-7.16-16-16-16z';
            this.iconPassenger = 'M96 0c35.346 0 64 28.654 64 64s-28.654 64-64 64-64-28.654-64-64S60.654 0 96 0m48 144h-11.36c-22.711 10.443-49.59 10.894-73.28 0H48c-26.51 0-48 21.49-48 48v136c0 13.255 10.745 24 24 24h16v136c0 13.255 10.745 24 24 24h64c13.255 0 24-10.745 24-24V352h16c13.255 0 24-10.745 24-24V192c0-26.51-21.49-48-48-48z';
            this.iconPassengerInOut = 'M96,128A64,64,0,1,0,32,64,64,64,0,0,0,96,128Zm0,176.08a44.11,44.11,0,0,1,13.64-32L181.77,204c1.65-1.55,3.77-2.31,5.61-3.57A63.91,63.91,0,0,0,128,160H64A64,64,0,0,0,0,224v96a32,32,0,0,0,32,32V480a32,32,0,0,0,32,32h64a32,32,0,0,0,32-32V383.61l-50.36-47.53A44.08,44.08,0,0,1,96,304.08ZM480,128a64,64,0,1,0-64-64A64,64,0,0,0,480,128Zm32,32H448a63.91,63.91,0,0,0-59.38,40.42c1.84,1.27,4,2,5.62,3.59l72.12,68.06a44.37,44.37,0,0,1,0,64L416,383.62V480a32,32,0,0,0,32,32h64a32,32,0,0,0,32-32V352a32,32,0,0,0,32-32V224A64,64,0,0,0,512,160ZM444.4,295.34l-72.12-68.06A12,12,0,0,0,352,236v36H224V236a12,12,0,0,0-20.28-8.73L131.6,295.34a12.4,12.4,0,0,0,0,17.47l72.12,68.07A12,12,0,0,0,224,372.14V336H352v36.14a12,12,0,0,0,20.28,8.74l72.12-68.07A12.4,12.4,0,0,0,444.4,295.34Z';

            this.controlPointIcon = [
                '{{ asset('img/control-point-0.png') }}',
                '{{ asset('img/control-point-1.png') }}'
            ];

            this.showInfo = $('.show-info');

            this.pauseOnEvent = null;
        }

        setPauseOnEvent(number) {
            this.pauseOnEvent = number;
        }

        processSVGIcon(reportLocation) {
            let rotation = parseInt(reportLocation.orientation);
            rotation = rotation > 0 ? rotation - 90 : (this.markerBus ? this.markerBus.getIcon().rotation : rotation);

            let scale = .02;
            let zIndex = 1000;
            let animation = null;
            let path = this.iconPathSVG;
            let fillColor = '#04bf8a';
            let strokeColor = '#00445f';
            let anchor = {
                x: 220,
                y: 250
            };

            if (reportLocation.offRoad) {
                fillColor = '#6a000e';
                strokeColor = '#ba0046';
            }

            const dr = reportLocation.dispatchRegister;

            if (reportLocation.vehicleStatus.id === 6 && !dr) {
                rotation = 0;
                path = this.iconPowerOffSVG;
                fillColor = '#bf1308';
                strokeColor = '#c2c2c2';
                scale = .035;
                zIndex = 100;
                anchor.x = 250;
                anchor.y = 280;
            } else if (reportLocation.vehicleStatus.id === 3) {
                rotation = 0;
                path = this.iconParkedOffSVG;
                fillColor = '#1300ce';
                strokeColor = 'rgb(181,181,181)';
                scale = .038;
                zIndex = 100;
                anchor.x = 250;
                anchor.y = 280;
            } else if (reportLocation.vehicleStatus.id === 4 && this.report && this.report.config.events.panic) {
                rotation = 0;
                path = this.iconPanicSVG;
                fillColor = '#ff0000';
                strokeColor = 'rgb(106,0,0)';
                scale = .030;
                zIndex = 100;
                anchor.x = 250;
                anchor.y = 280;
            } else if (reportLocation.vehicleStatus.id === 5) {
                rotation = 0;
                path = this.iconWithOutGPSSVG;
                fillColor = '#fffd06';
                strokeColor = '#d4760a';
                scale = .03;
                zIndex = 100;
                anchor.x = 250;
                anchor.y = 280;
            } else if (reportLocation.speeding) {
                fillColor = '#ffe415';
                strokeColor = '#d44200';
                zIndex = 100;
            }

            if (reportLocation.passengers.counted) {
                rotation = 0;
                path = this.iconPassenger;
                scale = .04;

                fillColor = '#84ff00';
                strokeColor = '#081a00';

                if (reportLocation.passengers.countedAscents && reportLocation.passengers.countedDescents) {
                    // path = this.iconPassengerInOut;
                    fillColor = '#ffdd00';
                    strokeColor = '#470020';
                } else {
                    if (reportLocation.passengers.countedAscents) {
                        fillColor = '#11bdfa';
                        strokeColor = '#000b47';
                    } else if (reportLocation.passengers.countedDescents) {
                        fillColor = '#ff6900';
                        strokeColor = '#530000';
                    }
                }

                zIndex = 1000;
                animation = google.maps.Animation.BOUNCE;
            }

            return {
                path,
                rotation,
                fillColor,
                strokeColor,
                animation,
                scale,
                zIndex,
                anchor
            };
        }

        processHistoricReportData(report) {
            this.report = report;
            // fitHeight('#google-map-light-dream');

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
                    $("html, body").animate({scrollTop: $(".range-reports").offset().top}, 1000);
                }, 1500);
            } else {
                gwarning("@lang("No registers found")");
            }
        }

        addKml(url) {
            this.kmlLayer = new google.maps.KmlLayer({
                url,
                map: this.map
            });
        }

        removeKml() {
            if (this.kmlLayer) {
                this.kmlLayer.setMap(null);
                this.kmlLayer = null;
            }
        }

        addControlPoints(list) {
            this.removeControlPoints();

            for(let controlPoint of list) {
                this.controlPoints.push(new google.maps.Marker({
                    map: this.map,
                    position: {lat: parseFloat(controlPoint.latitude), lng: parseFloat(controlPoint.longitude)},
                    icon: this.controlPointIcon[controlPoint.trajectory],
                    title: controlPoint.name,
                    zIndex: 10000,
                }));
            }
        }

        removeControlPoints() {
            for(let controlPoint of this.controlPoints) {
                controlPoint.setMap(null);
            }

            this.controlPoints = [];
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
                animation: svg.animation,
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

            this.removeKml();

            if (this.historicPath) {
                this.historicPath.setMap(null);
                this.historicPath = null;
            }
        }

        async paintHistoricPathTo(index) {
            let path = this.historicPath.getPath();

            this.historicLocations.forEach(async (historicLocation, i) => {
                path.removeAt(i);
                if (i <= index) {
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

        showPhotos(index) {
            const historicLocation = this.historicLocations[index];
            if (!historicLocation) return false;
            const reportLocation = historicLocation.reportLocation;
            const photos = reportLocation.photos;
            const photoCountedSeatingStr = reportLocation.photo.events.countedStr;

            if (photos.length) {
                let photosContainer = $('.photos-image-container');
                photosContainer.empty();

                $('#photo-show').hide();
                $('#photos-container').show();
                $('#photo-loading').show();

                let photoWidth = 100 / photos.length;
                if (photoWidth > 30) photoWidth = 30;
                for (let photo of photos) {
                    const url = `https://beta.pcwserviciosgps.com/api/v2/files/rocket/get-photo?id=${photo.id}&with-effect=true&encode=png&title=true&counted=${photoCountedSeatingStr}&mask=`;
                    photosContainer.append(`<img src="${url}" class="photo photo-image" draggable="false" onclick="toggleImgSize(this)"  alt="" width="${photoWidth}%">`);
                }
            }
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
                rotation: svg.rotation,
                zIndex: 10000,
            };

            if (this.markerBus) {
                this.markerBus.setPosition(marker.getPosition());
                this.markerBus.setIcon(icon);
                this.markerBus.setAnimation(svg.animation);
                this.markerBus.setZIndex(10000);
            } else {

                this.markerBus = new google.maps.Marker({
                    map: this.map,
                    position: marker.getPosition(),
                    icon: icon,
                    duration: 200,
                    animation: svg.animation,
                    easing: "swing",
                    title: marker.getTitle(),
                    shadow: "",
                    zIndex: 10000,
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

                if (reportLocation.vehicle.id != 1199) routeLabel.text(dr.route).parent().fadeIn();

                this.showInfo.find('.mileage-route').text(reportLocation.routeDistance);
                if (reportLocation.offRoad) {
                    routeLabel.parent().addClass('btn-danger').attr('title', '@lang('Off road vehicle')');
                } else {
                    routeLabel.parent().removeClass('btn-danger').attr('title', '@lang('In route')');
                }

                $('.passengers-within-round-trip').removeClass('hide');
                this.showInfo.find('.passengers-route-name').text(dr.route);


            } else {
                $('.passengers-within-round-trip').addClass('hide');
                routeLabel.parent().hide();
            }

            this.showInfo.find('.photo-alerts').empty();
            if (reportLocation.photo.events.alerts.length) {
                for (let alert of reportLocation.photo.events.alerts) {
                    this.showInfo.find('.photo-alerts').append(`<small class="m-0 p-l-5 p-r-5 bg-white text-${alert.color}">${alert.message}</small>`);
                }
            }

            this.processTrips(reportLocation, index);
            this.processTariffCharges(reportLocation);

            this.showInfo.find('.time').text(reportLocation.time);
            this.showInfo.find('.period').text(reportLocation.period);
            this.showInfo.find('.average-period').text(reportLocation.averagePeriod);
            this.showInfo.find('.speed').text(reportLocation.speed);

            $('.photo-id').text(reportLocation.photo.id);

            this.showInfo.find('.current-mileage').text(reportLocation.currentMileage);
            this.showInfo.find('.passengers-total').text(reportLocation.passengers.total);
            this.showInfo.find('.passengers-frame').text(reportLocation.passengers.frame);
            this.showInfo.find('.passengers-route-in').text(reportLocation.passengers.inRoundTrip);
            this.showInfo.find('.passengers-route-out').text(reportLocation.passengers.outRoundTrip);
            this.showInfo.find('.passengers-total-ascents').text(reportLocation.passengers.totalAscents);
            this.showInfo.find('.passengers-total-descents').text(reportLocation.passengers.totalDescents);

            this.showInfo.find('.photo-passengers-total').text(reportLocation.photo.passengers);
            this.showInfo.find('.photo-passengers-trip').text(reportLocation.photo.passengersTrip);
            this.showInfo.find('.photo-time').text(reportLocation.photo.time);

            this.showInfo.find('.passengers-route-ascents').text(reportLocation.passengers.ascentsInRoundTrip);
            this.showInfo.find('.passengers-route-descents').text(reportLocation.passengers.descentsInRoundTrip);

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

            if (parseInt(reportLocation.photo.id) > 0) {
                $('#photo-show').show();
            } else {
                $('#photo-show').hide();
            }

            $('#photo-loading').hide();
            $('#photos-container').hide();
            $('.photo-image').hide();
            // $('.photo-info').hide();
        }

        getEvents(index) {
            let event = 0;

            const historicLocation = this.historicLocations[index];
            if (historicLocation) {
                const reportLocation = historicLocation.reportLocation;
                return reportLocation.photo.events.types;
            }

            return event;
        }

        processTariffCharges(reportLocation) {
            const passengers = reportLocation.passengers;
            const tariffCharges = passengers.tariffCharges;

            const sorted = _.sortBy(tariffCharges, 'tariff');

            let html = "<ul>";
            for (const tariff in sorted) {
                const charge = sorted[tariff];
                html += `<div>
                        <small>
                            <span><i class="fa fa-dollar"></i> ${charge.tariff} • ${charge.totalCounted} • ${charge.totalCharge}</span>
                        </small>
                    </div>`;
            }

            html += "</ul>";

            this.showInfo.find('.passengers-tariff-charges').empty().html(html);
            this.showInfo.find('.passengers-route-tariff').text(reportLocation.passengers.tariff);
            this.showInfo.find('.passengers-total-charge').text(reportLocation.passengers.totalCharge);
        }

        processTrips(reportLocation, index) {
            const dr = reportLocation.dispatchRegister;
            const passengers = reportLocation.passengers;
            const trips = passengers.trips;


            const sorted = _.sortBy(trips, 'departureTime');
            let iterations = sorted.length;

            if (iterations && dr) {
                $('.passengers-route-ascents').parent().show();
                $('.passengers-route-descents').parent().show();
                $('.passengers-route-tariff').parent().show();
            } else {
                $('.passengers-route-ascents').parent().hide();
                $('.passengers-route-descents').parent().hide();
                $('.passengers-route-tariff').parent().hide();
            }

            let html = "<ol class='m-0'>";

            for (const drId in sorted) {
                const trip = sorted[drId];

                const classLast = (!--iterations && dr) ? 'active' : '';

                if (trip.index <= index) {
                    html += `<li class="${classLast}">
                        <small>
                            <span><i class="fa fa-exchange"></i> ${trip.roundTrip} ${trip.routeName} • ${ this.report.config.show.passengers ? trip.passengers.inRoundTrip : ''}</span>
                        </small>
                    </li>`;
                }
            }

            html += "</ol>";

            $('.info-trips').empty().html(html);

            $('.info-trips-total').empty().html(passengers.totalInRoundTrips);
        }

        createInfoWindow(r) {
            let infoDispatchRegister = '';
            let height = '200px';
            let dr = r.dispatchRegister;
            if (dr) {
                infoDispatchRegister = "" +
                    "<small class='text-bold'><i class='fa fa-flag text-muted'></i> @lang('Route'): " + dr.route + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-retweet text-muted'></i> @lang('Round Trip'): " + dr.trip + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-list-ol text-muted'></i> @lang('Turn'): " + dr.turn + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-clock-o text-muted'></i> @lang('Dispatched'): " + dr.departure + "</small><br>" +
                    "<small class='text-bold'><i class='fa fa-user text-muted'></i> @lang('Driver'): " + dr.driver + "</small><br>" +
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
