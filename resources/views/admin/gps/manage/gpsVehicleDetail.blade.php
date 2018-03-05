@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
    $error = $error ?? false;
    $updated = $updated ?? false;
@endphp

<td>{{ $vehicle->plate ?? 'NONE'  }}</td>
<td>{{ $vehicle->number ?? 'NONE'  }}</td>
<td width="20%">{{ $simGPS->getGPSType() }}</td>
<td width="20%">{{ $simGPS->sim }}</td>
<td class="text-center">
    <button class="btn btn-sm btn-info tooltips" onclick="$('#detail-{{ $id }}').addClass('hide');$('#edit-{{ $id }}').removeClass('hide');" data-title="@lang('Edit')">
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

