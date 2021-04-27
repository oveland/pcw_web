@extends('layout')

@section('stylesheets')
    <style>
        .page-content{
            padding: 0 !important;
        }
        .opacity-0 {
            opacity: 0;
        }

        .page-header.navbar.navbar-fixed-top, .page-header.navbar.navbar-static-top {
            z-index: 10001 !important;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li class="active">@lang('Video')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Video')</small>
    </h1>

    <div class="" style="position: relative">
        <div style="position: absolute; width: 100%;width: 100%;top: -60px;z-index: -1;">
            <div style="background: white;position: absolute;height: 60px;z-index: 100;width: 100%;"></div>
            <iframe id="ts-iframe" height="500px" class="col-md-12 no-padding opacity-0" frameborder="0" src="http://dev.pcwserviciosgps.com/reportes/rutas/reporte-de-ruta/ts"></iframe>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var logged = false;
        var iframe = document.getElementById('ts-iframe');
        iframe.onload = function(data) {
            if(!logged){
                logged = true;
                const urlVideo = "http://alpha.pcwserviciosgps.com/videoIndex";
                $('#ts-iframe').attr('src',urlVideo).attr('height', (window.innerHeight - 100) + 'px');
                setTimeout(()=>{ $('#ts-iframe').removeClass('opacity-0'); }, 1000);
            }
        }
    </script>
@endsection