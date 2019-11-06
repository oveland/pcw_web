@extends('layouts.blank')

@section('stylesheets')
    <style>
        .form-group .fa{
            margin-top: 10px;
        }
    </style>
@endsection

@php
    $currentIssue = $vehicle->getCurrentIssue();
    $driver = $currentIssue ? $currentIssue->driver : null;
@endphp

@section('content')
    <!-- begin row -->
    <div class="row">
        <div class="col-lg-2 col-lg-offset-5 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1 col-xs-12">
            <!-- begin page-header -->
            <h1 class="page-header"><i class="fa fa-life-ring fa-spin" aria-hidden="true"></i> @lang('Operation')
                <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> {{ __($currentIssue->readyForIn() ? "Create" : "Update") }} @lang('Vehicle issues')</small>
            </h1>
            <!-- end page-header -->

            <!-- begin search form -->
            <form class="col-md-12 form-search-operation" action="{{ route('operation-vehicles-issues-create', ['vehicle' => $vehicle->id]) }}" method="POST">
                @if(session('error'))
                    <div class="alert alert-danger alert-bordered fade in m-b-10 col-md-12">
                        <strong>
                            <i class="fa fa-check"></i>
                            {{ session('error') }}
                            <span class="close" data-dismiss="alert">×</span>
                        </strong>
                    </div>
                @endif

                <div class="clearfix m-b-10 text-center">
                    @if($currentIssue->readyForIn())
                        <input type="hidden" checked name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::IN }}"/>
                    @else
                        <label class="p-t-10">
                            <i class="fa fa-edit"></i> @lang('Register issue'):
                        </label>
                        <br>
                        <label class="radio-inline m-0 p-0 btn btn-sm btn-success" onclick="$('#observations').removeAttr('required')">
                            <input type="radio" name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::OUT }}"/> @lang('Out of issue') <i class="fa fa-check"></i>
                        </label>
                        <label class="radio-inline m-0 p-0 btn btn-sm btn-warning" onclick="$('#observations').attr('required', true)">
                            <input type="radio" checked name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::UPDATE }}"/> @lang('Other issue') <i class="fa fa-exclamation-triangle"></i>
                        </label>
                        <hr class="hr col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    @endif
                </div>

                <div class="form">
                    <div class="form-group has-{{ $currentIssue->getColor() }} has-feedback m-b-10">
                        <label for="vehicle" class="control-label">@lang('Vehicle')</label>
                        <input id="vehicle" name="vehicle" type="text" class="form-control" disabled value="{{ $vehicle->number }}">
                        <span class="fa fa-car form-control-feedback"></span>
                    </div>
                    <div class="form-group has-{{ $currentIssue->getColor() }} has-feedback m-b-10">
                        <label for="driver" class="control-label">@lang('Driver')</label>

                        <div class="form-group">
                            <select name="driver" id="driver" class="default-select2 form-control col-md-12">
                                <option value="">@lang('Unassigned')</option>
                                @foreach($drivers as $driverSelection)
                                    <option value="{{ $driverSelection->id }}" {{ $driver && $driverSelection->id == $driver->id ? "selected" : "" }} >{{ $driverSelection->code." | ".$driverSelection->fullName() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <span class="fa fa-user form-control-feedback"></span>
                    </div>
                    <div class="form-group has-{{ $currentIssue->getColor() }} has-feedback m-b-10">
                        <label for="observations" class="pull-left control-label">@lang('Observations')</label>
                        <textarea rows="2" id="observations" name="observations" required maxlength="256" style="resize: vertical" class="form-control"></textarea>
                        <span class="fa fa-exclamation-triangle form-control-feedback"></span>
                    </div>

                    <hr class="hr col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <div class="form-group m-t-10">
                        <button type="submit" class="btn btn-{{ $currentIssue->getColor() }} btn-sm">
                            <i class="fa fa-edit"></i> @lang('Register issue')
                        </button>
                    </div>
                </div>
            </form>
            <!-- end search form -->

            <!-- begin content operation -->
            <div class="main-container col-md-12"></div>
            <!-- end content operation -->
        </div>
    </div>
    <!-- end row -->
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
