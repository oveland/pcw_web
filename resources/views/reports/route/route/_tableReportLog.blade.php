
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
      integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
@if($reports->isNotEmpty())
<div class='row'>
    <div class='col-md-12'>
        <table class='table table-borderless table-hover table-striped table-dark table-sm'>
            <thead>
            <tr>
                <th>#</th>
                <th>@lang('Date')</th>
                <th>@lang('Order') - @lang('Control Point')</th>
                <th>@lang('Distance')</th>
                <th>@lang('Distance') M</th>
                <th>@lang('Time') D</th>
            </tr>
            </thead>
            @foreach ($reports as $report)
                @php
                    $date = explode('.',$report->date)[0];
                    $controlPoint = $report->controlPoint;
                @endphp
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $date }}</td>
                    <td>{{ $controlPoint->order }} - {{ $controlPoint->name }}</td>
                    <td>{{ $controlPoint->distance_from_dispatch }}</td>
                    <td>{{ $report->distancem }}</td>
                    <td>{{ $report->timed }}</td>
                </tr>
            @endforeach
        </table>
    </div>
</div>
@else
@include('partials.alerts.noRegistersFound')
@endif