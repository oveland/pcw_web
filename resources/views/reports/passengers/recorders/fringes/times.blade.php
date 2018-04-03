@if( $dispatchRegistersByVehicles->isNotEmpty() )
<script>
    var footerInfo = {};
    var roundTrips = {};
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
        @endphp
        @if( !$report->issue )
            @php
                $vehicle = $report->vehicle;

                $dispatchRegisters =

                $roundTrips = collect([]);
                $passengersByRoundTrips = collect([]);
                $routeTimes = collect([]);

                $departureTimes = collect([]);
                $arrivalTimes = collect([]);

                $dispatchTimes = collect([]);

                foreach ($report->history as $history){
                    if( $history->dispatchRegisterIsComplete ){
                        $roundTrips->push( "⇄ $history->roundTrip ($history->route)" );
                        $passengersByRoundTrips->push( $history->passengersByRoundTrip );
                        $routeTimes->push( $strTime::toShortString($history->departureTime)." - ".$strTime::toShortString($history->arrivalTime) );

                        $departureTimes->push( $strTime::toShortString($history->departureTime) );
                        $arrivalTimes->push( $strTime::toShortString($history->arrivalTime) );

                        $dispatchTimes->push( $departureTimes->last() );
                        $dispatchTimes->push( $arrivalTimes->last() );
                        $passengersByRoundTrips->push(0);
                        $routeTimes->push( $strTime::toShortString($history->arrivalTime) );
                        $roundTrips->push( "Dispatched" );
                    }
                }

                $roundTrips->prepend($roundTrips->first());
                $passengersByRoundTrips->prepend(0);
                $routeTimes->prepend($departureTimes->first());

                $arrivalTimes->prepend($departureTimes->first());
                $departureTimes->prepend('...');
            @endphp

            <div class="col-md-12 p-10">
                <div class="bg-white p-10">
                    <h5 class="text-center">@lang('Vehicle') {{ $vehicle->number }}<br> @lang('Total') @lang('passengers'): {{ $report->passengers }}</h5>
                    <canvas id="chart-{{ $vehicleId }}" class="canvas-charts" width="500" height="100"></canvas>
                </div>
            </div>
            <script>
                var ctx = $("#chart-{{ $vehicleId }}");

                footerInfo['{{ $vehicle->number }}'] = JSON.parse('{!! $routeTimes->toJson() !!}');
                roundTrips['{{ $vehicle->number }}'] = JSON.parse('{!! $roundTrips->toJson() !!}');

                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: JSON.parse('{!! $dispatchTimes->toJson() !!}'),
                        display: false,
                        datasets: [{
                            steppedLine: 'after',
                            label: '{{ $vehicle->number }}',
                            data: JSON.parse('{!! $passengersByRoundTrips->toJson() !!}'),
                            backgroundColor: '#7e8e9c',
                            borderColor: '#134657',
                            hoverRadius: 10,
                            borderWidth: 3
                        }]
                    },
                    options: {
                        tooltips: {
                            intersect: false,
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    var vehicle = data.datasets[tooltipItem.datasetIndex].label || '';
                                    return "@lang('Passengers') ("+vehicle+"): "+tooltipItem.yLabel;
                                },
                                title: function(tooltipItem, data){
                                    return "@lang('Time') "+tooltipItem[0].xLabel;
                                },
                                footer: function(tooltipItem, data){
                                    var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                                    return footerInfo[vehicle][tooltipItem[0].index];
                                },
                                afterTitle: function(tooltipItem, data){
                                    var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                                    return roundTrips[vehicle][tooltipItem[0].index];
                                }
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {
                                    beginAtZero:true
                                },
                                scaleLabel: {
                                    display: true,
                                    labelString: '@lang('Passengers')'
                                }
                            }]
                        },
                        responsive: true,
                        legend: {
                            position: 'bottom'
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