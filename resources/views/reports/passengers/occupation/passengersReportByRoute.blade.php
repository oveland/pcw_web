@if(count($dispatchRegisters))
    <div class="panel panel-inverse">
        <div class="panel-heading hide">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">

                </div>
            </div>
        </div>
        <div class="tab-content panel p-0">
            <div  class="table-responsive">
                <!-- begin table -->
                <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th>@lang('Vehicle')</th>
                        <th>@lang('Route')</th>
                        <th class="col-md-2">@lang('Hour dispatch')</th>
                        <th>@lang('Round Trip')</th>
                        <th data-sorting="disabled">@lang('Turn')</th>
                        <th data-sorting="disabled">@lang('Actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($dispatchRegisters as $dispatchRegister)
                        <tr>
                            <td>{{$dispatchRegister->vehicle->number}} <i class="fa fa-hand-o-right" aria-hidden="true"></i> {{$dispatchRegister->vehicle->plate}}</td>
                            <td>{{$dispatchRegister->route->name}}</td>
                            <td>{{$dispatchRegister->departure_time}}</td>
                            <td>{{$dispatchRegister->round_trip}}</td>
                            <td>{{$dispatchRegister->turn}}</td>
                            <td>
                                <a href="#modal-passengers-route-report" data-toggle="modal"
                                   data-url="{{ route('report-passengers-occupation-by-dispatch',['id'=>$dispatchRegister->id]) }}"
                                   class="btn btn-sm btn-primary faa-parent animated-hover btn-show-passengers-route-report">
                                    <i class="fa fa-users faa-pulse"></i> @lang('Passenger report detail')
                                </a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif