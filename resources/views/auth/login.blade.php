@extends('layouts.auth')

@section('content')
    <div class="login-page">
        <div class="login-box">
            <div class="login-logo">
                <b>PearTech</b>
            </div>
            <!-- /.login-logo -->
            <div class="card">
                <div class="card-body login-card-body">
                    <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>

                    <form action="{{ route('login') }}" method="post">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="email" name="email" placeholder="Email"
                                   value="{{ old('email') }}" class="form-control {!! \Render::isInvalid('email') !!}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-envelope"></span>
                                </div>
                            </div>
                            {!! \Render::errMsg('email') !!}
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" name="password" placeholder="Password"
                                   class="form-control {!! \Render::isInvalid('password') !!}">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                            {!! \Render::errMsg('password') !!}
                        </div>
                        <div class="row">
                            <div class="col-8">
                                <div class="icheck-primary">
                                    <input type="checkbox" id="remember">
                                    <label for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                            <!-- /.col -->
                            <div class="col-4">
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Sign In') }}</button>
                            </div>
                            <!-- /.col -->
                        </div>
                    </form>

                    <!-- /.social-auth-links -->

{{--                    <p class="mb-1">--}}
{{--                        <a href="forgot-password.html">{{ __('I forgot my password') }}</a>--}}
{{--                    </p>--}}
                    <p class="mb-0">
                        <a href="{{ route('register') }}" class="text-center">{{ __('Register a new membership') }}</a>
                    </p>
                    {{--                    @if (Route::has('password.request'))--}}
                    {{--                        <a class="btn btn-link" href="{{ route('password.request') }}">--}}
                    {{--                            {{ __('Forgot Your Password?') }}--}}
                    {{--                        </a>--}}
                    {{--                    @endif--}}
                </div>
                <!-- /.login-card-body -->
            </div>
        </div>
    </div>
@endsection
