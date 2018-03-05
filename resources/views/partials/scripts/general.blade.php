<script type="application/javascript">
    function loadSelectRouteReport(company) {
        var routeSelect = $('#route-report');
        if( is_not_null(company) ){
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('general-load-select-routes') }}', {
                company: company
            }, function () {
                routeSelect.trigger('change.select2');
            });
        }else{
            routeSelect.html('<option value="null">@lang('Select an option')</option>').trigger('change.select2');
        }
    }

    function loadSelectVehicleReport(company) {
        var vehicleSelect = $('#vehicle-report');
        if( is_not_null(company) ) {
            vehicleSelect.html($('#select-loading').html()).trigger('change.select2');
            vehicleSelect.load('{{ route('general-load-select-vehicles') }}', {
                company: company
            }, function () {
                vehicleSelect.trigger('change.select2');
            });
        }else{
            vehicleSelect.html('<option value="null">@lang('Select an option')</option>').trigger('change.select2');
        }
    }
</script>