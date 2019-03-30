@if(empty($routes))
    <option value="">@lang('No routes found')</option>
@else
    @if( $withAll ?? false )<option value="all">@lang('All routes')</option>
    @else <option value="">@lang('Select a route')</option> @endif

    @php
        $typeRoutes = $routes->groupBy('as_group');
    @endphp

    @foreach($typeRoutes as $asGroup => $routes)
        <optgroup label="{{ $typeRoutes->count() > 1 ? ($asGroup ?'Grupos':'Individuales') : '' }}">
            @foreach($routes as $route)
                <option data-kmz-url="{{ $route->url }}" value="{{ $route->id }}">{{ $route->name }}</option>
            @endforeach
        </optgroup>
    @endforeach
@endif


