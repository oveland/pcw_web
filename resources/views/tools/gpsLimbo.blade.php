@extends('layout')

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Administration')</a></li>
        <li><a href="javascript:;">@lang('GPS')</a></li>
        <li class="active">@lang('Limbo')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-cogs" aria-hidden="true"></i> @lang('Administration')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('GPS Limbo')</small>
    </h1>

    <!-- end page-header -->

    <div class="col-md-12" style="background: white;margin-bottom: 40px">
        @if($simGPSLimbo)
            <div class="col-md-12">
                <h5>{{ count($simGPSLimbo) }} vehículos en el Limbo</h5>
            </div>
            <div class="col-md-12 table-responsive">
                <table class="table table-striped table-bordered table-condensed table-report">
                    <thead>
                        <tr class="inverse">
                            <th>#</th>
                            <th>Interno</th>
                            <th>Placa</th>
                            <th>SIM</th>
                            <th>Imei</th>
                            <th>Tipo GPS</th>
                            <th>RUTA</th>
                            <th>Estado PCW</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($simGPSLimbo as $gps)
                            <tr>
                                <td>
                                    {{ $loop->iteration }}<br>
                                    <i class="{{ $gps->icon_class }} text-{{ $gps->main_class }}"></i>
                                </td>
                                <td>{{ $gps->number }}</td>
                                <td width="12%">{{ $gps->plate }}</td>
                                <td>
                                    <a class="tooltips" data-title="@lang("Call")" href='tel:{{ $gps->sim }}'>{{ $gps->sim }}</a>
                                </td>
                                <td>{{ $gps->imei }}</td>
                                <td>{{ $gps->hardware_name }}</td>
                                <td>{{ $gps->route_name }}</td>
                                <td id="" class="{{ $gps->vehicle_status_id != 1 ? "success":"" }}">
                                    {{ $gps->date }}<br>
                                    <small class="text-{{ $gps->vehicle_status_id == 1?"danger text-bold":"" }}">
                                        ({{ $gps->vehicle_status_id }}) {{ $gps->des_status }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            @include('partials.alerts.noRegistersFound')
        @endif
    </div>
@endsection


@section('scripts')
    <script type="application/javascript">
        $(document).ready(function () {

            setTimeout(()=>{
                window.location.reload()
            },10000);
        });
    </script>
@endsection

