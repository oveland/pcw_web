@extends('layouts.app')

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
        .badge{
            padding: 3px 7px !important;
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
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Mileage')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-vehicle-mileage-show-date-range') }}">
            <input type="hidden" name="export" class="export" value="false">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report" onclick="$('.export').val(false);$('.form-search-report').attr('target', '_self')">
                        <i class="fa fa-search"></i> @lang('Search')
                    </button>
                    <button type="submit" class="btn btn-lime btn-sm btn-search-report" onclick="$('.export').val(true);$('.form-search-report').attr('target', '_blank')">
                        <i class="fa fa-file-excel-o"></i> @lang('Export')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="company" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company" id="company" class="default-select2 form-control col-md-12">
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
                                    <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12">
                                        @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="initial-date-report" class="control-label field-required">@lang('Initial date')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="initial-date-report" id="initial-date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->subDays(1)->toDateString() }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="final-date-report" class="control-label field-required">@lang('Final date')</label>
                                <div class="input-group date" id="datetimepicker-report">
                                    <input name="final-date-report" id="final-date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->subDays(1)->toDateString() }}"/>
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
    @include('layouts.template.google.maps')
    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/sparkline/jquery.sparkline.min.js') }}"></script>

    <script type="application/javascript">
        $('.menu-report-vehicles, .menu-report-vehicles-mileage, .menu-report-vehicles-mileage-date-range').addClass('active-animated');
        const form = $('.form-search-report');
        const reportContainer = $('.report-container');
        $(document).ready(function () {
            form.submit(function (e) {
                if( $('.export').val() === "false" ){
                    e.preventDefault();
                }

                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            if( $('.export').val() === "false" ) {
                                reportContainer.empty().hide().html(data);
                            }
                            reportContainer.fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

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
            });

            $('#vehicle-report, #initial-date-report, #final-date-report').change(function(){
                reportContainer.slideUp(100);
            });

            @if(Auth::user()->isAdmin())
                $('#company').change(function () {
                    loadSelectVehicleReport($(this).val(), true);
                }).change();
            @else
            loadSelectVehicleReport(1, true);
            @endif
        });
    </script>
@endsection
