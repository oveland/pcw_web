@if(count($dispatchRegistersByDrivers))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime pull-left" data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-12 p-0 m-b-10">
                    <ul class="nav nav-pills nav-pills-success nav-vehicles">
                        @foreach($dispatchRegistersByDrivers as $driverCode => $dispatchRegistersByDriver)
                            @php
                                $driver = \App\Models\Drivers\Driver::withCode($driverCode);
                            @endphp
                            <li class="{{$loop->first?'active':''}}" onclick="$('.driver-name').hide().text('{{ $driver->fullName() }}').slideDown()">
                                <a href="#report-tab-{{ $driverCode }}" data-toggle="tab" aria-expanded="true" class="tooltips" data-placement="bottom"
                                   data-original-title="{{ $driver->fullName() }}">
                                    <i class="icon-user f-s-9 icon-report"></i><span class="icon-report f-s-8">{{ $loop->iteration }}</span>
                                    <strong>{{ $driver->code }}</strong>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-1 hide">
                    <a href="{{ route('report-route-search') }}?type-report=vehicle&export=true" class="btn btn-lime bg-lime-dark pull-right" style="position: absolute;left: -20px;">
                        <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                    </a>
                </div>
                <div class="p-0">
                    <hr class="hr">
                    <blockquote class="m-b-0">
                        <i class="icon-user f-s-24 text-muted" style="position: absolute;left: 40px;"></i>
                        <p class="driver-name m-l-40">{{ $dispatchRegistersByDrivers->first()->first()->driver->fullName() }}</p>
                    </blockquote>
                </div>
            </div>
        </div>

        <div class="tab-content panel p-0">
            @foreach($dispatchRegistersByDrivers as $driverCode => $dispatchRegistersByDriver)
                @php
                    $driver = \App\Models\Drivers\Driver::withCode($driverCode);
                @endphp
                <div id="report-tab-{{ $driverCode }}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">

                    <!-- begin table -->
                    <table class="table table-bordered table-striped table-hover table-valign-middle table-report">
                        <thead>
                        <tr class="inverse">
                            <th>
                                <i class="fa fa-flag text-muted"></i><br>
                                @lang('Route')
                            </th>
                            <th>
                                <i class="fa fa-retweet text-muted"></i><br>
                                @lang('Round Trip')
                            </th>
                            <th>
                                <i class="fa fa-list-ol text-muted"></i><br>
                                @lang('Turn')
                            </th>
                            <th>
                                <i class="fa fa-car text-muted"></i><br>
                                @lang('Vehicle')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i><br>
                                @lang('Dispatched')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i><br>
                                @lang('Arrival Time Scheduled')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i><br>
                                @lang('Arrival Time')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i><br>
                                @lang('Arrival Time Difference')
                            </th>
                            <th class="col-md-2">
                                <i class="fa fa-clock-o text-muted"></i>
                                <i class="fa fa-flag text-muted m-r-5"></i><br>
                                <span class="text-warning">@lang('Route Time')</span>
                            </th>
                            <th >
                                <i class="fa fa-tachometer text-muted"></i><br>
                                @lang('Status')
                            </th>
                            <th >
                                <i class="fa fa-rocket text-muted"></i><br>
                                @lang('Actions')
                            </th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach( $dispatchRegistersByDriver as $dispatchRegister )
                            @php
                                $strTime = new \App\Http\Controllers\Utils\StrTime();
                                $route = $dispatchRegister->route;
                                $vehicle = $dispatchRegister->vehicle;
                            @endphp
                            <tr>
                                <th class="bg-inverse text-white text-center">{{ $route->name }}</th>
                                <th class="bg-inverse text-white text-center">{{ $dispatchRegister->round_trip }}</th>
                                <td class="text-center">{{ $dispatchRegister->turn }}</td>
                                <td class="text-center">{{ "$vehicle->number" }}</td>
                                <td class="text-center">{{ $strTime->toString($dispatchRegister->departure_time) }}</td>
                                <td class="text-center">{{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}</td>
                                <td class="text-center">{{ $strTime->toString($dispatchRegister->arrival_time) }}</td>
                                <td class="text-center">{{ $strTime->toString($dispatchRegister->arrival_time_difference) }}</td>
                                <td class="text-center">
                                    @if($dispatchRegister->complete())
                                        {{ $strTime::subStrTime($dispatchRegister->arrival_time, $dispatchRegister->departure_time) }}
                                    @endif
                                </td>
                                <td class="text-center">{{ $dispatchRegister->status }}</td>

                                <td width="10%" class="bg-inverse text-white text-center">
                                    <button class="btn btn-xs btn-primary faa-parent animated-hover tooltips"
                                       data-toggle="collapse"
                                       data-target="#report-detail-{{ $dispatchRegister->id }}"
                                       data-original-title="@lang('See details')">
                                        <i class="fa icon-layers faa-ring animated-hover"></i>
                                    </button>
                                </td>
                            </tr>
                            <tr id="report-detail-{{ $dispatchRegister->id }}" class="collapse fade">
                                <td colspan="20">
                                    <div class="panel">
                                        <ul class="nav nav-tabs nav-tabs-primary nav-justified">
                                            <li class="active">
                                                <a href="#report-off-road-{{ $dispatchRegister->id }}" data-toggle="tab">
                                                    <i class="fa fa-road faa-tada f-s-15"></i> @lang('Off Roads')
                                                    <span class="badge badge-danger badge-off-road-{{ $dispatchRegister->id }} m-b-5 hide"></span>
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="#report-speeding-{{ $dispatchRegister->id }}" data-toggle="tab">
                                                    <i class="fa fa-tachometer faa-tada f-s-15"></i> @lang('Speeding')
                                                    <span class="badge badge-danger badge-speeding-{{ $dispatchRegister->id }} m-b-5 hide"></span>
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="#report-parking-{{ $dispatchRegister->id }}" data-toggle="tab">
                                                    <i class="fa fa-product-hunt faa-pulse" aria-hidden="true"></i> @lang('Parked vehicles')
                                                    <span class="badge badge-danger badge-parking-{{ $dispatchRegister->id }} m-b-5 hide"></span>
                                                </a>
                                            </li>
                                            <li class="">
                                                <a href="#report-control-point-time-{{ $dispatchRegister->id }}" data-toggle="tab">
                                                    <i class="fa fa-map-marker faa-tada f-s-15"></i> @lang('Control Points')
                                                </a>
                                            </li>
                                        </ul>
                                        <div class="tab-content p-0">
                                            <div class="tab-pane fade active in" id="report-off-road-{{ $dispatchRegister->id }}">
                                                @include('reports.drivers.consolidated.partials.offRoadsReportTable')
                                            </div>
                                            <div class="tab-pane fade" id="report-speeding-{{ $dispatchRegister->id }}">
                                                @include('reports.drivers.consolidated.partials.speedingReportTable')
                                            </div>
                                            <div class="tab-pane fade" id="report-parking-{{ $dispatchRegister->id }}">
                                                @include('reports.drivers.consolidated.partials.parkingReportTable')
                                            </div>
                                            <div class="tab-pane fade" id="report-control-point-time-{{ $dispatchRegister->id }}">
                                                {{ dump('report-control-point-time') }}
                                            </div>
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

    <script>
        $('.collapse-speeding-location-image').on('show.bs.collapse',function(){
            var img = $('<img>').attr('src',$(this).data('url'));
            $(this).find('td').empty().append( img );
        });
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif