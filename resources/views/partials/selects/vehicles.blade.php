@if(empty($vehicles))
    <option value="">@lang('No vehicles found')</option>
@else
    <option value="">@lang('Select an option')</option>
    @foreach($vehicles as $vehicle)
        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }} #{{ $vehicle->number }}</option>
    @endforeach
@endif