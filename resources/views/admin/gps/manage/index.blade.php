@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Administration')</a></li>
        <li><a href="javascript:;">@lang('GPS')</a></li>
        <li class="active">@lang('Manage')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-cogs" aria-hidden="true"></i> @lang('Administration')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Manage GPS')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('admin-gps-manage-list') }}">
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
                                    <label for="company-report" class="control-label field-required text-bold">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="">@lang('Select a company')</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @if(Auth::user()->isAdmin())
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="route-report" class="control-label">@lang('Route')</label>
                                    <div class="form-group">
                                        <select name="route-report" id="route-report"
                                                class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select a company')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @else
                            <input type="hidden" name="route-report" id="route-report" value="all"/>
                        @endif
                    </div>

                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin() || true)
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="gps-report" class="control-label field-required text-bold">@lang('GPS')</label>
                                    <div class="form-group">
                                        <select name="gps-report" id="gps-report" class="default-select2 form-control col-md-12">
                                            @if(Auth::user()->isSuperAdmin())
                                                <option value="all" data-reset-command="">@lang('All')</option>
                                            @endif
                                            @foreach( \App\Models\Vehicles\SimGPS::DEVICES as $device )
                                                <option value="{{ $device }}" data-reset-command="{{ \App\Models\Vehicles\SimGPS::RESET_COMMAND[ $device ] }}">{{ $device }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="col-md-12">
                                    <div class="col-md-2">
                                        @if(Auth::user()->isSuperAdmin2())
                                        <label for="show-vehicle-ready" class="text-bold">
                                            @lang('Limbo')
                                        </label>
                                        @endif
                                    </div>

                                    <div class="col-md-10">
                                        <label for="show-vehicle-ready" class="text-bold">
                                            @lang('Selection')
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        @if(Auth::user()->isSuperAdmin2())
                                            <div class="radio m-0 m-b-5">
                                                <label>
                                                    <input type="radio" name="limbo" value="si"> Si
                                                </label>
                                                <label>
                                                    <input type="radio" name="limbo" value="no" checked> No
                                                </label>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="none" checked> @lang('None')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="all"> @lang('All')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="ok"> @lang('OK')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="no-report"> @lang('NR')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="new"> @lang('GPS') @lang('New')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
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

        $('.menu-administration-gps, .menu-administration-gps-manage').addClass('active-animated');

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
                            if( is_not_null($('#create-register').val()) ){
                                setTimeout(function(){
                                    $('a[href="#tab-2"]').click();
                                },400);
                            }
                            mainContainer.empty().hide().html(data).fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#company-report,#gps-report,input[name="option-selection"]').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            form.submit();
        });

        setTimeout(function(){
            setInterval(function () {
                $('#sim-gps').change();
            },10000);
        },1000);


        $('#company-report').change(function () {
            loadRouteReport($(this).val());
        });

        @if(!Auth::user()->isAdmin())
        loadRouteReport(null);
        @endif

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('route-ajax-action') }}', {
                option: 'loadRoutes',
                company: company
            }, function () {
                routeSelect.find('option[value=""]').remove();
                routeSelect.prepend("<option value='all'>@lang('All Routes')</option>");
                routeSelect.val('all').change();
            });
        }
    </script>
@endsection
