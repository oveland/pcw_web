@extends('layouts.app')

@section('content')
    <ol class="breadcrumb pull-right">
        <li><a href="javascript:;">@lang('Reports')</a></li>
        <li><a href="javascript:;">@lang('Routes')</a></li>
        <li class="active">@lang('Promedio de pasajeros')</li>
    </ol>
    <!-- end breadcrumb -->
    <!-- begin page-header -->
    <h1 class="page-header">
        <i class="fa fa-map-marker"></i>
        @lang('report')
        <small><i class="fa fa-hand-o-right" aria-hidden="true"></i> @lang('Promedio Pasajeros')</small>
    </h1>

    <div class="row">
        <!-- begin search form -->

            <form class="col-md-12 form-search-report" action="{{route('report-route-average-passenger-show')}}">
                <div class="panel panel-inverse">
                    <div class="panel-heading">
                        <div class="panel-heading-btn">
                            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning tooltips"
                               data-click="panel-collapse" data-original-title="" title="@lang('Expand / Compress')">
                                <i class="fa fa-minus"></i>
                            </a>
                        </div>
                        <div style="display: flex; gap: 4px">
                            <!-- Formulario de búsqueda -->
                            <form class="form-search-report" action="{{route('report-route-average-passenger-show')}}" method="GET">
                                <button type="submit" class="btn btn-success btn-sm btn-search-report">
                                    <i class="fa fa-search"></i> @lang('Search')
                                </button>
                            </form>
                            <!-- Formulario de exportación -->
                            <form action="{{ route('report-route-average-passenger-export') }}" method="GET">
                                <button type="submit" class="btn btn-primary">Generar Reporte Excel</button>
                            </form>
                        </div>

                    </div>
                    <div class="panel-body p-b-15">
                        <div class="form-input-flat">
                            @if(Auth::user()->isAdmin())
                                <div class="col-md-2 hide">
                                    <div class="form-group">
                                        <label for="company-report"
                                               class="control-label field-required">@lang('Company')</label>
                                        <div class="form-group">
                                            <select name="company-report" id="company-report"
                                                    class="default-select2 form-control col-md-12 primary-filter">
                                                <option value="{{$company->id}}">{{ $company->short_name }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            @php
                            $today = \Carbon\Carbon::now()->dayOfWeekIso; // Devuelve de 1 (Lunes) a 7 (Domingo)
                        @endphp
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="day-of-week" class="control-label field-required">
                                    @lang('Día')
                                </label>
                                <select name="day-of-week" id="day-of-week" class="form-control col-md-2">
                                    @foreach (['1' => 'LUNES', '2' => 'MARTES', '3' => 'MIÉRCOLES', '4' => 'JUEVES', '5' => 'VIERNES', '6' => 'SÁBADO', '9' => 'DIA HABIL', '7'=>'DOMINGO/FESTIVO'] as $value => $label)
                                        <option value="{{ $value }}" {{ isset($today) && $today == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="time-report" class="control-label field-required">
                                    @lang('Hora')
                                </label>
                                <div class="input-group date" id="timepicker">
                                    <input name="time-report" id="time-report" type="text"
                                           class="form-control primary-filter" autocomplete="off"
                                           placeholder="HH:MM" value="{{ date('H:i') }}"/>
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="route-report" class="control-label field-required">@lang('Route')</label>
                                <div class="form-group">
                                    <select name="route-report" id="route-report" data-with-all="false"
                                            data-with-none="false"
                                            class="default-select2 form-control col-md-12 primary-filter">
                                    </select>
                                </div>
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

   
@endsection
@section('scripts')
  <script src="{{ asset('assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.js') }}"
          type="text/javascript"></script>

  <script type="application/javascript">
      $('.menu-routes, .menu-route-report').addClass('active-animated');

      let form = $('.form-search-report');
      let reportContainer = $('.report-container');
      let modalBinnacle = $('#modal-vehicles-binnacle');

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
          });

          $('#date-report, #route-report, #vehicle-report, #company-report, #type-report, #completed-turns, #no-taken-turns, #last-laps').change(function () {
              $('.report-container').slideUp();
          });

          $('#route-report').change(function () {
              const route = $(this).val();
              loadSelectVehicleReportFromRoute(route);
              reportContainer.slideUp(100);
          });

          @if(Auth::user()->isAdmin())
          $('#company-report').change(function () {
              loadSelectVehicleReport($(this).val(), true);
              loadSelectRouteReport($(this).val());
              reportContainer.slideUp(100);
          }).change();
          @else
          loadSelectRouteReport(null);
          @endif

          setTimeout(function () {
              $('.btn-show-off-road-report').click();
          }, 500);

          $('body').on('click', '.btn-show-historic-report', function () {
              const url = $(this).data('url');
              $('#modal-historic-report .modal-content iframe').attr('src', url);
          });
      });


      function initDateTimePicker(format, els) {
          const containers = els ? els : $('.datetime-report');

          containers.each(function (i, el) {
              $(el).data("DateTimePicker")?.destroy();

              $(el).datetimepicker({
                  format,
                  locale: 'es',
                  sideBySide: true,
                  showTodayButton: true
              });

              $(el).click(function () {
                  $(this).data("DateTimePicker")?.show();
              });
          });
      }

      $('#with-end-date').change(function () {
          const dec = $('.date-end-container').slideUp();

          if ($(this).is(':checked')) {
              dec.slideDown();
              initDateTimePicker("YYYY-MM-DD HH:mm");

              $('#date-end-report').val($('#date-end-report').val().split(' ')[0] + " 23:59")
          } else {
              initDateTimePicker("YYYY-MM-DD");
          }
      });

      $('#cancelled-turns').click(function (el) {
          if ($(this).is(':checked')) {
              $('#completed-turns').prop('checked', false);
              $('#active-turns').prop('checked', false);
          }
      });

      $('#spreadsheet-report').keyup(function () {
          const hasValue = !!$(this).val();
          const primaryFilters = $('.primary-filter');
          primaryFilters.parents('.form-group:not(.form-date)').css({'opacity': (hasValue ? 0.3 : 1)});

          const labels = primaryFilters.parents('.form-group').find('.control-label').addClass('field-required');
          if (hasValue) labels.removeClass('field-required');
      });

      initDateTimePicker("YYYY-MM-DD");
      $(document).ready(function () {
          $('#timepicker').datetimepicker({
              format: 'HH:mm',   // Solo hora y minutos
              stepping: 1,       // Intervalos de 5 minutos
              useCurrent: false, // No selecciona automáticamente la hora actual
          });
      });

  </script>
@endsection
