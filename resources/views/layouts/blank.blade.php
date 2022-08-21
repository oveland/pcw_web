@php( $current = request()->segment(count(request()->segments())) )
<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    <title>@yield('title','PCW | Servicios GPS')</title>
    @yield('login-style')
    @include('layouts.template.header')
    @yield('stylesheets')
</head>

<body>

<div class="page-content-wrapper">
    <div class="p-20">
        <div class="body-content" style="display: none">
            @yield('content')
        </div>
    </div>
</div>

@if(isset($mail))
    <br><hr><br>
@endif

<div class="page-footer">
    <div class="page-footer-inner col-md-12 text-center text-white" style="width: 100%"> <b>{{ date('Y') }}</b> <i class="fa fa-rocket"></i> PCW @
        <a href="https://pcwtecnologia.com" title="PCW Tecnología" style="color: #419368" target="_blank">tecnologia.com</a>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>

<template id="animated-loading">
    <div class="col-md-12 text-center">
        <img class="" alt="@lang('Loading')..." src="https://www.pcwserviciosgps.com/files/loading.svg" height="200px">
    </div>
</template>

@if(!isset($mail))
    @include('layouts.template.metronic.plugins')
@endif
</body>

</html>
