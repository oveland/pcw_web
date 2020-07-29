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
                    <span>{{ intval($dispatchRegister->end_recorder) - intval($dispatchRegister->start_recorder) }}</span>
                </h5>
                <h5>
                    <i class="icon-compass"></i> <strong>@lang('Recorders'):</strong>
                    <span>{{ intval($dispatchRegister->start_recorder) }} - {{ intval($dispatchRegister->end_recorder) }}</span>
                </h5>
            </div>
        </div>
    </div>

    <div class="col-md-8 col-md-offset-2">
        <form class="form-horizontal form-taking-passengers" role="form" action="{{ route('operation-routes-takings-taking', ['dispatchRegister' => $dispatchRegister->id]) }}" data-dr="{{ $dispatchRegister->id }}">
            {{ csrf_field() }}
            <div class="form-group">
                <label for="total-production" class="col-md-5 control-label">@lang('Total production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" class="form-control input-circle-right" name="total_production" value="{{ $dispatchRegister->takings->total_production }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="control" class="col-md-5 control-label">@lang('Control')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-bag"></i>
                        <input type="number" class="form-control input-circle-right" name="control" value="{{ $dispatchRegister->takings->control }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="fuel" class="col-md-5 control-label">@lang('Fuel')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-fire"></i>
                        <input type="number" class="form-control input-circle-right" name="fuel" value="{{ $dispatchRegister->takings->fuel }}">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="others" class="col-md-5 control-label">@lang('Others')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-cup"></i>
                        <input type="number" class="form-control input-circle-right" name="others" value="{{ $dispatchRegister->takings->others }}">
                    </div>
                </div>
            </div>

            <hr class="hr no-padding">

            <div class="form-group has-success">
                <label for="net-production" class="col-md-5 control-label">@lang('Net production')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="fa fa-dollar"></i>
                        <input type="number" class="form-control input-circle-right" name="net_production" value="{{ $dispatchRegister->takings->net_production }}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label for="observations" class="col-md-5 control-label">@lang('Observations')</label>
                <div class="col-md-7">
                    <div class="input-icon">
                        <i class="icon-note"></i>
                        <textarea style="resize: vertical;min-height: 40px;max-height: 300px" maxlength="500" class="form-control input-circle-right" name="observations">{{ $dispatchRegister->takings->observations }}</textarea>
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
    text.on('change drop keydown cut paste', function() {
        text.height('auto');
        text.height(text.prop('scrollHeight'));
    });

    let modalTakingsPassengers = $('#modal-takings-passengers');

    $('.form-taking-passengers').submit(function () {
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
</script>

<style>
    .value-recorders{
        font-size: 0.8em !important;
    }
</style>