@extends('admin.layouts.login')
@section('title', 'Shambhavi | Admin Login')
@section('content')
<div class="user-login-5">
    <div class="row bs-reset">
        <div class="col-md-6 bs-reset">
            <div class="login-bg"
                data-background-images="{{ json_encode([
                    asset('admin/assets/pages/img/login/HeroBanner_Scalable_Desktop.svg'),
                    asset('admin/assets/pages/img/login/bg3.jpg'),
                ]) }}"
                style="background-image: url('{{ asset('admin/assets/pages/img/login/bg1.jpg') }}');">
                
            </div>
        </div>
        <div class="col-md-6 login-container bs-reset">
            <div class="login-content">
                <img class="login-logo" src="{{ asset('admin/assets/pages/img/login/Header_Logo_Transparent.png') }}" width="400" height="100" /> 
                <h1>{{ __('Admin Login') }}</h1>
                <p> {{ __('Lorem ipsum dolor sit amet, coectetuer adipiscing elit sed diam nonummy et nibh euismod aliquam erat volutpat. Lorem ipsum dolor sit amet, coectetuer adipiscing.') }} </p>
                <form method="POST" action="{{ route('admin.login.submit') }}" id="admin-login-form" class="login-form">
                    @csrf
                    @if (session('status'))
                        <div class="alert alert-success" role="status">
                            <button class="close" data-close="alert"></button>
                            <span>{{ session('status') }} </span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="alert alert-danger" role="alert">
                            <button class="close" data-close="alert"></button>
                            <span>{{ $errors->first() }} </span>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-xs-6">
                            <input
                                class="form-control form-control-solid placeholder-no-fix"
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                autocomplete="username"
                                required
                                autofocus
                                maxlength="255"
                            >
                        </div>

                        <div class="col-xs-6">
                            <input
                                class="form-control form-control-solid placeholder-no-fix"
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Enter your password"
                                autocomplete="off"
                                required
                                maxlength="255"
                            >
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="rem-password">
                                <p>
                                    {{ __('Remember Me') }}
                                    <input type="checkbox" name="remember" class="rem-checkbox" />
                                </p>
                            </div>
                        </div>
                        <div class="col-sm-8 text-right">
                            <div class="forgot-password">
                                <a href="javascript:;" id="forget-password" class="forget-password">{{ __('Forgot Password?') }}</a>
                            </div>
                            <button
                                type="submit"
                                class="btn blue password-toggle login-button"
                                aria-label="Show password"
                                aria-pressed="false"
                                id="login-button"
                            >
                                {{ __('Sign In') }}
                            </button>
                        </div>
                    </div>
                </form>
                <!-- BEGIN FORGOT PASSWORD FORM -->
                <form class="forget-form" action="javascript:;" method="post">
                    <h3 class="font-green">{{ __('Forgot Password?') }}</h3>
                    <p> {{ __('Enter your e-mail address below to reset your password.') }} </p>
                    <div class="form-group">
                        <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Email" name="email" /> </div>
                    <div class="form-actions">
                        <button type="button" id="back-btn" class="btn grey btn-default">{{ __('Back') }}</button>
                        <button type="submit" class="btn blue btn-success uppercase pull-right">{{ __('Submit') }}</button>
                    </div>
                </form>
                <!-- END FORGOT PASSWORD FORM -->
            </div>
            <div class="login-footer">
                <div class="row bs-reset">
                    <div class="col-xs-4 bs-reset">
                        <ul class="login-social">
                            <li>
                                <a href="javascript:;">
                                    <i class="icon-social-facebook"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <i class="icon-social-twitter"></i>
                                </a>
                            </li>
                            <li>
                                <a href="javascript:;">
                                    <i class="icon-social-dribbble"></i>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-8 bs-reset">
                        <div class="login-copyright text-right">
                            <p>&copy; {{ date('Y') }} Shambhavi. All rights reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection