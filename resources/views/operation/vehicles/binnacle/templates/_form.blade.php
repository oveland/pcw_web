<div class="form-group">
    @if(isset($update) && $update === true)
        <input type="hidden" name="vehicle" value="{{ $binnacle->vehicle->id }}">
        <span class="col-md-6 col-md-offset-4 p-20">
            <i class="fa fa-bus"></i>
            {{ $binnacle->vehicle->number }} | {{ $binnacle->vehicle->plate }}
        </span>
    @else
        <label for="vehicle-binnacle" class="control-label col-md-4 field-required">@lang('Vehicle')</label>
        <div class="col-md-6">
            <select name="vehicle" id="vehicle-binnacle" class="default-select2 form-control col-md-12" data-with-all="true" data-with-only-active="true">
                @include('partials.selects.vehicles', compact('vehicles'))
            </select>
        </div>
    @endif
</div>
<div class="form-group">
    <label for="binnacle-type" class="control-label col-md-4 field-required">@lang('Type')</label>
    <div class="col-md-6">
        <select name="type" id="binnacle-type" class="default-select2 form-control col-md-12" data-with-all="true" data-with-only-active="true">
            @include('partials.selects.binnacleTypes', compact('binnacleTypes'), ['selected' => $binnacle ? $binnacle->type->id : ''])
        </select>
    </div>
</div>

<div class="form-group">
    <label for="expiration-date" class="control-label col-md-4 field-required">@lang('Expiration date')</label>
    <div class="col-md-6">
        <div class="input-group date binnacle-date">
            <input name="date" id="expiration-date" type="text" class="form-control" value="{{ $binnacle ? $binnacle->date->toDateString() : \Carbon\Carbon::now()->toDateString() }}"/>
            <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>
        </div>
    </div>
</div>

<div class="form-group">
    <label for="binnacle-observations" class="control-label col-md-4 field-required">@lang('Observations')</label>
    <div class="col-md-6">
        <textarea rows="2" id="binnacle-observations" name="observations" maxlength="256" style="resize: vertical" class="form-control">{{ $binnacle ? $binnacle->observations : '' }}</textarea>
    </div>
</div>

<hr>

<div class="form-group">
    <label for="notifications" class="control-label col-md-4"></label>
    <div class="col-md-6">
        <h5 class="text-primary">
            @lang('Notifications')
            <i class="fa fa-bell faa-ring animated"></i>
        </h5>
    </div>
</div>

<div class="form-group">
    <label for="notification-date" class="control-label col-md-4 field-required">@lang('Notification date')</label>
    <div class="col-md-6">
        <div class="input-group date binnacle-date">
            <input name="notification-date" id="notification-date" type="text" class="form-control" value="{{ $binnacle ? $binnacle->notification->date->toDateString() : \Carbon\Carbon::now()->toDateString() }}"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
            </span>
        </div>
    </div>
</div>

<div class="form-group hide">
    <label for="notification-period" class="control-label col-md-4 field-required">@lang('Every (days)')</label>
    <div class="col-md-6">
        <input type="number" name="notification-period" id="notification-period" class="form-control col-md-12" value="{{ $binnacle ? $binnacle->notification->period : 30 }}">
    </div>
</div>

<div class="form-group">
    <label for="notification-users" class="control-label col-md-4 field-required">@lang('Users')</label>
    <div class="col-md-6">
        <select name="notification-users[]" id="notification-users" class="default-select2 form-control col-md-12" multiple>
            @include('partials.selects.users', compact('users'), ['selected' => $binnacle ? $binnacle->notification->notificationUsers->pluck('user_id') : '', 'withEmail' => true, 'withName' => true])
        </select>
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