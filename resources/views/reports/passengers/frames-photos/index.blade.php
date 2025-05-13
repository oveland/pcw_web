@extends('layouts.app')

@section('content')
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Passengers')</a></li>
        <li class="active">@lang('Trama de fotos 5G')</li>
    </ol>
    <!-- end breadcrumb -->

    <!-- begin page-header -->
    <h1 class="page-header">
        <i class="fa fa-map-marker"></i>
        @lang('Passengers')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Trama de fotos 5G')</small>
    </h1>
    <div class="row">
        <form class="col-md-12 form-search-report" action="{{ route('report-passengers-frames-show') }}">
            <div class="panel panel-inverse">
                <div class="panel-heading">
                    <div class="panel-heading-btn">
                        <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning"
                           data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                            <i class="fa fa-minus"></i>
                        </a>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm btn-search-report">
                        <i class="fa fa-search"></i> @lang('Search')
                    </button>
                </div>
                <div class="panel-body p-b-15">
                    <div class="form-input-flat">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="vehicle-report"
                                           class="control-label field-required">@lang('Vehicle')</label>
                                    <div class="form-group">
                                        <select name="vehicle-report" id="vehicle-report"
                                                class="default-select2 form-control col-md-12" data-with-all="true">
                                            @include('partials.selects.vehicles', compact('vehicles'))
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="date-report" class="control-label field-required">@lang('Date')</label>
                                    <div class="input-group date" id="datetimepicker-report">
                                        <input name="date-report" id="date-report" type="text" class="form-control"
                                               placeholder="yyyy-mm-dd" value="{{ date('Y-m-d') }}"/>
                                        <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-20"></div>
        <!-- end content report -->
    </div>


@endsection
@section('scripts')
    <script src="{{ asset('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js') }}"
            type="text/javascript"></script>
    <script type="application/javascript">
        let form = $('.form-search-report');
        let reportContainer = $('.report-container');
        let modalBinnacle = $('#modal-vehicles-binnacle');
        console.log('{{ route('report.passengers.frames') }}');
        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    reportContainer.show();
                    reportContainer.empty().hide().html($('#animated-loading').html()).show();
                    $.ajax({
                        url: $(this).attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            reportContainer.empty().hide().html(data).fadeIn();
                            hideSideBar();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                            modalBinnacle.modal('hide');
                        },
                        error: function (data) {
                            reportContainer.empty().fadeIn();
                        }
                    });
                }
            })
        })
    </script>
@endsection
