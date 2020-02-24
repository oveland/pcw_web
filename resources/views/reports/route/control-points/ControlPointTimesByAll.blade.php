@if(count($reportsByControlPoints))
    <style>
        .measured-time {
            font-size: 0.6em;
        }

        .measured-time-dark {
            color: white;
            font-weight: bold;
            text-shadow: #111111 1px 1px 4px;
        }
    </style>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-control-points-search-report') }}?export=true&type-report={{ $query->typeReport }}&date-report={{ $query->dateReport }}&company-report={{ $query->company->id }}&route-report={{ $query->route->id }}" class="btn btn-lime btn-rounded bg-lime-dark btn-sm tooltips"
                   data-title="@lang('Export excel')" data-placement="bottom">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>

            <h5 class="text-white m-t-10">
                <span class="hides text-uppercase">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    @lang('Control point time report')
                    <hr class="text-inverse-light">
                </span>
            </h5>
        </div>

        <div class="tab-content panel p-0">
            <div id="report-tab" class="tab-pane fade active in report-tab-cp">
                <div class="">
                    <div class="table-responsive col-md-12 p-0" style="padding-bottom: 90px;height: 1000px">
                        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report-control-point data-table-report">
                            <thead>
                            <tr class="">
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-retweet"></i>
                                    @lang('Round Trip')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-list"></i>
                                    @lang('Turn')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-car"></i>
                                    @lang('Vehicle')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-user"></i>
                                    @lang('Driver')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="ion-android-stopwatch"></i><br>
                                    @lang('In route')
                                </th>
                                @php($controlPoints =  $route->controlPoints)
                                @foreach($controlPoints as $controlPoint)
                                    <th class="{{ $controlPoint->trajectory == 0 ? 'success':'warning' }}" style="">
                                        <div style="display: inline-block;vertical-align: middle;float: none;">
                                            <span>{{ $controlPoint->name }}</span>
                                            <br><br>
                                            <small>
                                                <i style="font-size: 100% !important;" class="fa fa-map-marker"></i> <strong class="control-point-distance">{{ $controlPoint->distance_from_dispatch }} m</strong>
                                            </small>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="bg-inverse-dark text-muted">
                                    <i class="fa fa-rocket"></i><br>
                                    @lang('Actions')
                                </th>
                            </tr>
                            </thead>
                            <tbody>

                            @php
                                $strTime = new \App\Http\Controllers\Utils\StrTime();
                                $averageRouteTime = '00:00:00';
                            @endphp

                            @foreach( $reportsByControlPoints as $reportsByControlPoint )
                                @php
                                    $dispatchRegister = $reportsByControlPoint->dispatchRegister;
                                    $offRoadPercent = $dispatchRegister->getOffRoadPercent();
                                    $vehicle = $reportsByControlPoint->vehicle;
                                    $driver = $reportsByControlPoint->driver;

                                    $averageRouteTime = $strTime::addStrTime($averageRouteTime, $dispatchRegister->getRouteTime(true))
                                @endphp
                                <tr class="">
                                    <th class="text-capitalize text-muted bg-{{ $dispatchRegister->inProgress() ? 'warning':'inverse' }}">
                                        {{ $dispatchRegister->round_trip }}<br>
                                        <small class="status-html">{!! $dispatchRegister->status !!}</small>
                                        @if($offRoadPercent)
                                            <div class="m-t-1">
                                                <label class="label label-{{ $offRoadPercent < 5 ? 'success': ($offRoadPercent < 50 ? 'warning': 'danger') }} tooltips" data-placement="bottom" title="@lang('Percent in off road')">
                                                    {{ number_format($offRoadPercent, 1,'.', '') }}% <i class="fa fa-random faa-passing animated" style="font-size: inherit !important;"></i>
                                                </label>
                                            </div>
                                        @endif
                                    </th>
                                    <th class="text-capitalize text-muted bg-inverse">
                                        {{ $dispatchRegister->turn }}
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        {{ $vehicle->number }}
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        {{ $driver?$driver->fullName():__('Not assigned') }}
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        @if( $dispatchRegister->complete() )
                                            {{ $dispatchRegister->departure_time }}
                                            <br>{{ $dispatchRegister->arrival_time }}
                                            <hr class="m-1">
                                            {{ $dispatchRegister->getRouteTime() }}
                                        @else
                                            {{ '--:--:--' }}
                                        @endif
                                    </th>
                                    @foreach($reportsByControlPoint->reportsByControlPoint as $reportByControlPoint)
                                        @php( $controlPoint = $reportByControlPoint->controlPoint )
                                        @if( $reportByControlPoint->hasReport )
                                            <td class="text-center td-info" style="background: {{  $query->paintProfile ? $reportByControlPoint->backgroundProfile : "white" }};">
                                                <div class="tooltipss" data-title="{{ $controlPoint->name }}">
                                                    <i class="fa fa-bus f-s-15 icon-vehicle-status text-{{ $reportByControlPoint->statusColor }}" style="{{  $query->paintProfile ? "" : "font-size: 1.5em !important;color: $reportByControlPoint->backgroundProfile !important" }}"></i>
                                                    <br>
                                                    <button type="button" class="f-s-12 m-t-5 btn btn-{{ $reportByControlPoint->statusColor }} light btn-xs" style="{{  $query->paintProfile ? "" : "background: $reportByControlPoint->backgroundProfile !important" }}"
                                                            data-placement="bottom" data-toggle="popover" data-html="true" data-trigger="click"
                                                            title="
                                                                <strong>
                                                                    <i class='fa fa-map-marker text-muted'></i>  {{ $controlPoint->name }}
                                                                </strong>
                                                                <small class='text-bold text-{{ $reportByControlPoint->statusColor }} pull-right'>
                                                                    <i class='ion-android-stopwatch'></i> {{ $reportByControlPoint->statusText }}
                                                                </small><br>
                                                                <small>
                                                                    <i class='fa fa-car text-muted'></i> {{ $vehicle->number }}
                                                                </small>
                                                                <small class='pull-right'>
                                                                    <i class='fa fa-retweet text-muted'></i> @lang('Round trip') {{ $dispatchRegister->round_trip }}
                                                                </small>
                                                                <br>
                                                                <small class='text-bold'>
                                                                    @lang('Scheduled Time'): {{ $reportByControlPoint->scheduledControlPointTime }}
                                                                </small><br>
                                                                <small class='text-bold'>
                                                                    @lang('Reported Time'):&nbsp;&nbsp;&nbsp; {{ $reportByControlPoint->measuredControlPointTime }}
                                                                </small>
                                                            "
                                                            data-content="<div style='width:200px'>
                                                                <strong>@lang('Fringe'):</strong> <small>{{ $reportByControlPoint->fringeName }}</small><br>
                                                                <strong>@lang('Interpolation report'):</strong><br>
                                                                <small><strong> • @lang('Time scheduled from dispatch'):</strong> {{ $reportByControlPoint->timeScheduled }}</small><br>
                                                                <small><strong> • @lang('Time measured from dispatch'):</strong> {{ $reportByControlPoint->timeMeasured }}</small><br><br>
                                                                <strong>@lang('GPS report'):</strong><br>
                                                                <small><strong> • @lang('Time scheduled from dispatch'):</strong> {{ $reportByControlPoint->timep }}</small><br>
                                                                <small><strong> • @lang('Time measured from dispatch'):</strong> {{ $reportByControlPoint->timem }}</small>
                                                            </div>">
                                                        <span>{{ $reportByControlPoint->difference }}</span>
                                                    </button>
                                                    <br>
                                                    <small class="measured-time {{ $query->paintProfile ? "measured-time-dark" : ""  }}"><i class="fa fa-clock-o"></i> {{ $reportByControlPoint->measuredControlPointTime }}</small>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-center">
                                                {{ $reportByControlPoint->difference }}
                                            </td>
                                        @endif
                                    @endforeach
                                    <td class="text-center" width="10%">
                                        <a href="#modal-route-report"
                                           class="btn btn-xs btn-lime btn-link faa-parent animated-hover btn-show-chart-route-report tooltips"
                                           data-toggle="modal"
                                           data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}"
                                           data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}"
                                           data-original-title="@lang('Graph report detail')">
                                            <i class="fa fa-area-chart faa-pulse"></i>
                                        </a>
                                        @if( Auth::user()->isSuperAdmin() )
                                            @php( $totalLocations = \DB::select("SELECT count(1) total FROM locations WHERE dispatch_register_id = $dispatchRegister->id")[0]->total )
                                            @php( $totalReports = \DB::select("SELECT count(1) total FROM reports WHERE dispatch_register_id = $dispatchRegister->id")[0]->total )
                                            <hr class="hr no-padding">
                                            <small class="tooltips" data-title="@lang('Locations')">{!! $totalLocations !!}</small>/<small class="tooltips" title="@lang('Reports')">{!! $totalReports !!}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($reportsByControlPoints->count())
                                <tr>
                                    <td colspan="4">

                                    </td>
                                    <th class="text-center text-muted bg-inverse tooltips" data-title="@lang('Average'): @lang('Route time')">
                                        {{ $strTime::segToStrTime($strTime::toSeg($averageRouteTime)/$reportsByControlPoints->count()) }}
                                    </th>
                                    <td colspan="20">

                                    </td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">hideSideBar();</script>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups')!</strong></h4>
            <hr class="hr">
            @lang('The date haven´t a control point time report')
        </div>
    </div>
@endif