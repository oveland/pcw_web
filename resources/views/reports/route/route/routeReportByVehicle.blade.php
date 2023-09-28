@php use App\Models\Company\Company; @endphp
@if(count($dispatchRegistersByVehicles))
    <div class="alert alert-warning p-t-10 container-alert-new-values"
         style="display: none;position: absolute;z-index: 10;margin-top: 0">
        <strong>
            <i class="fa fa-exclamation-circle"></i> @lang('Registers updated')
        </strong>
        <hr>
        <p>@lang('Please refresh the report once you finish the update all data')</p>
        <hr>
        <button class="btn btn-warning btn-sm" onclick="$('.form-search-report').submit()">
            <i class="fa fa-refresh"></i> @lang('Update')
        </button>
    </div>

    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                @if($company->canCreateDR())
                <button class="btn btn-circle tooltips btn-primary btn-add-dr"
                        data-title="@lang('Create round trip')">
                    <i class="fa fa-plus-square"></i>
                </button>
                @endif
                <a href="{{ route('report-route-search') }}?company-report={{ $company->id }}&date-report={{ urlencode($dateTimeRequest) }}&date-end-report={{ urlencode($dateTimeEndRequest) }}&time-range-report={{ $timeReport }}&with-end-date={{ $withEndDate }}&route-report={{ $routeReport }}&vehicle-report={{ $vehicleReport }}&spreadsheet-report={{ $spreadsheetReport }}&completed-turns={{ $completedTurns }}&active-turns={{ $activeTurns }}&cancelled-turns={{ $cancelledTurns }}&type-report=group-vehicles&export=true"
                   class="btn green btn-circle tooltips"
                   data-title="@lang('Export grouped report') | @lang('Excel')">
                    <i class="fa fa-download"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success m-0">
                        @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                            @php
                                $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId);
                            @endphp
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true"
                                   class="tooltips" data-placement="bottom"
                                   data-original-title="{{ $vehicle->plate }}"
                                   data-vehicle-id="{{ $vehicle->id }}"
                                   data-vehicle-number="{{ $vehicle->number }}"
                                >
                                    <i class="fa fa-car f-s-8 pull-right icon-report icon-car-{{ $vehicleId }}"></i><span
                                            class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong>{{ $vehicle->number }}</strong>

                                    @if(Auth::user()->isSuperAdmin())
                                        <span class="car-ss car-ss-percent-{{ $vehicleId }} hide">
                                            <i class="fa fa-signal faa-flash animated"></i>
                                        </span>
                                        <span class="car-nr car-nr-{{ $vehicleId }} hide">
                                            <i class="fa fa-clock-o red faa-flash animated"></i>
                                        </span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegisters)
                <div id="report-tab-{{ $vehicleId }}"
                     class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                    @if($company->id == 39)
                        @include('reports.route.route.templates.EP._tableReportEP',compact('dispatchRegisters', 'reportsByVehicle', 'company', 'withEndDate'))
                    @else
                        @include('reports.route.route.templates._tableReport',compact('dispatchRegisters', 'reportsByVehicle', 'company', 'withEndDate'))
                    @endif
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>
    <script>$('#company-report').val({{ $company->id }})</script>
@else
    <div class="col-md-12 text-center">
        @include('partials.alerts.noRegistersFound', ['message' => 'There are not dispatch registers on this date'])
    </div>

    @if($company->canCreateDR())
        <div class="col-md-12 text-center">
            <button class="btn btn-circle btn-primary btn-add-dr">
                <i class="fa fa-plus-square"></i> @lang('Create round trip')
            </button>
        </div>
    @endif
    <br>
    @if( Auth::user()->canMakeTakings())
        <div class="col-md-12 text-center">
            @if($vehicleReport && $vehicleReport != 'all' && $dateReport)
                <a id="btn-taking-empty" href="#modal-takings-passengers" data-toggle="modal"
                   onclick="showTakingsForm('{{ route("operation-routes-takings-form-create", ["vehicle" => $vehicleReport, 'date' => $dateReport]) }}')"
                   class="btn purple-sharp btn-outline sbold uppercase faa-parent animated-hover btn-circle tooltips">
                    <i class="icon-briefcase faa-ring" style="margin-right: 0; margin-left: 0px"></i>
                    <i class="fa fa-dollar faa-vertical" style="margin-right: 0px; margin-left: 0"></i>
                    @lang('Register takings')
                </a>
            @else
                <p>@lang('To register takings you must be select a vehicle')</p>
            @endif
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

@if($company->canCreateDR() && $company->equal(Company::EXPRESO_PALMIRA))
    @include('reports.route.route.templates.EP._formCreateDR')
@endif