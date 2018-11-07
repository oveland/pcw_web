@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
    $currentLocation = $vehicle->currentLocation;
    $vehicleStatus = $currentLocation?$currentLocation->vehicleStatus:null;
    $error = $error ?? false;
    $updated = $updated ?? false;
@endphp
<td class="text-center bg-inverse text-white">{!! $loop->iteration ?? '*' !!}</td>
<td class="text-center">
    {!!  $vehicle->numberAndPlate ?? 'NONE'  !!}
    @if( $vehicleStatus )
        <br><small class="text-{{ $vehicleStatus->main_class }} tooltips" data-html="true" data-title="@lang('Last report'): {{ $currentLocation->date }} <br> @lang('Updated at'): {{ Carbon\Carbon::now()->toTimeString() }}">
            <i class="{{ $vehicleStatus->icon_class }}"></i> {{ $vehicleStatus->des_status }}
        </small>
    @endif
</td>
<td width="20%" class="text-center">
    <span class="btn btn-sm btn-rounded btn-{{ $simGPS->getGPSTypeCssColor() }}">
        <i class="icon-tag"></i> {{ $simGPS->gps_type }}
    </span>
</td>
<td width="20%" class="text-center">
    <span class="{{ $gpsVehicle->hasValidImei()?'':'text-danger text-bold tooltips' }}" data-title="@lang('The imei must have a length of 15 characters')">
        {{ $gpsVehicle->imei }}
    </span>
</td>
<td class="text-center" width="20%">
    <button class="btn btn-{{ $simGPS->getOperatorCssColor() }} btn-rounded active tooltips" data-title="{{ strtoupper($simGPS->operator) }}">
        {!! $simGPS->getUrlImageOperator() !!}
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

