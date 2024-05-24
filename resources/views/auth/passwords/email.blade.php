@extends('layouts.app')
@section('content')
    <!--begin::Content body-->
    <div class="d-flex flex-column-fluid flex-center mt-30 mt-lg-0">
        <!--begin::Signin-->
        <div class="login-form login-signin">
            <div class="text-center mb-10 mb-lg-20">
                <h3 class="font-size-h1">{{ __('Reset Password') }}</h3>
            </div>
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif
            <!--begin::Form-->
            <!-- <form class="form" novalidate="novalidate" id="kt_login_signin_form"> -->
            <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                <div class="form-group">
                    <input id="email" type="email" placeholder="Enter valid email" class="form-control form-control-solid h-auto py-5 px-6 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
               
                <!--begin::Action-->
                <div class="form-group d-flex flex-wrap justify-content-between align-items-center">
                    <button type="submit" class="btn btn-primary font-weight-bold px-9 py-4 my-3">  {{ __('Send Password Reset Link') }}</button>
                </div>
                <!--end::Action-->
            </form>
            <!--end::Form-->
        </div>
        <!--end::Signin-->
    </div>
    <!--end::Content body-->
@endsection

