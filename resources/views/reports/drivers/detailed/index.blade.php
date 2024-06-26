@extends('layout')

@section('stylesheets')
    <style>
        .icons-info-drivers .badge{
            position: relative !important;
            font-size: 55% !important;
            top: -35px !important;
            right: 0px !important;
        }

        .icons-info-drivers > span {
            width: 25px !important;
            float: left !important;
            height: 1px !important;
            top: 4px !important;
            left: 5px !important;
            position: relative !important;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Drivers')</a></li>
        <li class="active">@lang('Detailed per day')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="ion-ios-people" aria-hidden="true"></i> @lang('Drivers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Detailed per day')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-drivers-detailed-search') }}">
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
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="14">ALAMEDA</option>
                                            @if(false)
                                                <option value="null">@lang('Select an option')</option>
                                                @foreach($companies as $company)
                                                        <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="driver-report" class="control-label">@lang('Driver')(es)</label>
                                <div class="form-group">
                                    <select name="driver-report[]" id="driver-report" class="default-select2 form-control col-md-12" multiple>
                                        @foreach(\App\Models\Drivers\Driver::where('company_id',14)->get() as $driver)
                                            <option value="{{$driver->code}}">#{{ $driver->code }} | {{ $driver->fullName() }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="date-report" class="control-label field-required">@lang('Date')</label>
                                <div class="input-group date datepicker" data-less="true" data-than="#final-date">
                                    <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
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

    <div class="modal modal-message fade" id="modal-drivers-report-report">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="width: 90%">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                        <i class="fa fa-times"></i>
                    </button>
                    <div class="row">
                        <blockquote class="m-0">
                            <h3 class="m-3">@lang('Drivers report')</h3>
                        </blockquote>
                        <hr class="col-md-12 col-xs-12 col-sm-12 p-0">
                    </div>
                </div>
                <div class="modal-body" style="width:90%;">
                    <div class="row">
                        <div class="col-md-12 p-5">

                        </div>
                    </div>
                </div>
                <div class="modal-footer hide" style="width:90%;">
                    <a href="javascript:;" class="btn width-100 btn-danger" data-dismiss="modal">@lang('Close')</a>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')
    <script type="application/javascript">
        $('.menu-drivers, .menu-drivers-detailed').addClass('active-animated');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                var form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report,#driver-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#date-report, #route-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('body').on('click', '.btn-show-address', function () {
                var el = $(this);
                el.attr('disabled', true);
                el.find('span').hide();
                el.find('i').removeClass('hide');
                $($(this).data('target')).load($(this).data('url'), function (response, status, xhr) {
                    console.log(status);
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

            @if(!Auth::user()->isAdmin())
            loadRouteReport(null);
            @endif
        });

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('general-load-select-routes') }}', {
                company: company
            }, function () {
                routeSelect.trigger('change.select2');
            });
        }
    </script>
@endsection
