<div class="alert alert-warning alert-bordered fade in m-b-10 col-md-6 col-md-offset-3">
    <div class="col-md-2" style="padding-top: 20px">
        <i class="fa fa-3x fa-calendar"></i>
    </div>
    <div class="col-md-10">
        <span class="close pull-right" data-dismiss="alert">×</span>
        <h4><strong>@lang('Ups!')</strong></h4>
        <hr class="hr">
        @lang('The date range must be less than :limit days', ['limit' => $limit ?? 30])
    </div>
</div>