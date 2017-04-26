<!-- begin #sidebar -->
<div id="sidebar" class="sidebar">
    <!-- begin sidebar scrollbar -->
    <div data-scrollbar="true" data-height="100%">
        <!-- begin sidebar nav -->
        <ul class="nav">
            <li class="nav-user">
                <div class="image">
                    <img src="assets/img/logo.png" alt="" />
                </div>
                <div class="info">
                    <div class="name dropdown">
                        <a href="javascript:;" data-toggle="dropdown">Usuario NE <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="javascript:;">Editar Perfil</a></li>
                            <li><a href="javascript:;"><span class="badge badge-danger pull-right">2</span> Inbox</a></li>
                            <li><a href="javascript:;">Calendario</a></li>
                            <li><a href="javascript:;">Configuración</a></li>
                            <li class="divider"></li>
                            <li><a href="http://www.pcwserviciosgps.com/">Cerrar Sesión</a></li>
                        </ul>
                    </div>
                    <div class="position">Admin/Soporte PCW</div>
                </div>
            </li>
            <li class="nav-header">MENÚ</li>
            <li class="">
                <a href="http://www.pcwserviciosgps.com/pcw_gps/php/inicio.php" class="faa-parent animated-hover">
                    <i class="fa fa-home faa-pulse"></i>
                    <span>@lang('Home')</span>
                </a>
            </li>
            <li class="active">
                <a href="" class="faa-parent animated-hover">
                    <i class="fa fa-area-chart faa-pulse"></i>
                    <span>@lang('Reports')</span>
                </a>
                <ul class="sub-menu" style="display: block;">
                    <li class="has-sub">
                        <a href="javascript:;">
                            <b class="caret pull-right"></b>
                            @lang('Route')
                        </a>
                        <ul class="sub-menu" style="display: block;">
                            <li class="has-sub active">
                                <a href="javascript:;">
                                    @lang('Route reports')
                                </a>
                            </li>
                        </ul>
                    </li>
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
                                <div class="progress-bar progress-bar-success" style="width: 70%"></div>
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
                                <div class="progress-bar progress-bar-danger" style="width: 10%"></div>
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