<template>
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
</template>

<script>
    export default {
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>
