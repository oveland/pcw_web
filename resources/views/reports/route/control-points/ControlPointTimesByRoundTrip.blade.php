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
                    <li class="{{ $loop->first ? 'active':'' }}">
                        <a href="#report-tab-{{ $roundTrip }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-retweet" aria-hidden="true"></i> {{ $roundTrip }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content panel">
            @foreach($controlPointTimeReportsByRoundTrip as $roundTrip => $controlPointTimeReportByRoundTrip)
                <div id="report-tab-{{ $roundTrip }}" class="tab-pane fade {{ $loop->first ? 'active in':'' }}">
                    <div class="row">
                        <div class="table-responsive col-md-12" style="padding-bottom: 90px">
                            @php( $reportsByControlPoint =  $controlPointTimeReportByRoundTrip->groupBy('control_point_id') )
                            @php( $reportsByVehicles = $controlPointTimeReportByRoundTrip->groupBy('vehicle_id') )

                            <table class="table table-bordered table-striped table-hover table-valign-middle table-report-control-point data-table-report">
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
                                    @foreach($reportsByControlPoint->keys() as $controlPointId)
                                        @php( $controlPoint = \App\ControlPoint::find($controlPointId) )
                                        <th class="{{ $controlPoint->trajectory == 0 ? 'success':'warning' }}">
                                            <i class="fa fa-map-marker"></i><br>
                                            {{ $controlPoint->name }}
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach( $reportsByVehicles as $vehicleId => $reportByVehicles )
                                        @php
                                            $vehicle = \App\Vehicle::find($vehicleId);
                                            $dispatchRegister = $reportByVehicles->first()->dispatchRegister;
                                            $departureTime = $dispatchRegister->departure_time;
                                            $arrival_time = $dispatchRegister->arrival_time;
                                        @endphp
                                        <tr class="">
                                            <th class="text-capitalize {{ $dispatchRegister->inProgress() ? 'warning':'' }}">
                                                {{ $dispatchRegister->turn }}<br>
                                                {{ $dispatchRegister->status }}
                                            </th>
                                            <th class="text-uppercase">
                                                {{ $vehicle->number }} <br> {{ $vehicle->plate }}
                                            </th>
                                            @foreach($reportsByControlPoint->keys() as $controlPointId)
                                                @php( $controlPoint = \App\ControlPoint::find($controlPointId) )
                                                @php( $report = $reportByVehicles->where('control_point_id',$controlPointId)->first() ?? null )
                                                <td class="text-center">
                                                    @if( $report )
                                                        @php
                                                            $controlPointTime = \App\ControlPointTime::where('control_point_id',$controlPointId)
                                                                ->where('fringe_id',$report->fringe_id)
                                                                ->get()->first();

                                                            $strTime = new \App\Http\Controllers\Utils\StrTime();
                                                            $measuredTime = $strTime::addStrTime($departureTime,$report->timem);
                                                            $scheduledTime = $strTime::addStrTime($departureTime,$report->timep);

                                                            $scheduledControlPointTime = $strTime::addStrTime($departureTime,$controlPointTime->time_from_dispatch);

                                                            $measuredControlPointTime = "";
                                                            if( $loop->first ){
                                                                $measuredControlPointTime = $departureTime;
                                                            }else if( $loop->last && $dispatchRegister->complete() ){
                                                                $measuredControlPointTime = $arrival_time;
                                                            }
                                                            else{
                                                                $measuredControlPointTime = $strTime::segToStrTime(
                                                                    $strTime::toSeg($scheduledControlPointTime)*$strTime::toSeg($measuredTime)/
                                                                    $strTime::toSeg($scheduledTime)
                                                                );
                                                            }
                                                        @endphp

                                                        @if( $measuredControlPointTime && $scheduledControlPointTime )
                                                            @php
                                                                $statusColor =  'lime';
                                                                $statusText =  __('on time');
                                                                if( $strTime::subStrTime($measuredControlPointTime, $scheduledControlPointTime) > '00:01:00' ){
                                                                    $statusColor = $report->fast() ? 'primary':'danger';
                                                                    $statusText = __($report->status);
                                                                }
                                                            @endphp
                                                            <div class="tooltipss" data-title="{{ $controlPoint->name }}">
                                                                <i class="fa fa-bus f-s-20 icon-vehicle-status text-{{ $statusColor }}"></i>
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
                                                                                <strong>@lang('Scheduled Time'):</strong> {{ $scheduledControlPointTime }}<br>
                                                                                <strong>@lang('Reported Time'):&nbsp;&nbsp;&nbsp;</strong> {{ $measuredControlPointTime }}
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
    <script type="application/javascript">

    </script>
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