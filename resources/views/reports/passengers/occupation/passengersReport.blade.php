@if(count($report->historySeats))
    @php
        $historySeats = $report->historySeats;
        $dispatchRegister = $report->dispatchRegister;
        $dispatchArrivalTime = $report->dispatchArrivalTime;
        $thresholdKm = $report->thresholdKm;

        $controlPoints = $report->controlPoints;
        $cpCounts = $report->cpCounts;

        $totalProduction = $report->totalProduction;

        $totalProductionStr = number_format($totalProduction, 0, ',', '.');

        $historyBySeats = $historySeats->groupBy('seat');

        $routeDistance = $dispatchRegister->route->distance * 1000;

        $reference_location = $dispatchRegister->locations->first();

        $correction = 0.08;

        function percentTo($km, $routeDistance) {
            return $routeDistance ? $km * 100 / $routeDistance: 0;
        }

        function formatW($value) {
            global $correction;
            return number_format($value - $correction, 20, '.', ',');
        }

        $passengersStopsFICS = $report->passengersStopsFICS;

        $spreadsheetPassengers = $dispatchRegister->getObservation('spreadsheet_passengers_sync');
    @endphp
    <div class="panel-inverse col-md-12" style="padding-bottom: 60px">
        <div class="panel-heading">
            <div class="row">
                <div class="col-md-12">
                    <div style="position: absolute; right: 10px; display: flex; gap: 12px">
                        <span style="color: black; display: flex; align-items: center">
                            <input class="threshold-km input-sm form-control"
                                   type="number"
                                   placeholder="Distancia mínima en m."
                                   value="{{ $thresholdKm ?: '' }}"/>
                            <button class="btn btn-default btn-xs" style="width: 100px; height: 30px" onclick="loadSeatingProfile('{{ route('report-passengers-occupation-by-dispatch',['id'=>$dispatchRegister->id]) }}')">Actualizar</button>
                        </span>
                        <a href="javascript:;"
                           class="btn btn-xs btn-icon btn-circle btn-danger"
                           style="margin: 8px 8px !important;"
                           data-dismiss="modal"
                           aria-hidden="true"
                           title="@lang('Expand / Compress')">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>

                    <div class="text-white m-t-10" style="display: flex; align-items: center">
                        <div>
                            <div>
                                @if(auth()->user()->isSuperAdmin())
                                    {{ $dispatchRegister->id }} •
                                @endif
                                <span><i class="fa fa-flag-o" aria-hidden="true"></i> {{ $dispatchRegister->route->name }}</span>
                                <div class="text-white">
                                    <i class="fa fa-clock-o"></i> {{ $dispatchRegister->getDateTimeDeparture()->toDateTimeString() }} @lang('to') <i class="fa fa-clock-o"></i> {{ $dispatchRegister->getDateTimeEnd()->toDateTimeString() }}
                                </div>
                            </div>
                            @if(auth()->user()->isSuperAdmin())
                            <div class="">
                                {{ $report->totalAscents }} @lang('activaciones') @if($totalProductionStr)• ${{ $totalProductionStr }}@endif
                            </div>
                            @endif
                            <div>
                                <small>{{ $historyBySeats->count() }} @lang('seats')</small>
                            </div>
                            @if($passengersStopsFICS)
                                <div class="passengers-stops">
                                    <small>Conteos FICS: </small>
                                    @foreach($passengersStopsFICS as $stop => $data)
                                        <span class="passengers-stop">
                                    <span class="stop"><i class="fa fa-map-marker"></i> {{ $stop }}</span>
                                    <span>
                                        <span class="up">{{ $data->a }}⭡</span>
                                        <span class="down">{{ $data->d }}⭣</span>
                                    </span>
                                </span>
                                    @endforeach

                                    <div class="spreadsheet_passengers">
                                        <small>TOTAL {{ intval($spreadsheetPassengers->value)  }}</small>
                                        <small class="tooltips" title="# @lang('Spreadsheet')"><i class="fa fa-file"></i> {{ intval($spreadsheetPassengers->observation)  }}</small>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div style="position: absolute; right: 20px">
                            <a target="_blank" href="{!! route('report-route-historic') !!}?{{ $dispatchRegister->getHistoricReportQueryParams() }}" class="btn btn-success faa-parent animated">
                                <i class="fa fa-map faa-pulse"></i>
                                Ver Histórico y Fotos
                                <i class="fa fa-external-link"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-content p-0">
            <div id="report-tab-chart" class="tab-pane active fade in">
                @if($reference_location)
                    <div class="profile-seat-container">
                        @php
                            $historyByCP = $historySeats->groupBy('tariff.from_control_point_id');
                            $totalW = 0;
                        @endphp

                        <div class="progress progress-striped p-0 m-0 no-rounded-corner progress-lg active" style="height: 16px !important;">
                            <div class="progress-bar progress-bar-route p-0" style="width: 100%;">
                                <b style="font-size: 1rem; display: block; margin-top: -3px">
                                    @lang('Total route distance') {{ $routeDistance / 1000 }}
                                    Km
                                </b>
                            </div>
                        </div>

                        <div class="info-control-points progress p-0 m-0 no-rounded-corner progress-lg">
                            @foreach($controlPoints as $controlPoint)
                                @php
                                    $width = percentTo($controlPoint->distance_next_point, $routeDistance) - $correction * 0.4;
                                    $specialWidths = [
                                        2642 => 50000,
                                        2639 => 50000,
                                        2670 => 50000,
                                        2643 => 110000,
                                        2672 => 110000,
                                        2673 => 90000,
                                    ];

                                    if (isset($specialWidths[$controlPoint->id])) {
                                        //$width = percentTo($specialWidths[$controlPoint->id], $routeDistance) - $correction;
                                    }

                                    $cpDistance = intval($controlPoint->distance_from_dispatch / 1000);
                                    $width = $loop->last ? 0 : $width;
                                    //$trajectory = $controlPoint->name . ' ➤ '.($loop->index+1<count($controlPoints)?$controlPoints[$loop->index + 1]->name : '');
                                    $trajectory = "$controlPoint->name";

                                    $seatsByCP = $historyByCP->get($controlPoint->id);

                                    if($width + $totalW >= 100) {
//                                        $width = 100 - $totalW - 0.2;
                                    }
                                    $totalW += $width;

                                    $cpCount = $cpCounts[$controlPoint->id];

                                    $cpCountIn = $loop->first ? 0 : $cpCount->count->in;
                                    $cpCountOut = $cpCount->count->out;

                                    $cpCountFICS = $cpCount->ficsStops;
                                @endphp
                                <div class="{{ $loop->index % 2 == 0 || true ? 'bg-cp-1' : 'bg-cp-2' }} p-t-5 text-left" style="border-left: 3px solid yellow;width:{{ number_format(( $width ),20,'.','') }}%; font-size: 120%;position: relative">
                                    <div class="cp-info {{ $loop->first ? 'cp-first' : ($loop->last ? 'cp-last' : 'cp-normal')  }} text-center zoomed">
                                        <strong><i class="fa fa-map-marker"></i> {{ $trajectory }} <small class="text-muted">{{ $cpDistance }} Km</small></strong>

                                        @if($seatsByCP && $seatsByCP->count())
                                            <div style="margin-top: 2px;border-top: 1px solid #878787; font-size: 0.8rem">
                                                <i class="fa fa-users"></i> {{ $seatsByCP->count() }}
                                                •
                                                ${{ number_format($seatsByCP->sum('tariff.value'), 0, ',', '.') }}
                                            </div><br>
                                        @endif
                                        <div style="padding: 2px 0;border-top: 1px solid #878787; font-size: 0.9rem">
                                            → {{ $cpCountIn }} vs {{ $cpCountOut }} →
                                        </div>
                                        @php
                                            $ascentsInCP = $cpCount->count->ascents;
                                            $descentsInCP = $cpCount->count->descents;
                                        @endphp
                                        <div class="cp-counts">
                                            <span class="cp-up {{ $ascentsInCP ? '' : 'empty' }}">{{ $ascentsInCP }} <i class='fa fa-arrow-up {{ $ascentsInCP ? 'faa-bounce faa-fast animated' : '' }}'></i></span> @if($cpCountFICS)vs <span class="cp-up fics">{{ $cpCountFICS->a }}⭡</span> : <span class="diff">{{ intval($ascentsInCP - $cpCountFICS->a) }}</span>@endif
                                            <br>
                                            <span class="cp-down {{ $descentsInCP ? '' : 'empty' }}">{{ $descentsInCP }} <i class='fa fa-arrow-down {{ $descentsInCP ? 'faa-falling animated' : '' }}'></i></span> @if($cpCountFICS)vs <span class="cp-down fics">{{ $cpCountFICS->d }}⭣</span> : <span class="diff">{{ intval($descentsInCP - $cpCountFICS->d) }}</span>@endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @foreach($historyBySeats as $historySeats)
                             <div class="col-md-12 p-0">
                                 <div class="bg-white p-0" style="height: auto; padding: 5px;">
                                     @php
                                         $parentLoop = $loop;
                                         $width = 0;

                                         $historySeats = $historySeats->sortBy('active_time')->values();
                                         $totalW = 0;
                                     @endphp
                                    <div class="progress p-0 m-0 no-rounded-corner progress-xs"
                                         style="height: 2px !important;">
                                        @foreach($controlPoints as $controlPoint)
                                            @php
                                                $width = percentTo($controlPoint->distance_next_point, $routeDistance) - $correction * 0.4;
                                                $specialWidths = [
                                                    2642 => 50000,
                                                    2639 => 50000,
                                                    2670 => 50000,
                                                    2643 => 110000,
                                                    2672 => 110000,
                                                    2673 => 90000,
                                                ];
                                                if (isset($specialWidths[$controlPoint->id])) {
                                                    //$width = percentTo($specialWidths[$controlPoint->id], $routeDistance) - $correction;
                                                }

                                                $cpDistance = intval($controlPoint->distance_from_dispatch / 1000);
                                                $width = $loop->last ? 0 : $width;
                                                $trajectory = "$controlPoint->name";

                                                if($width + $totalW >= 100) {
//                                                    $width = 100 - $totalW;
                                                }
                                                $totalW += $width;
                                            @endphp
                                            <div class="progress-bar bg-cp-1 p-t-5 text-left"style="border-left: 3px solid yellow;width:{{ number_format(( $width ),20,'.','') }}%; font-size: 120%;position: relative"></div>
                                        @endforeach
                                    </div>

                                     <div class="progress m-0 progress-md progress-seat"
                                         style="border-top: 1px solid #3b3a3a">
                                        @foreach($historySeats as $historySeat)
                                            @php
                                                $first = $loop->iteration == 1;
                                                $last = $loop->iteration == $historySeats->count();

                                                $nextHistorySeat = $historySeats->get( $loop->index + 1 );
                                                $nextActiveKmPercent = $nextHistorySeat ? percentTo($nextHistorySeat->active_km, $routeDistance) : 100;

                                                $activeSeatRouteDistance = $historySeat->active_km;
                                                $inactiveSeatRouteDistance = $historySeat->inactive_km > 0 ? $historySeat->inactive_km : $routeDistance;

                                                $initialInactivePercent = $first ? percentTo($activeSeatRouteDistance, $routeDistance) : 0;
                                                $busyPercent = percentTo($historySeat->busy_km, $routeDistance);
                                                $inactivePercent = percentTo($inactiveSeatRouteDistance, $routeDistance);

                                                //$finalInactivePercent = $last ? (100 - $inactivePercent) : $nextActiveKmPercent - $inactivePercent;
                                                $finalInactivePercent = $last ? (100 - $inactivePercent) : percentTo($nextHistorySeat ? $nextHistorySeat->active_km - $inactiveSeatRouteDistance : 0, $routeDistance);

                                                $activeKm = intval($historySeat->busy_km / 1000);
                                                $activeTimeBy = explode('.', $historySeat->busy_time)[0];
                                                $activeTimeFrom = $historySeat->getTime('active', true);
                                                $activeTimeTo = $historySeat->getTime('inactive', true);
                                                $activeSeatRouteKm = intval($activeSeatRouteDistance / 1000);
                                                $inactiveSeatRouteKm = intval($inactiveSeatRouteDistance / 1000);

                                                $tariff = $historySeat->tariff;
                                                $tariffValue = $tariff ? $tariff->value : 0;
                                                //$fromCP = $tariff ? $tariff->fromControlPoint->name : '--';
                                                //$toCP = $tariff ? $tariff->toControlPoint->name : '--';

                                                $fromCP = $historySeat->cpFrom ? $historySeat->cpFrom->name : '--';
                                                $toCP = $historySeat->cpTo ? $historySeat->cpTo->name : '--';

                                                $startPhotoUrl = "https://beta.pcwserviciosgps.com/api/v2/files/rocket/get-photo?id=$historySeat->start_photo_id&with-effect=true&encode=png&title=true&counted=$historySeat->seat&mask=";
                                                $endPhotoUrl = "https://beta.pcwserviciosgps.com/api/v2/files/rocket/get-photo?id=$historySeat->end_photo_id&with-effect=true&encode=png&title=true&counted=$historySeat->seat&mask=";

                                                $html_tooltip = "
                                                <div class='text-left' style='width: 500px'>
                                                    <div class='' style='display:flex; gap: 4px'>
                                                        <a href='$startPhotoUrl' target='_blank'>
                                                            <small>Foto inicial</small> <i class='fa fa-external-link'></i> <br>
                                                            <img src='$startPhotoUrl' width='100%' alt='Foto inicial'/>
                                                        </a>
                                                        <a href='$endPhotoUrl' target='_blank'>
                                                            <small>Foto final</small> <i class='fa fa-external-link'></i> <br>
                                                            <img src='$endPhotoUrl' width='100%' alt='Foto final'/>
                                                        </a>
                                                    </div>
                                                    <div>
                                                        <b class='text-warning'><i class='fa fa-sitemap'></i> ".__('Seat')."</b> <span class='seat-number-tooltip'>$historySeat->seat</span>
                                                    </div>
                                                    <div>
                                                        <b class='text-warning'><i class='fa fa-money'></i> ".__('Tariff')."</b> $$tariffValue<br>
                                                        <b class='text-muted'> • ".__('From')."</b> $fromCP <b class='text-muted'>".__('to')."</b> $toCP
                                                    </div>
                                                    <div>
                                                        <b class='text-warning'><i class='fa fa-clock-o'></i> ".__('Active time')."</b> $activeTimeBy<br>
                                                        <b class='text-muted'> • ".__('From')."</b> $activeTimeFrom <b class='text-muted'>".__('to')."</b> $activeTimeTo
                                                    </div>
                                                    <div>
                                                        <b class='text-warning'><i class='fa fa-road'></i> ".__('Active by')."</b> $activeKm Km<br>
                                                        <b class='text-muted'> • ".__('From')."</b> Km $activeSeatRouteKm <b class='text-muted'>".__('to')."</b> Km $inactiveSeatRouteKm
                                                    </div>
                                                </div>
                                                ";
                                            @endphp

                                            <div class="progress-bar initial-inactive"
                                                 style="width: {{ formatW($initialInactivePercent) }}%;background: #0c0c0c !important;">
                                                <span class="seat-number pull-left label label-inverse {{ $first ? '' : 'hide' }}"
                                                      style="margin-left: 5px;margin-top: 5px; position: relative; z-index: 2;">
                                                    {{ $historySeat->seat }}
                                                </span>
                                            </div>

                                            <div class="tooltip-info progress-bar bg-{{ $historySeat->busy_km >= $thresholdKm ? 'seat-active' : 'danger' }} active--"
                                                 style="width: {{ formatW($busyPercent) - ($last ? 0.4 : 0) }}%;box-shadow: inset -10px -4px 15px -9px #000000; position: relative"
                                                 data-trigger="click"
                                                 data-html="true"
                                                 data-placement="bottom"
                                                 data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                                 title="{{ $html_tooltip }}">
                                                @if($historySeat->isAscent)
                                                <div class="seat-ascent-mark"><small><i class='fa fa-arrow-up faa-falling faa-reverse'></i></small></div>
                                                @endif
                                                <b class="label__active-km">{{ $activeSeatRouteKm }}</b>
                                                <b class="m-t-20 text-white text-sm">
                                                    <label class="label label-lime label-sm hide">{{ $historySeat->seat }}</label> {{ $activeKm }} <small>Km</small>
                                                </b>
                                                <b class="label__inactive-km">{{ $inactiveSeatRouteKm }}</b>
                                                @if($historySeat->isDescent)
                                                <div class="seat-descent-mark"><small><i class='fa fa-arrow-down faa-falling'></i></small></div>
                                                @endif
                                            </div>

                                            <div class="progress-bar final-inactive" style="width: {{ formatW($finalInactivePercent) }}%;background: #0c0c0c !important;">
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
                        <div class="col-md-2"
                             style="padding-top: 10px">
                            <i class="fa fa-3x fa-exclamation-circle"></i>
                        </div>
                        <div class="col-md-10">
                            <span class="close pull-right"
                                  data-dismiss="alert">×</span>
                            <h4>
                                <strong>@lang('Ups!')</strong>
                            </h4>
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
            z-index: 1 !important;
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

        .zoomed {
            transform: scale(1);
            transition: transform 0.2s ease;
        }

        .zoomed:hover {
            transform: scale(1.2);
            opacity: 1 !important;
            z-index: 2000 !important;
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
            z-index: 2 !important;
        }

        .cp-info {
            box-shadow: -19px 5px 16px 5px black;
            line-height: 10px;
            min-width: 130px;
        }

        .cp-first {
            font-size: 1.1rem !important;
            position: absolute;
            left: 0;
            background: #762d00;
            border: 3px solid grey;
            padding: 2px 10px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            top: 20px;
        }

        .cp-last {
            font-size: 1.1rem !important;
            position: absolute;
            right: 0;
            background: #004603;
            border: 3px solid grey;
            padding: 2px 10px;
            border-radius: 10px;
            color: white;
            font-weight: bold;
            z-index: 1000;
            top: 20px;
        }
        .bg-cp-1 {
            background: #000000 !important;
        }

        .bg-cp-2 {
            background: #ddeaea !important;
        }

        .progress-bar-route {
            background-color: #928a09 !important;
            z-index: 1 !important;
        }

        .label-lime {
            background: #528700;
        }

        .text-sm {
            font-size: 0.9rem !important;
        }

        .passengers-stops {
            padding: 8px;
            background: rgba(29, 30, 27, 0.71);
            display: flex;
            gap: 8px;
            margin-top: 12px;
            place-items: center;
        }

        .passengers-stop {
            background: #00353b;
            border: 1px solid white;
            border-radius: 2px;
            padding: 2px 4px;
            display: flex;
            gap: 8px;
            font-size: 0.9rem;
            align-items: center;
        }

        .passengers-stop .stop {

        }

        .passengers-stop .up {
            background: #00353b;
            border: 1px solid #38ff00;
            padding: 0 4px;
        }

        .passengers-stop .down {
            background: #00353b;
            border: 1px solid #ff6000;
            padding: 0 4px;
        }

        .spreadsheet_passengers {
            display: flex;
            gap: 8px;
            background: black;
            padding: 4px 8px;
            border-radius: 4px;
        }

        .cp-counts {
            padding: 8px 0 8px;
            border-top: 1px solid #878787;
            font-size: 0.9rem;
            width: 100%;
            line-height: 24px;
        }

        .cp-counts > .diff {
            background: #dbb9b94f;
            padding: 4px;
            border-radius: 2px;
        }

        .cp-counts > .empty {
            opacity: 0.2;
        }

        .cp-up {
            background: green;
            border: 1px solid green;
            padding: 4px;
            border-radius: 4px;
            position: relative;
        }

        .cp-down {
            background: orangered;
            border: 1px solid orangered;
            padding: 4px;
            border-radius: 4px;
            position: relative;
        }

        .cp-up.fics {
            background: transparent;
            border: 1px solid #38ff00;
            border-radius: 0;
        }

        .cp-down.fics {
            background: transparent;
            border: 1px solid #ff6000;
            border-radius: 0;
        }

        .seat-number {
            opacity: 0.9;
            left: -4px;
            z-index: 10000 !important;
            background: #45007e !important;
            padding: 2px 4px;
        }

        .seat-number-tooltip {
            background: #45007e !important;
            padding: 2px 4px;
        }

        .progress-seat:hover {
            border: 1px solid yellow !important;
        }

        .seat-ascent-mark {
            width: 12px;
            background: green;
            height: 100%;
            left: 0;
            top: 0;
            position: absolute;
            color: white;
            z-index: 100;
        }

        .seat-descent-mark {
            width: 12px;
            background: orangered;
            height: 100%;
            right: 0;
            top: 0;
            position: absolute;
            color: white;
            z-index: 100;
        }

        .label__active-km {
            font-size: 0.6rem;
            position: absolute;
            left: 15px;
        }
        .label__inactive-km {
            font-size: 0.6rem;
            position: absolute;
            right: 15px;
        }

        .tooltip.in {
            opacity: 1 !important;
        }

        .tooltip-inner.width-md {
            display: inline-table !important;
        }

        .tooltip-arrow {
            box-shadow: 0 0 5px 3px #fc9300b5;
            border: 6px solid black !important;
            transform: rotate(45deg) !important;
            z-index: -1;
        }

        .tooltip-info {
            cursor: pointer;
        }

        .tooltip-info:hover {
            background: #7100bc !important;
        }

        .profile-seat-container {
            height: 800px;
            overflow-y: scroll;
        }

        .info-control-points {
            display: flex;
            overflow: visible;
            height: 140px !important;
            position: sticky;
            top: 0;
            z-index: 100000;
        }
    </style>

    <script type="text/javascript">
        setTimeout(() => {
            $('.tooltip-info').tooltip({
                viewport: '#report-tab-chart'
            });
        }, 500);
    </script>
@else
    @include('partials.alerts.noRegistersFound')
@endif