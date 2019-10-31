<div class="form">
    <div class="form-group has-{{ $form->color }} has-feedback m-b-10">
        <label for="vehicle" class="control-label">@lang('Vehicle')</label>
        <label class="pull-right control-label">{{ $form->currentIssue ? $form->currentIssue->id : "" }}</label>
        <input id="vehicle" name="vehicle" type="text" class="form-control" disabled value="{{ $form->vehicle->number }}">
        <span class="fa fa-car form-control-feedback"></span>
    </div>
    <div class="form-group has-{{ $form->color }} has-feedback m-b-10">
        <label for="driver" class="control-label">@lang('Driver')</label>
        <input id="driver" name="driver" type="text" class="form-control" disabled value="{{ $form->dispatchRegister ? $form->dispatchRegister->driver_name : __('Unassigned') }}">
        <span class="fa fa-user form-control-feedback"></span>
    </div>
    <div class="form-group has-{{ $form->color }} has-feedback m-b-10">
        <label for="observations" class="pull-left control-label">@lang('Observations')</label>
        <textarea rows="2" id="observations" name="observations" required maxlength="256" style="resize: vertical" class="form-control">{{ $form->currentIssue ? $form->currentIssue->observations_in : "" }}</textarea>
        <span class="fa fa-exclamation-triangle form-control-feedback"></span>
    </div>

    <hr class="hr col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <div class="form-group m-t-10">
        <button type="submit" class="btn btn-{{ $form->color }} btn-sm">
            <i class="fa fa-edit"></i> @lang('Register issue')
        </button>
    </div>
</div>