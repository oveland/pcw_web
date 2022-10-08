@php
    $reports = $passengerReport->reports;
    $issuesByVehicles = $passengerReport->issues;
    $company = $passengerReport->company;
@endphp
@if(count($reports))
    <style>
        @media (min-width: 600px) {
            .table {
                position: relative !important;
                background: white;
                border: 1px  black !important;
            }

            .table th {
                position: sticky !important;
                position: -webkit-sticky !important;
                z-index: 2 !important;
                top: 46px !important; /* Don't forget this, required for the stickiness */
                box-shadow: -3px 6px 6px 1px rgba(0, 0, 0, 0.8);
                border: 1px #2b3643 !important;
            }

            .table tfoot td {
                position: sticky !important;
                position: -webkit-sticky !important;
                z-index: 2 !important;
                bottom: 34px !important; /* Don't forget this, required for the stickiness */
                box-shadow: -3px 6px 6px 1px rgba(0, 0, 0, 0.8);
                background: #2b3643f7;
                border: 1px #2b3643 !important;
            }
        }

        @media (max-width: 600px) {
            .table-responsive-xs {
                overflow-x: auto;
            }
        }
    </style>


    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-passengers-recorders-consolidated-date-range-search') }}?export=true&company-report={{ $passengerReport->companyId }}&route-report={{ $passengerReport->routeReport }}&driver-report={{ $passengerReport->driverReport }}&vehicle-report={{ $passengerReport->vehicleReport }}&date-report={{ $passengerReport->dateReport }}&date-end-report={{ $passengerReport->dateEndReport }}&group-by-vehicle={{ $passengerReport->groupByVehicle }}&group-by-route={{ $passengerReport->groupByRoute }}&group-by-driver={{ $passengerReport->groupByDriver }}&group-by-date={{ $passengerReport->groupByDate }}&with-end-date={{ $passengerReport->withEndDate }}"
                   class="btn btn-success btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-download"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                @if($company->hasRecorderCounter())
                    <soan class="text-bold text-white">
                        <i class="fa fa-compass" aria-hidden="true"></i> {{ $passengerReport->totalRecorder }}
                    </soan>
                @endif
                @if($company->hasSensorCounter())
                    <soan class="text-bold text-white">
                        &#8226; <i class="fa fa-crosshairs" aria-hidden="true"></i> {{ $passengerReport->totalSensor }}
                    </soan>
                @endif

                @if($passengerReport->route)
                    <small class="text-white text-bold" style="font-size: 1em">
                        &#8226; <i class="fa fa-flag" aria-hidden="true"></i> {{ $passengerReport->route->name }}
                    </small>
                @endif
                @if($passengerReport->vehicle)
                    <small class="text-white" style="font-size: 1em">
                        &#8226; <i class="fa fa-bus" aria-hidden="true"></i> {{ $passengerReport->vehicle->number }}
                    </small>
                @endif
                @if($passengerReport->driver)
                <small class="text-white text-bold" style="font-size: 1em">
                    &#8226; <i class="fa fa-user" aria-hidden="true"></i> {{ $passengerReport->driver->fullName }}
                </small>
                @endif
            </h5>
        </div>
        <div class="tab-content p-0">
            @if(count($issuesByVehicles) && $company->hasRecorderCounter())
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

            <div id="all-report-tab" class="table-responsive-xs table-report tab-pane active fade in">
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
                            <i class="fa fa-bus" aria-hidden="true"></i><br> @lang('Vehicle')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-flag" aria-hidden="true"></i><br> @lang('Route')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-user" aria-hidden="true"></i><br> @lang('Driver')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-car" aria-hidden="true"></i><br> @lang('Total') <br> @lang('vehicles')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-calendar-check-o" aria-hidden="true"></i><br> @lang('Total') <br> @lang('days')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-retweet" aria-hidden="true"></i><br> @lang('Total') <br> @lang('Round trips')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-road" aria-hidden="true"></i><br> @lang('Mileage round trips')
                        </th>
                        @if($company->hasMileageScheduled())
                            <th class="text-center">
                                <i class="fa fa-road" aria-hidden="true"></i><br> @lang('Mileage programmed')
                            </th>
                            <th class="text-center">
                                <i class="fa fa-road" aria-hidden="true"></i><br> @lang('Difference mileage')
                            </th>
                            <th class="text-center hide">
                                <i class="fa fa-crosshairs" aria-hidden="true"></i> <i class="fa fa-compass" aria-hidden="true"></i><br> @lang('Sensor recorder')
                            </th>
                        @endif
                        <th class="text-center recorder">
                            <i class="fa fa-compass" aria-hidden="true"></i>
                            <i class="icon-users text-muted"></i>
                            <br> @lang('Recorder')
                        </th>
                        @if($company->hasSensorCounter())
                            <th class="text-center sensor">
                                <i class="fa fa-crosshairs" aria-hidden="true"></i>
                                <i class="icon-users text-muted"></i>
                                <br> @lang('Sensor')
                            </th>
                        @endif
                        @if($company->hasSensorCounter())
                            <th class="text-center sensor">
                                <i class="fa fa-crosshairs" aria-hidden="true"></i>
                                <i class="icon-users text-muted"></i>
                                <br> @lang('Sensor TOTAL')
                            </th>
                        @endif
                        @if($company->hasSensorRecorderCounter())
                            <th class="text-center sensor">
                                <i class="fa fa-dollar text-muted"></i>
                                <i class="icon-users text-muted"></i>
                                <br> {{ $company->getSensorRecorderCounterLabel() }}
                            </th>
                        @endif

                        @if($company->hasRecorderCounter())
                            <th class="text-center">
                                <i class="fa fa-line-chart" aria-hidden="true"></i><br> @lang('IPK')
                            </th>

                            <th class="p-3 {{ $passengerReport->canLiquidate ? '' : 'hide' }}">
                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i><br> @lang('Settlement daily receipt')
                            </th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalSensor = collect([]);
                        $totalAllSensor = collect([]);
                        $totalRecorder = collect([]);
                        $totalSensorRecorder = collect([]);
                    @endphp
                    @foreach($reports as $date => $report)
                        @php
                            $sensorRecorder = $report->totalBySensorRecorder;
                            $recorder = $report->totalByRecorder;
                            $sensor = $report->totalBySensor;
                            $allSensor = $report->totalAllBySensor;
                            $issuesByVehicles = $report->issues;

                            $sensorRecorder > 0 ? $totalSensorRecorder->push($sensorRecorder) : null;
                            ($recorder > 0 && !count($issuesByVehicles)) ? $totalRecorder->push($recorder) : null;
                            $sensor > 0 ? $totalSensor->push($sensor) : null;
                            $allSensor > 0 ? $totalAllSensor->push($allSensor) : null;
                        @endphp

                        <tr class="text-center click" data-toggle="collapse" data-target="#collapse-{{ $date }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $report->date }} </td>
                            <td>{{ $report->vehicleProcessed }} </td>
                            <td>{{ $report->routeProcessed }} </td>
                            <td class="">{{ $report->driverProcessed }} </td>
                            <td>{{ $report->totalVehicles }} </td>
                            <td>{{ $report->totalDates }} </td>
                            <td>{{ $report->roundTrips }} </td>
                            <td>{{ number_format($report->mileage, 1) }} </td>
                            @if($company->hasMileageScheduled())
                                <td>{{ number_format($report->programmedMileage, 1) }} </td>
                                <td>{{ number_format($report->differenceMileage, 1) }} </td>
                            @endif
                            <td class="recorder text-center">
                                    <span class="{{ count($issuesByVehicles) ? "text-warning click tooltips":""  }}" data-html="true" data-title="@lang('Error in') {{ $issuesByVehicles->first()[0]->field ?? '' }}"
                                          onclick="{{ count($issuesByVehicles) ? "$('#issue-$date').collapse('show');":""  }}">
                                        {{ number_format($recorder, 0) }}
                                    </span>
                            </td>

                            @if($company->hasSensorCounter())
                                <td class="sensor">{{ $sensor }}</td>
                            @endif
                            @if($company->hasSensorCounter())
                                <td class="sensor">{{ $allSensor }}</td>
                            @endif
                            @if($company->hasSensorRecorderCounter())
                                <td class="sensor-recorder">{{ $sensorRecorder }}</td>
                            @endif

                            @if($company->hasRecorderCounter())
                                <td class="">{{ number_format($report->IPK, 2) }}</td>

                                <td class="p-3 {{ $passengerReport->canLiquidate ? '' : 'hide' }}">
                                    @if($report->vehicle)
                                        @if($report->issues->count())
                                            <div class="tooltips text-danger" data-html="true" data-title="<i class='fa fa-exclamation-triangle'></i> @lang('Please fix the issues first')">
                                                <a href="javascript:alert('@lang('Please fix the issues first')')" class="btn btn-sm btn-circle red  sbold uppercase disabled">
                                                    <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                </a>
                                            </div>
                                        @else
                                            <a href="https://www.pcwserviciosgps.com/pcw_gps/php/despachoDinamico/pdf/crearrecibopdf.php?action=descargarReciboFinal&empresa={{ $report->vehicle->company->short_name }}&ui={{ Auth::user()->id }}&n_carro={{ $report->vehicle->number }}&placa={{ $report->vehicle->plate }}&fecha_sel={{ $report->date }}"
                                               target="_blank" class="btn btn-sm btn-circle blue-hoki btn-outline sbold uppercase tooltips" data-html="true"
                                               data-title="<i class='fa fa-users'></i> @lang('Settlement receipt')">
                                                <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                            </a>
                                        @endif
                                    @endif
                                </td>
                            @endif
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
                    </tbody>
                    <tfoot>
                    <tr class="inverse bg-inverse text-white">
                        <td colspan="5" class="text-right text-uppercase">@lang('Totals')</td>
                        <td class="text-center recorder">{{ $passengerReport->totalVehicles }}</td>
                        <td class="text-center">
                            {{ $passengerReport->totalDates }} {{ __(trans_choice('day|days', $passengerReport->totalDates))  }}
                        </td>
                        <td class="text-center recorder">{{ $passengerReport->totalRoundTrips }}</td>
                        <td class="text-center recorder">{{ number_format($passengerReport->totalMileage,1) }}</td>
                        @if($company->hasMileageScheduled())
                            <td class="text-center recorder">{{ number_format($passengerReport->totalProgrammedMileage,1) }}</td>
                            <td class="text-center recorder">{{ number_format($passengerReport->totalDifferenceMileage,1) }}</td>
                            <td class="text-center sensor recorder hide">{{ $totalSensorRecorder->sum() }}</td>
                        @endif
                        <td class="text-center recorder">{{ number_format($passengerReport->totalRecorder, 0) }}</td>

                        @if($company->hasSensorCounter())
                            <td class="text-center sensor">{{ $totalSensor->sum() }}</td>
                        @endif
                        @if($company->hasSensorCounter())
                            <td class="text-center sensor">{{ $totalAllSensor->sum() }}</td>
                        @endif
                        @if($company->hasSensorRecorderCounter())
                            <td class="text-center sensor">{{ $totalSensorRecorder->sum() }}</td>
                        @endif

                        @if($company->hasRecorderCounter())
                            <td class="text-center recorder">{{ number_format($passengerReport->IPK,2) }}</td>
                            <td class="text-center {{ $passengerReport->canLiquidate ? '' : 'hide' }}"></td>
                        @endif
                    </tr>
                    </tfoot>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>

    <style>
        .sensor{
            /*display: none;*/
        }
    </style>
@else
    @include('partials.alerts.noRegistersFound')
@endif
