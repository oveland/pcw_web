@if(isset($blankPage))
    @extends('layouts.blank')
@endif

<div class="alert alert-warning alert-bordered m-b-10 mb-10 mt-10 col-md-6 col-md-offset-3 offset-md-3">
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