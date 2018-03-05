@if(count($offRoadsByVehicles))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10 text-uppercase">
                <i class="fa fa-road"></i> <i class="fa fa-share" aria-hidden="true"></i>
                @lang('Off road report by Vehicle')
            </h5>
        </div>
        <div class="tab-content panel">
            <div class="row">
                <div class="col-md-6 col-lg-4 col-sm-12 col-xs-12">
                    <div class="widge p-t-0 report-by-vehicle-container">
                        <div class="widget-header bg-inverse m-0">
                            <h4 class="text-white label-vehicles">{{ count($offRoadsByVehicles) }} @lang('Vehicles')
                                <div class="col-md-1 pull-right p-0">
                                    <i class="fa fa-times btn-clear-search btn btn-default btn-xs" onclick="$(this).parents('.label-vehicles').find('input').val('').keyup()"></i>
                                </div>
                                <div class="col-md-6 pull-right p-0">
                                    <input type="number" class="form-control input-sm col-md-4 search-vehicle-list" placeholder="@lang('Search')" style="top:-7px"/>
                                </div>
                            </h4>
                        </div>
                        <div data-scrollbar="true" data-height="400px" data-distance="0px">
                            <ul class="widget-todolist">
                                @foreach($offRoadsByVehicles as $vehicleId => $offRoadReport)
                                    @php( $vehicle = App\Vehicle::find($vehicleId) )
                                    @php( $totalOffRoads = $offRoadReport->sum(function ($route) { return count($route); }) )

                                    <li id="vehicle-list-{{ $vehicle->number }}" class="vehicle-list accordion-toggle accordion-toggle-styled {{ $loop->first ? 'collapsed':'' }} accordion-vehicles" data-toggle="collapse" data-parent="#accordion-vehicles" data-target="#vehicle-{{ $vehicleId }}" {{ $loop->first ? 'aria-expanded=true':'' }}>
                                        <div class="checkbox">
                                            <label class="icon-vehicle-list">
                                                <i class="fa fa-car text-muted"><span style="font-family: 'Lato', sans-serif;font-size: 50%;float: left;margin-left: 8px">{{ $loop->index + 1 }}</span></i>
                                            </label>
                                        </div>
                                        <div class="info info-vehicle-list">
                                            <h4>{{ $vehicle->number  }} <i class="fa fa-minus"></i> {{ $vehicle->plate  }}</h4>
                                            <p><strong>{{ $totalOffRoads }} @lang('outs')</strong> @lang('in') {{ count($offRoadReport) }} {{ __(str_plural('route',count($offRoadReport))) }}</p>
                                        </div>
                                        <div class="action hide">
                                            <a href="#" data-toggle="dropdown"><i class="fa fa-cog fa-spin"></i></a>
                                            <ul class="dropdown-menu dropdown-menu-right">
                                                <li><a href="javascript:;">Edit</a></li>
                                                <li><a href="javascript:;">Archive</a></li>
                                                <li><a href="javascript:;">Delete</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div id="accordion-vehicles" class="col-md-6 col-lg-8 col-sm-12 col-sm-12">
                    @foreach($offRoadsByVehicles as $vehicleId => $offRoadReports)
                        @php( $vehicle = App\Vehicle::find($vehicleId) )
                        <div id="vehicle-{{ $vehicleId }}" class="panel-collapse collapse {{ $loop->first ? 'in':'' }}" aria-expanded="false">
                            <!-- begin panel -->
                            <div class="panel panel-white panel-with-tabs">
                                <div class="panel-heading">
                                    <ul id="panel-tab" class="nav nav-tabs nav-tabs-warning pull-right">
                                        @foreach($offRoadReports as $routeId => $offRoadReport)
                                            @php($route = \App\Route::find($routeId))
                                            <li class="{{ $loop->first ? 'active':'' }}">
                                                <a href="#panel-tab-{{ $vehicleId }}-{{ $route->id }}" data-toggle="tab">
                                                    <span class="badge badge-danger m-b-5">{{ count($offRoadReport) }}</span>
                                                    <span class="">{{ $route->name }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <h4 class="panel-title">
                                        {{ $vehicle->number  }} <i class="fa fa-minus"></i> {{ $vehicle->plate  }}
                                    </h4>
                                </div>
                                <div id="panel-tab-content" class="tab-content">
                                    @foreach($offRoadReports as $routeId => $offRoadReport)
                                        @php($route = \App\Route::find($routeId))
                                        <div id="panel-tab-{{ $vehicleId }}-{{ $routeId }}" class="tab-pane fade in {{ $loop->first ? 'active':'' }}">
                                            <div class="table-responsive">
                                                <table class="table table-bordered table-striped table-hover table-valign-middle">
                                                    <thead>
                                                        <tr class="inverse">
                                                            <th>@lang('Turn')</th>
                                                            <th>@lang('Round Trip')</th>
                                                            <th>@lang('Driver')</th>
                                                            <th>@lang('Off road time')</th>
                                                            <th>@lang('Address')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @php($dispatchRegister = null)
                                                        @foreach($offRoadReport as $offRoad)
                                                            @php($dispatchRegister = $offRoad->dispatchRegister)
                                                            @php($driver = $dispatchRegister->driver)
                                                            <tr>
                                                                <td>{{ $dispatchRegister->turn }}</td>
                                                                <td>{{ $dispatchRegister->round_trip }}</td>
                                                                <td class="text-uppercase" width="10%">{{ $driver?$driver->fullName():__('Not assigned') }}</td>
                                                                <td>{{ $offRoad->date }}</td>
                                                                <td>
                                                                    <a href="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}?export=true" class="btn btn-lime bg-lime-dark btn-sm">
                                                                        <i class="fa fa-file-excel-o"></i>
                                                                    </a>
                                                                    <button class="btn btn-sm btn-warning btn-location tooltips" data-toggle="collapse" data-target="#image-{{ $offRoad->id }}" data-title="@lang('Location')">
                                                                        <i class="fa fa-map-marker"></i>
                                                                        <span>@lang('Location')</span>
                                                                    </button>
                                                                    <span id="address-{{ $offRoad->id }}" class="tooltips" data-title="@lang('Address')"></span>
                                                                    <button class="btn btn-sm btn-info btn-show-address" onclick="$(this).parent('td').find('.btn-location').find('span').slideUp(1000)"
                                                                            data-url="{{ route('report-route-off-road-geolocation-address',['offRoad'=>$offRoad->id]) }}"
                                                                            data-target="#address-{{ $offRoad->id }}">
                                                                        <i class="fa fa-refresh faa-spin animated-hover hide"></i>
                                                                        <span>@lang('Address')</span>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <tr id="image-{{ $offRoad->id }}" class="collapse fade collapse-off-road-image" data-url="{{ route('report-route-off-road-geolocation-image',['offRoad'=>$offRoad->id]) }}">
                                                                <td colspan="4" class="text-center">
                                                                    <i class="fa fa-2x fa-cog fa-spin text-muted"></i>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                    <tfoot class="hide">
                                                        <tr>
                                                            <td colspan="4" class="text-right">

                                                            </td>
                                                        </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- end panel -->
                        </div>
                    @endforeach
                </div>
            </div>
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