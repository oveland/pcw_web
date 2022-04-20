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

        .info-correction-factor {
            background: #184b61;
            padding: 7px 30px 7px 30px !important;
            color: white !important;
        }

        .info-correction-factor-ok {
            box-shadow: -4px 0px 0px 0px #008000b3 !important;
        }

        .info-correction-factor-up {
            box-shadow: -4px 0px 0px 0px #10c1e0 !important;
        }

        .info-correction-factor-down {
            box-shadow: -4px 0px 0px 0px #cd762e !important;
        }

        .info-correction-factor:hover {
            color: white !important;
        }

        .label-time-between {
            color: #0b7d95;
            margin-bottom: 20px;
        }

        .label-time-between-scheduled {
            color: #078769;
            padding: 3px 35px 3px 35px !important;
        }

        .cursor-pointer {
            cursor: pointer !important;
        }

        .table-bordered, .table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
            border: 1px solid #d7d6d6 !important;
        }

        .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th {
            border: 1px solid white !important;
        }

        .table thead {
            background: white;
        }

        @media (min-width: 600px) {
            .table {
                position: relative !important;
                background: white;
                border: 1px  black !important;
            }

            .table th {
                position: sticky !important;
                position: -webkit-sticky !important;
                z-index: 2 !important;
                top: 47px !important; /* Don't forget this, required for the stickiness */
                box-shadow: -3px 6px 6px 1px rgba(0, 0, 0, 0.8);
                border: 1px #2b3643 !important;
            }

            .table tfoot td, .table tfoot th {
                position: sticky !important;
                position: -webkit-sticky !important;
                z-index: 2 !important;
                bottom: 30px !important; /* Don't forget this, required for the stickiness */
                box-shadow: -3px 6px 6px 1px rgba(0, 0, 0, 0.8);
                background: #2b3643f7;
                border: 1px #2b3643 !important;
            }
        }

        @media (max-width: 600px) {
            .table-responsive-xs {
                overflow-x: auto !important;
            }
        }

        hr.divider {
            margin: 5px 20% 0 20%;
            border-color: #e1dede;
        }
    </style>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-control-points-search-report') }}?{{ $query->stringParams }}&export=true"
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

        <div class="tab-content panel">
            <div id="report-tab" class="tab-pane fade active in report-tab-cp">
                <div class="">
                    <div class="table-responsive-xs col-md-12 p-0">
                        <table class="table table-bordered table-condensed table-hover table-valign-middle table-report-control-point data-table-report">
                            <thead>
                            <tr class="sticky">
                                <th class="text-center bg-inverse-dark text-muted">
                                    <i class="fa fa-list-ol"></i>
                                </th>
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
                                    @lang('Vh.')
                                </th>
                                <th class="text-center bg-inverse-dark text-muted hide">
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
                                @php
                                    $controlPointsDurations = collect([]);
                                    $lastScheduled = collect([]);
                                @endphp
                                @foreach($reportsByControlPoints->first()->reportsByControlPoint as $reportByControlPoint)
                                    @php
                                        $controlPoint =  $reportByControlPoint->controlPoint;
                                        $lastScheduled->put($controlPoint->id, "");
                                    @endphp
                                    <th class="{{ $controlPoint->trajectory == 0 ? 'info':'danger' }}" style="">
                                        <div style="display: inline-block;vertical-align: middle;float: none;">
                                            <small>{{ $controlPoint->name }}</small>
                                            <hr>
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

                                    $averageRouteTime = $strTime::addStrTime($averageRouteTime, $dispatchRegister->getRouteTime(true));
                                @endphp
                                <tr class="">
                                    <th class="text-capitalize text-muted bg-inverse">
                                        {{ $loop->iteration }}
                                    </th>
                                    <th class="text-capitalize text-muted bg-inverse">
                                        {{ $dispatchRegister->date }}
                                    </th>
                                    <th class="text-capitalize text-muted bg-{{ $dispatchRegister->inProgress() ? 'warning':'inverse' }}">
                                        {{ $dispatchRegister->round_trip }}<br>
                                        <small>@lang('Turn') {{ $dispatchRegister->turn }}</small><br>
                                        <small class="status-html" style="font-size: 0.95rem">{!! $dispatchRegister->status !!}</small>
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
                                    <th class="bg-inverse text-uppercase text-muted hide">
                                        <small>
                                            {{ $dispatchRegister->driverName() }}
                                        </small>
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

                                    <th class="bg-inverse text-capitalize text-muted" style="border-right: 10px solid {{ $dispatchRegister->departureFringe->style_color }}">
                                        {{ $dispatchRegister->departureFringe->name }} <br>
                                        <small>{{ $dispatchRegister->departureFringe->dayType->description }}</small>
                                    </th>
                                    @php
                                        $prevReport = $reportsByControlPoint->reportsByControlPoint->first();
                                    @endphp
                                    @foreach($reportsByControlPoint->reportsByControlPoint as $reportByControlPoint)
                                        @php
                                            $controlPoint = $reportByControlPoint->controlPoint;
                                            if( $reportByControlPoint->hasReport ){
                                                $lastScheduled->put($controlPoint->id, $reportByControlPoint->timeScheduled);
                                            }

                                            $prevMeasuredControlPointTime = $prevReport->measuredControlPointTime;
                                            $hasPreviousReference = $prevMeasuredControlPointTime != '--:--:--';

                                            $timeFromPrev = $hasPreviousReference ? $strTime::subStrTime($reportByControlPoint->measuredControlPointTime, $prevMeasuredControlPointTime) : '--:--:--';
                                            $diffBetween = $hasPreviousReference && $reportByControlPoint->controlPointTime ? $strTime::difference($timeFromPrev, $reportByControlPoint->controlPointTime->time) : '--:--:--';

                                            $thresholdAverages = intval(request()->get('threshold-averages'));
                                            $showAverages = $thresholdAverages && request()->get('fringe-report');
                                            $includesForAverages = $thresholdAverages && abs($strTime::toSeg($diffBetween) / 60) <= $thresholdAverages;

                                            if($includesForAverages && $hasPreviousReference) {
                                                $current = collect($controlPointsDurations->get($controlPoint->id) ?? []);
                                                $current->push($strTime::toSeg($timeFromPrev));
                                                $controlPointsDurations->put($controlPoint->id, $current);
                                            }

                                            $prevReport = $reportByControlPoint;
                                        @endphp
                                        @if( $reportByControlPoint->hasReport )
                                            <td class="text-center td-info" style="background: {{  $query->paintProfile ? $reportByControlPoint->backgroundProfile : "white" }};">
                                                <div class="tooltipss" data-title="{{ $controlPoint->name }}">
                                                    @if($query->showDetails && $hasPreviousReference)
                                                    <div class="measured-time">
                                                        <h6 class="m-0">
                                                            <span class="cursor-pointer tooltips" data-html="true" title="{{ $showAverages && !$includesForAverages ? "<span class='text-warning'>".__('Average excluded')."</span>" : '' }}">
                                                                <small class="tooltips label-time-between" title="@lang('Measured time from prev') {{ $reportByControlPoint->measuredControlPointTime }} - {{ $prevMeasuredControlPointTime }}" data-placement="left">
                                                                    <i class="ion-android-stopwatch faa-flash animated"></i> {{ $timeFromPrev }}
                                                                </small>
                                                                <small class="tooltips {{ $showAverages && !$includesForAverages ? 'text-muted' : '' }}" data-html="true" title="@lang('Difference time from prev') <br> <span style='color: #078769'>{{ $reportByControlPoint->controlPointTime->time }}</span> - <span style='color: #0b7d95'>{{ $timeFromPrev }}</span> <br> @lang('Scheduled time - Measured time')" data-placement="right">
                                                                    (
                                                                    <span>
                                                                        {{ $diffBetween }}
                                                                        @if($showAverages)
                                                                            <span class="{{ $includesForAverages ? 'text-lime' : 'text-danger' }}">•</span>
                                                                        @endif
                                                                    </span>
                                                                    )
                                                                </small>
                                                            </span>
                                                        </h6>
                                                    </div>
                                                    <hr class="divider">
                                                    @endif

                                                    <span class="f-s-12 btn-circle btn btn-{{ $reportByControlPoint->statusColor }} tooltips m-5"
                                                            type="button" data-html="true" title="@lang('Absolute difference from dispatch') {{ $reportByControlPoint->scheduledControlPointTime }} - {{ $reportByControlPoint->measuredControlPointTime }} <br><i class='fa fa-map-marker text-muted'></i>  {{ $controlPoint->name }}"
                                                            style="{{ "background: $reportByControlPoint->backgroundProfile !important;" . ($query->paintProfile ? "border: 1px solid white !important" : "") }};height: 23px;padding: 3px 6px; font-weight: bold">
                                                        <span>{{ $reportByControlPoint->difference }}</span>
                                                    </span>

                                                    @if($query->showDetails)
                                                    <div class="measured-time {{ $query->paintProfile ? "measured-time-dark" : ""  }}">
                                                        <h6 class="m-0 cursor-pointer">
                                                            <span>
                                                                <small class="tooltips" title="@lang('Scheduled Time')" data-placement="left">
                                                                    <i class="fa fa-clock-o"></i> {{ $reportByControlPoint->scheduledControlPointTime }}
                                                                </small>
                                                            </span>
                                                            <small>vs</small>
                                                            <span>
                                                                <small class="faa-parent animated tooltips" title="@lang('GPS time')" data-placement="right">
                                                                <i class="fa fa-dot-circle-o faa-burst green animated"></i> {{ $reportByControlPoint->measuredControlPointTime }}
                                                            </small>
                                                            </span>
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
                                        @if( Auth::user()->isSuperAdmin() && false )
                                            @php( $totalLocations = $dispatchRegister->locations()->count() )
                                            @php( $totalReports = $dispatchRegister->reports()->count() )
                                            <hr class="hr no-padding">
                                            <small class="badge tooltips" data-original-title="@lang('Locations') / @lang('Reports')" data-placement="bottom">{!! $totalLocations !!} / {!! $totalReports !!}</small>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                            <tfoot>
                            @if($reportsByControlPoints->count() && $query->fringeReport)
                                @php
                                    $fringeName = $reportsByControlPoints->last()->dispatchRegister->departureFringe->name;
                                    $typeDay = $reportsByControlPoints->last()->dispatchRegister->departureFringe->dayType->description;
                                @endphp
                                <tr class="sticky">
                                    <th class="bg-inverse text-muted text-right text-uppercase" colspan="4">
                                        <span class="pull-right">@lang('Averages')</span>
                                    </th>
                                    <th class="text-center text-muted bg-inverse tooltips" data-title="@lang('Average'): @lang('Route time')">
                                        {{ $strTime::segToStrTime($strTime::toSeg($averageRouteTime)/$reportsByControlPoints->count()) }}
                                    </th>
                                    <th class="bg-inverse text-muted">
                                            {{ $fringeName }}<br>
                                            <small>{{ $typeDay }}</small>
                                    </th>
                                    @foreach($reportsByControlPoints->first()->reportsByControlPoint as $reportByControlPoint)
                                        @php
                                            $controlPoint =  $reportByControlPoint->controlPoint;

                                            $cpDurations = collect($controlPointsDurations->get($controlPoint->id));

                                            $averageTimeSecondsBetween = $cpDurations->average();

                                            $correctionFactor = $averageTimeSecondsBetween ? $strTime::difference($reportByControlPoint->controlPointTime->time, $strTime::segToStrTime($averageTimeSecondsBetween)) : '--:--:--';
                                            $correctionFactorInSeconds = $strTime::toSeg($correctionFactor, true);
                                        @endphp
                                        <th class="{{ $controlPoint->trajectory == 0 ? 'info':'danger' }}" style="" rowspan="2">
                                            <span class="cursor-pointer">
                                                <small class="tooltips label-time-between" title="@lang('Average time from prev point')">
                                                    {{ $averageTimeSecondsBetween ? $strTime::segToStrTime($averageTimeSecondsBetween) : '--:--:--' }}
                                                </small>
                                                <br>
                                                <span class="tooltips btn btn-circle label-time-between-scheduled" title="@lang('Scheduled time from prev point')" data-placement="bottom">
                                                    {{ $reportByControlPoint->controlPointTime->time }}
                                                </span>
                                            </span>
                                            <hr>
                                            <span class="tooltips btn btn-circle info-correction-factor cursor-pointer info-correction-factor-{{ abs($correctionFactorInSeconds) <= 60 ? 'ok' : ($correctionFactorInSeconds > 60 ? 'up' : 'down') }}" title="@lang('Correction factor') {{ $controlPoint->name }} @lang('Fringe'): {{ $fringeName }} {{ $typeDay }}">
                                                <strong class="text-bold">{{ $correctionFactor }}</strong>
                                            </span>
                                        </th>
                                    @endforeach
                                    <th class="bg-inverse" rowspan="2"></th>
                                </tr>
                                <tr class="sticky bottom">
                                    <th class="bg-inverse text-muted text-right text-uppercase" colspan="6" style="height: 0">
                                        <strong class="pull-right text-bold">@lang('Correction factor') </strong>
                                    </th>
                                </tr>
                            @endif
                            </tfoot>
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