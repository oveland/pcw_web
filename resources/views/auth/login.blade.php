@extends('layouts.app')

@section('login-style')
    <style type="text/css">
        body{
            font-size: 13px!important;
            line-height: 20px;
            overflow-x: hidden!important;
            min-height: 100%;
            z-index: -2;
            margin: 0 !important;
            background: url(img/login.jpg) no-repeat top center fixed !important;
            -moz-background-size: cover;
            -webkit-background-size: cover;
            -o-background-size: cover;
            background-size: cover;
        }
        .transparent{
            background: rgba(0, 0, 0, 0.39) !important;
        }
        .transparent-header{
            background: rgba(0, 0, 0, 0.67) !important;
        }
    </style>
@endsection

@section('content')
<!-- begin #page-container -->
<div id="page-container" class="page-container">
    <!-- begin login -->
    <div class="login">
        <!-- begin login-brand -->
        <div class="login-brand text-white transparent-header">
            <i class="icon-user pull-right fa fa-2x"></i> @lang('Log In')
        </div>
        <!-- end login-brand -->
        <!-- begin login-content -->
        <div class="login-content transparent">
            <h4 class="text-center text-white m-t-0 m-b-20">@lang('Type your credentials')</h4>
            <form action="{{ route('login') }}" method="POST" name="login_form" class="form-input-flat">
                {{ csrf_field() }}
                <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                    <input id="username" type="text" class="form-control input-lg" placeholder="@lang('Username')" name="username" value="{{ old('username') }}" required onchange="$(this).val($(this).val().toUpperCase())" onkeypress="$(this).addClass('text-uppercase')"/>
                    @if ($errors->has('username'))
                        <span class="help-block">
                            <strong>{{ $errors->first('username') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                    <input id="password" type="password" class="form-control input-lg" name="password" placeholder="@lang('Password')" required/>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4 has-success">
                        <div class="checkbox">
                            <label class="text-white">
                                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> @lang('Remember Me')
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row m-b-20">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-success btn-lg btn-block">
                            @lang('Login') <i class="fa fa-sign-in"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        <!-- end login-content -->
    </div>
    <!-- end login -->
</div>
<!-- end page container -->
@endsection
