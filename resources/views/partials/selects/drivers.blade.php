@if(empty($drivers))
    <option value="">@lang('No drivers found')</option>
@else
    @if( $withAll ?? false )<option value="all">@lang('All drivers')</option>
    @else <option value="">@lang('Select a driver')</option> @endif

    @foreach($drivers as $driver)
        <option value="{{ $driver->id }}">{{ $driver->code }} | {{ $driver->fullName }}</option>
    @endforeach
@endif


