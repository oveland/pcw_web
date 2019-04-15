@php
    $vehicle = $counterIssue->vehicle;
    $itemsIssues = collect(json_decode($counterIssue->items_issues,true));
@endphp
<tr>
    <td class="text-center" width="5%">{!! $loop->iteration  or 1 !!} </td>
    <td width="30%">{{ $counterIssue->date }}</td>
    <td>{{ $counterIssue->total }}</td>
    <td width="30%">
        @foreach($itemsIssues as $item => $issues)
            <div class="widget widget-stat bg-warning text-white p-5 m-b-5">
                <div class="row">
                    <div class="col-md-3 text-right p-0 m-t-5 p-r-5" style="border-right: 1px dotted white">
                        <p class="text-uppercase text-bold m-0">@lang('Item') {{ $item }}</p>
                    </div>
                    <div class="col-md-9 p-0 m-0">
                        <ul>
                            @foreach($issues as $name => $issue)
                                <li style="text-align: left">
                                    <small class="text-white text-bold" style="float: left">@lang($name):
                                        @if(is_array($issue))
                                            <ul>
                                                @foreach($issue as $field => $value)
                                                    <li>
                                                        F[{{ $field }}] = {{ $value }}
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @else
                                            <cite title="">{{ $issue }}</cite>
                                        @endif
                                    </small>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endforeach
    </td>
    <td>
        @if($counterIssue->raspberry_cameras_issues)
            <div class="btn btn-danger btn-sm">
                {{ $counterIssue->raspberry_cameras_issues }}
            </div>
        @endif
    </td>
    <td width="30%">
        @if($counterIssue->raspberry_check_counter_issue)
            <div class="btn btn-info btn-sm">
                {{ explode('.',$counterIssue->raspberry_check_counter_issue)[0] }}
            </div>
        @endif
    </td>
    <td>
        <button class="btn btn-sm btn-primary" data-toggle="collapse" data-target="#frame-issue-{{ $counterIssue->id }}">
            <i class="ion-ios-search"></i>
            @lang('See frame')
        </button>
    </td>
</tr>
<tr id="frame-issue-{{ $counterIssue->id }}" class="bg-inverse text-white text-bold collapse collapse-frame fade">
    <td colspan="7" style="font-family: monospace">
        @php
            $currentFrame = $counterIssue->frame;
            $comparedFrame = \App\Http\Controllers\PassengerReportCounterController::compareChangeFrames($currentFrame,$currentFrame);
        @endphp
        <span>
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
        </span>
        <button class="btn btn-copy btn-sm btn-default pull-right tooltips" data-title="@lang('Copy frame')" data-clipboard-text="{{ $counterIssue->frame }}">
            <i class="fa fa-copy"></i>
        </button>
    </td>
</tr>