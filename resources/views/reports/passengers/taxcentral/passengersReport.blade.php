@if(count($historySeats))
    @php
        $threshold_km = 1000;
    @endphp
    <div class="panel panel-inverse col-md-12">
        <div class="panel-heading">
            <div class="panel-heading-btn" style="display: absolute; right: 10px">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" style="margin-top: 8px !important;" data-dismiss="modal" aria-hidden="true" title="@lang('Expand / Compress')">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h5 class="text-white m-t-10">
                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                        {{ $dispatchRegister->route->name }} <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        {{ collect($historySeats->where('busy_km','>',$threshold_km)->pluck('busy_km')->count())[0] }} @lang('passengers')
                        <br>
                        <small class="text-white">{{ number_format(collect($historySeats->where('busy_km','>',$threshold_km)->pluck('busy_km')->sum())[0]/1000,'2',',','.') }} @lang('Km in total')</small>
                        <br>
                        <small class="text-white">@lang('Between') {{ $dispatchRegister->departure_time }} @lang('to') {{ $dispatchRegister->canceled?$dispatchRegister->time_canceled:$dispatchArrivaltime }}</small>
                    </h5>
                </div>
                <div class="col-md-3">
                    <ul class="nav nav-pills nav-pills-default pull-right m-0">
                        <li class="active">
                            <div class="btn-group m-b-5 m-r-5">
                                <a href="#report-tab-chart" data-toggle="tab" aria-expanded="true" class="btn btn-inverse">
                                    <i class="fa fa-bar-chart"></i> @lang('Chart')
                                </a>
                                <a href="javascript:;" class="btn btn-inverse dropdown-toggle" onclick="gsuccess('@lang('Feature on development')')" aria-expanded="false">
                                    <i class="fa fa-file-pdf-o"></i>
                                </a>
                            </div>
                        </li>
                        <li class="">
                            <div class="btn-group m-b-5 m-r-5">
                                <a href="#report-tab-table" data-toggle="tab" class="btn btn-inverse">
                                    <i class="fa fa-table"></i> @lang('Table')
                                </a>
                                <a href="javascript:;" class="btn btn-inverse dropdown-toggle" onclick="$(this).attr('href','{{ route('report-passengers-taxcentral-by-dispatch',[$dispatchRegister->id])  }}?export=true&'+$('.form-search-report').serialize())">
                                    <i class="fa fa-file-excel-o"></i>
                                </a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content p-0">
            <div id="report-tab-chart" class="tab-pane active fade in">
                @php
                    $routeDistance = $dispatchRegister->route->distance*1000;
                    $reference_location = $dispatchRegister->locations->first()
                @endphp

                @if($reference_location)
                    <div class="row p-20">
                        @php
                            //$historySeats->groupBy('seat')
                        @endphp

                        @foreach($historySeats as $historySeat)
                            <div class="col-md-12 p-0">
                                @php
                                    $activeSeatRouteDistance = $historySeat->active_km;
                                    $inactiveSeatRouteDistance = $historySeat->inactive_km;

                                    $inactivePercent = number_format($activeSeatRouteDistance*100/$routeDistance,'2','.','');
                                    $activePercent = number_format($historySeat->busy_km*100/$routeDistance,'2','.','');
                                    $activeKm = intval($historySeat->busy_km/1000);
                                    $activeTimeBy = explode('.', $historySeat->busy_time)[0];
                                    $activeTimeFrom = $historySeat->getTime('active', true);
                                    $activeTimeTo = $historySeat->getTime('inactive', true);
                                    $activeSeatRouteKm = intval($activeSeatRouteDistance/1000);
                                    $inactiveSeatRouteKm = intval($inactiveSeatRouteDistance/1000);

                                    $controlPoints = $dispatchRegister->route->controlPoints;

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
                                <div class="bg-white p-0" style="height: auto;padding: 5px;">
                                    @if($loop->first)
                                        <div class="progress progress-striped p-0 m-0 no-rounded-corner progress-lg active">
                                            <div class="progress-bar progress-bar-route p-0" style="width: 100%">
                                                <b class="" style="font-size: 1.4rem">
                                                    @lang('Total route distance') {{ $routeDistance/1000 }} Km
                                                </b>
                                            </div>
                                        </div>
                                    @endif
                                    <div class="progress p-0 m-0 no-rounded-corner progress-{{$loop->first ? 'lg' : 'xs' }}" style="opacity: {{ $loop->first ? 1 : 0.8 }};height: {{ $loop->first ? 60 : 2 }}px !important;">
                                        @php
                                            $parentLoop = $loop;
                                            $width = 0;
                                        @endphp
                                        @foreach($controlPoints as $controlPoint)
                                            @php
                                                $width = ($controlPoint->distance_next_point * 100 / $routeDistance) - 0.02;
                                                $cpDistance = intval($controlPoint->distance_from_dispatch / 1000);
                                                $width = $loop->last ? 0 : $width;
                                                //$trajectory = $controlPoint->name . ' ➤ '.($loop->index+1<count($controlPoints)?$controlPoints[$loop->index + 1]->name : '');
                                                $trajectory = "$controlPoint->name";
                                            @endphp
                                            <div class="progress-bar {{ $loop->index % 2 == 0 ? 'bg-cp-1' : 'bg-cp-2' }} p-t-5 text-left" style="width:{{ number_format(( $width ),'1','.','') }}%; font-size: 120%;position: relative"
                                                 data-toggle="tooltip" data-html="true" data-placement="top"
                                                 data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                                 title="{{ '<i class="fa fa-map-signs"></i> '.$trajectory }}"
                                            >
                                                <b class="{{ $parentLoop->first ? '' : 'hide' }} {{ $loop->first ? 'cp-first' : ($loop->last ? 'cp-last' : 'cp-normal')  }} text-center">
                                                    {{ $trajectory }} <br> {{ $cpDistance }} Km
                                                </b>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="progress m-0 p-1 progress-md">
                                        <div class="progress-bar" style="width: {{ $inactivePercent }}%;background: #2a2222 !important;">
                                            <span class="pull-left label label-sm label-inverse" style="margin-left: 5px;margin-top: 5px">@lang('Seat') {{ $historySeat->seat }}</span>
                                        </div>
                                        <div class="progress-bar bg-{{ $historySeat->busy_km>$threshold_km ? 'seat-active' : 'danger' }} active--" style="width: {{ $activePercent }}%"
                                             data-toggle="tooltip" data-html="true" data-placement="bottom"
                                             data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                             title="{{ $html_tooltip }}">
                                            <b class="m-l-10 pull-left">{{$activeSeatRouteKm}} Km</b>
                                            <b class="m-t-20 text-white"><label class="label label-lime label-sm">{{ $historySeat->seat }}</label> {{ $activeKm }} <small>Km</small> • <label class="label label-danger label-lg">${{ $tariffValue }}</label></b>
                                            <b class="m-r-10 pull-right">{{$inactiveSeatRouteKm}} Km</b>
                                        </div>
                                        <div class="progress-bar " style="width: {{ (100-($inactivePercent+$activePercent)) }}%;background: #2a2222 !important;"></div>
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
            <div id="report-tab-table" class="table-responsive tab-pane fade">
                <!-- begin table -->
                <table class="table table-bordered table-striped table-hover table-valign-middle">
                    <thead>
                    <tr class="inverse">
                        <th>N°</th>
                        <th>@lang('Vehicle')</th>
                        <th>@lang('Seat')</th>
                        <th>@lang('Event active time')</th>
                        <th>@lang('Event inactive time')</th>
                        <th>@lang('Active time')</th>
                        <th>@lang('Active kilometers')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $totalKm = 0;
                    @endphp
                    @foreach($historySeats as $historySeat)
                        @php
                            $activeSeatRouteDistance = $historySeat->active_km;
                            $inactiveSeatRouteDistance = $historySeat->inactive_km;
                            $activeSeatRouteKm = number_format($activeSeatRouteDistance/1000,'2',',','.');
                        @endphp

                        <tr class="{{ $historySeat->busy_km>$threshold_km?'':'text-danger' }}">
                            <td>{{$loop->index+1}}</td>
                            <td>{{$historySeat->plate}}</td>
                            <td>{{$historySeat->seat}}</td>
                            <td>
                                {{$historySeat->active_time ? date('H:i:s',strtotime(explode(" ",$historySeat->active_time)[1])) : __('Still busy') }}
                                <br><small class="text-muted">{{ $activeSeatRouteKm }} Km</small>
                            </td>
                            @if($historySeat->inactive_time)
                                @php
                                    $inactiveSeatRouteKm = number_format($inactiveSeatRouteDistance/1000,'2',',','.');
                                @endphp
                                <td>
                                    {{ date('H:i:s',strtotime(explode(" ",$historySeat->inactive_time)[1])) }}
                                    <br><small class="text-muted">{{ $inactiveSeatRouteKm }} Km</small>
                                </td>
                                <td>{{date('H:i:s',strtotime($historySeat->busy_time))}}</td>
                                @php
                                    $km=$historySeat->busy_km/1000;
                                    $historySeat->busy_km>$threshold_km?($totalKm += $km):null;
                                @endphp
                                <td class="{{ $historySeat->busy_km>$threshold_km?'':'danger' }}">{{number_format($km, 2, ',', '.')}}</td>
                            @else
                                <td class="text-center" colspan="3">@lang('Still busy')</td>
                            @endif
                            <td>
                                <a href="javascript:;" class="btn btn-sm btn-grey btn-link" onclick="gsuccess('@lang('Feature on development')')">
                                    <i class="fa fa-cog fa-spin"></i> @lang('Report detail')
                                </a>
                            </td>
                        </tr>
                    @endforeach
                        <tr class="inverse bg-inverse text-white">
                            <td colspan="6" class="text-right">@lang('Total Km')</td>
                            <td colspan="2" class="text-left">{{number_format($totalKm, 2, ',', '.')}}</td>
                        </tr>
                    </tbody>
                </table>
                <!-- end table -->
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
            left: -30px;
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