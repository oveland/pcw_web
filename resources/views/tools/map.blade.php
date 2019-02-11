@extends('layout')

@include('template.google.maps')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Configurations')</a></li>
        <li><a href="javascript:;">@lang('Migrations')</a></li>
        <li class="active">@lang('Map Tool')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Migrations')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Map Tool')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->

    <div class="row">
        <div class="col-md-4">
            <label for="input-coordinates" class="">@lang('Coordinates')</label>
            <button class="btn btn-sm btn-info pull-right btn-traffic" onclick="showTraffic()">
                <i class="fa fa-map-marker"></i> @lang('Show Traffic')
            </button>
            <button class="btn btn-sm btn-warning pull-right btn-traffic" onclick="hideTraffic()" style="display: none">
                <i class="fa fa-map-marker"></i> @lang('Hide Traffic')
            </button>

            <hr>

            <div class="btn-group-vertical m-r-5">
                <button class="btn btn-sm btn-info btn-paint-coordinates">
                    <i class="fa fa-map-marker"></i> @lang('Paint Coordinates') <i class="fa fa-hand-o-right"></i>
                </button>
                <button onclick="clearMarkers();" class="btn btn-sm btn-warning">
                    <i class="fa fa-times"></i> @lang('Hide Markers')
                </button>
                <button onclick="showMarkers();" class="btn btn-sm btn-success">
                    <i class="fa fa-eye"></i> @lang('Show All Markers')
                </button>
                <button onclick="deleteMarkers();" class="btn btn-sm btn-danger">
                    <i class="fa fa-trash"></i> @lang('Delete Markers')
                </button>
            </div>
            <hr>
            <textarea id="input-coordinates" title="@lang('Coordinates')" rows="40" class="form-control"></textarea>
        </div>
        <div id="google-map-light-dream" class="col-md-8 p-0" style="height: 1000px"></div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        let markers = [];
        let infowindows = [];
        $(document).ready(function () {
            initializeMap();


            $('.btn-paint-coordinates').on('click',function () {
                let coordinates = $('#input-coordinates').val();
                if( is_null(coordinates) ){
                    gerror('@lang('Type coordinates!')')
                }else{
                    (coordinates.split("\n")).forEach(function (c,i) {
                        c = c.split(", ");
                        let latitude = c[0];
                        let longitude = c[1];
                        addMarker(new google.maps.LatLng({lat: parseFloat(latitude), lng: parseFloat(longitude)}));
                    });
                }
            });

            $('.btn-draw-route-kmz').on('click',function () {
                let routeKmz = $('#route-kmz').val();
                if( is_null(routeKmz) ){
                    alert_type('@lang('Select a route')')
                }else{
                    (coordinates.split("\n")).forEach(function (c,i) {
                        c = c.split(", ");
                        let latitude = c[0];
                        let longitude = c[1];
                        addMarker(new google.maps.LatLng({lat: parseFloat(latitude), lng: parseFloat(longitude)}));
                    });
                }
            });
        });
        setTimeout(function(){
            map.addListener('click', function(event) {
                addMarker(event.latLng,true);
            });
        },1500);

        function showTraffic(){
            $('.btn-traffic').toggle();
            trafficLayer.setMap(map);
        }
        function hideTraffic(){
            $('.btn-traffic').toggle();
            trafficLayer.setMap(null);
        }

        // Adds a marker to the map and push to the array.
        function addMarker(location,clicked) {
            let marker = new google.maps.Marker({
                position: location,
                map: map
            });

            if(clicked)marker.setIcon('http://maps.google.com/mapfiles/ms/icons/blue-dot.png');
            else marker.setIcon('{{ asset('img/point-map-on-road.png') }}');

            let infowindow = new google.maps.InfoWindow({
                content: location.lat()+', '+location.lng()
            });

            marker.addListener('click', function() {
                infowindow.open(marker.get('map'), marker);
            });

            markers.push(marker);
            infowindows.push(infowindow);
        }

        // Sets the map on all markers in the array.
        function setMapOnAll(map) {
            for (let i = 0; i < markers.length; i++) {
                markers[i].setMap(map);
            }
        }

        // Removes the markers from the map, but keeps them in the array.
        function clearMarkers() {
            setMapOnAll(null);
        }

        // Shows any markers currently in the array.
        function showMarkers() {
            setMapOnAll(map);
        }

        // Deletes all markers in the array by removing references to them.
        function deleteMarkers() {
            clearMarkers();
            markers = [];
            infowindows = [];
        }
    </script>
@endsection