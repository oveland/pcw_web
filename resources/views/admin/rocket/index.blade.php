@extends( 'layout')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Admin')</a></li>
        <li><a href="javascript:;">@lang('Rocket')</a></li>
        <li class="active">@lang('Photo')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-users" aria-hidden="true"></i> @lang('Admin')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Rocket')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="vue-container" class="row" url="{{ route('admin.rocket.search') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('admin.rocket.params.get', ['name' => __('search')]) }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet light ">
                <div class="portlet-body">
                    <vue-json-pretty :data="report"></vue-json-pretty>
                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-admin-rocket" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4><i class="fa fa-camera"></i> @lang('Admin rocket modal')</h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button class="btn width-100 btn-default" data-dismiss="modal">
                            @lang('Close')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection

@section('scripts')
    <script src="{{ mix('resources/js/admin/rocket/main.js') }}" type="application/ecmascript"></script>

    <script type="application/ecmascript">
        $('.menu-admin, .menu-admin-rocket').addClass('active-animated');
    </script>
@endsection