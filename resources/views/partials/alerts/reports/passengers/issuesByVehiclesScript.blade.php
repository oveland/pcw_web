@if( Auth::user()->isAdmin() )
<style>
    .box-edit .box-info:hover{
        border: 1px solid gray;
        border-radius: 6px;
        cursor: pointer;
        -webkit-transition: all 0.2s;
        transition: all 0.2s;
    }
</style>

<script type="application/javascript">
    $(document).ready(function () {
        $('body').on('click', '.box-edit', function () {
            $(this).find('.box-info').hide();
            let boxEdit = $(this).find('.box-edit');
            let inputEdit = boxEdit.fadeIn(1500).find('input');
            inputEdit.val( inputEdit.val() ).focus();
        });

        $('body').on('keyup', '.edit-input-recorder', function (e) {
            var keycode = (e.keyCode ? e.keyCode : e.which);
            var input = $(this);
            console.log("Key pressed: ", keycode);

            if (keycode == '13') {
                var dispatchRegisterId = input.data('id');
                var dispatchRegisterField = input.data('field');
                var dispatchRegisterValue = input.val();

                $.ajax({
                    url: input.data('url'),
                    data: {
                        id: dispatchRegisterId,
                        field: dispatchRegisterField,
                        value: dispatchRegisterValue
                    },
                    success: function (data) {
                        if(data.success){
                            gsuccess('@lang('Data updated successfully')');
                            input.parents('.box-edit').find('.box-info').show().find('span').text(data.value);
                            input.parents('.box-edit').find('.box-edit').hide();
                            $('.container-alert-new-values').slideDown(1500);
                        }else{
                            gerror('@lang('Error updating data')');
                        }
                    },
                    error: function () {
                        gerror('@lang('Error updating data')');
                    },
                    complete: function () {
                    }
                });
            }
        });
    });
</script>
@endif