@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Logs')</a></li>
        <li class="active">@lang('Access Logs')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-user" aria-hidden="true"></i> @lang('Logs report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Access Logs')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-download-report" data-action="{{ route('logs-access-export',['date'=>'']) }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-download"></i> @lang('Download report')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
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
    <script type="application/javascript">
        $('.menu-logs').addClass('active');
        $(document).ready(function () {
            $('.form-download-report').submit(function () {
                event.preventDefault();
                var form = $(this);
                if (form.isValid()) {
                    var url = form.data('action');
                    var date = $('#date-report').val();

                    form.find('.btn-search-report').addClass(loadingClass);
                    setTimeout(function () {
                        form.find('.btn-search-report').removeClass(loadingClass);
                    }, 1000);

                    window.location.href = url + '/' + date;
                }
            });

            $('#date-report').change(function () {
                var form = $('.form-download-report');
                $('.report-container').slideUp();
                form.isValid(false);
            });
        });
    </script>
@endsection
