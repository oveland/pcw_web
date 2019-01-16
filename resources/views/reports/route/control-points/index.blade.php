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
        }

        .report-tab-cp {
            font-size:70%;
            position:relative;
            top:-3px;
        }

        .report-tab-cp button span{
            font-size:80%;
            position:relative;
            top:-3px;
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
    <hr class="col-md-12 hr">
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
                            <div class="col-md-3">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date-report"
                                       class="control-label field-required">@lang('Date report')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="date-report" id="date-report" type="text" class="form-control"
                                           placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
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
        $('.menu-routes, .menu-report-control-points').addClass('active');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);

                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#date-report, #type-report, #route-report, #company-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#company-report').change(function () {
                loadRouteReport($(this).val());
            });

            @if(!Auth::user()->isAdmin())
            loadRouteReport(null);
            @endif
        });

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('route-ajax-action') }}', {
                option: 'loadRoutes',
                company: company
            }, function () {
                routeSelect.trigger('change.select2');
            });
        }
    </script>
@endsection
