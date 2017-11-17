@php($reports = $passengerReport->reports)
@php($issues = $passengerReport->issues)
@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('passengers-detailed-export-days') }}?date-report={{ $passengerReport->date }}&company-report={{ $passengerReport->companyId }}" class="btn btn-lime bg-lime-dark btn-sm">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>

            <h5 class="text-white m-t-10">
                <span class="hides">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    @lang('Detailed per day')
                    <hr class="text-inverse-light">
                </span>
            </h5>

            <ul class="nav nav-pills nav-pills-success">
                @foreach($reports as $route_id => $report)
                    @php( $route = \App\Route::find($route_id) )
                    <li class="{{ $loop->first ? 'active':'' }}">
                        <a href="#route-report-tab-{{ $route_id }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-bus" aria-hidden="true"></i> {{ $route->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content p-0">
            @foreach($reports as $route_id => $report)
                @php
                    $route = \App\Route::find($route_id);
                    $issueRoute = $issues[$route_id] ?? null;
                @endphp
                <div id="route-report-tab-{{ $route_id }}" class="table-responsive tab-pane fade {{ $loop->first ? 'active in':'' }}">
                    @if($issueRoute && count($issueRoute))
                        <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
                            <i class="fa fa-exclamation-circle"></i>
                            <strong>@lang('Warning'):</strong>
                            @lang('There are issues in data recorder'). <a data-toggle="collapse" data-target="#issue-{{ $route_id }}" class="text-bold text-warning click">@lang('See details')</a>
                        </div>
                        <div id="issue-{{ $route_id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body p-0">
                                @php( $issuesByVehicles = collect($issueRoute)->groupBy('vehicle_id') )
                                @foreach($issuesByVehicles as $vehicle_id => $issuesByVehicle)
                                    @php( $vehicle = \App\Vehicle::find($vehicle_id) )
                                    <div class="col-md-4 col-lg-3 col-sm-6 col-xs-12 p-t-5">
                                        <!-- begin widget -->
                                        <div class="widget">
                                            <div class="widget-header bg-inverse p-10">
                                                <h4 class="text-muted">
                                                    <i class="fa fa-car"></i>
                                                    <span class="issue-index">{{ $loop->iteration }}</span>
                                                    {{ $vehicle->number }} | {{ $vehicle->plate }}
                                                </h4>
                                            </div>
                                            <ul class="widget-todolist">
                                                @foreach($issuesByVehicle as $issue)
                                                @php($dispatchRegister = $issue->dispatchRegister)
                                                <li class="">
                                                    <div class="checkbox">
                                                        <label>
                                                            <i class="fa fa-exclamation-triangle text-warning tooltips" data-title="@lang('Error in') <b>{{ $issue->field }}</b>" data-html="true"></i>
                                                        </label>
                                                    </div>
                                                    <div class="info">
                                                        <h4 class="tooltips" data-title="{{ $dispatchRegister->status }}">
                                                            @lang('Turn') {{ $dispatchRegister->turn }},
                                                            @lang('Round Trip') {{ $dispatchRegister->round_trip }}
                                                        </h4>
                                                        <p class="tooltips" data-title="@lang('Departure time')">
                                                            <i class="fa fa-clock-o"></i>
                                                            {{ $dispatchRegister->departure_time }}
                                                        </p>
                                                        <p class="tooltips text-{{  $issue->field == __('Start Recorder')?'issue':'' }}" data-title="@lang('Start Recorder')">
                                                            <i class="fa fa-arrow-circle-right"></i>
                                                            {{ $issue->start_recorder }}
                                                        </p>
                                                        <p class="tooltips text-{{  $issue->field == __('End Recorder')?'issue':'' }}" data-title="@lang('End Recorder')">
                                                            <i class="fa fa-arrow-circle-left"></i>
                                                            {{ $dispatchRegister->end_recorder }}
                                                        </p>
                                                    </div>
                                                    <div class="action">
                                                        <a href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                                        <ul class="dropdown-menu dropdown-menu-right">
                                                            <li>
                                                                <a href="javascript:alert('@lang('Feature on development')');" class="disabled">
                                                                    <i class="fa fa-cog fa-spin"></i>
                                                                    @lang('Edit')
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <!-- end widget -->
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                            <tr class="inverse">
                                <th class="text-center">N°</th>
                                <th class="text-center"><i class="fa fa-car" aria-hidden="true"></i> @lang('Vehicle')</th>
                                <th class="text-center recorder"><i class="fa fa-compass" aria-hidden="true"></i> @lang('Passengers')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php( $totalPassengers = collect([]) )
                            @foreach($report as $vehicle_id => $vehicleReport)
                                @php
                                    $vehicle = \App\Vehicle::find($vehicle_id);
                                    $vehicleReport->issue ? null : $totalPassengers->push($vehicleReport->passengers);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $vehicle->number }} | {{  $vehicle->plate }}</td>
                                    <td class="text-center {{ $vehicleReport->issue ? 'text-danger tooltips click':'' }}" data-title="@lang('Error in') {{ $vehicleReport->issue }}"
                                        onclick="{{ $vehicleReport->issue ? "$('#issue-$route_id').collapse('show');":""  }}">
                                        {{ $vehicleReport->passengers }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="inverse bg-inverse-dark text-white">
                                <td colspan="2" class="text-right">@lang('Total passengers')</td>
                                <td class="text-center">{{ $totalPassengers->sum() }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif