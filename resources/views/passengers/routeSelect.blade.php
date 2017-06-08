<option value="">@lang('Select a route')</option>
@forelse ($routes as $route)
    <option value="{{ $route->id }}">{{ $route->name }}</option>
@empty
    <option value="">@lang('No routes found')</option>
@endforelse