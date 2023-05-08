@extends('admin.layouts.main')


@section('title')
Change password
@endsection

@section('content')
<main>
    <div class="two-col">
        @include("admin.common.sidebar")
        <div class="right-sec">
            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post" action="{{route('admin.changePassword.save')}}">
                                    @csrf
                                    <div class="form-body">
                                        <h2 class="form-sec-title">{{ @trans('user.change_password') }}</h2>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="current_password">{{ @trans('user.current_password') }}
                                                    <span class="required">*</span></label>
                                                <input type="password" name="current_password" class="form-control" placeholder="Enter Password" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="password">{{ @trans('user.new_password') }}
                                                    <span class="required">*</span></label>
                                                <input type="password" name="password" class="form-control" placeholder="Enter Password" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="control-label" for="password_confirmation">{{ @trans('user.confirm_new_password') }}
                                                    <span class="required">*</span></label>
                                                    <input type="password" name="password_confirmation" class="form-control" placeholder="Enter Password Again" />
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="submit" class="btn step-btn" value="{{ @trans('user.save') }}">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</main>
@endsection

@push('pageJs')
{!! JsValidator::formRequest('App\Http\Requests\User\ChangePasswordRequest', '#frmAddEditUser') !!}
<script>
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
@endpush