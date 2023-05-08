@extends('admin.layouts.main')

@section('title')
Login
@endsection

@section('content')

<!-- Row -->
<div class="row signpages text-center">
    <div class="col-md-12">
        <div class="card">
            <div class="row row-sm">
                <div class="col-lg-6 col-xl-5 d-none d-lg-block text-center bg-primary details">
                    <div class="mt-5 pt-4 p-2 pos-absolute">
                        <img src="{{ asset('assets/img/brand/logo-main.png') }}" class="header-brand-img mb-4"
                            alt="logo">
                        <div class="clearfix"></div>
                        <img src="{{ asset('assets/img/svgs/user.svg') }}" class="ht-100 mb-0" alt="user">
                        <h5 class="mt-4 text-white">Signin to Your Account</h5>
                        <span class="tx-white-6 tx-13 mb-5 mt-xl-0">Signup to create, discover and connect with the
                            global community</span>

                    </div>
                </div>
                <div class="col-lg-6 col-xl-7 col-xs-12 col-sm-12 login_form ">
                    <div class="container-fluid">
                        <div class="row row-sm">
                            <div class="card-body mt-2 mb-2">
                                <img src="{{ asset('assets/img/brand/logo.png') }}"
                                    class=" d-lg-none header-brand-img text-left float-left mb-4" alt="logo">
                                <div class="clearfix"></div>
                                <form id="frmLogin" method="post"
                                    action="{{ route('validate-login',['panel' => $panel])}}">
                                    @csrf

                                    @if(Session::get('fail_message', false))
                                    @component('admin.components.fail_message')
                                    {{ Session::get('fail_message') }}
                                    @endcomponent
                                    @endif

                                    @if(Session::get('success_message', false))
                                    @component('admin.components.success_message')
                                    {{ Session::get('success_message') }}
                                    @endcomponent
                                    @endif

                                    <h5 class="text-left mb-2">Signin to Your Account</h5>
                                    <p class="mb-4 text-muted tx-13 ml-0 text-left">&nbsp;</p>

                                    <div class="form-group text-left">
                                        <label class="">@lang('app.email')</label>
                                        <input name="username" id="username" type="text" placeholder="Email"
                                            class="form-control" maxlength="100" />
                                        @if($errors->has('username'))
                                        <label class="invalid-feedback" for="username">
                                            {{ $errors->first('username') }}
                                        </label>
                                        @endif
                                    </div>

                                    <div class="form-group text-left">
                                        <label class="">@lang('app.password')</label>
                                        <input name="password" id="password" type="password" placeholder="Password"
                                            class="form-control" maxlength="50" />
                                        @if($errors->has('password'))
                                        <label class="invalid-feedback" for="username">
                                            {{ $errors->first('password') }}
                                        </label>
                                        @endif
                                    </div>

                                    <!-- <button class="btn ripple btn-main-primary btn-block">Sign In</button> -->
                                    <div class="form-action mt-3">
                                        <!-- <button type="submit" class="btn ripple btn-main-primary btn-block login-btn">@lang('app.sign_in')</button> -->
                                        <button type="submit" class="btn ripple btn-main-primary btn-block login-btn"
                                            id="load2"
                                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">@lang('app.sign_in')</button>

                                        {{-- @if (config('custom.auth.forgot_password'))
                                        <a href="{{ route('password-reset-form') }}"
                                        class="before-login-link float-right">@lang('app.forgot_password')</a>
                                        @endif --}}
                                    </div>
                                </form>
                                <div class="text-left mt-5 ml-0">
                                    <!-- <div class="mb-1"><a href="">Forgot password?</a></div>
                                    <div>Don't have an account? <a href="#">Register Here</a></div> -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Row -->

@endsection

@push('pageJs')
{!! JsValidator::formRequest('App\Http\Requests\Auth\LoginRequest', '#frmLogin') !!}
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
@endpush