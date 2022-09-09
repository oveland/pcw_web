<form class="col-md-12 form-horizontal form-binnacle-update p-b-20" action="{{ route('operation-vehicles-binnacle-update', ['binnacle' => $binnacle->id]) }}">
    <div class="form-body">
        @include('operation.vehicles.binnacle.templates._form', ['update' => true])

        <hr>

        <div class="form-actions p-t-10">
            <div class="col-md-12 text-center">
                <button class="btn btn-rounded btn-circle btn-outline btn-warning btn-lg">
                    <i class="fa fa-save"></i> @lang('Update')
                </button>
            </div>
        </div>
    </div>
</form>