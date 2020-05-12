<div class="page-header navbar navbar-fixed-top map-header-bg">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner ">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="{{ url('/')  }}" class="m-t-5 m-r-5 text-center animated">
                <span class="text-white f-s-16">PCW</span><span class="text-success f-s-22">SMS</span>
            </a>
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
                <li class="dropdown tooltips" data-title="@lang('Dispatch report')" data-placement="bottom">
                    <a href="{{ route('report-dispatch')  }}" class="dropdown-toggle p-t-10">
                        <i class="fa fa-paper-plane faa-vertical m-r-10"></i>
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
                            {{ Auth::user()->name }}
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
                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>