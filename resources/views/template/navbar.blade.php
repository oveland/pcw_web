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
            <a href="index.html" class="navbar-brand" style="text-transform: uppercase; font-weight: normal">
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
            <li>
                <form class="navbar-form form-input-flat">
                    <div class="form-group">
                        <input type="text" class="form-control" placeholder="Quiero buscar..."/>
                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </div>
                </form>
            </li>
            <li class="dropdown">
                <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle has-notify"
                   data-click="toggle-notify">
                    <i class="fa fa-bell"></i>
                </a>
                <ul class="dropdown-menu dropdown-notification pull-right">
                    <li class="dropdown-header">Notificaciones (1)</li>
                    <li class="notification-item">
                        <a href="javascript:;">
                            <div class="media"><i class="fa fa-exclamation-triangle"></i></div>
                            <div class="message">
                                <h6 class="title">Nuevo Reporte de Ruta</h6>
                                <div class="time">hace 5 min</div>
                            </div>
                            <div class="option" data-toggle="tooltip" data-title="Mark as Read"
                                 data-click="set-message-status" data-status="unread" data-container="body">
                                <i class="fa fa-circle-o"></i>
                            </div>
                        </a>
                    </li>
                    <!-- more notification item -->
                    <li class="dropdown-footer text-center">
                        <a href="javascript:;">Aquí aparecerán las notificaciones de nuevos reportes</a>
                    </li>
                </ul>
            </li>
            <li class="dropdown navbar-user">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="image"><img src="{{asset('assets/img/logo.png')}}" alt=""/></span>
                    <span class="hidden-xs text-capitalize">{{ Auth::user()->name }}</span> <b class="caret"></b>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li><a href="javascript:;">Editar perfil</a></li>
                    <li>
                        <a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Inbox</a>
                    </li>
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