@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
    $error = $error ?? false;
    $updated = $updated ?? false;
@endphp

<td>{{ $vehicle->plate ?? 'NONE'  }}</td>
<td>{{ $vehicle->number ?? 'NONE'  }}</td>
<td width="20%">{{ $simGPS->getGPSType() }}</td>
<td width="20%">
    <button class="btn btn-{{ $simGPS->operator == 'movistar'?'info':($simGPS->operator == 'avantel'?'purple':'white') }} btn-rounded active">
        @if( $simGPS->operator == 'movistar' )
            <img src="https://cdn.iconverticons.com/files/png/1a712eaf7266f623_256x256.png" width="15px">
        @elseif( $simGPS->operator == 'avantel' )
            <img src="https://static-s.aa-cdn.net/img/gp/20600003166342/9dHdvx-VIURI3nv_XqIiGitDqIOCZ0ZlvFcISfYSF7EnoqM1VjC78aujcafZwocwgA=w300" width="20px">
        @else
        @endif
        {{ $simGPS->sim }}
    </button>
</td>
<td class="text-center">
    <button class="btn btn-sm btn-white tooltips btn-rounded" onclick="$('#detail-{{ $id }}').addClass('hide');$('#edit-{{ $id }}').removeClass('hide');" data-title="@lang('Edit')">
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

