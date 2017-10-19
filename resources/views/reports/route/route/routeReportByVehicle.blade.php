@if(count($vehiclesDispatchRegisters))
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
                <div class="col-md-1">
                    <a href="{{ route('route-search-report') }}?date-report={{ $dateReport }}&route-report={{ $route->id }}&type-report=vehicle&export=true" class="btn btn-lime bg-lime-dark pull-right" style="position: absolute;left: -20px;">
                        <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                    </a>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($vehiclesDispatchRegisters as $vehicleId => $dispatchRegisters)
                @php( $vehicle = \App\Vehicle::find($vehicleId) )
                <div id="report-tab-{{ $vehicle->plate }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                    <table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th data-sorting="disabled">
                                <i class="fa fa-list-ol text-muted"></i><br>
                                @lang('Round Trip')
                            </th>
                            <th data-sorting="disabled">
                                <i class="fa fa-retweet text-muted"></i><br>
                                @lang('Turn')
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
                                $recorderCounterPerRoundTrip = $dispatchRegister->recorderCounterPerRoundTrip;
                                $currentRecorder = $recorderCounterPerRoundTrip->end_recorder;
                                $startRecorderPrev= $recorderCounterPerRoundTrip->end_recorder_prev;
                                $passengersPerRoundTrip = $recorderCounterPerRoundTrip->passengers_round_trip;
                                $totalPerRoute+=$passengersPerRoundTrip;
                                $invalid = ($totalPerRoute > 1000)?true:false;
                            @endphp
                            <tr>
                                <th class="bg-inverse text-white text-center">{{ $dispatchRegister->round_trip }}</th>
                                <td>{{ $dispatchRegister->turn }}</td>
                                <td>{{ $dispatchRegister->departure_time }}</td>
                                <td>{{ $dispatchRegister->arrival_time_scheduled }}</td>
                                <td>{{ $dispatchRegister->arrival_time }}</td>
                                <td>{{ $dispatchRegister->arrival_time_difference }}</td>
                                <td>{{ $dispatchRegister->status }}</td>
                                <td width="15%">{{ $startRecorderPrev }}</td>
                                <td width="15%">{{ $currentRecorder }}</td>
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
                                <td width="10%" class="text-center">
                                    <a href="#modal-route-report"
                                       class="btn btn-xs btn-lime btn-link faa-parent animated-hover btn-show-chart-route-report tooltips"
                                       data-toggle="modal"
                                       data-url="{{ route('route-chart-report',['dispatchRegister'=>$dispatchRegister->id]) }}"
                                       data-url-off-road-report="{{ route('route-off-road-report',['dispatchRegister'=>$dispatchRegister->id]) }}"
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
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups!')</strong></h4>
            <hr class="hr">
            @lang('No registers found')
        </div>
    </div>
@endif