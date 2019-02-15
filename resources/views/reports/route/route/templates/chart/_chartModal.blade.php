<div class="modal modal-message fade" id="modal-route-report">
    <div class="modal-dialog modal-full" style="height: 500px !important;margin-top: 1px">
        <div class="modal-content">
            @include('reports.route.route.templates.chart._chartView')
        </div>
    </div>
</div>

<div class="modal fade" id="modal-off-road-report" style="background: #535353;opacity: 0.96;">
    <div class="modal-dialog modal-lg" style="width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title">@lang('Off road report')</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 modal-off-road-report-table"></div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="javascript:;" class="btn width-100 btn-default" data-dismiss="modal">@lang('Close')</a>
            </div>
        </div>
    </div>
</div>

@include('reports.route.route.templates.chart._chartAssets')
