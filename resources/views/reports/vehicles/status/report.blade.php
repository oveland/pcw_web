@if(count($vehicleStatusReports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="text-white m-t-10">
                <ul class="nav nav-pills nav-pills-success">
                    @foreach($vehicleStatusReports as $vehicleId => $vehicleStatusReport)
                    @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                    <li class="{{ $loop->first ? 'active':'' }} tooltips" data-title="{{ $vehicle->plate }} ({{ $vehicle->company->short_name }})">
                        <a href="#vehicle-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true">
                            <i class="fa fa-car" aria-hidden="true"></i> {{ $vehicle->number }}
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="tab-content p-0">
            @foreach($vehicleStatusReports as $vehicleId => $vehicleStatusReport)
                @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                <div id="vehicle-{{ $vehicle->id }}" class="table-responsive tab-pane fade {{ $loop->first ? 'active in':'' }}">
                <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                        <tr class="inverse">
                            <th class="text-center">N°</th>
                            <th class="text-center"><i class="fa fa-clock-o" aria-hidden="true"></i> @lang('Time')</th>
                            <th class="text-center"><i class="fa fa-podcast" aria-hidden="true"></i> @lang('Status')</th>
                            <th class="text-center"><i class="fa fa-map-marker" aria-hidden="true"></i> @lang('Latitude')</th>
                            <th class="text-center"><i class="fa fa-map-marker" aria-hidden="true"></i> @lang('Longitude')</th>
                            <th class="text-center"><i class="fa fa-tachometer" aria-hidden="true"></i> @lang('Speed')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($vehicleStatusReport as $report)
                            <tr class="text-center">
                                <td >{{ $loop->iteration }}</td>
                                <td >{{ $report->time }}</td>
                                <td >{{ $report->status->des_status }}</td>
                                <td >{{ $report->latitude }}</td>
                                <td >{{ $report->longitude }}</td>
                                <td >{{ $report->speed }}</td>
                            </tr>
                        @endforeach
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