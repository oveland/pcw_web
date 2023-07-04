<table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
    <thead>
    <tr class="inverse">
        <th class="">
            <i class="fa fa-car text-muted"></i><br>
            @lang('Vehículo')
        </th>
        <th class="">
            <i class="fa fa-bookmark text-muted"></i><br>
            @lang('Número Asientos')
        </th>
        <th class="">
            <i class="fa fa-camera text-muted"></i><br>
            @lang('Cámara')
        </th>
        <th class="">
            <i class="fa fa-camera-retro"></i><br>
            @lang('Infrarojo IR')
        </th>
    </tr>
    </thead>
    <tbody>
    @php
        $totalSeats = 0;
        $totalCameras = 0;
        $totalIR = 0;
    @endphp

    @foreach($topologies as $topologie)

        <tr>
{{--            <td class="text-center">{{$topologie->vehicle->number}}</td>--}}
            <th
                class="bg-inverse text-white text-center">
                 {{$topologie->vehicle->number}}
                <br>
                <i class="fa fa-arrow-right"> </i> <i class="fa fa-bus text-muted"> </i> <i class="fa fa-arrow-left"></i>
                <br>
                <small>
                    {{ $topologie->vehicle->plate }}
                </small>
            </th>
            <td class="text-center">{{$topologie->number_seats }}</td>
            <td class="text-center">{{($topologie->number_cam)}}</td>
            <td class="text-center">{{$topologie->number_ir}}</td>
        </tr>
        <?php $totalCameras++;
        ?>
        @php
            $totalSeats += $topologie->number_seats;
        @endphp
    @endforeach
    <tr style="background: #2b3643f7 !important; color: white">
        <td style="background: white !important;"><strong></strong></td>
        <td class="text-center"><strong>Total Asientos : {{$totalSeats}}</strong></td>
        <td class="text-center"><strong>Total camaras: {{$totalCameras}}</strong></td>
        <td class="text-center"></td>
    </tr>
    </tbody>
</table>

