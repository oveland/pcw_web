@if(empty($vehicles))
    <option value="">@lang('No vehicles found')</option>
@else
    @php
        $vehicles = collect($vehicles)->sortBy(function($v){
            return intval($v->number);
        });
    @endphp

    @foreach($vehicles as $vehicle)
        <option value="{{ $vehicle->id }}">#{{ $vehicle->number }} | {{ $vehicle->plate }}</option>
    @endforeach
@endif