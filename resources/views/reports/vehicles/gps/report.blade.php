@if(count($reportByVehicles))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn hide">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-info " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="text-white m-t-10">
                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat green">
                            <div class="visual">
                                <i class="fa fa-check faa-float animated"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" data-value="{{ $statistics->gpsOK }}">{{ $statistics->gpsOK }}</span> <small style="font-size: 0.5em">@lang('of') {{ $reportByVehicles->count() }}</small>
                                </div>
                                <div class="desc"> {{ number_format($statistics->percentOK, 1, '.', '') }}% @lang('Vehicles with GPS OK') </div>
                            </div>
                            <a class="more" href="javascript: $('.vehicles').show();$('.vehicles-bad').fadeOut();"> @lang('See') +
                                <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat red">
                            <div class="visual">
                                <i class="fa fa-exclamation-circle faa-pulse animated"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" data-value="{{ $statistics->gpsOK }}">{{ $statistics->gpsBAD }}</span> <small style="font-size: 0.5em">@lang('of') {{ $reportByVehicles->count() }}</small>
                                </div>
                                <div class="desc"> {{ number_format($statistics->percentBAD, 1, '.', '') }}% @lang('Vehicles with errors in GPS') </div>
                            </div>
                            <a class="more" href="javascript:$('.vehicles').show();$('.vehicles-ok').fadeOut();"> @lang('See') +
                                <i class="m-icon-swapright m-icon-white"></i>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat blue">
                            <div class="visual">
                                <i class="fa fa-map-o faa-pulse animated"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" data-value="{{ $statistics->averageLocations }}">{{ $statistics->averageLocations }}</span>
                                </div>
                                <div class="desc"> @lang('Locations per vehicle') </div>
                            </div>
                            <a class="more text-right" href="javascript:;">
                                <span class="">@lang('Average daily')</span>
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                        <div class="dashboard-stat purple">
                            <div class="visual">
                                <i class="fa fa-road faa-float animated"></i>
                            </div>
                            <div class="details">
                                <div class="number">
                                    <span data-counter="counterup" data-value="{{ $statistics->averageKm }}">{{ $statistics->averageKm }} Km</span>
                                </div>
                                <div class="desc"> @lang('Mileage per vehicle') </div>
                            </div>
                            <a class="more text-right" href="javascript:;">
                                <span class="">@lang('Average daily')</span>
                            </a>
                        </div>
                    </div>
                </div>

                <ul class="nav nav-pills nav-pills-success nav-vehicles">
                    @foreach($reportByVehicles as $vehicleId => $reportByVehicle)
                    @php
                        $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId)
                    @endphp
                    <li class="{{ $loop->first ? 'active':'' }} tooltips col-lg-1 col-md-2 col-sm-4 col-xs-12 p-1 vehicles vehicles-{{ $reportByVehicle->isOK ? 'ok':'bad' }}" style="margin: 0 !important;" data-title="{{ $vehicle->plate }}">
                        <a href="#vehicle-{{ $vehicle->id }}" data-toggle="tab" aria-expanded="true" class="text-center">
                            <i class="fa fa-car {{ $reportByVehicle->isOK ? ($reportByVehicle->percentOK >= 100 ? 'text-success' : "") : ($reportByVehicle->percentOK <= 0 ? 'text-danger' : "text-warning") }}" aria-hidden="true"></i> {{ $vehicle->number }}
                            <br>
                            <small style="font-size: 0.8em !important;">{{ number_format($reportByVehicle->percentOK, 1, '.', '') }}% OK</small>
                        </a>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="tab-content p-0">
            @foreach($reportByVehicles as $vehicleId => $reportByVehicle)
                @php
                    $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId)
                @endphp
                <div id="vehicle-{{ $vehicleId }}" class="table-responsive tab-pane fade {{ $loop->first ? 'active in':'' }}">
                <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                            <tr class="inverse">
                                <th class="text-right" colspan="6">

                                </th>
                            </tr>
                            <tr class="inverse">
                                <th class="text-center">N°</th>
                                <th class="text-center"><i class="fa fa-calendar" aria-hidden="true"></i> @lang('Date')</th>
                                <th class="text-center"><i class="fa fa-podcast" aria-hidden="true"></i> @lang('Total Locations')</th>
                                <th class="text-center"><i class="fa fa-road" aria-hidden="true"></i> @lang('Total Km')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reportByVehicle->report as $report)
                                <tr class="text-center">
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <label class="label {{ $report->isOK ? "label-success" : "label-warning" }}" style="font-size: 1.1em !important;">{{ $report->date->toDateString() }}</label>
                                    </td>
                                    <td >{{ $report->totalLocations }}</td>
                                    <td >{{ number_format($report->totalKm/1000, 2, '.', '') }}</td>
                                </tr>
                            @endforeach
                            <tr class="bg-inverse text-white">
                                <th class="text-right" colspan="2">@lang('Average')</th>
                                <th class="text-center">{{ $reportByVehicle->report->average('totalLocations') }}</th>
                                <th class="text-center">{{ number_format($reportByVehicle->report->average('totalKm')/1000, 2, '.', '')}}</th>
                            </tr>
                            <tr class="bg-inverse text-white">
                                <th class="text-right" colspan="2">@lang('TOTAL')</th>
                                <th class="text-center">{{ $reportByVehicle->report->sum('totalLocations') }}</th>
                                <th class="text-center">{{ number_format($reportByVehicle->report->sum('totalKm')/1000, 2, '.', '')}}</th>
                            </tr>
                        </tbody>
                    </table>
                    <!-- end table -->
                </div>
            @endforeach
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip({
            container: 'body'
        });
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif