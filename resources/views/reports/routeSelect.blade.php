@forelse ($routes as $route)
    <option value="{{ $route->id_rutas }}">{{ $route->nombre }}</option>
@empty
    <option value="">@lang('No routes found')</option>
@endforelse