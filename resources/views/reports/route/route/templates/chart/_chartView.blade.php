<div class="modal-header" style="width: 100%">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-times"></i>
    </button>
    <div class="row">
        <h4 class="modal-title">
            <i class="fa fa-area-chart"></i> @lang('Historic route time chart'): <span id="date-report-details"></span>
        </h4>
        <div class="col-md-12 p-5">
            <div id="chart-route-report" style="height: 80px"></div>
        </div>
    </div>
</div>
<div class="modal-body" style="width:100%;">
    <h4 class="info-control-points">
        <span class="pull-right"><img src="{{ asset('img/control-point-1.png') }}"> @lang('Control point return')</span>                        &nbsp;&nbsp;
        <span class="pull-right p-r-20"><img src="{{ asset('img/control-point-0.png') }}"> @lang('Control point going')</span>
        <a href="" class="btn-primary btn btn-show-off-road-report pull-right hide">
            <i class="ion-merge m-r-5 fa-fw"></i> @lang('See off road report')
        </a>
    </h4>
    <div class="row">
        <div id="info-route" class="col-md-3 col-sm-4 col-xs-6 p-0 p-t-15" style="float: left !important;position: absolute;z-index: 1;">
            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin widget -->
                <div class="widget widget-stat widget-stat-right bg-inverse text-white">
                    <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                    <div class="widget-stat-info m-0">
                        <div class="widget-stat-title">
                            <i class="fa fa-bus"></i> @lang('Vehicle')
                        </div>
                        <div class="widget-stat-number modal-report-vehicle report-info"></div>
                    </div>
                    <div class="widget-stat-progress">
                        <div class="progress progress-striped progress-xs active">
                            <div class="progress-bar progress-success modal-report-vehicle-speed-progress report-info" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="widget-stat-footer text-left">
                        <i class="fa fa-tachometer" aria-hidden="true"></i> <span class="modal-report-vehicle-speed report-info"></span><br>
                        <i class="fa fa-tachometer" aria-hidden="true"></i> <span class="modal-report-vehicle-speed-average report-info"></span>
                    </div>
                </div>
                <!-- end widget -->
            </div>

            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin widget -->
                <div class="widget widget-stat widget-stat-right bg-success-dark text-white">
                    <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                    <div class="widget-stat-info m-0 row">
                        <div class="widget-stat-title">
                            <i class="fa fa-flag"></i> @lang('Route info')
                        </div>
                        <div class="widget-stat-number modal-report-dispatch-route-name report-info"></div>
                        <div class="col-md-12 no-padding">
                            <hr class="hr col-md-12 no-padding">
                        </div>
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-list-ol" aria-hidden="true"></i> @lang('Turn')
                            <span class="modal-report-dispatch-turn report-info"></span>
                        </div>
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-retweet" aria-hidden="true"></i> @lang('Round trip')
                            <span class="modal-report-dispatch-round-trip report-info"></span>
                        </div>
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('Departure time')
                            <span class="modal-report-dispatch-departure-time report-info"></span>
                        </div>
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('Arrival time')
                            <span class="modal-report-dispatch-arrival-time report-info"></span>
                        </div>
                        <div class="col-md-12 no-padding">
                            <i class="ion-android-stopwatch" aria-hidden="true"></i> @lang('Route time')
                            <span class="modal-report-dispatch-route-time report-info"></span>
                        </div>
                    </div>
                    <div class="widget-stat-progress">
                        <div class="progress progress-striped progress-xs active">
                            <div class="progress-bar progress-bar-lime modal-report-route-percent-progress report-info" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="widget-stat-footer text-left row">
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                            <span class="modal-report-route-percent"></span>% @lang('of the route'). <span class="modal-report-dispatch-status report-info"></span>
                        </div>
                    </div>
                </div>
                <!-- end widget -->
            </div>

            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin widget -->
                <div class="widget widget-stat widget-stat-right bg-info text-white">
                    <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                    <div class="widget-stat-info m-0">
                        <div class="widget-stat-title">
                            <i class="fa fa-user"></i> @lang('Driver')
                        </div>
                        <div class="widget-stat-number modal-report-driver report-info"></div>
                    </div>
                </div>
                <!-- end widget -->
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 p-0">
            <div id="google-map-light-dream"></div>
        </div>
    </div>
</div>
<div class="modal-footer hide" style="width:90%;">
    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
</div>