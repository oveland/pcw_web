
@php
    $selected = collect( isset($selected) && is_array($selected) ? $selected : $selected )->values();
@endphp

@if(count($users))
    @foreach($users as $user)
        <option value="{{ $user->id }}" {{ $selected->contains($user->id) ? 'selected' : '' }}>{{ $user->username }}
            {{ isset($withName) ? "<br><small class='p-5 text-'><i class='fa fa-user text-success'></i> $user->name </small>" : '' }}
            {{ isset($withEmail) ? "<br><small class='p-5 text-'><i class='fa fa-envelope text-warning'></i> $user->email </small>" : '' }}
        </option>
    @endforeach
@endif