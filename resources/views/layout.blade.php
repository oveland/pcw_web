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

<body class="page-header-fixed page-footer-fixed page-sidebar-closed-hide-logo page-content-white page-md">
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top map-header-bg">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="{{ url('/')  }}" class="m-t-5 m-r-5 text-center animated">
                <span class="text-white f-s-16">PCW</span><span class="text-success f-s-22">SMS</span>
            </a>
            <div class="menu-toggler sidebar-toggler-old pull-right"></div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse"> </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="col-md-offset-3 col-sm-6 col-md-3 col-xs-12">
            @include('flash::message')
        </div>
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-extended dropdown-notification hide" id="header_notification_bar">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">
                        <i class="icon-bell"></i>
                        <span class="badge badge-default"> 7  </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3>
                                <span class="bold">12 pending</span> notifications
                            </h3>
                            <a href="page_user_profile_1.html">view all</a>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 250px;" data-handle-color="#637283">
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">just now</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-success">
                                                        <i class="fa fa-plus"></i>
                                                    </span> New user registered. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">3 mins</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-danger">
                                                        <i class="fa fa-bolt"></i>
                                                    </span> Server #12 overloaded. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">10 mins</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-warning">
                                                        <i class="fa fa-bell-o"></i>
                                                    </span> Server #2 not responding. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">14 hrs</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-info">
                                                        <i class="fa fa-bullhorn"></i>
                                                    </span> Application error. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">2 days</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-danger">
                                                        <i class="fa fa-bolt"></i>
                                                    </span> Database overloaded 68%. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">3 days</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-danger">
                                                        <i class="fa fa-bolt"></i>
                                                    </span> A user IP blocked. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">4 days</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-warning">
                                                        <i class="fa fa-bell-o"></i>
                                                    </span> Storage Server #4 not responding dfdfdfd. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">5 days</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-info">
                                                        <i class="fa fa-bullhorn"></i>
                                                    </span> System Error. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="javascript:;">
                                        <span class="time">9 days</span>
                                        <span class="details">
                                                    <span class="label label-sm label-icon label-danger">
                                                        <i class="fa fa-bolt"></i>
                                                    </span> Storage server failed. </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- END NOTIFICATION DROPDOWN -->
                <!-- BEGIN INBOX DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-extended dropdown-inbox hide" id="header_inbox_bar">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">
                        <i class="icon-envelope-open"></i>
                        <span class="badge badge-default"> 4 </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="external">
                            <h3>You have
                                <span class="bold">7 New</span> Messages</h3>
                            <a href="app_inbox.html">view all</a>
                        </li>
                        <li>
                            <ul class="dropdown-menu-list scroller" style="height: 275px;" data-handle-color="#637283">
                                <li>
                                    <a href="#">
                                        <span class="photo"></span>
                                        <span class="subject">
                                                    <span class="from"> Lisa Wong </span>
                                                    <span class="time">Just Now </span>
                                                </span>
                                        <span class="message"> Vivamus sed auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="photo"></span>
                                        <span class="subject">
                                                    <span class="from"> Richard Doe </span>
                                                    <span class="time">16 mins </span>
                                                </span>
                                        <span class="message"> Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="photo"></span>
                                        <span class="subject">
                                                    <span class="from"> Bob Nilson </span>
                                                    <span class="time">2 hrs </span>
                                                </span>
                                        <span class="message"> Vivamus sed nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="photo"></span>
                                        <span class="subject">
                                                    <span class="from"> Lisa Wong </span>
                                                    <span class="time">40 mins </span>
                                                </span>
                                        <span class="message"> Vivamus sed auctor 40% nibh congue nibh... </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#">
                                        <span class="photo"></span>
                                        <span class="subject">
                                                    <span class="from"> Richard Doe </span>
                                                    <span class="time">46 mins </span>
                                                </span>
                                        <span class="message"> Vivamus sed congue nibh auctor nibh congue nibh. auctor nibh auctor nibh... </span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- END INBOX DROPDOWN -->

                <li class="dropdown tooltips" data-title="@lang('Dispatch report')" data-placement="bottom">
                    <a href="{{ route('report-dispatch')  }}" class="dropdown-toggle p-t-10 faa-parent animated-hover">
                        <i class="fa fa-rocket faa-float m-r-10"></i>
                    </a>
                </li>
                <li class="dropdown tooltips" data-title="@lang('Historic report')" data-placement="bottom">
                    <a href="{{ route('report-route-historic')  }}" class="dropdown-toggle p-t-10">
                        <i class="fa fa-map-o faa-vertical m-r-10"></i>
                    </a>
                </li>
                <li class="dropdown tooltips" data-title="@lang('Control point time report')" data-placement="bottom">
                    <a href="{{ route('report-route-control-points')  }}" class="dropdown-toggle p-t-10">
                        <i class="fa fa-map-marker faa-vertical m-r-10"></i>
                    </a>
                </li>

                <!-- BEGIN USER LOGIN DROPDOWN -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-user">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">

                        <span class="username">
                            <i class="icon-user"></i>
                            @if(Auth::user())
                                {{ Auth::user()->name }}
                            @endif
                        </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <li>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="icon-key"></i> @lang('Logout')
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hide">
                                {{ csrf_field() }}
                            </form>

                        </li>
                    </ul>
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                <li class="dropdown dropdown-quick-sidebar-toggler hide">
                    <a href="javascript:;" class="dropdown-toggle">
                        <i class="icon-logout"></i>
                    </a>
                </li>
                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<!-- BEGIN HEADER & CONTENT DIVIDER -->
<div class="clearfix"></div>
<!-- END HEADER & CONTENT DIVIDER -->
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <!-- BEGIN SIDEBAR -->
    <div class="page-sidebar-wrapper">
        <!-- BEGIN SIDEBAR -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <div class="page-sidebar navbar-collapse collapse">
            <!-- BEGIN SIDEBAR MENU -->
            <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
            <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
            <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
            <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
            <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
            <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->

            <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 0">
                <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                <li class="sidebar-toggler-wrapper hide">
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler"></div>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                </li>
                <br>
                <li class="nav-item start">
                    <a href="http://www.pcwserviciosgps.com/pcw_gps/php/inicio.php" target="_blank" class="faa-parent animated-hover nav-link">
                        <i class="fa fa-home faa-vertical"></i>
                        <span>Inicio</span>
                    </a>

                </li>
                <!-- DOC: To remove the search box from the sidebar you just need to completely remove the below "sidebar-search-wrapper" LI element -->
                <li class="sidebar-search-wrapper hide">
                    <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                    <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
                    <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
                    <form class="sidebar-search  " action="page_general_search_3.html" method="POST">
                        <a href="javascript:;" class="remove">
                            <i class="icon-close"></i>
                        </a>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="@lang('Search')">
                            <span class="input-group-btn">
                                <a href="javascript:;" class="btn submit">
                                    <i class="icon-magnifier"></i>
                                </a>
                            </span>
                        </div>
                    </form>
                    <!-- END RESPONSIVE QUICK SEARCH FORM -->
                </li>

                @if( Auth::user() && Auth::user()->canAdmin())
                    <li class="nav-item start {{ $baseMenu == __('url-administration')?'active-animated':'' }}">
                        <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                            <i class="fa fa-cogs faa-horizontal"></i>
                            <span>@lang('Administration')</span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item menu-administration-vehicles">
                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                    <b class="caret pull-right"></b>
                                    <i class="fa fa-bus faa-vertical"></i>
                                    @lang('Vehicles')
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item menu-administration-vehicles-peak-and-plate">
                                        <a href="{{ route('admin-vehicles-peak-and-plate')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-ban faa-vertical" aria-hidden="true"></i>
                                            @lang('Peak and Plate')
                                        </a>
                                    </li>
                                    <li class="nav-item menu-administration-vehicles-maintenance">
                                        <a href="{{ route('admin-vehicles-maintenance')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-wrench faa-wrench" aria-hidden="true"></i>
                                            @lang('Maintenance')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @if( Auth::user() && Auth::user()->canAdminGPS() )
                                <li class="nav-item menu-administration-gps">
                                    <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                        <i class="fa fa-podcast faa-vertical"></i>
                                        @lang('GPS')
                                        <span class="arrow"></span>
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="nav-item menu-administration-gps-manage">
                                            <a href="{{ route('admin-gps-manage')  }}" class="faa-parent animated-hover nav-link">
                                                <i class="fa fa-signal faa-vertical" aria-hidden="true"></i>
                                                @lang('Manage')
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                            <li class="nav-item menu-administration-proprietaries">
                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                    <i class="fa fa-user faa-vertical"></i>
                                    @lang('Proprietaries')
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item menu-administration-proprietaries-manage">
                                        <a href="{{ route('admin-proprietaries-manage')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-user-plus faa-vertical" aria-hidden="true"></i>
                                            @lang('Manage')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                @endif

                <li class="nav-item {{ $baseMenu == __('url-operation')?'active-animated':'' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-life-ring faa-spin"></i>
                        <span>@lang('Operation')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-operation-dispatch">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-rocket faa-passing"></i>
                                @lang('Dispatch')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-operation-dispatch-automatic">
                                    <a href="{{ route('operation-dispatch-auto-dispatcher') }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-play faa-burst" aria-hidden="true"></i>
                                        @lang('Automatic')
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $baseMenu == __('reports')?'active-animated':'' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-area-chart faa-horizontal"></i>
                        <span>@lang('Reports')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-routes">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-flag faa-vertical"></i>
                                @lang('Route')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-route-report">
                                    <a href="{{ route('report-route')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-rocket faa-float animated" aria-hidden="true"></i>
                                        @lang('Dispatch')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-route-historic">
                                    <a href="{{ route('report-route-historic')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-map-o" aria-hidden="true"></i>
                                        @lang('Historic')
                                    </a>
                                </li>
                                <li class="nav-item menu-off-road-report">
                                    <a href="{{ route('report-route-off-road-index')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-random faa-vertical" aria-hidden="true"></i>
                                        @lang('Off road')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-control-points">
                                    <a href="{{ route('report-route-control-points')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-map-marker faa-vertical" aria-hidden="true"></i>
                                        @lang('Control Points')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-routes-dispatch-users">
                                    <a href="{{ route('report-route-dispatch-users')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-users faa-vertical" aria-hidden="true"></i>
                                        @lang('Dispatch users')
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item menu-report-vehicles">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-bus faa-vertical"></i>
                                @lang('Vehicles')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-report-vehicles-issues">
                                    <a href="{{ route('report-vehicles-issues')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-wrench faa-wrench" aria-hidden="true"></i>
                                        @lang('Vehicle issues')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-vehicles-parked">
                                    <a href="{{ route('report-vehicle-parked')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-product-hunt faa-vertical" aria-hidden="true"></i>
                                        @lang('Parked vehicles')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-vehicles-speeding">
                                    <a href="{{ route('report-vehicle-speeding')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-tachometer text-danger faa-tada" aria-hidden="true"></i>
                                        @lang('Speeding')
                                    </a>
                                </li>
                                <li class="nav-item menu-report-vehicles-mileage">
                                    <a href="javascript:;" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-road faa-tada" aria-hidden="true"></i>
                                        @lang('Mileage')
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="nav-item menu-route-report menu-report-vehicles-mileage-daily">
                                            <a href="{{ route('report-vehicle-mileage')  }}" class="faa-parent animated-hover nav-link">
                                                <i class="fa fa-calendar-o faa-vertical" aria-hidden="true"></i> @lang('Daily')
                                            </a>
                                        </li>
                                        <li class="nav-item menu-route-report menu-report-vehicles-mileage-date-range">
                                            <a href="{{ route('report-vehicle-mileage-date-range')  }}" class="faa-parent animated-hover nav-link">
                                                <i class="fa fa-calendar faa-vertical" aria-hidden="true"></i> @lang('Date range')
                                            </a>
                                        </li>
                                    </ul>

                                </li>
                                <li class="nav-item menu-report-vehicles-round-trips">
                                    <a href="{{ route('report-vehicle-round-trips')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-retweet faa-shake" aria-hidden="true"></i>
                                        @lang('Round trips') / @lang('Routes')
                                    </a>
                                </li>
                                @if( Auth::user() && Auth::user()->canAdmin() )
                                    <li class="nav-item menu-report-vehicles-status">
                                        <a href="{{ route('report-vehicle-status')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-podcast blue faa-burst" aria-hidden="true"></i>
                                            @lang('GPS Status')
                                        </a>
                                    </li>
                                    <li class="nav-item menu-report-vehicles-gps">
                                        <a href="{{ route('report-vehicle-gps')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-signal blue faa-burst" aria-hidden="true"></i>
                                            @lang('GPS')
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item menu-passengers">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-users faa-vertical"></i>
                                @lang('Passengers')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                @if( Auth::user() && (Auth::user()->isAdmin() ||  Auth::user()->company->hasRecorderCounter()) )
                                    <li class="nav-item menu-passengers-recorders">
                                        <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                            <i class="fa fa-compass faa-vertical"></i>
                                            @lang('Recorders')
                                            <span class="arrow"></span>
                                        </a>
                                        <ul class="sub-menu">
                                            <li class="nav-item menu-passengers-recorders-consolidated">
                                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                                    <i class="fa fa-archive faa-vertical"></i>
                                                    @lang('Consolidated')
                                                    <span class="arrow"></span>
                                                </a>
                                                <ul class="sub-menu">
                                                    <li class="nav-item menu-passengers-recorders-consolidated-days">
                                                        <a href="{{ route('report-passengers-recorders-consolidated-daily')  }}" class="faa-parent animated-hover nav-link">
                                                            <i class="fa fa-file-archive-o faa-vertical" aria-hidden="true"></i>
                                                            @lang('Daily')
                                                        </a>
                                                    </li>
                                                    <li class="nav-item menu-passengers-recorders-consolidated-range">
                                                        <a href="{{ route('report-passengers-recorders-consolidated-date-range')  }}" class="faa-parent animated-hover nav-link">
                                                            <i class="fa fa-calendar faa-vertical" aria-hidden="true"></i>
                                                            @lang('Date range')
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-item menu-passengers-recorders-detailed">
                                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                                    <i class="fa fa-list-alt faa-vertical"></i>
                                                    @lang('Detailed')
                                                    <span class="arrow"></span>
                                                </a>
                                                <ul class="sub-menu">
                                                    <li class="nav-item menu-passengers-recorders-detailed-days">
                                                        <a href="{{ route('report-passengers-recorders-detailed-daily')  }}" class="faa-parent animated-hover nav-link">
                                                            <i class="fa fa-file-text-o faa-vertical" aria-hidden="true"></i>
                                                            @lang('Daily')
                                                        </a>
                                                    </li>
                                                    <li class="nav-item menu-passengers-recorders-detailed-range">
                                                        <a href="{{ route('report-passengers-recorders-detailed-date-range')  }}" class="faa-parent animated-hover nav-link">
                                                            <i class="fa fa-calendar faa-vertical" aria-hidden="true"></i>
                                                            @lang('Date range')
                                                        </a>
                                                    </li>
                                                </ul>
                                            </li>
                                            <li class="nav-item menu-passengers-recorders-fringes">
                                                <a href="{{ route('report-passengers-recorders-fringes') }}" class="faa-parent animated-hover nav-link">
                                                    <i class="fa fa-industry faa-vertical"></i>
                                                    @lang('Fringes')
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if( Auth::user() && (Auth::user()->isAdmin() ||  Auth::user()->company->hasSeatSensorCounter()) )
                                    <li class="nav-item menu-passengers-sensors">
                                        <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                            <i class="fa fa-crosshairs faa-vertical"></i>
                                            @lang('Sensors')
                                            <span class="arrow"></span>
                                        </a>
                                        <ul class="sub-menu">
                                            <li class="nav-item menu-passengers-sensors-counter">
                                                <a href="{{ route('report-passengers-sensors-counter')  }}" class="faa-parent animated-hover nav-link">
                                                    <i class="fa fa-user-circle-o faa-vertical" aria-hidden="true"></i>
                                                    @lang('Counter')
                                                </a>
                                            </li>
                                            <li class="nav-item menu-passengers-sensors-seats">
                                                <a href="{{ route('report-passengers-sensors-seats') }}" class="faa-parent animated-hover nav-link">
                                                    <i class="fa fa-table faa-vertical"></i>
                                                    @lang('Seats')
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if( Auth::user() && Auth::user()->isAdmin() )
                                    <li class="nav-item menu-passengers-mixed">
                                        <a href="{{ route('report-passengers-mixed')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-compass faa-vertical"></i> <i class="fa fa-crosshairs faa-vertical"></i>
                                            @lang('Mixed')
                                        </a>
                                    </li>
                                @endif

                                <li class="nav-item menu-passengers-geolocation">
                                    <a href="{{ route('report-passengers-geolocation')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-map-marker faa-vertical"></i> @lang('Geolocation')
                                    </a>
                                </li>

                                @if( Auth::user() && (Auth::user()->isAdmin() ||  Auth::user()->belongsToTaxcentral()) )
                                    <li class="nav-item menu-passengers-taxcentral">
                                        <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                            <i class="fa fa-building faa-vertical"></i>
                                            @lang('Taxcentral')
                                            <span class="arrow"></span>
                                        </a>
                                        <ul class="sub-menu">
                                            <li class="nav-item menu-passengers-taxcentral">
                                                <a href="{{ route('report-passengers-taxcentral-report')  }}" class="faa-parent animated-hover nav-link">
                                                    <i class="fa fa-user-circle-o faa-vertical" aria-hidden="true"></i>
                                                    @lang('Passengers report')
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif
                            </ul>
                        </li>
                        <li class="nav-item menu-drivers">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="icon-user fa faa-vertical"></i>
                                @lang('Drivers')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-drivers-consolidated hide">
                                    <a href="{{ route('report-drivers-consolidated')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-archive faa-vertical" aria-hidden="true"></i>
                                        @lang('Consolidated')
                                    </a>
                                </li>
                                <li class="nav-item menu-drivers-detailed">
                                    <a href="{{ route('report-drivers-detailed')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-list-alt faa-vertical" aria-hidden="true"></i>
                                        @lang('Detailed')
                                    </a>
                                </li>
                            </ul>
                        </li>

                        @if( Auth::user() && Auth::user()->isAdmin() )
                            <li class="nav-item menu-logs">
                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                    <i class="fa fa-user faa-vertical"></i>
                                    @lang('Users')
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item menu-logs">
                                        <a href="{{ route('report-user-access-log')  }}">
                                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                                            @lang('Access log')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif

                        @if( Auth::user() && (Auth::user()->isAdmin() || Auth::user()->company->id == \App\Models\Company\Company::BOOTHS) )
                            <li class="nav-item menu-booths">
                                <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                    <i class="fa fa-shield faa-vertical"></i>
                                    @lang('Booths')
                                    <span class="arrow"></span>
                                </a>
                                <ul class="sub-menu">
                                    <li class="nav-item menu-booths-historic">
                                        <a href="{{ route('report-booths-historic')  }}">
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                            @lang('Historic')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        @endif
                    </ul>
                </li>

                <li class="nav-item {{ $baseMenu == __('takings')?'active-animated':'' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-dollar faa-horizontal"></i>
                        <span>@lang('Takings')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-takings-passengers">
                            <a href="{{ route('takings-passengers-liquidation')  }}" class="faa-parent animated-hover">
                                <i class="fa fa-users faa-vertical"></i>
                                @lang('Passengers')
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
                <!-- DOC: To remove the sidebar toggler from the sidebar you just need to completely remove the below "sidebar-toggler-wrapper" LI element -->
                <li class="sidebar-toggler-wrapper hide">
                    <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
                    <div class="sidebar-toggler"></div>
                    <!-- END SIDEBAR TOGGLER BUTTON -->
                </li>
            </ul>
            <!-- END SIDEBAR MENU -->
            <!-- END SIDEBAR MENU -->
        </div>
        <!-- END SIDEBAR -->
    </div>
    <!-- END SIDEBAR -->
    <!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <!-- BEGIN CONTENT BODY -->
        <div class="page-content">
            <!-- BEGIN PAGE HEADER-->
            <!-- BEGIN THEME PANEL -->
            <div class="theme-panel hidden-xs hidden-sm hide">
                <div class="toggler"></div>
                <div class="toggler-close"></div>
                <div class="theme-options">
                    <div class="theme-option theme-colors clearfix">
                        <span> THEME COLOR </span>
                        <ul>
                            <li class="color-default current tooltips" data-style="default" data-container="body"
                                data-original-title="Default"></li>
                            <li class="color-darkblue tooltips" data-style="darkblue" data-container="body"
                                data-original-title="Dark Blue"></li>
                            <li class="color-blue tooltips" data-style="blue" data-container="body"
                                data-original-title="Blue"></li>
                            <li class="color-grey tooltips" data-style="grey" data-container="body"
                                data-original-title="Grey"></li>
                            <li class="color-light tooltips" data-style="light" data-container="body"
                                data-original-title="Light"></li>
                            <li class="color-light2 tooltips" data-style="light2" data-container="body" data-html="true"
                                data-original-title="Light 2"></li>
                        </ul>
                    </div>
                    <div class="theme-option">
                        <span> Layout </span>
                        <select class="layout-option form-control input-sm">
                            <option value="fluid" selected="selected">Fluid</option>
                            <option value="boxed">Boxed</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Header </span>
                        <select class="page-header-option form-control input-sm">
                            <option value="fixed" selected="selected">Fixed</option>
                            <option value="default">Default</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Top Menu Dropdown</span>
                        <select class="page-header-top-dropdown-style-option form-control input-sm">
                            <option value="light" selected="selected">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Sidebar Mode</span>
                        <select class="sidebar-option form-control input-sm">
                            <option value="fixed">Fixed</option>
                            <option value="default" selected="selected">Default</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Sidebar Menu </span>
                        <select class="sidebar-menu-option form-control input-sm">
                            <option value="accordion" selected="selected">Accordion</option>
                            <option value="hover">Hover</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Sidebar Style </span>
                        <select class="sidebar-style-option form-control input-sm">
                            <option value="default" selected="selected">Default</option>
                            <option value="light">Light</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Sidebar Position </span>
                        <select class="sidebar-pos-option form-control input-sm">
                            <option value="left" selected="selected">Left</option>
                            <option value="right">Right</option>
                        </select>
                    </div>
                    <div class="theme-option">
                        <span> Footer </span>
                        <select class="page-footer-option form-control input-sm">
                            <option value="fixed">Fixed</option>
                            <option value="default" selected="selected">Default</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- END THEME PANEL -->
            <!-- BEGIN PAGE BAR -->
            <div class="page-bar">
                <ul class="page-breadcrumb hide">
                    <li>
                        <a href="{{ url('/') }}">@lang('Home')</a>
                        <i class="fa fa-circle"></i>
                    </li>
                    <li>
                        <span class="page-title">{{ __(ucfirst($current)) }}</span>
                    </li>
                </ul>
                <div class="page-toolbar hide">
                    <div class="btn-group pull-right">
                        <button type="button" class="btn green btn-sm btn-outline dropdown-toggle"
                                data-toggle="dropdown"> Actions
                            <i class="fa fa-angle-down"></i>
                        </button>
                        <ul class="dropdown-menu pull-right" role="menu">
                            <li>
                                <a href="#">
                                    <i class="icon-bell"></i> Action</a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="icon-shield"></i> Another action</a>
                            </li>
                            <li>
                                <a href="#">
                                    <i class="icon-user"></i> Something else here</a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a href="#">
                                    <i class="icon-bag"></i> Separated link</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- END PAGE BAR -->

            <!-- END PAGE HEADER-->
            <div class="body-content" style="display: none">
                @yield('content')
            </div>
        </div>
        <!-- END CONTENT BODY -->
    </div>
    <!-- END CONTENT -->
    <!-- BEGIN QUICK SIDEBAR -->
    <a href="javascript:;" class="page-quick-sidebar-toggler">
        <i class="icon-login"></i>
    </a>
    <div class="page-quick-sidebar-wrapper" data-close-on-body-click="false">
        <div class="page-quick-sidebar">
            <ul class="nav nav-tabs">
                <li class="active">
                    <a href="javascript:;" data-target="#quick_sidebar_tab_1" data-toggle="tab"> Users
                        <span class="badge badge-danger">2</span>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" data-target="#quick_sidebar_tab_2" data-toggle="tab"> Alerts
                        <span class="badge badge-success">7</span>
                    </a>
                </li>
                <li class="dropdown">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"> More
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                <i class="icon-bell"></i> Alerts </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                <i class="icon-info"></i> Notifications </a>
                        </li>
                        <li>
                            <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                <i class="icon-speech"></i> Activities </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="javascript:;" data-target="#quick_sidebar_tab_3" data-toggle="tab">
                                <i class="icon-settings"></i> Settings </a>
                        </li>
                    </ul>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
                    <div class="page-quick-sidebar-chat-users" data-rail-color="#ddd"
                         data-wrapper-class="page-quick-sidebar-list">
                        <h3 class="list-heading">Staff</h3>
                        <ul class="media-list list-items">
                            <li class="media">
                                <div class="media-status">
                                    <span class="badge badge-success">8</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Bob Nilson</h4>
                                    <div class="media-heading-sub"> Project Manager</div>
                                </div>
                            </li>
                            <li class="media">

                                <div class="media-body">
                                    <h4 class="media-heading">Nick Larson</h4>
                                    <div class="media-heading-sub"> Art Director</div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-status">
                                    <span class="badge badge-danger">3</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Deon Hubert</h4>
                                    <div class="media-heading-sub"> CTO</div>
                                </div>
                            </li>
                            <li class="media">

                                <div class="media-body">
                                    <h4 class="media-heading">Ella Wong</h4>
                                    <div class="media-heading-sub"> CEO</div>
                                </div>
                            </li>
                        </ul>
                        <h3 class="list-heading">Customers</h3>
                        <ul class="media-list list-items">
                            <li class="media">
                                <div class="media-status">
                                    <span class="badge badge-warning">2</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Lara Kunis</h4>
                                    <div class="media-heading-sub"> CEO, Loop Inc</div>
                                    <div class="media-heading-small"> Last seen 03:10 AM</div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-status">
                                    <span class="label label-sm label-success">new</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Ernie Kyllonen</h4>
                                    <div class="media-heading-sub"> Project Manager,
                                        <br> SmartBizz PTL
                                    </div>
                                </div>
                            </li>
                            <li class="media">

                                <div class="media-body">
                                    <h4 class="media-heading">Lisa Stone</h4>
                                    <div class="media-heading-sub"> CTO, Keort Inc</div>
                                    <div class="media-heading-small"> Last seen 13:10 PM</div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-status">
                                    <span class="badge badge-success">7</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Deon Portalatin</h4>
                                    <div class="media-heading-sub"> CFO, H&D LTD</div>
                                </div>
                            </li>
                            <li class="media">

                                <div class="media-body">
                                    <h4 class="media-heading">Irina Savikova</h4>
                                    <div class="media-heading-sub"> CEO, Tizda Motors Inc</div>
                                </div>
                            </li>
                            <li class="media">
                                <div class="media-status">
                                    <span class="badge badge-danger">4</span>
                                </div>

                                <div class="media-body">
                                    <h4 class="media-heading">Maria Gomez</h4>
                                    <div class="media-heading-sub"> Manager, Infomatic Inc</div>
                                    <div class="media-heading-small"> Last seen 03:10 AM</div>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="page-quick-sidebar-item">
                        <div class="page-quick-sidebar-chat-user">
                            <div class="page-quick-sidebar-nav">
                                <a href="javascript:;" class="page-quick-sidebar-back-to-list">
                                    <i class="icon-arrow-left"></i>Back</a>
                            </div>
                            <div class="page-quick-sidebar-chat-user-messages">
                                <div class="post out">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Bob Nilson</a>
                                        <span class="datetime">20:15</span>
                                        <span class="body"> When could you send me the report ? </span>
                                    </div>
                                </div>
                                <div class="post in">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Ella Wong</a>
                                        <span class="datetime">20:15</span>
                                        <span class="body"> Its almost done. I will be sending it shortly </span>
                                    </div>
                                </div>
                                <div class="post out">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Bob Nilson</a>
                                        <span class="datetime">20:15</span>
                                        <span class="body"> Alright. Thanks! :) </span>
                                    </div>
                                </div>
                                <div class="post in">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Ella Wong</a>
                                        <span class="datetime">20:16</span>
                                        <span class="body"> You are most welcome. Sorry for the delay. </span>
                                    </div>
                                </div>
                                <div class="post out">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Bob Nilson</a>
                                        <span class="datetime">20:17</span>
                                        <span class="body"> No probs. Just take your time :) </span>
                                    </div>
                                </div>
                                <div class="post in">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Ella Wong</a>
                                        <span class="datetime">20:40</span>
                                        <span class="body"> Alright. I just emailed it to you. </span>
                                    </div>
                                </div>
                                <div class="post out">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Bob Nilson</a>
                                        <span class="datetime">20:17</span>
                                        <span class="body"> Great! Thanks. Will check it right away. </span>
                                    </div>
                                </div>
                                <div class="post in">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Ella Wong</a>
                                        <span class="datetime">20:40</span>
                                        <span class="body"> Please let me know if you have any comment. </span>
                                    </div>
                                </div>
                                <div class="post out">

                                    <div class="message">
                                        <span class="arrow"></span>
                                        <a href="javascript:;" class="name">Bob Nilson</a>
                                        <span class="datetime">20:17</span>
                                        <span class="body"> Sure. I will check and buzz you if anything needs to be corrected. </span>
                                    </div>
                                </div>
                            </div>
                            <div class="page-quick-sidebar-chat-user-form">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Type a message here...">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn green">
                                            <i class="icon-paper-clip"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tab-pane page-quick-sidebar-alerts" id="quick_sidebar_tab_2">
                    <div class="page-quick-sidebar-alerts-list">
                        <h3 class="list-heading">General</h3>
                        <ul class="feeds list-items">
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 4 pending tasks.
                                                <span class="label label-sm label-warning "> Take action
                                                            <i class="fa fa-share"></i>
                                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> Just now</div>
                                </div>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-success">
                                                    <i class="fa fa-bar-chart-o"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> Finance Report for year 2013 has been released.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 20 mins</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-danger">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 5 pending membership that requires a quick
                                                review.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 24 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> New order received with
                                                <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 30 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-success">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 5 pending membership that requires a quick
                                                review.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 24 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> Web server hardware needs to be upgraded.
                                                <span class="label label-sm label-warning"> Overdue </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 2 hours</div>
                                </div>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-default">
                                                    <i class="fa fa-briefcase"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> IPO Report for year 2013 has been released.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 20 mins</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                        <h3 class="list-heading">System</h3>
                        <ul class="feeds list-items">
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-check"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 4 pending tasks.
                                                <span class="label label-sm label-warning "> Take action
                                                            <i class="fa fa-share"></i>
                                                        </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> Just now</div>
                                </div>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-danger">
                                                    <i class="fa fa-bar-chart-o"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> Finance Report for year 2013 has been released.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 20 mins</div>
                                    </div>
                                </a>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-default">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 5 pending membership that requires a quick
                                                review.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 24 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-info">
                                                <i class="fa fa-shopping-cart"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> New order received with
                                                <span class="label label-sm label-success"> Reference Number: DR23923 </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 30 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-success">
                                                <i class="fa fa-user"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> You have 5 pending membership that requires a quick
                                                review.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 24 mins</div>
                                </div>
                            </li>
                            <li>
                                <div class="col1">
                                    <div class="cont">
                                        <div class="cont-col1">
                                            <div class="label label-sm label-warning">
                                                <i class="fa fa-bell-o"></i>
                                            </div>
                                        </div>
                                        <div class="cont-col2">
                                            <div class="desc"> Web server hardware needs to be upgraded.
                                                <span class="label label-sm label-default "> Overdue </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col2">
                                    <div class="date"> 2 hours</div>
                                </div>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <div class="col1">
                                        <div class="cont">
                                            <div class="cont-col1">
                                                <div class="label label-sm label-info">
                                                    <i class="fa fa-briefcase"></i>
                                                </div>
                                            </div>
                                            <div class="cont-col2">
                                                <div class="desc"> IPO Report for year 2013 has been released.</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col2">
                                        <div class="date"> 20 mins</div>
                                    </div>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tab-pane page-quick-sidebar-settings" id="quick_sidebar_tab_3">
                    <div class="page-quick-sidebar-settings-list">
                        <h3 class="list-heading">General Settings</h3>
                        <ul class="list-items borderless">
                            <li> Enable Notifications
                                <input type="checkbox" class="make-switch" checked data-size="small"
                                       data-on-color="success" data-on-text="ON" data-off-color="default"
                                       data-off-text="OFF"></li>
                            <li> Allow Tracking
                                <input type="checkbox" class="make-switch" data-size="small" data-on-color="info"
                                       data-on-text="ON" data-off-color="default" data-off-text="OFF"></li>
                            <li> Log Errors
                                <input type="checkbox" class="make-switch" checked data-size="small"
                                       data-on-color="danger" data-on-text="ON" data-off-color="default"
                                       data-off-text="OFF"></li>
                            <li> Auto Sumbit Issues
                                <input type="checkbox" class="make-switch" data-size="small" data-on-color="warning"
                                       data-on-text="ON" data-off-color="default" data-off-text="OFF"></li>
                            <li> Enable SMS Alerts
                                <input type="checkbox" class="make-switch" checked data-size="small"
                                       data-on-color="success" data-on-text="ON" data-off-color="default"
                                       data-off-text="OFF"></li>
                        </ul>
                        <h3 class="list-heading">System Settings</h3>
                        <ul class="list-items borderless">
                            <li> Security Level
                                <select class="form-control input-inline input-sm input-small">
                                    <option value="1">Normal</option>
                                    <option value="2" selected>Medium</option>
                                    <option value="e">High</option>
                                </select>
                            </li>
                            <li> Failed Email Attempts
                                <input class="form-control input-inline input-sm input-small" value="5"/></li>
                            <li> Secondary SMTP Port
                                <input class="form-control input-inline input-sm input-small" value="3560"/></li>
                            <li> Notify On System Error
                                <input type="checkbox" class="make-switch" checked data-size="small"
                                       data-on-color="danger" data-on-text="ON" data-off-color="default"
                                       data-off-text="OFF"></li>
                            <li> Notify On SMTP Error
                                <input type="checkbox" class="make-switch" checked data-size="small"
                                       data-on-color="warning" data-on-text="ON" data-off-color="default"
                                       data-off-text="OFF"></li>
                        </ul>
                        <div class="inner-content">
                            <button class="btn btn-success">
                                <i class="icon-settings"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END QUICK SIDEBAR -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->

<template id="animated-loading">
    <div class="col-md-12 text-center">
        <img class="" alt="@lang('Loading')..." src="{{ asset('img/loading.gif') }}">
    </div>
</template>

<div class="page-footer">
    <div class="page-footer-inner col-md-12 text-center" style="width: 100%"> <b>2020</b> <i class="fa fa-rocket"></i> PCW @
        <a href="http://pcwtecnologia.com" title="PCW Tecnología" style="color: #419368" target="_blank">tecnologia.com</a>
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
@include('template.metronic.plugins')
</body>

</html>