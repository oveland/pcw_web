@php
    $reports = $passengerReport->reports;
@endphp
@if(count($reports))
    <style>
        table.pcw_excel {
            text-align: center;
            font-family: 'Segoe UI Light', sans-serif;
            width: 1000px;
        }

        table.pcw_excel td, table.pcw_excel th {
            padding: 5px 10px;
        }

        table.pcw_excel tbody td {
            color: #1A1A1A;
        }

        table.pcw_excel thead th {
            text-align: center !important;
            background: #183744;
        }

        table.pcw_excel thead th {
            font-weight: bold;
            color: #FFFFFF;
            text-align: center;
        }

        table.pcw_excel .footer td {
            font-weight: bold;
            color: #FFFFFF;
            background: #132d38;
            text-align: right;
        }

        table.pcw_excel .title th {
            background-color: #0c2d33 !important;
            text-align: center !important;
        }
    </style>
    <!-- begin table -->
    @php
        $th = (object)[
            'height' => 25,
            'align' => 'center',
            'valign' => 'middle'
        ];

        $tf = (object)[
            'height' => 20,
            'align' => 'right',
            'valign' => 'middle'
        ];
    @endphp
    <table class="pcw_excel">
        <thead>
        <tr class="title">
            <th height="35" valign="{{ $th->valign }}" align="{{ $th->align }}" colspan="4">
                @lang('Passengers report'): @lang('Consolidated per day')
            </th>
            <th height="35" valign="{{ $th->valign }}" align="{{ $th->align }}" colspan="2">
                @lang('Date'): {{ $passengerReport->date }}
            </th>
        </tr>
        <tr>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="1">N°</th>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="15">@lang('Vehicle')</th>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="15">@lang('Plate')</th>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="12">@lang('Sensor')</th>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="20">@lang('Recorder')</th>
            <th height="{{ $th->height }}" valign="{{ $th->valign }}" align="{{ $th->align }}" width="20">@lang('Difference')</th>
        </tr>
        </thead>
        <tbody>
        @php
            $totalSensor = collect([]);
            $totalRecorder = collect([]);
        @endphp
        @foreach($reports as $report)
            @php
                $sensor = $report->passengers->sensor;
                $recorder = $report->passengers->recorder;
                $sensor > 0 ? $totalSensor->push($sensor):null;
                $recorder > 0 ? $totalRecorder->push($recorder):null;
                $invalidRecorder = $recorder > 1000 || $recorder < 0;
            @endphp
            <tr>
                <td>{{ $loop->index + 1 }}</td>
                <td>{{ $report->number }}</td>
                <td>{{ $report->plate }}</td>
                <td>{{ $sensor }}</td>
                <td>{{ $recorder }}</td>
                <td>{{ abs($sensor - $recorder) }}</td>
            </tr>
        @endforeach
        <tr class="footer">
            <td height="{{ $tf->height }}" align="{{ $th->align }}" valign="{{ $tf->valign }}" width="20" colspan="3">@lang('Total passengers')</td>
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}" width="20">{{ $totalSensor->sum() }}</td>
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}"class=""  width="20">{{ $totalRecorder->sum() }}</td>
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}" width="20" rowspan="2"></td>
        </tr>
        <tr class="footer">
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}" width="20" colspan="3">@lang('Average per vehicle')</td>
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}" width="20">{{ number_format($totalSensor->average(),1) }}</td>
            <td height="{{ $tf->height }}" align="{{ $tf->align }}" valign="{{ $tf->valign }}" width="20">{{ number_format($totalRecorder->average(),1) }}</td>
        </tr>
        </tbody>
    </table>
    <!-- end table -->
@endif