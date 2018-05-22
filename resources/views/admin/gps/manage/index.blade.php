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
    <hr class="col-md-12 hr">
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
                        @if(Auth::user()->isAdmin() || Auth::user()->id == 999459 || Auth::user()->id == 841403)
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company-report" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company-report" id="company-report" class="default-select2 form-control col-md-12">
                                            <option value="any">@lang('Any GPS')</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-8">
                                <div class="col-md-12">
                                    <div class="col-md-12">
                                        <label for="show-vehicle-ready">
                                            @lang('Selection')
                                        </label>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="none" checked> @lang('None')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="all-skypatrol"> @lang('Skypatrol')
                                            </label>
                                        </div>
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="all-coban"> @lang(' Coban')
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="ready"> @lang('Ready')
                                            </label>
                                        </div>
                                        <div class="radio m-0 m-b-5">
                                            <label>
                                                <input type="radio" name="option-selection" value="unready"> @lang('Unready')
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

        $('.menu-administration-gps, .menu-administration-gps-manage').addClass('active');

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

            $('#company-report,input[name="option-selection"]').change(function () {
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
    </script>
@endsection
