@php
    $error = $error ?? false;
    $updated = $updated ?? false;
    $assignedVehicles = $proprietary->assignedVehicles;
@endphp
<td class="text-center bg-inverse text-white">
    {!! $loop->iteration ?? '*' !!}
    <br>
    <strong>ID {{ $proprietary->id }}</strong>
</td>
<td>{{ $proprietary->first_name }}</td>
<td>{{ $proprietary->surname }}</td>
<td>{{ $proprietary->cellphone }}</td>
<td width="20%">
    <select name="company" id="company" title="" class="default-select2 form-control col-md-12" multiple disabled>
        @foreach($assignedVehicles as $assignation)
            @php
                $vehicle = $assignation->vehicle;
            @endphp
            <option value="{{ $vehicle->id }}" selected>{{ $vehicle->numberAndPlate() }}</option>
        @endforeach
    </select>
    @if( $proprietary->passenger_report_via_sms )
        <i class="fa fa-envelope-square faa-passing animated text-info" style="font-size: 1.3em"></i>
    @endif
    <small class="pull-right">@lang('Total') {{ count($assignedVehicles) }}</small>
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

