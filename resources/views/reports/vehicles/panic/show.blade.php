@if(count($report))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('report-vehicle-panic-search-report') }}?{{ $query->stringParams }}&export=true"
                   class="btn green btn-circle tooltips"
                   data-title="@lang('Export excel')">
                    <i class="fa fa-download"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success m-0">
                        @foreach($report as $vehicleId => $events)
                            @php
                                $vehicle = $events->first()['vehicle'];
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
            @foreach($report as $vehicleId => $events)
                @php
                    $vehicle = $events->first()['vehicle'];
                @endphp
                <div id="report-tab-{{ $vehicleId }}" class="tab-pane fade {{ $loop->first ? 'active in' : '' }}">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                            <thead>
                            <tr class="inverse">
                                <th>
                                    <i class="fa fa-list"></i>
                                </th>
                                <th>
                                    <i class="fa fa-clock-o"></i><br>
                                    @lang('Date')
                                </th>
                                <th>
                                    <i class="fa fa-car"></i><br>
                                    @lang('Vehicle')
                                </th>
                                <th>
                                    <i class="fa fa-tachometer"></i><br>
                                    @lang('Speed') Km/h
                                </th>
                                <th>
                                    <i class="fa fa-flag"></i><br>
                                    @lang('Route')
                                </th>
                                <th>
                                    <i class="fa fa-rocket text-muted"></i><br>
                                    @lang('Actions')
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                            @php
                                $dispatchRegister = null;
                                $events = $events->sortBy('date')
                            @endphp
                            @foreach($events as $event)
                                @php
                                    $event = (object) $event;
                                    $dispatchRegister = $event->dispatchRegister;
                                @endphp
                                <tr>
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-center">{{ $event->date }}</td>
                                    <td class="text-center">{{ $event->vehicle->number }}</td>
                                    <td class="text-center">
                                        {{ number_format($event->speed,2, ',', '') }}
                                    </td>
                                    <td class="text-uppercase" width="40%">
                                        <div class="col-md-12">
                                            @if($dispatchRegister)
                                                <span class="tooltips" data-title="@lang('Route')"><i class="fa fa-flag"></i> {{ $dispatchRegister->route }}</span>
                                                <span class="tooltips" data-title="@lang('Round trip')"><i class="fa fa-retweet text-muted"></i> {{ $dispatchRegister->trip }}</span>
                                                <span class="tooltips" data-title="@lang('Turn')"><i class="fa fa-list-ol text-muted"></i> {{ $dispatchRegister->turn }}</span>
                                                @if($dispatchRegister->driver)
                                                    <br><span class="tooltips" data-title="@lang('Driver')"><i class="fa fa-user text-muted"></i> {{ $dispatchRegister->driver }}</span>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-outline btn-circle yellow-casablanca btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $event->id }}" data-title="@lang('Location')">
                                            <i class="fa fa-map-marker"></i>
                                        </button>
                                        <span id="address-{{ $event->id }}" class="tooltips" data-title="@lang('Address')"></span>
                                        <button class="btn btn-outline btn-circle blue-chambray btn-show-address tooltips" data-title="@lang('Address')" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                                data-url="{{ route('report-vehicle-panic-geolocation-address', ['panic' => $event->id]) }}"
                                                data-target="#address-{{ $event->id }}">
                                            <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                            <i class="fa fa-map"></i>
                                        </button>
                                        @if($dispatchRegister)
                                            <a href="#modal-route-report"
                                               class="btn btn-outline btn-circle green-meadow faa-parent animated-hover btn-show-chart-route-report tooltips"
                                               data-toggle="modal"
                                               data-url="{{ route('report-route-chart',['dispatchRegister'=>$event->dispatchRegister->id]) }}?centerOnLocation={{ $event->id }}"
                                               data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$event->dispatchRegister->id]) }}"
                                               data-original-title="@lang('Graph report detail')">
                                                <i class="fa fa-area-chart faa-pulse"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                <tr id="image-{{ $event->id }}" class="collapse fade collapse-panic-location-image" data-url="{{ route('report-vehicle-panic-geolocation-image',['panic'=>$event->id]) }}">
                                    <td colspan="5" class="text-center">
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
        $('.collapse-panic-location-image').on('show.bs.collapse',function(){
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
            @lang('The date haven´t panic list')
        </div>
    </div>
@endif