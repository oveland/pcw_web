@php($reports = $passengerReport->reports)
@php($issuesByVehicles = $passengerReport->issues)
@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-passengers-recorders-consolidated-date-range-search') }}?export=true&company-report={{ $passengerReport->companyId }}&route-report={{ $passengerReport->routeReport }}&driver-report={{ $passengerReport->driverReport }}&vehicle-report={{ $passengerReport->vehicleReport }}&date-report={{ $passengerReport->dateReport }}&date-end-report={{ $passengerReport->dateEndReport }}&group-by-vehicle={{ $passengerReport->groupByVehicle }}&group-by-route={{ $passengerReport->groupByRoute }}&with-end-date={{ $passengerReport->withEndDate }}"
                   class="btn btn-success btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-download"></i>
                </a>
                @if( $passengerReport->vehicleReport != 'all' && Auth::user()->isAdmin() )
                <a href="javascript:;" class="btn btn-rounded btn-info tooltips" onclick="$('.collapse-frame').collapse('show')" data-title="@lang('See all frames')">
                    <i class="fa fa-podcast faa-pulse animated"></i>
                </a>
                @endif
            </div>
            <h5 class="text-white m-t-10">
                <soan class="text-bold text-white">
                    <i class="fa fa-users" aria-hidden="true"></i> {{ $passengerReport->totalRecorder }}
                </soan>
                @if($passengerReport->route)
                    <small class="text-white text-bold" style="font-size: 1em">
                        | <i class="fa fa-flag" aria-hidden="true"></i> {{ $passengerReport->route->name }}
                    </small>
                @endif
                @if($passengerReport->vehicle)
                    <small class="text-white" style="font-size: 1em">
                        | <i class="fa fa-bus" aria-hidden="true"></i> {{ $passengerReport->vehicle->number }}
                    </small>
                @endif
                @if($passengerReport->driver)
                <small class="text-white text-bold" style="font-size: 1em">
                    | <i class="fa fa-user" aria-hidden="true"></i> {{ $passengerReport->driver->fullName }}
                </small>
                @endif
                <br>

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
            @if(count($issuesByVehicles))
                <div class="alert alert-warning alert-bordered fade in m-0" style="border-radius: 0px">
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

            <div id="all-report-tab" class="table-responsive table-report tab-pane active fade in">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th class="text-center">
                            <i class="fa fa-list-ol"></i>
                        </th>
                        <th class="text-center">
                            <i class="fa fa-calendar" aria-hidden="true"></i><br> @lang('Date')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-flag" aria-hidden="true"></i><br> @lang('Route')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-bus" aria-hidden="true"></i><br> @lang('Vehicle')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-user" aria-hidden="true"></i><br> @lang('Driver')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-retweet" aria-hidden="true"></i><br> @lang('Total round trips')
                        </th>
                        <th class="text-center sensor recorder">
                            <i class="fa fa-crosshairs" aria-hidden="true"></i> <i class="fa fa-compass" aria-hidden="true"></i><br> @lang('Sensor recorder')
                        </th>
                        <th class="text-center recorder">
                            <i class="fa fa-compass" aria-hidden="true"></i><br> @lang('Passengers') <br> @lang('Recorder')
                        </th>
                        <th class="text-center sensor">
                            <i class="fa fa-crosshairs" aria-hidden="true"></i> @lang('Sensor')
                        </th>
                        <th class="p-3 {{ $passengerReport->canLiquidate ? '' : 'hide' }}">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> @lang('Settlement daily receipt')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalSensor = collect([]);
                        $totalRecorder = collect([]);
                        $totalSensorRecorder = collect([]);
                    @endphp
                    @foreach($reports as $date => $report)
                        @php
                            $sensorRecorder = $report->totalBySensorRecorder;
                            $recorder = $report->totalByRecorder;
                            $sensor = $report->totalBySensor;
                            $issuesByVehicles = $report->issues;

                            $sensorRecorder > 0 ? $totalSensorRecorder->push($sensorRecorder):null;
                            ($recorder > 0 && !count($issuesByVehicles)) ? $totalRecorder->push($recorder):null;
                            $sensor > 0 ? $totalSensor->push($sensor):null;
                        @endphp

                        <tr class="text-center click" data-toggle="collapse" data-target="#collapse-{{ $date }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $report->date }} </td>
                            <td>{{ $report->routeProcessed }} </td>
                            <td>{{ $report->vehicleProcessed }} </td>
                            <td class="">{{ $report->driverProcessed }} </td>
                            <td>{{ $report->roundTrips }} </td>
                            <td class="sensor recorder">{{ $sensorRecorder }}</td>
                            <td class="recorder text-center">
                                <span class="{{ count($issuesByVehicles) ? "text-warning click":""  }}" data-toggle="tooltip" data-html="true" data-title="@lang('Error in') {{ $issuesByVehicles->first()[0]->field ?? '' }}"
                                      onclick="{{ count($issuesByVehicles) ? "$('#issue-$date').collapse('show');":""  }}">
                                    {{ $recorder }}
                                </span>
                            </td>
                            <td class="sensor">{{ $sensor }}</td>
                            <td class="p-3 {{ $passengerReport->canLiquidate ? '' : 'hide' }}">
                                @if($report->vehicle)
                                    @if($report->issues->count())
                                        <div class="tooltips text-danger" data-html="true" data-title="<i class='fa fa-exclamation-triangle'></i> @lang('Please fix the issues first')">
                                            <a href="javascript:alert('@lang('Please fix the issues first')')" class="btn btn-sm btn-circle red  sbold uppercase disabled">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            </a>
                                        </div>
                                    @else
                                        <a href="http://www.pcwserviciosgps.com/pcw_gps/php/despachoDinamico/pdf/crearrecibopdf.php?action=descargarReciboFinal&empresa={{ $report->vehicle->company->short_name }}&ui={{ Auth::user()->id }}&n_carro={{ $report->vehicle->number }}&placa={{ $report->vehicle->plate }}&fecha_sel={{ $report->date }}"
                                           target="_blank" class="btn btn-sm btn-circle blue-hoki btn-outline sbold uppercase tooltips" data-html="true"
                                           data-title="<i class='fa fa-users'></i> @lang('Settlement receipt')">
                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                        </a>
                                    @endif
                                @endif
                            </td>
                        </tr>

                        @php
                            $currentFrame = $report->frame;
                            $comparedFrame = \App\Http\Controllers\PassengerReportCounterController::compareChangeFrames($currentFrame,$currentFrame);
                        @endphp

                        @if($currentFrame)
                        <tr id="collapse-{{ $date }}" class="bg-inverse text-white text-bold collapse-frame fade collapse">
                            <td colspan="5" style="font-family: monospace">
                                <span>
                                    @foreach($comparedFrame as $frame)
                                        <label class="p-0 text-center">
                                            <span class="text-center p-0 {{ $frame->class }}" data-title="@lang('Prev value'): <b>{{ $frame->prevField }}</b>" data-html="true" style="border-bottom: 1px dotted gray">
                                                {{ $frame->field }}
                                            </span>
                                            <br>
                                            <small class="text-muted p-t-3 btn-block" style="border: 1px dotted gray">
                                                {{ $loop->iteration }}
                                            </small>
                                        </label>
                                    @endforeach
                                </span>
                                <button class="btn btn-copy btn-sm btn-default pull-right tooltips" data-title="@lang('Copy frame')" data-clipboard-text="{{ $report->frame }}">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                    <tr class="inverse bg-default hide">
                        <td colspan="5" class="text-right">@lang('Average')</td>
                        <td class="text-center recorder">{{ number_format($passengerReport->totalRoundTrips/count($reports),1) }}</td>
                        <td class="text-center sensor recorder">{{ number_format($totalSensorRecorder->average(),1) }}</td>
                        <td class="text-center recorder">{{ number_format($totalRecorder->average(),1) }}</td>
                        <td class="text-center sensor">{{ number_format($totalSensor->average(),1) }}</td>
                    </tr>
                    <tr class="inverse bg-inverse text-white">
                        <td colspan="5" class="text-right text-uppercase">@lang('Totals')</td>
                        <td class="text-center recorder">{{ $passengerReport->totalRoundTrips }}</td>
                        <td class="text-center sensor recorder">{{ $totalSensorRecorder->sum() }}</td>
                        <td class="text-center recorder">{{ $totalRecorder->sum() }}</td>
                        <td class="text-center sensor">{{ $totalSensor->sum() }}</td>
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