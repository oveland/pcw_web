@php
    $selected = $selected ?? "";
@endphp

@if(empty($vehicles))
    <option value="">@lang('No vehicles found')</option>
@else
    @if( $withAll ?? false )
        <option value="all">@lang('All')</option>
    @endif
    @if( $withOnlyActive ?? false )
        <option value="active" {{ $selected == 'active' ? 'selected' : '' }}>@lang('Only active')</option>
    @endif

    @if( $tags ?? false )
        @foreach($tags as $tag)
            <option value="{{ $tag->id }}" {{ $selected == $tag->id ? 'selected' : '' }}>{{ __($tag->name)  }}</option>
        @endforeach
    @endif

    @php
        $vehicles = collect($vehicles)->sortBy(function($v){
            return intval($v->number);
        });
    @endphp

    @foreach($vehicles as $vehicle)
        <option value="{{ $vehicle->id }}" {{ $selected == $vehicle->id ? 'selected' : '' }}>#{{ $vehicle->number }} | {{ $vehicle->plate }}</option>
    @endforeach
@endif