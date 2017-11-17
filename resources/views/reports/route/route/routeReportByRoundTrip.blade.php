@if(count($roundTripDispatchRegisters))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <a href="{{ route('route-search-report') }}?date-report={{ $dateReport }}&route-report={{ $route->id }}&type-report=round_trip&export=true" class="btn btn-lime bg-lime-dark pull-right">
                        <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                    </a>
                    <ul class="nav nav-pills nav-pills-success">
                        @foreach($roundTripDispatchRegisters as $roundTrip => $dispatchRegisters)
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{ $roundTrip }}" data-toggle="tab" aria-expanded="true">
                                    @lang('Round trip') {{ $roundTrip }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content panel p-0">
            @foreach($roundTripDispatchRegisters as $roundTrip => $dispatchRegisters)
                <div id="report-tab-{{ $roundTrip }}" class="table-responsive tab-pane fade {{ $loop->first?'active in':'' }}">
                    <!-- begin table -->
                    <table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th data-sorting="disabled">
                                <i class="fa fa-list-ol text-muted"></i><br>
                                @lang('Turn')
                            </th>
                            <th>
                                <i class="fa fa-car text-muted"></i><br>
                                @lang('Vehicle')
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
                            <th data-sorting="disabled" class="text-center">
                                <i class="fa fa-users text-muted"></i><br>
                                @lang('Passengers')
                                <hr class="hr">
                                @lang('Day')
                            </th>
                            <th data-sorting="disabled">
                                <i class="fa fa-rocket text-muted"></i><br>
                                @lang('Actions')
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $dispatchRegisters as $dispatchRegister )
                            <tr>
                                <td class="bg-inverse text-white text-center">{{ $dispatchRegister->turn }}</td>
                                <td width="25%" class="text-center">{{ $dispatchRegister->vehicle->number }} | {{ $dispatchRegister->vehicle->plate }}</td>
                                <td>{{ $dispatchRegister->departure_time }}</td>
                                <td>{{ $dispatchRegister->arrival_time_scheduled }}</td>
                                <td>{{ $dispatchRegister->arrival_time }}</td>
                                <td>{{ $dispatchRegister->arrival_time_difference }}</td>
                                <td>{{ $dispatchRegister->status }}</td>
                                @php($total = $dispatchRegister->recorderCounterPerRoundTrip->passengers)
                                @php($invalid = ($total<0 || $total > 1000)?true:false )
                                <td width="5%" class="text-center">
                                    <span class="{{ $invalid?'tooltips text-danger':'' }}" data-original-title="{{ $invalid?__('Verify possible error in register data'):'' }}">
                                        {{ $total }}
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
    @include('partials.alerts.noRegistersFound')
@endif