@if(count($vehiclesDispatchRegisters))
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
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-10">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @foreach($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters)
                            @php( $vehicle = \App\Vehicle::find($vehicleId) )
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{ $vehicle->plate }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-placement="bottom"
                                    data-original-title="{{ $vehicle->plate }}">
                                    <i class="fa fa-car f-s-8 icon-report"></i><span class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong>{{ $vehicle->number }}</strong>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-1 hide">
                    <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&route-report={{ $route->id ?? $route }}&type-report=vehicle&export=true" class="btn btn-lime bg-lime-dark pull-right" style="position: absolute;left: -20px;">
                        <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters)
                @php( $vehicle = \App\Vehicle::find($vehicleId) )
                @php( $company = $vehicle->company )
                <div id="report-tab-{{ $vehicle->plate }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                    <table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th data-sorting="disabled">
                                <i class="fa fa-flag text-muted"></i><br>
                                @lang('Route')
                            </th>
                            <th data-sorting="disabled">
                                <i class="fa fa-list-ol text-muted"></i><br>
                                @lang('Round Trip')
                            </th>
                            <th data-sorting="disabled">
                                <i class="fa fa-retweet text-muted"></i><br>
                                @lang('Turn')
                            </th>
                            <th>
                                <i class="fa fa-user text-muted"></i><br>
                                @lang('Driver')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i><br>
                                @lang('Departure time')
                            </th>
                            <th class="col-md-2">
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
                            <th data-sorting="disabled">
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
                        @foreach( $dispatchRegisters as $dispatchRegister )
                            @php
                                $strTime = new \App\Http\Controllers\Utils\StrTime();
                                $route = $dispatchRegister->route;
                                $recorderCounterPerRoundTrip = $dispatchRegister->recorderCounterPerRoundTrip;
                                $driver = $dispatchRegister->driver;

                                $currentRecorder = $recorderCounterPerRoundTrip->end_recorder;

                                $startRecorderPrev = $recorderCounterPerRoundTrip->end_recorder_prev;
                                if($dispatchRegister->start_recorder > 0){
                                    $startRecorderPrev = $dispatchRegister->start_recorder;
                                }

                                //$passengersPerRoundTrip = $recorderCounterPerRoundTrip->passengers_round_trip;
                                $passengersPerRoundTrip = $currentRecorder - $startRecorderPrev;
                                $totalPerRoute+=$passengersPerRoundTrip;
                                $invalid = ($totalPerRoute > 1000 || $totalPerRoute < 0)?true:false;
                            @endphp
                            <tr>
                                <th class="bg-inverse text-white text-center">{{ $route->name }}</th>
                                <th class="bg-inverse text-white text-center">{{ $dispatchRegister->round_trip }}</th>
                                <td>{{ $dispatchRegister->turn }}</td>
                                <td class="text-uppercase">{{ $driver?$driver->fullName():__('Not assigned') }}</td>
                                <td>{{ $strTime->toString($dispatchRegister->departure_time) }}</td>
                                <td>{{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}</td>
                                <td>{{ $strTime->toString($dispatchRegister->arrival_time) }}</td>
                                <td>{{ $strTime->toString($dispatchRegister->arrival_time_difference) }}</td>
                                <td>{{ $dispatchRegister->status }}</td>
                                @if( $company->hasRecorderCounter() )
                                <td width="20%" class="p-r-0 p-l-0 text-center">
                                    @if( Auth::user()->isAdmin() )
                                    <div class="tooltips box-edit-recorder" data-title="@lang('Start Recorder')">
                                        <span class="box-info">
                                            <span class="">
                                                {{ $startRecorderPrev }}
                                            </span>
                                        </span>
                                        <div class="box-edit" style="display: none">
                                            <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('start_recorder')"
                                                   class="input-sm form-control edit-input-recorder" value="{{ $startRecorderPrev }}">
                                        </div>
                                    </div>
                                    @else
                                        {{ $startRecorderPrev }}
                                    @endif
                                </td>
                                <td width="20%" class="p-r-0 p-l-0 text-center">
                                    @if( Auth::user()->isAdmin() )
                                    <div class="tooltips box-edit-recorder" data-title="@lang('Start Recorder')">
                                        <span class="box-info">
                                            <span class="">
                                                {{ $currentRecorder }}
                                            </span>
                                        </span>
                                        <div class="box-edit" style="display: none">
                                            <input id="edit-end-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('end_recorder')"
                                                   class="input-sm form-control edit-input-recorder" value="{{ $currentRecorder }}">
                                        </div>
                                    </div>
                                    @else
                                        {{ $currentRecorder }}
                                    @endif
                                </td>
                                <td width="5%">
                                    <span title="{{ $currentRecorder.'-'.$startRecorderPrev }}" class="{{ $invalid?'tooltips text-danger':'' }}" data-original-title="{{ $invalid?__('Verify possible error in register data'):'' }}">
                                        {{ $currentRecorder - $startRecorderPrev }}
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
                        @endforeach
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif