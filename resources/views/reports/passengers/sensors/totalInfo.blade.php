@if( $initialPassengerCount && $lastPassengerCount )
    <div style="width: 25%;border-top: 1px solid #505050;" class="m-t-10 p-t-5">
        <small class="clearfix f-s-15 text-white">
            <i class="icon-users"></i>
            <strong>{{ $lastPassengerCount->total - $initialPassengerCount->total }}</strong> @lang('passengers')
        </small>
    </div>
@endif