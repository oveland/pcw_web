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
            <button class="pull-right btn btn-info btn-paint-coordinates">
                <i class="fa fa-map-marker"></i> @lang('Paint Coordinates') <i class="fa fa-hand-o-right"></i>
            </button>
            <hr>
            <textarea id="input-coordinates" title="@lang('Coordinates')" rows="40" class="form-control"></textarea>
        </div>
        <div id="google-map-light-dream" class="col-md-8 p-0" style="height: 1000px"></div>
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            initializeMap();

            $('.btn-paint-coordinates').on('click',function () {
                var coordinates = $('#input-coordinates').val();
                if( is_null(coordinates) ){
                    alert_type('@lang('Type coordinates!')')
                }else{
                    (coordinates.split("\n")).forEach(function (c,i) {
                        c = c.split(", ");
                        var latitude = c[0];
                        var longitude = c[1];

                        new google.maps.Marker({
                            map: map,
                            //icon: iconbus,
                            //animation: google.maps.Animation.DROP,
                            position: {lat: parseFloat(latitude), lng: parseFloat(longitude)}
                        });
                    });
                }
            })
        });
    </script>
@endsection