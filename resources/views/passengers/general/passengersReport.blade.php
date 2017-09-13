@if(count($reports))
    <div class="panel panel-inverse">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <h5 class="text-white m-t-10">
                <span class="hide">
                <i class="fa fa-user-circle" aria-hidden="true"></i>
                Title <i class="fa fa-angle-double-right" aria-hidden="true"></i> Subtitle
                <hr class="text-inverse-light">
                </span>

                <ul class="nav nav-pills nav-pills-success">
                    <li class="active">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor,.recorder').show()">
                            <i class="fa fa-asterisk" aria-hidden="true"></i> @lang('All')
                        </a>
                    </li>
                    <li class="">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.sensor').show();$('.recorder').hide()">
                            <i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')
                        </a>
                    </li>
                    <li class="">
                        <a href="#all-report-tab" data-toggle="tab" aria-expanded="true" onclick="$('.recorder').show();$('.sensor').hide()">
                            <i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')
                        </a>
                    </li>
                </ul>
            </h5>
        </div>
        <div class="tab-content p-0">
            <div id="all-report-tab" class="table-responsive tab-pane active fade in">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th class="text-center">N°</th>
                        <th class="text-center"><i class="fa fa-car" aria-hidden="true"></i> @lang('Vehicle')</th>
                        <th class="text-center sensor"><i class="fa fa-microchip" aria-hidden="true"></i> @lang('Sensor')</th>
                        <th class="text-center recorder"><i class="fa fa-compass" aria-hidden="true"></i> @lang('Recorder')</th>
                        <th class="text-center sensor recorder"><i class="fa fa-minus" aria-hidden="true"></i> @lang('Difference')</th>
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
                        <tr class="text-center">
                            <td >{{ $loop->index + 1 }}</td>
                            <td>{{ $report->number }} <i class="fa fa-hand-o-right" aria-hidden="true"></i> {{  $report->plate }}</td>
                            <td class="sensor">{{ $sensor }}</td>
                            <td class="recorder"><span {{ $invalidRecorder ? 'class=text-warning data-toggle=tooltip data-html=true':"" }} title="{{ $invalidRecorder ? 'Verificar posible error en los datos de registradora':'' }}">{{ $recorder }}</span></td>
                            <td class="sensor recorder">{{ abs($sensor - $recorder) }}</td>
                        </tr>
                    @endforeach
                    <tr class="inverse bg-inverse-light text-white">
                        <td colspan="2" class="text-right">@lang('Total passengers')</td>
                        <td class="text-center sensor">{{ $totalSensor->sum() }}</td>
                        <td class="text-center recorder">{{ $totalRecorder->sum() }}</td>
                        <td rowspan="2" class="sensor recorder"></td>
                    </tr>
                    <tr class="inverse bg-inverse text-white">
                        <td colspan="2" class="text-right">@lang('Average per vehicle')</td>
                        <td class="text-center sensor">{{ number_format($totalSensor->average(),1) }}</td>
                        <td class="text-center recorder">{{ number_format($totalRecorder->average(),1) }}</td>
                    </tr>
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups!')</strong></h4>
            <hr class="hr">
            @lang('No registers found')
        </div>
    </div>
@endif