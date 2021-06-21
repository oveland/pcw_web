<form class="col-md-12 form-horizontal form-binnacle-complete p-b-20" action="{{ route('operation-vehicles-binnacle-complete', ['binnacle' => $binnacle->id]) }}">
    <div class="form-body p-l-40 p-r-40">
        <div class="details col-md-12 bg-success-light">
            <strong class="text-success">
                <i class="fa fa-warning"></i>
                @lang('Confirm completed the maintenance'):
            </strong>

            <p>@lang('Vehicle') {{ $binnacle->vehicle->number }}</p>

            <p>
                <label class="label-type tooltips" data-title="{{ $binnacle->type->description }}">
                    <i class='fa fa-circle text-{{ $binnacle->type->css_class }}'></i> {{ $binnacle->type->name }}
                </label>
            </p>

            @if($binnacle->date)
                <p>@lang('Expiration date'): {{ $binnacle->date }}</p>
            @endif

            @if($binnacle->mileage_expiration)
                <p>@lang('Expiration mileage'):
                    @if(Auth::user()->isSuperAdmin())
                        <span class="tooltips" title="Km Odometer" data-placement="left">
                        {{ number_format($binnacle->getMileageTraveled('odometer'), 1)." Km" }}
                    </span>
                        |
                        <span class="tooltips" title="Km Route" data-placement="left">
                        {{ number_format($binnacle->getMileageTraveled('route'), 1)." Km" }}
                    </span>
                        <br>
                    @else
                        {{ number_format($binnacle->getMileageTraveled(), 1)." Km" }}
                    @endif
                </p>
            @endif
        </div>

        <hr class="hr col-md-12">

        <div class="col-md-12">
            <div class="md-checkbox">
                <input type="checkbox" id="checkbox-next" class="md-check" name="create-next" value="true">
                <label for="checkbox-next">
                    <span class="inc"></span>
                    <span class="check"></span>
                    <span class="box"></span>

                    @lang('Create next maintenance')
                </label>
            </div>
        </div>

        <hr class="hr col-md-12">
        
        <div class="form-actions">
            <div class="col-md-12">
                <button class="btn btn-rounded btn-circle btn-outline green-dark btn-lg">
                    <i class="fa fa-check"></i> @lang('Save')
                </button>
            </div>
        </div>
    </div>
</form>


<script>

    let formBinnacleComplete = $('.form-binnacle-complete');

    $('.default-select2').select2();

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

    formBinnacleComplete.submit(function (e) {
        e.preventDefault();
        if (formBinnacleComplete.isValid()) {
            formBinnacleComplete.find('button').addClass(loadingClass);
            $.ajax({
                url: formBinnacleComplete.attr('action'),
                data: formBinnacleComplete.serialize(),
                type: 'POST',
                success: function (data) {
                    if (data.success) {
                        $('.modal').modal('hide');
                        gsuccess(data.message);

                        $('.form-search-operation').submit();

                        if(data.createNext){
                            setTimeout(function() {
                                loadBinnacleFormCreate(data.fromBinnacle);
                            }, 1000);
                        }

                    } else {
                        gerror(data.message);
                    }
                },
                complete: function () {
                    formBinnacleComplete.find('button').removeClass(loadingClass);
                }
            });
        }
    });
</script>