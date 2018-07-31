<div class="loading-geolocation-map" style="display: none"></div>
<div class="geolocation-map-container">
    <div class='col-md-3 pull-right recorder-passenger-info-map' style="position: relative;z-index: 2;background: rgba(255,255,255,0.8)">
        <div class='col-md-12'>
            <div class=''>
                <h5 class='text-info'><i class='fa fa-users'></i> <b>@lang('Count by round trip')</b></h5>
                <hr class='hr'>
            </div>
            <div class=''>
                <i class='fa fa-compass text-muted'></i> <b>@lang('Total') @lang('Recorder'): </b><span class="total-recorder"></span><br>
                <i class='fa fa-crosshairs text-muted'></i> <b>@lang('Total') @lang('Sensor recorder'): </b><span class="total-sensor-recorder"></span><br>
                <hr class='hr'>
            </div>
            <div class='text-bold'>
                <i class='fa fa-clock-o text-muted'></i>
                <small class='tooltips departure-time' data-title="@lang('Departure time')" data-placement="bottom">00:00:00</small>
                -
                <small class='tooltips arrival-time' data-title="@lang('Arrival time')" data-placement="bottom"></small>
                <hr class='hr'>
            </div>
        </div>
    </div>
    <div class="col-md-12 p-0" style="position: relative; top: -128px;z-index: 1;">
        <div id="google-map-light-dream" class="height-lg" style="width: 100%"></div>
    </div>
</div>