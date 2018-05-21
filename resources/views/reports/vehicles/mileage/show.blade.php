@if(count($mileageReport->reports))
    @php($reports = $mileageReport->reports)
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
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
                    </ul>
                </div>
            </div>
            <hr class="hr">
            <div class="panel-heading-btn">
                <a href="{{ route('report-vehicle-mileage-show') }}?company={{ $mileageReport->company->id }}&date-report={{ $mileageReport->dateReport }}&export=true" class="btn btn-lime btn-sm bg-lime-dark btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <small class="text-white label-vehicles"><i class="fa fa-car"></i> {{ count($reports) }} @lang('Vehicles')  <i class="fa fa-road"></i> {{ number_format($mileageReport->mileageByFleet,2, ',', '.') }} Km @lang('in the fleet')</small>
        </div>
        <div class="tab-content panel">
            @foreach($reports as $report)
                @php
                    $vehicle = $report->vehicle;
                    $reportByRoutes = $report->byRoutes;
                @endphp
                <div id="report-tab-{{ $vehicle->id }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <h4><i class="fa fa-road"></i> {{ number_format($report->mileage,2) }} Km @lang('in the day')</h4>
                    <hr class="hr">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                            <thead>
                            <tr class="inverse">
                                <th>
                                    <i class="fa fa-flag"></i><br>
                                    @lang('Route')
                                </th>
                                <th>
                                    <i class="fa fa-list"></i><br>
                                    @lang('Turn')
                                </th>
                                <th>
                                    <i class="fa fa-exchange"></i><br>
                                    @lang('Round trip')
                                </th>
                                <th>
                                    <i class="fa fa-tachometer"></i><br>
                                    @lang('Status')
                                </th>
                                <th>
                                    <i class="fa fa-road"></i><br>
                                    @lang('Mileage')
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reportByRoutes as $dispatchRegisterId => $reportByRoute)
                                @php
                                    $route = $reportByRoute->route;
                                    $dispatchRegister = $reportByRoute->dispatchRegister;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $route->name }} </td>
                                    <td class="text-center">{{ $dispatchRegister->turn }} </td>
                                    <td class="text-center">{{ $dispatchRegister->round_trip }} </td>
                                    <td class="text-center">{!! $dispatchRegister->status !!} </td>
                                    <td class="text-center">{{ number_format($reportByRoute->mileage,2) }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-inverse text-white">
                                <td class="text-right" colspan="4">@lang('Total')</td>
                                <td class="text-center">{{ $report->mileageByAllRoutes }} </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script type="application/javascript">
        hideSideBar();
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif