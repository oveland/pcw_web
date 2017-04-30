@extends('layout')

@section('stylesheets')
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker.css" rel="stylesheet"/>
    <link href="assets/plugins/bootstrap-datepicker/css/bootstrap-datepicker3.css" rel="stylesheet"/>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Route reports')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">@lang('Route reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Route times')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <div class="col-md-12">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="Ocultar / Mostrar">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <h4 class="panel-title">@lang('Search report')</h4>
                </div>
                <div class="panel-body p-b-15">
                    <form class="form-input-flat form-search-report">
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                <div class="form-group">
                                    <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select an option')</option>
                                        @foreach($companies as $company)
                                        <option value="{{$company->id_empresa}}">{{ $company->des_corta }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions col-md-12">
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="fa fa-search"></i> @lang('Search')
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- begin row -->

@endsection


@section('scripts')
    <script src="assets/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
    <script src="assets/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js"></script>

    <script src="assets/plugins/sparkline/jquery.sparkline.min.js"></script>
    <script type="application/javascript">
        $(document).ready(function () {
            $('#datetimepicker-report').datepicker({
                format: "yyyy-mm-dd",
                todayBtn: "linked",
                language: "es",
                orientation: "bottom auto",
                daysOfWeekHighlighted: "0,6",
                calendarWeeks: true,
                autoclose: true,
                todayHighlight: true
            });

            $('.default-select2').select2();

            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if($(this).isValid()){
                    $('.report-container').slideUp(100);
                    $.ajax({
                        url: '{{ route('search-report') }}',
                        data: $(this).serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        }
                    });
                }
            });

            $('#company-report').change(function () {
                var roouteSelect = $('#route-report');
                roouteSelect.html($('#select-loading').html()).trigger('change.select2');
                roouteSelect.load('{{route('ajax-action')}}', {
                    option: 'loadRoutes',
                    company: $(this).val()
                },function () {
                    roouteSelect.trigger('change.select2');
                });
            });
        });
    </script>
@endsection
