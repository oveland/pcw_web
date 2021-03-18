@if(Auth::user() && Auth::user()->canEditRecorders() )
    <script type="application/javascript">

        $(document).ready(function () {
            $('body').on('click', '.box-edit', function () {
                $(this).find('.box-info').hide();
                let boxEdit = $(this).find('.box-edit');
                let inputEdit = boxEdit.fadeIn(1500).find('input');
                inputEdit.val(inputEdit.val()).focus();
            });

            $('body').on('keyup', '.edit-input-recorder', function (e) {
                var keycode = (e.keyCode ? e.keyCode : e.which);
                var input = $(this);

                if (keycode.toString() === '13') {
                    var dispatchRegisterId = input.data('id');
                    var dispatchRegisterField = input.data('field');
                    var dispatchRegisterValue = input.val();
                    var url = input.data('url');

                    editFieldDr(url, dispatchRegisterId, dispatchRegisterField, dispatchRegisterValue, function (data) {
                        input.parents('.box-edit').find('.box-info').show().find('span').text(data.value);
                        input.parents('.box-edit').find('.box-edit').hide();
                    });
                }
            }).on('click', '.edit-field-dr', function (e) {
                var el = $(this);
                var url = el.data('url');
                var id = el.data('id');
                var field = el.data('field');
                var value = el.data('value');
                var confirm = el.data('confirm');

                var confirm = window.confirm(confirm ? confirm : `Confirm edit id = ${id}: field ${field} = ${value}.`);

                if (confirm) {
                    editFieldDr(url, id, null, null, function () {
                        $('.btn-search-report').click();
                    });
                }
            });
        });

        function editFieldDr(url, id, field, value, onSuccess) {
            $.ajax({
                url: url,
                data: {id, field, value},
                success: function (data) {
                    if (data.success) {
                        gsuccess('@lang('Data updated successfully')');
                        $('.container-alert-new-values').slideDown(1500);
                        onSuccess(data);
                    } else {
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
    </script>
@endif