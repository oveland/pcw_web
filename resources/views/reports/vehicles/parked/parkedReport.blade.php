@if(count($parkedReportsByVehicles))
<div class="panel panel-inverse">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="{{ route('report-vehicle-parked-search-report') }}?{{ $stringParams }}&export=true"
               class="btn green btn-circle tooltips"
               data-title="@lang('Export excel')">
                <i class="fa fa-download"></i>
            </a>
        </div>
        <div class="row">
            <div class="col-md-11">
                <ul class="nav nav-pills nav-pills-success m-0">
                    @foreach($parkedReportsByVehicles as $vehicleId => $parkedReportsByVehicle)
                        @php( $vehicle = $parkedReportsByVehicle->first()->vehicle )
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
        @foreach($parkedReportsByVehicles as $vehicleId => $parkedReportsByVehicle)
        <div id="report-tab-{{ $vehicleId }}" class="table-responsive tab-pane fade {{ $loop->first?'active in':'' }}">
            <!-- begin table -->
            <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                <thead>
                <tr class="inverse">
                    <th>
                        <i class="fa fa-list text-muted"></i>
                    </th>
                    <th class="col-md-2">
                        <i class="fa fa-clock-o text-muted"></i><br>
                        @lang('Date')
                    </th>
                    <th>
                        <i class="fa fa-car text-muted"></i><br>
                        @lang('Vehicle')
                    </th>
                    <th data-sorting="disabled">
                        <i class="fa fa-search text-muted"></i><br>
                        @lang('Details')
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach( $parkedReportsByVehicle as $parking )
                    @php( $dispatchRegister = $parking->dispatchRegister )
                    <tr>
                        <td class="bg-inverse text-white text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            {{ $parking->date->toDateTimeString() ?? '' }}
                        </td>
                        <td class="text-center">
                            {{ $parking->vehicle->number }}
                        </td>
                        <td class="text-center">
                            <button class="btn btn-outline btn-circle yellow-casablanca btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $parking->id }}" data-title="@lang('Location')">
                                <i class="fa fa-map-marker"></i>
                            </button>
                            <span id="address-{{ $parking->id }}" class="tooltips" data-title="@lang('Address')"></span>
                            <button class="btn btn-outline btn-circle blue-chambray btn-show-address tooltips" data-title="@lang('Address')" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                    data-url="{{ route('report-vehicle-parked-geolocation-address',['speeding'=>$parking->id]) }}"
                                    data-target="#address-{{ $parking->id }}">
                                <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                <i class="fa fa-map"></i>
                            </button>
                            @if( $dispatchRegister )
                                <div class="faa-parent animated-hover tooltips" data-title="@lang('Route')"
                                     data-toggle="collapse" data-target="#collapse-{{ $parking->id }}" aria-expanded="false" aria-controls="collapse-{{ $parking->id }}">
                                    <i class="fa fa-flag"></i>
                                    {{ $dispatchRegister->route->name }} |

                                    <span class="text-capitalize tooltips" data-title="@lang('Driver')" data-placement="right">
                                            <i class="fa fa-user"></i>
                                            {{ $dispatchRegister->driverName() }}
                                        </span>
                                </div>
                            @else
                                <div>
                                    @lang('Without assigned route')
                                </div>
                            @endif
                        </td>
                    </tr>

                    <tr id="collapse-{{ $parking->id }}" class="e" aria-expanded="true">
                        <td colspan="4" class="p-0">
                            <div id="image-{{ $parking->id }}" class="collapse collapse-parked-location-image" data-url="{{ route('report-vehicle-parked-geolocation-image',['parkingReport'=>$parking->id]) }}">
                                <div class="text-center">
                                    <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <!-- end table -->
        </div>
        @endforeach
    </div>
</div>

<script type="application/javascript">
    $('.collapse-parked-location-image').on('show.bs.collapse',function(){
        var el = $(this);
        var btnLocation = el.parents('td').find('.btn-location');
        var iconBtnLocation = btnLocation.find('i');
        btnLocation.addClass('disabled');
        iconBtnLocation.removeClass('fa-map-marker').addClass('fa-cog fa-spin');

        var img = $('<img>').attr('src',el.data('url'));
        el.find('div').empty().append( img );

        setTimeout(function(){
            btnLocation.removeClass('disabled');
            iconBtnLocation.addClass('fa-map-marker').removeClass('fa-cog fa-spin');
        },1000);
    });
</script>
@else
@include('partials.alerts.noRegistersFound')
@endif