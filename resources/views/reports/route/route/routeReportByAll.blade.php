@if(count($dispatchRegisters))
    <div class="col-md-12 alert alert-info p-t-5 container-alert-new-values" style="display: none">
        <strong>
            <i class="fa fa-exclamation"></i> @lang('Registers updated')
        </strong>
        <button class="btn btn-info btn-xs" onclick="$('.form-search-report').submit()">
            <i class="fa fa-refresh"></i>
        </button>
        <p>@lang('Please refresh the report once you finish the fix bugs')</p>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading p-b-40">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&route-report={{ $route->id ?? $route }}&type-report=vehicle&export=true" class="btn btn-sm btn-lime bg-lime-dark btn-rounded pull-left hide">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
        </div>

        <div class="tab-content panel p-0">
            <div id="report-tab" class="table-responsive tab-pane fade active in">
                <!-- begin table -->
                <table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                    <thead>
                    <tr class="inverse">
                        <th data-sorting="disabled">
                            <i class="fa fa-flag text-muted"></i><br>
                            @lang('Route')
                        </th>
                        <th data-sorting="disabled">
                            <i class="fa fa-retweet text-muted"></i><br>
                            @lang('Turn')
                        </th>
                        <th data-sorting="disabled">
                            <i class="fa fa-car text-muted"></i><br>
                            @lang('Vehicle')
                        </th>
                        <th data-sorting="disabled">
                            <i class="fa fa-list-ol text-muted"></i><br>
                            @lang('Round Trip')
                        </th>
                        <th>
                            <i class="fa fa-user text-muted"></i><br>
                            @lang('Driver')
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-clock-o text-muted"></i><br>
                            @lang('Departure time')
                        </th>
                        <th class="col-md-2 hide">
                            <i class="fa fa-clock-o text-muted"></i><br>
                            @lang('Arrival Time Scheduled')
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-clock-o text-muted"></i><br>
                            @lang('Arrival Time')
                        </th>
                        <th class="col-md-2">
                            <i class="fa fa-clock-o text-muted"></i><br>
                            @lang('Arrival Time Difference')
                        </th>
                        <th class="col-md-2">
                            <i class="ion-android-stopwatch text-muted"></i><br>
                            <span class="text-warning">@lang('Route Time')</span>
                        </th>
                        <th class="hide" data-sorting="disabled">
                            <i class="fa fa-tachometer text-muted"></i><br>
                            @lang('Status')
                        </th>
                        @if( $company->hasRecorderCounter() )
                        <th colspan="2" data-sorting="disabled" class="text-center">
                            @lang('Registradora')
                            <hr class="hr">
                            @lang('Inicial') | @lang('Final')
                        </th>
                        <th colspan="2" data-sorting="disabled" class="text-center">
                            <i class="fa fa-users text-muted"></i><br>
                            @lang('Passengers')
                            <hr class="hr">
                            @lang('Round Trip') | @lang('Route')
                        </th>
                        @endif
                        <th data-sorting="disabled">
                            <i class="fa fa-rocket text-muted"></i><br>
                            @lang('Actions')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($totalPerRoute = 0)
                    @php($totalDeadTime = array())
                    @php( $lastArrivalTime = array() )
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
                            $totalPerRoute = $historyCounter->totalPassengersByRoute;

                            $invalid = ($totalPerRoute > 1000 || $totalPerRoute < 0)?true:false;
                        @endphp
                        <tr>
                            <th class="bg-{{ $dispatchRegister->complete() ?'inverse':'warning' }} text-white text-center">
                                {{ $route->name }}<br>
                                <small>{{ $dispatchRegister->status }}</small>
                            </th>
                            <th class="bg-inverse text-white text-center">
                                {{ $dispatchRegister->turn }}
                            </th>
                            <th class="bg-inverse text-white text-center">{{ $vehicle->number }}</th>
                            <td class="bg-inverse text-white text-center">{{ $dispatchRegister->round_trip }}</td>
                            <td class="text-uppercase">
                                @if( Auth::user()->isAdmin() )
                                    <div class="tooltips box-edit" data-title="@lang('Driver')">
                                    <span class="box-info">
                                        <span class="">
                                            {{ $driver?$driver->fullName():$dispatchRegister->driver_code }}
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
                            <td class="text-center">
                                {{ $strTime->toString($dispatchRegister->departure_time) }}<br>
                                <small class="tooltips text-info" data-title="@lang('Vehicles without route')" data-placement="bottom">
                                    {{ $dispatchRegister->available_vehicles }} <i class="fa fa-bus"></i>
                                </small>
                                @if( isset($lastArrivalTime[$vehicle->id]) )
                                    @php($deadTime=$strTime->subStrTime($dispatchRegister->departure_time, $lastArrivalTime[$vehicle->id]))
                                    @php($totalDeadTime[$vehicle->id]=$strTime->addStrTime($totalDeadTime[$vehicle->id], $deadTime))
                                <br>
                                <small class="tooltips text-primary" data-title="@lang('Dead time')" data-placement="bottom">
                                    <i class="ion-android-stopwatch text-muted"></i> {{ $deadTime }}
                                </small>
                                <br>
                                <small class="tooltips text-warning" data-title="@lang('Total dead time')" data-placement="bottom">
                                    <i class="ion-android-stopwatch text-muted"></i> {{ $totalDeadTime[$vehicle->id] }}
                                </small>
                                @else
                                    @php($totalDeadTime[$vehicle->id]='00:00:00')
                                @endif
                            </td>
                            <td class="hide text-center">{{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}</td>
                            <td class="text-center">{{ $strTime->toString($dispatchRegister->arrival_time) }}</td>
                            <td class="text-center">{{ $strTime->toString($dispatchRegister->arrival_time_difference) }}</td>
                            <td class="text-center">{{ $dispatchRegister->getRouteTime() }}</td>
                            <td class="hide">{!! $dispatchRegister->status !!}</td>

                            @if( $company->hasRecorderCounter() )
                            <td width="20%" class="p-r-0 p-l-0 text-center">
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
                            </td>
                            <td width="20%" class="p-r-0 p-l-0 text-center">
                                @if( Auth::user()->isAdmin() )
                                <div class="tooltips box-edit" data-title="@lang('Start Recorder')">
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
                            <td width="5%">
                                <span title="{{ $endRecorder.'-'.$startRecorder }}" class="{{ $invalid?'tooltips text-danger':'' }}" data-original-title="{{ $invalid?__('Verify possible error in register data'):'' }}">
                                    {{ $endRecorder - $startRecorder }}
                                </span>
                            </td>
                            <td width="5%">
                                <span class="{{ $invalid?'tooltips text-danger':'' }}" data-original-title="{{ $invalid?__('Verify possible error in register data'):'' }}">
                                    {{ $totalPerRoute }}
                                </span>
                            </td>
                            @endif
                            <td width="10%" class="text-center">
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
                    <tr class="hide">
                        <td colspan="4" class="text-right"><i class="ion-android-stopwatch"></i> @lang('Total dead time')</td>
                        <td class="text-center">{{ dump($totalDeadTime) }}</td>
                        <td colspan="8"></td>
                    </tr>
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif
{{ dd('') }}