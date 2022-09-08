@extends( Session::has('hide-menu') ? 'layouts.blank' : 'layouts.app')

@section('stylesheets')
    <style>
        .issue-observations::first-letter{
            text-transform: uppercase;
        }

        .label-type {
            font-size: 1.3rem !important;
            padding: 10px;
            background: #d3d3d375;
            margin: 0;
        }
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Operation')</a></li>
        <li class=""><a href="javascript:;">@lang('Vehicles')</a></li>
        <li class="active">@lang('Memos')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-warning" aria-hidden="true"></i> @lang('Operation')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Vehicles memo')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row"  style="{{ Session::has('hide-menu') ? 'height:600px !important;' : '' }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-operation" action="{{ route('operation-vehicles-memo-show') }}">
                <div class="panel panel-">
                    <div class="panel-heading">
                        <button type="submit" class="btn btn-outline green btn-circle btn-search-operation">
                            <i class="fa fa-search"></i> @lang('Search')
                        </button>
                        <button class="btn btn-outline btn-circle green-jungle" type="button" onclick="loadMemoFormCreate()">
                            <i class="icon-bag"></i> @lang('Create')
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
                            <div class="col-md-2 date-end-container" style="display: none">
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
                                    <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                                    <div class="form-group">
                                        <select name="vehicle-report" id="vehicle-report" class="default-select2 form-control col-md-12" data-with-all="true" data-with-only-active="true">
                                            @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-2 col-sm-4 col-xs-12">
                                <div class="form-group">
                                    <label for="type-report" class="control-label">@lang('Options')</label>
                                    <div class="form-group">
                                        <div class="has-warning">
                                            <div class="checkbox" style="border: 1px solid lightgray;padding: 5px;margin: 0;border-radius: 5px;">
                                                <label class="text-bold">
                                                    <input id="sort-desc" class="vehicle-options" name="sort-desc" type="checkbox" value="true"> @lang('Sort by date desc')
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
        <hr class="hr {{ Auth::user()->isAdmin()?'':'hide' }}">
        <!-- begin content operation -->
        <div class="main-container col-md-12"></div>
        <!-- end content operation -->

        <div class="modal fade modal-memo" id="modal-memo-create" style="background: #535353;opacity: 0.96;" data-url="{{ route('operation-vehicles-memo-form-create') }}">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header p-20 bg-green-jungle">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i>
                        </button>
                        <h3 class="modal-title text-center text-white">
                            <span><i class="icon-bag"></i>@lang('Create') <span class="text-lowercase">@lang('memo')</span></span>
                        </h3>
                    </div>
                    <div class="modal-body p-t-20 p-b-0 row"></div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-memo" id="modal-memo-detail" style="background: #535353;opacity: 0.96;" data-url="{{ route('operation-vehicles-memo-form-detail', ['memo' => 'ID']) }}">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header p-20 bg-blue-dark">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i>
                        </button>
                        <h3 class="modal-title text-center text-white">
                            <span><i class="icon-bag"></i> @lang('Detail') <span class="text-lowercase">@lang('memo')</span></span>
                        </h3>
                    </div>
                    <div class="modal-body p-t-20 p-b-0 row"></div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-memo" id="modal-memo-edit" style="background: #535353;opacity: 0.96;" data-url="{{ route('operation-vehicles-memo-form-edit', ['memo' => 'ID']) }}">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header p-20 bg-yellow">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i>
                        </button>
                        <h3 class="modal-title text-center text-white">
                            <span><i class="icon-bag"></i> @lang('Edit') <span class="text-lowercase">@lang('memo')</span></span>
                        </h3>
                    </div>
                    <div class="modal-body p-t-20 p-b-0 row"></div>
                </div>
            </div>
        </div>

        <div class="modal fade modal-memo" id="modal-memo-delete" style="background: #535353;opacity: 0.96;" data-url="{{ route('operation-vehicles-memo-form-delete', ['memo' => 'ID']) }}">
            <div class="modal-dialog modal-md">
                <div class="modal-content">
                    <div class="modal-header p-20 bg-danger">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <i class="fa fa-times"></i>
                        </button>
                        <h3 class="modal-title text-center text-danger">
                            <span><i class="icon-bag"></i> @lang('Delete') <span class="text-lowercase">@lang('memo')</span></span>
                        </h3>
                    </div>
                    <div class="modal-body p-t-20 p-b-0 row"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


@section('scripts')

    <script type="application/javascript">
        let mainContainer = $('.main-container');
        let form = $('.form-search-operation');
        let modalMemoCreate = $('#modal-memo-create');
        let modalMemoDetail = $('#modal-memo-detail');
        let modalMemoEdit = $('#modal-memo-edit');
        let modalMemoDelete = $('#modal-memo-delete');

        $('.menu-operation-vehicles, .menu-operation-vehicles-memo').addClass('active-animated');

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
                            $('.modal').modal('hide');
                            hideSideBar();
                        },
                        complete:function(){
                            form.find('.btn-search-operation').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#date-report, #vehicle-report, #sort-desc, #include-completed').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();

                    $('#vehicle-memo').val($('#vehicle-report').val()).change();
                }
            });

            @if(Auth::user()->isAdmin())
                $('#company-report').change(function () {
                    loadSelectVehicleReport($(this).val(), true, null, function(){
                        $('#vehicle-memo').empty();
                        $('#vehicle-report > option:not(option[value="all"])').clone().appendTo('#vehicle-memo').change();
                    });
                    mainContainer.slideUp(100);
                }).change();
            @endif

            $('#with-end-date').change(function(){
                const dec =  $('.date-end-container').slideUp();
                if ($(this).is(':checked')) {
                    dec.slideDown();
                }
            });
        });

        function loadMemoFormCreate(formMemo){
            $('.modal-memo').find('.modal-body').empty();

            modalMemoCreate.find('.modal-body').load(modalMemoCreate.data('url'), {
                company: $('#company-report').val(),
                formMemo
            });
            modalMemoCreate.modal('show');
        }

        function loadMemoFormDetail(id){
            $('.modal-memo').find('.modal-body').empty();

            const url = modalMemoDetail.data('url').toString();
            modalMemoDetail.find('.modal-body').load(url.replace('ID', id), {
                company: $('#company-report').val()
            });
            modalMemoDetail.modal('show');
        }

        function loadMemoFormEdit(id){
            $('.modal-memo').find('.modal-body').empty();

            const url = modalMemoEdit.data('url').toString();
            modalMemoEdit.find('.modal-body').load(url.replace('ID', id), {
                company: $('#company-report').val()
            });
            modalMemoEdit.modal('show');
        }

        function loadMemoFormDelete(id){
            $('.modal-memo').find('.modal-body').empty();

            const url = modalMemoDelete.data('url').toString();
            modalMemoDelete.find('.modal-body').load(url.replace('ID', id), {}, function (){
                $('.tooltips').tooltip();
            });
            modalMemoDelete.modal('show');
        }
    </script>
@endsection
