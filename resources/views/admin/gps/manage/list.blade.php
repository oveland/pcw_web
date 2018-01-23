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
                        <div class="row">
                            <div class="form-group col-md-5 has-success has-feedback">
                                <label for="sim-gps" class="control-label field-required">
                                    @lang('SIM GPS')
                                </label>
                                <div class="input-group m-b-15">
                                    <span class="input-group-addon"><i class="ion-android-phone-portrait fa-fw text-center f-s-17" aria-hidden="true"></i></span>
                                    @if( $simGPSList )
                                        <select id="sim-gps" name="sim-gps" class="form-control default-select2">
                                            @foreach($simGPSList as $simGPS)
                                                <option value="{{ $simGPS->sim }}">
                                                    {{ $simGPS->getGPSType() }}: {{ $simGPS->sim }} | #{{ $simGPS->vehicle->number ?? 'NONE'  }} ({{ $simGPS->vehicle->plate ?? 'NONE'  }})
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input id="sim-gps" name="sim-gps" type="number" class="form-control">
                                    @endif
                                    <div class="input-group-btn">
                                        <button type="submit" class="btn btn-info btn-submit m-l-2">
                                            @lang('Send SMS')
                                            <i class="fa fa-paper-plane" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>

                                <hr class="hr">
                                <pre class="pre col-md-12 sms-response-container hide"></pre>
                            </div>
                            <div class="form-group col-md-7">
                                <label for="command-gps" class="control-label col-md-12 field-required">@lang('GPS Command')</label>
                                <div class="col-md-12">
                                    <textarea id="command-gps" name="command-gps" class="form-control" rows="40" placeholder="@lang('Type the GPS command')"></textarea>
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
</script>