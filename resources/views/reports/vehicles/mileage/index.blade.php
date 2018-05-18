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
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Mileage')</small>
    </h1>
    <hr class="col-md-12 hr">
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
                            <div class="col-md-4">
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
                        <div class="col-md-4">
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
        $('.menu-report-vehicles, .menu-report-vehicles-mileage').addClass('active');
        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                var form = $(this);
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                            //hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report, #date-report').change(function () {
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

            $('#company-report').change();

            $('body').on('click', '.accordion-vehicles', function () {
                $($(this).data('parent'))
                    .find('.collapse').collapse('hide')
                    .find($(this).data('target')).collapse('show');
            });

            $('body').on('keyup', '.search-vehicle-list', function () {
                var vehicle = $(this).val();
                if (is_not_null(vehicle)) {
                    $('.vehicle-list').slideUp("fast", function () {
                        $('#vehicle-list-' + vehicle).slideDown();
                    });
                } else {
                    $('.vehicle-list').slideDown();
                }
            });
        });
    </script>
@endsection
