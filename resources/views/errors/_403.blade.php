<div class="row">
    <div class="col-md-12 page-404 text-center p-40">
        <div class="number text-warning" style="font-size: 10em !important;">
            <i class="fa fa-exclamation-triangle"></i>
        </div>
        <div class="details">
            <h3 class="text-info">@lang("Oops... You don't have access permissions")</h3>
            @if(Session::has('message'))
                <hr>
                <h3 class="text-warning">{{ Session::get('message') }}</h3>
            @else
                <p> @lang("The page you are looking for might have been protected with admin permissions")</p>

                <a href="javascript:window.history.back();" class="btn btn-danger btn-rounded">
                    <i class="fa fa-backward"></i>
                    @lang('Go Back')
                </a>
            @endif
        </div>
    </div>
</div>
