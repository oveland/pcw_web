@if(count($off_road_report_list))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="{{ route('route-off-road-report',['dispatchRegister'=>$dispatchRegister->id]) }}?export=true"
                   class="btn btn-lime bg-lime-dark btn-sm" style="color: white !important;">
                    <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand"
                   title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10 text-uppercase">
                @lang('Off road report') @lang('Vehicle') {{ $dispatchRegister->vehicle->number  }} <i
                        class="fa fa-hand-o-right"></i> {{ $dispatchRegister->vehicle->plate  }}
                . @lang('Route') {{ $dispatchRegister->route->name  }}
                : @lang('Round Trip') {{ $dispatchRegister->round_trip  }}, @lang('Turn') {{ $dispatchRegister->turn }}
            </h5>
        </div>
        <div class="tab-content panel p-0">
            <div class="table-responsive">
                <!-- begin table -->
                <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th>@lang('Date')</th>
                        <th>@lang('Status')</th>
                        <th>@lang('Latitude')</th>
                        <th>@lang('Longitude')</th>
                        <th>@lang('Address')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($off_road_report_list as $off_road_report)
                        @if ($off_road_report->latitude != 0 && $off_road_report->longitude != 0)
                            <tr>
                                <td>{{ $off_road_report->date }}</td>
                                <td>{{ $off_road_report->time }}</td>
                                <td>{{ $off_road_report->latitude }}</td>
                                <td>{{ $off_road_report->longitude }}</td>
                                <td>{{ \App\Http\Controllers\RouteReportController::getAddressFromCoordinates($off_road_report->latitude,$off_road_report->longitude)}}</td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    <div class="alert alert-success alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Hey!')</strong></h4>
            <hr class="hr">
            @lang('The vehicle haven´t off roads list')
        </div>
    </div>
@endif