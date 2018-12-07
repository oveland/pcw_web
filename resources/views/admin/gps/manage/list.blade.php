<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <ul class="nav nav-tabs nav-tabs-primary nav-justified tab-report">
                <li class="active" onclick="if(editedSIM)$('.form-search-report').submit()">
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
                            <div class="col-md-6 col-sm-12 col-xs-12 p-0">
                                <div class="form-group col-md-12 has-success has-feedback">
                                    <i class="fa fa-signal f-s-17" aria-hidden="true"></i>
                                    <label for="sim-gps" class="control-label field-required">
                                        @lang('SIM GPS')
                                    </label>
                                </div>
                                <div class="col-md-12">
                                    <div class="m-b-15 col-md-12 p-0">
                                        @if( $simGPSList )
                                            <select id="sim-gps" name="sim-gps[]" class="form-control select default-select2 col-md-12" multiple>
                                                @foreach($simGPSList as $simGPS)
                                                    <option value="{{ $simGPS->sim }}" {{ in_array($simGPS->vehicle->number,$selection) ?'selected':'' }}
                                                    data-reset-command="{{ $simGPS->getResetCommand() }}"
                                                            data-gps-type="{{ $simGPS->gps_type }}"
                                                            data-vehicle-id="{{ $simGPS->vehicle->id ?? null }}">
                                                        {{ $simGPS->gps_type }}: {{ $simGPS->sim }} | #{{ $simGPS->vehicle->number ?? 'NONE'  }} ({{ $simGPS->vehicle->plate ?? 'NONE'  }})
                                                    </option>
                                                @endforeach
                                            </select>
                                        @else
                                            <input id="sim-gps" name="sim-gps" type="text" class="form-control" data-any-gps="true" data-gps-type="SKYPATROL">
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12 p-0">
                                <div class="info-gps">
                                    @if( $simGPSList )
                                        <div class="col-md-12">
                                            <button type="button" class="btn btn-inverse m-b-5 btn-sm btn-submit pull-right" data-toggle="modal" data-target="#modal-show-sms-console">
                                                <i class="fa fa-paper-plane" aria-hidden="true"></i> @lang('GPS Command')
                                            </button>
                                            <label class="control-label">
                                                <i class="fa fa-podcast faa-burst animated"></i>
                                                @lang('Status GPS'):
                                            </label>
                                        </div>
                                        <div class="col-md-12">
                                            <pre class="col-md-12 text-info status-gps-container text-center" style="overflow: inherit">@lang('Select a SIM number')</pre>
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
                                                        <a href="#nav-pills-justified-2" data-toggle="tab" class="tab-console-log">
                                                            <i class="fa fa-code"></i> @lang('Console log')
                                                        </a>
                                                    </li>
                                                </ul>
                                                <!-- end nav-pills -->
                                                <!-- begin tab-content -->
                                                <div class="tab-content panel row m-0 p-t-5">
                                                    <div class="tab-pane fade active in" id="nav-pills-justified-1">
                                                        <div class="col-md-12">
                                                            <div class="btn-group">
                                                                <button type="submit" class="btn btn-success btn-sm btn-submit">
                                                                    @lang('Send') <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                                                </button>
                                                                <button data-toggle="dropdown" class="btn btn-success btn-sm dropdown-toggle" aria-expanded="false">
                                                                    <span class="caret"></span>
                                                                </button>
                                                                <ul class="dropdown-menu">
                                                                    @if( $gpsReport === \App\Models\Vehicles\SimGPS::SKYPATROL )
                                                                    <li>
                                                                        <a href="javascript:getScript('general-skypatrol')">
                                                                            <i class="fa fa-podcast text-info" aria-hidden="true"></i> @lang('Script General Skypatrol')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('apn-skypatrol')">
                                                                            <i class="fa fa-podcast text-info" aria-hidden="true"></i> @lang('Script APN Skypatrol')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('ip-skypatrol')">
                                                                            <i class="fa fa-podcast text-info" aria-hidden="true"></i> @lang('Script IP Skypatrol')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('plate-skypatrol')">
                                                                            <i class="fa fa-podcast text-info" aria-hidden="true"></i> @lang('Script plate Skypatrol')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('new-skypatrol')">
                                                                            <i class="fa fa-podcast text-danger" aria-hidden="true"></i> @lang('NEW Script Skypatrol')
                                                                        </a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    @endif
                                                                    @if( $gpsReport === \App\Models\Vehicles\SimGPS::COBAN )
                                                                    <li>
                                                                        <a href="javascript:getScript('apn-claro-coban')">
                                                                            <i class="fa fa-podcast text-danger" aria-hidden="true"></i> @lang('Script Coban - CLARO')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('apn-movistar-coban')">
                                                                            <i class="fa fa-podcast text-info" aria-hidden="true"></i> @lang('Script Coban - MOVISTAR')
                                                                        </a>
                                                                    </li>
                                                                    <li>
                                                                        <a href="javascript:getScript('apn-avantel-coban')">
                                                                            <i class="fa fa-podcast text-purple" aria-hidden="true"></i> @lang('Script Coban - AVANTEL')
                                                                        </a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    @endif
                                                                    @if( $gpsReport === \App\Models\Vehicles\SimGPS::RUPTELA )
                                                                        <li>
                                                                            <a href="javascript:getScript('ip-ruptela')">
                                                                                <i class="fa fa-podcast text-purple" aria-hidden="true"></i> @lang('Script Ruptela') | IP
                                                                            </a>
                                                                        </li>
                                                                        <li>
                                                                            <a href="javascript:getScript('time-report-ruptela')">
                                                                                <i class="fa fa-podcast text-purple" aria-hidden="true"></i> @lang('Script Ruptela') | @lang('Time report')
                                                                            </a>
                                                                        </li>
                                                                        <li class="divider"></li>
                                                                    @endif
                                                                    <li>
                                                                        <a href="javascript:;" class="set-reset-command">
                                                                            <i class="fa fa-undo text-primary" aria-hidden="true"></i>
                                                                            @lang('Reset Command')
                                                                        </a>
                                                                    </li>
                                                                    <li class="divider"></li>
                                                                    <li>
                                                                        <a href="javascript:$('#command-gps').val('');">
                                                                            <i class="fa fa-trash text-danger" aria-hidden="true"></i> @lang('Clear')
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                            <div class="btn-group" style="
                                                                border-left: 1px solid lightgray;
                                                                margin-left: 20px;
                                                                padding-left: 20px;
                                                            ">
                                                                <div class="checkbox">
                                                                    <label>
                                                                        <input name="auto-set-plate" type="checkbox" value="true" checked>
                                                                        @lang('Auto set plate')
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <label for="command-gps" class="control-label col-md-12 field-required text-right">@lang('Commands')</label>
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
                        <div class="panel panel-inverse">
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
                                    <strong>@lang('List GPS SIM') {{ $gpsReport }}</strong><br>
                                    @php
                                        $totalSkypatrol = $simGPSList->filter(function ($simGPS, $key){ return $simGPS->isSkypatrol(); })->count();
                                        $totalCoban = $simGPSList->filter(function ($simGPS, $key){ return $simGPS->isCoban(); })->count();
                                    @endphp
                                    <small class="text-bold">{{ count($simGPSList) }} @lang('assigned vehicles')</small><br>
                                    <small class="text-bold">{{ count($unAssignedVehicles) }} @lang('unassigned vehicles')</small>
                                </h4>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped table-td-valign-middle table-hover table-report">
                                    <thead>
                                    <tr class="inverse">
                                        <th class="text-center">
                                            <i class="icon-list"></i><br>
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-car"></i><br>
                                            @lang('Vehicle')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-podcast"></i><br>
                                            @lang('GPS Type')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-tag"></i><br>
                                            @lang('Imei')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-phone"></i><br>
                                            @lang('GPS SIM')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-calendar"></i><br>
                                            @lang('Created')<hr class="hr">@lang('Updated')
                                        </th>
                                        <th class="text-center">
                                            <i class="fa fa-cogs"></i><br>
                                            @lang('Actions')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($simGPSList as $simGPS)
                                        @php( $vehicle = $simGPS->vehicle )
                                        @php( $gpsVehicle = $vehicle->gpsVehicle )
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
                                <input type="hidden" id="create-register" value="">
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
                                                            <label for="vehicle_id" class="control-label field-required">
                                                                @lang('Vehicle')
                                                            </label>
                                                            <i class="fa fa-car"></i>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <select id="vehicle_id" name="vehicle_id" class="default-select2 form-control input-sm" title="@lang('Vehicle')"
                                                                    onchange="$('#create-imei').val($(this).find('option:selected').data('plate'))">
                                                                <option value="">@lang('Select an option')</option>
                                                                @foreach( $unAssignedVehicles as $vehicle )
                                                                    <option data-plate="{{ $vehicle->plate }}" value="{{ $vehicle->id }}">{{ "#$vehicle->number | $vehicle->plate" }}</option>
                                                                @endforeach
                                                            </select>
                                                            <small>{{ count($unAssignedVehicles) }} @lang('unassigned vehicles')</small>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <label for="gps_type" class="control-label field-required">
                                                                @lang('GPS Type')
                                                            </label>
                                                            <i class="fa fa-podcast"></i>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <select id="gps_type" name="gps_type" class="default-select2 form-control input-sm" title="@lang('GPS type')">
                                                                @foreach( \App\Models\Vehicles\SimGPS::DEVICES as $device )
                                                                    <option value="{{ $device }}" {{ $device == $gpsReport?'selected':'' }} data-reset-command="{{ \App\Models\Vehicles\SimGPS::RESET_COMMAND[ $device ] }}">
                                                                        {{ $device }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <label for="imei" class="control-label field-required">
                                                                @lang('Imei')
                                                            </label>
                                                            <i class="fa fa-tag"></i>
                                                        </div>
                                                        <div class="input-group col-md-7">
                                                            <div class="form-group has-success has-feedback m-b-0">
                                                                <input id="create-imei" name="imei" type="text" class="form-control input-sm" value="" placeholder="Imei" style="border-radius: 50px">
                                                                <span class="fa fa-tag form-control-feedback"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="form-group">
                                                        <div class="text-right col-md-5">
                                                            <label for="sim" class="control-label field-required">
                                                                @lang('GPS SIM')
                                                            </label>
                                                            <i class="fa fa-phone"></i>
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
                                            <button type="submit" class="btn width-100 btn-lime btn-rounded btn-sm" onclick="$('#create-register').val(true)">
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
    hideSideBar();
    var editedSIM = false;
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
                    $('.tab-console-log').tab('show');
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
                    editedSIM = true;
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
                        editedSIM = true;
                        $('.modal').modal('hide');
                        setTimeout(function(){
                            $('.form-search-report').submit();
                        },1000);
                    }else{
                        gerror(data.message);
                    }
                },
                error:function () {
                    gerror('@lang('An error occurred in the process. Contact your administrator')');
                },
                complete:function(){

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

    var gpsType = $('#gps-type');
    gpsType.val( $("#gps-report").val() );
    gpsType.data('reset-command', $('#gps-report option:selected').data('reset-command'));
</script>