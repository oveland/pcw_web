@php
    $thresholdAlertSS = 1;
    $thresholdAlertNR = 2;
    $thresholdMinLocations = 100;
    $alertPhoto = false;

    $isExpresoPalmira = $company->id == \App\Models\Company\Company::EXPRESO_PALMIRA;
    $user = Auth::user();



@endphp
<!-- begin table -->
<table id="table-report"
       class="table table-bordered table-striped table-hover table-valign-middle table-report">
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
                @lang('Driver')
                <hr class="m-0"> @lang('Dispatcher')
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
        @if( Auth::user()->isSuperAdmin())
            <th width="10%" class="text-center">
                <i class="fa fa-file  fa-3x fa-fw"></i><br>
                @lang('Planilla FICS')
            </th>
        @endif
        <th width="10%" class="text-center">
            <i class="fa fa-file  fa-3x fa-fw"></i><br>
            @lang('Pasajeros Planilla')
        </th>
        <th width="10%" class="text-center">
            <i class="fa fa-file  fa-3x fa-fw"></i><br>
            @lang('Número de planilla')
            <br>
            <div class="input-group input-group-sm text-center" style="display: none;">
                <input type="text" class="form-control form-control-sm search-input">
                 <span class="input-group-append">
                    <button type="button" class="btn btn-info btn-sm search-button">Go!</button>
                </span>
            </div>
        </th>
        @if( $company->hasRecorderCounter() && $user->canViewPasengervisual())
            <th class="text-center">
                <i class="fa fa-compass text-muted"></i><br>
                {{ str_limit(__(  'Pasajeros'),9) }}
                <br>
                {{ str_limit(__(  'Visual'), 6) }}
            </th>
            <th class="text-center">
                <i class="icon-users text-muted"></i><br>
                {{ str_limit(__('Pasajeros'),9) }}
                <br>
                {{ str_limit(__('Cámaras')) }}
            </th>
        @endif
        @if($user->canViewtotalSistem())
            <th>
                <i class="icon-users text-muted">
                </i><br>{{"Total Sistema"}}
            </th>
        @endif
        @if($company->hasSensorTotalCounter())
            <th>
                <i class="icon-users text-muted"></i>
                <br>
                <small><i class="fa fa-crosshairs text-muted"></i></small> {{ str_limit(__('Passengers'),5) }}
                <br>
                <small class="text-muted">
                    @lang('Sensor')
                </small>
                <small class="text-muted tooltips"
                       data-title="Sumatoria del conteo enviado por el sensor independiente de los turnos/despachos realizados en el día">
                    @lang('Sensor total')
                </small>
            </th>
        @endif

        @if($company->hasSensorRecorderCounter())
            <th>
                <i class="fa fa-dollar text-muted"></i>
                <i class="icon-users text-muted"></i>
                <br>
                <small>
                    {{ $company->getSensorRecorderCounterLabel() }}
                </small>
            </th>
        @endif
        @if($user->canViewAverageCount())
            <th>
                <i class="icon-users text-muted">
                </i><br>{{"Promedio"}}
            </th>
        @endif
        @if($user->CanViewInfoPhotos())
            <th>
                <i class="fa fa-camera text-muted"></i><br>
                @lang('Info. Fotos')
            </th>
        @endif
        @if(Auth::user()->isSuperAdmin())
            <th class="text-center">
                <i class="icon-users text-muted"></i><br>
                @lang('Conteo Maximos')
            </th>
        @endif
        @if($user->canViewAction())
            <th width="10%">
                <i class="fa fa-rocket text-muted"></i><br>
                @lang('Actions')
            </th>
        @endif
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

        $maxInvalidGPSPercent = 0;
        $lowerGPSReport = 0;

        $sumatoriaCountMax = 0;
        $sumByCountSpreadSheet = 0;
        $sumByCountSensor = 0;
        $sumByCountProm = 0;
        $sumByCountManual=0;
        $sumByCountPassengerVisual=0;
        $sumByCountSpreadSheetFICS=0;
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
            $totalPhotosNew = $dispatchRegister;

            $invalid = ($totalPassengersByRecorder > 1000 || $totalPassengersByRecorder < 0) && !$withEndDate ? true : false;

            $offRoadPercent = $dispatchRegister->getOffRoadPercent();

            if($dispatchRegister->complete()) {
                $averageRouteTime = $strTime::addStrTime($averageRouteTime, $dispatchRegister->getRouteTime(true));
            }


            $invalidGPSPercent = 0;
            if(Auth::user()->isSuperAdmin()){
                $invalidGPSPercent = $dispatchRegister->invalidGPSPercent();

                if($invalidGPSPercent > $maxInvalidGPSPercent) {
                    $maxInvalidGPSPercent = $invalidGPSPercent;
                }
            }

        $color = $offRoadPercent > 50 ? 'red-sunglo' : ($dispatchRegister->complete() ? 'inverse' : ($dispatchRegister->isCancelled() ? 'yellow-crusta' : 'yellow-soft'));
        @endphp
        <tr class="{{ $dispatchRegister->isCancelled() ? 'row-turn-cancelled' : '' }}">
            <th width="5%"
                class="bg-{{ $color }} text-white text-center">
                {{ $dispatchRegister->date }}
                @if( Auth::user()->isSuperAdmin() )
                    <div class="text-sm">
                        <small>{{ $dispatchRegister->id }}</small>
                    </div>
                @endif
            </th>
            <th width="10%"
                class="bg-{{ $color }} text-white text-center">
                <span class="m-b-5">
                    @if($dispatchRegister->processedByARD())
                        @php
                            $dv = $dispatchRegister->dispatcherVehicle;
                        @endphp
                        <span class="label label-lime label-lg">{{ $route->name }}</span>
                        <small class="text-muted"
                               style="margin-top: 12px;display: block">{{ $dv ? $dv->route->name : '---' }}</small>
                    @else
                        <span>{{ $route->name }}</span>
                    @endif
                </span>

                @if($dispatchRegister->hasValidOffRoad() && $offRoadPercent)
                    <div class="m-t-1">
                        <label class="label label-{{ $offRoadPercent < 5 ? 'success': ($offRoadPercent < 50 ? 'warning': 'danger bg-red-mint p-5') }} tooltips"
                               data-placement="bottom"
                               title="@lang('Percent in off road')">
                            {{ number_format($offRoadPercent, 1,'.', '') }}
                            %
                            <i class="fa fa-random faa-passing animated"></i>
                        </label>
                    </div>
                @endif
                @if(Auth::user()->isSuperAdmin() && $invalidGPSPercent)
                    <div class="m-t-1">
                        <label class="label label-{{ $invalidGPSPercent < $thresholdAlertSS ? 'default': 'danger' }} tooltips"
                               data-placement="bottom"
                               title="@lang('GPS with issues')">
                            @if($invalidGPSPercent)
                                {{ $invalidGPSPercent  }}
                                %
                                <i class="fa fa-signal faa-flash animated"></i>
                            @endif
                        </label>
                    </div>
                @endif
            </th>
            <th width="5%"
                class="bg-{{ $color }} text-white text-center">
                {{ $dispatchRegister->round_trip }}
                <br>
                <small class="html-observations {{ $dispatchRegister->isCancelled() ? 'cancelled' : '' }}">{!! $dispatchRegister->status !!}</small>
                @if($dispatchRegister->isCancelled())
                    <small>{{ $dispatchRegister->time_canceled }}</small>
                @endif
                <hr>
                <small>
                    {{ $dispatchRegister->getRouteDistance(true) }} Km
                </small>
            </th>
            <th width="5%"
                class="bg-{{ $color }} text-white text-center">{{ $dispatchRegister->turn }}</th>
            <th width="5%" class="bg-{{ $color }} text-white text-center">
                {{ $vehicle->number }}
                @if( Auth::user()->isSuperAdmin() )
                    @php
                        $totalLocations = $dispatchRegister->locations()->count();
                        $alert = false;
                        if($totalLocations < $thresholdMinLocations) {
                            $lowerGPSReport++;
                            $alert = true;
                        }
                    @endphp
                    <br>
                    <small class="badge tooltips bg-{{ $alert ? 'red' : '' }}"
                           data-original-title="{!! $totalLocations !!} @lang('Locations')"
                           data-placement="bottom">
                        <i class="fa fa-location-arrow"></i> {!! $totalLocations !!}
                    </small>
                @endif
            </th>

            @if($company->hasDriverRegisters())
                <td width="25%"
                    class="text-uppercase">
                    @if(Auth::user()->canEditDrivers())
                        @php
                            $driverInfo = $driver ? $driver->fullName() : $dispatchRegister->driver_code;
                            $driverInfo = trim($driverInfo);
                        @endphp
                        <div class="tooltips box-edit"
                             data-title="@lang('Edit') @lang('Driver')">
                        <span class="box-info">
                            <span class="{{ !$driverInfo?'text-danger text-bold':'' }} text-capitalize">
                                {{ $driverInfo ?: __('None') }}
                            </span>
                        </span>
                            <div class="box-edit"
                                 style="display: none">
                                @php
                                    $obs = $dispatchRegister->getObservation('driver_code');
                                @endphp
                                <input id="edit-start-recorder-{{ $dispatchRegister->id }}"
                                       title="@lang('Press enter for edit')"
                                       name=""
                                       type="number"
                                       data-url="{{ route('report-passengers-manage-update', ['action' => 'editField']) }}"
                                       data-id="{{ $dispatchRegister->id }}"
                                       data-field="driver_code"
                                       class="input-sm form-control edit-input-recorder edit-input-value"
                                       value="{{ $dispatchRegister->driver_code }}">
                                <div class="box-obs">
                                <textarea
                                        name=""
                                        rows="3"
                                        class="input-sm form-control edit-input-obs"
                                        placeholder="@lang('Observations')"
                                >{{ $obs->observation }}</textarea>
                                    @if($obs->updated_at)
                                        <div class="text-muted text-center box-audit">
                                            <small style="font-size: 0.9rem">{{ $obs->user->username }}</small>
                                            ·
                                            <small style="font-size: 0.9rem">{{ $obs->updated_at }}</small>
                                        </div>
                                    @endif
                                    <button class="btn btn-xs btn-default m-5 edit-btn-cancel"
                                            title="@lang('Cancel')">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn btn-xs btn-success m-5 edit-btn-save"
                                            title="@lang('Save')">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        {{ $driver ? $driver->fullName():$dispatchRegister->driver_code }}
                    @endif
                    @if($dispatchRegister->user)
                        <hr class="m-0">
                        <small class="text-muted tooltips"
                               data-title="@lang('User') @lang('Dispatcher')"
                               data-placement="bottom">
                            {{ $dispatchRegister->user->name }}
                        </small>
                    @endif
                </td>
            @endif

            <td width="10%" class="text-center">
                <span class="tooltips" title="Registrado a las {{ $dispatchRegister->updated_at }}">{{ $strTime->toString($dispatchRegister->departure_time) }}</span>
            </td>

            <td width="10%"
                class="text-center">
                <span class="tooltips"
                      data-title="@lang('Arrival Time')"
                      data-placement="left">
                            {{ $strTime->toString($dispatchRegister->arrival_time) }}
                </span><br>
                <small class="tooltips text-muted"
                       data-title="@lang('Arrival Time Scheduled')"
                       data-placement="left">
                    {{ $strTime->toString($dispatchRegister->arrival_time_scheduled) }}
                </small>
                <hr class="m-0">
                <small class="tooltips text"
                       data-title="@lang('Arrival Time Difference')"
                       data-placement="left">

                    {{ $strTime->toString($dispatchRegister->arrival_time_difference) }}
                    <i class="ion-android-stopwatch text-muted"></i>
                </small>
            </td>

            @if(!$isExpresoPalmira)
                <td width="8%"
                    class="text-center">
                    {{$dispatchRegister->getRouteTime()}}
                </td>
            @endif
            @if( Auth::user()->isSuperAdmin())
                <td width="6%" class="p-r-0 p-l-0 text-center" style="font-weight: 900; background: #ffd39e">
                    @php
                        $spreadsheetPassengersSync = $dispatchRegister->getObservation('spreadsheet_passengers_sync');
                        $sumByCountSpreadSheetFICS += $spreadsheetPassengersSync->value;
                    @endphp
                    <span class="box-info">
                        {{ $spreadsheetPassengersSync->value ?? 0 }}
                        </span>
                    @if($spreadsheetPassengersSync->observation)
                        <br>
                        <small class="tooltips text-bold text-xs" data-title="@lang('# Spreadsheet') sincronizada"
                               data-placement="bottom">
                            <i class="fa fa-file-o text-muted"></i> {{ $spreadsheetPassengersSync->observation }}
                        </small>
                    @endif
                </td>
            @endif

            <td width="6%" class="p-r-0 p-l-0 text-center" style="font-weight: 900; background: #b7f4ff">
                {{--td pasajeros planilla--}}
                @php
                    $spreadsheetPassengers = $dispatchRegister->getObservation('spreadsheet_passengers');
                    $sumByCountManual += $spreadsheetPassengers->value;
                @endphp

                @if( Auth::user()->canEditRecorders() && $dispatchRegister->complete())
                    <div class="tooltips box-edit" data-title="@lang('Edit') @lang('Pasajeros planilla')">
                        <span class="box-info">{{ $spreadsheetPassengers->value ?? 0 }}</span>
                        <div class="box-edit" style="display: none">
                            <input id="edit-end-recorder-{{ $dispatchRegister->id }}"
                                   title="@lang('Press enter for edit')"
                                   name=""
                                   type="number"
                                   data-url="{{ route('report-passengers-manage-update',['action'=> 'editField']) }}"
                                   data-id="{{ $dispatchRegister->id }}"
                                   data-field="spreadsheet_passengers"
                                   data-single="true"
                                   class="input-sm form-control edit-input-recorder edit-input-value"
                                   value="{{ $spreadsheetPassengers->value }}"
                                   onKeyUp="$('#edit-spreadsheet-passengers-obs-{{ $dispatchRegister->id }}').val($(this).val())"
                            />
                            <div class="box-obs ">
                                <textarea style="display: none"
                                          name=""
                                          rows="3"
                                          class="input-sm form-control edit-input-obs"
                                          placeholder="{{ $isExpresoPalmira ? __('# Spreadsheet') : __('Observations') }}">
                                    {{ $spreadsheetPassengers->observation }}
                                </textarea>
                                @if($spreadsheetPassengers->updated_at)
                                    <div class="text-muted text-center box-audit">
                                        <small style="font-size: 0.9rem">{{ $spreadsheetPassengers->user->username }}</small>
                                        ·
                                        <small style="font-size: 0.9rem">{{ $spreadsheetPassengers->updated_at }}</small>
                                    </div>
                                @endif
                                <button class="btn btn-xs btn-default m-5 edit-btn-cancel"
                                        title="@lang('Cancel')">
                                    <i class="fa fa-times"></i>
                                </button>
                                <button class="btn btn-xs btn-success m-5 edit-btn-save"
                                        title="@lang('Save')"
                                        onclick="return confirm('Confirma que los datos son correctos')">

                                    <i class="fa fa-save"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    <span class="box-info">{{ $spreadsheetPassengers->value ?? 0 }}</span>
                @endif
            </td>
            <td class="text-center" style="font-weight: bold;background: #b7f4ff">
                {{--td Numero de planilla 05/08/2023--}}
                @if( Auth::user()->canEditRecorders() && $dispatchRegister->complete())
                    <div class="tooltips box-edit" data-title="@lang('Modificar planilla')">
                        <span class="tooltips text-bold" data-title="@lang('# Spreadsheet')" data-placement="bottom">
                            <i class="fa fa-file-o text-muted"></i> {{ $spreadsheetPassengers->observation ?? '0' }}
                        </span>
                        <div class="box-edit" style="display: none">
                            <input id="edit-spreadsheet-passengers-obs-{{ $dispatchRegister->id }}"style="display: none"
                               title="@lang('Press enter for edit')"
                               name=""
                               type="number"
                               data-url="{{ route('report-passengers-manage-update',['action'=> 'editField']) }}"
                               data-id="{{ $dispatchRegister->id }}"
                               data-field="spreadsheet_passengers"
                               class="input-sm form-control edit-input-recorder edit-input-value"
                               value="{{ $spreadsheetPassengers->value }}"/>
                            <div class="box-obs">
                                    <textarea
                                            name=""
                                            rows="3"
                                            class="input-sm form-control edit-input-obs result-textarea"
                                            placeholder="@lang('# Spreadsheet')">{{ $spreadsheetPassengers->observation }}</textarea>
                                    @if($spreadsheetPassengers->updated_at)
                                    <div class="text-muted text-center box-audit">
                                        <small style="font-size: 0.9rem">{{ $spreadsheetPassengers->user->username }}</small>
                                        ·
                                        <small style="font-size: 0.9rem">{{ $spreadsheetPassengers->updated_at }}</small>
                                    </div>
                                    @endif
                                     <button class="btn btn-xs btn-default m-5 edit-btn-cancel"
                                            title="@lang('Cancel')">
                                            <i class="fa fa-times"></i>
                                     </button>
                                    <button class="btn btn-xs btn-success m-5 edit-btn-save"
                                            title="@lang('Save')"
                                            onclick="return confirm('Confirma que los datos son correctos')">
                                            <i class="fa fa-save"></i>
                                    </button>
                            </div>
                        </div>
                    </div>
                @else
                    <br>
                    <span class="tooltips text-bold" data-title="@lang('# Spreadsheet')" data-placement="bottom">
                        <i class="fa fa-file-o text-muted"></i> {{ $spreadsheetPassengers->observation ?? '0' }}
                    </span>
                @endif
            </td>


            @if( $company->hasRecorderCounter() && $user->canViewPasengervisual())
                {{-- Columna pasajeros visual--}}
                <td width="10%" class="p-r-0 p-l-0 text-center" style="background:  #b7f4ff ">
                    @php
                        $visualPassengers = $dispatchRegister->getObservation('end_recorder');
                        $sumByCountPassengerVisual += $visualPassengers->value;
                    @endphp

                    @if( Auth::user()->canEditRecorders() && $dispatchRegister->complete())
                        <div class="tooltips box-edit" data-title="@lang('Edit') @lang('Manual count')">
                            <span class="box-info">
                                <span class="">
                                        {{ $visualPassengers->value ?? 0 }}
                                </span>
                            </span>
                            <div class="box-edit" style="display: none">
                                <input id="edit-end-recorder-{{ $dispatchRegister->id }}"
                                       title="@lang('Press enter for edit')"
                                       name=""
                                       type="number"
                                       data-url="{{ route('report-passengers-manage-update',['action'=> 'editField']) }}"
                                       data-id="{{ $dispatchRegister->id }}"
                                       data-field="end_recorder"
                                       data-single="true"
                                       class="input-sm form-control edit-input-recorder edit-input-value"
                                       value="{{ $visualPassengers->value }}">
                                <div class="box-obs">
                                    <textarea style="display: none"
                                            name=""
                                            rows="3"
                                            class="input-sm form-control edit-input-obs"
                                            placeholder="@lang('# Spreadsheet')">{{ $visualPassengers->observation }}</textarea>
                                    @if($visualPassengers->updated_at)
                                        <div class="text-muted text-center box-audit">
                                            <small style="font-size: 0.9rem">{{ $visualPassengers->user->username }}</small>
                                            ·
                                            <small style="font-size: 0.9rem">{{ $visualPassengers->updated_at }}</small>
                                        </div>
                                    @endif
                                    <button class="btn btn-xs btn-default m-5 edit-btn-cancel"
                                            title="@lang('Cancel')">
                                        <i class="fa fa-times"></i>
                                    </button>
                                    <button class="btn btn-xs btn-success m-5 edit-btn-save"
                                            title="@lang('Save')"
                                            onclick="return confirm('Confirma que los datos son correctos')">
                                        <i class="fa fa-save"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @else
                        {{ $visualPassengers->value }}
                    @endif
                </td>
                @if($user->canViewtotalSistem())
                    <td width="10%" class="text-center">
                        <span title=""
                              class=" tooltips"
                              data-original-title="Conteo camaras">
                            {{ $dispatchRegister->final_sensor_counter }}
                        </span>
                    </td>
                @endif
            @endif
            @php
                $sumByCountSpreadSheet += $spreadsheetPassengers->value;
                $countBySensorFinal = $dispatchRegister->final_sensor_counter;
                $sumByCountSensor += $countBySensorFinal;
            @endphp
            @if($user->canViewtotalSistem())
                <td width="10%" class="text-center" style="background: #c4c9d0">
                    @php
                        $timeFringe = $dispatchRegister->departure_time;
                        $promPassengers = 0;
                        $routeProm = $dispatchRegister->route_id;
                        if ($routeProm==285 || $routeProm==286){
                             $promPassengers = 15;
                        }
                          switch (true) {
                              case ($timeFringe >= '04:00:00' && $timeFringe <= '06:00:59'):
                                  if ($routeProm==279||$routeProm==280){
                                     $promPassengers = 10;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                              case ($timeFringe >= '06:01:00' && $timeFringe <= '09:00:51'):
                                  if ($routeProm==279||$routeProm==280){
                                    $promPassengers = 22;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                              case ($timeFringe >= '09:01:00' && $timeFringe <= '11:00:59'):
                                  if ($routeProm==279||$routeProm==280){
                                      $promPassengers = 18;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                              case ($timeFringe >= '11:01:00' && $timeFringe <= '14:00:59'):
                                   if ($routeProm==279||$routeProm==280){
                                    $promPassengers = 19;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                               case ($timeFringe >= '14:01:00' && $timeFringe <= '17:00:59'):
                                   if ($routeProm==279||$routeProm==280){
                                     $promPassengers = 20;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                               break;
                               case ($timeFringe >= '17:01:00' && $timeFringe <= '20:00:59'):
                                  if ($routeProm==279||$routeProm==280){
                                     $promPassengers = 21;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                               case ($timeFringe >= '20:01:00' && $timeFringe <= '23:59:59'):
                                  if ($routeProm==279||$routeProm==280){
                                    $promPassengers = 13;
                                  }else if ($routeProm==282){
                                      $promPassengers = 3;
                                  }else if ($routeProm==283){
                                      $promPassengers = 27;
                                  }
                              break;
                          }
                    @endphp

                    @if($dispatchRegister->final_sensor_counter <= $spreadsheetPassengers->value)
                        <span class="tooltips" title="Número de planilla">
                            {{ $spreadsheetPassengers->value ?? 0 }}
                        </span>
                    @elseif($dispatchRegister->final_sensor_counter>= $promPassengers )
                        <span class="tooltips" title="Número de cámara">
                            {{ $countBySensorFinal ?? 0 }}
                        </span>
                    @else
                        <span class="tooltips" style="color: darkred; font-weight: 900"
                              title="Número de cámara < promedio">
                            {{ $countBySensorFinal ?? 0 }}
                        </span>
                    @endif
                </td>
            @endif
            @if($company->hasSensorTotalCounter())
                <td width="8%"
                    class="text-center">
                    <div style="display: flex;">
                        <div style="width: 100%">
                            <span class="tooltips"
                                  data-title="@lang('Round trip')"
                                  style="font-size: 1.5rem !important;">
                                {{ $dispatchRegister->passengersBySensor }}
                            </span>
                            <hr class="m-0">
                            <small class="tooltips text-bold text-muted"
                                   data-title="@lang('Accumulated day')">
                                {{ $totalPassengersBySensor }}
                            </small>
                        </div>
                        <div class="{{ $company->id == $company::TRANSPUBENZA ? '' : 'hide' }}"
                             style="width: 50%">
                            <span class="tooltips"
                                  data-title="@lang('Round trip')"
                                  style="font-size: 1.5rem !important;">
                                {{ $dispatchRegister->passengersBySensorTotal }}
                            </span>
                            <hr class="m-0">
                            <small class="tooltips text-bold text-muted"
                                   data-title="@lang('Accumulated day')">
                                {{ $totalPassengersBySensorTotal }}
                            </small>
                        </div>
                    </div>
                </td>
            @endif

            @if($company->hasSensorRecorderCounter())
                <td width="10%"
                    class="text-center">
                        <span class="tooltips"
                              data-title="@lang('Round trip')">
                            {{ $dispatchRegister->passengersBySensorRecorder }}
                        </span>
                    <hr class="m-0">
                    <small class="tooltips text-bold"
                           data-title="@lang('Accumulated day')">
                        {{ $totalPassengersBySensorRecorder }}
                    </small>
                </td>
            @endif
            @if($isExpresoPalmira && $user->canViewAverageCount() )
                <td width="5%" class="text-center">
                        <span>
                            {{$promPassengers}}
                        </span>
                </td>
            @endif
            @if($user->CanViewInfoPhotos())
                <td width="10%" class="text-center">
                    <div>
                        @php
                            $photos = $dispatchRegister->photos;
                            $photosByCamera = $photos->sortBy('side')->groupBy('side');

                            $vehicleCameras = \App\Models\Apps\Rocket\VehicleCamera::where('vehicle_id', $dispatchRegister->vehicle_id)
                                ->get()
                                ->pluck('camera');

                            $routeTimeInMinutes = \App\Http\Controllers\Utils\StrTime::toSeg($dispatchRegister->getRouteTime())/60;
                            $expectedTotalPhotos = intval(($routeTimeInMinutes) / 2 * $vehicleCameras->count());
                        @endphp

                        @foreach($vehicleCameras as $camera)
                            @php
                                $cameraPhotos = $photosByCamera->get($camera);
                                $totalPhotos = 0;
                                $photoStatus = "green";

                                $expectedPhotos = intval($routeTimeInMinutes / 2);

                                if($cameraPhotos) {
                                    $totalPhotos = $cameraPhotos->count();
                                    if($totalPhotos < $expectedPhotos * 0.5) $photoStatus = "warning";
                                    else $photoStatus = "green";
                                } else {
                                    $photoStatus = "red";
                                    $alertPhoto = true;
                                }
                            @endphp
                            <br>
                            <small class="badge bg-{{ $photoStatus }}">
                                {{ $camera }} <i class="fa fa-camera"></i> {!! $totalPhotos !!}
                            </small>
                        @endforeach
                        <div class="hide">
                            <small>{{ $photos->count() }} / {{ $expectedTotalPhotos }}</small>
                        </div>
                    </div>
                </td>
                @if ($alertPhoto)
                <!-- Modal -->
                    <div class="modal fade" id="alertPhotoModal" tabindex="-1" role="dialog"
                         aria-labelledby="labelModal"
                         aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content" style="border: 3px solid darkred;background: silver">
                                <div class="modal-header text-center">
                                    <h5 class="modal-title"
                                        style="font-weight: bold;font-size: 20px;font-family: Glyphicons-Halflings">
                                        <i class="fa fa-warning faa-pulse"></i> @lang('alert') <i
                                                class="fa fa-warning faa-pulse"></i>
                                    </h5>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-warning"
                                         style="font-weight: bold ; color: white; background: darkred; font-size: 18px">
                                        El vehículo número {{ $vehicle->number }} presenta una novedad en el envío de
                                        fotos.
                                        Por favor, comuníquese con soporte.
                                    </div>
                                </div>
                                <div class="modal-footer text-center">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif


            @if(Auth::user()->isSuperAdmin())
                @php
                    $countMax = $dispatchRegister->final_front_sensor_counter;
                    $sumatoriaCountMax += $countMax;
                @endphp
                <td class="text-center">
                    <small class="tooltips text-bold"
                           data-title="@lang('Conteo por maximos')">
                        {{$countMax}}
                    </small>
                </td>
            @endif
            @if($user->canViewAction())
                <td width="15%" class="text-center">
                    <a href="#modal-historic-report"
                       class="btn green-haze faa-parent animated-hover btn-show-historic-report btn-circle btn-outline tooltips"
                       data-toggle="modal"
                       data-url="{!! route('report-route-historic') !!}?{{ $dispatchRegister->getHistoricReportQueryParams() }}&hide-menu=true"
                       data-original-title="@lang('Historic report')">
                        <i class="fa fa-map faa-pulse"></i>
                    </a>

                    <div class="p-t-5 {{ Auth::user()->isSuperAdmin() || !$dispatchRegister->round_trip ? '' : 'hide' }}">
                        <div class="btn-group">
                            <a href="javascript:;" data-toggle="dropdown" class="btn btn-circle btn-outline dropdown-toggle btn-dropdown" aria-expanded="false">
                                <i class="fa fa-ellipsis-v"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-route-report" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 32px, 0px);">
                                @if(Auth::user()->isSuperAdmin())
                                    <li>
                                        <a id="btn-taking-{{ $dispatchRegister->id }}" href="#modal-takings-passengers" data-toggle="modal"
                                           onclick="showTakingsForm('{{ route("operation-routes-takings-form", ["dispatchRegister" => $dispatchRegister->id]) }}')">
                                            <span class="{{ $dispatchRegister->takings->isTaken() ? 'purple' : 'purple-sharp' }}">
                                                <i class="fa fa-dollar faa-vertical"></i> @lang('Takings')
                                            </span>
                                        </a>
                                    </li>
                                @endif

                                @if(Auth::user()->isSuperAdmin())
                                    <li>
                                        <a href="#modal-seating-profile" data-toggle="modal"
                                           onclick="loadSeatingProfile('{{ route('report-passengers-occupation-by-dispatch',['id'=>$dispatchRegister->id]) }}')">
                                            <i class="fa fa-users faa-pulse text-warning"></i> @lang('See profile seating report')
                                        </a>
                                    </li>
                                @endif
                                @if(Auth::user()->isSuperAdmin() || !$dispatchRegister->round_trip)
                                <li>
                                    <a class="text-danger tooltips edit-field-dr"
                                       data-confirm="@lang('Confirm action for discard dispatch turn')"
                                       data-url="{{ route('report-passengers-manage-update',['action'=>'cancelTurn']) }}"
                                       data-id="{{ $dispatchRegister->id }}">
                                        <span class="text-danger">
                                            <i class="fa fa-trash"></i> @lang('Cancel turn')
                                        </span>
                                    </a>
                                </li>
                                @endif
                                @if(Auth::user()->isSuperAdmin())
                                <li class="divider"></li>
                                <li>
                                    <a onclick="executeDAR({{ $dispatchRegister->id }})" class="{{ $dispatchRegister->process_ard ? 'text-warning' : 'text-success' }}">
                                        <i class="fa fa-cogs faa-pulse"></i> @lang('Execute DAR')
                                    </a>
                                </li>
                                <li>
                                    <a href="#modal-report-log" data-toggle="modal"
                                       onclick="$('#iframe-report-log').hide().attr('src','{{ route('report-route-get-log',['dispatchRegister' => $dispatchRegister->id]) }}').fadeIn()">
                                        <i class="fa fa-code faa-pulse"></i> @lang('Show report details')
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </td>
            @endif
        </tr>

        @php
            $lastArrivalTime[$vehicle->id] = $dispatchRegister->arrival_time;
        @endphp

        <script>
            @if($offRoadPercent)
            $('.icon-car-{{ $vehicle->id }}').removeClass('f-s-8').removeClass('fa-car').addClass('fa-random text-{{ $offRoadPercent < 50 ? 'warning': 'danger' }} faa-flash animated');
            @endif

                    @if($maxInvalidGPSPercent)
            if (parseFloat('{{ $maxInvalidGPSPercent }}') > 0) {
                $('.car-ss-percent-{{ $vehicle->id }}').removeClass('hide').addClass('text-{{ $maxInvalidGPSPercent < $thresholdAlertSS ? 'white': 'danger' }} faa-pulse animated');
            }
            @endif

                    @if($lowerGPSReport)
            if (parseFloat('{{ $lowerGPSReport }}') > 1) {
                $('.car-nr-{{ $vehicle->id }}').removeClass('hide').addClass('text-{{ $lowerGPSReport < $thresholdAlertNR ? 'white': 'danger' }}');
            }
            @endif
        </script>

    @endforeach
    @if($dispatchRegisters->count() && Auth::user()->isSuperAdmin())
        <tr>
            <td colspan="7">

            </td>
            <td class="text-center tooltips" data-title="@lang('Sumatoria pasajeros planilla FICS')">
                {{ $sumByCountSpreadSheetFICS }}
            </td>
            <td class="text-center tooltips"
                data-title="@lang('Sumatoria pasajeros planilla')">
                {{--                {{ $strTime::segToStrTime($strTime::toSeg($averageRouteTime)/$dispatchRegisters->count()) }}--}}
                {{ $sumByCountManual }}
            </td>
            <td colspan="1">

            </td>
            <td class="text-center tooltips" data-title="@lang('Sumatoria conteo Manual')">
                {{ $sumByCountPassengerVisual }}
            </td>
            <td class="text-center tooltips" data-title="@lang('Sumatoria conteo sistema')">
                {{$sumByCountSensor}}
            </td>

            @if($dispatchRegister->final_sensor_counter <= $spreadsheetPassengers->value)
                <td class="text-center tooltips" data-title="@lang('Sumatoria Conteo Planilla')">
                    {{ $sumByCountSpreadSheet }}
                </td>
            @else
                <td class="text-center tooltips" data-title="@lang('Sumatoria conteo sistema')">
                    {{$sumByCountSensor}}
                </td>
            @endif
            <td colspan="2"></td>
            <td class="text-center tooltips"
                data-title="@lang('Sumatoria Maximos')">
                {{$sumatoriaCountMax}}
            </td>
        </tr>
    @endif
    </tbody>
</table>
<!-- end table -->
<script type="application/javascript">

    @if( Auth::user()->belongsToCootransol() )
    let modalExecuteDAR = $('#modal-execute-DAR');


    function executeDAR(dispatchRegisterId) {
        modalExecuteDAR.modal('show');
        modalExecuteDAR.find('pre').html('@lang('This process can take several minutes')...');
        $.ajax({
            url: '{{ route('route-ajax-action') }}',
            data: {
                option: 'executeDAR',
                dispatchRegisterId: dispatchRegisterId
            },
            dataType: 'json',
            timeout: 0,
            success: function (data) {
                if (data.success) {
                    modalExecuteDAR.find('pre').html(data.infoProcess.totalNewReports + ' @lang('locations have been processed')<br>@lang('Detected route'): ' + data.infoProcess.routeName);
                    gsuccess('@lang('Process executed successfully')');
                } else {
                    let message = '@lang('An error occurred in the process. Contact your administrator')';
                    gerror(message);
                    modalExecuteDAR.find('pre').html(message + '<hr>Data: ' + JSON.stringify(data));
                }
            },
            error: function () {
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
    function confirmacion() {
        let respuesta = confirm("El numero de planilla es el correcto")
        if (respuesta == true) {
            return true
        } else {
            return false
        }
    }

    $('.html-observations').each(function (i, el) {
        const content = $(el).find('a').data('content');
        $(el).html($(el).text() + (content && content !== undefined ? '<br><span>' + content + '</span>' : ''))
    });
        @if($dispatchRegister->complete()){
        $(document).ready(function () {
                    @if ($alertPhoto)
                    $('#alertPhotoModal').modal('show');
                    @endif
            }
        );
    }
    @endif
    document.addEventListener("DOMContentLoaded", function () {
        // Agrega el evento de clic a cada botón de búsqueda
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".search-button").forEach(function (button, index) {
                button.addEventListener("click", function () {
                    var searchInput = document.querySelectorAll(".search-input")[index];
                    var resultTextarea = document.querySelectorAll(".result-textarea")[index];

                    var searchTerm = searchInput.value.toLowerCase();

                    var records = JSON.parse(resultTextarea.getAttribute("data-records"));

                    var filteredRecords = records.filter(function (record) {
                        return record.toLowerCase().includes(searchTerm);
                    });

                    resultTextarea.value = filteredRecords.join("\n");
                });
            });
        })
    });
</script>

<style>
    #modal-takings-passengers button.close {
        margin: 10px !important;
    }

    .label-lime {
        background: #74a400;
    }

    .label-lg {
        font-size: 1.4rem !important;
    }

    .box-edit {
        position: relative;
        width: 100%;
    }

    .box-obs {
        text-align: center;
        position: absolute;
        z-index: 1000;
        background: #e7e5e5;
        border-radius: 10px;
        padding-bottom: 5px;
        border-bottom: 4px solid rgb(143, 146, 149);
    }

    .box-audit {
        white-space: nowrap;
        padding: 0 20px 0 20px;
    }

    .html-observations.cancelled {
        display: block;
        background: #0a2a34;
        color: white;
        padding: 2px;
        font-family: Consolas, monaco, monospace;
        border-radius: 3px;
    }

    .row-turn-cancelled td {
        opacity: 50%;
        background: rgba(239, 203, 155, 0.48) !important;
    }

    .text-xs {
        font-size: 0.9rem !important;
    }

    .text-danger {
        color: #c03c38 !important;
    }

    .dropdown-menu-route-report {
        top: -38px !important;
        left: -180px !important;
        transform: translate3d(0px, 40px, 0px) !important;
    }

    .btn-dropdown {
        width: 48px !important;
    }
</style>
