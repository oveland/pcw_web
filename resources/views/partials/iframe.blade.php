@extends('layout')

@section('stylesheets')
    <style>
        .page-content {
            padding: 0 !important;
            min-height: 95vh !important;
            height: 95vh !important;
        }
    </style>
@endsection

@section('content')
    <iframe class="" src="{{ $link }}" width="100%"  style="height: 95vh"></iframe>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            setTimeout(() => hideSideBar(), 2000);
        });
    </script>
@endsection