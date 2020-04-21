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
                    @lang('Passengers') | @lang('Date')
                </th>
                <th>
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('Total passengers')
                </th>
                <th>
                    <i class="fa fa-battery text-muted"></i><br>
                    @lang('Liquid level')
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
                <td class="text-center p-l-20">
                    <span class="text-{{ $passenger->vehicleStatus->main_class }}" disabled style="text-transform: capitalize !important;width: 80%;text-align: start">
                        <i class="fa {{ $passenger->vehicleStatus->icon_class }}"></i>
                        {{ \Illuminate\Support\Str::lower($passenger->vehicleStatus->des_status) }}
                    </span>
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
                    <pre class="pre" style="background: rgba(105,104,104,0.45);color: darkgrey;font-family: consolas, monospace !important;width: 20%;margin: auto;">
                        <button class="btn btn-copy btn-sm btn-default pull-right tooltips" data-title="@lang('Copy frame')" data-clipboard-text="{{ $currentFrame }}" style="float: right">
                        <i class="fa fa-copy"></i>
                    </button>
                        {{ $currentFrame }}
                    </pre>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <!-- end table -->
</div>