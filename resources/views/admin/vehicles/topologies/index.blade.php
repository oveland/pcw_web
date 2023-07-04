@extends('layout')

@section('stylesheets')
    <style>
        .table-report-control-point th {
            text-align: center !important;
        }

        .table-report-control-point th i {
            font-size: 200% !important;
        }

        .icon-vehicle-status {
            position: sticky;
        }

        .report-tab-cp {
            font-size: 80% !important;
            position: relative;
            top: -3px;
        }

        .report-tab-cp button span {
            font-size: 80% !important;
            position: relative;
            top: -3px;
            padding: 5px
        }

        .report-tab-cp button {
            padding-left: 2px;
            padding-right: 2px;
            height: 15px
        }

        .report-tab-cp th {
            padding: 8px !important;
        }

        .report-tab-cp td {
            padding: 5px !important;
        }

        .report-tab-cp .bg-warning {
            color: white !important;
        }
    </style>

@endsection

@section('content')
    <!-- begin breadcrumb -->
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('admin')</a></li>
        <li><a href="javascript:;">@lang('vehicles')</a></li>
        <li class="active">@lang('Topologia asientos')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <i class="fa fa-map-marker"></i>
        @lang('Administracion')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Topologias de asientos')</small>
    </h1>

    <!-- end page-header -->

    <!-- begin row -->
    <div class="row">
        <!-- begin search form -->
        <form class="col-md-12 form-search-report" action="{{ route('admin-vehicles-table') }}">
            <div class="panel-body p-b-15">
                <div class="form-input-flat">
                    @if(Auth::user()->isAdmin())
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="company-report"
                                       class="control-label field-required">@lang('Company')</label>
                                <div class="form-group">
                                    <select name="company-report" id="company-report"
                                            class="default-select2 form-control col-md-12 ">
                                        <option value="null">@lang('Select an option')</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->short_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="vehicle-report" class="control-label field-required">@lang('Vehicle')</label>
                        <div class="form-group">
                            <select name="vehicle-report" id="vehicle-report"
                                    class="default-select2 form-control col-md-12" data-with-all="true">
                                @include('partials.selects.vehicles', compact('vehicles'), ['withAll' => true])
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="camera">@lang('Camara ')</label>
                        <div class="form-group">
                            <select name="cameras" id="cameras" class="default-select2 form-control col-md-12"
                                    data-with-all="true">
                                <option value="all">Todas</option>
                                <option value="1">Camara 1</option>
                                <option value="2">Camara 2</option>
                                <option value="3">Camara 3</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">

                        <label style="color: transparent">""</label>
                        <div class="form-group">
                            <button type="submit" class="btn btn-success btn-sm btn-search-report">
                                <i class="fa fa-search"></i> @lang('Search report')
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <!-- end search form -->
        <hr class="hr">
        <!-- begin content report -->
        <div class="report-container col-md-12"></div>
        <!-- end content report -->
    </div>
    <div>
        <div id="report-container"></div>
    </div>


    <!-- end row -->

    <!-- Include template for show modal report with char and historic route coordinates -->

    <!-- end template -->
    {{-- @include('admin.vehicles.topologies._table')--}}
@endsection


@section('scripts')
    <!-- BEGIN PAGE LEVEL PLUGINS -->
    <script src="{{ asset('assets/global/plugins/ion.rangeslider/js/ion.rangeSlider.min.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/lib/markdown.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-markdown/js/bootstrap-markdown.js') }}"
            type="text/javascript"></script>
    <script src="{{ asset('assets/global/plugins/bootstrap-summernote/summernote.min.js') }}"
            type="text/javascript"></script>
    <!-- END PAGE LEVEL PLUGINS -->

    <script src="{{ asset('assets/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    <script type="application/javascript">
        $('.menu-routes, .menu-off-road-report').addClass('active-animated');
        let form = $('.form-search-report');
        let mainContainer = $('.report-container');

        $(document).ready(function () {
            form.submit(function (e) {
                e.preventDefault();
                mainContainer.show().empty().html($('#animated-loading').html());
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);
                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            mainContainer.empty().hide().html(data).fadeIn();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#route-report').change(function () {
                loadSelectVehicleReportFromRoute($(this).val());
                mainContainer.slideUp(100);
            });

            $('#vehicle-report').change(function () {
                mainContainer.slideUp(100);
            });

            $('#date-report, #type-report').change(function () {
                mainContainer.slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#modal-route-report').on('shown.bs.modal', function () {
                initializeMap();
            });

            $('body').on('click', '.btn-show-address', function () {
                let el = $(this);
                el.attr('disabled', true);
                el.find('span').hide();
                el.find('i').removeClass('hide');
                $($(this).data('target')).load($(this).data('url'), function (response, status, xhr) {
                    console.log(status);
                    el.attr('disabled', false);
                    if (status === "error") {
                        if (el.hasClass('second-time')) {
                            el.removeClass('second-time');
                        } else {
                            el.addClass('second-time', true).click();
                        }
                    } else {
                        el.fadeOut(1000);
                    }
                });
            })
                .on('click', '.accordion-vehicles', function () {
                    $($(this).data('parent'))
                        .find('.collapse').collapse('hide')
                        .find($(this).data('target')).collapse('show');
                })
                .on('keyup', '.search-vehicle-list', function () {
                    let vehicle = $(this).val();
                    if (is_not_null(vehicle)) {
                        $('.vehicle-list').slideUp("fast", function () {
                            $('#vehicle-list-' + vehicle).slideDown();
                        });
                    } else {
                        $('.vehicle-list').slideDown();
                    }
                });

            @if(Auth::user()->isAdmin())
            $('#company-report').change(function () {
                loadSelectVehicleReport($(this).val(), true);
                loadSelectRouteReport($(this).val());
                mainContainer.slideUp(100);
            }).change();
            @else
            $('#route-report').change();
            @endif

            let time = moment('00:00', 'HH:mm');
            let timeRange = [];
            for (let min = 0; min <= (24 * 60 - 2); min += 5) {
                timeRange.push(time.format('HH:mm'));
                time.add(5, 'minutes');
            }
            timeRange.push(time.subtract(1, 'minutes').format('HH:mm'));

            const initialTime = parseInt(0);
            const finalTime = parseInt(288);

            $("#time-range-report").ionRangeSlider({
                type: "double",
                from: initialTime,
                to: finalTime,
                values: timeRange,
                drag_interval: true,
                //max_interval: 48,
                prefix: "<i class='fa fa-clock-o'></i> ",
                skin: "modern",
                grid: false,
                decorate_both: true,
                prettify: true,
                keyboard: true,
                grid_num: 10,
                values_separator: " → ",
                onChange: function (slider) {
                    mainContainer.slideUp(100);
                }
            });
        });
    </script>
@endsection
