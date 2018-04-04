@if( $dispatchRegistersByVehicles->isNotEmpty() )
    <script>
        var footerInfo = {};
        var roundTrips = {};
    </script>
    <div id="canvas-holder" class="col-md-8 col-md-offset-2 bg-white p-b-10">
        <h4 class="text-center"><i class="fa fa-archive"></i> @lang('Consolidated daily')</h4>
        <hr class="hr">
        <canvas id="chart-area-total-passengers"></canvas>
    </div>
    <div class="col-md-12 bg-inverse-dark m-t-5">
        <h4 class="text-center text-white" data-toggle="collapse" data-target="#report-char-details"><i class="fa fa-list-alt"></i> @lang('Details')</h4>
    </div>
    <div id="report-char-details" class="row collapse fade in">
        @php
            $strTime = new \App\Http\Controllers\Utils\StrTime();
            $issuesByVehicles = collect([]);
            $totalPassengers = collect([]);
        @endphp
        @foreach($dispatchRegistersByVehicles as $vehicleId => $dispatchRegistersByVehicle)
            @php
                $reportByVehicle = \App\Traits\CounterByRecorder::reportByVehicle($vehicleId,$dispatchRegistersByVehicle);
                $report = $reportByVehicle->report;
                $vehicle = $report->vehicle;
            @endphp
            @if( !$report->issue )
                @php
                    $totalPassengers->put(__('Vehicle')." $vehicle->number",
                    [
                        'passengers' => $report->passengers,
                        'colors' => "#".substr(md5(rand()), 0, 6)
                    ]);
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
                            $routeTimes->push( __('Between')." ".$strTime::toShortString($history->departureTime)." ".__('and')." ".$strTime::toShortString($history->arrivalTime) );

                            $departureTimes->push( $strTime::toShortString($history->departureTime) );
                            $arrivalTimes->push( $strTime::toShortString($history->arrivalTime) );
                        }
                    }

                    $roundTrips->prepend($roundTrips->first());
                    $passengersByRoundTrips->prepend(0);
                    $routeTimes->prepend($departureTimes->first());

                    $arrivalTimes->prepend($departureTimes->first());
                    $departureTimes->prepend('...');
                @endphp

                <div class="col-md-6 p-10">
                    <div class="bg-white p-10">
                        <h5 class="text-center">@lang('Vehicle') {{ $vehicle->number }}<br> @lang('Total') @lang('passengers'): {{ $report->passengers }}</h5>
                        <canvas id="chart-{{ $vehicleId }}" class="canvas-charts" width="500" height="150"></canvas>
                    </div>
                </div>
                <script>
                    var ctx = $("#chart-{{ $vehicleId }}");

                    footerInfo['{{ $vehicle->number }}'] = JSON.parse('{!! $routeTimes->toJson() !!}');
                    roundTrips['{{ $vehicle->number }}'] = JSON.parse('{!! $roundTrips->toJson() !!}');

                    var configchart = {
                        type: 'line',
                        data: {
                            labels: JSON.parse('{!! $roundTrips->toJson() !!}'),
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
                                        return "@lang('Round Trip') "+tooltipItem[0].xLabel;
                                    },
                                    footer: function(tooltipItem, data){
                                        var vehicle = data.datasets[tooltipItem[0].datasetIndex].label || '';
                                        return footerInfo[vehicle][tooltipItem[0].index];
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
                    };
                    var myChart = new Chart(ctx, configchart);
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

        <script>
            var chartColors = window.chartColors;
            var color = Chart.helpers.color;
            var config = {
                data: {
                    datasets: [{
                        data: JSON.parse('{!! $totalPassengers->values()->pluck('passengers')->toJson() !!}'),
                        backgroundColor: JSON.parse('{!! $totalPassengers->values()->pluck('colors')->toJson() !!}'),
                        label: '@lang('Consolidated graph')'
                    }],
                    labels: JSON.parse('{!! $totalPassengers->keys()->toJson() !!}')
                },
                options: {
                    responsive: true,
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: false,
                        text: '@lang('Consolidated daily')'
                    },
                    scale: {
                        ticks: {
                            beginAtZero: true
                        },
                        reverse: false
                    },
                    animation: {
                        animateRotate: false,
                        animateScale: true
                    }
                }
            };
            Chart.PolarArea($('#chart-area-total-passengers'), config);
        </script>

        @if(count($issuesByVehicles))
            <div class="col-md-12" style="position: absolute;top:-25px">
                <div class="alert alert-warning alert-bordered fade in m-b-0" style="border-radius: 0">
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