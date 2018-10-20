<!doctype html>
<html lang="{{ config('app.locale') }}">
<head>
    @include('template.header')
    @yield('stylesheets')
    @yield('templateStyles')
</head>
<body class="">

<!-- begin #page-loader -->
<div id="page-loader" class="fade in">
    <span class="spinner"></span>
</div>
<!-- end #page-loader -->

<!-- begin #page-container -->
<!-- <div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-footer-fixed"> -->
<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-footer page-footer-fixed">
    @include('template.navbar')
    @include('template.sideBar')
    @include('template.content')
    {{--@include('template.rightSidebar')--}}
    {{--@include('template.panelBox')--}}
</div>
<!-- end #page-container -->

<template id="loading">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12 text-center"><i class="fa fa-spinner fa-pulse fa-fw"></i></div>
    </div>
</template>

<template id="select-loading">
    <option value=""><i class="fa fa-spinner fa-pulse fa-fw"></i> @lang('Loading...')</option>
</template>

@include('template.plugins')
@include('partials.alerts.reports.passengers.issuesByVehiclesScript')

@yield('templateScripts')
@yield('scripts')

<script type="application/javascript">
    var loading = $('#loading').html();
    $(document).ready(function () {
        App.init();
    });
</script>
</body>
</html>