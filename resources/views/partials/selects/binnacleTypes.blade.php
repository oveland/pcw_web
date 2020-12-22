@php
    $selected = $selected ?? "";
@endphp

@if(count($binnacleTypes))
    @foreach($binnacleTypes as $binnacle)
        <option value="{{ $binnacle->id }}" {{ $selected == $binnacle->id ? 'selected' : '' }} data-description="{{ $binnacle->description }}">
            {{ "<i class='fa fa-circle text-$binnacle->css_class'></i> ". $binnacle->name }}
        </option>
    @endforeach
@endif