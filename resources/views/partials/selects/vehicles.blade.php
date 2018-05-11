@if(empty($vehicles))
    <option value="">@lang('No vehicles found')</option>
@else
    @foreach($vehicles as $vehicle)
        <option value="{{ $vehicle->id }}">{{ $vehicle->plate }} #{{ $vehicle->number }}</option>
    @endforeach
@endif