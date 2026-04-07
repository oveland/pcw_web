@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
    $company = $simGPS->vehicle->company->id;
    $currentLocation = $vehicle->currentLocation;
    $vehicleStatus = $currentLocation?$currentLocation->vehicleStatus:null;
    $error = $error ?? false;
    $updated = $updated ?? false;
@endphp
<td class="text-center bg-inverse text-white">{!! $loop->iteration ?? '*' !!}</td>
<td class="text-center">
    {!!  $vehicle->numberAndPlate ?? 'NONE'  !!}
    @if( $vehicleStatus )
        <br><small class="text-{{ $vehicleStatus->main_class }}
                tooltips" data-html="true"
                   data-title="@lang('Last report'): {{ $currentLocation->date }} <br> @lang('Updated at'): {{ Carbon\Carbon::now()->toTimeString() }}">
            <i class="{{ $vehicleStatus->icon_class }}"></i> {{ $vehicleStatus->des_status }}
        </small>
    @else
        <br><small class="badge badge-danger m-b-5 tooltips" data-html="true" data-title="@lang('Description')">
            <i class="fa fa-tag"></i> @lang('New')
        </small>
    @endif
</td>
<td width="20%" class="text-center">
    <span class="btn btn-sm btn-rounded btn-{{ $simGPS->getGPSTypeCssColor() }}">
        <i class="icon-tag"></i> {{ $simGPS->gps_type }}
    </span>
</td>
<td width="20%" class="text-center">
  <span>
    {{ $gpsVehicle && $gpsVehicle->imei ? $gpsVehicle->imei : '' }}
      @if($gpsVehicle && ($gpsVehicle->device_id || $gpsVehicle->tags || $gpsVehicle->type_device))
          <div class="gps-info">
              @if(isset($gpsVehicle->technology) && $gpsVehicle->technology == '5G')
                  <span class="badge badge-info" style="margin-bottom: 2px;">5G</span><br>
                  <span title="XVR 1"><strong></strong> {{ $gpsVehicle->device_id ?? '' }}</span><br>
                  <span title="XVR 2"><strong></strong> {{ $gpsVehicle->device_id_2 ?? '' }}</span><br>
                  <span title="XVR 3"><strong></strong> {{ $gpsVehicle->device_id_3 ?? '' }}</span>
              @elseif($gpsVehicle->device_id && $gpsVehicle->tags)
                  <span title="XVR 1"><strong></strong> {{ $gpsVehicle->device_id ?? '' }}</span><br>
                  <span title="XVR 2"><strong></strong> {{ $gpsVehicle->tags ?? '' }}</span>
              @elseif($gpsVehicle->device_id)
                  <span title="XVR 1"><strong></strong> {{ $gpsVehicle->device_id ?? '' }}</span>
              @endif
        </div>
      @endif
  </span>
</td>
<td class="text-center" width="20%">
    <div style="display: inline-flex; align-items: center; gap: 5px;">
        <small class="text-muted" style="display: inline-block; width: 70px; text-align: right;">SIM GPS</small>
        <button class="btn btn-{{ $simGPS->getOperatorCssColor() }} btn-rounded active tooltips"
                data-title="{{ strtoupper($simGPS->operator) }}">
            {!! $simGPS->getUrlImageOperator() !!}
            {{ $simGPS->sim }}
        </button>
    </div>
    @if($simGPS->sim_router)
        <div style="display: inline-flex; align-items: center; gap: 5px; margin-top: 6px;">
            <small class="text-muted" style="display: inline-block; width: 70px; text-align: right;">SIM Router</small>
            <button class="btn btn-default btn-rounded btn-xs tooltips"
                    data-title="SIM Router">
                <i class="fa fa-wifi"></i>
                {{ $simGPS->sim_router }}
            </button>
        </div>
    @endif
</td>
<td class="text-center">
    {{ $simGPS->created_at }}
    <hr class="hr">
    {{ $simGPS->updated_at }}
</td>
<td class="text-center">
    <button class="btn btn-sm btn-white tooltips btn-rounded"
            onclick="$('#detail-{{ $id }}').addClass('hide');$('#edit-{{ $id }}').removeClass('hide');"
            data-title="@lang('Edit')">
        <i class="fa fa-edit"></i>
    </button>
</td>
<td class="hide">
    @if($error)
        <script>gerror('{{ $error }}')</script>
    @elseif($updated)
        <script>gsuccess('{{ $message }}')</script>
    @endif
</td>
<style>
    td {
        border-bottom: 1.5px solid darkgrey;
        padding-bottom: 10px;
    }

</style>
