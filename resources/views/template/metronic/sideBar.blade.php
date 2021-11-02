@php
    $baseMenu = $baseMenu ?? '';
@endphp

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

            <li class="nav-item start">
                <a href="http://www.pcwserviciosgps.com" target="_blank" class="faa-parent animated-hover nav-link">
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

            @if( Auth::user())
                @if( Auth::user()->canAdmin())
                    <li class="nav-item start {{ $baseMenu == __('url-administration') ? 'active-animated' : '' }}">
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
                            @if( Auth::user()->canAdminGPS() )
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

                <li class="nav-item {{ $baseMenu == __('url-operation') ? 'active-animated' : '' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-life-ring faa-spin"></i>
                        <span>@lang('Operation')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-operation-dispatch">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-automobile faa-passing"></i>
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

                <li class="nav-item {{ $baseMenu == __('reports') ? 'active-animated' : '' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-area-chart faa-horizontal"></i>
                        <span>@lang('Reports')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-apps hide">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-bookmark faa-vertical"></i>
                                @lang('Apps')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-apps-report">
                                    <a href="{{ route('report.app')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-location-arrow faa-vertical" aria-hidden="true"></i>
                                        @lang('Report')
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li class="nav-item menu-routes">
                            <a href="javascript:;" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-flag faa-vertical"></i>
                                @lang('Route')
                                <span class="arrow"></span>
                            </a>
                            <ul class="sub-menu">
                                <li class="nav-item menu-route-report">
                                    <a href="{{ route('report-route')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="fa fa-paper-plane faa-vertical" aria-hidden="true"></i>
                                        @lang('Dispatch')
                                    </a>
                                </li>
                                <li class="nav-item menu-routes-takings">
                                    <a href="{{ route('reports.routes.takings.index')  }}" class="faa-parent animated-hover nav-link">
                                        <i class="icon-briefcase faa-ring" style="margin-right: 0; margin-left: 0px"></i>
                                        @lang('Takings')
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
                                @if( Auth::user()->isAdmin() )
                                    <li class="nav-item menu-report-vehicles-status">
                                        <a href="{{ route('report-vehicle-status')  }}" class="faa-parent animated-hover nav-link">
                                            <i class="fa fa-podcast blue faa-burst" aria-hidden="true"></i>
                                            @lang('Status')
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
                                <li class="nav-item menu-passengers-photos">
                                    <a href="{{ route('report.passengers.photos') }}" class="faa-parent animated-hover nav-link nav-toggle">
                                        <i class="fa fa-camera faa-vertical"></i>
                                        @lang('Video')
                                    </a>
                                </li>

                                @if( Auth::user()->isAdmin() ||  Auth::user()->company->hasRecorderCounter() )
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

                                @if( Auth::user()->isAdmin() ||  Auth::user()->company->hasSeatSensorCounter() )
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
                                            <li class="has-sub menu-passengers-sensors-cameras">
                                                <a href="{{ route('report-passengers-sensors-cameras') }}" class="faa-parent animated-hover">
                                                    <i class="fa fa-camera faa-vertical"></i>
                                                    @lang('Cameras')
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endif

                                @if( Auth::user()->isAdmin() )
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

                                @if( Auth::user()->isAdmin() ||  Auth::user()->belongsToTaxcentral() )
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

                        @if( Auth::user()->isAdmin() )
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

                        <li class="nav-item menu-logs">
                            <a href="{{ route('reports.liquidation.index') }}" class="faa-parent animated-hover">
                                <i class="fa fa-dollar faa-vertical"></i>
                                @lang('Liquidation')
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item {{ $baseMenu == __('takings') ? 'active-animated' : '' }}">
                    <a href="#" class="faa-parent animated-hover nav-link nav-toggle">
                        <i class="fa fa-dollar faa-horizontal"></i>
                        <span>@lang('Takings')</span>
                        <span class="arrow"></span>
                    </a>
                    <ul class="sub-menu">
                        <li class="nav-item menu-takings-passengers">
                            <a href="{{ route('takings-passengers-liquidation')  }}" class="faa-parent animated-hover nav-link nav-toggle">
                                <i class="fa fa-users faa-vertical"></i>
                                @lang('Passengers')
                            </a>
                        </li>
                    </ul>
                </li>
            @endif
        </ul>

        <!-- END SIDEBAR MENU -->
        <!-- END SIDEBAR MENU -->
    </div>
    <!-- END SIDEBAR -->
</div>
<!-- END SIDEBAR -->