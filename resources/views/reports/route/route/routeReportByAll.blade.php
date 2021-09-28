
@if(count($dispatchRegistersByVehicles))
    <div class="alert alert-info p-t-5 container-alert-new-values" style="display: none;position: absolute;z-index: 10;">
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
            <h6 class="pull-left">
                {{ count($dispatchRegistersByVehicles) }} @lang('Vehicles')
            </h6>

            <div class="panel-heading-btn pull-right">
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ $dateReport }}&date-end-report={{ $dateEndReport }}&time-range-report={{ $timeReport }}&with-end-date={{ $withEndDate }}&route-report={{ $routeReport }}&vehicle-report={{ $vehicleReport }}&completed-turns={{ $completedTurns }}&type-report=ungrouped-vehicles&export=true"
                   class="btn green btn-circle tooltips"
                   data-title="@lang('Export ungrouped report') | @lang('Excel')">
                    <i class="fa fa-download"></i>
                </a>
            </div>
        </div>

        <div class="tab-content panel p-0">
            <div id="report-tab" class="table-responsive tab-pane fade active in">
                @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                    <!-- begin table -->
                    @include('reports.route.route.templates._tableReport',compact('dispatchRegisters', 'reportsByVehicle', 'company', 'withEndDate'))

                    <hr class="hr no-padding">

                    <!-- end table -->
                @endforeach
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound', ['message' => 'There are not dispatch registers on this date'])

    @if( Auth::user()->canMakeTakings())
        <div class="m-b-10 mb-10 mt-10 col-md-6 col-md-offset-3 offset-md-3" style="position: relative;top: -60px;z-index: 10000;">
            <div class="col-md-12">
                <div class="details col-md-8 col-md-offset-3">
                    @if($vehicleReport && $vehicleReport != 'all' && $dateReport)
                        <a id="btn-taking-empty" href="#modal-takings-passengers" data-toggle="modal" onclick="showTakingsForm('{{ route("operation-routes-takings-form-create", ["vehicle" => $vehicleReport, 'date' => $dateReport]) }}')"
                           class="btn purple-sharp btn-outline sbold uppercase faa-parent animated-hover btn-circle tooltips">
                            <i class="icon-briefcase faa-ring" style="margin-right: 0; margin-left: 0px"></i>
                            <i class="fa fa-dollar faa-vertical" style="margin-right: 0px; margin-left: 0"></i>

                            @lang('Register takings')
                        </a>
                    @else
                        <p>@lang('To register takings you must be select a vehicle')</p>
                    @endif
                </div>
            </div>
        </div>
        <script>
            function showTakingsForm(url) {
                let modalTakingsPassengers = $('#modal-takings-passengers');
                let modalBody = modalTakingsPassengers.find('.modal-body');
                modalBody.html($('.loading').html()).load(url);
            }
        </script>
    @endif
@endif
