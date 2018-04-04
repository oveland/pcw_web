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
                    if( $recorderCounterHistory ){
                        if( $strTime::timeAGreaterThanTimeB($timeChart,$recorderCounterHistory->departureTime) )$startRecorderCounter = true;
                        if( $strTime::timeAGreaterThanTimeB($timeChart,$recorderCounterHistory->arrivalTime) ){
                            $recorderCounterHistories->shift();
                            $recorderCounterHistory = $recorderCounterHistories->first();
                            $startRecorderCounter = false;
                        }
                        if( $recorderCounterHistory ){
                            $recorderCounterHistoriesDataValues->push( $startRecorderCounter ? $recorderCounterHistory->passengersByRoundTrip : 0);
                            $roundTrips->push( $startRecorderCounter ? "⇄ $recorderCounterHistory->roundTrip ($recorderCounterHistory->route)": '' );
                            $routeTimes->push( $startRecorderCounter ? __('Between')." ".$strTime::toShortString($recorderCounterHistory->departureTime)." ".__('and')." ".$strTime::toShortString($recorderCounterHistory->arrivalTime) : '' );
                        }
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

                    $timeChart = $strTime::addStrTime( $timeChart,"00:02:00" );
                }

            @endphp

            <div class="col-md-12 p-10">
                <div class="bg-white p-10">
                    <h5 class="text-center">@lang('Vehicle') {{ $vehicle->number }}<br> @lang('Total') @lang('passengers'): {{ $report->passengers }}</h5>
                    <canvas id="chart-{{ $vehicleId }}" class="canvas-charts" width="500" height="100"></canvas>
                </div>
            </div>
            <script>
                var ctx = $("#chart-{{ $vehicleId }}");

                subTitles['{{ $vehicle->number }}'] = JSON.parse('{!! $roundTrips->toJson() !!}');
                footerInfo['{{ $vehicle->number }}'] = JSON.parse('{!! $routeTimes->toJson() !!}');
                fringesInfo['{{ $vehicle->number }}'] = JSON.parse('{!! $fringeTimes->toJson() !!}');

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: JSON.parse('{!! $timesLabels->toJson() !!}'),
                        datasets: [
                            {
                                type: 'line',
                                label: '{{ $vehicle->number }}',
                                steppedLine: 'after',
                                borderColor: '#1f383f',
                                backgroundColor: '#26778f',
                                borderWidth: 1,
                                pointBorderWidth: 1,
                                pointRadius: 1,
                                fill: true,
                                data: JSON.parse('{!! $recorderCounterHistoriesDataValues->toJson() !!}')
                            },
                            {
                                type: 'bar',
                                label: '{{ $vehicle->number }}',
                                steppedLine: 'after',
                                data: JSON.parse('{!! $fringesDataValues->toJson() !!}'),
                                fill: false,
                                backgroundColor: JSON.parse('{!! $fringesBackgrounds->toJson() !!}'),
                                borderColor: JSON.parse('{!! $fringesBackgrounds->toJson() !!}'),
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        tooltips: {
                            mode: 'index',
                            intersect: true,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var vehicle = data.datasets[tooltipItem.datasetIndex].label || '';
                                    if( tooltipItem.datasetIndex == 0 ){
                                        return "@lang('Passengers') ("+vehicle+"): "+tooltipItem.yLabel;
                                    }
                                    return "@lang('Fringe'): "+fringesInfo[vehicle][tooltipItem.index];;
                                },
                                title: function(tooltipItem, data){
                                    return "@lang('Time') "+tooltipItem[0].xLabel;
                                },
                                afterTitle: function(tooltipItem, data){
                                    var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                                    if( tooltipItem[0].datasetIndex == 0 ) {
                                        return subTitles[vehicle][tooltipItem[0].index];
                                    }
                                },
                                footer: function(tooltipItem, data){
                                    var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                                    if( tooltipItem[0].datasetIndex == 0 ) {
                                        return footerInfo[vehicle][tooltipItem[0].index];
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
                            display: false
                        },
                        title: {
                            display: false
                        }
                    }
                });
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
<script> console.log(fringesInfo)</script>
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