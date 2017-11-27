<!-- begin #sidebar -->
<div id="sidebar" class="sidebar">
    <!-- begin sidebar scrollbar -->
    <div data-scrollbar="true" data-height="100%">
        <!-- begin sidebar nav -->
        <ul class="nav">
            <li class="nav-user">
                <div class="image">
                    <img src="{{asset('assets/img/logo.png')}}" alt="" />
                </div>
                <div class="info">
                    <div class="name dropdown">
                        <a href="javascript:;" data-toggle="dropdown" class="text-capitalize">{{ Auth::user()->name }}<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:;">Editar Perfil</a></li>
                            <li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Inbox</a></li>
                            <li><a href="javascript:;">Calendario</a></li>
                            <li><a href="javascript:;">Configuración</a></li>
                            <li class="divider"></li>
                            <li>
                                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    @lang('Logout')
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hide">
                                    {{ csrf_field() }}
                                </form>
                            </li>
                        </ul>
                    </div>
                    <div class="position">{!! Auth::user()->company?Auth::user()->company->name:__('Unasigned') !!}</div>
                </div>
            </li>
            <li class="nav-header">MENÚ</li>
            <li class="">
                <a href="http://www.pcwserviciosgps.com/pcw_gps/php/inicio.php" target="_blank" class="faa-parent animated-hover">
                    <i class="fa fa-home faa-pulse"></i>
                    <span>@lang('Home')</span>
                </a>
            </li>
            <li class="has-sub active">
                <a href="javascritp:;" class="faa-parent animated-hover">
                    <i class="fa fa-area-chart faa-pulse"></i>
                    <span>@lang('Reports')</span>
                </a>
                <ul class="sub-menu">
                    <li class="has-sub menu-routes">
                        <a href="javascript:;" class="faa-parent animated-hover">
                            <b class="caret pull-right"></b>
                            <i class="fa fa-flag faa-pulse"></i>
                            @lang('Route')
                        </a>
                        <ul class="sub-menu">
                            <li class="has-sub menu-route-report">
                                <a href="{{ route('route-report')  }}">
                                    <i class="fa fa-line-chart" aria-hidden="true"></i>
                                    @lang('Route report')
                                </a>
                            </li>
                            <li class="has-sub menu-off-road-report">
                                <a href="{{ route('off-road-report')  }}">
                                    <i class="fa fa-road" aria-hidden="true"></i>
                                    @lang('Off road')
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="has-sub menu-report-vehicles">
                        <a href="javascript:;" class="faa-parent animated-hover">
                            <b class="caret pull-right"></b>
                            <i class="fa fa-bus faa-pulse"></i>
                            @lang('Vehicles')
                        </a>
                        <ul class="sub-menu">
                            <li class="has-sub menu-report-vehicles-parked">
                                <a href="{{ route('report-vehicle-parked')  }}">
                                    <i class="fa fa-product-hunt" aria-hidden="true"></i>
                                    @lang('Parked Report')
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="has-sub menu-passengers">
                        <a href="javascript:;" class="faa-parent animated-hover">
                            <b class="caret pull-right"></b>
                            <i class="fa fa-users faa-pulse"></i>
                            @lang('Passengers')
                        </a>
                        <ul class="sub-menu">
                            <li class="has-sub menu-passengers-consolidated">
                                <a href="javascript:;" class="faa-parent animated-hover">
                                    <b class="caret pull-right"></b>
                                    <i class="fa fa-archive faa-pulse"></i>
                                    @lang('Consolidated')
                                </a>
                                <ul class="sub-menu">
                                    <li class="has-sub menu-passengers-consolidated-days">
                                        <a href="{{ route('passengers-consolidated-report-days')  }}">
                                            <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                                            @lang('Daily')
                                        </a>
                                    </li>
                                    <li class="has-sub menu-passengers-consolidated-range">
                                        <a href="{{ route('passengers-consolidated-report-range')  }}">
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            @lang('Date range')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="has-sub menu-passengers-detailed">
                                <a href="javascript:;" class="faa-parent animated-hover">
                                    <b class="caret pull-right"></b>
                                    <i class="fa fa-list-alt faa-pulse"></i>
                                    @lang('Detailed')
                                </a>
                                <ul class="sub-menu">
                                    <li class="has-sub menu-passengers-detailed-days">
                                        <a href="{{ route('passengers-detailed-report-days')  }}">
                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                            @lang('Daily')
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            @if( Auth::user()->isAdmin() ||  Auth::user()->belongsToTaxcentral() )
                                <li class="has-sub menu-passengers-taxcentral">
                                    <a href="javascript:;" class="faa-parent animated-hover">
                                        <b class="caret pull-right"></b>
                                        <i class="fa fa-building faa-pulse"></i>
                                        @lang('Taxcentral')
                                    </a>
                                    <ul class="sub-menu">
                                        <li class="has-sub menu-passengers-taxcentral">
                                            <a href="{{ route('tc-passengers-report')  }}">
                                                <i class="fa fa-user-circle-o" aria-hidden="true"></i>
                                                @lang('Passengers report')
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            @endif
                        </ul>
                    </li>
                    @if( Auth::user()->isAdmin() )
                    <li class="has-sub menu-logs">
                        <a href="javascript:;" class="faa-parent animated-hover">
                            <b class="caret pull-right"></b>
                            <i class="fa fa-user faa-pulse"></i>
                            @lang('Users')
                        </a>
                        <ul class="sub-menu">
                            <li class="has-sub menu-logs">
                                <a href="{{ route('logs-access')  }}">
                                    <i class="fa fa-sign-in" aria-hidden="true"></i>
                                    @lang('Access log')
                                </a>
                            </li>
                        </ul>
                    </li>
                    @endif
                </ul>
            </li>

            <br>
            <li class="divider has-minify-btn">
                <!-- begin sidebar minify button -->
                <a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify">
                    <i class="fa fa-angle-left"></i>
                </a>
                <!-- end sidebar minify button -->
            </li>
            <li class="nav-header">Projects</li>
            <li class="nav-project">
                <ul class="project-list">
                    <li>
                        <div class="icon"><i class="fa fa-circle-o text-success"></i></div>
                        <div class="info">
                            <div class="title"><a href="javascript:;">@lang('Manage <b>New Strategy</b>')</a></div>
                            <div class="progress progress-striped m-b-10 active">
                                <div class="progress-bar progress-bar-success" style="width: 90%"></div>
                            </div>
                            <div class="desc">
                                Monitoreo <b>contínuo</b> del estado del vehículo, a nivel de distancias y tiempos estimados,
                                durante todo el trayecto de la ruta
                            </div>
                        </div>
                    </li>
                    <li>
                        <div class="icon"><i class="fa fa-circle-o text-danger"></i></div>
                        <div class="info">
                            <div class="title">
                                <a href="javascript:;">Migración a nueva plataforma</a>
                            </div>
                            <div class="progress progress-striped m-b-10 active">
                                <div class="progress-bar progress-bar-danger" style="width: 50%"></div>
                            </div>
                            <div class="desc">
                                Mejoras e integración de nuevas funcionalidades
                            </div>
                        </div>
                    </li>
                </ul>
            </li>
        </ul>
        <!-- end sidebar nav -->
    </div>
    <!-- end sidebar scrollbar -->
</div>
<div class="sidebar-bg"></div>
<!-- end #sidebar -->