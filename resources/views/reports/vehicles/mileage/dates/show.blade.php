@if(count($mileageReport->reports))
    @php
        $reports = $mileageReport->reports;
    @endphp
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @if(false)
                        @foreach($reports as $vehicleId => $report)
                            @php
                                $vehicle = $report->vehicle;
                            @endphp
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-placement="bottom"
                                   data-original-title="{{ $vehicle->plate }}">
                                    <i class="fa fa-car f-s-8 icon-report"></i><span class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong>{{ $vehicle->number }}</strong>
                                </a>
                            </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
            </div>
            <div class="panel-heading-btn">
                <a href="{{ route('report-vehicle-mileage-show-date-range') }}?company={{ $mileageReport->companyReport }}&vehicle-report={{ $mileageReport->vehicleReport  }}&initial-date-report={{ $mileageReport->initialDateReport }}&final-date-report={{ $mileageReport->finalDateReport }}&export=true" class="btn btn-lime btn-sm bg-lime-dark btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <small class="text-white label-vehicles hide"><i class="fa fa-car"></i> {{ count($reports) }} @lang('Dates')  <i class="fa fa-road"></i> {{ number_format($mileageReport->mileageByFleet,2, ',', '.') }} Km @lang('in total')</small>

            <h1 class="text-white text-bold" style="font-size: 1.2em !important;">
                <i class="fa fa-road"></i> @lang('Report') @lang('mileage')
            </h1>

        </div>
        <div class="tab-content table-responsives">

            <div id="report-tab-{{ 0 }}" class="tab-pane fade {{ true ? 'active in' : '' }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-repodrt">
                        <thead>
                            <tr class="inverse">
                                <th class="text-center">
                                    <i class="fa fa-list-o"></i><br>
                                    @lang('#')
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-calendar-o"></i><br>
                                    @lang('Date')
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-car"></i><br>
                                    @lang('Number')
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-credit-card"></i><br>
                                    @lang('Plate')
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-car"></i><br>
                                    @lang('Status')
                                </th>
                                <th class="text-center">
                                    <i class="fa fa-road"></i><br>
                                    @lang('Mileage') (km)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }} </td>
                                    <td class="text-center">{{ $report->date }} </td>
                                    <td class="text-center">{{ $report->vehicleNumber }} </td>
                                    <td class="text-center">{{ $report->vehiclePlate }} </td>
                                    <td class="text-center">
                                        @php
                                            $infoReportVehicleStatus = "";
                                        @endphp
                                        @if($report->reportVehicleStatus)
                                            @php
                                                foreach ($report->reportVehicleStatus as $r){
                                                    $infoReportVehicleStatus .= "$r->status ".__('by')." $r->updated_by <br>";
                                                }
                                            @endphp
                                        @endif
                                        <span class="badge badge-{{ $report->vehicleIsActive ? 'success' : 'warning' }} tooltips" data-html="true" title="{{ $infoReportVehicleStatus }}" style="text-transform:none">
                                            {{ $report->vehicleStatus }}
                                            @if($infoReportVehicleStatus)
                                                <i class="fa fa-exclamation-circle"></i>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="text-center {{ $report->hasReports ? '':'text-warning tooltips' }}"
                                        data-title="{{ $report->hasReports ? '':__('No GPS reports found') }}">
                                        {{ number_format($report->mileage/1000,2, ',', '.') }} </td>
                                </tr>
                            @endforeach

                            <tr class="bg-inverse text-white">
                                <td class="text-right" colspan="5">@lang('TOTAL')</td>
                                <td class="text-center">{{ number_format($mileageReport->mileageByFleet/1000,2, ',', '.') }} Km</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="application/javascript">
        hideSideBar();
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif