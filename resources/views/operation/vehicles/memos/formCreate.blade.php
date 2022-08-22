<form class="col-md-12 form-horizontal form-memo-create p-b-20" action="{{ route('operation-vehicles-memo-create') }}">
    <div class="form-body">
        @include('operation.vehicles.memos.templates._form')

        <hr>

        <div class="form-actions p-t-10">
            <div class="col-md-12 text-center">
                <button class="btn btn-rounded btn-circle btn-outline green-jungle btn-lg">
                    <i class="fa fa-save"></i> @lang('Create')
                </button>
            </div>
        </div>
    </div>
</form>