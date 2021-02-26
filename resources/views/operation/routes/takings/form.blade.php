@php
    $company = $dispatchRegister->route->company
@endphp

<div class="row">
    <div class="col-md-12">
        <div class="well row m-b-0">
            <div class="col-md-12 row">
                <h4 class="pull-left m-0">
                    <i class="fa fa-bus"></i> <strong>@lang('Vehicle'):</strong>
                    <span>{{ $dispatchRegister->vehicle->number }}</span>
                </h4>
                <h4 class="pull-right m-0">
                    <i class="fa fa-calendar"></i>
                    <span>{{ $dispatchRegister->date }}</span>
                </h4>
            </div>
            @if(!$dispatchRegister->onlyControlTakings())
                <div class="col-md-12">
                    <h5>
                        <i class="icon-directions"></i> <strong>@lang('Round trip'):</strong>
                        <span>{{ $dispatchRegister->round_trip }}</span>
                    </h5>
                    @if($company->id == $company->hasRecorderCounter())
                        <h5>
                            <i class="fa fa-users"></i> <strong>@lang('Total') @lang('passengers'):</strong>
                            <span>{{ $dispatchRegister->passengers->recorders->count }}</span>
                        </h5>
                    @else
                        <h5>
                            <i class="fa fa-users"></i> <strong>@lang('Total') @lang('passengers'):</strong>
                            <span>{{ $dispatchRegister->passengers->sensor->count }}</span>
                        </h5>
                    @endif
                    @if($company->id == $company->hasRecorderCounter())
                        <h5>
                            <i class="icon-compass"></i> <strong>@lang('Recorders'):</strong>
                            <span>{{ $dispatchRegister->passengers->recorders->start }} - {{ $dispatchRegister->passengers->recorders->end }}</span>
                        </h5>
                    @endif
                    <div style="border-top: 1px solid lightgray;" class="m=t-10 p-t-10">
                        <h5 class="pull-left">
                            <i class="fa fa-dollar"></i><i class="fa fa-user"></i><strong>@lang('Passenger tariff'):</strong>
                            <span>${{ number_format($dispatchRegister->takings->passenger_tariff, 0) }}</span>
                        </h5>
                        <h5 class="pull-right">
                            <i class="fa fa-dollar"></i><i class="fa fa-flask"></i><strong>@lang('Fuel tariff'):</strong>
                            <span>${{ number_format($dispatchRegister->takings->fuel_tariff, 1) }}</span>
                        </h5>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if(!$dispatchRegister->complete())
        <div class="bg-warning text-white p-10 text-center" style="display: flow-root">
            <i class="fa fa-bus faa-passing animated"></i> <strong>@lang('Turno no completado')</strong>
        </div>
    @endif

    <div class="col-md-8 col-md-offset-2 p-t-15">
        <form class="form-horizontal form-taking-passengers" role="form"
              action="{{ route('operation-routes-takings-taking', ['dispatchRegister' => $dispatchRegister->id]) }}"
              data-dr="{{ $dispatchRegister->id }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="total-production" class="col-md-5 control-label">@lang('Total production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" disabled class="form-control input-circle-right disabled" id="total_production_takings" name="total_production" value="{{ $dispatchRegister->takings->total_production }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="control" class="col-md-5 control-label">@lang('Control')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-bag"></i>
                        <input type="number" class="form-control input-circle-right currency" id="control_takings" name="control" value="{{ $dispatchRegister->takings->control }}">
                    </div>
                </div>
            </div>

            <hr class="hr no-padding">

            <div class="form-group">
                <label for="fuel" class="col-md-5 control-label">@lang('Fuel gallons')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-flask"></i>
                        <input type="number" disabled step="0.1" class="form-control input-circle-right disabled" id="fuel_gallons_takings" name="fuel_gallons" value="{{ number_format($dispatchRegister->takings->fuel_gallons, 2) }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="fuel" class="col-md-5 control-label">@lang('Fuel')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-fire"></i>
                        <input type="number" class="form-control input-circle-right" id="fuel_takings" name="fuel" value="{{ $dispatchRegister->takings->fuel }}">
                    </div>
                </div>
            </div>
            @if(!$dispatchRegister->onlyControlTakings())
            <div class="form-group m-b-0">
                <label for="fuel" class="col-md-5 control-label">@lang('Station')</label>
                <div class="col-md-7">
                    <div class="md-radio-list">
                        @foreach($fuelStations as $stationId => $station)
                            @php
                                $checked = ($dispatchRegister->takings->station_fuel_id == $stationId) || ($loop->first && !$dispatchRegister->takings->station_fuel_id);
                            @endphp

                            <div class="md-radio">
                                <input type="radio" id="station_fuel_{{ $stationId }}" name="station_fuel_id" {{ $checked ? 'checked' : '' }} class="md-radiobtn" value="{{ $stationId }}">
                                <label for="station_fuel_{{ $stationId }}">
                                    <span class="inc"></span>
                                    <span class="check"></span>
                                    <span class="box"></span> {{ $station }} </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <hr class="hr no-padding">

            <div class="form-group">
                <label for="others" class="col-md-5 control-label">@lang('Various')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-badge"></i>
                        <input type="number" class="form-control input-circle-right" id="bonus_takings" name="bonus" value="{{ $dispatchRegister->takings->bonus }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="others" class="col-md-5 control-label">@lang('Others')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-cup"></i>
                        <input type="number" class="form-control input-circle-right" id="others_takings" name="others" value="{{ $dispatchRegister->takings->others }}">
                    </div>
                </div>
            </div>

            <hr class="hr no-padding">

            <div class="form-group has-success m-t-30 m-b-30">
                <label for="net-production" class="col-md-5 control-label">@lang('Net production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" disabled class="form-control input-circle-right disabled" id="net_production_takings" name="net_production" value="{{ $dispatchRegister->takings->net_production }}">
                    </div>
                </div>
            </div>

            <hr class="hr no-padding">

            <div class="form-group has-info">
                <label for="net-production" class="col-md-5 control-label text-primary">@lang('Advance')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" class="form-control input-circle-right disabled" id="advance_takings" name="advance" value="{{ $dispatchRegister->takings->advance }}">
                    </div>
                </div>
            </div>

            <div class="form-group has-success">
                <label for="net-production" class="col-md-5 control-label text-uppercase">@lang('Balance')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" disabled class="form-control input-circle-right disabled" id="balance_takings" name="balance" value="{{ $dispatchRegister->takings->balance }}">
                    </div>
                </div>
            </div>


            <hr class="hr no-padding">

            <div class="form-group m-t-30 m-b-30">
                <label for="observations" class="col-md-5 control-label">@lang('Observations')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-note"></i>
                        <textarea style="resize: vertical;min-height: 40px;max-height: 300px" maxlength="500"
                                  class="form-control input-circle-right"
                                  name="observations">{{ $dispatchRegister->takings->observations }}</textarea>
                    </div>
                </div>
            </div>


            <hr>
            <div class="form-group">
                <div class="col-md-offset-5 col-md-7">
                    <button type="submit" class="btn green btn-outline btn-circle">
                        <i class=""></i>@lang('Save')
                    </button>
                </div>
            </div>
        </form>
    </div>

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
    // $("#control_takings").inputmask('999,999', {
    //     numericInput: true
    // });

    let text = $('#observations');
    text.on('change drop keydown cut paste', function () {
        text.height('auto');
        text.height(text.prop('scrollHeight'));
    });

    let modalTakingsPassengers = $('#modal-takings-passengers');
    let formTakingsPassengers = $('.form-taking-passengers');

    formTakingsPassengers.submit(function () {
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
                } else {
                    gerror(data.message);
                }
            },
            error: function () {
                gerror('<?php echo app('translator')->getFromJson('An error occurred in the process. Contact your administrator'); ?>');
            },
            complete: function () {

            }
        });
    });

    formTakingsPassengers.find('input.form-control').change(function () {
        let totalProduction = $(this).parents('form').find('#total_production_takings').val();
        let control = $(this).parents('form').find('#control_takings').val();
        let others = $(this).parents('form').find('#others_takings').val();
        let bonus = $(this).parents('form').find('#bonus_takings').val();
        let advance = $(this).parents('form').find('#advance_takings').val();

        let fuel = $(this).parents('form').find('#fuel_takings').val();
        const fuelTariff = parseFloat({{ $dispatchRegister->takings->fuel_tariff }});
        let fuelGallons = fuelTariff > 0 && fuel > 0 ? fuel / fuelTariff : 0;

        totalProduction = totalProduction ? totalProduction : 0;
        control = control ? control : 0;
        fuel = fuel ? fuel : 0;
        others = others ? others : 0;
        advance = advance ? advance : 0;

        let netProduction = totalProduction - control - fuel - others - bonus;

        $(this).parents('form').find('#net_production_takings').val(netProduction);
        $(this).parents('form').find('#balance_takings').val(netProduction - advance);
        $(this).parents('form').find('#fuel_gallons_takings').val(fuelGallons.toFixed(2));
    });

    formTakingsPassengers.find('input.form-control').keyup(function () {
        $(this).change();
    });

    $('.tooltips').tooltip();
</script>

<style>
    .value-recorders {
        font-size: 0.8em !important;
    }
    .form-taking-passengers input[type=number]::-webkit-inner-spin-button,
    .form-taking-passengers input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>