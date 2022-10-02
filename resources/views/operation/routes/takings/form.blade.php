@php
    $company = $dispatchRegister->route->company;
    $processTakings = $dispatchRegister->vehicle->process_takings;

    $passengersAccumulated = $dispatchRegister->getPassengersAccumulated();
    $turnsToTaken = $passengersAccumulated->noTaken;

    $passengersTakings = $dispatchRegister->getPassengersTakings();

    $takingsParent = $dispatchRegister->takings->parent;
    
    $canTakingsByAll = !$takingsParent && $company->canTakingsByAll();

    $totalProduction = $company->hasTakingsWithMultitariff() ? $occupationReport->totalProduction : intval($dispatchRegister->takings->total_production);
@endphp

<div class="row form-takings">
    <div class="col-md-12">
        <div class="well row m-b-0">
            <div class="col-md-12 p-0">
                <h4 class="pull-left m-0">
                    <i class="fa fa-bus"></i> <strong>@lang('Vehicle'):</strong>
                    <span>{{ $dispatchRegister->vehicle->number }}</span>
                </h4>
                <h4 class="pull-right m-0">
                    <i class="fa fa-calendar"></i>
                    <span>{{ $dispatchRegister->date }}</span>
                </h4>
            </div>
            @if(!$dispatchRegister->onlyControlTakings() && $dispatchRegister->complete() && !$company->canManualTakings())
                <div class="col-md-12 no-padding m-t-10 form-takings-type">
                    <div class="form-takings_card form-takings_card_passengers form-takings_card_passengers__activated" data-type="{{ \App\Models\Routes\RouteTaking::TAKING_BY_ROUND_TRIP }}"
                         data-passengers-recorders="{{ $dispatchRegister->passengers->recorders->count }}"
                         data-passengers-sensor="{{ $dispatchRegister->passengers->sensor->count }}"
                         data-passengers-takings="{{ $dispatchRegister->passengers->takings->count }}"
                    >
                        <h5 class="form-takings_title">
                            <i class="icon-directions"></i> <strong>@lang('Round trip'):</strong>
                            <span>{{ $dispatchRegister->round_trip }}</span>
                        </h5>

                        @if($company->id == $company->hasRecorderCounter())
                            <h5>
                                <i class="icon-users"></i> <strong>@lang('Passengers') @lang('recorders'):</strong>
                                <span>{{ $dispatchRegister->passengers->recorders->count }}</span>
                            </h5>
                            <h5>
                                <i class="icon-compass"></i> <strong>@lang('Recorders'):</strong>
                                <span>{{ $dispatchRegister->passengers->recorders->start }} - {{ $dispatchRegister->passengers->recorders->end }}</span>
                            </h5>
                        @endif
                        <hr>
                        @if($company->id == $company->hasSensorCounter())
                            <h5>
                                <i class="icon-users"></i> <strong>@lang('Passengers') @lang('sensor'):</strong>
                                <span>{{ $dispatchRegister->passengers->sensor->count }}</span>
                            </h5>
                            <h5>
                                <i class="fa fa-crosshairs"></i> <strong>@lang('Sensor'):</strong>
                                <span>{{ $dispatchRegister->passengers->sensor->start }} - {{ $dispatchRegister->passengers->sensor->end }}</span>
                            </h5>
                        @endif
                    </div>
                    @if($canTakingsByAll)
                        <div class="form-takings_card form-takings_card_passengers" data-type="{{ \App\Models\Routes\RouteTaking::TAKING_BY_ALL }}"
                             data-passengers-recorders="{{ $passengersAccumulated->recorders }}"
                             data-passengers-sensor="{{ $passengersAccumulated->sensor }}"
                             data-passengers-takings="{{ $passengersAccumulated->takings }}"
                        >
                            <h5 class="form-takings_title">
                                <i class="fa fa-calendar-o"></i> <strong>@lang('Accumulated day'):</strong>
                                <br>
                                <small>@lang('Excludes already takings')</small>
                            </h5>
                            <hr>
                            @if($company->id == $company->hasRecorderCounter())
                                <h5>
                                    <i class="icon-users"></i> <strong>@lang('Total') @lang('recorders'):</strong>
                                    <span>{{ $passengersAccumulated->recorders }}</span>
                                </h5>
                            @endif
                            @if($company->id == $company->hasSensorCounter())
                                <h5>
                                    <i class="icon-users"></i> <strong>@lang('Total') @lang('sensor'):</strong>
                                    <span>{{ $passengersAccumulated->sensor }}</span>
                                </h5>
                            @endif

                            @if(count($turnsToTaken))
                                <div>
                                    <hr>
                                    <h5 class="m-0 text-bold">
                                        <i class="icon-briefcase faa-ring" style="margin-right: 0; margin-left: 0px"></i> @lang('Will taken turns')
                                    </h5>
                                    <ul>
                                        @foreach($turnsToTaken as $turnToTaken)
                                            <li>{{ $turnToTaken->routeName }} ➜ @lang('Round trip') {{ $turnToTaken->roundTrip }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
                <div class="col-md-12 m-t-10 p-0">
                    <h5 class="pull-left m-b-0">
                        <i class="fa fa-dollar"></i><i class="fa fa-user"></i><strong>@lang('Passenger tariff'):</strong>
                        <span>${{ number_format($dispatchRegister->takings->passenger_tariff, 0) }}</span>
                    </h5>
                    <h5 class="pull-right m-b-0">
                        <i class="fa fa-dollar"></i><i class="fa fa-flask"></i><strong>@lang('Fuel tariff'):</strong>
                        <span>${{ number_format($dispatchRegister->takings->fuel_tariff, 1) }}</span>
                    </h5>
                </div>
            @endif
        </div>
    </div>

    @if($dispatchRegister->takings->passenger_tariff <= 0 && $dispatchRegister->route && !$dispatchRegister->route->getControlPointsTariff()->count())
        <div class="bg-red text-white p-10 text-center" style="display: flow-root">
            <i class="fa fa-warning faa-passing animated"></i> <strong>@lang('Invalid passenger tariff')</strong>
            <div>
                <small>@lang('Please verify passenger tariff for route :route', ['route' => $dispatchRegister->route->name])</small>
            </div>
        </div>
    @endif

    @if($passengersTakings->count < 0)
        <div class="bg-red text-white p-10 text-center" style="display: flow-root">
            <i class="fa fa-warning faa-passing animated"></i> <strong>@lang('Invalid count')</strong>
            <div>
                <small>@lang('Please verify recorder or sensor values')</small>
            </div>
        </div>
    @endif

    @if(!$processTakings)
    <div class="bg-red text-white p-10 text-center" style="display: flow-root">
        <strong>@lang('Excluded for takings')</strong>
        <br><br>
        <i class="fa fa-lock faa-ring fa-2x animated"></i>
    </div>
    @endif

    @if(!$dispatchRegister->complete() && !$dispatchRegister->onlyControlTakings())
        <div class="bg-warning text-white p-10 text-center" style="display: flow-root">
            <i class="fa fa-bus faa-passing animated"></i> <strong>@lang('Turn not completed')</strong>
        </div>
    @endif

    @if($takingsParent)
        <div class="bg-green-dark text-white p-10 text-center" style="display: flow-root">
            <i class="fa fa-exclamation-circle faa-ring animated"></i> <strong>{{ $dispatchRegister->takings->observations }}</strong>
        </div>
    @elseif($dispatchRegister->complete() || $dispatchRegister->onlyControlTakings())
        <div class="col-md-8 col-md-offset-2 p-t-15">
            <form class="form-horizontal form-taking-passengers" role="form" action="{{ route('operation-routes-takings-taking', ['dispatchRegister' => $dispatchRegister->id]) }}" data-dr="{{ $dispatchRegister->id }}">
                <input type="hidden" id="tariff_passenger" value="{{ intval($dispatchRegister->takings->passenger_tariff) }}" class="form-control">

                <input type="hidden" id="form_takings_type" name="type" value="{{ \App\Models\Routes\RouteTaking::TAKING_BY_ROUND_TRIP }}"/>
                <input type="hidden" id="form_takings_delete" name="delete" value=""/>
                {{ csrf_field() }}

                @if($company->canManualTakings())
                    <div class="divider m-b-0">
                        <div>@lang('Manual count information')</div>
                    </div>
                    <div class="form-group has-info">
                        <label for="total-production" class="col-md-5 control-label text-info">@lang('Manual total passengers')</label>
                        <div class="col-md-7">
                            <div class="input-icon">
                                <i class="fa fa-dollar"></i>
                                <input type="number" id="manual_total_passengers" class="form-control input-circle-right disabled" name="manual_total_passengers" value="{{ intval($dispatchRegister->takings->manual_total_passengers) }}">
                            </div>
                        </div>
                    </div>
                    <div class="form-group has-info">
                        <label for="total-production" class="col-md-5 control-label text-info">@lang('Manual total production')</label>
                        <div class="col-md-7">
                            <div class="input-icon">
                                <i class="fa fa-dollar"></i>
                                <input type="number" class="form-control input-circle-right disabled" id="manual_total_production" name="manual_total_production" value="{{ intval($dispatchRegister->takings->manual_total_production) }}">
                            </div>
                        </div>
                    </div>
                @endif
                <div class="divider m-t-40 m-b-0">
                    <div>@lang('System count information')</div>
                </div>
                <div class="m-b-15">
                    <div class="takings_card_passengers_counter">
                        <div class="form-group m-0">
                            <div class="radio-list">
                                @foreach($dispatchRegister->getTypeCounters() as $typeCounter)
                                    <label class="radio-inline takings_counter-select well">
                                        <span>
                                            <input type="radio" name="counter" id="takings-counter-{{ $typeCounter->name }}" value="{{ $typeCounter->name }}" {{ $loop->first ? 'checked' : '' }}>
                                            {{ __(ucfirst($typeCounter->name)) }} <i class="{{ $typeCounter->icon }}"></i>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="total-production" class="col-md-5 control-label">@lang('Total passengers')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="fa fa-dollar"></i>
                            <input type="number" id="total_passengers" disabled class="form-control input-circle-right disabled" name="total_passengers" value="{{ intval($passengersTakings->count) }}">
                        </div>
                    </div>
                </div>
                <div class="form-group m-b-5">
                    <label for="total-production" class="col-md-5 control-label">@lang('Total production')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="fa fa-dollar"></i>
                            <input type="number" readonly class="form-control input-circle-right disabled" id="total_production_takings" name="total_production" value="{{ $totalProduction }}">
                        </div>
                    </div>
                </div>
                <div class="divider m-t-40 m-b-0">
                    <div>@lang('Discounts')</div>
                </div>
                <div class="form-group has-warning">
                    <label for="control" class="col-md-5 control-label">{{ $company->getTakingsLabel('control') }}</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="icon-bag"></i>
                            <input type="number" class="form-control input-circle-right currency" id="control_takings" name="control" value="{{ $dispatchRegister->takings->control }}" {{ $processTakings ? '' : 'disabled' }} >
                        </div>
                    </div>
                </div>

                <div class="form-group m-b-5 has-warning">
                    <label for="fuel" class="col-md-5 control-label">@lang('Fuel')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="icon-fire"></i>
                            <input type="number" class="form-control input-circle-right" id="fuel_takings" name="fuel" value="{{ $dispatchRegister->takings->fuel }}" {{ $processTakings ? '' : 'disabled' }} >
                        </div>
                    </div>
                </div>
                <div class="form-group m-b-5">
                    <label for="fuel" class="col-md-5 control-label">@lang('Fuel gallons')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="fa fa-flask"></i>
                            <input type="number" disabled step="0.1" class="form-control input-circle-right disabled" id="fuel_gallons_takings" name="fuel_gallons" value="{{ number_format($dispatchRegister->takings->fuel_gallons, 2) }}">
                        </div>
                    </div>
                </div>
                @if(!$dispatchRegister->onlyControlTakings())
                <div class="form-group m-b-0">
                    <label for="fuel" class="col-md-5 control-label">@lang('Station')</label>
                    <div class="col-md-7">
                        <div class="md-radio-list">
                            @foreach($fuelStations as $station)
                                @php
                                    $checked = ($dispatchRegister->takings->fuel_station_id == $station->id) || ($loop->first && !$dispatchRegister->takings->fuel_station_id);
                                @endphp

                                <div class="md-radio m-b-0">
                                    <input type="radio" id="fuel_station__{{ $station->id }}" name="fuel_station_id" {{ $checked ? 'checked' : '' }} class="md-radiobtn" value="{{ $station->id }}" {{ $processTakings ? '' : 'disabled' }} >
                                    <label for="fuel_station__{{ $station->id }}">
                                        <span class="inc"></span>
                                        <span class="check"></span>
                                        <span class="box"></span> {{ $station->name }} </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <div class="form-group m-b-5 has-warning">
                    <label for="others" class="col-md-5 control-label">{{ $company->getTakingsLabel('bonus') }}</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="icon-badge"></i>
                            <input type="number" class="form-control input-circle-right" id="bonus_takings" name="bonus" value="{{ $dispatchRegister->takings->bonus }}" {{ $processTakings ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>

                <div class="form-group m-b-5 has-warning">
                    <label for="others" class="col-md-5 control-label">@lang('Others')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="icon-cup"></i>
                            <input type="number" class="form-control input-circle-right" id="others_takings" name="others" value="{{ $dispatchRegister->takings->others }}" {{ $processTakings ? '' : 'disabled' }}>
                        </div>
                    </div>
                </div>

                <div class="divider m-t-40 m-b-0">
                    <div>@lang('Totales')</div>
                </div>

                <div class="form-group has-success">
                    <label for="net_production" class="col-md-5 control-label">@lang('Net production')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="fa fa-dollar"></i>
                            <input type="number" disabled class="form-control input-circle-right disabled" id="net_production_takings" name="net_production" value="{{ $dispatchRegister->takings->net_production }}">
                        </div>
                    </div>
                </div>

                <div class="form-group has-info m-b-5">
                    <label for="advance" class="col-md-5 control-label text-primary">@lang('Advance')</label>
                    <div class="col-md-7">
                        <div class="input-icon input-group tooltips" title="@lang('Taken passengers')" data-placement="right">
                            <i class="fa fa-dollar"></i>
                            <input type="number" class="form-control disabled tooltips" id="advance_takings" name="advance" value="{{ $dispatchRegister->takings->advance }}" {{ $processTakings ? '' : 'disabled' }} data-title="@lang('Please insert value for takings')">
                            <span class="input-group-addon">
                                <i class="icon-users"></i> <span id="passengers_taken">{{ $dispatchRegister->takings->getPassengersTaken() }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group has-success m-b-5">
                    <label for="balance" class="col-md-5 control-label text-uppercase">@lang('Balance')</label>
                    <div class="col-md-7">
                        <div class="input-icon input-group">
                            <i class="fa fa-dollar"></i>
                            <input type="number" disabled class="form-control disabled" id="balance_takings" name="balance" value="{{ $dispatchRegister->takings->balance }}" {{ $processTakings ? '' : 'disabled' }}>
                            <span class="input-group-addon">
                                <i class="icon-users" style="color: #0b4d3f"></i> <span id="passengers_balance">{{ $dispatchRegister->takings->passengersBalance }}</span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="form-group m-t-20 m-t-0">
                    <label for="observations" class="col-md-5 control-label">@lang('Observations')</label>
                    <div class="col-md-7">
                        <div class="input-icon">
                            <i class="icon-note"></i>
                            <textarea style="resize: vertical;min-height: 40px;max-height: 300px" maxlength="500" class="form-control input-circle-right" name="observations">{{ $dispatchRegister->takings->observations }}</textarea>
                        </div>
                    </div>
                </div>

                @if($processTakings)
                    <hr>
                    <div class="form-group">
                        <div class="col-md-offset-{{ $dispatchRegister->takings->isTaken() ? '3':'4' }} col-md-7">
                            <button type="submit" class="btn green btn-outline btn-circle">
                                <i class="fa fa-save"></i>@lang('Save')
                            </button>
                            @if($dispatchRegister->takings->isTaken())
                                <button type="button" class="btn red btn-outline btn-circle" onclick="deleteTakings()">
                                    <i class="fa fa-trash"></i>@lang('Delete')
                                </button>
                            @endif
                        </div>
                    </div>
                @endif
            </form>
            <form class="form-horizontal form-taking-passengers-delete" role="form" action="{{ route('operation-routes-takings-delete', ['dispatchRegister' => $dispatchRegister->id]) }}">
                {{ csrf_field() }}
            </form>
        </div>
    @endif

    @if($dispatchRegister->takings->isTaken())
        <div class="col-md-12 p-0">
            <div class="well row m-0">
                <h6 class="text-purple purple">
                    <i class="fa fa-calendar"></i> <strong>@lang('Taken at'):</strong>
                    <span>{{ $dispatchRegister->takings->created_at }}</span>
                </h6>

                @if($dispatchRegister->takings->created_at != $dispatchRegister->takings->updated_at)
                    <h6 class="text-warning orange">
                        <i class="fa fa-calendar"></i> <strong>@lang('Updated at'):</strong>
                        <span>{{ $dispatchRegister->takings->updated_at }}</span>
                    </h6>
                @endif
                @if($dispatchRegister->takings->user)
                    <h6 class="text-info orange">
                        <i class="fa fa-user"></i> <strong>@lang('User'):</strong>
                        <span>{{ $dispatchRegister->takings->user->username }} ({{ $dispatchRegister->takings->user->name }})</span>
                    </h6>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
    let text = $('#observations');
    text.on('change drop keydown cut paste', function () {
        text.height('auto');
        text.height(text.prop('scrollHeight'));
    });

    let modalTakingsPassengers = $('#modal-takings-passengers');
    let formTakingsPassengers = $('.form-taking-passengers');
    let formTakingsPassengersDelete = $('.form-taking-passengers-delete');

    let formTakingsCard = $('.form-takings_card_passengers');
    let counterTakings = $('input[name="counter"]');
    let takingsType = $('#form_takings_type');
    let passengersTakings = $('#total_passengers');
    let tariffTakings = $('#tariff_passenger');

    formTakingsPassengers.submit(function (event) {
        $('#net_production').removeAttr('disabled');
        event.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function (data) {
                if (data.success) {
                    gsuccess(data.message);
                    modalTakingsPassengers.modal('hide');

                    if (data.taken) {
                        $('#btn-taking-' + form.data('dr')).removeClass('btn-outline').removeClass('purple-sharp').addClass('purple');
                    }

                    refreshReport();
                } else {
                    gerror(data.message);
                }
            },
            error: function () {
                gerror('@lang('An error occurred in the process. Contact your administrator')');
            },
            complete: function () {

            }
        });
    });

    formTakingsPassengersDelete.submit(function (event) {
        event.preventDefault();
        let form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'DELETE',
            data: form.serialize(),
            success: function (data) {
                if (data.success) {
                    gwarning(data.message);
                    modalTakingsPassengers.modal('hide');
                    $('#btn-taking-' + form.data('dr')).addClass('btn-outline').addClass('purple-sharp').removeClass('purple');

                    refreshReport();
                } else {
                    gerror(data.message);
                }
            },
            error: function () {
                gerror('@lang('An error occurred in the process. Contact your administrator')');
            },
            complete: function () {

            }
        });
    });

    formTakingsPassengers.find('input.form-control').change(function () {
        const passengersTaken = getPassengersTaken();
        if(getCounter() === 'takings') {
            // $('.form-takings_card').slideUp();
            setPassengersTakings(passengersTaken, true);
        } else {
            // $('.form-takings_card').slideDown();
        }

        const tariffPassenger = parseInt(tariffTakings.val());
        const totalPassengers = parseInt(passengersTakings.val());

        let totalProduction = 0;
        @if($company->hasTakingsWithMultitariff())
            totalProduction = {{ intval($totalProduction) }};
        @else
            totalProduction = tariffPassenger * totalPassengers;
        @endif

        const totalDiscounts = getTotalDiscounts();
        const netProduction = totalProduction - totalDiscounts;
        
        const advance = getAdvance();


        const balance = netProduction - advance;
        const passengersBalance = totalPassengers - passengersTaken;

        const fuelGallons = getFuelGallons();

        formTakingsPassengers.find('#total_production_takings').val(totalProduction);
        formTakingsPassengers.find('#fuel_gallons_takings').val(fuelGallons.toFixed(2));
        formTakingsPassengers.find('#net_production_takings').val(netProduction);
        formTakingsPassengers.find('#passengers_taken').text(passengersTaken.toFixed(1));
        formTakingsPassengers.find('#balance_takings').val(balance);
        formTakingsPassengers.find('#passengers_balance').text(passengersBalance.toFixed(1));
    });

    function getFuelGallons() {
        const fuel = parseInt(formTakingsPassengers.find('#fuel_takings').val()) || 0;
        const fuelTariff = parseFloat({{ $dispatchRegister->takings->fuel_tariff }}) || 0;
        
        return fuelTariff > 0 && fuel > 0 ? fuel / fuelTariff : 0;
    }

    function getTotalDiscounts() {
        const control = parseInt(formTakingsPassengers.find('#control_takings').val()) || 0;
        const others = parseInt(formTakingsPassengers.find('#others_takings').val()) || 0;
        const bonus = parseInt(formTakingsPassengers.find('#bonus_takings').val()) || 0;
        const fuel = parseInt(formTakingsPassengers.find('#fuel_takings').val()) || 0;

        return parseInt(control + fuel + bonus + others) || 0;
    }
    
    function getAdvance() {
        return parseInt(formTakingsPassengers.find('#advance_takings').val()) || 0;
    }

    function getPassengersTaken() {
        const tariffPassenger = parseInt(tariffTakings.val());
        const totalDiscounts = getTotalDiscounts();

        const advance = getAdvance();
        return tariffPassenger ? (advance + totalDiscounts) / tariffPassenger : 0;
    }

    formTakingsPassengers.find('input.form-control').keyup(function () {
        $(this).change();
    });

    formTakingsCard.click(function () {
        formTakingsCard.removeClass('form-takings_card_passengers__activated');
        $(this).addClass('form-takings_card_passengers__activated');

        setTakingsType($(this).data('type'));

        const passengers = getPassengersTakings();
        setPassengersTakings(passengers);
    });

    counterTakings.change(function () {
        const passengers = getPassengersTakings();
        setPassengersTakings(passengers);
    });

    function setPassengersTakings(passengers, preventEvent) {
        passengersTakings.val(passengers);
        if(!preventEvent) passengersTakings.change();
    }
    function getPassengersTakings() {
        const counter = getCounter();
        const type = getTakingsType();

        return $(`.form-takings_card_passengers[data-type=${type}]`).data(`passengers-${counter}`);
    }

    function setTakingsType(type) {
        takingsType.val(type);
    }
    function getTakingsType() {
        return takingsType.val();
    }

    function setCounter(counter) {
        $('#takings-counter-' + counter ).click();
    }
    function getCounter() {
        return counterTakings.filter(':checked').val();
    }

    function deleteTakings() {
        formTakingsPassengersDelete.submit();
    }

    function refreshReport() {
        setTimeout(() => {
            $('.btn-search-report').click();
        }, 500);
    }

    function initCounter(counter){
        setCounter(counter);
    }

    function initTakingstype(type) {
        return $(`.form-takings_card_passengers[data-type=${type}]`).click();
    }

    initCounter('{{ $dispatchRegister->takings->counter }}');
    initTakingstype('{{ $dispatchRegister->takings->type }}');

    $('.tooltips').tooltip();
    setTimeout(() => {
        $('#advance_takings').change();
    }, 100);
</script>

<style>
    .form-takings-type {
        display: grid;
        grid-gap: 10px;
        grid-template-columns: repeat(2, 1fr);
    }

    .form-takings_card {
        padding: 0;
    }

    .form-takings_card_passengers {
        border: 2px solid #eceaea;
        border-radius: 10px;
        height: 100%;
        padding: 15px;
        cursor: pointer;
    }

    .form-takings_card_passengers:hover {
        background: #ebf6eb;
        border: 2px solid #74ba55;
    }

    .form-takings_card_passengers__activated {
        background: #ebf6eb;
        border: 2px solid #194903 !important;
    }

    .form-takings_title {
        margin: 0 0 20px;
    }

    .value-recorders {
        font-size: 0.8em !important;
    }
    .form-taking-passengers input[type=number]::-webkit-inner-spin-button,
    .form-taking-passengers input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .takings_counter-select {
        padding: 2px 8px 0 4px !important;
        border: 1px solid lightgray;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        margin: 0;
    }

    .takings_counter-select:hover {
        border: 1px solid #b4b4b4;
    }

    .takings_card_passengers_counter {
        text-align: center;
    }

    .checkbox-inline+.checkbox-inline, .radio-inline+.radio-inline {
        margin-left: 2px;
        margin-bottom: 15px;
    }

    .form-control[disabled], .form-control[readonly], fieldset[disabled] .form-control {
        background-color: #e6fdff;
    }

    div.divider {
        border-top: 2px solid #d3d9d9;
        text-align: center;
        margin-top: 10px;
        margin-bottom: 10px;
    }

    div.divider div {
        background: white;
        width: fit-content;
        margin: auto;
        top: -14px;
        position: relative;
        padding: 3px 15px;
        box-shadow: -1px 3px 6px 1px #e6e6e7;
        border-radius: 50px;
        color: #5c5c5c;
    }
</style>

