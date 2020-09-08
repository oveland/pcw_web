@if(count($roundTripsReport->reports))
    @php($reports = $roundTripsReport->reports)
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a class="btn hide btn-info btn-sm bg-info-dark btn-rounded hide-detailss">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> @lang('Hide details')
                </a>
                <a class="btn hide btn-info btn-sm bg-info-dark btn-rounded see-detailss">
                    <i class="fa fa-list-alt" aria-hidden="true"></i> @lang('See details')
                </a>
                <a href="{{ route('report-vehicle-round-trips-show') }}?company={{ $roundTripsReport->company->id }}&date-report={{ $roundTripsReport->dateReport }}&route-report={{ $roundTripsReport->routeReport }}&date-end-report={{ $roundTripsReport->dateEndReport }}&vehicle-report={{ $roundTripsReport->vehicleReport }}&with-end-date={{ $roundTripsReport->withEndDate }}&export=true"
                   class="btn btn-success btn-rounded tooltips" data-title="@lang('Export excel')">
                    <i class="fa fa-download"></i>
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
            <table class="table table-bordered table-striped table-hover table-valign-middle table-report table-condensed">
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
                        <td>{{ $vehicle->number }}</td>
                        <td class="text-center tooltips" data-title="{{ $reportRoundTripByRoutes->count() > 1 ? __('Vehicle with mixed routes'):'' }}">
                            {{ $report->totalRoundTrips }}
                            @if($reportRoundTripByRoutes->count() > 1)
                                <i class="fa fa-info text-info tooltips vehicle-see-details"></i>
                            @endif
                        </td>
                        <td class="text-left details">
                            <ul>
                                @foreach($reportRoundTripByRoutes as $reportRoundTripByRoute)
                                    <li>
                                        <span>{{ $reportRoundTripByRoute->route->name }}</span> |
                                        <strong>{{ $reportRoundTripByRoute->roundTripsByRoute }} {{ __('round trip'.($reportRoundTripByRoute->roundTripsByRoute > 1?'s':'')) }}</strong><br>
                                    </li>
                                @endforeach
                            </ul>
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