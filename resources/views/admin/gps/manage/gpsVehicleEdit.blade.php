@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
    $currentLocation = $vehicle->currentLocation;
    $vehicleStatus = $currentLocation?$currentLocation->vehicleStatus:null;
@endphp
<td class="text-center bg-inverse text-white">{!! $loop->iteration !!}</td>
<td class="text-center">
    {!!  $vehicle->numberAndPlate ?? 'NONE'  !!}
    @if( $vehicleStatus )
        <br><small class="text-{{ $vehicleStatus->main_class }} tooltips" data-html="true" data-title="@lang('Last report'): {{ $currentLocation->date }} <br> @lang('Updated at'): {{ Carbon\Carbon::now()->toTimeString() }}">
            <i class="{{ $vehicleStatus->icon_class }}"></i> {{ $vehicleStatus->des_status }}
        </small>
    @endif
</td>
<td class="text-center" width="60%" colspan="3">
    <form id="form-edit-sim-gps-{{ $id }}" data-id="{{ $id }}" action="{{ route('admin-gps-manage-update-sim-gps',['simGPS' => $id]) }}" class="form-edit-sim-gps" data-target="#detail-{{ $id }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-3">
                <select id="gps-type-{{ $id }}" name="gps_type" class="form-control input-sm gps-type" title="@lang('GPS type')"
                        onchange="">
                    @foreach( \App\Models\Vehicles\SimGPS::DEVICES as $device )
                        <option value="{{ $device }}">{{ $device }}</option>
                    @endforeach
                </select>
                <script>
                    $('#gps-type-{{ $id }}').val('{{ $simGPS->gps_type }}');
                </script>
            </div>
            <div class="col-md-3 text-center ">
                <div class="form-group has-success has-feedback m-b-0">
                    <input name="imei" type="text" class="form-control input-sm" value="{{ $gpsVehicle ? $gpsVehicle->imei : '' }}" placeholder="Imei" style="border-radius: 50px">
                    <span class="fa fa-tag form-control-feedback"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-success has-feedback m-b-0">
                    <input name="sim" type="number" class="form-control input-sm" value="{{ $simGPS->sim }}" placeholder="SIM GPS" style="border-radius: 50px">
                    <span class="fa fa-phone form-control-feedback"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback m-b-0">
                    <input name="sim_router" type="number" class="form-control input-sm" value="{{ $simGPS->sim_router }}" placeholder="SIM Router" style="border-radius: 50px">
                    <span class="fa fa-wifi form-control-feedback"></span>
                </div>
            </div>
        </div>
        
        <div class="row m-t-10">
            <div class="col-md-3">
                <select name="technology" class="form-control input-sm" title="Technology">
                    <option value="">Ninguno</option>
                    <option value="3G" {{ ($gpsVehicle && $gpsVehicle->technology == '3G') ? 'selected' : '' }}>3G</option>
                    <option value="4G" {{ ($gpsVehicle && $gpsVehicle->technology == '4G') ? 'selected' : '' }}>4G</option>
                    <option value="5G" {{ ($gpsVehicle && $gpsVehicle->technology == '5G') ? 'selected' : '' }}>5G</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback m-b-0">
                    <input name="device_id" type="text" class="form-control input-sm" value="{{ $gpsVehicle ? $gpsVehicle->device_id : '' }}" placeholder="XVR 1" style="border-radius: 50px">
                    <span class="fa fa-video-camera form-control-feedback"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback m-b-0">
                    <input name="device_id_2" type="text" class="form-control input-sm" value="{{ $gpsVehicle ? $gpsVehicle->device_id_2 : '' }}" placeholder="XVR 2" style="border-radius: 50px">
                    <span class="fa fa-video-camera form-control-feedback"></span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group has-feedback m-b-0">
                    <input name="device_id_3" type="text" class="form-control input-sm" value="{{ $gpsVehicle ? $gpsVehicle->device_id_3 : '' }}" placeholder="XVR 3" style="border-radius: 50px">
                    <span class="fa fa-video-camera form-control-feedback"></span>
                </div>
            </div>
        </div>
        
        <div class="row m-t-10">
            <div class="col-md-3">
                <div class="form-group has-feedback m-b-0">
                    <input name="cameras_count" type="number" min="0" max="20" class="form-control input-sm" value="{{ $vehicle->cameras ? $vehicle->cameras->count() : '' }}" placeholder="# Cámaras" style="border-radius: 50px">
                    <span class="fa fa-camera form-control-feedback"></span>
                </div>
            </div>
        </div>
    </form>
</td>
<td class="text-center" colspan="2">
    <button type="button" class="btn btn-sm btn-default tooltips btn-rounded" onclick="$('#detail-{{ $id }}').removeClass('hide');$('#edit-{{ $id }}').addClass('hide');" data-title="@lang('Cancel')">
        <i class="fa fa-times"></i>
    </button>
    <button type="button" class="btn btn-sm btn-lime tooltips btn-rounded" onclick="$('#form-edit-sim-gps-{{ $id }}').submit()" data-title="@lang('Update')">
        <i class="fa fa-floppy-o"></i>
    </button>
    <button type="button" class="btn btn-sm btn-danger tooltips btn-rounded m-l-40" onclick="$('#form-delete-sim-gps-{{ $id }}').submit()" data-title="@lang('Delete')">
        <i class="fa fa-trash"></i>
    </button>
    <form id="form-delete-sim-gps-{{ $id }}" class="form-delete-sim-gps" action="{{ route('admin-gps-manage-delete-sim-gps',['simGPS' => $id]) }}">
        {{ csrf_field() }}
    </form>
</td>

