@php($reports = $passengerReport->reports)
@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('passengers-consolidated-export-report-range') }}?initial-date={{ $passengerReport->initialDate }}&final-date={{ $passengerReport->finalDate }}&company-report={{ $passengerReport->companyId }}" class="btn btn-lime bg-lime-dark btn-sm">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                <span class="hides">
                    <i class="fa fa-users" aria-hidden="true"></i>
                    @lang('Consolidated per date range')
                    <hr class="text-inverse-light">
                </span>

                <ul class="nav nav-pills nav-pills-success">
                    <li class="hide">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor,.recorder').show()">
                            <i class="fa fa-asterisk" aria-hidden="true"></i> @lang('All')
                        </a>
                    </li>
                    <li class="hide">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor').show();$('.recorder').hide()">
                            <i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')
                        </a>
                    </li>
                    <li class="active">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.recorder').show();$('.sensor').hide()">
                            <i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')
                        </a>
                    </li>
                </ul>
            </h5>
        </div>
        <div class="tab-content p-0">
            <div id="all-report-tab" class="table-responsive tab-pane active fade in">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th class="text-center">N°</th>
                        <th class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('Date')</th>
                        <th class="text-center sensor hide"><i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')</th>
                        <th class="text-center recorder"><i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')</th>
                        <th class="text-center sensor recorder hide"><i class="fa fa-minus" aria-hidden="true"></i> @lang('Difference')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalSensor = collect([]);
                        $totalRecorder = collect([]);
                    @endphp
                    @foreach($reports as $date => $report)
                        @php
                            $sensor = 0;
                            $recorder = $report->total;
                            $sensor > 0 ? $totalSensor->push($sensor):null;
                            $recorder > 0 ? $totalRecorder->push($recorder):null;
                            $invalidRecorder = count($report->violations);
                            $messageRecorder = $invalidRecorder?
                                "<br>".__('Verify possible error in register data')."<hr class='hr'>".
                                "<div class='text-left'>".
                                    "<b>".$report->violations->first()->route->name."</b><br>".
                                    "<b>".__('Turn')."</b> ".$report->violations->first()->turn."<br>".
                                    "<b>".__('Round Trip')."</b> ".$report->violations->first()->round_trip."<br>".
                                    "<b>".__('Vehicle')."</b> ".$report->violations->first()->vehicle->number."<br>".
                                    "<b>".__('Arrived Recorder')."</b> ".$report->violations->first()->end_recorder."<br>".
                                "</div>"
                                :"";
                        @endphp
                        <tr class="text-center">
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $date }} </td>
                            <td class="sensor hide">{{ $sensor }}</td>
                            <td class="recorder">
                                <span class="{{ $invalidRecorder ? "text-warning":""  }}" data-toggle="tooltip" data-html="true" title="{{ $messageRecorder }}">
                                    {{ $recorder }}
                                </span>
                            </td>
                            <td class="sensor recorder hide">{{ abs($sensor - $recorder) }}</td>
                        </tr>
                    @endforeach
                    <tr class="inverse bg-inverse-light text-white">
                        <td colspan="2" class="text-right">@lang('Total passengers')</td>
                        <td class="text-center sensor hide">{{ $totalSensor->sum() }}</td>
                        <td class="text-center recorder">{{ $totalRecorder->sum() }}</td>
                        <td rowspan="2" class="sensor recorder hide"></td>
                    </tr>
                    <tr class="inverse bg-inverse text-white">
                        <td colspan="2" class="text-right">@lang('Average')</td>
                        <td class="text-center sensor hide">{{ number_format($totalSensor->average(),1) }}</td>
                        <td class="text-center recorder">{{ number_format($totalRecorder->average(),1) }}</td>
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