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
    <div class="portlet light bordered m-t-20" style="position: relative; z-index: 1000">
        <div class="portlet-body">
            <ul class="nav nav-tabs m-0">
                <li class="active">
                    <a href="#tab_photos" data-toggle="tab">
                        <i class="fa fa-image"></i> @lang('Photos')
                    </a>
                </li>
                <li>
                    <a href="#tab_video" data-toggle="tab">
                        <i class="fa fa-video-camera"></i> @lang('Video')
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane fade active in" id="tab_photos">
                    <iframe class="" src="{{ $link }}" width="100%" height="1100px" frameborder="0"></iframe>
                </div>
                <div class="tab-pane fade" id="tab_video">
                    <div class="" style="position: relative">
                        <div style="position: absolute; width: 100%;width: 100%;top: -60px;z-index: -1;">
                            <div style="background: white;position: absolute;height: 60px;z-index: 100;width: 100%;"></div>
                            <iframe id="ts-iframe" height="500px" class="col-md-12 no-padding opacity-0" frameborder="0" src="{{ route('report-route-ts') }}"></iframe>
                        </div>
                    </div>
                </div>
            </div>
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