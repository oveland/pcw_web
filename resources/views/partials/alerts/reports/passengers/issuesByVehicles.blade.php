@php($index = 0)
@foreach($issuesByVehicles as $vehicle_id => $issuesByVehicle)
    @if( count($issuesByVehicle) )
         @php($index++)
        @php( $vehicle = \App\Vehicle::find($vehicle_id) )
        <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 p-t-5">
            <!-- begin widget -->
            <div class="widget">
                <div class="widget-header bg-inverse p-10">
                    <h4 class="text-muted">
                        <i class="fa fa-car"></i>
                        <span class="issue-index">{{ $index }}</span>
                        {{ $vehicle->number }} | {{ $vehicle->plate }}
                    </h4>
                </div>
                <ul class="widget-todolist">
                    @foreach($issuesByVehicle as $issue)
                        @php($dispatchRegister = $issue->dispatchRegister)
                        @php($lastDispatchRegister = $issue->lastDispatchRegister)
                        <li class="">
                            <div class="checkbox">
                                <label>
                                    <i class="fa fa-exclamation-triangle text-warning tooltips" data-title="@lang('Error in') <b>{{ $issue->field }}</b>" data-html="true"></i>
                                </label>
                            </div>
                            <div class="info">
                                <h4 class="tooltips" data-title="{{ $dispatchRegister->status }}">
                                    {{ $dispatchRegister->route->name }},
                                    @lang('Turn') {{ $dispatchRegister->turn }},
                                    @lang('Round Trip') {{ $dispatchRegister->round_trip }}
                                </h4>
                                <p class="tooltips" data-title="@lang('Departure time')">
                                    <i class="fa fa-clock-o"></i>
                                    {{ $dispatchRegister->departure_time }}
                                </p>
                                <p class="tooltips text-{{  $issue->field == __('Start Recorder')?'issue':'' }}" data-title="@lang('Start Recorder')">
                                    <i class="fa fa-arrow-circle-right"></i>
                                    <span class="text-{{ $issue->bad_start_recorder ? 'warning':'' }}">
                                        {{ $issue->start_recorder }}
                                    </span>
                                </p>
                                <p class="tooltips text-{{  $issue->field == __('End Recorder')?'issue':'' }}" data-title="@lang('End Recorder')">
                                    <i class="fa fa-arrow-circle-left"></i>
                                    {{ $dispatchRegister->end_recorder }}
                                </p>
                                @if( $issue->bad_start_recorder )
                                    <hr class="hr">
                                    <h4>@lang('Last dispatch register'):</h4>

                                    <h4 class="tooltips" data-title="{{ $lastDispatchRegister->status }}">
                                        {{ $lastDispatchRegister->route->name }},
                                        @lang('Turn') {{ $lastDispatchRegister->turn }},
                                        @lang('Round Trip') {{ $lastDispatchRegister->round_trip }}
                                    </h4>
                                    <p class="tooltips" data-title="@lang('Departure time')">
                                        <i class="fa fa-clock-o"></i>
                                        {{ $lastDispatchRegister->departure_time }}
                                    </p>
                                    <p class="tooltips hide text-{{  $issue->field == __('Start Recorder')?'issue':'' }}" data-title="@lang('Start Recorder') @lang('prev')">
                                        <i class="fa fa-arrow-circle-right"></i>
                                        {{ $lastDispatchRegister->start_recorder }}
                                    </p>
                                    <p class="tooltips" data-title="@lang('End Recorder') @lang('prev')">
                                        <i class="fa fa-arrow-circle-left"></i>
                                        <span class="text-warning">
                                            {{ $lastDispatchRegister->end_recorder }}
                                        </span>
                                    </p>
                                @endif
                            </div>
                            <div class="action">
                                <a href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li>
                                        <a href="javascript:alert('@lang('Feature on development')');" class="disabled">
                                            <i class="fa fa-cog fa-spin"></i>
                                            @lang('Edit')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
            <!-- end widget -->
        </div>
    @endif
@endforeach