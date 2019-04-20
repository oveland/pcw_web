@extends('layouts.blank')

<script src="http://www.pcwserviciosgps.com/pcw_mov/php/calendar.js" type=text/javascript></script>
<script src="http://www.pcwserviciosgps.com/pcw_mov/php/calendar-es.js" type=text/javascript></script>
<script src="http://www.pcwserviciosgps.com/pcw_mov/php/calendar-setup.js" type=text/javascript></script>



{{--<script src="http://www.pcwserviciosgps.com/pcw_mov/php/mapa/plugins/jquery.easing.1.3.js"></script>
<script src="http://www.pcwserviciosgps.com/pcw_mov/php/mapa/plugins/markerAnimate.js"></script>
<script src="http://www.pcwserviciosgps.com/pcw_mov/php/mapa/plugins/SlidingMarker.min.js"></script>--}}

<!-- ****************************************************** -->
<script src="https://unpkg.com/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.11/lodash.min.js"></script>
<!-- ****************************************************** -->

@section('stylesheets')
    <link rel="stylesheet" href="http://www.pcwserviciosgps.com/pcw_mov/php/mapa/mapa.css">
    <link rel="stylesheet" href="http://www.pcwserviciosgps.com/pcw_mov/php/calendar-green.css" type="text/css"/>
    <link href="http://www.pcwserviciosgps.com/pcw_mov/php/bootstrap/pluginsBootstrap/Switch/dist/css/bootstrap3/bootstrap-switch.css" rel="stylesheet">
@endsection

@section('content')
    <div class="col-md-12 col-sm-12 col-xs-12 no-padding head-panel-map">
        <div class="panel panel-default" style="margin-bottom: 0;">
            <div class="panel-heading" style="padding-top: 5px;padding-bottom: 5px">
                <div class="panel-title row">
                    <div class="col-md-4 col-sm-12 col-xs-12 text-center">
                        <a data-toggle="collapse" onclick="$('.action-filters').toggle();" style=""
                           class="no-padding col-md-2 col-sm-2 col-xs-2" style="margin-top" data-parent="#accordion"
                           href="#opciones" aria-expanded="true" aria-controls="collapseOne">
                            <i class="fa fa-angle-double-down fa-2x action-filters pull-left"
                               aria-hidden="true"></i>
                            <i class="fa fa-angle-double-up fa-2x action-filters pull-left" style="display: none;"
                               aria-hidden="true"></i>
                        </a>
                        <div class="col-md-4 col-sm-8 col-xs-8 no-padding">
                            <input type="checkbox" id='monitorear' name="monitorear" value="true" checked>
                            <input type="number" id="numPeticionesMapa" value="" class="form-control hide">
                        </div>
                    </div>

                    <div class="col-md-3 col-sm-6 col-xs-12">
                        <label for="ruta" class="col-md-2 col-sm-2 col-xs-12 hidden-sm hidden-xs" style="margin-top: 8px !important">
                            <span>Ruta</span>
                        </label>
                        <div class="col-md-9 col-sm-9 col-xs-12" style="margin-top: 3px !important">
                            <select name='ruta' id='ruta' class='col-md-12 col-xs-12 col-sm-12'>
                                @foreach($routes as $route)
                                    <option data-as-group="{{ $route->as_group }}" value="{{ $route->id }}${{ $route->url }}">
                                        {{ $route->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <form name="control" class="col-md-1 col-sm-1 col-xs-1 no-padding" onsubmit="return false" style="display: none">
                            <select name='puntos_control' id='puntos_control' class='col-md-12 col-xs-12 col-sm-12' style="visibility:hidden;width:2px;height:0px;"></select>
                            <input type="checkbox" id="pControl" name="puntos" value="puntos" style="margin: 0 !important;height: 10px">
                        </form>
                    </div>

                    <div class="col-md-5 col-sm-12 col-xs-12 no-padding text-right">
                        <div class="col-md-6 col-sm-12 no-padding pull-right" style="margin-bottom: 3px">
                            <div class="input-group">
                            <span class="input-group-btn map-action-bar text-center">
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="Estado OK" data-filter-status-id="0">
                                    <i class="fa fa-dot-circle-o faa-burst green" aria-hidden="true"></i>
                                    <span class="text-success status-count"></span>
                                </button>
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="Apagados" data-filter-status-id="6">
                                    <i class="fa fa-power-off red faa-float" aria-hidden="true"></i>
                                    <span class="text-danger status-count"></span>
                                </button>
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="Parqueados" data-filter-status-id="3">
                                    <i class="fa fa-product-hunt text-info faa-pulse" aria-hidden="true"></i>
                                    <span class="text-primary status-count"></span>
                                </button>
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="Sin señal GPS" data-filter-status-id="5">
                                    <i class="fa fa-signal orange2 faa-flash" aria-hidden="true"></i>
                                    <span class="text-warning status-count"></span>
                                </button>
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="No reporta" data-filter-status-id="1">
                                    <i class="fa fa-clock-o red faa-flash" aria-hidden="true"></i>
                                    <span class="text-danger status-count"></span>
                                </button>
                                <button class="btn btn-default faa-parent animated-hover btn-filter-vehicles" title="En taller" data-filter-status-id="31">
                                    <i class="fa fa-wrench faa-wrench blue" aria-hidden="true"></i>
                                    <span class="text-info status-count"></span>
                                </button>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="opciones" class="panel-body collapse fade no-padding">
                <div>
                    @if(Auth::user()->isAdmin())
                    <div class="col-md-3 col-md-offset-3 col-sm-6 col-xs-12" style="padding: 10px">
                        <label for="empresa" style="margin: 4px !important;">
                            <i class="fa fa-building" aria-hidden="true"></i>&nbsp;
                            <span>@lang('Company')</span>
                        </label>
                        <select name='empresa' id='empresa' class='col-md-12 col-xs-12 col-sm-12'>
                            @foreach($companies as $company)
                                <option data-xy="{{ '' }}" value='{{ $company->id }}'>{{ $company->short_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div id="filter-vehicles" class="col-md-12 col-sm-12 col-xs-12" style="margin-top:3px;margin-bottom: 3px">
                        <h5 class="text-center">
                            <small class="">
                                <i class="fa fa-car"></i>
                                { filterVehicles.length }} vehículos
                            </small>
                        </h5>
                        <hr class="hr">
                        <button type="button" class="btn btn-xs btn-default" style="width: 40px;margin: 1px"
                                @click="seeOnMap(vehicle.number)"
                                v-for="vehicle in filterVehicles">
                            { vehicle.number }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="coordenadas" onchange="changeCity()" style="display: none" coorde=''></div>
    <div id="rutas" style="display:none"></div>

    <div class="map col-md-12 col-sm-12 col-xs-12 no-padding">
        <div class="col-md-12 frame-log" style="font-family: monospace !important"></div>
        <div id="map" style="width:100%;"></div>
        <div id="google-map-light-dream" class="col-md-12 col-sm-12 col-xs-12 p-0 map-report-historic" style="height: 1000px"></div>

        <div class="" style="position: absolute;top: 15px;right: 0;z-index: 1 !important;padding-right: 15px;width: 0">
            <div class="input-group pull-right">
            <span class="input-group-btn map-action-bar no-padding" style="display: flex;">
                <input id="number" type="number" onKeyPress="validar(event)" placeholder="N°" class="form-control tooltips pull-right"data-placement="bottom" data-title="Ingres un vehículo" value="" style="width: 80px">
                <button type="submit" class="btn btn-default btn-search-vehiculo faa-parent animated-hover pull-right" data-placement="bottom" onClick="searchMarker()" title="Buscar vehículo ingresado">
                    <i class="fa fa-search faa-shake" aria-hidden="true"></i>
                </button>
                <button type="submit" class="btn btn-primary btn-show-historic faa-parent animated-hover tooltips" data-placement="bottom" disabled data-toggle="modal" data-target="#modal-historic-report" title="Ver histórico">
                    <i class="fa fa-map-o"></i>
                </button>
                <button type="button" class="btn btn-default btn-fullscreen faa-parent animated-hover" data-placement="bottom" onClick="autoSizeMap(2);$('.map').addClass('fullscreen');$('.btn-fullscreen').toggle()" title="Ampliar mapa">
                    <i class="fa fa-expand faa-burst" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-primary btn-fullscreen faa-parent animated-hover" data-placement="bottom" onClick="autoSizeMap(90);$('.map').removeClass('fullscreen');;$('.btn-fullscreen').toggle()" title="Reducir mapa" style="display: none">
                    <i class="fa fa-compress faa-falling" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-default faa-parent animated-hover" data-placement="left" title="Menú mapa" data-toggle="collapse" data-target=".options-map">
                    <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                </button>
            </span>
            </div>
            <div class="input-group pull-right">
            <span class="input-group-btn map-action-bar no-padding" style="display: grid">
                <button class="btn btn-default btn-traffic faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="showTraffic()" title="Mostrar tráfico">
                    <i class="fa fa-random faa-passing" aria-hidden="true"></i>
                </button>
                <button class="btn btn-warning btn-traffic faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="hideTraffic()" style="display: none" title="Ocultar tráfico">
                    <i class="fa fa-random faa-passing-reverse"></i>
                </button>
                <button class="btn btn-warning btn-route-layer faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="hideRouteLayer()" title="Ocultar ruta de seguimiento">
                    <i class="fa fa-flag faa-flash" aria-hidden="true"></i>
                </button>
                <button class="btn btn-default btn-route-layer faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="showRouteLayer()" style="display: none" title="Permitir rutas de seguimiento">
                    <i class="fa fa-flag" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-default btn-display-control-points faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="showControlPoints()" title="Mostrar Puntos Control">
                    <i class="fa fa-map-marker faa-pulse" style="position:relative;color: darkcyan" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger btn-display-control-points faa-parent animated-hover options-map collapse" data-placement="bottom" onclick="hideControlPoints()" style="display: none" title="Ocultar Puntos Control">
                    <i class="fa fa-map-marker faa-pulse" aria-hidden="true"></i>
                </button>

                <button type="button" class="btn btn-default faa-parent animated-hover btn-play-audio hide options-map collapse" data-placement="bottom" onClick="setMuteAudio(true);alert_type('<i class=\'fa fa-bell-slash faa-tada animated fa-2x\'></i> Las alarmas sonoras han sido desactivadas', 'error')" title="Alarmas sonoras activas">
                    <i class="fa fa-bell faa-ring" aria-hidden="true"></i>
                </button>
                <button type="button" class="btn btn-danger faa-parent animated-hover btn-stop-audio hide options-map collapse" data-placement="bottom" onClick="setMuteAudio(false);gsuccess('<i class=\'fa fa-bell faa-ring animated fa-2x\'></i> Las alarmas sonoras se han activado')" title="Alarmas sonoras NO activas">
                    <i class="fa fa-bell-slash faa-tada"></i>
                </button>
            </span>
            </div>
        </div>
        <div class="disabled" style="position: absolute;top: 15px;left: 10px;">
            <button class="btn btn-default text-bold disabled pull-right" style="cursor: none">
                <i class="fa fa-flag"></i> <small class="tracking-route"></small>
            </button>
        </div>
    </div>
    <div class="" style="clear:both"></div>
    <div id="sin_actividad"></div>

    <div class="modal fade" id="modal-historic-report" style="background: #535353;opacity: 0.96;height: 100%">
        <div class="modal-dialog modal-lg" style="width: 98%;height: 100%">
            <div class="modal-content">
                <div class="modal-header text-center" style="background: transparent;position: absolute;z-index: 1;width: 100%;padding: 0;border: none;height: 0;padding-top: 7px">
                    <button type="button" class="btn btn-sm btn-default text-white" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-undo"></i> Volver
                    </button>
                </div>
                <div class="modal-body p-0" style="height: 100%">
                    <div class='hide cargando' ><br><br><div class='fa-lg text-center'><h1><span class="faa-vertical animated"><i class="fa fa-cog fa fa-spin"></i></span>  Cargando...</h1></div></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @include('template.google.maps')

    <script type="application/javascript">
        @php
            $user = Auth::user();
            $companySession = $user->company;
        @endphp
        const companySession = parseInt("{{ $companySession->id }}");
        const userRole = parseInt("{{ $user->role_id }}");

        const infoSession = {
            company: companySession,
            userID: parseInt("{{ $user->id }}"),
            userIsProprietary: "{{ $user->isProprietary() ? 'true': 'false'}}" == true,
            userRoleId: userRole,
            userIsAdmin: (userRole === 2 || userRole === 1 || companySession === 6),
            userIsSuperAdmin: userRole === 6,
            userBelongsToTaxcentral: companySession === 21
        };

        var coo = '3.454644067130714, -76.52202325439453';
    </script>

    @if(Auth::user()->isAdmin())
        <script src="{{ asset('operation/admin.js') }}" type="text/javascript"></script>
    @endif

    <script src="http://www.pcwserviciosgps.com/pcw_mov/php/bootstrap/pluginsBootstrap/Switch/dist/js/bootstrap-switch.js"></script>
    <template id="marker-animation-scripts"></template>

    <script type="application/javascript">
        $(document).ready(function(){
            $('.btn-show-historic').click(function(){
                const modal = $("#modal-historic-report");
                modal.css('height', (window.innerHeight+500)+'px');
                const modalBody = modal.find(".modal-body");
                if( !modalBody.find('iframe').length ){
                    const modalHeight = (window.innerHeight)+'px';

                    modalBody.empty().append("<iframe id=\"iframe-historic-report\" src=\"http://beta.pcwserviciosgps.com/link/reportes/rutas/historico/{{ Auth::user()->id }}\" width=\"100%\" height=\""+modalHeight+"\"></iframe>");
                }
            });

            $('#modal-historic-report').on('shown.bs.modal', function () {
                $('#monitorear').bootstrapSwitch('state', false);
            }).on('hidden.bs.modal', function (e) {
                $('#monitorear').bootstrapSwitch('state', true);
            })
        });

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

        $(document).ready(function () {
            initializeMap(() => {
                loadScript("https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js", function(){
                    loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/vendor/markerAnimate.js", function(){
                        loadScript("https://cdnjs.cloudflare.com/ajax/libs/marker-animate-unobtrusive/0.2.8/SlidingMarker.min.js", function(){
                            SlidingMarker.initializeGlobally();
                            loadScript("{{ asset('operation/mapa.js') }}", function(){

                            });
                        });
                    });
                });
            });
        });
    </script>
@endsection
