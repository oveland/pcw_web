@if(count($vehiclesData))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-sm btn-circle btn-default" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                <span class="hides">
                    <i class="fa fa-car" aria-hidden="true"></i>
                    @lang('Vehicles without turns')
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
            <div class="table-responsive">
            <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th class="text-center">N°</th>
                        <th class="text-center">
                            <i class="fa fa-car" aria-hidden="true"></i> @lang('Vehicle')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-flag" aria-hidden="true"></i> @lang('Route') (Predefinida)
                        </th>
                        <th class="text-center sensor">
                            <i class="fa fa-road" aria-hidden="true"></i> @lang('Mileage day')
                        </th>
                        <th class="text-center sensor">
                            <i class="fa fa-road" aria-hidden="true"></i> @lang('Mileage in time range')
                        </th>
                        <th class="text-center sensor">
                            <i class="fa fa-rocket" aria-hidden="true"></i> @lang('Options')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($vehiclesData as $data)
                        @php
                            $vehicle = $data->last->vehicle;
                            $last = $data->last;
                        @endphp
                        <tr class="text-center">
                            <td >{{ $loop->iteration }}</td>
                            <td >{{ $vehicle->number }}</td>
                            <td >{{ $vehicle->dispatcherVehicle && $vehicle->dispatcherVehicle->route ? $vehicle->dispatcherVehicle->route->name : '' }}</td>
                            <td >{{ intval($last->current_mileage/1000) }}</td>
                            <td>{{ number_format($data->kmInTimeRange/1000, 1) }}</td>

                            <td class="p-3">
                                <div class="tooltips" data-html="true" data-title="@lang('See historic report')">
                                    <a href="{{ route('report-route-historic') }}?c={{ $company->id }}&d={{ $dateReport }}&v={{ $vehicle->id }}&i={{ $timeRange->initial }}&f={{ $timeRange->final }}&s=400" target="_blank" class="btn btn-sm btn-circle blue-hoki btn-outline sbold uppercase">
                                        <i class="fa fa-map-o" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
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