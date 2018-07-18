<div class="bg-inverse text-white text-bold col-md-12 p-5" style="font-family: monospace">
    @php
        $comparedFrame = \App\Http\Controllers\PassengerReportCounterController::compareChangeFrames($currentFrame,$prevFrame ?? $currentFrame);
    @endphp
    @if( $comparedFrame )
        @foreach($comparedFrame as $frame)
            <label class="p-0 text-center">
                <span class="text-center p-0 {{ $frame->class }}" data-title="@lang('Prev value'): <b>{{ $frame->prevField }}</b>" data-html="true" style="border-bottom: 1px dotted gray">
                    {{ $frame->field }}
                </span>
                <br>
                <small class="text-muted p-t-3 btn-block" style="border: 1px dotted gray">
                    {{ $loop->index }}°
                </small>
            </label>
        @endforeach
        <button class="btn btn-copy btn-sm btn-default pull-right tooltips" data-title="@lang('Copy frame')" data-clipboard-text="{{ $currentFrame }}">
            <i class="fa fa-copy"></i>
        </button>
    @else
        <pre class="pre p-1 col-md-3 m-0">
            @lang('Not frame found')
        </pre>
    @endif
</div>