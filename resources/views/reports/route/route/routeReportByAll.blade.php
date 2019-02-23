
@if(count($dispatchRegisters))
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
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&route-report={{ $route->id ?? $route }}&type-report=vehicle&export=true" class="btn btn-sm btn-lime bg-lime-dark btn-rounded pull-left hide">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
        </div>

        <div class="tab-content panel p-0">
            <div id="report-tab" class="table-responsive tab-pane fade active in">
                <!-- begin table -->
                @include('reports.route.route.templates._tableReport',compact('dispatchRegisters', 'reportsByVehicle', 'company', 'route', 'dateReport', 'routeReport', 'typeReport'))
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif
