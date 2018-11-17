@php($routeReports = $passengerReport->reports)
@if(count($routeReports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-passengers-recorders-detailed-daily-export') }}?date-report={{ $passengerReport->date }}&company-report={{ $passengerReport->companyId }}" class="btn btn-lime bg-lime-dark btn-sm">
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
                @foreach($routeReports as $route_id => $routeReport)
                    @php( $route = \App\Models\Routes\Route::find($route_id) )
                    <li class="{{ $loop->first ? 'active':'' }}">
                        <a href="#route-report-tab-{{ $route_id }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-bus" aria-hidden="true"></i> {{ $route->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="tab-content p-0">
            @foreach($routeReports as $route_id => $routeReport)
                @php
                    $route = \App\Models\Routes\Route::find($route_id);
                    $report = $routeReport->report;
                    $issuesByVehicles = $routeReport->issues;
                @endphp

                <div id="route-report-tab-{{ $route_id }}" class="table-responsive tab-pane fade {{ $loop->first ? 'active in':'' }}">
                    @if(count($issuesByVehicles))
                        <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
                            <i class="fa fa-exclamation-circle"></i>
                            <strong>@lang('Warning'):</strong>
                            @lang('There are issues in data recorder'). <a data-toggle="collapse" data-target="#issue-{{ $route_id }}" class="text-bold text-warning click">@lang('See details')</a>
                        </div>
                        <div id="issue-{{ $route_id }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                            <div class="panel-body p-0">
                                @include('partials.alerts.reports.passengers.issuesByVehicles',compact('issuesByVehicles'))
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
                                    $vehicle = $vehicleReport->vehicle;
                                    $vehicleReport->issue ? null : $totalPassengers->push($vehicleReport->passengers);
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $vehicle->number }} | {{  $vehicle->plate }}</td>
                                    <td class="text-center {{ $vehicleReport->issue ? 'text-danger tooltips click':'' }}" data-title="@lang('Error in') {{ $vehicleReport->issue->field ?? '' }}"
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