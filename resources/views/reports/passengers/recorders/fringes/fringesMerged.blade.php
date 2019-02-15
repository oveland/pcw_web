@if( $dispatchRegistersByVehicles->isNotEmpty() )
<script>
    var subTitles = {};
    var footerInfo = {};
    var fringesInfo = {};
</script>
<div class="row">
    @php
        $strTime = new \App\Http\Controllers\Utils\StrTime();
        $issuesByVehicles = collect([]);

        /* Vars for chart */
        $timesLabels = collect([]);                        // X Axis
        $fringesDataValues = collect([]);                  // Y Axis - Dataset 1
        $fringeTimes = collect([]);
        $fringesBackgrounds = collect([]);

        $mergedDataValues = collect([]);
        $totalPassengersByRoute = 0;
        $totalPassengersByFringes = collect([]);
    @endphp
    @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle)
        @php
            $reportByVehicle = \App\Traits\CounterByRecorder::reportByVehicle($vehicleId,$dispatchRegistersByVehicle);
            $report = $reportByVehicle->report;
            $fringesVehicle = collect($fringes);

            $vehicle = $report->vehicle;
        @endphp
        @if( !$report->issue )
            @php
                $histories = $report->history;

                /*
                    Process vars for recorder counter report
                */
                $recorderCounterHistories = $histories->where('dispatchRegisterIsComplete',true);
                $roundTrips = collect([]);
                $routeTimes = collect([]);
                $startRecorderCounter = false;
                $endRecorderCounter = false;

                /*
                    Process vars for route fringes
                */
                $fringeTimes = collect([]);
                $fringesBackgrounds = collect([]);
                $fringesBackground = "white";
                $startFringes = false;

                /*
                    Loop from 4 am to 10 pm (18 hours) with steps of one minute
                */
                $timesLabels = collect([]);                        // X Axis
                $recorderCounterHistoriesDataValues = collect([]); // Y Axis - Dataset 0
                $fringesDataValues = collect([]);                  // Y Axis - Dataset 1
                $timeChart = '04:00:00';
                foreach (range(1,(18*30)) as $minute){
                    $timesLabels->push( $strTime::toShortString($timeChart) );

                    /* Process for recorder counter report */
                    $recorderCounterHistory = $recorderCounterHistories->first();
                    $totalByStep = 0;
                    if( $recorderCounterHistory ){
                        $currentRecorderCounterHistory = $recorderCounterHistory;
                        if( $strTime::timeAGreaterThanTimeB($timeChart,$recorderCounterHistory->departureTime) )$startRecorderCounter = true;
                        if( $strTime::timeAGreaterThanTimeB($timeChart,$recorderCounterHistory->arrivalTime) ){
                            $recorderCounterHistories->shift();
                            $recorderCounterHistory = $recorderCounterHistories->first();
                            $startRecorderCounter = false;
                            $endRecorderCounter = true;
                        }
                        $totalByStep = $endRecorderCounter ? $currentRecorderCounterHistory->passengersByRoundTrip : 0;
                        $recorderCounterHistoriesDataValues->push( $totalByStep);

                        if( $startRecorderCounter ){
                            $roundTrips->push( "⇄ $recorderCounterHistory->roundTrip ($recorderCounterHistory->routeName)" );
                            $routeTimes->push( "⚐ ".__('Departure')." ".$strTime::toShortString($recorderCounterHistory->departureTime)." ".__('Arrived')." ".$strTime::toShortString($recorderCounterHistory->arrivalTime) );
                        }else if ($endRecorderCounter){
                            $totalPassengersByRoute += $currentRecorderCounterHistory->passengersByRoundTrip;
                            $roundTrips->push( "⇄ $currentRecorderCounterHistory->roundTrip ($currentRecorderCounterHistory->routeName)" );
                            $routeTimes->push( "⚐ ".__('Departure')." ".$strTime::toShortString($currentRecorderCounterHistory->departureTime)." ".__('Arrived')." ".$strTime::toShortString($currentRecorderCounterHistory->arrivalTime) );
                        }
                        else{
                            $roundTrips->push( __("In dispatch") );
                            $routeTimes->push( "" );
                        }
                        $endRecorderCounter = false;
                    }

                    /* Process for route fringes */
                    $fringe = $fringesVehicle->first();
                    $fringeTimeFrom = $fringe->from ?? $fringeTimeFrom;
                    $fringeTimeTo = $fringe->to ?? $fringeTimeTo;

                    if( $strTime::timeAGreaterThanTimeB($timeChart,$fringeTimeFrom) )$startFringes = true;
                    if( $strTime::timeAGreaterThanTimeB($timeChart,$fringeTimeTo) ){
                        $fringesVehicle->shift();
                        $fringe = $fringesVehicle->first();
                    }
                    $fringesDataValues->push( $fringe && $startFringes ? 150 : '' );
                    $fringeTimes->push( $fringe && $startFringes ? $strTime::toShortString($fringeTimeFrom)." ".__('to')." ".$strTime::toShortString($fringeTimeTo) : '' );
                    $fringesBackgrounds->push( $fringe->style_color ?? '' );

                    $keyFringeTime = $fringeTimes->last();
                    if( $keyFringeTime != '' ){
                        $currentPassengersByFringes = $totalPassengersByFringes[$keyFringeTime] ?? 0;
                        $totalPassengersByFringes[$keyFringeTime] = $currentPassengersByFringes + $totalByStep;
                    }

                    $timeChart = $strTime::addStrTime( $timeChart,"00:02:00" ); // Increase values to X axe
                }

                $mergedDataValues->push((object)[
                    'vehicle' => $vehicle,
                    'data' => $recorderCounterHistoriesDataValues
                ]);
            @endphp
            <script>
                subTitles['{{ $vehicle->number }}'] = JSON.parse('{!! $roundTrips->toJson() !!}');
                footerInfo['{{ $vehicle->number }}'] = JSON.parse('{!! $routeTimes->toJson() !!}');
                fringesInfo['@lang('fringes')'] = JSON.parse('{!! $fringeTimes->toJson() !!}');
            </script>
        @else
            @php
                if( !$issuesByVehicles->get($vehicleId) ){
                    $issuesByVehicles->put($vehicleId,$reportByVehicle->issues);
                }else{
                    $issuesByVehicles->put($vehicleId,$issuesByVehicles->merge($reportByVehicle->issues));
                }
            @endphp
        @endif
    @endforeach
    <style>
        .table-passengers-by-fringes *{

        }
    </style>
    <div class="col-md-2 table-passengers-by-fringes">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h6 class="text-white m-0">
                    <span class="hides">
                        <i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers by fringes')
                    </span>
                </h6>
            </div>
            <div class="table-responsive">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle table-condensed">
                    <thead>
                    <tr class="inverse">
                        <th width="60%" class="text-center p-4 f-s-10"><i class="fa fa-hourglass" aria-hidden="true"></i> @lang('Fringe')</th>
                        <th width="40%" class="text-center p-4 f-s-10"><i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers')</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($totalPassengersByFringes as $fringe => $passengersByFringes)
                            <tr>
                                <td width="60%" class="f-s-10 p-2 text-center"><strong>{{ $fringe }}</strong></td>
                                <td width="40%" class="f-s-10 p-2 text-center">{{ $passengersByFringes }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <!-- end table -->
            </div>
        </div>
    </div>


    <div class="col-md-10">
        <div class="bg-white">
            <h5 class="text-center m-0 p-t-10">{{ count($dispatchRegistersByVehicles) }} @lang('Vehicles')<br> @lang('Total') @lang('passengers'): {{ $totalPassengersByRoute }}</h5>
            <canvas id="chart-merged" class="canvas-charts" width="500" height="120"></canvas>
        </div>
    </div>
    <script>
        var ctx = $("#chart-merged");
        var data = [];

        @forEach($mergedDataValues as $mergedDataValue)
            @php( $vehicle = $mergedDataValue->vehicle )
            data.push({
                type: 'line',
                label: '{{ $vehicle->number }}',
                steppedLine: 'after',
                borderColor: '#1f383f',
                backgroundColor: '#{{ substr(md5(rand()), 0, 6) }}',
                borderWidth: 1,
                pointBorderWidth: 1,
                pointRadius: 3,
                fill: true,
                data: JSON.parse('{!! $mergedDataValue->data->toJson() !!}')
            });
        @endforeach

        data.push({
            type: 'bar',
            label: '@lang('Fringes')',
            steppedLine: 'after',
            data: JSON.parse('{!! $fringesDataValues->toJson() !!}'),
            fill: false,
            backgroundColor: JSON.parse('{!! $fringesBackgrounds->toJson() !!}'),
            borderColor: JSON.parse('{!! $fringesBackgrounds->toJson() !!}'),
            borderWidth: 1
        });

        var totalLayers = data.length - 1;

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: JSON.parse('{!! $timesLabels->toJson() !!}'),
                datasets: data
            },
            options: {
                tooltips: {
                    intersect: true,
                    callbacks: {
                        label: function(tooltipItem, data) {
                            var vehicle = data.datasets[tooltipItem.datasetIndex].label || '';
                            if( tooltipItem.datasetIndex < totalLayers ){
                                return "@lang('Passengers') @lang('vehicle') "+vehicle+": "+tooltipItem.yLabel;
                            }
                            return "⌛ @lang('Fringe'): "+fringesInfo['@lang('fringes')'][tooltipItem.index];
                        },
                        title: function(tooltipItem, data){
                            return "@lang('Time') "+tooltipItem[0].xLabel;
                        },
                        afterTitle: function(tooltipItem, data){
                            var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                            if( tooltipItem[0].datasetIndex < totalLayers ) {
                                return subTitles[vehicle][tooltipItem[0].index];
                            }
                        },
                        footer: function(tooltipItem, data){
                            var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                            if( tooltipItem[0].datasetIndex < totalLayers ) {
                                return footerInfo[vehicle][tooltipItem[0].index];
                            }
                        },
                        afterFooter: function(tooltipItem, data){
                            var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                            if( tooltipItem[0].datasetIndex < totalLayers ) {
                                return "⌛ @lang('Fringe'): "+fringesInfo['@lang('fringes')'][tooltipItem[0].index];
                            }
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        scaleLabel: {
                            display: true,
                            labelString: '@lang('Passengers')'
                        }
                    }]
                },
                responsive: true,
                legend: {
                    position: 'bottom',
                    display: true
                },
                title: {
                    display: false
                }
            }
        });
    </script>

    @if(count($issuesByVehicles))
    <div class="col-md-12" style="position: absolute;top:-25px">
        <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0px">
            <i class="fa fa-exclamation-circle"></i>
            <strong>@lang('Warning'):</strong>
            @lang('There are issues in data recorder'). <a data-toggle="collapse" data-target="#issues" class="text-bold text-warning click">@lang('See details')</a>
        </div>
        <div id="issues" class="panel-collapse collapse bg-white p-b-40" aria-expanded="false" style="height: 0px;">
            <div class="panel-body p-0">
                @include('partials.alerts.reports.passengers.issuesByVehicles',compact('issuesByVehicles'))
            </div>
        </div>
    </div>
    @endif
</div>
<script>hideSideBar()</script>
@else
    @include('partials.alerts.noRegistersFound')
@endif