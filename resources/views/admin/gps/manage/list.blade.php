<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <ul class="nav nav-tabs nav-tabs-primary nav-justified">
                @if( $simGPSList )
                <li class="active">
                    <a href="#tab-1" data-toggle="tab">
                        <i class="fa fa-podcast"></i>
                        @lang('Manage SIM GPS')
                    </a>
                </li>
                @endif
                <li class="{{ $simGPSList?"":"active" }}">
                    <a href="#tab-2" data-toggle="tab">
                        <i class="fa fa-paper-plane"></i>
                        @lang('Send Commands')
                    </a>
                </li>
            </ul>
            <div class="tab-content m-b-0">
                @if( $simGPSList )
                <div class="tab-pane fade active in" id="tab-1">
                    <!-- begin panel -->
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <div class="panel-heading-btn hide">
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-white" data-click="panel-expand"><i class="fa fa-expand"></i></a>
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-white" data-click="panel-reload"><i class="fa fa-repeat"></i></a>
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-white" data-click="panel-collapse"><i class="fa fa-minus"></i></a>
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-white" data-click="panel-remove"><i class="fa fa-times"></i></a>
                            </div>
                            <h4 class="panel-title">
                                <i class="fa fa-table" aria-hidden="true"></i>
                                @lang('List GPS SIM') - ({{ count($simGPSList) }})
                            </h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-td-valign-middle table-hover">
                                <thead>
                                <tr class="info">
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
                                    <tr id="detail-{{ $simGPS->id }}">
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
                </div>
                @endif
                <div class="tab-pane fade {{ $simGPSList?"":"active in" }}" id="tab-2">
                    <form class="form form-send-message" action="{{ route('admin-gps-manage-send-sms') }}">
                        {{ csrf_field() }}
                        <input type="hidden" id="gps-type" name="gps-type" value="">
                        <div class="row">
                            <div class="form-group col-md-5 has-success has-feedback">
                                <i class="fa fa-signal f-s-17" aria-hidden="true"></i>
                                <label for="sim-gps" class="control-label field-required">
                                    @lang('SIM GPS')
                                </label>
                                <div class="row info-gps">
                                    <div class="col-md-12">
                                        <div class="input-group m-b-15 col-md-12">
                                            @if( $simGPSList )
                                                <select id="sim-gps" name="sim-gps" class="form-control default-select2 col-md-12">
                                                    <option value="">@lang('Select an option')</option>
                                                    @foreach($simGPSList as $simGPS)
                                                        <option value="{{ $simGPS->sim }}"
                                                                data-reset-command="{{ $simGPS->getResetCommand() }}"
                                                                data-gps-type="{{ $simGPS->getGPSType() }}"
                                                                data-vehicle-id="{{ $simGPS->vehicle->id ?? null }}">
                                                            {{ $simGPS->getGPSType() }}: {{ $simGPS->sim }} | #{{ $simGPS->vehicle->number ?? 'NONE'  }} ({{ $simGPS->vehicle->plate ?? 'NONE'  }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            @else
                                                <div class="m-b-10">
                                                    <button type="button" class="btn btn-success btn-xs gps-skypatrol m-r-5"
                                                            onclick="$(this).removeClass('disabled');$('.gps-tracker').addClass('disabled');$('#sim-gps').data('gps-type','SKYPATROL')">
                                                        <i class="fa fa-podcast"></i>
                                                        @lang('SKYPATROL')
                                                    </button>
                                                    <button type="button" class="btn btn-primary btn-xs gps-tracker disabled"
                                                            onclick="$(this).removeClass('disabled');$('.gps-skypatrol').addClass('disabled');$('#sim-gps').data('gps-type','TRACKER')">
                                                        <i class="fa fa-podcast"></i>
                                                        @lang('COBAN')
                                                    </button>
                                                </div>
                                                <input id="sim-gps" name="sim-gps" type="text" class="form-control" data-any-gps="true" data-gps-type="SKYPATROL">
                                            @endif
                                        </div>
                                    </div>
                                    @if( $simGPSList )
                                        <div class="col-md-12">
                                            <label class="control-label">
                                                <i class="fa fa-podcast faa-burst animated"></i>
                                                @lang('Status GPS'):
                                            </label>
                                            <pre class="pre col-md-12 text-info status-gps-container">@lang('Select a SIM number')</pre>
                                        </div>
                                    @endif
                                    <hr class="col-md-12">
                                    <div class="col-md-12">
                                        <i class="fa fa-envelope faa-horizontal animated"></i> @lang('SMS Response')
                                        <pre class="pre col-md-12 sms-response-container hide"></pre>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md-7">
                                <label for="command-gps" class="control-label col-md-12 field-required">@lang('GPS Command')</label>
                                <div class="col-md-12">
                                    <div class="input-group-btn">
                                        <button type="button" class="btn btn-danger btn-sm set-reset-command pull-right">
                                            <i class="fa fa-undo" aria-hidden="true"></i>
                                            @lang('Reset Command')
                                        </button>

                                        <button type="submit" class="btn btn-info btn-submit m-l-2 pull-left">
                                            @lang('Send')
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                    <hr class="col-md-12">
                                    <textarea id="command-gps" name="command-gps" class="form-control pre" rows="40" placeholder="@lang('Type here the commands')"></textarea>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="application/javascript">
    $('.default-select2').select2();
    setTimeout(function () {
        $('.form-send-message .select2-container').css('width','100%');
    },100);

    $('.form-send-message').submit(function(e){
        e.preventDefault();
        var smsResponseContainer = $('.sms-response-container');
        var formSendSMS = $(this);
        if (formSendSMS.isValid()) {
            formSendSMS.find('.btn-submit').addClass(loadingClass);
            smsResponseContainer.slideUp();

            var simGPS = $('#sim-gps');
            var gpsType = simGPS.find('option[value="' + simGPS.val() + '"]').data('gps-type');
            if( simGPS.data('any-gps') ){
                gpsType = simGPS.data('gps-type');
                console.log(gpsType);
            }
            $('#gps-type').val(gpsType);

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

    $('#sim-gps').change(function(){
        var simGPS = $(this);
        var simNumber = simGPS.val();
        var statusGPSContainer = $('.status-gps-container');
        if( !simGPS.data('any-gps') ){
            statusGPSContainer.parent().slideDown();
            statusGPSContainer.html('@lang('Searching')...');
            if( is_not_null(simNumber) ){
                var vehicleId = simGPS.find('option[value="'+simGPS.val()+'"]').data('vehicle-id');
                $.ajax({
                    url: '{{ route('admin-gps-get-vehicle-status') }}',
                    data:{
                        vehicleId: vehicleId
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
        var simGPS = $('#sim-gps');
        if( is_not_null(simGPS.val()) ){
            var resetCommand = simGPS.find('option[value="'+simGPS.val()+'"]').data('reset-command');
            $('#command-gps').val(resetCommand);
        }else{
            gwarning('@lang('Select a SIM number')');
        }
    });
</script>