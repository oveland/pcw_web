<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Periods</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
<div>
    <div class="container">
        <div class="jumbotron mt-3">
            <h1>Vehículos pendientes de migración a 20 segundos</h1>
            <div class="table-responsive">
                <table class="table table-sm table-dark">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">@lang('Route')</th>
                        <th scope="col">@lang('Vehicle')</th>
                        <th scope="col">@lang('GPS')</th>
                        <th scope="col">@lang('Locations')</th>
                        <th scope="col">@lang('Period')</th>
                        <th scope="col">@lang('Status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($measures as $measure)
                        @php
                            $vehicle = $measure->vehicle;
                        @endphp
                        <tr class="{{ $measure->bgStatus }}">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $measure->route ? $measure->route->name : __("Unassigned") }}</td>
                            <td>{{ $vehicle->number }}</td>
                            <td>{{ $vehicle->gpsVehicle->type->name }}</td>
                            <td>{{ $measure->totalLocations }}</td>
                            <td>{{ $measure->averagePeriod }}</td>
                            <td>{{ $measure->status }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>