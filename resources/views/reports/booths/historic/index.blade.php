@extends('layouts.app')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Booths')</a></li>
        <li class="active">@lang('Historic')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-cogs" aria-hidden="true"></i> @lang('Reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Booths historic report')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-booths-search') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
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
                                            <option value="null">@lang('Select a company')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                        @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 form-date-range">
                            <div class="form-group">
                                <label for="initial-date" class="control-label field-required">@lang('Date range')</label>

                                <div class="row row-space-10">
                                    <div class="col-xs-6 date">
                                        <input name="initial-date" id="initial-date" type="text" class="form-control date-time-picker-report" placeholder="@lang('Initial date')" value="{{ date('Y-m-d H:i:s') }}"/>
                                    </div>
                                    <div class="col-xs-6 date">
                                        <input name="final-date" id="final-date" type="text" class="form-control date-time-picker-report" placeholder="@lang('Final date')" value="{{ date('Y-m-d') }} 20:00:00"/>
                                    </div>
                                </div>

                                <div class="input-group hide">
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
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
        <div class="main-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>

    <script type="application/javascript">
        var mainContainer = $('.main-container');
        var form = $('.form-search-report');

        $('.menu-booths, .menu-booths-historic').addClass('active-animated');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    mainContainer.slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            mainContainer.empty().hide().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report, #initial-date, #final-date, #vehicle-report').change(function () {
                setTimeout(function(){
                    mainContainer.slideUp();
                    if (form.isValid(false)) {
                        form.submit();
                    }
                },500);
            });

            $('#company-report').change(function () {
                mainContainer.slideUp();
                loadSelectVehicleReport($(this).val());
            });

            var clipboard = new Clipboard('.btn-copy');

            clipboard.on('success', function (e) {
                gsuccess("@lang('Text copied'):" + e.text);
                e.clearSelection();
            });

            @if(!Auth::user()->isAdmin())
                loadSelectVehicleReport(1);
            @else
                $('#company-report').change();
            @endif
        });

        function loadSelectRouteRoundTripsReport() {
            var route = $('#route-report').val();
            var vehicle = $('#vehicle-report').val();
            var date = $('#route-date-report').val();

            var routeRoundTripReport = $('#route-round-trip-report');
            if( is_not_null(route) && is_not_null(vehicle) ) {
                routeRoundTripReport.html($('#select-loading').html()).trigger('change.select2');
                routeRoundTripReport.load('{{ route('general-load-select-route-round-trips') }}', {
                    route: route,
                    vehicle: vehicle,
                    date: date
                }, function () {
                    routeRoundTripReport.trigger('change.select2');
                });
            }else{
                routeRoundTripReport.html('<option value="null">@lang('Round trip')</option>').trigger('change.select2');
            }
        }
    </script>
@endsection
