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
        <li class="active">@lang('Mileage')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-bus animated" aria-hidden="true"></i> @lang('Vehicles Report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Mileage') - @lang('Daily')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-vehicle-mileage-show') }}">
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
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(Auth::user()->canSelectRouteReport())
                            <div class="col-md-3">
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
    @include('template.google.maps')
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <script type="application/javascript">
<<<<<<< HEAD:resources/views/reports/vehicles/mileage/index.blade.php
        $('.menu-report-vehicles, .menu-report-vehicles-mileage').addClass('active-animated');
        const form = $('.form-search-report');
        const reportContainer = $('.report-container');

=======
        $('.menu-report-vehicles, .menu-report-vehicles-mileage, .menu-report-vehicles-mileage-daily').addClass('active-animated');
        const form = $('.form-search-report');
        const reportContainer = $('.report-container');
>>>>>>> 24b5c16dbc4f2df89d567548a11deef6fefb1fe2:resources/views/reports/vehicles/mileage/daily/index.blade.php
        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            reportContainer.empty().hide().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

<<<<<<< HEAD:resources/views/reports/vehicles/mileage/index.blade.php
            $('#route-report, #date-report').change(function () {
                reportContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

=======
>>>>>>> 24b5c16dbc4f2df89d567548a11deef6fefb1fe2:resources/views/reports/vehicles/mileage/daily/index.blade.php
            $('body').on('click', '.btn-show-address', function () {
                var el = $(this);
                el.attr('disabled', true);
                el.find('span').hide();
                el.find('i').removeClass('hide');
                $($(this).data('target')).load($(this).data('url'), function (response, status, xhr) {
                    el.attr('disabled', false);
                    if (status === "error") {
                        if (el.hasClass('second-time')) {
                            el.removeClass('second-time');
                        } else {
                            el.addClass('second-time', true).click();
                        }
                    } else {
                        el.fadeOut(1000);
                    }
                });
<<<<<<< HEAD:resources/views/reports/vehicles/mileage/index.blade.php
            });

            $('body').on('click', '.accordion-vehicles', function () {
=======
            }).on('click', '.accordion-vehicles', function () {
>>>>>>> 24b5c16dbc4f2df89d567548a11deef6fefb1fe2:resources/views/reports/vehicles/mileage/daily/index.blade.php
                $($(this).data('parent'))
                    .find('.collapse').collapse('hide')
                    .find($(this).data('target')).collapse('show');
            }).on('keyup', '.search-vehicle-list', function () {
                var vehicle = $(this).val();
                if (is_not_null(vehicle)) {
                    $('.vehicle-list').slideUp("fast", function () {
                        $('#vehicle-list-' + vehicle).slideDown();
                    });
                } else {
                    $('.vehicle-list').slideDown();
                }
            });

            @if(Auth::user()->isAdmin())
<<<<<<< HEAD:resources/views/reports/vehicles/mileage/index.blade.php
                $('#company-report').change(function () {
                    loadSelectRouteReport($(this).val());
                    reportContainer.slideUp(100);
                }).change();
=======
            $('#company').change(function () {
                reportContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            }).change();
>>>>>>> 24b5c16dbc4f2df89d567548a11deef6fefb1fe2:resources/views/reports/vehicles/mileage/daily/index.blade.php
            @endif
        });
    </script>
@endsection
