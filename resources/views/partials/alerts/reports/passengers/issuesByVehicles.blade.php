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
                                    {{ $issue->start_recorder }}
                                </p>
                                <p class="tooltips text-{{  $issue->field == __('End Recorder')?'issue':'' }}" data-title="@lang('End Recorder')">
                                    <i class="fa fa-arrow-circle-left"></i>
                                    {{ $dispatchRegister->end_recorder }}
                                </p>
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