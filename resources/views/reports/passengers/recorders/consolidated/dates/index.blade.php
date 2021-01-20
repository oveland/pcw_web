@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Recorders')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Passengers report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Recorders')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('report-passengers-recorders-consolidated-date-range-search') }}">
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
                                            <option value="null">@lang('Select an option')</option>
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
                                        <option value="null">@lang('Select a company first')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report" data-with-all="true" class="default-select2 form-control col-md-12">
                                        <option value="null">@lang('Select a company')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="driver-report" class="control-label field-required">@lang('Driver')</label>
                                <div class="form-group">
                                    <select name="driver-report" id="driver-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                        <option value="null">@lang('Select a company first')</option>
                                    </select>
                                </div>
                            </div>
                        </div>

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

                        <div class="col-md-2 options with-route">
                            <div class="form-group">
                                <label for="grouped-report" class="control-label">@lang('Group options')</label>
                                <div class="form-group">
                                    <div class="has-warning">
                                        <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                            <label class="text-bold">
                                                <input id="group-by-date" name="group-by-date" type="checkbox" checked><i class="fa fa-calendar"></i> @lang('By date')
                                            </label>
                                            <label class="text-bold">
                                                <input id="group-by-vehicle" name="group-by-vehicle" type="checkbox" checked><i class="fa fa-bus"></i>  @lang('By vehicle')
                                            </label>
                                            <label class="text-bold">
                                                <input id="group-by-route" name="group-by-route" type="checkbox"><i class="fa fa-flag"></i> @lang('By route')
                                            </label>
                                            <label class="text-bold">
                                                    <input id="group-by-driver" name="group-by-driver" type="checkbox"><i class="fa fa-user"></i> @lang('By driver')
                                            </label>
                                        </div>
                                    </div>
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
        $('.menu-passengers, .menu-passengers-recorders, .menu-passengers-recorders-consolidated, .menu-passengers-recorders-consolidated-range').addClass('active-animated');
        const mainContainer = $('.main-container');
        const form = $('.form-search-report');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    mainContainer.empty().hide().html($('#animated-loading').html()).show();
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            mainContainer.empty().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        },
                        error: function (data) {
                            mainContainer.empty().fadeIn();
                        }
                    });
                }
            });

            $('#date-report, #date-end-report, #group-by-date, #group-by-vehicle, #group-by-route, #group-by-driver').change(function () {
                mainContainer.hide();
            });

            $('#route-report, #driver-report, #vehicle-report').change(function () {
                mainContainer.hide();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            const clipboard = new Clipboard('.btn-copy');

            clipboard.on('success', function (e) {
                gsuccess("@lang('Text copied'):" + e.text);
                e.clearSelection();
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    mainContainer.hide();
                    loadSelectRouteReport($(this).val());
                    loadSelectVehicleReport($(this).val(), true);
                    loadSelectDriverReport($(this).val());
                }).change();
            @else
                loadSelectRouteReport(null);
                loadSelectVehicleReport(1, true);
                loadSelectDriverReport(1);
            @endif

            $('#with-end-date').change(function(){
                const isChecked = $(this).is(':checked');
                const dec =  $('.date-end-container').slideUp();
                if (isChecked) {
                    dec.slideDown();
                }
            });
        });
    </script>
@endsection
