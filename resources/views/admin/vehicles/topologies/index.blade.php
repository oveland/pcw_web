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
        <form>
            <div class="panel panel-inverse">
                <button class="btn btn-success btn-sm btn-search-report" id="btnLlamarControlador">Cargar Tabla</button>
            </div>
            <div class="panel-body p-b-15">
                <div class="form-input-flat">
                    @if(Auth::user()->isAdmin())
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="company-report"
                                       class="control-label field-required">@lang('Company')</label>
                                <div class="form-group">
                                    <select name="company-report" id="company-report"
                                            class="default-select2 form-control col-md-12">
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
                                    class="default-select2 form-control col-md-12">
                                @include('partials.selects.vehicles')
                            </select>
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
        <div id="tableContainer"></div>
    </div>
    <!-- end row -->

    <!-- Include template for show modal report with char and historic route coordinates -->

    <!-- end template -->
    @include('admin.vehicles.topologies._table')
@endsection


@section('scripts')

    <script type="application/javascript">
        $('.menu-routes, .menu-report-control-points').addClass('active-animated');

        $(document).ready(function () {
            $('.form-search-report').submit(function (e) {
                e.preventDefault();
                var form = $(this);
                if (form.isValid()) {
                    form.find('.btn-search-report').addClass(loadingClass);

                    $.ajax({
                        url: form.attr('action'),
                        data: form.serialize(),
                        success: function (data) {
                            $('.report-container').empty().hide().html(data).fadeIn();
                        },
                        complete: function () {
                            form.find('.btn-search-report').removeClass(loadingClass);
                        }
                    });
                }
            });

            $('#date-report, #type-report, #route-report, #company-report').change(function () {
                var form = $('.form-search-report');
                $('.report-container').slideUp();
                if (form.isValid(false)) {
                    form.submit();
                }
            });

            $('#company-report').change(function () {
                loadRouteReport($(this).val());
            });

            @if(!Auth::user()->isAdmin())
            loadRouteReport(null);
            @endif
        });
        $(document).ready(function () {
            $('#btnLlamarControlador').click(function () {
                $.ajax({
                    url: '{{ route('admin-vehicles-table') }}',
                    method: 'GET',
                    success: function (response) {
                    },
                    error: function (xhr, status, error) {
                    }
                });
            });
        });
        $(document).ready(function() {
            // Lógica para cargar la tabla al hacer clic en un botón
            $('#btnLoadTable').click(function() {
                $.ajax({
                    url: 'admin-vehicles-table', // Reemplaza esto con la ruta hacia tu controlador que devuelve los datos de la tabla
                    method: 'GET', // O el método HTTP que corresponda
                    success: function(response) {
                        // Actualiza el contenido del contenedor con la respuesta del servidor
                        $('#tableContainer').html(response);
                    },
                    error: function(xhr, status, error) {
                        // Maneja el error si la solicitud no se completó correctamente
                        console.log(error);
                    }
                });
            });

        function loadRouteReport(company) {
            var routeSelect = $('#route-report');
            routeSelect.html($('#select-loading').html()).trigger('change.select2');
            routeSelect.load('{{ route('route-ajax-action') }}', {
                option: 'loadRoutes',
                company: company
            }, function () {
                routeSelect.trigger('change.select2');
            });
        }

    </script>
@endsection
