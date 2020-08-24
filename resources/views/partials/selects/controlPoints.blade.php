@if(empty($controlPoints))
    <option value="">@lang('No control points found')</option>
@else
    @foreach($controlPoints as $controlPoint)
        <option value="{{ $controlPoint->id }}">{{ $controlPoint->order }} - {{ $controlPoint->trajectory ? 'REGRESO': 'IDA' }} | {{ $controlPoint->name }}</option>
    @endforeach
@endif


