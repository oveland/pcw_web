@if(empty($routes))
    <option value="">{{ strtoupper(__('No routes found')) }}</option>
@else
    @if( isset($withAll) && ($withAll === true || $withAll === "true") )

        @php
            $defaultKmzUrl = isset($defaultKmzUrl) ? $defaultKmzUrl : ($routes->count() ? $routes->first()->company->default_kmz_url : "");
        @endphp

        <option data-kmz-url="{{ $defaultKmzUrl }}" value="all">{{ strtoupper(__('All routes')) }}</option>
    @else
        <option value="">{{ strtoupper(__('Select a route')) }}</option>
    @endif

    @if( Auth::user()->canViewAllRoutes() && isset($withNone) && ($withNone === true || $withNone === "true") )
        <option value="none">{{ strtoupper(__('Without route')) }}</option>
    @endif

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


