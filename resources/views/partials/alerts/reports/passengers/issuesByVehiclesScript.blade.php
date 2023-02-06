@if(Auth::user() && (Auth::user()->canEditFields()) )
    <script type="application/javascript">

        $(document).ready(function () {
            $('body')
            .on('click', '.box-edit', function () {
                $(this).find('.box-info').hide();
                let boxEdit = $(this).find('.box-edit');
                let inputEdit = boxEdit.find('input');
                inputEdit.val(inputEdit.val());

                if(!boxEdit.is(':visible')) {
                    boxEdit.show();
                    inputEdit.focus();
                }
            })
            .on('click', '.box-edit .edit-btn-cancel', function () {
                let boxEdit = $(this).parents('.box-edit');
                setTimeout(function() {
                    boxEdit.find('.box-info').show();
                    boxEdit.find('.box-edit').hide();
                }, 300);
            })
            .on('click', '.box-edit .edit-btn-save', function () {
                let boxEdit = $(this).parents('.box-edit');
                saveAction(boxEdit);
            })
            .on('keyup', '.edit-input-recorder', function (e) {
                let keycode = (e.keyCode ? e.keyCode : e.which);
                if (keycode.toString() === '13') {
                    let boxEdit = $(this).parents('.box-edit');
                    saveAction(boxEdit);
                }
            })
            .on('click', '.edit-field-dr', function (e) {
                let el = $(this);
                let url = el.data('url');
                let id = el.data('id');
                let field = el.data('field');
                let value = el.data('value');
                let confirm = el.data('confirm');

                let confirmMessage = window.confirm(confirm ? confirm : `Confirm edit id = ${id}: field ${field} = ${value}.`);

                if (confirmMessage) {
                    editFieldDr(url, id, null, null, function () {
                        $('.btn-search-report').click();
                    });
                }
            });
        });

        function saveAction(boxEdit) {
            let input = $(boxEdit).find('.edit-input-value');
            input = input ? input : $(boxEdit).find('.edit-input-recorder');

            let inputObs = boxEdit.find('.edit-input-obs');


            if(inputObs.val().trim() || input.data('single')) {
                console.log("data",input.data('single'));
                let dispatchRegisterId = input.data('id');
                let dispatchRegisterField = input.data('field');
                let dispatchRegisterValue = input.val();
                let url = input.data('url');
                let obs = inputObs.val()?.trim();

                editFieldDr(url, dispatchRegisterId, dispatchRegisterField, dispatchRegisterValue, obs, function (data) {
                    boxEdit.find('.box-info').show().find('span').text(data.value);
                    boxEdit.find('.box-edit').hide();
                });
            } else {
                gerror('@lang('Please write a observation')');
            }
        }

        function editFieldDr(url, id, field, value, obs, onSuccess) {
            $.ajax({
                url: url,
                data: {id, field, value, obs},
                success: function (data) {
                    if (data.success) {
                        gsuccess('@lang('Data updated successfully')');
                        $('.container-alert-new-values').slideDown(100);
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