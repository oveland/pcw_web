@if(empty($fringes))
    <option value="">@lang('No fringes found')</option>
@else
    <option value="">@lang('All')</option>
    @php
        $fringesByDaytypes = collect($fringes)->groupBy('day_type_id')
    @endphp
    @foreach($fringesByDaytypes as $dayTypeId => $fringesByDayType)
        @php
            $dayType = \App\Models\Routes\DayType::find($dayTypeId)
        @endphp

        <optgroup label="{{ $dayType->description }}">
            @foreach($fringesByDayType as $fringe)
                <option value="{{ $fringe->id }}">
                    {{ $fringe->name }} <i class="fa fa-flask"></i>
                </option>
            @endforeach
        </optgroup>
    @endforeach
@endif


