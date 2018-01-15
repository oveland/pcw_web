<form class="form form-send-message" action="{{ route('admin-gps-manage-send-sms') }}">
    {{ csrf_field() }}
    <div class="row">
        <div class="form-group col-md-5 has-success has-feedback">
            <label for="sim-gps" class="control-label field-required">
                @lang('SIM GPS')
            </label>
            <div class="input-group m-b-15">
                <span class="input-group-addon"><i class="ion-android-phone-portrait fa-fw text-center f-s-17" aria-hidden="true"></i></span>
                @if( $gpsVehicles )
                    <select id="sim-gps" name="sim-gps" class="form-control default-select2">
                        @foreach($gpsVehicles as $gpsVehicle)
                            <option value="{{ $gpsVehicle->sim }}">
                                {{ $gpsVehicle->getGPSType() }}: {{ $gpsVehicle->sim }} | #{{ $gpsVehicle->vehicle->number ?? 'NONE'  }} ({{ $gpsVehicle->vehicle->plate ?? 'NONE'  }})
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
</script>