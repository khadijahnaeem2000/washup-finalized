@extends('layouts.app')

@section('content')
    <!--begin::Content body-->
    <div class="d-flex flex-column-fluid flex-center mt-30 mt-lg-0">
        <!--begin::Signin-->
        <div class="login-form login-signin">
            <div class="text-center mb-10 mb-lg-20">
                <h3 class="font-size-h1">{{ __('Login') }}</h3>
                <p class="text-muted font-weight-bold">Enter your email and password</p>
            </div>
            <!--begin::Form-->
            <!-- <form class="form" novalidate="novalidate" id="kt_login_signin_form"> -->
            <form method="POST" action="{{ route('login') }}">
                        @csrf
                <div class="form-group">
                    <input id="email" type="email" placeholder="Enter valid email" class="form-control form-control-solid h-auto py-5 px-6 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-group">

                    <input id="password" type="password" placeholder="Enter password" class="form-control form-control-solid h-auto py-5 px-6 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
               
                <!--begin::Action-->
                <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                <!-- <a href="javascript:;" class="text-dark-50 text-hover-primary my-3 mr-2" id="kt_login_forgot">Forgot Password ?</a> -->
                     @if (Route::has('password.request'))
                        <a class="text-dark-50 text-hover-primary my-3 mr-2" href="{{ route('password.request') }}">
                            {{ __('Forgot Your Password?') }}
                        </a>
                    @endif
                    <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3"> {{ __('Login') }}</button>
                   
                </div>
                <!--end::Action-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Signin-->
    </div>
    <!--end::Content body-->
@endsection
