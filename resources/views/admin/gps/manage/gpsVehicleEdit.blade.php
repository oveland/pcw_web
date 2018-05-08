@php
    $id = $simGPS->id;
    $vehicle = $simGPS->vehicle;
@endphp
<td class="text-center bg-info text-white">{!! $loop->iteration !!}</td>
<td>{{ $vehicle->plate ?? 'NONE'  }}</td>
<td>{{ $vehicle->number ?? 'NONE'  }}</td>
<td width="40%" colspan="2">
    <form id="form-edit-sim-gps-{{ $id }}" data-id="{{ $id }}" action="{{ route('admin-gps-manage-update-sim-gps',['simGPS' => $id]) }}" class="form-edit-sim-gps" data-target="#detail-{{ $id }}">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-md-6">
                <select id="gps-type-{{ $id }}" name="gps_type" class="form-control input-sm" title="@lang('GPS type')">
                    <option value="SKY">SKYPATROL</option>
                    <option value="TR">COBAN</option>
                </select>
                <script>$('#gps-type-{{ $id }}').val('{{ $simGPS->gps_type }}')</script>
            </div>
            <div class="col-md-6">
                <div class="form-group has-success has-feedback m-b-0">
                    <input name="sim" type="number" class="form-control input-sm" value="{{ $simGPS->sim }}" placeholder="SIM" style="border-radius: 50px">
                    <span class="fa fa-phone form-control-feedback"></span>
                </div>
            </div>
        </div>
    </form>
</td>
<td class="text-center">
    <button type="button" class="btn btn-sm btn-default tooltips btn-rounded" onclick="$('#detail-{{ $id }}').removeClass('hide');$('#edit-{{ $id }}').addClass('hide');" data-title="@lang('Cancel')">
        <i class="fa fa-undo"></i>
    </button>
    <button type="button" class="btn btn-sm btn-success tooltips btn-rounded" onclick="$('#form-edit-sim-gps-{{ $id }}').submit()" data-title="@lang('Update')">
        <i class="fa fa-floppy-o"></i>
    </button>
    <button type="button" class="btn btn-sm btn-danger tooltips btn-rounded m-l-20" onclick="$('#form-delete-sim-gps-{{ $id }}').submit()" data-title="@lang('Delete')">
        <i class="fa fa-times"></i>
    </button>
    <form id="form-delete-sim-gps-{{ $id }}" class="form-delete-sim-gps" action="{{ route('admin-gps-manage-delete-sim-gps',['simGPS' => $id]) }}">
        {{ csrf_field() }}
    </form>
</td>

