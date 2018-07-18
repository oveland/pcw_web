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
        <th data-sorting="disabled">
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

            <td width="10%" class="text-center">
                <small class="tooltips" data-title="@lang('Departure time')">
                    {{ $strTime->toString($dispatchRegister->departure_time) }}
                </small>
                <br>
                <small class="tooltips" data-title="@lang('Arrival time')">
                    {{ $strTime->toString($dispatchRegister->arrival_time) }}
                </small>
                <hr class="m-0">
                <small class="tooltips text-bold" data-title="@lang('Route time')">
                    {{ $dispatchRegister->getRouteTime() }}
                </small>
            </td>

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

            <td width="5%" class="text-center">
                <button data-target="#collapse-{{ $dispatchRegister->id }}" class="btn btn-sm btn-rounded btn-info tooltips" data-title="@lang('Show frames')" data-toggle="collapse">
                    <i class="ion-ios-search"></i>
                </button>
            </td>
        </tr>

        <tr id="collapse-{{ $dispatchRegister->id }}" class="bg-inverse text-white text-bold collapse fade">
            <td colspan="8" class="p-l-4 p-r-4" style="font-family: monospace">
                @lang('Initial frame counter') ({{ $strTime->toString($dispatchRegister->initial_time_sensor_counter) }}): <strong>{{ $dispatchRegister->initial_sensor_counter }}</strong>
                @include('.partials.reports.frame', ['currentFrame' => $dispatchRegister->initial_frame_sensor_counter])
                <hr class="hr">
                @lang('Final frame counter') ({{ $strTime->toString($dispatchRegister->final_time_sensor_counter) }}): <strong>{{ $dispatchRegister->final_sensor_counter }}</strong>
                @include('.partials.reports.frame', ['currentFrame' => $dispatchRegister->final_frame_sensor_counter])
            </td>
        </tr>

        @php( $lastArrivalTime[$vehicle->id] = $dispatchRegister->arrival_time )
    @endforeach
    </tbody>
</table>
<!-- end table -->