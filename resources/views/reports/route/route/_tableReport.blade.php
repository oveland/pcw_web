<!-- begin table -->
<table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
    <thead>
    <tr class="inverse">
        <th class="{{ $routeReport != 'all'?'hide':'' }}">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Route')
        </th>
        <th>
            <i class="fa fa-list-ol text-muted"></i><br>
            @lang('Round Trip')
        </th>
        <th>
            <i class="fa fa-retweet text-muted"></i><br>
            @lang('Turn')
        </th>
        <th class="{{ $typeReport == 'group-vehicles'?'hide':'' }}">
            <i class="fa fa-car text-muted"></i><br>
            @lang('Vehicle')
        </th>
        @if( $company->hasDriverRegisters() )
        <th>
            <i class="fa fa-user text-muted"></i><br>
            @lang('Driver')
        </th>
        @endif
        <th>
            <i class="fa fa-clock-o text-muted"></i><br>
            @lang('Departure time')
        </th>
        <th>
            <i class="fa fa-clock-o text-muted"></i><br>
            @lang('Arrival Time')
        </th>
        <th>
            <i class="ion-android-stopwatch text-muted"></i><br>
            @lang('Route Time')
        </th>
        @if( $company->hasRecorderCounter() )
            <th class="text-center">
                <i class="fa fa-compass text-muted"></i><br>
                {{ str_limit(__('Recorder'),5) }}<br>
                <small class="text-muted">
                    @lang('Initial')
                    <hr class="m-0">
                    @lang('Final')
                </small>
            </th>
            <th class="text-center">
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-compass text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Recorder')
                </small>
            </th>
        @endif
        @if( false )
            <th>
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Sensor')
                </small>
            </th>
            <th>
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Sensor') @lang('Recorder')
                </small>
            </th>
        @endif
        <th>
            <i class="fa fa-rocket text-muted"></i><br>
            @lang('Actions')
        </th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalDeadTime = array();
        $lastArrivalTime = array();

        $totalPassengersBySensor = 0;
        $totalPassengersBySensorRecorder = 0;
    @endphp

    @foreach( $dispatchRegisters as $dispatchRegister )
        @php
            $strTime = new \App\Http\Controllers\Utils\StrTime();
            $route = $dispatchRegister->route;
            $driver = $dispatchRegister->driver;
            $vehicle = $dispatchRegister->vehicle;
            $historyCounter = $reportsByVehicle[$vehicle->id]->report->history[$dispatchRegister->id];

            $endRecorder = $historyCounter->endRecorder;
            $startRecorder = $historyCounter->startRecorder;
            $passengersPerRoundTrip = $historyCounter->passengersByRoundTrip;

            $totalPassengersByRecorder = $historyCounter->totalPassengersByRoute;
            $totalPassengersBySensor +=$dispatchRegister->passengersBySensor;
            $totalPassengersBySensorRecorder +=$dispatchRegister->passengersBySensorRecorder;

            $invalid = ($totalPassengersByRecorder > 1000 || $totalPassengersByRecorder < 0)?true:false;
        @endphp
        <tr>
            <th width="10%" class="{{ $routeReport != 'all'?'hide':'' }} bg-{{ $dispatchRegister->complete() ?'inverse':'warning' }} text-white text-center">
                {{ $route->name }}
            </th>
            <th width="5%" class="bg-{{ $dispatchRegister->complete() ?'inverse':'warning' }} text-white text-center">
                {{ $dispatchRegister->round_trip }}<br>
                <small>{{ $dispatchRegister->status }}</small>
            </th>
            <th width="5%" class="bg-inverse text-white text-center">{{ $dispatchRegister->turn }}</th>
            <th width="5%" class="bg-inverse text-white text-center {{ $typeReport == 'group-vehicles'?'hide':'' }}">{{ $vehicle->number }}</th>
            @if( $company->hasDriverRegisters() )
            <td width="30%" class="text-uppercase">
                @if( Auth::user()->isAdmin() )
                    @php( $driverInfo = $driver?$driver->fullName():$dispatchRegister->driver_code )
                    <div class="tooltips box-edit" data-title="@lang('Driver')">
                        <span class="box-info">
                            <span class="{{ !$driverInfo?'text-danger text-bold':'' }} text-capitalize">
                                {{ $driverInfo?$driverInfo:__('Empty') }}
                            </span>
                        </span>
                        <div class="box-edit" style="display: none">
                            <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('driver_code')"
                                   class="input-sm form-control edit-input-recorder" value="{{ $dispatchRegister->driver_code }}">
                        </div>
                    </div>
                @else
                    {{ $driver?$driver->fullName():$dispatchRegister->driver_code }}
                @endif
            </td>
            @endif
            <td class="text-center">
                {{ $strTime->toString($dispatchRegister->departure_time) }}<br>
                <small class="tooltips text-info" data-title="@lang('Vehicles without route')" data-placement="bottom">
                    {{ $dispatchRegister->available_vehicles }} <i class="fa fa-bus"></i>
                </small>
                @if( isset($lastArrivalTime[$vehicle->id]) )
                    @php($deadTime = $strTime->subStrTime($dispatchRegister->departure_time, $lastArrivalTime[$vehicle->id]))
                    @php($totalDeadTime[$vehicle->id] = $strTime->addStrTime($totalDeadTime[$vehicle->id], $deadTime))
                    <br>
                    <small class="tooltips text-primary" data-title="@lang('Dead time')" data-placement="bottom">
                        <i class="ion-android-stopwatch text-muted"></i> {{ $deadTime }}
                    </small>
                    <br>
                    <small class="tooltips text-warning" data-title="@lang('Accumulated dead time')" data-placement="bottom">
                        <i class="ion-android-stopwatch text-muted"></i> {{ $totalDeadTime[$vehicle->id] }}
                    </small>
                @else
                    @php($totalDeadTime[$vehicle->id] = '00:00:00')
                @endif
            </td>
            <td width="10%" class="text-center">
                <small class="tooltips text-bold" data-title="@lang('Arrival Time')">
                    {{ $strTime->toString($dispatchRegister->arrival_time) }}
                </small>
                <small class="tooltips text-muted" data-title="@lang('Arrival Time Scheduled')">
                    {{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}
                </small>
                <hr class="m-0">
                <small class="tooltips text" data-title="@lang('Arrival Time Difference')">
                    {{ $strTime->toString($dispatchRegister->arrival_time_difference) }} <i class="ion-android-stopwatch text-muted"></i>
                </small>
            </td>
            <td width="8%" class="text-center">{{ $dispatchRegister->getRouteTime() }}</td>

            @if( $company->hasRecorderCounter() )
                <td width="10%" class="p-r-0 p-l-0 text-center">
                    @if( Auth::user()->isAdmin() )
                        <div class="tooltips box-edit" data-title="@lang('Start Recorder')">
                            <span class="box-info">
                                <span class="">
                                    {{ $startRecorder }}
                                </span>
                            </span>
                            <div class="box-edit" style="display: none">
                                <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                       data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('start_recorder')"
                                       class="input-sm form-control edit-input-recorder" value="{{ $startRecorder }}">
                            </div>
                        </div>
                    @else
                        {{ $startRecorder }}
                    @endif
                    <hr class="m-0">
                    @if( Auth::user()->isAdmin() )
                        <div class="tooltips box-edit" data-title="@lang('End Recorder')">
                            <span class="box-info">
                                <span class="">
                                    {{ $endRecorder }}
                                </span>
                            </span>
                            <div class="box-edit" style="display: none">
                                <input id="edit-end-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                       data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('end_recorder')"
                                       class="input-sm form-control edit-input-recorder" value="{{ $endRecorder }}">
                            </div>
                        </div>
                    @else
                        {{ $endRecorder }}
                    @endif
                </td>
                <td width="10%" class="text-center">
                    @if( $dispatchRegister->complete() )
                        <span title="" class="{{ $invalid?'text-danger':'' }} tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Round trip').' '.($endRecorder.' - '.$startRecorder) }}">
                            {{ $endRecorder - $startRecorder }}
                        </span>
                        <hr class="m-0">
                        <small class="{{ $invalid?'text-danger':'' }} text-bold tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Accumulated day') }}">
                            {{ $totalPassengersByRecorder }}
                        </small>
                    @else
                        ...
                        <hr class="hr">
                        ...
                    @endif
                </td>
            @endif

            @if( false )
                <td width="10%" class="text-center">
                    <span class="tooltips" data-title="@lang('Round trip')">
                        {{ $dispatchRegister->passengersBySensor }}
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensor }}
                    </small>
                </td>
                <td width="10%" class="text-center">
                    <span class="tooltips" data-title="@lang('Round trip')">
                        {{ $dispatchRegister->passengersBySensorRecorder }}
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensorRecorder }}
                    </small>
                </td>
            @endif

            <td width="5%" class="text-center">
                <a href="#modal-route-report"
                   class="btn btn-xs btn-lime btn-link faa-parent animated-hover btn-show-chart-route-report tooltips"
                   data-toggle="modal"
                   data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}"
                   data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}"
                   data-original-title="@lang('Graph report detail')">
                    <i class="fa fa-area-chart faa-pulse"></i>
                </a>
            </td>
        </tr>
        @php( $lastArrivalTime[$vehicle->id] = $dispatchRegister->arrival_time )
    @endforeach
    </tbody>
</table>
<!-- end table -->