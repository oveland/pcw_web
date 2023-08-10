@extends('layouts.blank')

@section('login-style')
    <style type="text/css">
        body{
            font-size: 13px!important;
            line-height: 20px;
            overflow-x: hidden!important;
            min-height: 100%;
            z-index: -2;
            margin: 0 !important;

            background: url(img/map-bg.jpg) repeat center center fixed !important;
        }
        .transparent{
            background: rgba(0, 0, 0, 0.39) !important;
        }
        .transparent-header{
            background: rgba(0, 0, 0, 0.67) !important;
        }

        @media (max-width: 420px){
            .login, .register {
                width: 100%;
                left: 0;
                top: 0;
                margin: 0;
                bottom: 0;
                background: initial !important;
            }
        }

        .btn-login {
            background-image: linear-gradient(to right, #002d3c, #005566, #008073, #00aa5c, #3fcf04) !important;
            color: white;
        }
    </style>
@endsection

@section('content')
<!-- begin #page-container -->
<div id="page-container" class="page-container">
    <!-- begin login -->

    <div class="login m-auto">
        <div class="login-brand text-white transparent-header p-20">
            <h4>
               <strong><i class="icon-user pull-right fa fa-2x"></i> Inicia sesión</strong>
            </h4>
        </div>

        <!-- begin login-content -->
        <div class="login-content transparent p-20">
            <form action="{{ route('login') }}" method="POST" name="login_form" class="form-input-flat">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <input id="username" type="text" class="form-control input-lg block" placeholder="@lang('Username')" name="username" value="{{ old('username') }}" required onchange="$(this).val($(this).val().toUpperCase())" onkeypress="$(this).addClass('text-uppercase')" autofocus/>
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control input-lg block" name="password" placeholder="@lang('Password')" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group" style="opacity: 20%">
                    <div class="col-md-6 col-md-offset-4 has-success">
                        <div class="checkbox">
                            <label class="text-white">
                                <input type="checkbox" name="remember" checked {{ old('remember') ? 'checked' : '' }}> @lang('Remember Me')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row m-b-20">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-login btn-success btn-lg btn-block">
                            @lang('Login') <i class="fa fa-sign-in"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- end login-content -->

        <div class="transparent-header text-center text-white p-20" style="width: 100%"> <b>{{ date('Y') }}</b> <i class="fa fa-rocket"></i> PCW @
            <a href="https://brochure.pcwserviciosgps.com/" title="PCW Tecnología" style="color: #419368" target="_blank">tecnologia.com</a>
        </div>
    </div>
    <!-- end login -->
</div>
<!-- end page container -->

<script>setTimeout(function(){$('#username').focus()},500)</script>
<style>
    .block {
        width: 100% !important;
    }

    .page-container {
        margin: auto !important;
        border-radius: 20px;
        overflow: hidden;
    }

    @media (max-width: 768px) {
        .page-container {
            width: 100% !important;
        }
    }
    @media (min-width: 768px) {
        .page-container {
            width: 40% !important;
            margin-top: 10% !important;
        }
    }
    @media (min-width: 1024px) {
        .page-container {
            width: 30% !important;
        }
    }
    @media (min-width: 1940px) {
        .page-container {
            width: 20% !important;
        }
    }
</style>
@endsection
