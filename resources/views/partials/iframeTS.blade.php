@extends('layouts.app')

@section('stylesheets')
    <style>
        .opacity-0 {
            opacity: 0;
        }
    </style>
@endsection

@section('content')
    <div class="col-md-12">
        <div style="position: absolute;width: 100%;top: 10px;z-index: 100000">
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
        </div>

        <div style="position: absolute;width: 99%;top: -10px;">
            <div style="background: white;position: absolute;height: 60px;z-index: 100;width: 100%;"></div>
            <iframe id="ts-iframe" style="height: 90vh" class="col-md-12 no-padding opacity-0" frameborder="0" src="http://dev.pcwserviciosgps.com/reportes/rutas/reporte-de-ruta/ts"></iframe>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.menu-passengers, .menu-passengers-video').addClass('active-animated');

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