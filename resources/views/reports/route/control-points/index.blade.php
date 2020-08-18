@extends('layout')

@section('stylesheets')
    <style>
        .table-report-control-point th{
            text-align: center !important;
        }
        .table-report-control-point th i{
            font-size: 200% !important;
        }
        .icon-vehicle-status{
            position: sticky;
            text-shadow: white 1px 1px 4px;
        }

        .report-tab-cp {
            font-size:80% !important;
            position:relative;
            top:-3px;
        }

        .report-tab-cp button span{
            font-size:80% !important;
            position:relative;
            top:-3px;
            padding: 5px
        }

        .report-tab-cp button{
            padding-left:2px;
            padding-right:2px;
            height:15px
        }

        .report-tab-cp th {
            padding:8px !important;
        }

        .report-tab-cp td {
            padding:5px !important;
        }

        .report-tab-cp .bg-warning{
            color:white !important;
        }
        .td-info{
            -webkit-box-shadow: inset 0px 0px 32px -12px rgba(255,255,255,1);
            -moz-box-shadow: inset 0px 0px 32px -12px rgba(255,255,255,1);
            box-shadow: inset 0px 0px 32px -12px rgba(255,255,255,1);
        }
    </style>

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Control Points')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <i class="fa fa-map-marker"></i>
        @lang('Route report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Control Points')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-route-control-points-search-report') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search report')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-2" style="width: 200px !important;">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report"
                                                class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2" style="width: 200px !important;">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">
                                    @lang('Date')
                                </label>
                                <label class="text-bold">
                                    &nbsp;| <input id="with-end-date" name="with-end-date" type="checkbox"> @lang('Range')
                                </label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2 p-l-0 date-end-container" style="display: none; width: 200px !important;">
                            <div class="form-group">
                                <label for="date-end-report" class="control-label">@lang('Date end')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-end-report" id="date-end-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-1" style="width: 180px !important;">
                            <div class="form-group">
                                <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                <div class="form-group">
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                        @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="col-md-4 p-l-0">
                                <div class="form-group">
                                    <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report"
                                                class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select a company')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="fringe-report" class="control-label">@lang('Fringe')</label>
                                    <div class="form-group">
                                        <select name="fringe-report" id="fringe-report"
                                                class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select a route')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4 p-r-0">
                                <div class="form-group">
                                    <label for="control-point-report" class="control-label">@lang('Control points')</label>
                                    <div class="form-group">
                                        <select name="control-points-report[]" id="control-point-report" multiple
                                                class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select a route')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3 hide">
                            <div class="form-group">
                                <label for="type-report" class="control-label field-required">
                                    @lang('Type of report') @lang('by')
                                </label>
                                <div class="form-group">
                                    <select name="type-report" id="type-report" class="default-select2 form-control col-md-12">
                                        <option value="all">@lang('All')</option>
                                        <option disabled value="vehicle">@lang('Vehicles')</option>
                                        <option disabled value="round-trip">@lang('Round trips')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2 col-sm-4 col-xs-12">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="active-vehicles" class="vehicle-options" name="ascendant" type="checkbox" value="active-vehicles"> @lang('Ascendant')
                                            </label>
                                            <label class="text-bold">
                                                <input id="exclude-in-repair" class="vehicle-options" name="paint-profile" type="checkbox" value="exclude-in-repair"> @lang('Paint profile')
                                            </label>
                                            <label class="text-bold">
                                                <input id="show-details" class="vehicle-options" name="show-details" type="checkbox" value="show-details"> @lang('Show details')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
            </div>
        </form>
        <!-- end search form -->
        <!-- begin content report -->
        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->

    <!-- Include template for show modal report with char and historic route coordinates -->
    @include('reports.route.route.templates.chart._chartModal')
    <!-- end template -->

@endsection


@section('scripts')

    <script type="application/javascript">
        $('.menu-routes, .menu-report-control-points').addClass('active-animated');

        let form = $('.form-search-report');
        let reportContainer = $('.report-container');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);

                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            reportContainer.empty().hide().html(data).fadeIn();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#date-report, #type-report, #company-report, .vehicle-options').change(function () {
                reportContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#route-report, #vehicle-report').change(function () {
                //loadSelectVehicleReportFromRoute($(this).val());
                reportContainer.slideUp(100);
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), true);
                    loadSelectRouteReport($(this).val());
                    reportContainer.slideUp(100);
                }).change();
            @else
                loadSelectRouteReport(null);
            @endif

            $('#route-report').change(function () {
                loadSelectControlPointReport($(this).val());
                loadSelectFringesReport($(this).val());
                reportContainer.slideUp(100);
            }).change();
        });

        $('#with-end-date').change(function(){
            const dec =  $('.date-end-container').slideUp();
            if ($(this).is(':checked')) {
                dec.slideDown().val( '2020-10-08' );
            }
        });
    </script>
@endsection
