@if(count($beaMarks))
    <div class="tab-content panel p-0">
        <!-- begin table -->
        <table class="table table-bordered table-striped table-condensed table-hover table-valign-middle table-report">
            <thead>
            <tr class="inverse">
                <th>
                    <i class="fa fa-calendar text-muted"></i><br>
                    @lang('Date')
                </th>
                <th>
                    <i class="fa fa-flag text-muted"></i><br>
                    @lang('Route')
                </th>
                <th>
                    <i class="fa fa-retweet text-muted"></i><br>
                    @lang('Trajectory')
                </th>
                <th>
                    <i class="fa fa-user text-muted"></i><br>
                    @lang('Driver')
                </th>
                <th>
                    <i class="fa fa-clock-o text-muted"></i><br>
                    @lang('From')
                </th>
                <th>
                    <i class="fa fa-clock-o text-muted"></i><br>
                    @lang('To')
                </th>
                <th>
                    <i class="ion-android-stopwatch"></i><br>
                    @lang('Duration')
                </th>
                <th class="col-md-2">
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('Ascents')
                </th>
                <th class="col-md-2">
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('Descents')
                </th>
                <th class="col-md-2">
                    <i class="fa fa-users text-muted"></i><br>
                    @lang('BEA')
                </th>
                <th class="col-md-2">
                    <i class="fa fa-dollar text-muted"></i><br>
                    @lang('Total BEA')
                </th>
            </tr>
            </thead>
            <tbody>
            @foreach($beaMarks as $beaMark)
                @php
                    $turn = $beaMark->turn;
                    $route = $turn->route;
                    $driver = $turn->driver;
                    $vehicle = $turn->vehicle;
                @endphp
                <tr>
                    <td class="text-center">{{ $beaMark->initialDate->toDateString() }}</td>
                    <td class="text-center">{{ $route->name }}</td>
                    <td class="text-center">
                        <span class="label span-full label-{{ $beaMark->trajectory->id == 1 ? 'success' : 'warning' }}">
                            {{ $beaMark->trajectory->name }}
                        </span>
                    </td>
                    <td class="text-center">{{ $driver->name }}</td>
                    <td class="text-center">{{ $beaMark->initialDate->toTimeString() }}</td>
                    <td class="text-center">{{ $beaMark->finalDate->toTimeString() }}</td>
                    <td class="text-center">{{ $beaMark->duration }}</td>
                    <td class="text-center">{{ $beaMark->passengersUp }}</td>
                    <td class="text-center">{{ $beaMark->passengersDown }}</td>
                    <td class="text-center">{{ $beaMark->passengersBEA }}</td>
                    <td class="text-center">{{ number_format($beaMark->totalBEA, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="11" style="    height: 3px !important;background: gray;text-align: center;padding: 0;"></td>
            </tr>
            <tr>
                <td rowspan="2" colspan="5" class="text-center">
                    <button class="btn btn-sm green-haze btn-outline sbold uppercase m-t-5" data-toggle="modal" data-target="#modal-generate-liquidation">
                        <i class="fa fa-dollar"></i> @lang('Generate liquidation')
                    </button>
                </td>
                <td colspan="2" class="text-right">
                    <i class="fa fa-sliders"></i> @lang('Average')
                </td>
                <td class="text-center">{{ number_format($beaMarks->average('passengersUp'), 1) }}</td>
                <td class="text-center">{{ number_format($beaMarks->average('passengersDown'), 1) }}</td>
                <td class="text-center">{{ number_format($beaMarks->average('passengersBEA'), 1) }}</td>
                <td class="text-center">{{ number_format($beaMarks->average('totalBEA'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="text-right">
                    <i class="fa fa-tags"></i> @lang('Total')
                </td>
                <td class="text-center">{{ $beaMarks->sum('passengersUp') }}</td>
                <td class="text-center">{{ $beaMarks->sum('passengersDown') }}</td>
                <td class="text-center">{{ $beaMarks->sum('passengersBEA') }}</td>
                <td class="text-center">{{ number_format($beaMarks->sum('totalBEA'), 0, ',', '.') }}</td>
            </tr>
            </tbody>
        </table>
        <!-- end table -->
    </div>

    <div class="modal fade" id="modal-generate-liquidation" tabindex="-1" role="basic" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header hide">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h5 class="modal-title">
                        <i class="fa fa-dollar"></i> @lang('Generate liquidation')
                    </h5>
                </div>
                <div class="modal-body" style="height: 400px">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="portlet light portlet-fit bordered">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-dollar font-green"></i>
                                        <span class="caption-subject font-green bold uppercase">
                                            @lang('Generate liquidation')
                                        </span>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="mt-element-step">
                                        <div class="row step-line">
                                            <div class="mt-step-desc text-center hide">
                                                <div class="font-dark bold uppercase">
                                                    @lang('Generate liquidation')
                                                </div>
                                                <div class="caption-desc font-grey-cascade">
                                                </div>
                                                <br/>
                                            </div>
                                            <div class="phases col-md-3 mt-step-col first phase-inventory warning" data-toggle="tab" href="#step-discounts" data-active="warning">
                                                <div class="mt-step-number bg-white">
                                                    <i class="icon-arrow-down"></i>
                                                </div>
                                                <div class="mt-step-title uppercase font-grey-cascade">@lang('Discounts')</div>
                                                <div class="mt-step-content font-grey-cascade hide">@lang('')</div>
                                            </div>
                                            <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab" href="#step-commissions" data-active="active">
                                                <div class="mt-step-number bg-white">
                                                    <i class=" icon-user-follow"></i>
                                                </div>
                                                <div class="mt-step-title uppercase font-grey-cascade">@lang('Commissions')</div>
                                                <div class="mt-step-content font-grey-cascade hide">@lang('')</div>
                                            </div>
                                            <div class="phases col-md-3 mt-step-col phase-inventory" data-toggle="tab" href="#step-penalties" data-active="error">
                                                <div class="mt-step-number bg-white">
                                                    <i class="icon-shield"></i>
                                                </div>
                                                <div class="mt-step-title uppercase font-grey-cascade">@lang('Penalties')</div>
                                                <div class="mt-step-content font-grey-cascade hide">@lang('')</div>
                                            </div>
                                            <div class="phases col-md-3 mt-step-col last phase-inventory" data-toggle="tab" href="#step-liquidate" data-active="done">
                                                <div class="mt-step-number bg-white">
                                                    <i class="icon-calculator"></i>
                                                </div>
                                                <div class="mt-step-title uppercase font-grey-cascade">@lang('Liquidate')</div>
                                                <div class="mt-step-content font-grey-cascade">@lang('')</div>
                                            </div>
                                        </div>
                                        <hr/>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="tab-content">
                                                <div id="step-discounts" class="tab-pane fade in active">
                                                    <div class="portlet light bordered phase-container col-md-6 col-md-offset-3 m-t-10">
                                                        <div class="form form-horizontal">
                                                            <div class="form-group">
                                                                <label for="tolls" class="col-md-4 control-label">@lang('Tolls')</label>
                                                                <div class="col-md-8">
                                                                    <input id="tolls" name="tolls" class="input-sm form-control" type="number">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="washing" class="col-md-4 control-label">@lang('Washing')</label>
                                                                <div class="col-md-8">
                                                                    <input id="washing" name="washing" class="input-sm form-control" type="number">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tolls" class="col-md-4 control-label"></label>
                                                                <div class="col-md-8">
                                                                    <button class="btn btn-sm btn-outline btn-white">
                                                                        <i class="fa fa-plus"></i> @lang('Add other')
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <hr class="hr">
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label"></label>
                                                                <div class="col-md-8">
                                                                    <span class="pull-left">@lang('Total discounts'):</span>
                                                                    <span class="pull-right">$ 0.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="step-commissions" class="tab-pane fade">
                                                    <div class="portlet light bordered col-md-6 col-md-offset-3 m-t-10">
                                                        <div class="form form-horizontal">
                                                            <p>
                                                                <span class="pull-left">@lang('Total BEA'): $aaa.aaa</span>
                                                                <span class="pull-right">@lang('Passengers'): aaa</span>
                                                            </p>
                                                            <hr class="hr">
                                                            <div class="form-group">
                                                                <label for="percent_bea" class="col-md-4 control-label">@lang('% BEA')</label>
                                                                <div class="col-md-8">
                                                                    <input id="percent_bea" name="percent_bea" class="input-sm form-control" type="number" value="">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="fixed-value" class="col-md-4 control-label">@lang('Fixed Value / Pass.')</label>
                                                                <div class="col-md-8">
                                                                    <input id="fixed-value" name="fixed-value" class="input-sm form-control" type="number">
                                                                </div>
                                                            </div>
                                                            <hr class="hr">
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label"></label>
                                                                <div class="col-md-8">
                                                                    <span class="pull-left">@lang('Total commissions'):</span>
                                                                    <span class="pull-right">$ 0.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="step-penalties" class="tab-pane fade">
                                                    <div class="portlet light bordered phase-container col-md-6 col-md-offset-3 m-t-10">
                                                        <div class="form form-horizontal">
                                                            <p class="text-center">
                                                                <span>@lang('Total abordados'): aa</span>
                                                            </p>
                                                            <div class="form-group">
                                                                <label for="addressed" class="col-md-4 control-label">@lang('Valor x abordado')</label>
                                                                <div class="col-md-8">
                                                                    <input id="addressed" name="tolls" class="input-sm form-control" type="number">
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="tolls" class="col-md-4 control-label"></label>
                                                                <div class="col-md-8">
                                                                    <button class="btn btn-sm btn-outline btn-white">
                                                                        <i class="fa fa-plus"></i> @lang('Add other')
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            <hr class="hr">
                                                            <div class="form-group">
                                                                <label class="col-md-4 control-label"></label>
                                                                <div class="col-md-8">
                                                                    <span class="pull-left">@lang('Total penalties'):</span>
                                                                    <span class="pull-right">$ 0.00</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="step-liquidate" class="tab-pane fade">
                                                    <div class="portlet light bordered phase-container col-md-6 col-md-offset-3 m-t-10">
                                                        <div class="form form-horizontal">
                                                            <h3 class="">
                                                                <span class="">@lang('Total BEA')</span>
                                                                <span class="pull-right">$ aaa.aaa</span>
                                                            </h3>
                                                            <h3 class="">
                                                                <span class="">@lang('Total discounts')</span>
                                                                <span class="pull-right">$ aaa.aaa</span>
                                                            </h3>
                                                            <h3 class="">
                                                                <span class="">@lang('Total commissions')</span>
                                                                <span class="pull-right">$ aaa.aaa</span>
                                                            </h3>
                                                            <h3 class="">
                                                                <span class="">@lang('Total penalties')</span>
                                                                <span class="pull-right">$ aaa.aaa</span>
                                                            </h3>
                                                            <hr class="hr">
                                                            <h3 class="" style="font-size: 1.3em">
                                                                <span class="text-bold" style="font-size: 1.5em">@lang('TOTAL')</span>
                                                                <span class="pull-right" style="font-size: 1.5em">$ aaa.aaa</span>
                                                            </h3>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn dark btn-outline" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn green">Siguiente</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
    </div>
@else
    @include('partials.alerts.noRegistersFound')
@endif
