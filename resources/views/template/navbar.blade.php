<style>
    .map-header-bg{
        background: url({{ asset('img/map-bg.jpg') }}) top center fixed !important;
    }
</style>
<!-- begin #header -->
<div id="header" class="header navbar navbar-fixed-top navbar-inverse map-header-bg">
    <!-- begin container-fluid -->
    <div class="container-fluid transparent">
        <!-- begin mobile sidebar expand / collapse button -->
        <div class="navbar-header" style="width: 50%">
            <a href="{{ url('/') }}" class="navbar-brand" style="text-transform: uppercase; font-weight: normal">
                PCW <i class="ion-ios-pulse" style="font-size: 150%"></i> <span class="hidden-sm hidden-xs">Sistema de monitoreo satelital</span><span class="hidden-md hidden-lg">SMS</span>
            </a>
            <button type="button" class="navbar-toggle" data-click="sidebar-toggled">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <!-- end mobile sidebar expand / collapse button -->

        <!-- begin navbar-right -->
        <ul class="nav navbar-nav navbar-right">
            <li class="hide">
                <form class="navbar-form form-input-flat">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Quiero buscar..."/>
                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </li>
            <li class="dropdown">
                <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle" data-click="toggle-notify">
                    <i class="fa fa-bell"></i>
                    <span class="badge badge-danger pull-right">2</span>
                </a>
                <ul class="dropdown-menu dropdown-notification pull-right">
                    <li class="dropdown-header">Notificaciones <span class="badge badge-danger pull-right">2</span></li>
                    <li class="notification-item">
                        <a href="{{ route('admin-vehicles-peak-and-plate') }}">
                            <div class="media"><i class="fa fa-car"></i></div>
                            <div class="message">
                                <h6 class="title">Nueva Interfaz de Administración</h6>
                                <div class="time">
                                    @lang('Peak and Plate')
                                </div>
                            </div>
                            <div class="option" data-toggle="tooltip" data-title="El 30 de Diciembre"
                                 data-click="set-message-status" data-status="unread" data-container="body">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </a>
                    </li>
                    <li class="notification-item">
                        <a href="{{ route('passengers-detailed-report-range') }}">
                            <div class="media"><i class="fa fa-users"></i></div>
                            <div class="message">
                                <h6 class="title">Nuevo Reporte de Pasajeros</h6>
                                <div class="time">
                                    @lang('Detailed per date range')
                                </div>
                            </div>
                            <div class="option" data-toggle="tooltip" data-title="El 27 de Diciembre"
                                 data-click="set-message-status" data-status="unread" data-container="body">
                                <i class="fa fa-calendar"></i>
                            </div>
                        </a>
                    </li>
                    <!-- more notification item -->
                    <li class="dropdown-footer text-center hide">
                        <a href="javascript:;">Aquí aparecerán las notificaciones de nuevos reportes</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown navbar-user">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="logo">
                        <i class="fa fa-user"></i>
                    </span>
                    <span class="hidden-xs text-capitalize">{{ Auth::user()->name }}</span> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li class="divider"></li>
                    <li>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fa fa-logout"></i> @lang('Logout')
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hide">
                            {{ csrf_field() }}
                        </form>
                    </li>
                </ul>
            </li>
            <li class="hide">
                <a href="javascript:;" data-click="right-sidebar-toggled">
                    <i class="fa fa-align-left"></i>
                </a>
            </li>
        </ul>
        <!-- end navbar-right -->
    </div>
    <!-- end container-fluid -->
</div>
<!-- end #header -->