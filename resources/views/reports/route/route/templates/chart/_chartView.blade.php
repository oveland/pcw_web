<div class="modal-header" style="width: 100%">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
        <i class="fa fa-times"></i>
    </button>
    <div class="">
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
        <div class="col-md-2 col-sm-4 col-xs-6 p-0 p-t-15" style="float: left !important;position: absolute;z-index: 1;">
            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">

                <div class="dashboard-stat blue hide">
                    <div class="visual">
                        <i class="fa fa-bus"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <span data-counter="counterup" data-value="1349">1349</span>
                        </div>
                        <div class="desc"> New Feedbacks </div>
                    </div>
                    <a class="more" href="javascript:;"> View more
                        <i class="m-icon-swapright m-icon-white"></i>
                    </a>
                </div>

                <div class="dashboard-stat2 blue">
                    <div class="display">
                        <div class="number">
                            <small>@lang('Vehicle')</small>
                            <h3 class="font-green-sharp">
                                <span data-counter="counterup" data-value="7800">
                                    <div class="widget-stat-number modal-report-vehicle report-info"></div>
                                </span>
                                <small class="font-green-sharp">

                                </small>
                            </h3>
                        </div>
                        <div class="icon">
                            <i class="fa fa-bus"></i>
                        </div>
                    </div>
                    <div class="progress-info">
                        <div class="progress progress-striped progress-xs active green-sharp">
                            <div class="progress-bar progress-success modal-report-vehicle-speed-progress report-info" style="width: 50%"></div>
                        </div>

                        <div class="status">
                            <div class="status-title">
                                <i class="fa fa-tachometer" aria-hidden="true"></i> @lang('Speed')<br>
                                <i class="fa fa-tachometer" aria-hidden="true"></i> @lang('Average')
                            </div>
                            <div class="status-number">
                                <span class="modal-report-vehicle-speed report-info"></span><br>
                                <span class="modal-report-vehicle-speed-average report-info"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- begin widget -->

                <!-- end widget -->
            </div>

            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin widget -->
                <div class="widget-stat-chart widget-stat-chart-green text-white">
                    <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                    <div class="widget-stat-info m-0 row">
                        <div class="col-md-12 no-padding">
                            <i class="fa fa-flag"></i>
                            <span class="widget-stat-number modal-report-dispatch-route-name report-info"></span>
                            <small class="modal-report-dispatch-status report-info pull-right"></small>
                        </div>
                        <div class="col-md-12 no-padding">
                            <hr class="hr col-md-12 no-padding">
                        </div>
                        <small class="col-md-12 no-padding">
                            <i class="fa fa-list-ol" aria-hidden="true"></i> @lang('Turn')
                            <span class="modal-report-dispatch-turn report-info pull-right"></span>
                        </small>
                        <small class="col-md-12 no-padding">
                            <i class="fa fa-retweet" aria-hidden="true"></i> @lang('Round trip')
                            <span class="modal-report-dispatch-round-trip report-info pull-right"></span>
                        </small>
                        <small class="col-md-12 no-padding">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('Dispatched')
                            <span class="modal-report-dispatch-departure-time report-info pull-right"></span>
                        </small>
                        <small class="col-md-12 no-padding">
                            <i class="fa fa-clock-o" aria-hidden="true"></i> @lang('Arrived')
                            <span class="modal-report-dispatch-arrival-time report-info pull-right"></span>
                        </small>
                        <small class="col-md-12 no-padding">
                            <i class="ion-android-stopwatch" aria-hidden="true"></i> @lang('In route')
                            <span class="modal-report-dispatch-route-time report-info pull-right"></span>
                        </small>
                    </div>
                    <div class="widget-stat-progress">
                        <div class="progress progress-striped progress-xs active">
                            <div class="progress-bar progress-bar-lime modal-report-route-percent-progress report-info" style="width: 50%"></div>
                        </div>
                    </div>
                    <div class="widget-stat-footer text-left">
                        <small class="no-padding">
                            <i class="fa fa-flag-checkered" aria-hidden="true"></i>
                            <span class="modal-report-route-percent"></span>% @lang('of the route')
                        </small>
                    </div>
                </div>
                <!-- end widget -->
            </div>

            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin widget -->
                <div class="widget-stat-chart widget-stat-chart-blue text-white">
                    <div class="widget-stat-btn"><a href="javascript:;" class="hide" data-click="widget-reload"><i class="fa fa-repeat"></i></a></div>
                    <div class="widget-stat-info m-0">
                        <div class="widget-stat-title text-uppercase text-bold">
                            <i class="fa fa-user"></i> @lang('Driver')
                        </div>
                        <div class="col-md-12 no-padding">
                            <hr class="hr col-md-12 no-padding">
                        </div>
                        <small class="modal-report-driver report-info text-capitalize"></small>
                    </div>
                </div>
                <!-- end widget -->
            </div>
        </div>

        <div class="col-md-2 col-sm-4 col-xs-6 p-0 p-t-15" style="float: right !important;position: absolute;z-index: 1;right: 5px; top: 100px;">
            <div class="col-md-12 col-sm-12 col-xs-10" style="opacity: 0.92">
                <!-- begin panel off roads -->
                <div class="panel panel-danger panel-off-road">
                    <div class="panel-heading p-10">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-white" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                        </div>
                        <h5 class="panel-title text-bold">
                            <i class="fa fa-random"></i> @lang('Off road')
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-td-valign-middle">
                            <thead class="off-road">
                            <tr class="danger">
                                <th class="text-center p-10" width="40%">
                                    <small>
                                        <i class="fa fa-clock-o f-s-5"></i> @lang('Time')
                                    </small>
                                </th>
                                <th class="text-center p-10" width="60%">
                                    <small>
                                        <i class="fa fa-rocket f-s-5"></i> @lang('Actions')
                                    </small>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="off-road"></tbody>
                            <tfoot class="no-off-road">
                            <tr>
                                <td colspan="4">
                                    <p class="text-center">
                                        <i class="fa fa-check-circle-o text-lime fa-2x"></i><br>
                                        @lang("The vehicle haven't off road")
                                    </p>
                                </td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- end panel off roads -->
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

<template id="template-button-off-road">
    <div class="btn-group m-b-5 m-r-5">
        <a href="javascript:;" data-latitude="" data-longitude="" class="btn btn-warning btn-xs btn-see-off-road">
            <i class="fa fa-map-marker"></i>
        </a>

        @if( Auth::user()->isAdmin() )
            <a href="javascript:;" data-toggle="dropdown" class="btn btn-xs btn-danger dropdown-toggle" aria-expanded="false">
                <span class="caret"></span>
            </a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <a href="{{ route('report-route-off-road-is-fake', ['location' => '']) }}" class="text-danger btn-fake-off-road">
                        <i class="fa fa-thumbs-down"></i> @lang('Mark as fake')
                    </a>
                </li>
            </ul>
        @endif
    </div>
</template>