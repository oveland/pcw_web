@extends('layouts.app')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Geolocation report')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Geolocation report')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-passengers-geolocation-search') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company first')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 form-container-route">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a vehicle first')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 form-container-route">
                            <div class="form-group">
                                <label for="route-round-trip-report" class="control-label field-required">@lang('Round trip')</label>
                                <div class="form-group">
                                    <select name="route-round-trip-report" id="route-round-trip-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a route first')</option>
                                    </select>
                                    <input type="hidden" id="dispatch-register-id" name="dispatch-register-id" value="">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12 p-5">
            @include('reports.passengers.geolocation.templates.geolocationPassengerMap')
        </div>
        <!-- end content report -->
    </div>
    <!-- end row -->

@endsection


@section('scripts')
    @include('layouts.template.google.maps')
    @include('reports.passengers.geolocation.templates.geolocationScript')

    <script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>
    <script type="application/javascript">
        $('.menu-passengers, .menu-passengers-geolocation').addClass('active-animated');
        var mainContainer = $('.report-container');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                var form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    mainContainer.slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        dataType:'json',
                        success: function (report) {
                            mainContainer.fadeIn();
                            GeolocationPassengerReport.processPassengerReportData(report);
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                mainContainer.slideUp();
                loadSelectVehicleReport($(this).val());
            });

            $('#company-report').change();

            $('#route-round-trip-report').change(function () {
                mainContainer.slideUp();
                var dispatchRegister = $(this).find('option:selected').data('dispatch-register-id');
                $('#dispatch-register-id').val(dispatchRegister);
                var form = $('.form-search-report');
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#vehicle-report, #date-report').change(function () {
                mainContainer.slideUp();
                var dateReport = $('#date-report');
                var routeReport = $('#route-report');
                var vehicleReport = $('#vehicle-report');

                routeReport.html($('#select-loading').html()).trigger('change.select2');
                if( is_not_null(vehicleReport.val()) && is_not_null(dateReport.val()) ){
                    $('.form-container-route').slideDown();
                    routeReport.load('{{ route('general-load-select-routes') }}',{
                        vehicle: vehicleReport.val(),
                        date: dateReport.val()
                    },function () {
                        routeReport.trigger('change.select2');
                    });
                }else{
                    $('.form-container-route').slideUp();
                }
            });

            $('#route-report').change(function () {
                mainContainer.slideUp();
                loadSelectRouteRoundTrips();
            });

            var clipboard = new Clipboard('.btn-copy');

            clipboard.on('success', function (e) {
                gsuccess("@lang('Text copied'):" + e.text);
                e.clearSelection();
            });

            @if(!Auth::user()->isAdmin())
                loadSelectVehicleReport(1);
            @endif

            setTimeout(function(){
                initializeMap();
                mainContainer.slideUp();
            },500);
        });
    </script>
@endsection
