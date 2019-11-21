@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Cameras')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header"><i class="fa fa-area-chart" aria-hidden="true"></i> @lang('Reports')
        <small><i class="fa fa-camera" aria-hidden="true"></i> @lang('Cameras')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div id="cameras-report" class="row" url="{{ route('report-passengers-sensors-cameras-show') }}">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" @submit.prevent="">
            <search-component url-params="{{ route('report-passengers-sensors-cameras-search-params') }}?company={{ $company }}" :search.sync="search" v-on:search-report="searchReport($event)"></search-component>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12">
            <div class="portlet light ">
                <div class="portlet-body">
                    <photos-component url-list="{{ route('report-passengers-sensors-cameras-show') }}"
                                      :photos="photos"
                                      @detail="setPhotoDetail"
                                      :search="search">
                    </photos-component>
                </div>
            </div>
        </div>
        <!-- end content report -->

        <div class="modal fade" id="modal-photo" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4><i class="fa fa-camera"></i> @lang('Photo detail')</h4>
                    </div>
                    <div class="modal-body">
                        <photo-detail url-photo="{{ route('report-passengers-sensors-cameras-photo', ['photo'=>'']) }}" :photo="photoDetail"></photo-detail>
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
    <script src="{{ mix('resources/js/reports/passengers/sensors/cameras/main.js') }}" type="application/ecmascript"></script>

    <script type="application/ecmascript">
        $('.menu-passengers, .menu-passengers-sensors, .menu-passengers-sensors-cameras').addClass('active-animated');
    </script>
@endsection