@if(count($offRoadsByVehicles))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-route-off-road-search') }}?{{ $query->stringParams }}&export=true"
                   class="btn green btn-circle tooltips"
                   data-title="@lang('Export excel')">
                    <i class="fa fa-download"></i>
                </a>
            </div>

            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success m-0">
                        @foreach($offRoadsByVehicles as $vehicleId => $offRoadReport)
                            @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                            <li class="{{ $loop->first ? 'active' : '' }}">
                                <a href="#vehicle-{{ $vehicleId }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-placement="bottom"
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
        <div class="tab-content panel">
            @foreach($offRoadsByVehicles as $vehicleId => $offRoadReports)
                @php( $vehicle = \App\Models\Vehicles\Vehicle::find($vehicleId) )
                <div id="vehicle-{{ $vehicleId }}" class="tab-pane fade {{ $loop->first ? 'active in' : '' }}">
                    <div class="table-responsive col-md-12 p-0">
                        <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                            <thead>
                            <tr class="inverse">
                                <th>
                                    <i class="fa fa-list"></i>
                                </th>
                                <th>
                                    <i class="fa fa-clock-o text-muted"></i><br>
                                    @lang('Date')
                                </th>
                                <th>
                                    <i class="fa fa-flag text-muted"></i><br>
                                    @lang('Route')
                                </th>
                                <th>
                                    <i class="fa fa-retweet text-muted"></i><br>
                                    @lang('Round Trip')
                                </th>
                                <th>
                                    <i class="fa fa-list-ol"></i><br>
                                    @lang('Turn')
                                </th>
                                <th>
                                    <i class="icon-user text-muted"></i><br>
                                    @lang('Driver')
                                </th>
                                <th>
                                    <i class="fa fa-rocket text-muted"></i><br>
                                    @lang('Actions')
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($offRoadReports as $offRoad)
                                @php
                                    $dispatchRegister = $offRoad->dispatchRegister;
                                    $driver = $dispatchRegister->driver;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $offRoad->date->toDatetimeString() }}</td>
                                    <td class="text-center">{{ $dispatchRegister->route->name }}</td>
                                    <td class="text-center">{{ $dispatchRegister->round_trip }}</td>
                                    <td class="text-center">{{ $dispatchRegister->turn }}</td>
                                    <td class="text-uppercase" width="10%">{{ $driver?$driver->fullName():$dispatchRegister->driver_code }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-outline btn-circle yellow-casablanca btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $offRoad->id }}" data-title="@lang('Location')">
                                            &nbsp;<i class="fa fa-map-marker"></i>&nbsp;
                                        </button>
                                        <span id="address-{{ $offRoad->id }}" class="tooltips" data-title="@lang('Address')"></span>
                                        <button class="btn btn-outline btn-circle blue-chambray btn-show-address tooltips" data-title="@lang('Address')" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                                data-url="{{ route('report-route-off-road-geolocation-address',['offRoad'=>$offRoad->id]) }}"
                                                data-target="#address-{{ $offRoad->id }}">
                                            <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                            <i class="fa fa-map"></i>
                                        </button>

                                        <a href="#modal-route-report"
                                           class="btn btn-outline btn-circle green-meadow faa-parent animated-hover btn-show-chart-route-report tooltips"
                                           data-toggle="modal"
                                           data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}?centerOnLocation={{ $offRoad->id }}"
                                           data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}"
                                           data-original-title="@lang('Graph report detail')">
                                            <i class="fa fa-area-chart faa-pulse"></i>
                                        </a>
                                    </td>
                                </tr>
                                <tr id="image-{{ $offRoad->id }}" class="collapse fade collapse-off-road-image" data-url="{{ route('report-route-off-road-geolocation-image',['offRoad'=>$offRoad->id]) }}">
                                    <td colspan="6" class="text-center">
                                        <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    <script type="application/javascript">
        //handleSlimScroll();
        $('.report-by-vehicle-container div[data-scrollbar="true"]').slimScroll({
            width: 'auto',
            height: '350px',
            size: '3px',
            position: 'right',
            color: '#0e7685',
            alwaysVisible: false,
            distance: '0px',
            railVisible: true,
            railColor: '#b1d3d6',
            railOpacity: 0.3,
            wheelStep: 10,
            allowPageScroll: true,
            disableFadeOut: false
        });

        $('.collapse-off-road-image').on('show.bs.collapse',function(){
            var img = $('<img>').attr('src',$(this).data('url'));
            $(this).find('td').empty().append( img );
        });
    </script>
@else
    <div class="alert alert-success alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Hey!')</strong></h4>
            <hr class="hr">
            @lang('The date haven´t off roads list')
        </div>
    </div>
@endif