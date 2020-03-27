<!DOCTYPE html>
<html lang="{{ config('app.locale') }}">
<head>
    @yield('login-style')
    @include('template.header')
    @yield('stylesheets')
</head>
<body>
<div id="app">
    @yield('content')
</div>

<!-- Scripts -->
@include('template.plugins')
@yield('scripts')

</body>
</html>
