
@php
    $selected = collect( isset($selected) && is_array($selected) ? $selected : [] )->values();
@endphp

@if( $withAll ?? false )
    <option value="all">@lang('All')</option>
@endif

@if(count($users))
    @foreach($users as $user)
        <option value="{{ $user->id }}" {{ $selected->contains($user->id) ? 'selected' : '' }}>
            {{ $user->username }}
            @if(isset($withName))
                {{ "<br><small class='p-5 text-'><i class='fa fa-user text-success'></i> $user->name </small>" }}
            @endif
            @if(isset($withEmail))
                {{ "<br><small class='p-5 text-'><i class='fa fa-envelope text-warning'></i> $user->email </small>" }}
            @endif
        </option>
    @endforeach
@endif