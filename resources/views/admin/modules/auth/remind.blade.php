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

            <h2 class="login-title">Forgot Password</h2>

            <form id="frmRemind" method="post" action="{{ route('password-reset-form-post')}}">
                @csrf
                <div class="form-body">

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

                    <div class="form-group">
                        <label class="control-label">Enter your registered email</label>
                        <input name="email" id="email" value="{{ old('email') }}" type="text" placeholder="Email"
                            class="form-control" />
                        @if($errors->has('email'))
                        <span class="invalid-feedback">
                            {{ $errors->first('email') }}
                        </span>
                        @endif
                    </div>

                    <div class="form-action mt-3">
                        <button type="submit" class="btn btn-em">@lang('Reset Link')</button>
                    </div>

                </div>
            </form>

        </div>
    </div>
</main>

@endsection

@push('pageJs')
{!! JsValidator::formRequest('App\Http\Requests\User\PasswordRemindRequest', '#frmRemind') !!}
@endpush