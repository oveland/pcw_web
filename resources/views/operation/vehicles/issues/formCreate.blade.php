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
            <h1 class="page-header" style="border: 0"><i class="fa fa-life-ring fa-spin" aria-hidden="true"></i> @lang('Operation')
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

                <div class="clearfix m-b-10 p-t-10">
                    <div class="text-left">
                        @if($vehicle->active)
                            @if(!$presetOutIssue)
                                <label class="radio-inline m-0 p-0 btn btn-sm btn-default tooltips" data-title="@lang('Check to inactivate')" onclick="clickInactivate()">
                                    <input type="checkbox" id="inactivate" name="inactivate" value="true" {{ $presetInactivate ? "checked" : "" }}/> @lang('Inactivate') <i class="fa fa-bus text-muted"></i>
                                </label>
                            @endif
                        @else
                            <label class="radio-inline m-0 p-0 btn btn-sm btn-lime tooltips" data-title="@lang('Vehicle currently inactive. Check to activate')" onclick="clickActivate()">
                                <input type="checkbox" id="activate" name="activate" value="true" {{ $presetActivate ? "checked" : "" }}/> @lang('Activate') <i class="fa fa-bus green"></i>
                            </label>
                        @endif
                        @if(!$presetOutIssue && !$presetActivate)
                            <label class="radio-inline m-0 p-0 btn btn-sm btn-warning tooltips" data-title="@lang('Check to register vehicle in repair')" onclick="clickSetInRepair()">
                                <input type="checkbox" id="set_in_repair" name="set_in_repair" value="true" {{ $presetActivate || $presetInactivate ? "" : "checked" }}/> @lang('Set in repair') <i class="fa fa-wrench faa-wrench animated"></i>
                            </label>
                        @endif
                    </div>
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
                        <textarea rows="2" id="observations" name="observations" {{ $presetActivate || $presetInactivate || $presetOutIssue ? "" : "required" }} maxlength="256" style="resize: vertical" class="form-control"></textarea>
                        <span class="fa fa-exclamation-triangle form-control-feedback"></span>
                    </div>

                    <div class="form-group has-{{ $currentIssue->getColor() }} has-feedback m-b-10">
                        @if($currentIssue->readyForIn())
                            <input type="hidden" checked name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::IN }}"/>
                            <label for="issue_type_id" class="pull-left control-label">@lang('Currently the vehicle has no novelties. This will register one')</label>
                        @else
                            <input type="hidden" checked name="force_out" value="true"/>
                            @if(!$presetOutIssue && !$presetActivate && !$presetInactivate)
                                <label for="issue_type_id" class="pull-left control-label">@lang('Currently the vehicle has novelties') {!! "<br> - $currentIssue->observations. <br>" !!}<br> @lang('Select an action')</label>
                            @elseif($presetActivate)
                                <label for="issue_type_id" class="pull-left control-label">@lang('The vehicle will activate')</label>
                            @elseif($presetInactivate)
                                <label for="issue_type_id" class="pull-left control-label">@lang('The vehicle will inactivate')</label>
                            @elseif($presetOutIssue)
                                <label for="issue_type_id" class="pull-left control-label">@lang('The vehicle will register out of repair')</label>
                            @endif
                            <div style="clear: both;">
                                @if(!$presetOutIssue && !$presetActivate)
                                    <label class="radio-inline m-0 p-0 btn btn-sm btn-info" style="color: white" onclick="clickOtherNovelty()">
                                        <input type="radio" {{ $presetOutIssue ? "" : "checked" }} name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::UPDATE }}"/> @lang('Update') <i class="fa fa-exclamation-triangle"></i>
                                    </label>
                                @endif
                                <label class="radio-inline m-0 p-0 btn btn-sm btn-success" style="color: white" onclick="clickOutIssue()">
                                    <input type="radio" {{ $presetOutIssue ? "checked" : "" }} name="issue_type_id" value="{{ \App\Models\Vehicles\VehicleIssueType::OUT }}"/> @lang('Out of issue') <i class="fa fa-check"></i>
                                </label>
                            </div>
                        @endif
                    </div>

                    <hr class="hr col-lg-12 col-md-12 col-sm-12 col-xs-12">

                    <div class="form-group text-center m-t-10 m-b-0">
                        <button type="submit" class="btn btn-{{ $currentIssue->getColor() }} btn">
                            <i class="fa fa-save"></i> @lang('Save')
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

        function clickSetInRepair(){
            $('#observations').prop('required', true);
        }

        function clickActivate(){
            $('#observations').removeAttr('required');
        }

        function clickInactivate(){
            $('#observations').removeAttr('required');
        }

        function clickOtherNovelty(){
            $('#observations').prop('required', true);
            $('#set_in_repair').parents('label').slideDown();
        }

        function clickOutIssue(){
            $('#observations').removeAttr('required');
            $('#set_in_repair').removeAttr('checked').parent().removeClass('checked').parents('label').hide();
            $('#inactivate').removeAttr('checked').parent().removeClass('checked').parents('label').hide();
            $('#activate').prop('checked', true).parent().addClass('checked').parents('label').slideDown();
        }

        $('.tooltips').tooltip();
    </script>
@endsection
