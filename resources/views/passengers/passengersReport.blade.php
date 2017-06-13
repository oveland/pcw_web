@if(count($historySeats))
    <div class="panel panel-inverse col-md-12">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand" title="@lang('Expand / Compress')">
                    <i class="fa fa-expand"></i>
                </a>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h5 class="text-white m-t-10">
                        <i class="fa fa-user-circle" aria-hidden="true"></i>
                        {{ $dispatchRegister->route->name }} <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                        {{ number_format(collect($historySeats->pluck('busy_km')->sum())[0]/1000,'2',',','.') }} @lang('Km in total'),
                        @lang('between') {{ $dispatchRegister->departure_time }} @lang('and') {{ $dispatchRegister->canceled?$dispatchRegister->time_canceled:$dispatchRegister->arrival_time_scheduled }}
                    </h5>
                </div>
                <div class="col-md-3">
                    <ul class="nav nav-pills nav-pills-default pull-right">
                        <li class="active">
                            <div class="btn-group m-b-5 m-r-5">
                                <a href="#report-tab-table" data-toggle="tab" aria-expanded="true" class="btn btn-default">
                                    <i class="fa fa-table"></i> @lang('Table')
                                </a>
                                <a href="javascript:;" data-toggle="dropdown" class="btn btn-inverse dropdown-toggle" aria-expanded="false">
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <a href="" class="bg-lime-dark" style="color: white !important;"
                                           onclick="$(this).attr('href','{{ route('passengers-by-dispatch',[$dispatchRegister->id])  }}?export=true&'+$('.form-search-report').serialize())">
                                            <i class="fa fa-file-excel-o"></i> @lang('Export excel')
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="">
                            <div class="btn-group m-b-5 m-r-5">
                                <a href="#report-tab-chart" data-toggle="tab" aria-expanded="true" class="btn btn-default">
                                    <i class="fa fa-bar-chart"></i> @lang('Chart')
                                </a>
                                <a href="javascript:;" data-toggle="dropdown" class="btn btn-inverse dropdown-toggle" aria-expanded="false">
                                    <i class="fa fa-angle-down"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li>
                                        <a href="javascript:;" class="" onclick="gsuccess('@lang('Feature on development')')">
                                            <i class="fa fa-file-pdf-o"></i> @lang('Export PDF')
                                            <i class="fa fa-cog fa-spin"></i>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content p-0">
            <div id="report-tab-table" class="table-responsive tab-pane fade in">
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
                    @php($totalKm = 0)
                    @foreach($historySeats as $historySeat)
                        <tr>
                            <td>{{$loop->index+1}}</td>
                            <td>{{$historySeat->plate}}</td>
                            <td>{{$historySeat->seat}}</td>
                            <td>{{$historySeat->active_time?date('H:i:s',strtotime(explode(" ",$historySeat->active_time)[1])):__('Still busy')}}</td>
                            @if($historySeat->inactive_time)
                                <td>{{date('H:i:s',strtotime(explode(" ",$historySeat->inactive_time)[1]))}}</td>
                                <td>{{date('H:i:s',strtotime($historySeat->busy_time))}}</td>
                                @php($km=$historySeat->busy_km/1000)
                                @php($totalKm += $km)
                                <td>{{number_format($km, 2, ',', '.')}}</td>
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
            <div id="report-tab-chart" class="tab-pane fade active in">
                <div class="row p-20">
                    @foreach($historySeats as $historySeat)
                        <div class="col-md-12 p-0">
                            @php($routeDistance = $dispatchRegister->route->distance*1000)
                            @php($reference_location = $dispatchRegister->reports->first()->location)
                            @php($activeSeatRouteDistance = $historySeat->active_km - $reference_location->odometer)
                            @php($inactiveSeatRouteDistance = $historySeat->inactive_km - $reference_location->odometer)

                            @php($inactivePercent = number_format($activeSeatRouteDistance*100/$routeDistance,'2','.',''))
                            @php($activePercent = number_format($historySeat->busy_km*100/$routeDistance,'2','.',''))
                            @php($activeKm = number_format($historySeat->busy_km/1000,'2',',','.'))
                            @php($activeSeatRouteKm = number_format($activeSeatRouteDistance/1000,'2',',','.'))
                            @php($inactiveSeatRouteKm = number_format($inactiveSeatRouteDistance/1000,'2',',','.'))

                            @php($html_toltip = "
                                <div style='font-size:90% !important'>
                                    <b class='text-warning'>".__('Seat')."</b> $historySeat->seat<br>
                                    <b class='text-warning'>".__('Active by')."</b> $activeKm Km<br>
                                    <b class='text-muted'>".__('From')."</b> $activeSeatRouteKm Km <b class='text-muted'>".__('to')."</b> $inactiveSeatRouteKm Km
                                </div>
                            ")
                            <div class="bg-white p-0" style="height: auto;padding: 5px;">
                                @if($loop->first)
                                    <div class="progress progress-striped p-0 m-0 no-rounded-corner progress-lg active">
                                    <div class="progress-bar progress-bar-success p-0" style="width: 100%">
                                        <b class="">
                                            @lang('Total route distance') {{ $routeDistance/1000 }} Km
                                        </b>
                                    </div>
                                </div>
                                @endif
                                <div class="progress progress-striped p-0 m-0 no-rounded-corner progress-{{$loop->first?'lg':'sm'}}" style="opacity: {{$loop->first?1:0.8}};">
                                    <div class="progress-bar bg-inverse-dark p-0" style="width: 33.3%;"><b class="{{$loop->first?'show':'hide'}}">Control Point 1</b></div>
                                    <div class="progress-bar bg-inverse-light p-0" style="width: 33.3%"><b class="{{$loop->first?'show':'hide'}}">Control Point 2</b></div>
                                    <div class="progress-bar bg-inverse-dark p-0" style="width: 33.3%"><b class="{{$loop->first?'show':'hide'}}">Control Point 3</b></div>
                                </div>
                                <div class="progress progress-striped m-0 p-1 progress-md">
                                    <div class="progress-bar progress-bar-grey bg-grey-light" style="width: {{ $inactivePercent }}%"></div>
                                    <div class="progress-bar bg-warning-dark active--" style="width: {{ $activePercent }}%"
                                         data-toggle="tooltip" data-html="true" data-placement="bottom"
                                         data-template="<div class='tooltip' role='tooltip'><div class='tooltip-arrow'></div><div class='tooltip-inner width-md'></div></div>"
                                         title="{{ $html_toltip }}">
                                        <b class="m-l-10 pull-left">{{$activeSeatRouteKm}} Km</b>
                                        <b class="m-t-20 text-white">@lang('Seat') {{ $historySeat->seat }}. @lang('Active by') {{ $activeKm }} Km</b>
                                        <b class="m-r-10 pull-right">{{$inactiveSeatRouteKm}} Km</b>
                                    </div>
                                    <div class="progress-bar progress-bar-grey bg-grey-light" style="width: {{ (100-($inactivePercent+$activePercent)) }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
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