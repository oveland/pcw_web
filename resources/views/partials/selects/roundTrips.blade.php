@if($dispatchRegisters->isEmpty())
    <option value="">@lang('No round trips found')</option>
@else
    <option value="">@lang('Round Trip')</option>
    @foreach($dispatchRegisters as $dispatchRegister)
        <option data-dispatch-register-id="{{ $dispatchRegister->id }}" value="{{ $dispatchRegister->round_trip }}">{{ $dispatchRegister->round_trip }}</option>
    @endforeach
@endif