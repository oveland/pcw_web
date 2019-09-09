
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
        <div class="panel-heading p-b-40">
            <div class="pull-left panel-title">
                <h2 class="m-t-10 uppercase text-bold">
                    @lang('Dispatch report')
                </h2>
            </div>
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&route-report={{ $routeReport }}&vehicle-report={{ $vehicleReport }}&completed-turns={{ $completedTurns }}&type-report=ungrouped-vehicles&export=true" class="btn btn-sm btn-info btn-rounded pull-left tooltips" data-title="@lang('Export ungrouped report')">
                    <i class="fa fa-file-excel-o"></i>
                </a>
            </div>
        </div>

        <div class="tab-content panel p-0">
            <div id="report-tab" class="table-responsive tab-pane fade active in">
                @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                    <!-- begin table -->
                    @include('reports.route.route.templates._tableReport',compact('dispatchRegisters', 'reportsByVehicle', 'company'))

                    <hr class="hr no-padding">

                    <!-- end table -->
                @endforeach
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif
