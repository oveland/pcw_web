@extends('layout')

@section('stylesheets')
    <style>
        .page-content{
            padding: 0 !important;
        }
    </style>
@endsection

@section('content')
    <iframe class="" src="{{ $link }}" width="100%" height="1100px"></iframe>
@endsection

@section('scripts')

    <script type="application/ecmascript">
        $('.menu-takings-passengers, .menu-takings-passengers-liquidation').addClass('active-animated');
        @if(!Auth::user()->isAdmin())
        //$(document).ready(loadSelectRouteReport(null));
        @endif
    </script>
@endsection