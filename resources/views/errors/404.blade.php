@extends('layout')

@section('stylesheets')

@endsection

@section('content')
    <!-- begin error -->
    <div class="error">
        <div class="error-code">404</div>
        <div class="error-content">
            <div class="error-message m-b-5">@lang("Oops... The page you're looking for doesn't exist.")</div>
            <div class="error-desc m-b-20">
                @lang("The page you are looking for might have been removed, had its name changed, or is temporarily unavailable")
            </div>
            <div>
                <a href="javascript:window.history.back();" class="btn btn-danger btn-rounded">
                    <i class="fa fa-backward"></i>
                    @lang('Go Back')
                </a>
            </div>
        </div>
    </div>
    <!-- end error -->
@endsection