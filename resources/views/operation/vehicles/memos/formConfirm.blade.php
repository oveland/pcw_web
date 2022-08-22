@extends('layouts.blank')

@section('stylesheets')
    <style>
        .form-group .fa{
            margin-top: 10px;
        }
    </style>
@endsection

@section('content')
    <!-- begin row -->
    <div class="row">
        <div class="col-lg-2 col-lg-offset-5 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
            <!-- begin page-header -->
            <h1 class="page-header"><i class="fa fa-life-ring fa-spin" aria-hidden="true"></i> @lang('Operation')
                <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Vehicle issues')</small>
            </h1>
            <!-- end page-header -->

            <div class="alert alert-success alert-bordered fade in m-b-10">
                <strong>
                    <i class="fa fa-check"></i>
                    {{ session('message') }}
                </strong>
            </div>
        </div>
    </div>
    <!-- end row -->
@endsection


@section('scripts')
    <script type="application/javascript">
        let mainContainer = $('.main-container');
        let form = $('.form-search-operation');

        $('.menu-operation-dispatch, .menu-operation-dispatch-automatic').addClass('active-animated');
    </script>
@endsection
