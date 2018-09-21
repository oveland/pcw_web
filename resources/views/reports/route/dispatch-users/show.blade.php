@if(count($dispatchUsersReport->reports))
    @php($reports = $dispatchUsersReport->reports)

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
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @foreach($reports as $userId => $report)
                            @php( $user = $report->user )
                            <li class="{{ $loop->first?'active':'' }}">
                                <a href="#report-tab-{{ $userId }}" data-toggle="tab" aria-expanded="true" class="text-center">
                                    <span class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong class="text-uppercase">
                                        <i class="fa fa-user"></i> {{ $user->name }}
                                    </strong>
                                    <hr class="m-t-5 m-b-5">
                                    <small class="text-bold">
                                        {{ $report->firstDispatchRegister->departure_time }} @lang('to') {{ $report->lastDispatchRegister->departure_time }}
                                    </small><br>
                                    <small>{{ $report->totalDispatches }} @lang('dispatches')</small><br>
                                    <small>{{ $report->totalVehiclesDispatched }} @lang('vehicles')</small>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($reports as $userId => $report)
                @php
                    $user = $report->user;
                    $dispatchRegistersByVehicles = $report->dispatchRegistersByVehicles;
                    $counterByRecorderByVehicles = $report->counterByRecorderByVehicles;
                @endphp
                <div id="report-tab-{{ $userId }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                    <div class="table-responsive">
                        <!-- begin table -->
                        @if(true)
                        <table class="table table-bordered table-striped table-hover table-valign-middle table-report table-condensed">
                            <thead>
                            <tr class="inverse">
                                <th class="text-center">
                                    <i class="fa fa-list-ol" aria-hidden="true"></i>
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-car" aria-hidden="true"></i><br>
                                    @lang('Vehicle')
                                </th>
                                <th class="text-center details">
                                    <i class="fa fa-compass" aria-hidden="true"></i><br>
                                    @lang('First start recorder')
                                </th>
                                <th class="text-center details">
                                    <i class="fa fa-compass" aria-hidden="true"></i><br>
                                    @lang('Last end recorder')
                                </th>
                                <th class="text-center details">
                                    <i class="fa fa-users" aria-hidden="true"></i><br>
                                    @lang('Passengers')
                                </th>
                                <th class="text-center details">
                                    <i class="fa fa-list-alt" aria-hidden="true"></i><br>
                                    @lang('Details')
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle)
                                @php
                                    $vehicle = \App\Vehicle::find($vehicleId);
                                    $counterByRecorder = $counterByRecorderByVehicles->where('vehicleId', $vehicleId)->first()->counter;
                                    $issues = $counterByRecorder->issues;
                                    $counterByRecorderByVehicle = $counterByRecorder->report[$vehicleId];
                                @endphp
                                <tr class="text-center {{ $issues->isNotEmpty()?'text-danger text-bold':''  }}">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{!! $vehicle->number !!}</td>
                                    <td>{{ $counterByRecorderByVehicle->firstStartRecorder ?? 'NONOE' }}</td>
                                    <td>{{ $counterByRecorderByVehicle->lastEndRecorder ?? 'NONOE' }}</td>
                                    <td>{{ $counterByRecorderByVehicle->passengersByRecorder ?? 'NONOE' }}</td>
                                    <td class="text-center details">
                                        <button class="btn btn-sm btn-link" data-toggle="collapse" data-target="#vehicle-{{ $vehicleId }}-{{ $userId }}">
                                            <i class="fa fa-eye"></i> @lang('Details')
                                        </button>
                                    </td>
                                </tr>
                                <tr id="vehicle-{{ $vehicleId }}-{{ $userId }}" class="collapse fade">
                                    <td colspan="6">
                                        <div class="row">
                                            @if( $loop->last && $loop->parent->last )
                                                {{ dd($report->dispatchRegistersByVehicles[$vehicleId]->toArray(), $report->counterByRecorderByVehicles[$vehicleId]) }}
                                                {{ dd($issues) }}
                                            @endif
                                            <div class="col-md-12 p-0">
                                                @if($issues->isNotEmpty())
                                                    <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
                                                        <i class="fa fa-exclamation-circle"></i>
                                                        <strong>@lang('Warning'):</strong>
                                                        @lang('There are issues in data recorder'). <a data-toggle="collapse" data-target="#issue-vehicles" class="text-bold text-warning click">@lang('See details')</a>
                                                    </div>
                                                    <div id="issue-vehicles" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                                        <div class="panel-body p-0">
                                                            @include('partials.alerts.reports.passengers.issuesByVehicles', ['issuesByVehicles' => $issues])
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="col-md-12 p-0">
                                                <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                                                    <thead>
                                                    <tr class="inverse">
                                                        <th class="text-center">
                                                            <i class="fa fa-flag" aria-hidden="true"></i><br>
                                                            @lang('Route')
                                                        </th>
                                                        <th class="text-center details">
                                                            <i class="fa fa-clock-o" aria-hidden="true"></i><br>
                                                            @lang('Route times')
                                                        </th>
                                                        <th class="text-center details">
                                                            <i class="fa fa-retweet" aria-hidden="true"></i><br>
                                                            @lang('Round trip')
                                                        </th>
                                                        <th class="text-center details">
                                                            <i class="fa fa-list-ol" aria-hidden="true"></i><br>
                                                            @lang('Turn')
                                                        </th>
                                                        <th class="text-center">
                                                            <i class="fa fa-compass text-muted"></i><br>
                                                            @lang('Recorder')<br>
                                                            <small class="text-muted">
                                                                @lang('Initial')
                                                            </small>
                                                        </th>
                                                        <th class="text-center">
                                                            <i class="fa fa-compass text-muted"></i><br>
                                                            @lang('Recorder')<br>
                                                            <small class="text-muted">
                                                                @lang('Final')
                                                            </small>
                                                        </th>
                                                        <th class="text-center">
                                                            <i class="fa fa-users text-muted"></i><br>
                                                            <small><i class="fa fa-compass text-muted"></i></small> @lang('Passengers')<br>
                                                            <small class="text-muted">
                                                                @lang('Recorder')
                                                            </small>
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    @foreach($dispatchRegistersByVehicle as $dispatchRegister)
                                                        @php( $dispatchRegisterCounter = $counterByRecorderByVehicle->history[$dispatchRegister->id] )
                                                        <tr>
                                                            <th width="20%" class="bg-inverse text-white text-center">
                                                                {{ $dispatchRegister->route->name }}
                                                            </th>
                                                            <th width="5%" class="bg-inverse text-white text-center">
                                                                <small class="tooltips" data-title="@lang('Departure time')">{{ $dispatchRegister->departure_time }}</small><br>
                                                                <small class="tooltips" data-title="@lang('Arrival time')">{{ $dispatchRegister->arrival_time }}</small>
                                                                <hr class="hr">
                                                                <small class="tooltips" data-title="@lang('Route time')">{{ $dispatchRegister->getRouteTime() }}</small>
                                                            </th>
                                                            <th width="5%" class="bg-inverse text-white text-center">
                                                                {{ $dispatchRegister->round_trip }}<br>
                                                                <small>{{ $dispatchRegister->status }}</small>
                                                            </th>
                                                            <th width="5%" class="bg-inverse text-white text-center">
                                                                {{ $dispatchRegister->turn }}
                                                            </th>
                                                            <td width="5%" class="text-center">
                                                                @if( Auth::user()->isAdmin() )
                                                                    <div class="tooltips box-edit" data-title="@lang('Start Recorder')">
                                                                        <span class="box-info">
                                                                            <span class="">{{ $dispatchRegisterCounter->startRecorder }}</span>
                                                                        </span>
                                                                        <div class="box-edit" style="display: none">
                                                                            <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                                                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('start_recorder')"
                                                                                   class="input-sm form-control edit-input-recorder" value="{{ $dispatchRegisterCounter->startRecorder }}">
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    {{ $dispatchRegisterCounter->startRecorder }}
                                                                @endif
                                                            </td>
                                                            <td width="5%" class="text-center">
                                                                @if( Auth::user()->isAdmin() )
                                                                    <div class="tooltips box-edit" data-title="@lang('End Recorder')">
                                                                        <span class="box-info">
                                                                            <span class="">{{ $dispatchRegisterCounter->endRecorder }}</span>
                                                                        </span>
                                                                        <div class="box-edit" style="display: none">
                                                                            <input id="edit-end-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                                                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('end_recorder')"
                                                                                   class="input-sm form-control edit-input-recorder" value="{{ $dispatchRegisterCounter->endRecorder }}">
                                                                        </div>
                                                                    </div>
                                                                @else
                                                                    {{ $dispatchRegisterCounter->endRecorder }}
                                                                @endif
                                                            </td>
                                                            <td width="5%" class="text-center">
                                                                @if( $dispatchRegister->complete() )
                                                                    <span class="tooltips" data-title="@lang('Passengers') @lang('round trip')">
                                                                        {{ $dispatchRegisterCounter->passengersByRoundTrip }}
                                                                    </span>
                                                                    <hr class="m-0">
                                                                    <small class="tooltips" data-title="@lang('Passengers') @lang('Accumulated day')">
                                                                        {{ $dispatchRegisterCounter->totalPassengersByRoute }}
                                                                    </small>
                                                                @else
                                                                    ...
                                                                    <hr class="hr">
                                                                    ...
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        @endif
                        <!-- end table -->
                    </div>
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif