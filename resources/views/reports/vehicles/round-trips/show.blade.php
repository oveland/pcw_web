@if(count($roundTripsReport->reports))
    @php($reports = $roundTripsReport->reports)
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a class="btn btn-info btn-sm bg-info-dark btn-rounded hide-details">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> @lang('Hide details')
                </a>
                <a class="btn btn-info btn-sm bg-info-dark btn-rounded see-details">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> @lang('See details')
                </a>
                <a href="{{ route('report-vehicle-round-trips-show') }}?company={{ $roundTripsReport->company->id }}&date-report={{ $roundTripsReport->dateReport }}&route-report={{ $roundTripsReport->routeReport }}&export=true"
                   class="btn btn-lime btn-sm bg-lime-dark btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-file-excel-o"></i>
                </a>
                <a href="javascript:;" class="btn btn-sm btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <p class="text-white label-vehicles">
                <strong>
                    <i class="fa fa-road"></i> {{ $roundTripsReport->totalRoundTripsByFleet }} @lang('round trips') @lang('in the fleet')
                </strong>
                <br>
                <small><i class="fa fa-car"></i> {{ count($reports) }} @lang('Vehicles')</small>
            </p>
        </div>
        <div class="table-responsive">
            <!-- begin table -->
            <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                <thead>
                <tr class="inverse">
                    <th class="text-center">N°</th>
                    <th class="text-center">
                        <i class="fa fa-car" aria-hidden="true"></i><br>
                        @lang('Vehicle')
                    </th>
                    <th class="text-center">
                        <i class="fa fa-retweet" aria-hidden="true"></i><br>
                        @lang('Round trips')
                    </th>
                    <th class="text-center details">
                        <i class="fa fa-list-alt" aria-hidden="true"></i><br>
                        @lang('Details')
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
                    @php
                        $vehicle = $report->vehicle;
                        $reportRoundTripByRoutes = $report->reportRoundTripByRoutes
                    @endphp
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $vehicle->number }} <i class="fa fa-hand-o-right"
                                                      aria-hidden="true"></i> {{  $vehicle->plate }}</td>
                        <td class="text-center">
                            <a class="btn btn-{{ $reportRoundTripByRoutes->count() > 1 ? 'grey tooltips vehicle-see-details' :'white' }} btn-icon btn-circle btn-sm" data-title="@lang('Vehicle with mixed routes')">
                                {{ $report->totalRoundTrips }}
                            </a>
                        </td>
                        <td class="text-center details">
                            @foreach($reportRoundTripByRoutes as $reportRoundTripByRoute)
                                <a class="btn btn-success btn-xs active p-l-15 p-r-15">
                                    <span><i class="fa fa-flag"></i> {{ $reportRoundTripByRoute->route->name }}</span><br>
                                    <strong> {{ $reportRoundTripByRoute->roundTripsByRoute }} {{ __('round trip'.($reportRoundTripByRoute->roundTripsByRoute > 1?'s':'')) }}</strong><br>
                                    <small class="tooltips" data-title="@lang('Departure time on first round trip')" data-placement="bottom">
                                        {{ \App\Http\Controllers\Utils\StrTime::toString($reportRoundTripByRoute->firstDepartureTime) }}
                                    </small>
                                    @lang('and')
                                    <small class="tooltips" data-title="@lang('Arrival time on last round trip')" data-placement="bottom">
                                        {{ \App\Http\Controllers\Utils\StrTime::toString($reportRoundTripByRoute->lastArrivalTime) }}
                                    </small>
                                </a>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                <tr class="inverse bg-inverse-light text-white">
                    <td colspan="2" class="text-right">@lang('Total') @lang('round trips')</td>
                    <td colspan="" class="text-center">{{ $roundTripsReport->totalRoundTripsByFleet }}</td>
                    <td colspan="2" class="text-center details"></td>
                </tr>
                </tbody>
            </table>
            <!-- end table -->

            <script>
                $(document).ready(function(){
                    let details = $('.details');
                    let hideDetails = $('.hide-details');
                    let seeDetails = $('.see-details');
                    let vehicleSeeDetails = $('.vehicle-see-details');

                    hideDetails.click(function(){
                        details.fadeOut();seeDetails.show();hideDetails.hide();
                    });

                    seeDetails.click(function(){
                        details.fadeIn();hideDetails.show();seeDetails.hide();
                    });

                    vehicleSeeDetails.click(function(){
                        seeDetails.click();
                    });

                    hideDetails.click();
                });
            </script>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif