@php
    $selected = $selected ?? "";
@endphp

@if(count($binnacleTypes))
    @foreach($binnacleTypes as $binnacle)
        <option value="{{ $binnacle->id }}" {{ $selected == $binnacle->id ? 'selected' : '' }} data-description="{{ $binnacle->description }}">{{ $binnacle->name }}</option>
    @endforeach
@endif