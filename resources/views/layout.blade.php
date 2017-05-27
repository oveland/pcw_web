<!doctype html>
<html lang="es">
<head>
    @include('template.header')
    @yield('stylesheets')
</head>
<body class="">

<!-- begin #page-loader -->
<div id="page-loader" class="fade">
    <span class="spinner"></span>
</div>
<!-- end #page-loader -->

<!-- begin #page-container -->
<!-- <div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-two-sidebar page-with-footer page-footer-fixed in"> -->
<div id="page-container" class="fade page-container page-header-fixed page-sidebar-fixed page-with-footer page-footer-fixed in">
    @include('template.navbar')
    @include('template.sideBar')
    @include('template.content')
    {{--@include('template.rightSidebar')--}}
    {{-- @include('template.panelBox') --}}
</div>
<!-- end #page-container -->

<template id="select-loading">
    <option value=""><i class="fa fa-spinner fa-pulse fa-fw"></i> @lang('Loading...')</option>
</template>

@include('template.plugins')

@yield('scripts')

<script>
    $(document).ready(function () {
        App.init();
    });
</script>
</body>
</html>