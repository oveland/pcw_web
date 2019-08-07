@if(count($controlPointTimeReportsByRoundTrip))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-control-points-export-report') }}?date-report={{ '' }}&company-report={{ '' }}" class="btn btn-lime bg-lime-dark btn-sm hide">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>

            <h5 class="text-white m-t-10">
                <span class="hides text-uppercase">
                    <i class="fa fa-map-marker" aria-hidden="true"></i>
                    @lang('Control point time report by Route')
                    <hr class="text-inverse-light">
                </span>
            </h5>

            <ul class="nav nav-pills nav-pills-success">
                @foreach($controlPointTimeReportsByRoundTrip->keys() as $roundTrip)
                    <li class="{{ $loop->first ? 'active':'' }} tooltips" data-title="@lang('Round Trip') {{ $roundTrip }}">
                        <a href="#report-tab-{{ $roundTrip }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-retweet" aria-hidden="true"></i> {{ $roundTrip }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content panel p-0">
            @foreach($controlPointTimeReportsByRoundTrip as $roundTrip => $controlPointTimeReportByRoundTrip)
                <div id="report-tab-{{ $roundTrip }}" class="tab-pane fade report-tab-cp {{ $loop->first ? 'active in':'' }}">
                    <div class="row">
                        <div class="table-responsive col-md-12" style="padding-bottom: 90px;height: 1000px">
                            @php
                                $controlPoints =  $route->controlPoints;
                                $reportsByVehicles = $controlPointTimeReportByRoundTrip->groupBy('vehicle_id')
                            @endphp


                            <table class="table table-bordered table-condensed table-hover table-valign-middle table-report-control-point data-table-report">
                                <thead>
                                <tr class="">
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
                                        <i class="fa fa-clock-o"></i><br>
                                        @lang('Route Time')
                                    </th>
                                    @foreach($controlPoints as $controlPoint)
                                        <th class="{{ $controlPoint->trajectory == 0 ? 'success':'warning' }}">
                                            <i class="fa fa-map-marker"></i><br>
                                            {{ $controlPoint->name }}
                                        </th>
                                    @endforeach
                                    <th class="bg-inverse-dark text-muted">
                                        <i class="fa fa-rocket"></i><br>
                                        @lang('Actions')
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach( $reportsByVehicles as $vehicleId => $reportByVehicles )
                                        @php
                                            $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId);
                                            $strTime = new \App\Http\Controllers\Utils\StrTime();
                                            $dispatchRegister = $reportByVehicles->first()->dispatchRegister;
                                            $driver = $dispatchRegister->driver;
                                            $departureTime = $dispatchRegister->departure_time;
                                            $arrivalTime = $dispatchRegister->arrival_time;
                                            $arrivalTimeScheduled = $dispatchRegister->arrival_time_scheduled;
                                        @endphp
                                        <tr class="">
                                            <th class="text-capitalize text-muted {{ $dispatchRegister->inProgress() ? 'warning':'bg-inverse' }}">
                                                {{ $dispatchRegister->turn }}<br>
                                                <span class="status-html">{!! $dispatchRegister->status !!}</span>
                                            </th>
                                            <th class="bg-inverse text-uppercase text-muted">
                                                {{ $vehicle->number }}
                                            </th>
                                            <th class="bg-inverse text-uppercase text-muted">
                                                {{ $driver?$driver->fullName():__('Not assigned') }}
                                            </th>
                                            <th class="bg-inverse text-uppercase text-muted">
                                                {{ $strTime::toString($departureTime) }}
                                                @if( $dispatchRegister->complete() )
                                                <br>{{ $strTime::toString($arrivalTime) }}
                                                <hr class="m-1">
                                                {{ $strTime::subStrTime($arrivalTime,$departureTime) }}
                                                @else
                                                    {{ '--:--:--' }}
                                                @endif
                                            </th>
                                            @foreach($controlPoints as $controlPoint)
                                                @php( $report = $reportByVehicles->where('control_point_id',$controlPoint->id)->first() ?? null )
                                                <td class="text-center">
                                                    @if( $report || ($loop->last && $dispatchRegister->complete() ) )
                                                        @php
                                                            if( $loop->last && $dispatchRegister->complete() ){ // For last control point
                                                                $measuredControlPointTime = $arrivalTime;
                                                                $scheduledControlPointTime = $arrivalTimeScheduled;
                                                            }else{
                                                                $controlPointTime = \App\Models\Routes\ControlPointTime::where('control_point_id',$controlPoint->id)
                                                                ->where('fringe_id',$report->fringe_id)
                                                                ->get()->first();

                                                                $measuredTime = $strTime::addStrTime($departureTime,$report->timem);
                                                                $scheduledTime = $strTime::addStrTime($departureTime,$report->timep);

                                                                $scheduledControlPointTime = $controlPointTime?$strTime::addStrTime($departureTime,$controlPointTime->time_from_dispatch):$scheduledTime;

                                                                $measuredControlPointTime = "";
                                                                if( $loop->first ){
                                                                    $measuredControlPointTime = $departureTime;
                                                                }
                                                                else{
                                                                    $measuredControlPointTime = $strTime::segToStrTime(
                                                                        $strTime::toSeg($scheduledControlPointTime)*$strTime::toSeg($measuredTime)/
                                                                        $strTime::toSeg($scheduledTime)
                                                                    );
                                                                }
                                                            }
                                                        @endphp

                                                        @if( $measuredControlPointTime && $scheduledControlPointTime )
                                                            @php
                                                                $statusColor =  'lime';
                                                                $statusText =  __('on time');
                                                                if( $strTime::subStrTime($measuredControlPointTime, $scheduledControlPointTime) > '00:01:00' ){
                                                                    if( $report ){
                                                                        $statusColor = $report->fast() ? 'primary':'danger';
                                                                        $statusText = __($report->status);
                                                                    }else{
                                                                        $isFast = $strTime::timeAGreaterThanTimeB($scheduledControlPointTime,$measuredControlPointTime);
                                                                        $statusColor =  $isFast ? 'primary':'danger';
                                                                        $statusText =  __($isFast ? 'fast':'slow');
                                                                    }
                                                                }
                                                            @endphp
                                                            <div class="tooltipss" data-title="{{ $controlPoint->name }}">
                                                                <i class="fa fa-bus f-s-15 icon-vehicle-status text-{{ $statusColor }}"></i>
                                                                <br>
                                                                <button type="button" class="f-s-12 m-t-5 btn btn-{{ $statusColor }} light btn-xs"
                                                                        data-placement="bottom"
                                                                        data-toggle="popover"
                                                                        data-html="true"
                                                                        data-trigger="hover"
                                                                        title="
                                                                            &nbsp;<i class='fa fa-map-marker text-muted'></i> {{ $controlPoint->name }}<br>
                                                                            <span class='f-s-12'>
                                                                                <i class='fa fa-car text-muted'></i>
                                                                                {{ $vehicle->number }}:
                                                                            </span>
                                                                            <b class='f-s-12 text-{{ $statusColor }}'>{{ $statusText }}</b>
                                                                        "
                                                                        data-content="
                                                                            <div style='width:200px'>
                                                                                <strong>@lang('Scheduled Time'):</strong> {{ $strTime::toString($scheduledControlPointTime) }}<br>
                                                                                <strong>@lang('Reported Time'):&nbsp;&nbsp;&nbsp;</strong> {{ $strTime::toString($measuredControlPointTime) }}
                                                                            </div>
                                                                        "
                                                                        >
                                                                    <span>
                                                                        {{ $strTime::difference($measuredControlPointTime, $scheduledControlPointTime) }}
                                                                    </span>
                                                                </button>
                                                                <br>
                                                            </div>
                                                        @else
                                                            @lang('--!--!--')
                                                        @endif
                                                    @else
                                                        @lang('--:--:--')
                                                    @endif
                                                </td>
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
                                                    <small>{!! $totalLocations !!} @lang('locations')</small><br>
                                                    <small>{!! $totalReports !!} @lang('reports')</small>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
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