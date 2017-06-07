@extends('layout')

@section('stylesheets')
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet"/>
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet"/>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Register historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Register historic')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search report')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date-report"
                                       class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control"
                                           placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company-report"
                                       class="control-label field-required">@lang('Company')</label>
                                <div class="form-group">
                                    <select name="company-report" id="company-report"
                                            class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select an option')</option>
                                        @foreach($companies as $company)
                                            <option value="{{$company->id_empresa}}">{{ $company->des_corta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 hide">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report"
                                            class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js"></script>

    @include('template.google.maps')

    <script type="application/javascript">
        $('.menu-passengers').addClass('active');
        var busMarker = null;
        var iconbus = '{{ asset('img/bus.png') }}';

        var controlPointIcon = [
            '{{ asset('img/control-point-0.png') }}',
            '{{ asset('img/control-point-1.png') }}'
        ];

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if ($(this).isValid()) {
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: '{{ route('passengers-search-report') }}',
                        data: $(this).serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                var roouteSelect = $('#route-report');
                roouteSelect.html($('#select-loading').html()).trigger('change.select2');
                roouteSelect.load('{{route('route-ajax-action')}}', {
                    option: 'loadRoutes',
                    company: $(this).val()
                }, function () {
                    roouteSelect.trigger('change.select2');
                });
            });

            $('#route-report').change(function () {
                $('.report-container').slideUp();
                if (is_not_null($(this).val())) {
                    $('.form-search-report').submit();
                }
            });

            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });
        });

        $('body').on('click', '.btn-show-chart-route-report', function () {
            //map.clearAllMarkers();
            var chartRouteReport = $("#chart-route-report");
            chartRouteReport.html(loading);
            $('.report-info').html(loading);
            $.ajax({
                url: $(this).data('url'),
                success: function (data) {
                    if (!data.empty) {
                        $('.modal-report-vehicle').html(data.vehicle + ' <i class="fa fa-hand-o-right" aria-hidden="true"></i> ' + data.plate);
                        $('.modal-report-vehicle-speed').html(data.vehicleSpeed);
                        $('.modal-report-vehicle-speed-progress').css('width', parseInt(data.vehicleSpeed) + '%');

                        $('.modal-report-route-name').html(data.route);
                        $('.modal-report-route-percent').html(data.routePercent);
                        $('.modal-report-route-percent-progress').css('width', parseInt(data.routePercent) + '%');

                        var dataValues = data.values;
                        var dataDates = data.dates;
                        var dataTimes = data.times;
                        var dataDistances = data.distances;
                        var routeDistance = data.routeDistance;
                        var latitudes = data.latitudes;
                        var longitudes = data.longitudes;
                        var dataPercentDistances = [];
                        var controlPoints = data.controlPoints;
                        var urlLayerMap = data.urlLayerMap;

                        new google.maps.KmlLayer({
                            url: urlLayerMap,
                            map: map
                        });

                        controlPoints.forEach(function (cp, i) {
                            new google.maps.Marker({
                                title: cp.nombre,
                                map: map,
                                icon: controlPointIcon[cp.trayecto],
                                animation: google.maps.Animation.DROP,
                                position: {lat: parseFloat(cp.lat), lng: parseFloat(cp.lng)}
                            });
                        });

                        dataDates.forEach(function (e, i) {
                            dataDates[i] = e;
                        });
                        dataValues.forEach(function (e, i) {
                            dataValues[i] = e * 60;
                        });
                        dataDistances.forEach(function (e, i) {
                            dataPercentDistances[i] = ((dataDistances[i] / routeDistance) * 100).toFixed(1);
                            dataDistances[i] = e / 1000;
                        });

                        chartRouteReport.empty().hide().sparkline(dataValues, {
                            type: 'line',
                            width: '1180px',
                            height: '80px',
                            fillColor: 'transparent',
                            spotColor: '#f0eb54',
                            lineColor: '#68a8b6',
                            minSpotColor: '#F04B46',
                            maxSpotColor: '#259bf0',
                            lineWidth: 3.5,
                            spotRadius: 7,
                            normalRangeMin: -50, normalRangeMax: 50,
                            tooltipFormat: '<?="'+
                            '<div class=\"info-route-report\">'+
                                '<b>Estado:</b> {{offset:times}} <br>'+
                                '<b>Hora:</b> {{offset:dates}} <br>'+
                                '<b>Distancia:</b> {{offset:distance}} Km <br>'+
                                '<b>Recorrido:</b> {{offset:percent}}% <br>'+
                                '<span class=\"hide latitude\">{{offset:latitude}}</span><br>'+
                                '<span class=\"hide longitude\">{{offset:longitude}}</span>'+
                            '</div>'+
                        '"?>',
                            tooltipValueLookups: {
                                'times': dataTimes,
                                'dates': dataDates,
                                'distance': dataDistances,
                                'percent': dataPercentDistances,
                                'latitude': latitudes,
                                'longitude': longitudes
                            }
                        }).slideDown();

                        chartRouteReport.bind('sparklineRegionChange', function (ev) {
                            //var sparkline = ev.sparklines[0],info = sparkline.getCurrentRegionFields();

                            setTimeout(function () {
                                var t = $('.info-route-report');
                                var latitude = t.find('.latitude').html();
                                var longitude = t.find('.longitude').html();

                                if(!busMarker){
                                    busMarker = new google.maps.Marker({
                                        map: map,
                                        icon: iconbus,
                                        animation: google.maps.Animation.DROP
                                    });
                                }
                                busMarker.setPosition({lat: parseFloat(latitude), lng: parseFloat(longitude)})
                                //map.setCenter(busMarker.getPosition());
                            },10);
                        }).bind('mouseleave', function() {

                            busMarker?busMarker.setMap(null):null;
                            busMarker = null;
                            //map.setCenter(mapDefaultOptions.center);
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
        });
    </script>
@endsection
