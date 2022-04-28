@if(count($historySeats))
    @php
        $threshold_km = $thresholdKm ?? 0;
        $controlPoints = $dispatchRegister->route->controlPoints;


        $truncateCounts = $historySeats->where('busy_km','>=',$threshold_km);

        $totalCash = $truncateCounts->sum('tariff.value');

        $totalCashStr = number_format($totalCash, 0, ',', '.');

        $historyBySeats = $historySeats->groupBy('seat');


        $routeDistance = $dispatchRegister->route->distance * 1000;

        $reference_location = $dispatchRegister->locations->first();

        $correction = 0.08;

        function percentTo($km, $routeDistance) {
            return $km * 100 / $routeDistance;
        }

        function formatW($value) {
            return number_format($value - 0.08, 2, '.', ',');
        }
    @endphp
    <div class="panel-inverse col-md-12">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <div style="position: absolute; right: 10px">
                        <span style="color: black">
                            <input class="threshold-km" type="number" value="{{ $threshold_km }}"/>
                            <button onclick="loadSeatingProfile('{{ route('report-passengers-taxcentral-by-dispatch',['id'=>$dispatchRegister->id]) }}')">Cargar</button>
                        </span>
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" style="margin: 8px 8px !important;" data-dismiss="modal" aria-hidden="true" title="@lang('Expand / Compress')">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                    <div class="text-white m-t-10">
                        <div>
                            <i class="fa fa-flag-o" aria-hidden="true"></i>
                            {{ $dispatchRegister->route->name }}
                            <hr>
                        </div>
                        <div>
                            {{ $truncateCounts->count() }} @lang('passengers') • ${{ $totalCashStr }}
                        </div>
                        <div>
                            <small>{{ $historyBySeats->count() }} @lang('seats')</small>
                        </div>
                        <div class="">
                            <small class="text-white">
                                <i class="fa fa-clock-o"></i>
                                {{ $dispatchRegister->departure_time }} @lang('to') {{ $dispatchRegister->canceled?$dispatchRegister->time_canceled:$dispatchArrivalTime }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content p-0">
            <div id="report-tab-chart" class="tab-pane active fade in">
                @if($reference_location)
                    <div class="">
                        @php
                            $historyByCP = $historySeats->groupBy('tariff.from_control_point_id');
                            $totalW = 0;
                        @endphp

                        <div class="progress progress-striped p-0 m-0 no-rounded-corner progress-lg active">
                            <div class="progress-bar progress-bar-route p-0" style="width: 100%">
                                <b class="" style="font-size: 1.4rem">
                                    @lang('Total route distance') {{ $routeDistance / 1000 }} Km
                                </b>
                            </div>
                        </div>

                        <div class="progress p-0 m-0 no-rounded-corner progress-lg" style="height: 60px !important;">
                        @foreach($controlPoints as $controlPoint)
                            @php
                                $width = percentTo($controlPoint->distance_next_point, $routeDistance) - $correction;
                                $cpDistance = intval($controlPoint->distance_from_dispatch / 1000);
                                $width = $loop->last ? 0 : $width;
                                //$trajectory = $controlPoint->name . ' ➤ '.($loop->index+1<count($controlPoints)?$controlPoints[$loop->index + 1]->name : '');
                                $trajectory = "$controlPoint->name";

                                $seatsByCP = $historyByCP->get($controlPoint->id);

                                if($width + $totalW >= 100) {
                                    $width = 100 - $totalW - 0.2;
                                }
                                $totalW += $width;
                            @endphp
                            <div class="progress-bar {{ $loop->index % 2 == 0 || true ? 'bg-cp-1' : 'bg-cp-2' }} p-t-5 text-left" style="border-left: 3px solid red;width:{{ number_format(( $width ),'1','.','') }}%; font-size: 120%;position: relative"
                                 data-toggle="tooltip" data-html="true" data-placement="top"
                                 data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                 title="{{ '<i class="fa fa-map-signs"></i> '.$trajectory }}"
                            >
                                <div class="{{ $loop->first ? 'cp-first' : ($loop->last ? 'cp-last' : 'cp-normal')  }} text-center" style="line-height: 10px">
                                    <strong>{{ $trajectory }}</strong>
                                    <div><small class="text-muted">{{ $cpDistance }} Km</small></div>
                                    @if($seatsByCP && $seatsByCP->count())
                                    <div style="margin-top: 2px;border-top: 1px solid #878787; font-size: 0.8rem">
                                        <i class="fa fa-users"></i> {{ $seatsByCP->count() }} • ${{ number_format($seatsByCP->sum('tariff.value'), 0, ',', '.') }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                        </div>

                        @foreach($historyBySeats as $historySeats)
                            <div class="col-md-12 p-0">
                                <div class="bg-white p-0" style="height: auto;padding: 5px;">
                                    <div class="progress p-0 m-0 no-rounded-corner progress-xs" style="height: 8px !important;">
                                        @php
                                            $parentLoop = $loop;
                                            $width = 0;

                                            $historySeats = $historySeats->sortBy('active_time')->values();
                                            $totalW = 0;
                                            @endphp
                                            @foreach($controlPoints as $controlPoint)
                                                @php
                                                    $width = percentTo($controlPoint->distance_next_point, $routeDistance) - $correction;
                                                    $cpDistance = intval($controlPoint->distance_from_dispatch / 1000);
                                                    $width = $loop->last ? 0 : $width;
                                                    $trajectory = "$controlPoint->name";

                                                    if($width + $totalW >= 100) {
                                                        $width = 100 - $totalW;
                                                    }
                                                    $totalW += $width;
                                                @endphp
                                                <div class="progress-bar bg-cp-1 p-t-5 text-left" style="border-left: 3px solid yellow;width:{{ number_format(( $width ),'1','.','') }}%; font-size: 120%;position: relative"
                                                     data-toggle="tooltip" data-html="true" data-placement="top"
                                                     data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                                 title="{{ '<i class="fa fa-map-signs"></i> '.$trajectory }}"
                                                >
                                                </div>
                                            @endforeach
                                    </div>

                                    <div class="progress m-0 progress-md" style="border-top: 1px solid #3b3a3a">
                                        @foreach($historySeats as $historySeat)
                                            @php
                                                $first = $loop->iteration == 1;
                                                $last = $loop->iteration == $historySeats->count();

                                                $nextHistorySeat = $historySeats->get( $loop->index + 1 );
                                                $nextActiveKmPercent = $nextHistorySeat ? percentTo($nextHistorySeat->active_km, $routeDistance) : 100;

                                                $activeSeatRouteDistance = $historySeat->active_km;
                                                $inactiveSeatRouteDistance = $historySeat->inactive_km;

                                                $initialInactivePercent = $first ? percentTo($activeSeatRouteDistance, $routeDistance) : 0;
                                                $busyPercent = percentTo($historySeat->busy_km, $routeDistance);
                                                $inactivePercent = percentTo($historySeat->inactive_km, $routeDistance);

                                                //$finalInactivePercent = $last ? (100 - $inactivePercent) : $nextActiveKmPercent - $inactivePercent;
                                                $finalInactivePercent = $last ? (100 - $inactivePercent) : percentTo($nextHistorySeat ? $nextHistorySeat->active_km - $historySeat->inactive_km : 0, $routeDistance);

                                                $activeKm = intval($historySeat->busy_km / 1000);
                                                $activeTimeBy = explode('.', $historySeat->busy_time)[0];
                                                $activeTimeFrom = $historySeat->getTime('active', true);
                                                $activeTimeTo = $historySeat->getTime('inactive', true);
                                                $activeSeatRouteKm = intval($activeSeatRouteDistance / 1000);
                                                $inactiveSeatRouteKm = intval($inactiveSeatRouteDistance / 1000);

                                                $tariff = $historySeat->tariff;
                                                $tariffValue = $tariff ? $tariff->value : 0;
                                                $fromCP = $tariff ? $tariff->fromControlPoint->name : '--';
                                                $toCP = $tariff ? $tariff->toControlPoint->name : '--';

                                                $html_tooltip = "
                                                <div class='text-left'>
                                                    <div>
                                                        <b class='text-warning'>".__('Seat')."</b> $historySeat->seat
                                                    </div>
                                                    <br><br>
                                                    <div>
                                                        <b class='text-warning'>".__('Tariff')."</b> $$tariffValue<br>
                                                        <b class='text-muted'>".__('From')."</b> $fromCP <b class='text-muted'>".__('to')."</b> $toCP
                                                    </div>
                                                    <br><br>
                                                    <div>
                                                        <b class='text-warning'>".__('Active time')."</b> $activeTimeBy<br>
                                                        <b class='text-muted'>".__('From')."</b> $activeTimeFrom <b class='text-muted'>".__('to')."</b> $activeTimeTo
                                                    </div>
                                                    <br><br>
                                                    <div>
                                                        <b class='text-warning'>".__('Active by')."</b> $activeKm Km<br>
                                                        <b class='text-muted'>".__('From')."</b> $activeSeatRouteKm Km <b class='text-muted'>".__('to')."</b> $inactiveSeatRouteKm Km
                                                    </div>
                                                </div>
                                                ";
                                            @endphp

                                            <div class="progress-bar initial-inactive" style="width: {{ formatW($initialInactivePercent) }}%;background: #0c0c0c !important;">
                                                <span class="pull-left label label-inverse {{ $first ? '' : 'hide' }}" style="margin-left: 5px;margin-top: 5px">
                                                    @lang('Seat') {{ $historySeat->seat }} ({{ $historySeats->count() }})
                                                </span>
                                            </div>

                                            <div class="progress-bar bg-{{ $historySeat->busy_km >= $threshold_km ? 'seat-active' : 'danger' }} active--" style="width: {{ formatW($busyPercent) }}%;box-shadow: inset -10px -4px 15px -9px #000000;"
                                                 data-toggle="tooltip" data-html="true" data-placement="bottom"
                                                 data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                                 title="{{ $html_tooltip }}">
                                                <b class="m-l-10 pull-left text-sm">{{ $activeSeatRouteKm }} Km</b>
                                                <b class="m-t-20 text-white text-sm"><label class="label label-lime label-sm hide">{{ $historySeat->seat }}</label> {{ $activeKm }} <small>Km</small> • <label class="label label-lime label-lg">${{ number_format($tariffValue, 0, ',', '.') }}</label></b>
                                                <b class="m-r-10 pull-right text-sm">{{ $inactiveSeatRouteKm }} Km</b>
                                            </div>

                                            <div class="progress-bar final-inactive" style="width: {{ formatW($finalInactivePercent) }}%;background: #0c0c0c !important;">
                                                <small class="hide">{{  $historySeat->inactive_km .' • '. ($nextHistorySeat ? $nextHistorySeat->active_km : '---') }}</small>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <hr>
                    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
                        <div class="col-md-2" style="padding-top: 10px">
                            <i class="fa fa-3x fa-exclamation-circle"></i>
                        </div>
                        <div class="col-md-10">
                            <span class="close pull-right" data-dismiss="alert">×</span>
                            <h4><strong>@lang('Ups!')</strong></h4>
                            <hr class="hr">
                            @lang('No registers location found')
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style type="text/css">
        .progress {
            height: 25px !important;
        }

        .progress.progress-lg .progress-bar {
            font-size: 20px !important;
        }

        .bg-seat-active {
            /*background: #8c00ff !important;*/
            background: #ef8900 !important;
        }

        .tooltip {
            height: auto;
            z-index: 100000000;
        }
        .nav.nav-pills li:not(.active) a {
            color: lightgrey;
        }
        .nav.nav-pills li.active a {
            color: white !important;
        }

        .cp-normal {
            font-size: 1rem !important;
            position: absolute;
            left: -40px;
            background: #00353b;
            border: 2px solid grey;
            padding: 2px 10px;
            border-radius: 10px;
            color: white;
        }

        .cp-first {
            font-size: 1.1rem !important;
            position: absolute;
            left: 0px;
            background: #762d00;
            border: 3px solid grey;
            padding: 2px 10px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
        }

        .cp-last {
            font-size: 1.1rem !important;
            position: absolute;
            right: 0px;
            background: #007906;
            border: 3px solid grey;
            padding: 2px 10px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
        }

        .bg-cp-1 {
            background: #000000 !important;
        }

        .bg-cp-2 {
            background: #ddeaea !important;
        }

        .progress-bar-route {
            background-color: #928a09 !important;
        }

        .label-lime {
            background: #528700;
        }

        .text-sm {
            font-size: 0.9rem !important;
        }
    </style>

    <script type="text/javascript">
        setTimeout(()=>{
            $('[data-toggle="tooltip"]').tooltip({
                container: '#report-tab-chart'
            });
        },2000);
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif