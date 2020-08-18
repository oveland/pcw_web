@if(count($reportsByControlPoints))
    <style>
        .measured-time {
            font-size: 0.6em;
        }

        .measured-time-dark, .measured-time-dark span, .measured-time-dark small {
            color: white;
            font-weight: bold;
            text-shadow: #111111 1px 1px 4px;
        }
    </style>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-control-points-search-report') }}?export=true&type-report={{ $query->typeReport }}&date-report={{ $query->dateReport }}&company-report={{ $query->company->id }}&route-report={{ $query->route->id }}"
                   class="btn green btn-circle tooltips"
                   data-title="@lang('Export excel')" data-placement="bottom">
                    <i class="fa fa-download"></i>
                </a>
            </div>

            <h5 class="text-white m-t-10">
                <span class="hides text-uppercase">
                    {{ count($reportsByControlPoints) }} @lang('Registers')
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
                                    <i class="fa fa-calendar"></i>
                                    @lang('Date')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-retweet"></i>
                                    @lang('Round Trip')
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
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="ion-android-stopwatch"></i><br>
                                    @lang('Fringe')
                                </th>
                                @foreach($reportsByControlPoints->first()->reportsByControlPoint as $reportByControlPoint)
                                    @php($controlPoint =  $reportByControlPoint->controlPoint)
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
                                    <th class="text-capitalize text-muted bg-inverse">
                                        {{ $dispatchRegister->date }}
                                    </th>
                                    <th class="text-capitalize text-muted bg-{{ $dispatchRegister->inProgress() ? 'warning':'inverse' }}">
                                        {{ $dispatchRegister->round_trip }}<br>
                                        <small>@lang('Turn') {{ $dispatchRegister->turn }}</small><br>
                                        <small class="status-html">{!! $dispatchRegister->status !!}</small>
                                        @if($offRoadPercent)
                                            <div class="m-t-1">
                                                <label class="label label-{{ $offRoadPercent < 5 ? 'success': ($offRoadPercent < 50 ? 'warning': 'danger') }} tooltips" data-placement="bottom" title="@lang('Percent in off road')">
                                                    {{ number_format($offRoadPercent, 1,'.', '') }}% <i class="fa fa-random faa-passing animated" style="font-size: inherit !important;"></i>
                                                </label>
                                            </div>
                                        @endif
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        {{ $vehicle->number }}
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        {{ $dispatchRegister->driverName() }}
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted">
                                        @if( $dispatchRegister->complete() )
                                            {{ $dispatchRegister->departure_time }}
                                            <br>{{ $dispatchRegister->arrival_time }}
                                            <hr class="m-1">
                                            {{ $dispatchRegister->getRouteTime() }}
                                        @else
                                            {{ $dispatchRegister->departure_time }}
                                            <br>{{ '--:--:--' }}
                                            <hr class="m-1">
                                            {{ '--:--:--' }}
                                        @endif
                                    </th>
                                    <th class="bg-inverse text-uppercase text-muted" style="border-right: 10px solid {{ $dispatchRegister->departureFringe->style_color }}">
                                        {{ $dispatchRegister->departureFringe->name }}
                                    </th>
                                    @foreach($reportsByControlPoint->reportsByControlPoint as $reportByControlPoint)
                                        @php
                                            $controlPoint = $reportByControlPoint->controlPoint;
                                        @endphp
                                        @if( $reportByControlPoint->hasReport )
                                            <td class="text-center td-info" style="background: {{  $query->paintProfile ? $reportByControlPoint->backgroundProfile : "white" }};">
                                                <div class="tooltipss" data-title="{{ $controlPoint->name }}">
                                                    @if($query->showDetails)
                                                    <div class="measured-time {{ $query->paintProfile ? "measured-time-dark" : ""  }}">
                                                        <h6 class="m-0">
                                                            <small class="faa-parent animated-hover tooltips" title="@lang('Measured')" data-placement="left">
                                                                <i class="fa fa-dot-circle-o faa-burst green"></i> {{ $reportByControlPoint->timeMeasured }}
                                                            </small>
                                                        </h6>
                                                        <h6 class="m-0">
                                                            <small class="tooltips" title="@lang('Scheduled')" data-placement="left">
                                                                <i class="ion-android-stopwatch"></i> {{ $reportByControlPoint->timeScheduled }}
                                                            </small>
                                                        </h6>
                                                    </div>
                                                    @endif

                                                    <span class="f-s-12 btn-circle btn btn-{{ $reportByControlPoint->statusColor }} tooltips m-5"
                                                            type="button" data-html="true" title="<i class='fa fa-map-marker text-muted'></i>  {{ $controlPoint->name }}"
                                                            style="{{ "background: $reportByControlPoint->backgroundProfile !important;" . ($query->paintProfile ? "border: 1px solid white !important" : "") }};height: 23px;padding: 3px 6px; font-weight: bold">
                                                        <span>{{ $reportByControlPoint->difference }}</span>
                                                    </span>
                                                    @if($query->showDetails)
                                                    <div class="measured-time {{ $query->paintProfile ? "measured-time-dark" : ""  }}">
                                                        <h6 class="m-0">
                                                            <small class="faa-parent animated-hover tooltips" title="@lang('GPS time')" data-placement="left">
                                                                <i class="fa fa-dot-circle-o faa-burst green"></i> {{ $reportByControlPoint->measuredControlPointTime }}
                                                            </small>
                                                        </h6>
                                                        <h6 class="m-0">
                                                            <i class="ion-android-stopwatch"></i>
                                                            <small class="tooltips" title="@lang('Scheduled Time')" data-placement="left">
                                                                {{ $reportByControlPoint->scheduledControlPointTime }}
                                                            </small>
                                                        </h6>
                                                    </div>
                                                    @endif
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
                                           class="btn green-haze faa-parent animated-hover btn-show-chart-route-report btn-circle btn-outline tooltips"
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
                                            <small class="badge tooltips" data-original-title="@lang('Locations') / @lang('Reports')" data-placement="bottom">{!! $totalLocations !!} / {!! $totalReports !!}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            @if($reportsByControlPoints->count())
                                <tr>
                                    <td class="bg-inverse" colspan="4">
                                    </td>
                                    <th class="text-center text-muted bg-inverse tooltips" data-title="@lang('Average'): @lang('Route time')">
                                        {{ $strTime::segToStrTime($strTime::toSeg($averageRouteTime)/$reportsByControlPoints->count()) }}
                                    </th>
                                    <td class="bg-inverse" colspan="20">
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