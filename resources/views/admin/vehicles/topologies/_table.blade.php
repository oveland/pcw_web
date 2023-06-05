<table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
    <thead>
    <tr class="inverse">
        <th class="">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Numero Vehiculo')
        </th>
        <th class="">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Numero Asientos')
        </th>
        <th class="">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Numero Camaras')
        </th>
        <th class="">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Numero IR')
        </th>

    </tr>
    </thead>
    <tbody>

        @foreach($topologies as $topologie)
            <tr>
                <td class="text-center">{{$topologie->vehicle_id}}</td>


                <td class="text-center">{{$topologie->number_seats }}</td>


                <td class="text-center">{{$topologie->number_cam}}</td>


                <td class="text-center">{{$topologie->number_ir}}</td>
            </tr>
        @endforeach
    </tbody>
</table>

