@if(count($roundTripDispatchRegisters))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-11">
                    <ul class="nav nav-pills nav-pills-success">
                        @foreach($roundTripDispatchRegisters as $dispatchRegisters)
                            <li class="{{$loop->first?'active':''}}">
                                <a href="#report-tab-{{$dispatchRegisters->first()->round_trip}}" data-toggle="tab" aria-expanded="true">
                                    @lang('Round trip') {{$dispatchRegisters->first()->round_trip}}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content panel p-0">
            @foreach($roundTripDispatchRegisters as $dispatchRegisters)
                <div id="report-tab-{{$dispatchRegisters->first()->round_trip}}" class="table-responsive tab-pane fade {{$loop->first?'active in':''}}">
                    <!-- begin table -->
                    <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle">
                        <thead>
                            <tr class="inverse">
                                <th>@lang('Vehicle')</th>
                                <th class="col-md-2">@lang('Hour dispatch')</th>
                                <th>@lang('Round Trip')</th>
                                <th data-sorting="disabled">@lang('Turn')</th>
                                <th data-sorting="disabled">@lang('Actions')</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach( $dispatchRegisters as $dispatchRegister )
                            <tr>
                                <td>{{$dispatchRegister->vehicle}} <i class="fa fa-hand-o-right" aria-hidden="true"></i> {{$dispatchRegister->plate}}</td>
                                <td>{{$dispatchRegister->dispatch_time}}</td>
                                <td>{{$dispatchRegister->round_trip}}</td>
                                <td>{{$dispatchRegister->turn}}</td>
                                <td>
                                    <a href="#modal-route-report" data-toggle="modal"
                                       data-url="{{ route('chart-report',['dispatchRegister'=>$dispatchRegister->id]) }}"
                                       class="btn btn-sm btn-lime btn-link faa-parent animated-hover btn-show-chart-route-report">
                                        <i class="fa fa-area-chart faa-pulse"></i> @lang('Report detail')
                                    </a>
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
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups!')</strong></h4>
            <hr class="hr">
            @lang('No registers found')
        </div>
    </div>
@endif