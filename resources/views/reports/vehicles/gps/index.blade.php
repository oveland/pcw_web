@extends('layout')

@section('stylesheets')
    <style>
        .table-report th{
            text-align: center !important;
        }
        .table-report th i{
            font-size: 200%;
            color: rgba(211, 211, 211, 0.16);
            position: relative;
            top: -5px;
            float: unset;
        }
        .accordion-toggle[aria-expanded="true"]{
            background:rgba(213, 208, 208, 0.14) !important;
        }
        .accordion-toggle[aria-expanded="true"]:after{
            content:'➤';
            color:rgba(211, 211, 211, 0.17);
            font-size:150%;
            position:relative;
            float:right;
            bottom:30px;
            right:8px;
        }
        .icon-vehicle-list i{
            font-size: 120% !important;
            margin: 10px;
        }
        .info-vehicle-list{
            margin-left: 70px !important;
        }
        .nav-tabs>li.active>a, .nav-tabs>li.active>a:focus, .nav-tabs>li.active>a:hover {
            color: #cabf52 !important;
        }
        .btn-clear-search{
            position: absolute !important;
            right: 50px !important;
            top: 7px;
            color: rgba(0, 0, 0, 0.14) !important;
            z-index: 1 !important;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Vehicles')</a></li>
        <li class="active">@lang('GPS')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <i class="fa fa-podcast blue faa-burst" aria-hidden="true"></i> @lang('Vehicles Report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('GPS')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-vehicle-gps-search-report') }}">
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
                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="initial-date" class="control-label field-required">@lang('Initial date')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="initial-date" id="initial-date" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->subDays(31)->toDateString() }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="final-date" class="control-label field-required">@lang('Final date')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="final-date" id="final-date" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->subDays(1)->toDateString() }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="type-report" class="control-label">@lang('Vehicle options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="active-vehicles" class="vehicle-options" name="active-vehicles" type="checkbox" value="active-vehicles" checked> @lang('Only active')
                                            </label>
                                            <label class="text-bold">
                                                <input id="exclude-in-repair" class="vehicle-options" name="exclude-in-repair" type="checkbox" value="exclude-in-repair" checked> @lang('Exclude in repair')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-12">
                            <label for="minimum-locations-daily" class="control-label field-required">@lang('Minimum locations dialy')</label>
                            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input name="minimum-locations-daily" id="minimum-locations-daily" type="number" class="form-control" value="100" max="10000"/>
                            </div>
                        </div>

                        <div class="col-lg-1 col-md-3 col-sm-6 col-xs-12">
                            <label for="minimum-percent-for-OK" class="control-label field-required">@lang('Minimum percent for OK')</label>
                            <div class="input-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <input name="minimum-percent-for-OK" id="minimum-percent-for-OK" type="number" class="form-control" value="80" max="100"/>
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
@endsection

@section('scripts')
    <script type="application/javascript">
        $('.menu-report-vehicles, .menu-report-vehicles-status').addClass('active-animated');

        let form = $('.form-search-report');
        let reportContainer = $('.report-container');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.show();
                    reportContainer.empty().html($('#animated-loading').html());
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            reportContainer.empty().hide().html(data).fadeIn();
                            //hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), true);
                    //loadSelectRouteReport($(this).val());
                    reportContainer.slideUp(100);
                }).change();
            @else
                loadSelectVehicleReport('{{ Auth::user()->company->id }}', true);
            @endif
        });
    </script>
@endsection
