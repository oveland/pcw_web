@if( count($proprietaries) )
<div class="row">
    <div class="col-md-12">
        <!-- begin panel -->
        <div class="panel panel-inverse">
            <div class="panel-heading hide">
                <div class="panel-heading-btn pull-rigth">
                    <a class="btn btn-sm btn-icon btn-rounded btn-lime disabled" onclick="ginfo('@lang('Feature on development')')">
                        <i class="fa fa-cog fa-spin"></i> @lang('Create')
                    </a>
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                </div>
                <div class="navbar-form form-input-flat pull-right m-0">
                    <div class="form-group">
                        <input type="number" class="form-control input-sm input-search-vehicle" placeholder="@lang('Search proprietary')">
                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                <h4 class="panel-title">
                    <i class="fa fa-table" aria-hidden="true"></i>
                    {{ count($proprietaries) }} <small class="text-bold">@lang('Proprietaries')</small><br>
                </h4>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-td-valign-middle table-hover table-report">
                    <thead>
                    <tr class="inverse">
                        <th class="text-center">
                            <i class="icon-list"></i>
                        </th>
                        <th>
                            <i class="fa fa-user"></i><br>
                            @lang('Proprietary name')
                        </th>
                        <th>
                            <i class="fa fa-user-plus"></i><br>
                            @lang('Username')
                        </th>
                        <th>
                            <i class="fa fa-podcast"></i><br>
                            @lang('Cellphone')
                        </th>
                        <th>
                            <i class="fa fa-phone"></i><br>
                            @lang('Assigned vehicles')
                        </th>
                        <th class="text-center">
                            <i class="fa fa-cogs"></i><br>
                            @lang('Actions')
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($proprietaries as $proprietary)
                        @php $id = $proprietary->id; @endphp
                        <tr id="detail-{{ $id }}" class="proprietary-list">
                            @include('admin.proprietaries.templates.tableDetail')
                        </tr>
                        <tr id="edit-{{ $id }}" class="proprietary-list hide">
                            @include('admin.proprietaries.templates.tableEdit')
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <!-- end panel -->
    </div>
</div>
@else
    @include('partials.alerts.noRegistersFound');
@endif

<script type="application/javascript">
    $('.default-select2').select2();
    setTimeout(function () {
        $('.form-send-message .select2-container').css('width','100%');
        $('#sim-gps').change();
    },100);

    $('.input-search-vehicle').keyup(function(){
        var target = $(this).val();
        $('.vehicle-list').each(function (i, e) {
            if (String($(e).data('vehicle-number')) === target || target === '') {
                $(e).slideDown();
            } else {
                $(e).hide();
            }
        });
    });

    function getScript(device) {
        var url = '{{ route('admin-gps-manage-get-script','_DEVICE') }}';
        $.ajax({
            url: url.replace('_DEVICE',device),
            success: function (data) {
                $('#command-gps').val(data);
            }
        });
    }

    function selectGPSType(gpsTypeString) {
        var simGPS = $('#sim-gps');
        var gpsType = $('#gps-type').val(gpsTypeString);
        gpsType.val(gpsTypeString);

        if (simGPS.hasClass('select')) {
            simGPS.find('option').each(function(i,e){
                var el = $(e);
                var disabled = $(e).data('gps-type') !== gpsTypeString;
                if( disabled ){
                    $(e).prop('disabled',disabled);
                    el.remove();
                    el.appendTo('#sim-gps');
                }
                else {
                    $(e).removeAttr('disabled');
                    gpsType.data('reset-command', $(e).data('reset-command'));
                }
            });
            simGPS.select2();
        }
    }

    $('.form-send-message').submit(function(e){
        e.preventDefault();
        var smsResponseContainer = $('.sms-response-container');
        var formSendSMS = $(this);
        if (formSendSMS.isValid()) {
            formSendSMS.find('.btn-submit').addClass(loadingClass);
            smsResponseContainer.slideUp();

            $.ajax({
                url: $(this).attr('action'),
                data: $(this).serialize(),
                type: 'POST',
                success: function (data) {
                    smsResponseContainer.empty().html(data);
                },
                error: function(){
                    smsResponseContainer.empty().html('@lang('An error occurred in the process. Contact your administrator')');
                },
                complete:function(){
                    smsResponseContainer.removeClass('hide').hide().fadeIn();
                    formSendSMS.find('.btn-submit').removeClass(loadingClass);
                }
            });
        }
    });

    $('.form-edit-sim-gps').submit(function(event){
        event.preventDefault();
        var form = $(this);
        var simGPSId = form.data('id');
        if( form.isValid() ){
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'POST',
                success:function(data){
                    $( form.data('target') ).empty().hide().html(data).fadeIn();
                },
                error:function(){
                    gerror('@lang('An error occurred in the process. Contact your administrator')');
                },
                complete:function () {
                    $('#detail-' + simGPSId).removeClass('hide');
                    $('#edit-' + simGPSId).addClass('hide');
                }
            });
        }
    });

    $('#form-create-sim-gps').submit(function(e){
        e.preventDefault();
        var form = $(this);
        if( form.isValid() ){
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                type: 'POST',
                success: function(data){
                    if( data.success ){
                        gsuccess(data.message);
                        $('.modal').modal('hide');
                    }else{
                        gerror(data.message);
                    }
                },
                error:function () {
                    gerror('@lang('An error occurred in the process. Contact your administrator')');
                },
                complete:function(){
                    setTimeout(function(){
                        $('.form-search-report').submit();
                    },500);
                }
            });
        }
    });

    $('.form-delete-sim-gps').submit(function(e){
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: form.attr('action'),
            type: 'DELETE',
            success: function(data){
                if( data.success ){
                    gsuccess(data.message);
                    $('.modal').modal('hide');
                }else{
                    gerror(data.message);
                }
            },
            error:function () {
                gerror('@lang('An error occurred in the process. Contact your administrator')');
            },
            complete:function(){
                setTimeout(function(){
                    $('.form-search-report').submit();
                },500);
            }
        });
    });

    $('#sim-gps').change(function(){
        var simGPS = $(this);
        var simNumber = simGPS.val();
        var statusGPSContainer = $('.status-gps-container');
        if( !simGPS.data('any-gps') ){
            statusGPSContainer.parent().slideDown();
            if( is_not_null(simNumber) ){

                $.ajax({
                    url: '{{ route('admin-gps-get-vehicle-status') }}',
                    data:{
                        simGPSList: simGPS.val()
                    },
                    success:function(data){
                        statusGPSContainer.html(data);
                    },error:function(){
                        statusGPSContainer.empty();
                        gerror('@lang('An error occurred in the process. Contact your administrator')');
                    }
                });

            }else{
                statusGPSContainer.html('@lang('Select a SIM number')');
            }
        }else{
            statusGPSContainer.parent().slideUp();
        }
    });

    $('.set-reset-command').click(function(){
        var resetCommand = $('#gps-type').data('reset-command');
        if( is_not_null(resetCommand) ){
            $('#command-gps').val(resetCommand);
        }else{
            gwarning('@lang('Select a SIM number')');
        }
    });

    $('.gps-skypatrol').click();
</script>