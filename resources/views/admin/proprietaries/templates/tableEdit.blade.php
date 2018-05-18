@php
    $error = $error ?? false;
    $updated = $updated ?? false;
    $assignedVehicles = $proprietary->assignedVehicles;
    $totalAssignedVehicles = count($assignedVehicles);
    $assignedIdVehicles = $totalAssignedVehicles?$assignedVehicles->pluck('vehicle_id')->toArray():[];
@endphp
<td class="text-center bg-inverse text-white">{!! $loop->iteration ?? '*' !!}</td>
<td>{{ $proprietary->first_name }}</td>
<td>{{ $proprietary->surname }}</td>
<td>
    <input name="cellphone" class="form-control input-sm" value="{{ $proprietary->cellphone }}" placeholder="@lang('Cellphone')">
</td>
<td width="20%">
    <select name="company" id="company" title="" class="default-select2 form-control col-md-12" multiple>
        @foreach($vehicles as $vehicle)
            @php( $selected = in_array($vehicle->id,$assignedIdVehicles)?'selected':'' )
            <option value="{{ $vehicle->id }}" {{ $selected }}>{{ $vehicle->numberAndPlate() }}</option>
        @endforeach
    </select>
    <div class="m-t-1">
        @if( $proprietary->passenger_report_via_sms )
            <button class="btn btn-xs btn-success disabled">
                <i class="fa fa-envelope faa-passing animated"></i>
            </button>
            <button class="btn btn-xs btn-default">
                <i class="fa fa-envelope"></i>
            </button>
        @else
            <button class="btn btn-xs btn-success">
                <i class="fa fa-envelope faa-passing animated"></i>
            </button>
            <button class="btn btn-xs btn-default disabled">
                <i class="fa fa-envelope"></i>
            </button>
        @endif
    </div>
</td>
<td class="text-center">
    <button class="btn btn-sm btn-white tooltips btn-rounded" onclick="$('#edit-{{ $id }}').addClass('hide');$('#detail-{{ $id }}').removeClass('hide');" data-title="@lang('Cancel')">
        <i class="fa fa-undo"></i>
    </button>
    <button class="btn btn-sm btn-success tooltips btn-rounded disabled" onclick="ginfo('@lang('Feature on development')')" data-title="@lang('Save')">
        <i class="fa fa-cog fa-spin"></i>
    </button>
</td>
<td class="hide">
    @if($error)
        <script>gerror('{{ $error }}')</script>
    @elseif($updated)
        <script>gsuccess('@lang('Data updated successfully')')</script>
    @endif
</td>

