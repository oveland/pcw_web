@if(empty($routes))
    <option value="">@lang('No routes found')</option>
@else
    @if( $withAll ?? false )<option value="all">@lang('All routes')</option>
    @else <option value="">@lang('Select a route')</option> @endif
    @foreach($routes as $route)
        <option value="{{ $route->id }}">{{ $route->name }}</option>
    @endforeach
@endif