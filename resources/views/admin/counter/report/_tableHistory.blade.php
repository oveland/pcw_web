<div class="table-responsive">
    <!-- begin table -->
    <table id="data-table" class="table table-bordered table-striped table-hover table-valign-middle table-report">
        <thead>
            <tr class="inverse">
                <th>
                    <i class="fa fa-list text-muted"></i><br>
                    @lang('N°')
                </th>
                <th>
                    <i class="fa fa-calendar text-muted"></i><br>
                    @lang('Date')
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('Passengers')
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('Total passengers')
                </th>
                <th>
                    <i class="fa fa-flag text-muted"></i><br>
                    @lang('Route')
                </th>
                <th>
                    <i class="fa fa-rocket text-muted"></i><br>
                    @lang('Actions')
                </th>
            </tr>
        </thead>
        <tbody>
        @php($prevFrame=null)
        @foreach($passengers as $passenger)
            @php($vehicle = $passenger->vehicle)
            @php($counterIssue = $passenger->counterIssue )
            @php($dispatchRegister = $passenger->dispatchRegister )
            <tr>
                <td class="text-center" width="5%">{{ $loop->index + $passengers->firstItem() }}</td>
                <td class="text-center">{{ $passenger->date }}</td>
                <td class="text-center">{{ ($passenger->total - $initialPassengerCount->total ) }}</td>
                <td class="text-center">{{ $passenger->total }}</td>
                <td class="text-center">
                    @if($dispatchRegister)
                        <div class="btn btn-info btn-sm btn-block"
                            data-placement="bottom"
                            data-toggle="popover"
                            data-html="true"
                            data-trigger="hover"
                            title="
                                <i class='fa fa-flag text-primary'></i> {{ $dispatchRegister->route->name }}<br>
                                <span class='f-s-12'><br>
                                    <i class='fa fa-retweet text-success'></i> <b>@lang('Round Trip') {{ $dispatchRegister->round_trip }}</b><br>
                                </span>
                                <span class='f-s-12'>
                                    <i class='fa fa-industry text-warning'></i> <b>{{ $dispatchRegister->status }}</b>
                                </span>
                            "
                            data-content="
                                <div style='width:200px'>
                                    <strong>@lang('Departure Time'):</strong> {{ $strTime::toString($dispatchRegister->departure_time) }}<br>
                                    <strong>@lang('Arrival Time'):</strong> {{ $strTime::toString($dispatchRegister->arrival_time) }}<br>
                                    @if($dispatchRegister->complete())<strong>@lang('Arrival Time Difference'):</strong> {{ $strTime::toString($dispatchRegister->arrival_time_difference) }}@endif
                             </div>"
                        >
                            {{ $dispatchRegister->route->name }}
                        </div>
                    @else
                        ----
                    @endif
                </td>
                <td class="text-center">
                    @if($counterIssue)
                        <button class="btn btn-sm btn-danger btn-show-counter-issue" data-action="{{ route('report-passengers-sensors-counter-issue',['counterIssue' => $counterIssue->id]) }}">
                            <i class="fa fa-exclamation-triangle"></i>
                            @lang('Issues')
                        </button>
                    @endif
                    <button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#frame-{{ $passenger->id }}">
                        <i class="ion-ios-search"></i>
                        @lang('See frame')
                    </button>
                </td>
            </tr>
            <tr id="frame-{{ $passenger->id }}" class="bg-inverse text-white text-bold collapse-frame collapse fade">
                <td colspan="6" class="p-l-4 p-r-4" style="font-family: monospace">
                    @php
                        $currentFrame = $passenger->frame;
                        $comparedFrame = \App\Http\Controllers\PassengerReportCounterController::compareChangeFrames($currentFrame,$prevFrame);
                        $prevFrame = $currentFrame;
                    @endphp
                    @foreach($comparedFrame as $frame)
                        <label class="p-0 text-center">
                            <span class="text-center p-0 {{ $frame->class }}" data-title="@lang('Prev value'): <b>{{ $frame->prevField }}</b>" data-html="true" style="border-bottom: 1px dotted gray">
                                {{ $frame->field }}
                            </span>
                            <br>
                            <small class="text-muted p-t-3 btn-block" style="border: 1px dotted gray">
                                {{ $loop->iteration }}
                            </small>
                        </label>
                    @endforeach
                    <button class="btn btn-copy btn-sm btn-default pull-right tooltips" data-title="@lang('Copy frame')" data-clipboard-text="{{ $currentFrame }}">
                        <i class="fa fa-copy"></i>
                    </button>
                    <div class="seating-template text-center">
                        {!! \App\Services\Reports\Passengers\SeatDistributionGualasService::makeHtmlTemplate($passenger) !!}
                    </div>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <!-- end table -->
</div>