@php
    $detail = isset($detail);
@endphp
<div class="form-group">
    @if(isset($update) && $update === true || $detail)
        <label for="vehicle-memo" class="control-label col-md-4 p-t-0">@lang('Vehicle')</label>
        <div class="col-md-7">
            <input type="hidden" name="vehicle" value="{{ $memo->vehicle->id }}">
            <span class="">
                <i class="fa fa-bus"></i>
                {{ $memo->vehicle->number }} | {{ $memo->vehicle->plate }}
            </span>
        </div>
    @else
        <label for="vehicle-memo" class="control-label col-md-4 field-required">@lang('Vehicle')</label>
        <div class="col-md-7">
            <select name="vehicle" id="vehicle-memo" class="default-select2 form-control col-md-12" data-with-all="true"
                    data-with-only-active="true">
                @include('partials.selects.vehicles', compact('vehicles'), ['selected' => $memo->vehicle ? $memo->vehicle->id : ''])
            </select>
        </div>
    @endif
</div>

<div class="form-group">
    <label for="date" class="control-label col-md-4 field-required">@lang('Date')</label>
    <div class="col-md-7">
        <div class="input-group date memo-date">
            <input name="date" id="date" type="text" class="form-control" {{ $detail ? 'disabled' : '' }}
                   value="{{ $memo->date && $memo->date->toDateString() ?  $memo->date->toDateString() : date('Y-m-d') }}"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar font-dark"></span>
            </span>
        </div>
    </div>
</div>

<hr>

<div class="form-group p-t-10">
    <label for="memo-observations" class="control-label col-md-4 field-required">@lang('Observations')</label>
    <div class="col-md-7">
        <textarea rows="4" id="memo-observations" name="observations" maxlength="5096" {{ $detail ? 'disabled' : '' }}
                  style="resize: vertical; min-height: 400px;max-height: 1200px"
                  class="form-control">{{ $memo->observations }}</textarea>
    </div>
</div>

<style>
    li.select2-selection__choice {
        width: 95%;
    }
</style>

<script>

    let formMemo = $('.form-memo-{{ !isset($update) ? 'create' : 'update' }}');

    $('.memo-date').datepicker({
        format: "yyyy-mm-dd",
        todayBtn: "linked",
        language: "es",
        orientation: "bottom auto",
        daysOfWeekHighlighted: "0,6",
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true
    });

    $('#expiration-date').change(function () {
        $('#notification-date').val($(this).val());
    });

    $('#expiration-mileage').change(function () {
        const prevNotificationMileage = $('#notification-mileage').val();
        if (!prevNotificationMileage) {
            $('#notification-mileage').val($(this).val());
        }

        let labelDate = $('label[for="expiration-date"], label[for="notification-date"]').addClass('field-required');

        if ($(this).val()) {
            labelDate.removeClass('field-required');
        }
    }).change();

    formMemo.submit(function (e) {
        e.preventDefault();
        if (formMemo.isValid()) {
            formMemo.find('button').addClass(loadingClass);
            $.ajax({
                url: formMemo.attr('action'),
                data: formMemo.serialize(),
                type: 'POST',
                success: function (data) {
                    if (data.success) {
                        $('.modal').modal('hide');
                        gsuccess(data.message);

                        $('.form-search-operation').submit();
                    } else {
                        gerror(data.message);
                    }
                },
                complete: function () {
                    formMemo.find('button').removeClass(loadingClass);
                }
            });
        }
    });
</script>