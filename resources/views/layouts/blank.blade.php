@php( $current = request()->segment(count(request()->segments())) )
<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.6
Version: 4.5.4
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    @include('template.metronic.header')
</head>
<!-- END HEAD -->

<body class="">

<div class="page-content-wrapper">
    <!-- BEGIN CONTENT BODY -->
    <div class="p-20" style="background:white">
        <div class="body-content" style="display: none">
            @yield('content')
        </div>
    </div>
</div>
<!-- BEGIN FOOTER -->

@if(isset($mail))
    <br><hr><br>
@endif

<div class="page-footer">
    <div class="page-footer-inner col-md-12 text-center" style="width: 100%"> <b>{{ date('Y') }}</b> <i class="fa fa-rocket"></i> PCW @
        <a href="https://pcwtecnologia.com" title="PCW Tecnología" style="color: #419368" target="_blank">tecnologia.com</a>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
@if(!isset($mail))
    @include('template.metronic.plugins')
@endif
</body>

</html>