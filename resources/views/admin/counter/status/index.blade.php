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
                                    <select name="type-report" id="type-report" class="default-select2 form-control col-md-12">
                                        <option value="history">@lang('Historic')</option>
                                        <option value="issues">@lang('Of issues')</option>
                                        <option value="route">@lang('By route')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 form-by-route" style="display: none">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="input-group btn-block">
                                    <select name="route-report" id="route-report" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 form-by-route" style="display: none">
                            <div class="form-group">
                                <label for="route-report-date" class="control-label field-required">@lang('Date')</label>
                                <div class="input-group date datepicker">
                                    <input name="route-report-date" id="route-report-date" type="text" class="form-control" placeholder="@lang('Date')" value="{{ date('Y-m-d') }}"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 form-date-range">
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
                        <div class="col-md-3 form-date-range">
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
    <script src="https://cdn.jsdelivr.net/npm/clipboard@1/dist/clipboard.min.js"></script>

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

            $('#company-report,#route-report,#route-report-date').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#company-report').change(function () {
                loadRouteReport($(this).val());
            });

            $('#type-report').change(function () {
                var typeReport = $(this).val();
                var formByRoute = $('.form-by-route');
                var formDateRange = $('.form-date-range');

                if( typeReport === 'route' ){
                    formDateRange.hide();
                    formByRoute.fadeIn();
                }else{
                    formByRoute.hide();
                    formDateRange.fadeIn();
                }
            });

            var clipboard = new Clipboard('.btn-copy');

            clipboard.on('success', function (e) {
                gsuccess("@lang('Text copied'):" + e.text);
                e.clearSelection();
            });

            @if(!Auth::user()->isAdmin())
            loadRouteReport(null);
            @endif
        });

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('route-ajax-action') }}', {
                option: 'loadRoutes',
                company: company
            }, function () {
                routeSelect.trigger('change.select2');
            });
        }
    </script>
@endsection
