@php($reports = $passengerReport->reports)
@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-passengers-recorders-consolidated-date-range-export') }}?initial-date={{ $passengerReport->initialDate }}&final-date={{ $passengerReport->finalDate }}&company-report={{ $passengerReport->companyId }}" class="btn btn-lime bg-lime-dark btn-sm">
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
                            $issuesByVehicles = $report->issues;
                            $sensor > 0 ? $totalSensor->push($sensor):null;
                            $recorder > 0 ? $totalRecorder->push($recorder):null;
                        @endphp

                        @if(count($issuesByVehicles))
                            <tr>
                                <td colspan="3">
                                    <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
                                        <i class="fa fa-exclamation-circle"></i>
                                        <strong>@lang('Warning'):</strong>
                                        @lang('There are issues in data recorder') ({{ $date }}). <a data-toggle="collapse" data-target="#issue-{{ $date }}" class="text-bold text-warning click">@lang('See details')</a>
                                    </div>
                                    <div id="issue-{{ $date }}" class="panel-collapse collapse" aria-expanded="false" style="height: 0px;">
                                        <div class="panel-body p-0">
                                            @include('partials.alerts.reports.passengers.issuesByVehicles',compact('issuesByVehicles'))
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif

                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $date }} </td>
                            <td class="recorder text-center">
                                <span class="{{ count($issuesByVehicles) ? "text-warning click":""  }}" data-toggle="tooltip" data-html="true" data-title="@lang('Error in') {{ $issuesByVehicles->first()[0]->field ?? '' }}"
                                      onclick="{{ count($issuesByVehicles) ? "$('#issue-$date').collapse('show');":""  }}">
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