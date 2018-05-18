@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Administration')</a></li>
        <li><a href="javascript:;">@lang('Proprietaries')</a></li>
        <li class="active">@lang('Manage')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-cogs" aria-hidden="true"></i> @lang('Administration')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Manage proprietaries')</small>
    </h1>
    <hr class="col-md-12 hr">
    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('admin-proprietaries-show') }}" style="z-index: 999;">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-form">
                        <i class="fa fa-search"></i> @lang('Search')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        @if(Auth::user()->isAdmin())
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="company" class="control-label field-required">@lang('Company')</label>
                                    <div class="form-group">
                                        <select name="company" id="company" class="default-select2 form-control col-md-12">
                                            <option value="null">@lang('Select an option')</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            @endforeach
                                        </select>
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
        var animateSubmit = true;
        var mainContainer = $('.main-container');
        var form = $('.form-search-report');

        $('.menu-administration-vehicles, .menu-administration-vehicles-maintenance').addClass('active');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-form').addClass(loadingClass);
                    if (animateSubmit) mainContainer.slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            if (animateSubmit){
                                mainContainer.hide();
                                mainContainer.empty();
                            }
                            mainContainer.html(data);
                            if (animateSubmit) mainContainer.fadeIn();
                        },
                        complete:function(){
                            form.find('.btn-search-form').removeClass(loadingClass);
                            animateSubmit = true;
                        }
                    });
                }
            });

            $('#company').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });
        });
    </script>
@endsection