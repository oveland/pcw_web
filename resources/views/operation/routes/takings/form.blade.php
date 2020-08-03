<div class="row">
    <div class="col-md-12">
        <div class="well row">
            <h4>
                <i class="fa fa-bus"></i> <strong>@lang('Vehicle'):</strong>
                <span>{{ $dispatchRegister->vehicle->number }}</span>
            </h4>
            <div class="col-md-12">
                <h5>
                    <i class="icon-directions"></i> <strong>@lang('Round trip'):</strong>
                    <span>{{ $dispatchRegister->round_trip }}</span>
                </h5>
                <h5>
                    <i class="fa fa-users"></i> <strong>@lang('Total') @lang('passengers'):</strong>
                    <span>{{ $dispatchRegister->passengers->recorders->count }}</span>
                </h5>
                <h5>
                    <i class="icon-compass"></i> <strong>@lang('Recorders'):</strong>
                    <span>{{ $dispatchRegister->passengers->recorders->start }} - {{ $dispatchRegister->passengers->recorders->end }}</span>
                </h5>
                <h5>
                    <i class="fa fa-dollar"></i><strong style="margin-left: 8px">@lang('Tariff'):</strong>
                    <span>{{ $dispatchRegister->tariff->value }}</span>
                </h5>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-md-offset-2">
        <form class="form-horizontal form-taking-passengers" role="form"
              action="{{ route('operation-routes-takings-taking', ['dispatchRegister' => $dispatchRegister->id]) }}"
              data-dr="{{ $dispatchRegister->id }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="total-production" class="col-md-5 control-label">@lang('Total production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" disabled class="form-control input-circle-right" id="total_production_takings" name="total_production" value="{{ $dispatchRegister->takings->total_production }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="control" class="col-md-5 control-label">@lang('Control')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-bag"></i>
                        <input type="number" class="form-control input-circle-right" id="control_takings" name="control" value="{{ $dispatchRegister->takings->control }}">
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

            <div class="form-group has-success">
                <label for="net-production" class="col-md-5 control-label">@lang('Net production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" disabled class="form-control input-circle-right disabled" id="net_production_takings" name="net_production" value="{{ $dispatchRegister->takings->net_production }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
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
</div>

<script>
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

    formTakingsPassengers.find('input.form-control').keyup(function () {
        let totalProduction = $(this).parents('form').find('#total_production_takings').val();
        let control = $(this).parents('form').find('#control_takings').val();
        let fuel = $(this).parents('form').find('#fuel_takings').val();
        let others = $(this).parents('form').find('#others_takings').val();

        totalProduction = totalProduction ? totalProduction : 0;
        control = control ? control : 0;
        fuel = fuel ? fuel : 0;
        others = others ? others : 0;

        $(this).parents('form').find('#net_production_takings').val(totalProduction - control - fuel - others);
    });
</script>

<style>
    .value-recorders {
        font-size: 0.8em !important;
    }
</style>