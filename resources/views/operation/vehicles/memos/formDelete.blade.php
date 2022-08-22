<form class="col-md-12 form-horizontal form-memo-delete p-b-20" action="{{ route('operation-vehicles-memo-delete', ['memo' => $memo->id]) }}">
    <div class="form-body p-l-40 p-r-40">
        <div class="details col-md-12 bg-danger-light">
            <strong class="text-danger">
                <i class="fa fa-warning"></i>
                @lang('Confirm delete the register'):
            </strong>

            <p>@lang('Vehicle') {{ $memo->vehicle->number }}</p>
            <p>@lang('Date'): {{ $memo->date }}</p>
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

    let formMemoDelete = $('.form-memo-delete');

    formMemoDelete.submit(function (e) {
        e.preventDefault();
        if (formMemoDelete.isValid()) {
            formMemoDelete.find('button').addClass(loadingClass);
            $.ajax({
                url: formMemoDelete.attr('action'),
                data: formMemoDelete.serialize(),
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
                    formMemoDelete.find('button').removeClass(loadingClass);
                }
            });
        }
    });
</script>