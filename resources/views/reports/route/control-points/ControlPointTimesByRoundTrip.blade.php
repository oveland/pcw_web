@if(count($controlPointTimeReportsByRoundTrip))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-control-points-export-report') }}?date-report={{ '' }}&company-report={{ '' }}" class="btn btn-lime bg-lime-dark btn-sm">
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
                        <div class="table-responsive col-md-12">
                            @php( $reportsByControlPoint =  $controlPointTimeReportByRoundTrip->groupBy('control_point_id') )
                            @php( $reportsByVehicles = $controlPointTimeReportByRoundTrip->groupBy('vehicle_id') )

                            <table class="table table-bordered table-striped table-hover table-valign-middle table-report-control-point">
                                <thead>
                                <tr class="inverse">
                                    <th class="text-center">
                                        <i class="fa fa-list text-muted"></i>
                                        @lang('Turn')
                                    </th>
                                    <th class="text-center">
                                        <i class="fa fa-car text-muted"></i>
                                        @lang('Vehicle')
                                    </th>
                                    @foreach($reportsByControlPoint->keys() as $controlPointId)
                                        @php( $controlPoint = \App\ControlPoint::find($controlPointId) )
                                        <th>
                                            <i class="fa fa-map-marker text-muted"></i><br>
                                            {{ $controlPoint->name }}
                                        </th>
                                    @endforeach
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach( $reportsByVehicles as $vehicleId => $reportByVehicles )
                                        @php( $vehicle = \App\Vehicle::find($vehicleId) )
                                        @php( $dispatchRegister = $reportByVehicles->first()->dispatchRegister )
                                        <tr class="inverse">
                                            <th class="text-uppercase">
                                                {{ $dispatchRegister->turn }}
                                            </th>
                                            <th class="text-uppercase">
                                                {{ $vehicle->number }} <br> {{ $vehicle->plate }}
                                            </th>
                                            @foreach($reportsByControlPoint->keys() as $controlPointId)
                                                @php( $report = $reportByVehicles->where('control_point_id',$controlPointId)->first() ?? null )
                                                <td class="text-center">
                                                    @if( $report )
                                                        @php
                                                            $measuredInterval = \Carbon\Carbon::parse(date('Y-m-d')." $report->timem");
                                                            $scheduledInterval = \Carbon\Carbon::parse(date('Y-m-d')." $report->timep");

                                                            $measuredTime = \Carbon\Carbon::parse(date('Y-m-d')." $dispatchRegister->departure_time");
                                                            $scheduledTime = \Carbon\Carbon::parse(date('Y-m-d')." $dispatchRegister->departure_time");

                                                            $measuredTime->addHours($measuredInterval->hour);
                                                            $measuredTime->addMinutes($measuredInterval->minute);
                                                            $measuredTime->addSecond($measuredInterval->second);

                                                            $scheduledTime->addHours($scheduledInterval->hour);
                                                            $scheduledTime->addMinutes($scheduledInterval->minute);
                                                            $scheduledTime->addSecond($scheduledInterval->second);

                                                            $statusColor =  'lime';
                                                            if( substr($report->timed,1) > '00:00:40' ){
                                                                $statusColor = substr($report->timed,0,1) == '+' ? 'primary':'danger';
                                                            }

                                                        @endphp
                                                        <i class="fa fa-bus f-s-20 icon-vehicle-status text-{{ $statusColor }}"></i>
                                                        <br>
                                                        <span class="f-s-10">
                                                        {{ $measuredTime->toTimeString() }} <br>
                                                        {{ $scheduledTime->toTimeString() }} <br>
                                                        <strong class="f-s-12">{{ $report->timed }}</strong>
                                                        </span>
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