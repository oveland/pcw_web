@if(count($parkedReportsByVehicles))
<div class="panel panel-inverse">
    <div class="panel-heading">
        <div class="panel-heading-btn">
            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                <i class="fa fa-expand"></i>
            </a>
        </div>
        <div class="row">
            <div class="col-md-11">
                <a href="{{ route('report-vehicle-parked-search-report') }}?{{ $stringParams }}&export=true" class="btn btn-lime bg-lime-dark pull-right">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <ul class="nav nav-pills nav-pills-success">
                    @foreach($parkedReportsByVehicles as $vehicleId => $parkedReportsByVehicle)
                        @php( $vehicle = $parkedReportsByVehicle->first()->vehicle )
                        <li class="{{$loop->first?'active':''}}">
                            <a href="#report-tab-{{ $vehicleId }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-title="{{ $vehicle->plate }}" data-title="{{ $vehicle->plate }}">
                                {{ $vehicle->number }}
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
                        @lang('Time')
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
                @foreach( $parkedReportsByVehicle as $parkedReportByVehicle )
                    @php( $dispatchRegister = $parkedReportByVehicle->dispatchRegister )
                    <tr>
                        <td class="bg-inverse text-white text-center">{{ $loop->iteration }}</td>
                        <td class="text-center">
                            {{ $parkedReportByVehicle->date->toTimeString() ?? '' }}
                        </td>
                        <td class="text-center">
                            {{ $parkedReportByVehicle->vehicle->number }} | {{ $parkedReportByVehicle->vehicle->plate }}
                        </td>
                        <td class="text-center">
                            @if( $dispatchRegister )
                                <button class="btn btn-sm btn-primary faa-parent animated-hover tooltips" data-title="@lang('Route')"
                                        data-toggle="collapse" data-target="#collapse-{{ $parkedReportByVehicle->id }}" aria-expanded="false" aria-controls="collapse-{{ $parkedReportByVehicle->id }}">
                                    <i class="fa fa-flag"></i>
                                    {{ $dispatchRegister->route->name }}
                                </button>
                            @else
                                @lang('Without assigned route')
                            @endif
                        </td>
                    </tr>

                    @if($dispatchRegister)
                        @php( $driver = $dispatchRegister->driver )
                        <tr id="collapse-{{ $parkedReportByVehicle->id }}" class="collapse fade" aria-expanded="true">
                            <td class="bg-inverse text-white text-center">
                                @lang('Details')
                            </td>
                            <td colspan="3">
                                <div class="col-md-4">
                                    <div class="widget widget-stat bg-info text-white">
                                        <div class="widget-stat-btn">
                                            <a href="javascript:void(0)" class="tooltips" data-title="{{ $dispatchRegister->status }}">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        </div>
                                        <div class="widget-stat-icon">
                                            <i class="fa fa-flag"></i>
                                        </div>
                                        <div class="widget-stat-info">
                                            <div class="widget-stat-title">
                                                @lang('Route Information')
                                            </div>
                                            <div class="widget-stat-number">
                                                <strong>{{ $dispatchRegister->route->name }}</strong>
                                            </div>
                                            <div class="widget-stat-text">
                                                <strong>@lang('Turn')</strong> {{ $dispatchRegister->turn }},
                                                <strong>@lang('Round Trip')</strong> {{ $dispatchRegister->round_trip }},
                                                <strong class="tooltips" data-title="@lang('Departure time')" data-placement="bottom">{{ $dispatchRegister->departure_time }}</strong>
                                                <br>
                                                <span class="f-s-10 text-capitalize tooltips" data-title="@lang('Driver')" data-placement="right">
                                                    <i class="fa fa-user"></i>
                                                    {{ $driver?$driver->fullName():__('Not assigned') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if($parkedReportByVehicle->report_id)
                                    <div class="col-md-4">
                                        <div class="widget widget-stat bg-success text-white">
                                        <div class="widget-stat-btn">
                                            <a href="javascript:void(0)" class="tooltips" data-title="@lang('Near of') {{ $parkedReportByVehicle->controlPoint->name }}">
                                                <i class="fa fa-map-marker"></i>
                                            </a>
                                        </div>
                                        <div class="widget-stat-icon">
                                            <i class="fa fa-area-chart"></i>
                                        </div>
                                        <div class="widget-stat-info">
                                            <div class="widget-stat-title">
                                                @lang('Route report')
                                            </div>
                                            <div class="widget-stat-number">
                                                <strong>{{ $parkedReportByVehicle->timed }}</strong>
                                            </div>
                                            <div class="widget-stat-text">
                                                <strong class="tooltips" data-title="@lang('Time scheduled')" data-placement="right">
                                                    {{ \App\Http\Controllers\Utils\Database::addStringTimes($dispatchRegister->departure_time, $parkedReportByVehicle->timep) }}
                                                </strong>
                                                <br>
                                                <strong class="tooltips" data-title="@lang('Time reported')" data-placement="right">
                                                    {{ \App\Http\Controllers\Utils\Database::addStringTimes($dispatchRegister->departure_time, $parkedReportByVehicle->timem) }}
                                                </strong>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                                @endif
                                <div class="col-md-4">
                                    <button class="btn btn-sm btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $parkedReportByVehicle->id }}" title="@lang('Location')">
                                        <i class="fa fa-map-marker"></i>
                                        <span>@lang('Location')</span>
                                    </button>
                                    <hr>
                                    <span id="address-{{ $parkedReportByVehicle->id }}" class="tooltips" data-title="@lang('Address')"></span>
                                    <button class="btn btn-sm btn-info btn-show-address"
                                            data-url="{{ route('report-vehicle-parked-geolocation-address',['parkingReport'=>$parkedReportByVehicle->id]) }}"
                                            data-target="#address-{{ $parkedReportByVehicle->id }}">
                                        <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                        <span>@lang('Address')</span>
                                    </button>
                                </div>
                                <div id="image-{{ $parkedReportByVehicle->id }}" class="collapse fade collapse-parked-location-image" data-url="{{ route('report-vehicle-parked-geolocation-image',['parkingReport'=>$parkedReportByVehicle->id]) }}">
                                    <div class="text-center">
                                        <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
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