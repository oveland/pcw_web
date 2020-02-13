@php
    $error = $error ?? false;
    $updated = $updated ?? false;
    $assignedVehicles = $proprietary->assignedVehicles();
    $user = $proprietary->user;
@endphp
<td class="text-center bg-inverse text-white">
    <small>{!! $loop->iteration ?? 'editado' !!}</small>
    <br>
    <label class="label label-inverse tooltips" data-title="@lang('Proprietary ID')" style="font-size: 0.8em !important;"><i class="fa fa-rocket"></i> {{ $proprietary->id }}</label>
</td>
<td>{{ $proprietary->fullName() }}</td>
<td>{!! $user ? "<i class='fa fa-user-plus'></i> $user->username" : '' !!}</td>
<td>{{ $proprietary->cellphone }}</td>
<td width="20%">
    <select name="company" id="company" title="" class="default-select2 form-control col-md-12" multiple disabled>
        @foreach($assignedVehicles as $vehicle)
            <option value="{{ $vehicle->id }}" selected>{{ $vehicle->number }}</option>
        @endforeach
    </select>
    @if( $proprietary->passenger_report_via_sms )
        <i class="fa fa-envelope-square faa-passing animated text-info" style="font-size: 1.3em"></i>
    @endif
    <small class="pull-right">@lang('Total') {{ count($assignedVehicles) }}</small>
</td>
<td class="text-center">
    <button class="btn btn-sm btn-white tooltips btn-rounded" onclickkkk="$('#detail-{{ $id }}').addClass('hide');$('#edit-{{ $id }}').removeClass('hide');" data-title="@lang('Asigne un usuario al propietario para administrar sus vehículos en la sección Perfil de Usuario')">
        <i class="fa fa-edit"></i>
    </button>
</td>
<td class="hide">
    @if($error)
        <script>gerror('{{ $error }}')</script>
    @elseif($updated)
        <script>gsuccess('@lang('Data updated successfully')')</script>
    @endif
</td>

