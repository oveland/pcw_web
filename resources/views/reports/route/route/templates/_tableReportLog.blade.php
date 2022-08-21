@include('layouts.template.header')

@if($locationsReports->notEmpty)

<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
<link href="{{asset('assets/plugins/bootstrap-calendar/css/bootstrap_calendar.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/DataTables/media/css/dataTables.bootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/DataTables/extensions/FixedHeader/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet" />
<link href="{{asset('assets/plugins/DataTables/extensions/Responsive/css/responsive.bootstrap.min.css')}}" rel="stylesheet" />
<style>
    table.dataTable.compact thead th,table.dataTable.compact thead td {
        font-size: 80% !important;
        padding: 4px 17px 4px 4px
    }

    table.dataTable.compact tfoot th,table.dataTable.compact tfoot td {
        padding: 4px;
        font-size: 80% !important;
    }

    table.dataTable.compact tbody th,table.dataTable.compact tbody td {
        padding: 4px;
        font-size: 65% !important;
    }
    .row{
        margin: 0 !important;
    }
</style>
<!-- ================== END PAGE LEVEL CSS STYLE ================== -->

<div class=''>
    <div id="head-data" class="col-md-12 text-center">
        @lang('Total'): {{ $reports->count() }} @lang('registers')
    </div>
    <div class="panel pagination-lime clearfix m-b-0">
        <table id="data-table" class='table table-hover table-striped compact' style="display: none">
            <thead>
            <tr>
                <th data-sortable="true">#</th>
                <th data-sortable="true">@lang('R Time')<br>hh:mm:ss</th>
                <th data-sortable="true">@lang('Order')<br>@lang('CP') @lang('Dist.')</th>
                <th data-sortable="true">@lang('R Dist. M')<br>m</th>
                <th data-sortable="true">@lang('R Time St')<br>s</th>
                <th data-sortable="true">@lang('R Odo')<br>m</th>
                <th data-sortable="true">@lang('R Mil.')<br>m</th>
                <th data-sortable="true">@lang('R Speed')<br>km/h</th>
                <th>@lang('Δ dist.')<br>m</th>
                <th>@lang('Δ time')<br>s</th>
                <th>@lang('Δ speed')<br>km/h</th>
                <th>@lang('Σ Dist.')<br>m</th>
            </tr>
            </thead>
            @php
                $lastReport = $reports->first();
                $lastLocation = $lastReport->location;
                $lastOrderCP = 0;
                $totalDistance = 0;
            @endphp
            @foreach ($reports as $report)
                @php
                    $time = $report->date->toTimeString();
                    $controlPoint = $report->controlPoint;

                    $deltaDistanceReport = intval($report->distancem - $lastReport->distancem);
                    $deltaTimeReport = intval(\App\Http\Controllers\Utils\StrTime::toSeg($report->date->toTimeString())) - intval(\App\Http\Controllers\Utils\StrTime::toSeg($lastReport->date->toTimeString()));
                    $deltaSpeedReport = $deltaTimeReport > 0 ?($deltaDistanceReport*3600)/($deltaTimeReport*1000):0;

                    $location = $report->location;
                    $deltaDistance = intval(\App\Http\Controllers\Utils\Geolocation::getDistance($location->latitude, $location->longitude, $lastLocation->latitude, $lastLocation->longitude));
                    $deltaTime = intval(\App\Http\Controllers\Utils\StrTime::toSeg($location->date->toTimeString())) - intval(\App\Http\Controllers\Utils\StrTime::toSeg($lastLocation->date->toTimeString()));
                    $deltaSpeed = $deltaTime > 0 ?($deltaDistance*3600)/($deltaTime*1000):0;

                    $totalDistance+=$deltaDistance;
                @endphp
                <tr class="{{ $deltaSpeed > 120 ? 'danger':'' }}">
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $time }}</td>
                    <td>
                        <span class="{{ $controlPoint->order - $lastOrderCP > 1 ?'btn-danger text-bold tooltips':'' }}" data-title="@lang('Possible issue'): @lang('Invalid sequence')">
                            {{ $controlPoint->order }} - {{ str_limit($controlPoint->name,10) }} ({{ $controlPoint->distance_from_dispatch }} m)
                        </span>
                    </td>
                    <td>
                        <span class="{{ $deltaSpeedReport > 120 ?'btn-danger text-bold tooltips':'' }}" data-title="@lang('Possible issue'): @lang('Great distance traveled'): {{ $deltaDistanceReport }} m en {{ $deltaTimeReport }} s. @lang('Calculated speed'): {{ $deltaSpeedReport }} km/h">
                            {{ $report->distancem }}
                        </span>
                    </td>
                    <td>
                        <span class="{{ abs(intval($report->status_in_minutes)) > 15 ?'text-warning text-bold tooltips':'' }}" data-title="@lang('Possible issue')">
                            {{ $report->timed }}
                        </span>
                    </td>
                    <td>{{ $location->odometer }}</td>
                    <td>{{ $location->distance }}</td>
                    <td>
                        <span class="{{ $location->speeding?'text-warning text-bold tooltips':'' }}" data-title="@lang('With speeding')">
                            {{ number_format($location->speed, 1, '.', '') }}
                        </span>
                    </td>
                    <td>{{ $deltaDistance }}</td>
                    <td>{{ $deltaTime }} s</td>
                    <td>{{ number_format($deltaSpeed, 1, '.', '') }}</td>
                    <td>{{ $totalDistance }}</td>
                </tr>
                @php
                    $lastReport = $report;
                    $lastLocation = $location;
                    $lastOrderCP = $controlPoint->order;
                @endphp
            @endforeach
        </table>
    </div>
</div>

@include('layouts.template.plugins')

<!-- ================== BEGIN PAGE LEVEL JS ================== -->
<script src="{{asset('assets/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js')}}"></script>
<script src="{{asset('assets/plugins/DataTables/media/js/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/plugins/DataTables/media/js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('assets/plugins/DataTables/extensions/FixedHeader/js/dataTables.fixedHeader.min.js')}}"></script>
<script src="{{asset('assets/plugins/DataTables/extensions/Responsive/js/dataTables.responsive.min.js')}}"></script>
{{--<script src="{{asset('assets/js/page-table-manage-fixed-header.demo.min.js'}}"></script>--}}
<!-- ================== END PAGE LEVEL JS ================== -->

<script type="application/javascript">
    $(document).ready(function () {
        $('#data-table').show().DataTable({
            lengthMenu: [1000, 2000],
            fixedHeader: {
                header: true,
                headerOffset: 0
            },
            responsive: false,

        });
        $('#data-table').find('th, td').addClass('text-center');
    })
</script>

@else
    <div class="m-t-40">
        @include('partials.alerts.noRegistersFound')
    </div>
@endif
