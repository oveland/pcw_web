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
                <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Update') | @lang('Vehicle issues')</small>
            </h1>
            <!-- end page-header -->

            <!-- begin search form -->
            <form class="col-md-12 form-search-operation" action="{{ route('operation-vehicles-issues-update', ['vehicle' => $form->vehicle->id]) }}" method="POST">
                @include('operation.vehicles.issues.form')
            </form>
            <!-- end search form -->

            <!-- begin content operation -->
            <div class="main-container col-md-12"></div>
            <!-- end content operation -->
        </div>

    </div>
@endsection


@section('scripts')
    <script type="application/javascript">
        let mainContainer = $('.main-container');
        let form = $('.form-search-operation');

        $('.menu-operation-dispatch, .menu-operation-dispatch-automatic').addClass('active-animated');

        /*$(document).ready(function () {
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

            $('#company-operation').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            form.submit();
        });*/
    </script>
@endsection
