@extends( Session::has('hide-menu') ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <style>
        .issue-observations::first-letter{
            text-transform: uppercase;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Operation')</a></li>
        <li class="hide"><a href="javascript:;">@lang('Vehicles')</a></li>
        <li class="active">@lang('Vehicle issues')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-life-ring fa-spin" aria-hidden="true"></i> @lang('Operation')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Vehicle issues')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row"  style="{{ Session::has('hide-menu') ? 'height:600px !important;' : '' }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-operation" action="{{ route('operation-vehicles-issues-show') }}">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                               data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                                <i class="fa fa-minus"></i>
                            </a>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm btn-search-operation">
                            <i class="fa fa-search"></i> @lang('Search')
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
                                    <label for="date-report" class="control-label field-required">@lang('Date report')</label>
                                    <div class="input-group date" id="datetimepicker-report">
                                        <input name="date-report" id="date-report" type="text" class="form-control" placeholder="yyyy-mm-dd" value="{{ \Carbon\Carbon::now()->toDateString() }}"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-2 col-md-3 col-sm-6 col-xs-12">
                                <div class="form-group">
                                    <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                    <div class="form-group">
                                        <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </form>
        <!-- end search form -->
        <hr class="hr {{ Auth::user()->isAdmin()?'':'hide' }}">
        <!-- begin content operation -->
        <div class="main-container col-md-12"></div>
        <!-- end content operation -->
    </div>
    <!-- end row -->
@endsection


@section('scripts')

    <script type="application/javascript">
        let mainContainer = $('.main-container');
        let form = $('.form-search-operation');

        $('.menu-operation-vehicles, .menu-operation-vehicles-issues').addClass('active-animated');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-operation').addClass(loadingClass);
                    mainContainer.slideUp(100);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            mainContainer.hide().empty().html(data).fadeIn();
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-operation').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#date-report, #vehicle-report').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), true);
                    mainContainer.slideUp(100);
                }).change();
            @endif
        });
    </script>
@endsection
