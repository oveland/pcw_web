@if(empty($roundTrips))
    <option value="">@lang('No round trips found')</option>
@else
    <option value="">@lang('Round Trip')</option>
    @foreach($roundTrips as $roundTrip)
        <option value="{{ $roundTrip }}">{{ $roundTrip }}</option>
    @endforeach
@endif