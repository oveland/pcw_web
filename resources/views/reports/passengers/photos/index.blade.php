@extends( $hideMenu ? 'layouts.blank' : 'layout')

@section('stylesheets')
    <style>
    </style>
@endsection

@section('content')

    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Photos')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header" style="margin-top: 20px">
        <i class="fa fa-users" aria-hidden="true"></i> @lang('Reports')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Photos')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="vue-container" class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component :admin="{{ Auth::user()->isAdmin() ? 'true' : 'false' }}" url-params="{{ route('report.passengers.photos.params.get', ['name' => __('search')]) }}" :search.sync="search" v-on:set-search="setSearch($event)"></search-component>
        </form>
        <!-- end search form -->

        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet-body">
                <div class="tab-pane fade active in" id="tab-report-photos">
                    <profile-seating-component :search-params="searchParams" api-url="{{ route('report.passengers.photos') }}"></profile-seating-component>
                    <report-photo-component :search-params="searchParams" api-url="{{ route('report.passengers.photos') }}"></report-photo-component>
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
    <script src="{{ mix('resources/js/reports/passengers/photos/main.js') }}" type="application/ecmascript"></script>
    {{--    <script src="{{ mix('resources/js/admin/rocket/main.js') }}" type="application/ecmascript"></script>--}}

    <script type="application/ecmascript">
        $('.menu-passengers, .menu-passengers-photos').addClass('active-animated');
    </script>
@endsection