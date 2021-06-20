<form class="col-md-12 form-horizontal form-binnacle-delete p-b-20" action="{{ route('operation-vehicles-binnacle-delete', ['binnacle' => $binnacle->id]) }}">
    <div class="form-body p-l-40 p-r-40">
        <div class="details col-md-12 bg-danger-light">
            <strong class="text-danger">
                <i class="fa fa-warning"></i>
                @lang('Confirm delete the register'):
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

        <div class="form-actions">
            <div class="col-md-12">
                <button class="btn btn-rounded btn-circle btn-outline btn-danger btn-lg">
                    <i class="fa fa-trash"></i> @lang('Delete')
                </button>
            </div>
        </div>
    </div>
</form>


<script>

    let formBinnacleDelete = $('.form-binnacle-delete');

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

    formBinnacleDelete.submit(function (e) {
        e.preventDefault();
        if (formBinnacleDelete.isValid()) {
            formBinnacleDelete.find('button').addClass(loadingClass);
            $.ajax({
                url: formBinnacleDelete.attr('action'),
                data: formBinnacleDelete.serialize(),
                type: 'DELETE',
                success: function (data) {
                    if(data.success){
                        $('.modal').modal('hide');
                        gwarning(data.message);

                        $('.form-search-operation').submit();
                    }else{
                        gerror(data.message);
                    }
                },
                complete:function(){
                    formBinnacleDelete.find('button').removeClass(loadingClass);
                }
            });
        }
    });
</script>