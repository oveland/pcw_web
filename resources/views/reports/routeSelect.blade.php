@if(empty($routes))
    <option value="">@lang('No routes found')</option>
@else
    <option value="">@lang('Select a route')</option>
    @foreach($routes as $route)
        <option value="{{ $route->id }}">{{ $route->name }}</option>
    @endforeach
@endif