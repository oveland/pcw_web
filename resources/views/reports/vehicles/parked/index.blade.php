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
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Vehicles')</a></li>
        <li class="active">@lang('Parked Report')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-bus" aria-hidden="true"></i> @lang('Vehicles Report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Parked Report')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-vehicle-parked-search-report') }}">
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
                            <div class="col-md-2">
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

                        @if(Auth::user()->canSelectRouteReport())
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.routes', compact('routes'), ['withAll' => true])
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="col-md-2">
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

                        <div class="col-md-2 date-end-container" style="display: none;">
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
        $('.menu-report-vehicles, .menu-report-vehicles-parked').addClass('active-animated');

        const form = $('.form-search-report');
        const mainContainer = $('.report-container');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    mainContainer.slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
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

            $('#route-report, #date-report, #date-end-report').change(function () {
                mainContainer.slideUp();
            });

            $('body').on('click', '.btn-show-address', function () {
                var el = $(this);
                el.attr('disabled', true);
                el.find('span').hide();
                el.find('i').removeClass('hide');
                $($(this).data('target')).load($(this).data('url'), function (response, status, xhr) {
                    el.attr('disabled', false);
                    if (status == "error") {
                        if (el.hasClass('second-time')) {
                            el.removeClass('second-time');
                        } else {
                            el.addClass('second-time', true).click();
                        }
                    } else {
                        el.fadeOut(1000);
                    }
                });
            });

            $('#route-report').change(function () {
                loadSelectVehicleReportFromRoute($(this).val());
                mainContainer.slideUp(100);
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), true);
                    loadSelectRouteReport($(this).val());
                    mainContainer.slideUp(100);
                }).change();
            @else
                $('#route-report').change();
            @endif
        });

        $('#with-end-date').change(function(){
            const dec =  $('.date-end-container').slideUp();
            if ($(this).is(':checked')) {
                dec.slideDown().val( '2020-10-08' );
            }
        });
    </script>
@endsection
