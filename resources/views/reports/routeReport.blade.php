@if(count($dispatchRegisters))
    <div class="panel panel-success ">
        <div class="panel-heading">
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-lime " data-click="panel-expand">
                    <i class="fa fa-expand"></i>
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-warning" data-click="panel-collapse"
                   data-original-title="" title="Ocultar / Mostrar">
                    <i class="fa fa-minus"></i>
                </a>
                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-danger" data-click="panel-remove">
                    <i class="fa fa-times"></i>
                </a>
            </div>
            <h4 class="panel-title">@lang('Report time route')</h4>
        </div>
        <div class="panel-body p-b-15">
            <!-- begin panel -->
            <div class="panel pagination-inverse bg-white clearfix no-rounded-corner m-b-0">
                <!-- begin table -->
                <table id="data-table" data-order='[[1,"asc"]]' class="table table-bordered table-hover">
                    <thead>
                    <tr>
                        <th class="col-md-1">@lang('Route')</th>
                        <th>@lang('Vehicle')</th>
                        <th class="col-md-2">@lang('Hour dispatch')</th>
                        <th>@lang('Round Trip')</th>
                        <th data-sorting="disabled">@lang('Turn')</th>
                        <th class="col-md-4" data-sorting="disabled"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $dispatchRegisters as $dispatchRegister )
                        <tr>
                            <td>{{$dispatchRegister->route->nombre}}</td>
                            <td>{{$dispatchRegister->n_vehiculo}}</td>
                            <td>{{$dispatchRegister->h_reg_despachado}}</td>
                            <td>{{$dispatchRegister->n_vuelta}}</td>
                            <td>{{$dispatchRegister->n_turno}}</td>
                            <td data-render="sparkline" data-values="{{ $dispatchRegister->reports->pluck('status_in_minutes')->toJson() }}" data-times="{{ $dispatchRegister->reports->pluck('timed')->toJson() }}"></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <!-- end table -->
            </div>
            <!-- end panel -->
        </div>
    </div>

    <script type="application/javascript">
        $('[data-render="sparkline"]').each(function() {
            var dataValues = $(this).data('values');
            var dataTimes = $(this).data('times');

            console.log('dataTimes = ',dataTimes);

            dataValues.forEach(function(e,i){
                dataValues[i] = e*60;
            });

            $(this).sparkline(dataValues, {
                type: 'line',
                width: '300%',
                height: '30px',
                fillColor: 'transparent',
                spotColor: '#F04B46',
                lineColor: '#17B6A4',
                minSpotColor: '#F04B46',
                maxSpotColor: '#F04B46',
                lineWidth: 2.5,
                spotRadius: 5,
                normalRangeMin: -5, normalRangeMax: 5,
                tooltipFormat: '<?="{{offset:offset}}"?>',
                tooltipValueLookups: {
                    'offset':dataTimes
                },
            });
        });
    </script>
@else
    <div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
        <div class="col-md-2" style="padding-top: 10px">
            <i class="fa fa-3x fa-exclamation-circle"></i>
        </div>
        <div class="col-md-10">
            <span class="close pull-right" data-dismiss="alert">×</span>
            <h4><strong>@lang('Ups!')</strong></h4>
            <hr class="hr">
            @lang('No registers found')
        </div>
    </div>
@endif