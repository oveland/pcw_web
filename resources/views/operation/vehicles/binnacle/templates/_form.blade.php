<div class="form-group">
    @if(isset($update) && $update === true)
        <label for="vehicle-binnacle" class="control-label col-md-5 p-t-0">@lang('Vehicle')</label>
        <div class="col-md-5">
            <input type="hidden" name="vehicle" value="{{ $binnacle->vehicle->id }}">
            <span class="">
                <i class="fa fa-bus"></i>
                {{ $binnacle->vehicle->number }} | {{ $binnacle->vehicle->plate }}
            </span>
        </div>
    @else
        <label for="vehicle-binnacle" class="control-label col-md-5 field-required">@lang('Vehicle')</label>
        <div class="col-md-5">
            <select name="vehicle" id="vehicle-binnacle" class="default-select2 form-control col-md-12" data-with-all="true" data-with-only-active="true">
                @include('partials.selects.vehicles', compact('vehicles'), ['selected' => $binnacle->vehicle ? $binnacle->vehicle->id : ''])
            </select>
        </div>
    @endif
</div>

<div class="form-group">
    <label for="binnacle-type" class="control-label col-md-5 field-required">@lang('Type')</label>
    <div class="col-md-5">
        <select name="type" id="binnacle-type" class="default-select2 form-control col-md-12" data-with-all="true" data-with-only-active="true">
            @include('partials.selects.binnacleTypes', compact('binnacleTypes'), ['selected' => $binnacle->type ? $binnacle->type->id : ''])
        </select>
    </div>
</div>

<div class="form-group">
    <label for="prev-date" class="control-label col-md-5 field-required">@lang('Previous maintenance date')</label>
    <div class="col-md-5">
        <div class="input-group date binnacle-date">
            <input name="prev-date" id="prev-date" type="text" class="form-control" value="{{ $binnacle->prev_date && $binnacle->prev_date->toDateString() ?  $binnacle->prev_date->toDateString() : date('Y-m-d') }}"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar font-dark"></span>
            </span>
        </div>
    </div>
</div>

<div class="form-group bg-default m-t-20">
    <label for="notifications" class="control-label col-md-5"></label>
    <div class="col-md-5">
        <h4 class="text-primary">
            @lang('Expirations')
            <i class="fa fa-calculator faa-float animated"></i>
        </h4>
    </div>
</div>

<div class="form-group">
    <label for="expiration-date" class="control-label col-md-5 field-required">@lang('Expiration date')</label>
    <div class="col-md-5">
        <div class="input-group date binnacle-date">
            <input name="date" id="expiration-date" type="text" class="form-control" value="{{ $binnacle->date ? $binnacle->date->toDateString() : '' }}"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar font-dark"></span>
            </span>
        </div>
    </div>
</div>
<hr>
<div class="form-group p-t-10" style="z-index: 100000">
    <label for="expiration-mileage" class="control-label col-md-5">@lang('Expiration mileage') <small class="text-muted">Km</small></label>
    <div class="col-md-5">
        <div class="input-group">
            <input name="mileage" id="expiration-mileage" type="number" min="1" class="form-control" data-toggle="click" value="{{ $binnacle ? $binnacle->mileage : '' }}"/>
            <span class="input-group-addon">
                <i class="fa fa-road font-dark"></i>
            </span>
        </div>
    </div>
</div>

<div class="form-group hide">
    <label for="current-mileage" class="control-label col-md-5">@lang('Traveled mileage')</label>
    <div class="col-md-5">
        <div class="input-group">
            <span class="input-group-addon">
                {{ $binnacle ? $binnacle->currentMileage ?? 0 : 0 }} Km
            </span>
        </div>
        <div class="">
            <small class="text-muted" style="font-size: 1rem; letter-spacing: 0; text-justify: auto ">Kilometraje recorrido desde la <b>Fecha del mantenimiento anterior</b></small>
        </div>
    </div>
</div>


<div class="form-group bg-default m-t-20">
    <label for="notifications" class="control-label col-md-5"></label>
    <div class="col-md-5">
        <h4 class="text-warning">
            @lang('Notifications')
            <i class="fa fa-bell faa-ring animated"></i>
        </h4>
    </div>
</div>

<div class="form-group">
    <label for="notification-date" class="control-label col-md-5 field-required">@lang('Notification date')</label>
    <div class="col-md-5">
        <div class="input-group date binnacle-date">
            <input name="notification-date" id="notification-date" type="text" class="form-control" value="{{ $binnacle->notification && $binnacle->notification->date ? $binnacle->notification->date->toDateString() : '' }}"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar font-dark"></span>
            </span>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="notification-mileage" class="control-label col-md-5">@lang('Notification mileage') <small class="text-muted">Km</small></label>
    <div class="col-md-5">
        <div class="input-group">
            <input name="notification-mileage" id="notification-mileage" type="number" min="1" class="form-control" value="{{ $binnacle->notification ? $binnacle->notification->mileage : '' }}"/>
            <span class="input-group-addon">
                <i class="fa fa-road font-dark"></i>
            </span>
        </div>
    </div>
</div>

<div class="form-group hide">
    <label for="notification-period" class="control-label col-md-5 field-required">@lang('Every (days)')</label>
    <div class="col-md-5">
        <input type="number" name="notification-period" id="notification-period" class="form-control col-md-12" value="{{ $binnacle->notification ? $binnacle->notification->period : 30 }}">
    </div>
</div>

<div class="form-group">
    <label for="notification-users" class="control-label col-md-5 field-required">@lang('Users')</label>
    <div class="col-md-5">
        <select name="notification-users[]" id="notification-users" class="default-select2 form-control col-md-12" multiple>
            @include('partials.selects.users', compact('users'), ['selected' => $binnacle->notification ? $binnacle->notification->notificationUsers->pluck('user_id') : '', 'withEmail' => true, 'withName' => true])
        </select>
    </div>
</div>

<hr>

<div class="form-group p-t-10">
    <label for="binnacle-observations" class="control-label col-md-5 field-required">@lang('Observations')</label>
    <div class="col-md-5">
        <textarea rows="2" id="binnacle-observations" name="observations" maxlength="256" style="resize: vertical; min-height: 60px;max-height: 200px" class="form-control">{{ $binnacle->observations }}</textarea>
    </div>
</div>

<style>
    li.select2-selection__choice{
        width: 95%;
    }
</style>

<script>

    let formBinnacle = $('.form-binnacle-{{ !isset($update) ? 'create' : 'update' }}');

    $('.binnacle-date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "es",
        orientation: "bottom auto",
        daysOfWeekHighlighted: "0,6",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true
    });

    $('#expiration-date').change(function (){
        $('#notification-date').val($(this).val());
    });

    $('#expiration-mileage').change(function (){
        const prevNotificationMileage = $('#notification-mileage').val();
        if (!prevNotificationMileage) {
            $('#notification-mileage').val($(this).val());
        }

        let labelDate = $('label[for="expiration-date"], label[for="notification-date"]').addClass('field-required');

        if ($(this).val()) {
            labelDate.removeClass('field-required');
        }
    }).change();

    formBinnacle.submit(function (e) {
        e.preventDefault();
        if (formBinnacle.isValid()) {
            formBinnacle.find('button').addClass(loadingClass);
            $.ajax({
                url: formBinnacle.attr('action'),
                data: formBinnacle.serialize(),
                type: 'POST',
                success: function (data) {
                    if(data.success){
                        $('.modal').modal('hide');
                        gsuccess(data.message);

                        $('.form-search-operation').submit();
                    }else{
                        gerror(data.message);
                    }
                },
                complete:function(){
                    formBinnacle.find('button').removeClass(loadingClass);
                }
            });
        }
    });
</script>