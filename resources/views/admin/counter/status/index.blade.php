@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Administration')</a></li>
        <li><a href="javascript:;">@lang('Counter')</a></li>
        <li class="active">@lang('Report')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-cogs" aria-hidden="true"></i> @lang('Administration')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Report Counter')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('admin-counter-status-list') }}">
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
                                            <option value="null">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="type-report" class="control-label field-required">@lang('Type of report')</label>
                                <div class="input-group btn-block">
                                    <select name="type-report" id="type-report" type="text" class="default-select2 form-control col-md-12" placeholder="@lang('Type of report')">
                                        <option value="issues">@lang('Issues')</option>
                                        <option value="history">@lang('History')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="initial-date" class="control-label field-required">@lang('Initial date')</label>
                                <div class="input-group date date-time-picker-report">
                                    <input name="initial-date" id="initial-date" type="text" class="form-control" placeholder="@lang('Initial date')"
                                           value="{{ date('Y-m-d H:i:s') }}"/>
                                           {{--value="2018-02-18 00:30:00"/>--}}
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="final-date" class="control-label field-required">@lang('Final date')</label>
                                <div class="input-group date date-time-picker-report">
                                    <input name="final-date" id="final-date" type="text" class="form-control" placeholder="@lang('Final date')"
                                           value="{{ date('Y-m-d') }} 20:00:00"/>
                                            {{--value="2018-02-18 00:50:00"/>--}}
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
        <div class="main-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script type="application/javascript">
        var mainContainer = $('.main-container');
        var form = $('.form-search-report');

        $('.menu-administration-counter, .menu-administration-counter-status').addClass('active');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    mainContainer.slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
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

            $('#company-report').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });
        });
    </script>
@endsection
