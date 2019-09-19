@if(count($dispatchRegistersByVehicles))
    <div class="col-md-12 alert alert-info p-t-5 container-alert-new-values" style="display: none">
        <strong>
            <i class="fa fa-exclamation"></i> @lang('Registers updated')
        </strong>
        <button class="btn btn-info btn-xs" onclick="$('.form-search-report').submit()">
            <i class="fa fa-refresh"></i>
        </button>
        <p>@lang('Please refresh the report once you finish the fix bugs')</p>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&route-report={{ $routeReport }}&vehicle-report={{ $vehicleReport }}&completed-turns={{ $completedTurns }}&type-report=group-vehicles&export=true" class="btn btn-sm btn-primary btn-rounded pull-left tooltips" data-title="@lang('Export grouped report')">
                    <i class="fa fa-file-excel-o"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                            @php
                                $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId);
                            @endphp
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-placement="bottom"
                                   data-original-title="{{ $vehicle->plate }}">
                                    <i class="fa fa-car f-s-8 icon-report icon-car-{{ $vehicleId }}"></i><span class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong>{{ $vehicle->number }}</strong>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                <div id="report-tab-{{ $vehicleId }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                @include('reports.route.route.templates._tableReport',compact('dispatchRegisters', 'reportsByVehicle', 'company'))
                <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif