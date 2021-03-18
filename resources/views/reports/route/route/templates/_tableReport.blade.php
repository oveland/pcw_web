<!-- begin table -->
<table id="table-report" class="table table-bordered table-striped table-hover table-valign-middle table-report">
    <thead>
    <tr class="inverse">
        <th class="">
            <i class="fa fa-calendar text-muted"></i><br>
            @lang('Date')
        </th>
        <th class="">
            <i class="fa fa-flag text-muted"></i><br>
            @lang('Route')
        </th>
        <th>
            <i class="fa fa-retweet text-muted"></i><br>
            @lang('Round Trip')
        </th>
        <th>
            <i class="fa fa-list-ol text-muted"></i><br>
            @lang('Turn')
        </th>
        <th class="">
            <i class="fa fa-car text-muted"></i><br>
            @lang('Vehicle')
        </th>
        @if( $company->hasDriverRegisters() )
        <th>
            <i class="fa fa-user text-muted"></i><br>
            @lang('Driver') <hr class="m-0"> @lang('Dispatcher')
        </th>
        @endif
        <th>
            <i class="fa fa-clock-o text-muted"></i><br>
            @lang('Departure time')
        </th>
        <th>
            <i class="fa fa-clock-o text-muted"></i><br>
            @lang('Arrival Time')
        </th>
        <th>
            <i class="ion-android-stopwatch text-muted"></i><br>
            @lang('Route Time')
        </th>
        @if( $company->hasRecorderCounter() )
            <th class="text-center">
                <i class="fa fa-compass text-muted"></i><br>
                {{ str_limit(__('Recorder'),5) }}<br>
                <small class="text-muted">
                    @lang('Initial')
                    <hr class="m-0">
                    @lang('Final')
                </small>
            </th>
            <th class="text-center">
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-compass text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Recorder')
                </small>
            </th>
        @endif

        @if($company->hasSensorCounter())
            <th>
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Sensor')
                </small>
            </th>

            <th>
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Sensor TOTAL')
                </small>
            </th>
        @endif
        @if(false)
            <th>
                <i class="fa fa-users text-muted"></i><br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}<br>
                <small class="text-muted">
                    @lang('Sensor') @lang('Recorder')
                </small>
            </th>
        @endif
        <th width="10%">
            <i class="fa fa-rocket text-muted"></i><br>
            @lang('Actions')
        </th>
    </tr>
    </thead>
    <tbody>
    @php
        $strTime = new \App\Http\Controllers\Utils\StrTime();

        $totalDeadTime = array();
        $lastArrivalTime = array();
        $averageRouteTime = '00:00:00';

        $totalPassengersBySensor = 0;
        $totalPassengersBySensorTotal = 0;
        $totalPassengersBySensorRecorder = 0;
    @endphp

    @foreach( $dispatchRegisters as $dispatchRegister )
        @php
            $route = $dispatchRegister->route;
            $driver = $dispatchRegister->driver;
            $vehicle = $dispatchRegister->vehicle;
            $historyCounter = $reportsByVehicle[$vehicle->id]->report->history[$dispatchRegister->id];

            $endRecorder = $historyCounter->endRecorder;
            $startRecorder = $historyCounter->startRecorder;
            $passengersPerRoundTrip = $historyCounter->passengersByRoundTrip;

            $totalPassengersByRecorder = $historyCounter->totalPassengersByRoute;
            $totalPassengersBySensor +=$dispatchRegister->passengersBySensor;
            $totalPassengersBySensorTotal +=$dispatchRegister->passengersBySensorTotal;
            $totalPassengersBySensorRecorder +=$dispatchRegister->passengersBySensorRecorder;

            $invalid = ($totalPassengersByRecorder > 1000 || $totalPassengersByRecorder < 0) && !$withEndDate ? true : false;

            $offRoadPercent = $dispatchRegister->getOffRoadPercent();

            $averageRouteTime = $strTime::addStrTime($averageRouteTime, $dispatchRegister->getRouteTime(true))
        @endphp
        <tr>
            <th width="5%" class="bg-inverse text-white text-center">{{ $dispatchRegister->date }}</th>
            <th width="10%" class="bg-{{ $offRoadPercent > 50 ? 'error' : $dispatchRegister->complete() ?'inverse':'warning' }} text-white text-center">
                {{ $route->name }}
                @if($offRoadPercent)
                    <div class="m-t-10">
                        <label class="label label-{{ $offRoadPercent < 5 ? 'success': ($offRoadPercent < 50 ? 'warning': 'danger') }} tooltips" data-placement="bottom" title="@lang('Percent in off road')">
                            {{ number_format($offRoadPercent, 1,'.', '') }}% <i class="fa fa-random faa-passing animated"></i>
                        </label>
                    </div>
                @endif
            </th>
            <th width="5%" class="bg-{{ $dispatchRegister->complete() ?'inverse':'warning' }} text-white text-center">
                {{ $dispatchRegister->round_trip }}<br>
                <small>{{ $dispatchRegister->status }}</small>
                <br>
                <small>{{ $dispatchRegister->getRouteDistance(true) }} Km</small>
            </th>
            <th width="5%" class="bg-inverse text-white text-center">{{ $dispatchRegister->turn }}</th>
            <th width="5%" class="bg-inverse text-white text-center">{{ $vehicle->number }}</th>

            @if( $company->hasDriverRegisters() )
            <td width="25%" class="text-uppercase">
                @if( Auth::user()->isSuperAdmin() )
                    @php
                        $driverInfo = $driver?$driver->fullName():$dispatchRegister->driver_code;
                    @endphp
                    <div class="tooltips box-edit" data-title="@lang('Driver')">
                        <span class="box-info">
                            <span class="{{ !$driverInfo?'text-danger text-bold':'' }} text-capitalize">
                                {{ $driverInfo?$driverInfo:__('Empty') }}
                            </span>
                        </span>
                        <div class="box-edit" style="display: none">
                            <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                   data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('driver_code')"
                                   class="input-sm form-control edit-input-recorder" value="{{ $dispatchRegister->driver_code }}">
                        </div>
                    </div>
                @else
                    {{ $driver?$driver->fullName():$dispatchRegister->driver_code }}
                @endif
                @if( $dispatchRegister->user )
                    <hr class="m-0">
                    <small class="text-muted tooltips" data-title="@lang('User') @lang('Dispatcher')" data-placement="bottom">
                        {{ $dispatchRegister->user->name }}
                    </small>
                @endif
            </td>
            @endif

            <td class="text-center">
                <span class="tooltips" title="Registrado a las {{ $strTime->toString($dispatchRegister->time) }}">{{ $strTime->toString($dispatchRegister->departure_time) }}</span>
                <br>
                <small class="tooltips text-info" data-title="@lang('Vehicles without route')" data-placement="bottom">
                    {{ $dispatchRegister->available_vehicles }} <i class="fa fa-bus"></i>
                </small>
                @if( isset($lastArrivalTime[$vehicle->id]) && $lastArrivalTime[$vehicle->id] )
                    @php
                        $deadTime = $strTime->subStrTime($dispatchRegister->departure_time, $lastArrivalTime[$vehicle->id]);
                        $totalDeadTime[$vehicle->id] = $strTime->addStrTime($totalDeadTime[$vehicle->id], $deadTime);
                    @endphp
                    <br>
                    <small class="tooltips text-primary" data-title="@lang('Dead time')" data-placement="bottom">
                        <i class="ion-android-stopwatch text-muted"></i> {{ $deadTime }}
                    </small>
                    <br>
                    <small class="tooltips text-warning" data-title="@lang('Accumulated dead time')" data-placement="bottom">
                        <i class="ion-android-stopwatch text-muted"></i> {{ $totalDeadTime[$vehicle->id] }}
                    </small>
                @else
                    @php
                        $totalDeadTime[$vehicle->id] = '00:00:00';
                    @endphp
                @endif
            </td>

            <td width="10%" class="text-center">
                <span class="tooltips" data-title="@lang('Arrival Time')"  data-placement="left">
                    {{ $strTime->toString($dispatchRegister->arrival_time) }}
                </span><br>
                <small class="tooltips text-muted" data-title="@lang('Arrival Time Scheduled')" data-placement="left">
                    {{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}
                </small>
                <hr class="m-0">
                <small class="tooltips text" data-title="@lang('Arrival Time Difference')"  data-placement="left">
                    {{ $strTime->toString($dispatchRegister->arrival_time_difference) }} <i class="ion-android-stopwatch text-muted"></i>
                </small>
            </td>

            <td width="8%" class="text-center">{{ $dispatchRegister->getRouteTime() }}</td>

            @if( $company->hasRecorderCounter() )
                <td width="10%" class="p-r-0 p-l-0 text-center">
                    @if( Auth::user()->canEditRecorders() )
                        <div class="tooltips box-edit" data-title="@lang('Start Recorder')">
                            <span class="box-info">
                                <span class="">
                                    {{ $startRecorder }}
                                </span>
                            </span>
                            <div class="box-edit" style="display: none">
                                <input id="edit-start-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                       data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('start_recorder')"
                                       class="input-sm form-control edit-input-recorder" value="{{ $startRecorder }}">
                            </div>
                        </div>
                    @else
                        {{ $startRecorder }}
                    @endif
                    <hr class="m-0">
                    @if( Auth::user()->canEditRecorders() )
                        <div class="tooltips box-edit" data-title="@lang('End Recorder')">
                            <span class="box-info">
                                <span class="">
                                    {{ $endRecorder }}
                                </span>
                            </span>
                            <div class="box-edit" style="display: none">
                                <input id="edit-end-recorder-{{ $dispatchRegister->id }}" title="@lang('Press enter for edit')" name="" type="number"
                                       data-url="{{ route('report-passengers-manage-update',['action'=>'editRecorders']) }}" data-id="{{ $dispatchRegister->id }}" data-field="@lang('end_recorder')"
                                       class="input-sm form-control edit-input-recorder" value="{{ $endRecorder }}">
                            </div>
                        </div>
                    @else
                        {{ $endRecorder }}
                    @endif
                </td>
                <td width="10%" class="text-center">
                    @if( $dispatchRegister->complete() )
                        <span title="" class="{{ $invalid?'text-danger':'' }} tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Round trip').' '.($endRecorder.' - '.$startRecorder) }}">
                            {{ $endRecorder - $startRecorder }}
                        </span>
                        <hr class="m-0">
                        <small class="{{ $invalid?'text-danger':'' }} text-bold tooltips" data-original-title="{{ $invalid?__('Verify possible error in register data'):__('Accumulated day') }}">
                            {{ $totalPassengersByRecorder }}
                        </small>
                    @else
                        ...
                        <hr class="hr">
                        ...
                    @endif
                </td>
            @endif

            @if($company->hasSensorCounter())
                <td width="10%" class="text-center">
                    <span class="tooltips {{ $dispatchRegister->passengersBySensor > 30 ? 'label label-warning' : '' }} " data-title="@lang('Round trip')" style="font-size: 1.5rem !important;">
                        {{ $dispatchRegister->passengersBySensor }}
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold text-muted" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensor }}
                    </small>
                </td>

                <td width="10%" class="text-center">
                    <span class="tooltips {{ $dispatchRegister->passengersBySensorTotal > 30 ? 'label label-warning' : '' }} " data-title="@lang('Round trip')" style="font-size: 1.5rem !important;">
                        {{ $dispatchRegister->passengersBySensorTotal }}
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold text-muted" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensorTotal }}
                    </small>
                </td>
            @endif
            @if( false )
                <td width="10%" class="text-center">
                    <span class="tooltips" data-title="@lang('Round trip')">
                        {{ $dispatchRegister->passengersBySensorRecorder }}
                    </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold" data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensorRecorder }}
                    </small>
                </td>
            @endif

            <td width="15%" class="text-center">
                @if( Auth::user()->canMakeTakings() )
                <a id="btn-taking-{{ $dispatchRegister->id }}" href="#modal-takings-passengers" data-toggle="modal" onclick="showTakingsForm('{{ route("operation-routes-takings-form", ["dispatchRegister" => $dispatchRegister->id]) }}')"
                   class="btn {{ $dispatchRegister->takings->isTaken() ? 'purple' : 'purple-sharp btn-outline' }} sbold uppercase faa-parent animated-hover btn-circle tooltips m-b-5"
                   data-original-title="<i class='fa fa-users faa-float animated'></i> @lang('Takings')" data-html="true">
                    <i class="icon-briefcase faa-ring" style="margin-right: 0; margin-left: 0px"></i>
                    <i class="fa fa-dollar faa-vertical" style="margin-right: 0px; margin-left: 0"></i>
                </a>
                @endif

                <a href="#modal-route-report"
                   class="btn green-haze faa-parent animated-hover btn-show-chart-route-report btn-circle btn-outline tooltips"
                   data-toggle="modal"
                   data-url="{{ route('report-route-chart',['dispatchRegister'=>$dispatchRegister->id]) }}"
                   data-url-off-road-report="{{ route('report-route-off-road',['dispatchRegister'=>$dispatchRegister->id]) }}"
                   data-original-title="@lang('Graph report detail')">
                    <i class="fa fa-area-chart faa-pulse"></i>
                </a>

                <div class="p-t-5">
                    @if( Auth::user()->isSuperAdmin() )
                        <button onclick="executeDAR({{ $dispatchRegister->id }})" class="btn btn-xs btn-warning faa-parent animated-hover btn-circle tooltips"
                                data-original-title="@lang('Execute DAR')" data-placement="bottom">
                            <i class="fa fa-cogs faa-pulse"></i>
                        </button>

                        <a href="#modal-report-log"
                           data-toggle="modal" data-placement="bottom"
                           onclick="$('#iframe-report-log').hide().attr('src','{{ route('report-route-get-log',['dispatchRegister' => $dispatchRegister->id]) }}').fadeIn()"
                           class="btn btn-xs btn-info faa-parent animated-hover tooltips btn-circle" data-original-title="@lang('Show report details')">
                            <i class="fa fa-code faa-pulse"></i>
                        </a>

                        <button class="btn btn-xs btn-danger faa-parent animated-hover btn-circle tooltips edit-field-dr"
                                data-original-title="@lang('Cancel turn')" data-placement="bottom"
                                data-confirm="@lang('Confirm action for discard dispatch turn')"
                                data-url="{{ route('report-passengers-manage-update',['action'=>'cancelTurn']) }}" data-id="{{ $dispatchRegister->id }}">
                            <i class="fa fa-times faa-shake"></i>
                        </button>
                    @endif

                    @if( Auth::user()->isSuperAdmin() )
                        <small class="badge tooltips" data-original-title="@lang('Locations') / @lang('Reports')" data-placement="bottom">{!! $dispatchRegister->locations()->count() !!} / {!! $dispatchRegister->reports()->count() !!}</small>
                    @endif
                </div>
            </td>
        </tr>
        @php
            $lastArrivalTime[$vehicle->id] = $dispatchRegister->arrival_time;
        @endphp

        @if($offRoadPercent)
            <script>
                $('.icon-car-{{$vehicle->id}}').removeClass('f-s-8').addClass('text-{{ $offRoadPercent < 50 ? 'warning': 'danger' }} faa-passing animated');
            </script>
        @endif
    @endforeach
    @if($dispatchRegisters->count())
    <tr>
        <td colspan="7">

        </td>
        <td class="text-center tooltips" data-title="@lang('Average'): @lang('Route time')">
            {{ $strTime::segToStrTime($strTime::toSeg($averageRouteTime)/$dispatchRegisters->count()) }}
        </td>
        <td colspan="3">

        </td>
    </tr>
    @endif
    </tbody>
</table>
<!-- end table -->

<script type="application/javascript">

    @if( Auth::user()->belongsToCootransol() )
    let modalExecuteDAR = $('#modal-execute-DAR');

    function executeDAR(dispatchRegisterId){
        modalExecuteDAR.modal('show');
        modalExecuteDAR.find('pre').html('@lang('This process can take several minutes')...');
        $.ajax({
            url: '{{ route('route-ajax-action') }}',
            data:{
                option:'executeDAR',
                dispatchRegisterId: dispatchRegisterId
            },
            dataType:'json',
            timeout: 0,
            success:function(data){
                if( data.success ){
                    modalExecuteDAR.find('pre').html(data.infoProcess.totalNewReports+' @lang('locations have been processed')<br>@lang('Detected route'): '+data.infoProcess.routeName);
                    gsuccess('@lang('Process executed successfully')');
                }else{
                    let message = '@lang('An error occurred in the process. Contact your administrator')';
                    gerror(message);
                    modalExecuteDAR.find('pre').html(message + '<hr>Data: ' + JSON.stringify(data));
                }
            },
            error:function(){
                gerror('@lang('An error occurred in the process. Contact your administrator')')
            }
        });
    }
    @endif

    @if( Auth::user()->canMakeTakings() )
    function showTakingsForm(url) {
        let modalTakingsPassengers = $('#modal-takings-passengers');
        let modalBody = modalTakingsPassengers.find('.modal-body');
        modalBody.html($('.loading').html()).load(url);
    }
    @endif
</script>

<style>
    #modal-takings-passengers button.close{
        margin: 10px !important;
    }
</style>