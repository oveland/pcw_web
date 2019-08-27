<script type="application/javascript">
    function loadSelectRouteReport(company) {
        let routeSelect = $('#route-report');
        routeSelect.html($('#select-loading').html()).trigger('change.select2');
        routeSelect.load('{{ route('general-load-select-routes') }}', {
            company: company,
            withAll: routeSelect.data('with-all'),
            withNone: routeSelect.data('with-none'),
        }, function () {
            routeSelect.trigger('change.select2');
        });
    }

    function loadSelectDriverReport(company) {
        let driverSelect = $('#driver-report');
        if (is_not_null(company)) {
            driverSelect.html($('#select-loading').html()).trigger('change.select2');
            driverSelect.load('{{ route('general-load-select-drivers') }}', {
                company: company,
                withAll: driverSelect.data('with-all')
            }, function () {
                driverSelect.trigger('change.select2');
            });
        } else {
            driverSelect.html('<option value="null">@lang('Select an option')</option>').trigger('change.select2');
        }
    }

    function loadSelectRouteRoundTrips() {
        let dateReport = $('#date-report');
        let routeReport = $('#route-report');
        let vehicleReport = $('#vehicle-report');
        let routeRoundTripReport = $('#route-round-trip-report');

        routeRoundTripReport.html($('#select-loading').html()).trigger('change.select2');
        if (is_not_null(vehicleReport.val()) && is_not_null(dateReport.val()) && is_not_null(routeReport.val())) {
            $('.form-container-route').slideDown();
            routeRoundTripReport.load('{{ route('general-load-select-route-round-trips') }}', {
                vehicle: vehicleReport.val(),
                route: routeReport.val(),
                date: dateReport.val()
            }, function () {
                routeRoundTripReport.trigger('change.select2');
            });
        } else {
            $('.form-container-route').slideUp();
        }
    }

    function loadSelectVehicleReportFromRoute(route, defaultValue, callback) {
        let vehicleSelect = $('#vehicle-report');
        vehicleSelect.html($('#select-loading').html()).trigger('change.select2');
        vehicleSelect.load('{{ route('general-load-select-vehicles-from-route') }}', {
            route: route
        }, function () {
            vehicleSelect.prepend('<option value="" selected>@lang('Select an vehicle')</option>');

            if(defaultValue)vehicleSelect.val(defaultValue);

            vehicleSelect.trigger('change.select2');

            if (callback) callback();
        });
    }

    function loadSelectVehicleReport(company, all, defaultValue, callback) {
        let vehicleSelect = $('#vehicle-report');
        if (is_not_null(company)) {
            vehicleSelect.html($('#select-loading').html()).trigger('change.select2');
            vehicleSelect.load('{{ route('general-load-select-vehicles') }}', {
                company: company
            }, function () {
                if (all) {
                    vehicleSelect.prepend('<option value="all" selected>@lang('All')</option>');
                } else {
                    vehicleSelect.prepend('<option value="" selected>@lang('Select an option')</option>');
                }

                if(defaultValue)vehicleSelect.val(defaultValue);

                vehicleSelect.trigger('change.select2');

                if (callback) callback();
            });
        } else {
            vehicleSelect.html('<option value="null">@lang('Select an option')</option>').trigger('change.select2');
        }
    }
</script>