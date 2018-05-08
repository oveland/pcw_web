<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <ul class="nav nav-tabs nav-tabs-primary nav-justified">
                <li class="active">
                    <a href="#tab-1" data-toggle="tab">
                        <i class="fa fa-paper-plane"></i>
                        @lang('Send Commands')
                    </a>
                </li>
                @if( $simGPSList )
                <li class="">
                    <a href="#tab-2" data-toggle="tab">
                        <i class="fa fa-podcast"></i>
                        @lang('Manage SIM GPS')
                    </a>
                </li>
                @endif
            </ul>
            <div class="tab-content m-b-0">
                <div class="tab-pane fade active in" id="tab-1">
                    <form class="form form-send-message" action="{{ route('admin-gps-manage-send-sms') }}">
                        <div class="row">
                            {{ csrf_field() }}
                            <input type="hidden" id="gps-type" name="gps-type" value="" data-reset-command="">
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                                <div class="form-group col-md-12 has-success has-feedback">
                                    <i class="fa fa-signal f-s-17" aria-hidden="true"></i>
                                    <label for="sim-gps" class="control-label field-required">
                                        @lang('SIM GPS')
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <div class="input-group m-b-15 col-md-12">
                                        <div class="m-b-10">
                                            <button type="button" class="btn btn-success btn-xs gps-skypatrol m-r-5" style="cursor: pointer"
                                                    onclick="$(this).removeClass('disabled');$('.gps-coban').addClass('disabled');selectGPSType('SKYPATROL')">
                                                <i class="fa fa-podcast"></i>
                                                @lang('SKYPATROL')
                                            </button>
                                            <button type="button" class="btn btn-primary btn-xs gps-coban disabled" style="cursor: pointer"
                                                    onclick="$(this).removeClass('disabled');$('.gps-skypatrol').addClass('disabled');selectGPSType('COBAN')">
                                                <i class="fa fa-podcast"></i>
                                                @lang('COBAN')
                                            </button>
                                            <button type="button" class="btn btn-inverse btn-sm btn-submit pull-right" data-toggle="modal" data-target="#modal-show-sms-console">
                                                <i class="fa fa-paper-plane" aria-hidden="true"></i> @lang('GPS Command')
                                            </button>
                                        </div>
                                        @if( $simGPSList )
                                            <select id="sim-gps" name="sim-gps[]" class="form-control select default-select2 col-md-12" multiple>
                                                @foreach($simGPSList as $simGPS)
                                                    <option value="{{ $simGPS->sim }}" {{ in_array($simGPS->vehicle->number,$selection) && $simGPS->getGPSType() == 'SKYPATROL' ?'selected':'' }}
                                                    data-reset-command="{{ $simGPS->getResetCommand() }}"
                                                            data-gps-type="{{ $simGPS->getGPSType() }}"
                                                            data-vehicle-id="{{ $simGPS->vehicle->id ?? null }}">
                                                        {{ $simGPS->getGPSType() }}: {{ $simGPS->sim }} | #{{ $simGPS->vehicle->number ?? 'NONE'  }} ({{ $simGPS->vehicle->plate ?? 'NONE'  }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input id="sim-gps" name="sim-gps" type="text" class="form-control" data-any-gps="true" data-gps-type="SKYPATROL">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <div class="row info-gps">
                                    @if( $simGPSList )
                                        <div class="col-md-12">
                                            <label class="control-label">
                                                <i class="fa fa-podcast faa-burst animated"></i>
                                                @lang('Status GPS'):
                                            </label>
                                            <pre class="pre col-md-12 text-info status-gps-container">@lang('Select a SIM number')</pre>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" id="modal-show-sms-console">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                        <h4 class="modal-title">@lang('SMS Console')</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <!-- begin nav-pills -->
                                                <ul class="nav nav-pills nav-pills-inverse nav-justified">
                                                    <li class="active">
                                                        <a href="#nav-pills-justified-1" data-toggle="tab">
                                                            <i class="fa fa-envelope"></i> @lang('GPS Command')
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a href="#nav-pills-justified-2" data-toggle="tab">
                                                            <i class="fa fa-code"></i> @lang('Console log')
                                                        </a>
                                                    </li>
                                                </ul>
                                                <!-- end nav-pills -->
                                                <!-- begin tab-content -->
                                                <div class="tab-content panel row m-0 p-t-5">
                                                    <div class="tab-pane fade active in" id="nav-pills-justified-1">
                                                        <div class="col-md-12">
                                                            <div class="input-group-btn text-center">
                                                                <button type="submit" class="btn btn-lime btn-sm btn-submit">
                                                                    @lang('Send')
                                                                    <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger btn-sm set-reset-command">
                                                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                                                    @lang('Reset Command')
                                                                </button>
                                                            </div>
                                                            <hr class="hr col-md-12">
                                                            <label for="command-gps" class="control-label col-md-12 field-required">@lang('Commands')</label>
                                                            <textarea id="command-gps" name="command-gps" class="form-control pre" rows="40" placeholder="@lang('Type here the commands')"></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="tab-pane fade" id="nav-pills-justified-2">
                                                        <i class="fa fa-envelope faa-horizontal animated"></i> @lang('SMS Response')
                                                        <hr class="hr">
                                                        <pre class="pre sms-response-container"></pre>
                                                    </div>
                                                </div>
                                                <!-- end tab-content -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn width-100 btn-default btn-rounded btn-sm" data-dismiss="modal">
                                            <i class="fa fa-undo"></i> @lang('Close')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @if( $simGPSList )
                    <div class="tab-pane fade" id="tab-2">
                        <!-- begin panel -->
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <div class="panel-heading-btn pull-rigth">
                                    <a class="btn btn-sm btn-icon btn-rounded btn-lime" data-toggle="modal" data-target="#modal-create-sim-gps">
                                        <i class="icon-plus"></i> @lang('Create')
                                    </a>
                                    <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                                </div>
                                <div class="navbar-form form-input-flat pull-right m-0">
                                    <div class="form-group">
                                        <input type="number" class="form-control input-sm input-search-vehicle" placeholder="@lang('Search vehicle')">
                                        <button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                                <h4 class="panel-title">
                                    <i class="fa fa-table" aria-hidden="true"></i>
                                    @lang('List GPS SIM')<br>
                                    <small class="text-bold">{{ count($simGPSList) }} @lang('assigned vehicles') | (66 SKYPATROL) (15 COBAN)</small><br>
                                    <small class="text-bold">{{ count($unAssignedVehicles) }} @lang('unassigned vehicles')</small>
                                </h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-td-valign-middle table-hover">
                                    <thead>
                                    <tr class="info">
                                        <th class="text-center">
                                            <i class="icon-list"></i>
                                        </th>
                                        <th>
                                            <i class="fa fa-car"></i>
                                            @lang('Vehicle')
                                        </th>
                                        <th>
                                            <i class="fa fa-car"></i>
                                            @lang('Number')
                                        </th>
                                        <th>
                                            <i class="fa fa-podcast"></i>
                                            @lang('GPS Type')
                                        </th>
                                        <th>
                                            <i class="fa fa-phone"></i>
                                            @lang('GPS SIM')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-cogs"></i>
                                            @lang('Actions')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($simGPSList as $simGPS)
                                        @php( $vehicle = $simGPS->vehicle )
                                        <tr id="detail-{{ $simGPS->id }}" class="vehicle-list" data-vehicle-number="{{ $vehicle->number ?? '' }}">
                                            @include('admin.gps.manage.gpsVehicleDetail')
                                        </tr>
                                        <tr id="edit-{{ $simGPS->id }}" class="hide">
                                            @include('admin.gps.manage.gpsVehicleEdit')
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- end panel -->

                        <div class="modal fade" id="modal-create-sim-gps">
                            <form id="form-create-sim-gps" action="{{ route('admin-gps-manage-create-sim-gps') }}" class="form-create-sim-gps">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                                            <h4 class="modal-title">@lang('Create register')</h4>
                                        </div>
                                        <div class="modal-body">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="col-md-8 col-md-offset-1">
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <i class="fa fa-car"></i>
                                                            <label for="vehicle_id" class="control-label field-required">
                                                                @lang('Vehicle')
                                                            </label>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <select id="vehicle_id" name="vehicle_id" class="default-select2 form-control input-sm" title="@lang('Vehicle')">
                                                                <option value="">@lang('Select an option')</option>
                                                                @foreach( $unAssignedVehicles as $vehicle )
                                                                    <option value="{{ $vehicle->id }}">{{ "#$vehicle->number | $vehicle->plate" }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small>{{ count($unAssignedVehicles) }} @lang('unassigned vehicles')</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <i class="fa fa-podcast"></i>
                                                            <label for="gps_type" class="control-label field-required">
                                                                @lang('GPS Type')
                                                            </label>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <select id="gps_type" name="gps_type" class="default-select2 form-control input-sm" title="@lang('GPS type')">
                                                                <option value="SKY">SKYPATROL</option>
                                                                <option value="TR">COBAN</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <i class="fa fa-phone"></i>
                                                            <label for="sim" class="control-label field-required">
                                                                @lang('GPS SIM')
                                                            </label>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <div class="form-group has-success has-feedback m-b-0">
                                                                <input name="sim" type="number" class="form-control input-sm" value="" placeholder="SIM" style="border-radius: 50px">
                                                                <span class="fa fa-phone form-control-feedback"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn width-100 btn-default btn-rounded btn-sm" data-dismiss="modal">
                                                <i class="fa fa-undo"></i> @lang('Close')
                                            </button>
                                            <button type="submit" class="btn width-100 btn-lime btn-rounded btn-sm">
                                                <i class="icon-plus"></i> @lang('Create')
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

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