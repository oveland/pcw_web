@php
    $reports = $passengerReport->reports;
    $issuesByVehicles = $passengerReport->issues;
@endphp
@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-passengers-recorders-consolidated-daily-export') }}?date-report={{ $passengerReport->date }}&company-report={{ $passengerReport->companyId }}" class="btn btn-lime bg-lime-dark btn-sm">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                <span class="hides">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    @lang('Consolidated per day')
                    <hr class="text-inverse-light">
                </span>

                <ul class="nav nav-pills nav-pills-success hide">
                    <li class="active">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor,.recorder').show()">
                            <i class="fa fa-asterisk" aria-hidden="true"></i> @lang('All')
                        </a>
                    </li>
                    <li class="">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor').show();$('.recorder').hide()">
                            <i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')
                        </a>
                    </li>
                    <li class="">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.recorder').show();$('.sensor').hide()">
                            <i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')
                        </a>
                    </li>
                </ul>
            </h5>
        </div>
        <div class="tab-content p-0">
            <div id="all-report-tab" class="table-responsive tab-pane active fade in">

                @if(count($issuesByVehicles))
                    <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
                        <i class="fa fa-exclamation-circle"></i>
                        <strong>@lang('Warning'):</strong>
                        @lang('There are issues in data recorder'). <a data-toggle="collapse" data-target="#issue-vehicles" class="text-bold text-warning click">@lang('See details')</a>
                    </div>
                    <div id="issue-vehicles" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
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
                        <th class="text-center">
                            <i class="fa fa-car" aria-hidden="true"></i> @lang('Vehicle')
                        </th>
                        <th class="text-center recorder">
                            <i class="fa fa-compass" aria-hidden="true"></i> <i class="fa fa-compass" aria-hidden="true"></i> <i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor recorder')
                        </th>
                        <th class="text-center recorder">
                            <i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')
                        </th>
                        <th class="text-center sensor">
                            <i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalSensor = collect([]);
                        $totalRecorder = collect([]);
                        $totalSensorRecorder = collect([]);
                    @endphp
                    @foreach($reports as $report)
                        @php
                            $vehicle = \App\Models\Vehicles\Vehicle::find($report->vehicle_id);
                            $sensorRecorder = $report->passengers->sensorRecorder;
                            $recorder = $report->passengers->recorder;
                            $sensor = $report->passengers->sensor;

                            $sensorRecorder > 0 ? $totalSensorRecorder->push($sensorRecorder):null;

                            $recorderIssue = $report->passengers->issue;
                            ($recorder > 0 && !$recorderIssue)? $totalRecorder->push($recorder):null;

                            $sensor > 0 ? $totalSensor->push($sensor):null;
                        @endphp
                        <tr class="text-center">
                            <td >{{ $loop->iteration }}</td>
                            <td>{{ $vehicle->number }} <i class="fa fa-hand-o-right" aria-hidden="true"></i> {{  $vehicle->plate }}</td>
                            <td class="sensor">{{ $sensorRecorder }}</td>
                            <td class="recorder">
                                <span class="text-center {{ $recorderIssue ? 'text-danger tooltips click':'' }}" data-title="@lang('Error in') {{ $recorderIssue->field ?? '' }}"
                                      onclick="{{ $recorderIssue ? "$('#issue-vehicles').collapse('show');":""  }}">
                                    {{ $recorder }}
                                </span>
                            </td>
                            <td class="sensor recorder">{{ $sensor }}</td>
                        </tr>
                        <tr class="hide">
                            <td colspan="5">
                                @foreach($report->historyRoutesByRecorder as $routeId => $historyRecorder)
                                    @php
                                        $lastHistory = $historyRecorder->last();
                                    @endphp
                                    <p>{{ $lastHistory->routeName }}: {{ $lastHistory->roundTrip }} @lang('round trips')</p>
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    <tr class="inverse bg-inverse-light text-white">
                        <td colspan="2" class="text-right">@lang('Total passengers')</td>
                        <td class="text-center recorder">{{ $totalSensorRecorder->sum() }}</td>
                        <td class="text-center recorder">{{ $totalRecorder->sum() }}</td>
                        <td class="text-center sensor">{{ $totalSensor->sum() }}</td>
                    </tr>
                    <tr class="inverse bg-inverse text-white">
                        <td colspan="2" class="text-right">@lang('Average per vehicle')</td>
                        <td class="text-center sensor">{{ number_format($totalSensorRecorder->average(),1) }}</td>
                        <td class="text-center recorder">{{ number_format($totalRecorder->average(),1) }}</td>
                        <td class="text-center sensor">{{ number_format($totalSensor->average(),1) }}</td>
                    </tr>
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif