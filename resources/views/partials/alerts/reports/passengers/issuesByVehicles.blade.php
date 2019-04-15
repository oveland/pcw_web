@php
    $index = 0;
@endphp
<div class="col-md-12 alert alert-info p-t-5 container-alert-new-values" style="display: none">
    <strong>
        <i class="fa fa-exclamation"></i> @lang('Registers updated')
    </strong>
    <button class="btn btn-info btn-xs" onclick="$('.form-search-report').submit()">
        <i class="fa fa-refresh"></i>
    </button>
    <p>@lang('Please refresh the report once you finish the fix bugs')</p>
</div>
@foreach($issuesByVehicles as $vehicle_id => $issuesByVehicle)
    @if( count($issuesByVehicle) )
        @php
            $index++;
            $vehicle = \App\Models\Vehicles\Vehicle::find($vehicle_id);
        @endphp
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
                        @php
                            $dispatchRegister = $issue->dispatchRegister;
                            $lastDispatchRegister = $issue->lastDispatchRegister;
                        @endphp
                        <li class="">
                            <div class="checkbox">
                                <label>
                                    <i class="fa fa-exclamation-triangle faa faa-tada animated text-warning tooltips" data-title="@lang('Error in') <b>{{ $issue->field }}</b>" data-html="true"></i>
                                </label>
                            </div>
                            <div class="info">
                                <h4 class="tooltips" data-title="{{ $dispatchRegister->status }}">
                                    {{ $dispatchRegister->route->name }},
                                    @lang('Turn') {{ $dispatchRegister->turn }},
                                    @lang('Round Trip') {{ $dispatchRegister->round_trip }},
                                    <small>{{ $dispatchRegister->status }}</small>
                                </h4>
                                @if( $dispatchRegister->user )
                                    <p class="tooltips" data-title="@lang('User') / @lang('Dispatcher')">
                                        <i class="fa fa-user"></i>
                                        {{ $dispatchRegister->user->name }}
                                    </p>
                                @endif
                                <p class="tooltips" data-title="@lang('Departure time')">
                                    <i class="fa fa-clock-o"></i>
                                    {{ $dispatchRegister->departure_time }}
                                </p>
                                <div class="tooltips text-{{  $issue->field == __('Start Recorder')?'issue':'' }} box-edit" data-title="@lang('Start Recorder')">
                                    <span class="box-info">
                                        <i class="fa fa-arrow-circle-right"></i>
                                        <span class="text-{{ $issue->bad_start_recorder ? 'warning':'' }}">
                                            {{ $issue->start_recorder }}
                                        </span>
                                    </span>
                                    <div class="box-edit" style="display: none">
                                        <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                               data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('start_recorder')"
                                               class="input-sm form-control edit-input-recorder" value="{{ $issue->start_recorder }}">
                                    </div>
                                </div>


                                <div class="tooltips text-{{  $issue->field == __('End Recorder')?'issue':'' }} box-edit" data-title="@lang('End Recorder')">
                                    <span class="box-info">
                                        <i class="fa fa-arrow-circle-left"></i>
                                        <span>
                                            {{ $dispatchRegister->end_recorder }}
                                        </span>
                                    </span>
                                    <div class="box-edit" style="display: none">
                                        <input id="edit-end-recorder-{{ $dispatchRegister->id }}" title="@lang('End Recorder')" name="" type="number"
                                               data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('end_recorder')"
                                               class="input-sm form-control edit-input-recorder" value="{{ $issue->end_recorder }}">
                                    </div>
                                </div>

                                @if( $issue->bad_start_recorder )
                                    <hr class="hr">
                                    <h4>@lang('Last dispatch register'):</h4>

                                    <h4 class="tooltips" data-title="{{ $lastDispatchRegister->status }}">
                                        {{ $lastDispatchRegister->route->name }},
                                        @lang('Turn') {{ $lastDispatchRegister->turn }},
                                        @lang('Round Trip') {{ $lastDispatchRegister->round_trip }}
                                    </h4>
                                    @if( $lastDispatchRegister->user )
                                    <p class="tooltips" data-title="@lang('User') / @lang('Dispatcher')">
                                        <i class="fa fa-user"></i>
                                        {{ $lastDispatchRegister->user->name }}
                                    </p>
                                    @endif
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
                            <div class="action hide">
                                <a href="#" data-toggle="dropdown" aria-expanded="false" class="tooltips" data-title="@lang('Options')">
                                    <i class="fa fa-ellipsis-v text-danger faa-ring animated"></i>
                                </a>
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