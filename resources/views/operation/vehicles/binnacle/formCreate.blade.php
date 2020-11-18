<form class="col-md-12 form-horizontal form-binnacle-create p-b-20" action="{{ route('operation-vehicles-binnacle-create') }}">
    <div class="form-body">
        @include('operation.vehicles.binnacle.templates._form')

        <hr>

        <div class="form-actions">
            <div class="col-md-6 col-md-offset-4">
                <button class="btn btn-rounded btn-circle btn-outline blue-chambray">
                    <i class="fa fa-save"></i> @lang('Save')
                </button>
            </div>
        </div>
    </div>
</form>