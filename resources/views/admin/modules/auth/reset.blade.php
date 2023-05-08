@extends('admin.layouts.main')

@section('title')
Forgot Password
@endsection

@section('content')

<main class="log-in">
    <div class="before-login-wrapper">
        <div class="img-title text-center logo">
            <img src="{{ asset('img/admin/logo@2x.png') }}" alt="Orange" />
        </div>
        <div class="before-login-screen box">

            <h2 class="login-title">{{ @trans('admin_auth.forgot_password') }}</h2>

            <form role="form" action="{{ route('password-reset-link-post') }}" method="POST" id="reset-password-form"
                autocomplete="off" class="p-3">
                <input type="hidden" name="token" value="{{ $token }}">
                {{ csrf_field() }}

                <p class="text-muted mb-4 text-center font-weight-light px-2">
                {{ @trans('admin_auth.pick_new_password') }}
                </p>

                <div class="form-group">
                    <label for="password" class="sr-only">{{ @trans('admin_auth.your_email') }}</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" class="form-control"
                        placeholder="Your Email">
                    @if($errors->has('email'))
                    <span class="invalid-feedback">
                        {{ $errors->first('email') }}
                    </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password" class="sr-only">{{ @trans('admin_auth.new_password') }}</label>
                    <input type="password" name="password" id="password" class="form-control"
                        placeholder="New Password">
                    @if($errors->has('password'))
                    <span class="invalid-feedback">
                        {{ $errors->first('password') }}
                    </span>
                    @endif
                </div>

                <div class="form-group">
                    <label for="password_confirmation" class="sr-only">{{ @trans('admin_auth.confirm_new_password') }}</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                        placeholder="Confirm New Password">
                    @if($errors->has('password_confirmation'))
                    <span class="invalid-feedback">
                        {{ $errors->first('password_confirmation') }}
                    </span>
                    @endif
                </div>

                <div class="form-group mt-5">
                    <button type="submit" class="btn btn-em" id="btn-reset-password">
                    {{ @trans('admin_auth.update_password') }}
                    </button>
                </div>

            </form>

        </div>
    </div>
</main>

@endsection