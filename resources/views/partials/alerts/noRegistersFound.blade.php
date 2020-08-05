<div class="m-b-10 mb-10 mt-10 col-md-6 col-md-offset-3 offset-md-3">
    <div class="col-md-12 page-404">
        <div class="number font-green col-md-3">
            <img src="{{ asset('img/no-data.svg') }}" width="100%"/>
        </div>
        <div class="details col-md-8">
            <h4><strong>@lang('Ups!')</strong></h4>
            <p>@lang( isset($message) ? $message : 'No registers found' )</p>
        </div>
    </div>
</div>