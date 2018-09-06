<!-- begin table -->
<table class="table table-bordered table-striped table-hover table-valign-middle table-report">
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
                <i class="fa fa-users text-muted"></i><br>
                @lang('Passengers')<br>
                <small class="text-muted">
                    <i class="fa fa-crosshairs text-muted"></i> <i class="fa fa-compass text-muted"></i> @lang('Sensor') @lang('Recorder')
                </small>
            </th>
            <th class="text-center hide">
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
                @lang('Passengers')<br>
                <small class="text-muted">
                    <i class="fa fa-compass text-muted"></i> @lang('Recorder')
                </small>
            </th>
        @endif
        <th>
            <i class="fa fa-users text-muted"></i><br>
            @lang('Passengers')<br>
            <small class="text-muted">
                <i class="fa fa-crosshairs text-muted"></i> @lang('Sensor')
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
                <td width="10%" class="text-center">
                    @if( $dispatchRegister->complete() )
                        <span class="tooltips" data-title="@lang('Round trip')">
                            {{ $dispatchRegister->passengersBySensorRecorder }}
                            @php( $error = $dispatchRegister->calculateErrorPercent(($endRecorder - $startRecorder), $dispatchRegister->passengersBySensorRecorder) )
                            <span class="text-{{ abs($error) < 5.9?'success':'danger' }} text-bold col-md-4 pull-right f-s-9 p-1 tooltips" data-title="@lang('% error')" data-placement="right">
                                {{  $error }}%
                            </span>
                        </span>
                        <hr class="m-0">
                        <small class="tooltips text-bold" data-title="@lang('Accumulated day')">
                            {{ $totalPassengersBySensorRecorder }}
                            @php( $error = $dispatchRegister->calculateErrorPercent($totalPassengersByRecorder, $totalPassengersBySensorRecorder) )
                            <span class="text-{{ abs($error) < 5.9?'success':'danger' }} text-bold col-md-4 pull-right f-s-9 p-1 tooltips" data-title="@lang('% error')" data-placement="right">
                                {{  $error }}%
                            </span>
                        </small>
                    @else
                        ...
                        <hr class="hr">
                        ...
                    @endif
                </td>
                <td width="10%" class="p-r-0 p-l-0 text-center hide">
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
                        <span title="" class="{{ $invalid?'text-muted':'' }} tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Round trip').' '.($endRecorder.' - '.$startRecorder) }}">
                            {{ $endRecorder - $startRecorder }}
                        </span>
                        <hr class="m-0">
                        <small class="{{ $invalid?'text-muted':'' }} text-bold tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Accumulated day') }}">
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
                @if( $dispatchRegister->complete() )
                    <span class="tooltips" data-title="@lang('Round trip')">
                        {{ $dispatchRegister->passengersBySensor }}
                        @php( $error = $dispatchRegister->calculateErrorPercent(($endRecorder - $startRecorder), $dispatchRegister->passengersBySensor) )
                        <span class="text-{{ abs($error) < 5.9?'success':'danger' }} text-bold col-md-4 pull-left f-s-9 p-1 tooltips" data-title="@lang('% error')" data-placement="left">
                            {{  $error }}%
                        </span>
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensor }}
                        @php( $error = $dispatchRegister->calculateErrorPercent($totalPassengersByRecorder, $totalPassengersBySensor) )
                        <span class="text-{{ abs($error) < 5.9?'success':'danger' }} text-bold col-md-4 pull-left f-s-9 p-1 tooltips" data-title="@lang('% error')" data-placement="left">
                            {{  $error }}%
                        </span>
                    </small>
                @else
                    ...
                    <hr class="hr">
                    ...
                @endif
            </td>

            <td width="5%" class="text-center">
                <button data-target="#collapse-{{ $dispatchRegister->id }}" class="btn btn-sm btn-rounded btn-info tooltips" data-title="@lang('Show frames')" data-toggle="collapse">
                    <i class="fa fa-code"></i>
                </button>
                @if( $dispatchRegister->complete() )
                <button class="btn btn-sm btn-rounded btn-success tooltips btn-geolocation-report"
                        data-toggle="modal"
                        data-target="#modal-geolocation-report"
                        data-title="@lang('Show geolocation report')"
                        data-url="{{ route('report-passengers-geolocation-search') }}"
                        data-id="{{ $dispatchRegister->id }}">
                    <i class="fa fa-map"></i>
                </button>
                @endif
            </td>
        </tr>

        <tr id="collapse-{{ $dispatchRegister->id }}" class="bg-inverse text-white text-bold collapse fade">
            <td colspan="9" class="p-l-4 p-r-4" style="font-family: monospace">
                <div class="col-md-12">
                    @lang('Initial frame counter') ({{ $strTime->toString($dispatchRegister->initial_time_sensor_counter) }}):<br>
                    <hr class="col-md-3 m-t-3 m-b-4"><br>
                    @if($dispatchRegister->hasObservationCounter())
                        {{ $dispatchRegister->displayInitialObservationsCounter() }}
                    @else
                        <ul class="col-md-3 m-l-20">
                            <li>@lang('Front door'): {{ $dispatchRegister->initial_front_sensor_counter }}</li>
                            <li>@lang('Back door'): {{ $dispatchRegister->initial_back_sensor_counter }}</li>
                            <li>@lang('Passengers') @lang('sensor'): {{ $dispatchRegister->initial_sensor_counter }}</li>
                        </ul>
                    @endif
                    @include('.partials.reports.frame', ['currentFrame' => $dispatchRegister->initial_frame_sensor_counter])
                </div>

                <hr class="col-md-12 no-padding">

                <div class="col-md-12">
                    @lang('Final frame counter') ({{ $strTime->toString($dispatchRegister->final_time_sensor_counter) }}):<br>
                    <hr class="col-md-3 m-t-3 m-b-4"><br>
                    @if($dispatchRegister->hasObservationCounter())
                        {{ $dispatchRegister->displayFinalObservationsCounter() }}
                    @else
                        <ul class="col-md-3 m-l-20">
                            <li>@lang('Front door'): {{ $dispatchRegister->final_front_sensor_counter }}</li>
                            <li>@lang('Back door'): {{ $dispatchRegister->final_back_sensor_counter }}</li>
                            <li>@lang('Passengers') @lang('sensor'): {{ $dispatchRegister->final_sensor_counter }}</li>
                        </ul>
                    @endif
                    @include('.partials.reports.frame', ['currentFrame' => $dispatchRegister->final_frame_sensor_counter])
                </div>
            </td>
        </tr>

        @php( $lastArrivalTime[$vehicle->id] = $dispatchRegister->arrival_time )
    @endforeach
    </tbody>
</table>
<!-- end table -->