@php
    $current = request()->segment(count(request()->segments()));
@endphp
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
<html lang="{{ app()->getLocale() }}" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="{{ app()->getLocale() }}" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="{{ app()->getLocale() }}">
<!--<![endif]-->
<!-- BEGIN HEAD -->

<head>
    <title></title>@include('template.metronic.header')
</head>
<!-- END HEAD -->

<body class="page-header-fixed page-footer-fixed page-sidebar-closed-hide-logo page-content-white page-md">
    <!-- BEGIN HEADER -->
    @include('template.metronic.navbar')
    <!-- END HEADER -->

    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"></div>
    <!-- END HEADER & CONTENT DIVIDER -->

    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        @include('template.metronic.sideBar')

        @include('template.metronic.content')

        <!-- BEGIN QUICK SIDEBAR -->
        <a href="javascript:;" class="page-quick-sidebar-toggler">
            <i class="icon-login"></i>
        </a>
        <!-- END QUICK SIDEBAR -->
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="page-footer-inner col-md-12 text-center"><b>2020</b> &copy;
            <a href="http://brochure.pcwserviciosgps.com/" title="PCW Tecnología" class="text-bold text-muted"
               target="_blank">Powered by PCW</a>
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!-- END FOOTER -->

    @include('template.metronic.plugins')

</body>

</html>