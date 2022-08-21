@extends('layouts.app')

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

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-download-report" action="{{ route('report.users.activity.search') }}" data-url-export-logins="{{ route('report.users.activity.export.logins',['date'=>'']) }}">
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

                    <button type="button" class="btn btn-danger btn-sm btn-download-logins">
                        <i class="fa fa-file-pdf-o"></i> @lang('Download login report')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select an option')</option>
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
                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12 date-end-container" style="display: none">
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

                        <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                            <div class="form-group">
                                <label for="user-report" class="control-label field-required">@lang('User')</label>
                                <div class="form-group">
                                    <select name="user-report" id="user-report" class="default-select2 form-control col-md-12" data-with-all="true" data-with-name="true">
                                        @include('partials.selects.users', compact('users'), ['withAll' => true])
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
@endsection

@section('scripts')
    <script type="application/javascript">
        $('.menu-logs').addClass('active-animated');
        let form = $('.form-download-report');
        let mainContainer = $('.report-container');

        $('.menu-reports-users, .menu-reports-users-activity').addClass('active-animated');

        $(document).ready(function () {
            $(document).ready(function () {
                form.submit(function (e) {
                    e.preventDefault();
                    if (form.isValid()) {
                        form.find('.btn-search-report').addClass(loadingClass);
                        mainContainer.slideUp(100);
                        $.ajax({
                            url: form.attr('action'),
                            data: form.serialize(),
                            success: function (data) {
                                mainContainer.hide().empty().html(data).fadeIn();
                                $('.modal').modal('hide');
                                hideSideBar();
                            },
                            complete:function(){
                                form.find('.btn-search-report').removeClass(loadingClass);
                            }
                        });
                    }
                });

                // $('#date-report, #user-report').change(function () {
                //     mainContainer.slideUp();
                //     if (form.isValid(false)) {
                //         form.submit();
                //     }
                // });

                @if(Auth::user()->isAdmin())
                    $('#company-report').change(function () {
                        loadSelectUserReport($(this).val());
                        mainContainer.slideUp(100);
                    }).change();
                @endif

                $('#with-end-date').change(function(){
                    const dec =  $('.date-end-container').slideUp();
                    if ($(this).is(':checked')) {
                        dec.slideDown();
                    }
                });

                $('.btn-download-logins').click(function (){
                    downloadLogins();
                });
            });

            function downloadLogins() {
                const url = form.data('url-export-logins');
                const date = $('#date-report').val();

                form.find('.btn-search-report').addClass(loadingClass);
                setTimeout(function () {
                    form.find('.btn-search-report').removeClass(loadingClass);
                }, 1000);

                window.location.href = url + '/' + date;
            }
        });

        function prettyJson(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                var cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return '<span class="' + cls + '">' + match + '</span>';
            });
        }

        function prettyParams(element, data) {
            $(element).parent().find('button').hide();
            $(element).parent().find('small').fadeIn(2000);
            $(element).html(prettyJson(data)).slideDown();
        }
    </script>
@endsection
