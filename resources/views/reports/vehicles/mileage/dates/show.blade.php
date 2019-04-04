@if(count($mileageReport->reports))
    @php($reports = $mileageReport->reports)
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @if(false)
                        @foreach($reports as $vehicleId => $report)
                            @php( $vehicle = $report->vehicle )
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

            <h2 class="text-white text-bold">
                <i class="fa fa-road"></i> @lang('Report')
            </h2>

        </div>
        <div class="tab-content table-responsives">

            <div id="report-tab-{{ 0 }}" class="tab-pane fade {{ true ? 'active in' : '' }}">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                            <tr class="inverse">
                                <th>
                                    <i class="fa fa-calendar-o"></i><br>
                                    @lang('Date')
                                </th>
                                <th>
                                    <i class="fa fa-car"></i><br>
                                    @lang('Number')
                                </th>
                                <th>
                                    <i class="fa fa-credit-card"></i><br>
                                    @lang('Plate')
                                </th>
                                <th>
                                    <i class="fa fa-road"></i><br>
                                    @lang('Mileage') (km)
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reports as $report)
                                <tr>
                                    <td class="text-center">{{ $report->date }} </td>
                                    <td class="text-center">{{ $report->vehicleNumber }} </td>
                                    <td class="text-center">{{ $report->vehiclePlate }} </td>
                                    <td class="text-center">{{ number_format($report->mileage/1000,2, ',', '.') }} </td>
                                </tr>
                            @endforeach

                            <tr class="bg-inverse text-white">
                                <td class="text-right" colspan="3">@lang('Total')</td>
                                <td class="text-center">{{ number_format($mileageReport->mileageByFleet/1000,2, ',', '.') }} </td>
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