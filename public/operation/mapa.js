var idEmp = 0;
var idRut = 0;
var centerMap = false;
//var map;
//var mgr;
var minZ = 2;
var maxZ = 18;
var marks = {};
var bandera = true;
var isArray = false;
//var routeLayerKML;
//var trafficLayer;
var infoBoxs = {};
var infoBoxsTop = {};
var puntos_control = [];
var info_windows = {};
var myOptionsBox;
var myOptionsBoxTop;
var boxText;

var pfx = ["webkit", "moz", "ms", "o", ""];

var numPeticionesMapa = 0;
var alarma = new Audio('http://www.pcwserviciosgps.com/pcw_mov/php/alarma.wav');
var parkedSound = new Audio('http://www.pcwserviciosgps.com/pcw_mov/php/parked.wav');
var speedingSound = new Audio('http://www.pcwserviciosgps.com/pcw_mov/php/speeding.wav');
var controlPointAlert = new Audio('http://www.pcwserviciosgps.com/pcw_mov/php/alert_control_point.wav');
var muteAudio = true;

/* Var for notifications */
var alertOpenPanic = {};
var alertOpenCall = {};
var alertOpenSpeeding = {};
var alertOpenOffRoad = {};
var alertOpenParked = {};
var alertOpenControlPoint = {};

var companySVGIcons = {};

var statusReport = {};
var filterVehicles = 'all';
var infoStatusVehicle = {};

var orientationVehicle = {};

var vehiclesShowFrameCounter = {};

var defaultRoutes = [];

let panelVehicles = null;
let vehicleReport = [];

function refreshFrameLog() {
    if( infoSession.userIsAdmin ) {
        var divLog = $('.frame-log');
        divLog.empty();
        $(vehiclesShowFrameCounter).each(function (i, vehicleFrame) {
            $.each(vehicleFrame, function (vehicle, frame) {
                if (frame) divLog.append("<p class='text-log'><i class='fa fa-car'></i> " + vehicle + " " + frame + "</p><button data-vehicle='" + vehicle + "' class='btn btn-xs btn-danger btn-hide-frame-counter'><i class='fa fa-times'></i></button>");
            });
        });
    }
}

function setMuteAudio(status){
    muteAudio = status;
    localStorage.setItem('muteAudio', status);

    $('.btn-play-audio, .btn-stop-audio').addClass('hide');
    if( !muteAudio )$('.btn-play-audio').removeClass('hide');
    else $('.btn-stop-audio').removeClass('hide');
}

var resizeTimer;
var lastOffsetResize = 90;
function autoSizeMap(offset){
    var heightHedaPanelMap = $(".head-panel-map").innerHeight();
    if (offset < 10 || infoSession.userIsProprietary) heightHedaPanelMap = 0;

    const windowHeight = (window.innerHeight - offset - heightHedaPanelMap);
    lastOffsetResize = offset;
    $('#google-map-light-dream').css('height', windowHeight+'px');
}

$(document).ready(function () {
    $(window).on('resize', function(e) {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            autoSizeMap(lastOffsetResize);
        }, 250);
    });

    $.extend($.gritter.options, {
        position: 'bottom-left',
    });

    autoSizeMap(lastOffsetResize);

    $("title").html("MAPA: TODOS");

    muteAudio = localStorage.getItem('muteAudio') === "true";
    setTimeout(function(){
        setMuteAudio(muteAudio);
    },1000);

    updateSVGIcons();
    getInfoStatusVehicle();

    $('.map-action-bar button, .tooltips').tooltip();

    $('body').on('click', '.btn-show-frame-counter', function () {
        var btn = $(this);
        var vehicle = btn.data('vehicle');
        var company = $("#empresa").val();
        vehiclesShowFrameCounter[vehicle] = btn.data('frame');

        localStorage.setItem("vehiclesShowFrameCounter" + company, JSON.stringify(vehiclesShowFrameCounter));
        refreshFrameLog();
    }).on('click', '.btn-hide-frame-counter', function () {
        var btn = $(this);
        var vehicle = btn.data('vehicle');
        var company = $("#empresa").val();
        vehiclesShowFrameCounter[vehicle] = false;

        localStorage.setItem("vehiclesShowFrameCounter" + company, JSON.stringify(vehiclesShowFrameCounter));
        refreshFrameLog();
    });

    if( infoSession.userIsAdmin ){
        setInterval(function () {
            refreshFrameLog()
        }, 10000);
        refreshFrameLog();
    }

    //$('.btn-filter-vehicles').hide();
    $('.btn-filter-vehicles').click(function () {
        if ($(this).hasClass('active-filter')) {
            $(this).removeClass('active-filter');
            filterVehicles = 'all';
        } else {
            $('.btn-filter-vehicles').removeClass('active-filter');
            filterVehicles = $(this).data('filter-status-id') + "";

            hideAll();
            marks = {};
            //mgr.clearMarkers();

            $(this).addClass('active-filter');
            alert_type("<i class='" + infoStatusVehicle[filterVehicles].icon_class + " fa-2x'></i> Filtrando vehículos en estado <b>" + infoStatusVehicle[filterVehicles].des_status + "</b> <i class='fa fa-spinner fa-pulse'></i>", infoStatusVehicle[filterVehicles].main_class);
        }

        panelVehicles.filterStatus = filterVehicles;

        paintLocates();
    });

    $("#ruta").change(function (event) {
        idRut = $("#ruta").find(':selected').val();
        $(".tracking-route").html("Todas");
        $("title").html("MAPA: TODOS");

        var info = idRut.split('$');
        idRut = info[0];

        hideAll();
        clearInfoBoxs();
        infoBoxs = {};
        clearInfoBoxsTop();
        infoBoxsTop = {};
        clearControlPoints();
        puntos_control = [];
        marks = {};
        //mgr.clearMarkers();
        bandera = true;

        defaultRoutes.forEach(function(kml,index){
            kml.setMap(null);
        });
        defaultRoutes = [];

        var id = info[1];
        if (id) {
            const routeName = $("#ruta").find(':selected').text();
            $("title").html("MAPA: "+routeName);
            $(".tracking-route").html(routeName);

            routeLayerKML.setMap(null);
            routeLayerKML = new google.maps.KmlLayer({
                url: id
            });
            routeLayerKML.setMap(map);

            var id2 = id.replace("&", "%");
            id2 = id2.replace("&", "%");
            id2 = id2.replace("&", "%");
            id2 = id2.replace("&", "%");
            id2 = id2.replace("&", "%");
            id2 = id2.replace("&", "%");
            id2 = id2.replace("&", "%");

            $("#puntos_control").load('http://www.pcwserviciosgps.com/pcw_mov/php/genera_select.php?id=' + id2 + '&opc=22', function () {
                if (document.control.puntos.checked) {
                    isArray = true;
                }
                setTimeout(function () {
                    paintLocates();
                }, 300);
            });
        } else {
            routeLayerKML.setMap(null);
            if( idEmp == 21 ){
                $(this).find('option').each(function(i,e){
                    var dataRoute = $(e).attr('value').split('$');

                    if(dataRoute[0] != 0){
                        defaultRoutes.push(new google.maps.KmlLayer({
                            url: dataRoute[1]
                        }));
                    }
                });
            }
        }

        $('.btn-filter-vehicles').each(function (i, e) {
            var span = $(this).find('span');
            span.html('');
        });

        setTimeout(function(){
            $('.btn-show-historic').removeAttr('disabled');
            //$('#opciones').collapse('hide');
        },2000);
    });

    $("#pControl").change(function (event) {
        var id = $("#ruta").find(':selected').val();
        if (id == 0 & document.control.puntos.checked) {
            alert("Por favor pinte una ruta");
            $("#pControl").prop('checked', false);
        } else if (id != 0 & document.control.puntos.checked) {
            isArray = true;
        } else if (!document.control.puntos.checked) {
            clearControlPoints();
            puntos_control = {};
            clearInfoBoxsTop();
            infoBoxsTop = {};
        }
    });

    $('#monitorear').bootstrapSwitch({
        onText: 'Monitoreando',
        offText: 'Apagado',
        labelText: '<i style="display: none;" class="fa fa-dot-circle-o faa-burst mon-map animated"></i><i class="fa fa-ban bigger-130 red mon-map" aria-hidden="true"></i>',
        offColor: 'warning',
        onColor: 'primary',
        size: 'normal',
        state: false
    });

    $('#monitorear').on('switchChange.bootstrapSwitch', function (event, state) {
        $('.mon-map').toggle();
        if (state && ($('#empresa').val() == '111111' || $('#empresa').val() == 111111)) {
            $.gritter.add({
                title: 'Alerta',
                text: 'Seleccione una empresa',
                sticky: false,
                time: '3000',
                class_name: 'gritter-error'
            });
            $('#monitorear').bootstrapSwitch('state', false);
            //$('.btn-filter-vehicles').slideUp();
        } else if (state) {
            setTimeout(function () {
                paintLocates();
                $('.btn-filter-vehicles').slideDown();
                //$('#opciones').collapse('hide');
            }, 300);
        }
        setTimeout(function () {
            hideAll();
            clearInfoBoxs();
            infoBoxs = {};
            clearInfoBoxsTop();
            infoBoxsTop = {};
            routeLayerKML.setMap(null);

            marks = {};
            //mgr.clearMarkers();
            bandera = true;
            clearControlPoints();
            puntos_control = [];
            $("#ruta").change();
        }, 500);
        numPeticionesMapa = 0;

        $('.btn-filter-vehicles').each(function (i, e) {
            var span = $(this).find('span');
            span.html('');
        });
    });

    $('#number').on('keyup', function (event) {
        var key = event.keyCode || event.which;
        if (key === 13) {
            $('.btn-search-vehiculo').click();
        }
    });

    google.maps.event.addDomListener(window, 'load', load);

    $('#number').attr('type',(companySession== 28)?'text':'number');

    setTimeout(()=>{
        $('#monitorear').bootstrapSwitch('state', true);
    },2000);
});

function getInfoStatusVehicle() {
    $.ajax({
        url: "http://www.pcwserviciosgps.com/pcw_mov/php/mapa/controller.php",
        data: {
            action: 'getInfoStatusVehicle'
        },
        dataType: 'json',
        success: function (data) {
            infoStatusVehicle = data;
        }
    });
}

function updateSVGIcons() {
    $.ajax({
        url: "http://www.pcwserviciosgps.com/pcw_mov/php/mapa/controller.php",
        crossDomain: true,
        data: {
            action: 'getSVGIcons'
        },
        dataType: 'json',
        success: function (data) {
            companySVGIcons = data;
        }
    });
}

function updateTooltip() {
    $('[data-toggle="tooltip"]').tooltip();
    setTimeout(function () {
        $('[data-toggle="tooltip"]').tooltip();
    }, 1000);
}

function reFresh() {
    //mgr.clearMarkers();
    //mgr.addMarkers(Object.values(marks), minZ, maxZ);
    //mgr.refresh();
}

//fullscreen
function goFullscreen(id) {
    // Get the element that we want to take into fullscreen mode
    var element = document.getElementById(id);

    if (RunPrefixMethod(document, "FullScreen") || RunPrefixMethod(document, "IsFullScreen")) {
        RunPrefixMethod(document, "CancelFullScreen");
    }
    else {
        RunPrefixMethod(element, "RequestFullScreen");
    }
}

function RunPrefixMethod(obj, method) {

    var p = 0, m, t;
    while (p < pfx.length && !obj[m]) {
        m = method;
        if (pfx[p] == "") {
            m = m.substr(0, 1).toLowerCase() + m.substr(1);
        }
        m = pfx[p] + m;
        t = typeof obj[m];
        if (t != "undefined") {
            pfx = [pfx[p]];
            return (t == "function" ? obj[m]() : obj[m]);
        }
        p++;
    }
}

function getSeatingStatus(sender) {
    $(sender).each(function (i, e) {
        var vehicle = $(e);
        var detail = vehicle.parents('.seating-container').find('.detail');
        $.ajax({
            url: "http://www.pcwserviciosgps.com/pcw_mov/php/mapa/controller.php",
            crossDomain: true,
            data: {
                action: 'getSeatingStatus',
                plate: vehicle.data('plate')
            },
            success: function (data) {
                detail.html(data).show();
            }
        });
    });
}

function paintLocates() {
    if (numPeticionesMapa < 1) {
        numPeticionesMapa++;
        $('#numPeticionesMapa').val(numPeticionesMapa);
        const empresaSelect = $('#empresa').val();
        const idRuta = ($("#ruta").val().split('$'))[0];
        const routeAsGroup = $("#ruta option:selected").data('as-group');

        $.ajax({
            url: "http://www.pcwserviciosgps.com/pcw_mov/php/phpsqlajax_genxmlp2.php",
            crossDomain: true,
            data: {
                empresaShow: empresaSelect,
                idRuta: idRuta
            },
            async: true,
            success: function (data) {
                if ($('#monitorear').bootstrapSwitch('state')) {
                    if (centerMap == true) {
                        centerMapSelectedCompany();
                        centerMap = false;
                        $("#coords").attr('xy', '');
                    }
                    if (isArray == true) {
                        getArrayPuntos();
                    }

                    let newvehicles = false;
                    statusReport = {};
                    vehicleReport = [];
                    $(JSON.parse(data)).each(function (i, d) {
                        const vehiclePlate = d.vehiclePlate;
                        const numero = d.vehicleNumber.toString();
                        const excesoVelocidad = d.speeding >= 1;
                        const obsVehicle = d.observations;
                        const pasajeros = d.passengers;
                        const pasajerosSensorRegistradora = d.passengersBySensorRecorder;
                        const asientosTemplate = d.seatingTemplate;
                        const horaContador = d.timeCounter.split('.')[0];
                        const timeChangeSensorRecorder = d.timeChangeSensorRecorder.split('.')[0];
                        const kilo = d.mileage;
                        const vuelta = d.dispatchRegisterRoundTrip;
                        const rutar = d.dispatchRegisterRouteName;
                        const ruta = d.dispatchRegisterRouteId;
                        const idEstado = parseInt(d.vehicleStatusId);
                        const horaEstado = d.vehicleTimeStatus.split('.')[0];
                        const empresa = d.companyId;
                        const des_estado = d.vehicleStatusName;
                        const fecha = d.date;
                        const hora = d.time.split('.')[0];
                        const vel = d.speed;
                        let ori = d.orientation;
                        const alertOffRoad = d.alertOffRoad;
                        const alertParked = d.alertParked;
                        const alertControlPoint = d.alertControlPoint;
                        const vehicleWithPeakAndPlate = d.vehicleWithPeakAndPlate;
                        const statusMainClass = d.vehicleStatusMainClass;
                        const statusIconClass = d.vehicleStatusIconClass;
                        const reportStatusVehicle = d.reportStatusVehicle;
                        const reportStatusTimeDifference = d.reportStatusTimeDifference;

                        /* Solo para ALAMEDA mostrar Registradora en el mapa */
                        const conteo_registradora = (idEmp == 14) ?
                            (
                                "Pasajeros registradora: <b>" + d.passengersByRecorder + "</b>" +
                                (d.timePassengersByRecorder ? ("  <i class='fa fa-clock-o grey'></i> " + d.timePassengersByRecorder) : "") + "<br>" +
                                "Sensor Registradora: <b>" + pasajerosSensorRegistradora + "</b> <i class='fa fa-clock-o grey'></i> " + timeChangeSensorRecorder + "<br>"
                            )
                            : "";
                        /* Mostrar tramas de conteo en el mapa */
                        let frameCounter = '';
                        if (d.frame != '' && infoSession.userIsAdmin) {
                            const infoFrameCounter = String(d.timeFrameCounter + " » " + d.frame);
                            frameCounter = "<div class='btn btn-xs frame-counter btn-show-frame-counter' data-vehicle='" + numero + "' data-frame='" + infoFrameCounter + "' >Ver trama contador</div><br>";
                            const vehicleShowFrameCounter = vehiclesShowFrameCounter && vehiclesShowFrameCounter !== undefined && vehiclesShowFrameCounter !== "" ? vehiclesShowFrameCounter[numero] : null;
                            vehiclesShowFrameCounter[numero] = vehicleShowFrameCounter && vehicleShowFrameCounter !== undefined ? infoFrameCounter : false;
                        }
                        /* Solo para buses intermunicipales mostrar Pasajeros por km recorrido */
                        const pasajeros_por_km = (d.vehicleIsIntermunicipal == 't') ? ("Pasajeros por Km recorrido: <b>" + d.passengersByKm + "</b><br>") : '';

                        const conteo_pasajeros = "Pasajeros Sensor: <b>" + pasajeros + "</b>  <i class='fa fa-clock-o grey'></i> " + horaContador + "<br>";
                        const status = '<i class="' + statusIconClass + '"></i> <span class="text-bold text-' + statusMainClass + '">' + des_estado + '</span>  <i class="fa fa-clock-o grey"></i> ' + horaEstado + '<br>';
                        const vehicleWithPeakAndPlateView = vehicleWithPeakAndPlate ? '<span class="btn btn-xs btn-warning">PyP</span>' : '';

                        let routeTimeStatus = "";
                        let classRouteTimeStatus = "default";
                        let titleRouteTimeStatus = "";
                        if (!rutar.includes("SIN RUTA") && reportStatusVehicle) {
                            if (reportStatusTimeDifference.substr(1) < '00:01:00') {
                                titleRouteTimeStatus = 'A tiempo';
                                classRouteTimeStatus = 'success';
                            }
                            else if (reportStatusVehicle == 'fast') {
                                classRouteTimeStatus = "primary";
                                titleRouteTimeStatus = 'Adelantado';
                            }
                            else if (reportStatusVehicle == 'slow') {
                                classRouteTimeStatus = "danger";
                                titleRouteTimeStatus = 'Atrasado';
                            }
                            routeTimeStatus = "<span title='" + titleRouteTimeStatus + "' class='btn btn-xs btn-" + classRouteTimeStatus + "'>" + reportStatusTimeDifference + "</span>";
                        }

                        const html = "<div class='p-4' style='font-size: 9pt;z-index: 10000 !important;'>" +
                            "VEHICULO: <b>" + numero + "</b><br>" +
                            //"PLACA: <b>" + vehiclePlate + "</b>" + vehicleWithPeakAndPlateView + "<br>" +
                            "<b>" + rutar + "</b>&nbsp;" + routeTimeStatus + "<br>" +
                            "Vuelta: <b>" + vuelta + "</b> <br>" +
                            conteo_registradora +
                            ((numero == '356' || numero == '338' || (infoSession.userIsProprietary && infoSession.userBelongsToTaxcentral)) ? '' : conteo_pasajeros) +
                            pasajeros_por_km +
                            frameCounter +
                            "Recorrido: <b>" + (kilo / 1000).toFixed(2) + "</b> km<br>" +
                            status +
                            "<b><i class='fa fa-calendar grey'></i> " + fecha + " <i class='fa fa-clock-o grey'></i>  " + hora + "</b><br>" +
                            "<b><i class='fa fa-tachometer grey'></i> " + parseFloat(vel).toFixed(2) + " km/h" + "</b>" +
                            (asientosTemplate != "" ? ("<hr class='hr'>" + asientosTemplate) : "") +
                            "</div>";

                        if (orientationVehicle[numero] === undefined) {
                            orientationVehicle[numero] = ori;
                        } else {
                            orientationVehicle[numero] = (ori > 0 || idEstado === 1) ? ori : orientationVehicle[numero];
                        }

                        ori = orientationVehicle[numero];

                        //var companyIcon = (empresa == 12 || empresa == 14) ? empresa : 14;
                        const companySVGIcon = companySVGIcons[empresa] ? companySVGIcons[empresa] : companySVGIcons[14];
                        const iconMarker = {
                            path: companySVGIcon.svg,
                            fillOpacity: 1,
                            fillColor: companySVGIcon.fillColor,
                            scale: companySVGIcon.scale,
                            strokeWeight: 1,
                            strokeColor: companySVGIcon.strokeColor,
                            anchor: new google.maps.Point(companySVGIcon.anchor.x, companySVGIcon.anchor.y),
                            rotation: ori - (ori == 0 ? 0 : 90)
                        };

                        const markerOptions = {
                            position: new google.maps.LatLng(parseFloat(d.lat), parseFloat(d.lng)),
                            icon: iconMarker,
                            duration: 3000,
                            title: numero.toString(),
                            shadow: shadowIcon + ".png"
                        };

                        let marker;
                        if (idEmp == empresa && (idRut == ruta || idRut == 0 || (routeAsGroup && idRut == d.routeIdGroup))) {
                            if (statusReport[idEstado] === undefined) {
                                statusReport[idEstado] = [];
                            }

                            statusReport[idEstado].push(numero);
                            vehicleReport.push({
                                number: numero,
                                statusId: idEstado,
                                date: fecha,
                                time: hora,
                            });

                            newvehicles = !marks[numero];

                            if (newvehicles) {
                                if (filterVehicles == 'all' || filterVehicles == idEstado) {
                                    marker = new google.maps.Marker(markerOptions);

                                    if (infoSession.userIsProprietary || !infoSession.userBelongsToTaxcentral) {
                                        const infoWindow = new google.maps.InfoWindow({
                                            content: html
                                        });
                                        bindInfoWindow(marker, infoWindow, numero, ruta);
                                    }

                                    bindInfoBox(marker, fecha, hora, numero, statusMainClass, statusIconClass, idEstado, des_estado, obsVehicle, true, classRouteTimeStatus);
                                    marks[numero] = marker;
                                }
                            } else {
                                if (filterVehicles == 'all' || filterVehicles == idEstado) {
                                    marker = marks[numero];
                                    marker.setIcon(markerOptions.icon);
                                    marker.setTitle(markerOptions.title);
                                    marker.setPosition(markerOptions.position);

                                    if (infoSession.userIsProprietary || !infoSession.userBelongsToTaxcentral) {
                                        updateInfoWindow(html, numero);
                                    }

                                    bindInfoBox(marker, fecha, hora, numero, statusMainClass, statusIconClass, idEstado, des_estado, obsVehicle, false, classRouteTimeStatus);
                                    show(numero);
                                } else {
                                    hide(numero);
                                }
                            }


                            /*---- NOTIFY ALERTS ------*/

                            if(d.showAlerts){
                                let title = '<i class="fa fa-bell bigger-180 faa-ring animated sound-alarm"></i> ALERTA';

                                if (idEstado == '24' || idEstado == '4') {
                                    let estado_alerta = 'Petición de llamada';
                                    let class_alert = 'gritter-success';

                                    if (idEstado == '4') {
                                        title = '<i class="fa fa-exclamation-triangle bigger-180 faa-flash animated orange2 sound-alarm"></i> ALERTA';
                                        estado_alerta = 'Pánico';
                                        class_alert = 'gritter-info';
                                    }

                                    let alert_id = alertOpenCall[numero];
                                    if (idEstado == '4') alert_id = alertOpenPanic[numero];

                                    if (!alert_id) {
                                        const alertVehiculo = $('<div></div>').html('<div class="col-md-12 col-sm-12 col-xs-12">' +
                                            '<p class="hide type-alert">' + idEstado + '</p>' +
                                            '<h2 style="font-size: 120%">El vehículo <b class="number-vehicle">' + numero + '</b> se encuentra en estado de <b>' + estado_alerta + '</b></h2><hr class="col-md-12 co-xs-12 col-sm-12 no-padding">' +
                                            '<button class="btn btn-sm btn-warning" onClick="verVehiculoFueraMapa(' + numero + ')">Mostrar en Mapa&nbsp;<i class="fa fa-search faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '<button class="btn btn-sm btn-success" onClick="backToNormalState(\'' + vehiclePlate.trim() + '\',' + numero + ',' + ((idEstado == '4') ? true : false) + ')" style="margin-left:10px">Atender&nbsp;<i class="fa fa-check-square-o faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '</div>');

                                        alarma.loop = true;
                                        if(!muteAudio)alarma.play();


                                        const alert_gritter_id = $.gritter.add({
                                            title: title,
                                            text: alertVehiculo.html(),
                                            sticky: true,
                                            time: '20000',
                                            class_name: class_alert,
                                            after_close: function (e, manualClose) {
                                                const numVehicle = parseInt($(e).find('.number-vehicle').text());
                                                const typeAlert = parseInt($(e).find('.type-alert').text());

                                                if (typeAlert == 4) alertOpenPanic[numVehicle] = null;
                                                else alertOpenCall[numVehicle] = null;
                                                alarma.loop = false;
                                            },
                                            position:'bottom-left'
                                        });

                                        if (idEstado == '4') alertOpenPanic[numero] = alert_gritter_id;
                                        else alertOpenCall[numero] = alert_gritter_id;
                                    }
                                }

                                if (excesoVelocidad && idEstado == '0') {
                                    const alertSpedding_id = alertOpenSpeeding[numero];

                                    if (!alertSpedding_id) {
                                        const alertSpeeding = $('<div></div>').html(
                                            '<div class="col-md-2 col-sm-2 col-xs-2">' +
                                            '<i class="fa fa-tachometer fa-3x faa-tada animated" style="float: left;margin-top: 15px"></i>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-sm-10 col-xs-10">' +
                                            '<h2 style="font-size: 120%">El vehículo <b class="number-vehicle">' + numero + '</b> tiene exceso de velocidad: <b>'+parseFloat(vel).toFixed(0)+' Km/h</b></h2><hr style="width: 100%;margin: 5px">' +
                                            '<button class="btn btn-xs btn-warning" onClick="verVehiculoFueraMapa(' + numero + ')">Mostrar en Mapa&nbsp;<i class="fa fa-search faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '</div>');

                                        speedingSound.loop = false;
                                        if(!muteAudio)speedingSound.play();

                                        alertOpenSpeeding[numero] = $.gritter.add({
                                            title: title,
                                            text: alertSpeeding.html(),
                                            time: 30000,
                                            sticky: false,
                                            class_name: 'gritter-ligth gritter-warning',
                                            after_close: function (e, manualClose) {
                                                const numVehicle = parseInt($(e).find('.number-vehicle').text());
                                                alertOpenSpeeding[numVehicle] = null;
                                            },
                                            position:'bottom-left'
                                        });
                                    }
                                }

                                if (alertOffRoad === true && parseInt(idRut) === parseInt(ruta) && parseInt(idEstado) === 0) {
                                    const alertOffRoadId = alertOpenOffRoad[numero];

                                    if (!alertOffRoadId) {
                                        const alertOffRoadContent = $('<div></div>').html(
                                            '<div class="col-md-2 col-sm-2 col-xs-2">' +
                                            '<i class="fa fa-road fa-3x faa-falling animated" style="float: left;margin-top: 15px"></i>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-sm-10 col-xs-10">' +
                                            '<h2 style="font-size: 120%">El vehículo <b class="number-vehicle">' + numero + '</b> se encuentra fuera de la ruta asignada</h2><hr style="width: 100%;margin: 5px">' +
                                            '<button class="btn btn-xs btn-warning" onClick="verVehiculoFueraMapa(' + numero + ')">Mostrar en Mapa&nbsp;<i class="fa fa-search faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '</div>');

                                        alarma.loop = false;
                                        if(!muteAudio)alarma.play();

                                        alertOpenOffRoad[numero] = $.gritter.add({
                                            title: title,
                                            text: alertOffRoadContent.html(),
                                            time: 60000,
                                            sticky: false,
                                            class_name: 'gritter-error',
                                            after_close: function (e, manualClose) {
                                                const numVehicle = parseInt($(e).find('.number-vehicle').text());
                                                alertOpenOffRoad[numVehicle] = null;
                                            },
                                            position:'bottom-left'
                                        });
                                    }
                                }

                                if (alertParked === true && parseInt(idEstado) === 3) {
                                    const alertParkedId = alertOpenParked[numero];

                                    if (!alertParkedId) {
                                        const alertParkedContent = $('<div></div>').html(
                                            '<div class="col-md-2 col-sm-2 col-xs-2 faa-parent animated-hover">' +
                                            '<i class="fa fa-product-hunt fa-3x faa-burst" style="float: left;margin-top: 15px"></i>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-sm-10 col-xs-10">' +
                                            '<h2 style="font-size: 120%">El vehículo <b class="number-vehicle">' + numero + '</b> está parqueado por más de 10 minutos</h2><hr style="width: 100%;margin: 5px">' +
                                            '<button class="btn btn-xs btn-warning" onClick="verVehiculoFueraMapa(' + numero + ')">Mostrar en Mapa&nbsp;<i class="fa fa-search faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '</div>');

                                        parkedSound.loop = false;
                                        if(!muteAudio)parkedSound.play();

                                        alertOpenParked[numero] = $.gritter.add({
                                            title: title,
                                            text: alertParkedContent.html(),
                                            time: 60000,
                                            sticky: false,
                                            class_name: 'gritter-info',
                                            after_close: function (e, manualClose) {
                                                const numVehicle = parseInt($(e).find('.number-vehicle').text());
                                                alertOpenParked[numVehicle] = null;
                                            },
                                            position:'bottom-left'
                                        });
                                    }
                                }

                                if (alertControlPoint === true) {
                                    const alertControlPointId = alertOpenControlPoint[numero];

                                    if (!alertControlPointId) {
                                        var alertControlPointContent = $('<div></div>').html(
                                            '<div class="col-md-2 col-sm-2 col-xs-2 faa-parent animated-hover">' +
                                            '<i class="fa fa-map-marker fa-3x faa-burst" style="float: left;margin-top: 15px"></i>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-sm-10 col-xs-10">' +
                                            '<h2 style="font-size: 120%">El vehículo <b class="number-vehicle">' + numero + '</b> está pasando por el punto <b>'+d.controlPointAlertName+'</b> con trayecto de <b>'+d.controlPointAlertTrajectoryName+'</b></h2><br> <i class="fa fa-clock-o"></i> Reporte GPS: '+fecha+' '+hora+'<hr style="width: 100%;margin: 5px">' +
                                            '<button class="btn btn-xs btn-warning" onClick="verVehiculoFueraMapa(' + numero + ')">Mostrar en Mapa&nbsp;<i class="fa fa-search faa-flash animated bigger-130 sound-alarm" aria-hidden="true"></i></i></button>' +
                                            '</div>');

                                        controlPointAlert.loop = false;
                                        if(!muteAudio)controlPointAlert.play();

                                        alertOpenControlPoint[numero] = $.gritter.add({
                                            title: "<h3 class='text-center'>Alerta en Punto de Control<hr class='hr'></h3>",
                                            text: alertControlPointContent.html(),
                                            time: 60000,
                                            sticky: false,
                                            class_name: 'gritter-'+(d.controlPointAlertTrajectoryId == 0 ? 'success': 'warning'),
                                            after_close: function (e, manualClose) {
                                                const numVehicle = parseInt($(e).find('.number-vehicle').text());
                                                alertOpenControlPoint[numVehicle] = null;
                                            },
                                            position:'bottom-left'
                                        });
                                    }
                                }
                            }
                        }
                        else {
                            if (marks[numero]) hide(numero);
                        }
                    });

                    updateTooltip();
                    $('.auto-refresh-seating').click();
                    if (bandera == true || newvehicles) {
                        bandera = false;
                        reFresh();
                    }
                    setTimeout(function () {
                        getSeatingStatus($('.btn-search-seating-status'));
                    });

                    $('.btn-filter-vehicles').each(function (i, e) {
                        const span = $(this).find('span');
                        const idStatus = $(this).data('filter-status-id');
                        const vehiclesByStatus = statusReport[idStatus];
                        span.html(vehiclesByStatus !== undefined ? vehiclesByStatus.length : 0);
                    });

                    panelVehicles.vehicles = vehicleReport;
                }
            }, complete: function () {
                numPeticionesMapa--;
                $('#numPeticionesMapa').val(numPeticionesMapa);
                vehicleReport = [];
            }
        });
    }
}

function centerMapSelectedCompany() {
    var coor = $("#empresa option:selected").data('xy');
    var coorde = coor.split(", ");
    map.setCenter(new google.maps.LatLng(parseFloat(coorde[0]), parseFloat(coorde[1])));
    map.setZoom(13);
}

function bindInfoBox(marcador, date, time, number, statusMainClass, statusIconClass, idEstado, des_estado, obs, create, classRouteTimeStatus) {
    boxText = document.createElement("button");
    boxText.className = "btn btn-default btn-map-info btn-xs";
    boxText.style.className = "opacity:0.7;width:40px;font-size:40% !important;top:15px !important;z-index:-1 !important";

    if (parseInt(idEstado) === 31) {
        boxText.setAttribute('data-toggle', "tooltip");
        boxText.setAttribute('data-placement', "right");
        boxText.setAttribute('data-html', "true");
        boxText.setAttribute('title', (obs ? obs : "Sin observaciones"));
    }else{
        boxText.setAttribute('data-toggle', "tooltip");
        boxText.setAttribute('data-placement', "right");
        boxText.setAttribute('data-html', "true");
        boxText.setAttribute('title', number+" "+des_estado+" desde "+date+" "+time);
    }

    var styleNumber = '';
    if (classRouteTimeStatus !== 'default') {
        statusMainClass = 'white';
        classRouteTimeStatus += ' btn btn-xs';
        styleNumber = 'padding:1px !important;font-size:85%';
    }

    boxText.innerHTML = ('<i class="' + statusIconClass + '"></i> <span style="' + styleNumber + '" class="btn-' + classRouteTimeStatus + ' text-bold text-' + statusMainClass + '">' + number + '</span>');

    if (create) {
        var ib = new InfoBox(myOptionsBox);
        ib.setContent(boxText);
        ib.open(map, marcador);
        infoBoxs[number] = ib;
    } else {
        if (infoBoxs[number]) {
            var prevDiv = $(infoBoxs[number].getContent());
            if (prevDiv) prevDiv.unbind(); // Unbind prev click event
            infoBoxs[number].setContent(boxText);
        }
    }

    boxText.onclick = function () {
        if (marks[number]) google.maps.event.trigger(marks[number], 'click');
    }
}

function crearInfoBoxTop(marcador, number) {
    var num = number.split(" ");
    var longitud = 0;
    for (var index in num) {
        if (num[index].length > longitud) {
            longitud = num[index].length
        }
    }
    longitud = longitud * 8.58;
    var x = (longitud / 2) * -1;
    myOptionsBoxTop = {
        disableAutoPan: true,
        alignBottom: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(x, -33),
        zIndex: null,
        boxStyle: {
            opacity: 0.8,
            width: longitud + "60px"
        },
        infoBoxClearance: new google.maps.Size(0, 0),
        isHidden: false,
        pane: "floatPane",
        enableEventPropagation: true,
        closeBoxURL: ""
    };
    boxText = document.createElement("div");
    boxText.style.cssText = "border: 1px solid black; background: gray; font-size: 10px; font-weight: bold;";
    boxText.style.className = "hide";
    boxText.innerHTML = number;
    var ib = new InfoBox(myOptionsBoxTop);
    ib.setContent(boxText);
    //ib.open(map, marcador);
    infoBoxsTop[parseInt(number)] = ib;
}

function bindInfoWindow(marker, infoWindow, num, ruta) {
    info_windows[num] = infoWindow;
    marker.addListener('click', function () {
        infoWindow.open(map, marker);
    });
}

function updateInfoWindow(html, title) {
    if (info_windows[title].getContent() != html) {
        info_windows[title].setContent(html)
    }
}

function searchMarker() {
    const numero = document.getElementById("number").value;

    if (marks[numero]) {
        map.panTo(marks[numero].getPosition());
        google.maps.event.trigger(marks[numero], 'click');
        map.setZoom(16);
    }
    else {
        gerror("El vehículo " + numero + " no fué encontrado en el mapa <br> Verifique su Busqueda");
    }
}

function validar(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 13) $('.btn-search-vehiculo').click();
}

function hide(vehicle) {
    if (marks[vehicle]) marks[vehicle].setMap(null);
    if (infoBoxs[vehicle]) infoBoxs[vehicle].setMap(null);
    if (infoBoxsTop[vehicle]) infoBoxsTop[vehicle].close();
}

function hideAll() {
    for (var vehicle in marks) {
        marks[vehicle].setMap(null);
    }
    for (var vehicle in infoBoxs) {
        infoBoxs[vehicle].setMap(null);
    }
    for (var vehicle in infoBoxsTop) {
        infoBoxsTop[vehicle].close();
    }
}

function show(vehicle) {
    if (marks[vehicle] && !marks[vehicle].getMap()) marks[vehicle].setMap(map);

    if (infoBoxs[vehicle] && !infoBoxs[vehicle].getMap()) infoBoxs[vehicle].setMap(map);
    if (infoBoxsTop[vehicle] && !infoBoxsTop[vehicle].getMap()) infoBoxsTop[vehicle].setMap(null);
}

function clearInfoBoxs() {
    for (var i in infoBoxs) {
        infoBoxs[i].close();
    }
}

function clearInfoBoxsTop() {
    for (var i in infoBoxsTop) {
        infoBoxsTop[i].close();
        infoBoxsTop[i].setMap(null);
    }
}

function getArrayPuntos() {
    try {
        var marcador = [];
        for (var i = 0; i < document.control.puntos_control.options.length; i++) {
            marcador = document.control.puntos_control.options[i].text.split("&");
            var infoWindow = new google.maps.InfoWindow;
            var point = new google.maps.LatLng(parseFloat(marcador[0]), parseFloat(marcador[1]));
            var icono = "iconos/inicio.png";
            if (marcador[3] == "1") {
                icono = "iconos/final.png"
            }
            var marker = new google.maps.Marker({
                position: point,
                icon: icono,
                title: marcador[2].toString(),
                map: map
            });
            puntos_control.push(marker);
            setInfoW(marker, infoWindow, marcador[2]);
            crearInfoBoxTop(marker, marcador[2]);
        }
        isArray = false;
    } catch (e) {
    }
}

function setInfoW(marker, infoW, nombre) {
    google.maps.event.addListener(marker, 'click', function () {
        infoW.setContent(nombre);
        infoW.open(map, marker);
    });
}

function clearControlPoints() {
    try {
        for (var i in puntos_control) {
            puntos_control[i].setMap(null);
        }
    }
    catch (e) {
    }
}

function verVehiculoFueraMapa(vehiculo) {
    $('.sound-alarm').removeClass('animated');
    alarma.loop = false;
    $('#number').val(vehiculo);
    searchMarker();
}

function backToNormalState(placa, numero, panic) {
    alarma.loop = false;
    $.ajax({
        url: 'http://www.pcwserviciosgps.com/pcw_mov/php/changeStatusAlert.php',
        crossDomain: true,
        data: {
            placa: placa
        }
    });

    try {
        var alert_id = alertOpenCall[numero];
        if (panic) {
            alert_id = alertOpenPanic[numero];
        }

        $.gritter.remove(alert_id, {
            fade: true,
            speed: 'fast'
        });
    }
    catch (e) {
    }
}

function load() {
    var coor = coo.split(", ");

    if (companySession != 6) {
        idEmp = companySession;
    }

    myOptionsBox = {
        disableAutoPan: true,
        maxWidth: 0,
        pixelOffset: new google.maps.Size(0, 0),
        zIndex: 10,
        boxStyle: {
            opacity: 0.8,
            width: "30px",
            zIndex: -1
        },
        infoBoxClearance: new google.maps.Size(0, 0),
        isHidden: false,
        pane: "floatPane",
        enableEventPropagation: true,
        closeBoxURL: ""
    };

    /* map = new google.maps.Map(document.getElementById("map"), {
        center: new google.maps.LatLng(parseFloat(coor[0]), parseFloat(coor[1])),
        zoom: 13,
        minZoom: minZ,
        maxZoom: maxZ,
        mapTypeId: 'roadmap',
        fullscreenControl: false,
        mapTypeControl: !infoSession.userBelongsToTaxcentral,
        mapTypeControlOptions: {
            style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: google.maps.ControlPosition.BOTTOM_CENTER
        },
        zoomControlOptions: {
            position: google.maps.ControlPosition.RIGHT_CENTER
        },
    });*/

    if( infoSession.userRoleId != 2 || !infoSession.userBelongsToTaxcentral ){
        SlidingMarker.initializeGlobally();
    }

    map.addListener('zoom_changed', function (e) {
        /*for (var v in marks) {
            if (!marks[v].getMap()) {
                delete marks[v];
                if (infoBoxs[v]) {
                    infoBoxs[v].setMap(null);
                    delete infoBoxs[v];
                }
                if (infoBoxsTop[v]) {
                    infoBoxsTop[v].setMap(null);
                    delete infoBoxsTop[v];
                }
            }
        }*/
        reFresh();
        paintLocates();
    });


    routeLayerKML = new google.maps.KmlLayer();

    /*mgr = new MarkerManager(map, {
        trackMarkers: true
    });*/

    setInterval(function () {
        if ($('#monitorear').bootstrapSwitch('state')) {
            paintLocates();
        }
    }, 15000);
}

function showTraffic() {
    $('.btn-traffic').toggle();
    trafficLayer = new google.maps.TrafficLayer({
        zIndex: 1000,
        preserveViewport: true,
        map: map
    });
}

function hideTraffic() {
    $('.btn-traffic').toggle();
    trafficLayer.setMap(null);
}

function hideRouteLayer() {
    if ($("#ruta").val() != 0) {
        $('.btn-route-layer').toggle();
        routeLayerKML.setMap(null);
    } else {
        gerror("Seleccione una ruta");
    }
}

function showControlPoints(){
    var id = $("#ruta").find(':selected').val();
    if (id == 0) {
        alert('Selecciona una ruta');
    }else{
        $("#pControl").prop('checked', true).change();
        $('.btn-display-control-points').toggle();
    }
}

function hideControlPoints(){
    $("#pControl").prop('checked', false).change();
    $('.btn-display-control-points').toggle();
}

function showRouteLayer() {
    $('.btn-route-layer').toggle();
    if (routeLayerKML) routeLayerKML.setMap(map);
}

$(document).ready(function(){
    panelVehicles = new Vue({
        el: '#filter-vehicles',
        data: {
            filterStatus: "all",
            vehicles: [],
        },
        methods: {
            seeOnMap: function(vehicleNumber){
                verVehiculoFueraMapa(vehicleNumber);
            }
        },
        computed: {
            filterVehicles: function(){
                if( this.filterStatus === 'all' )return this.vehicles;

                const filtered = _.filter(this.vehicles,{
                    statusId: parseInt(this.filterStatus)
                });

                return _.sortBy(filtered, ['number']);
            }
        }
    });
});