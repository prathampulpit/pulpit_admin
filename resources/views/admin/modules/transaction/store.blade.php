@extends('admin.layouts.main')


@section('title')
@if($id)
Edit User
@else
Add User
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">


            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('user.users') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.users.index',['panel' => Session::get('panel') ]) }}">{{ @trans('user.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('user.edit_user') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('user.add_user') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.users.store',['panel' => Session::get('panel')]) }}">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('user.edit_user') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        <input type="hidden" class="emailHidden" name="emailHidden"
                                            value="{{$user->email}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('user.add_user') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.first_name') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="first_name" class="form-control"
                                                        value="@if(old('first_name')){{old('first_name')}}@elseif($id){{$user->first_name}}@endif"
                                                        placeholder="Enter First Name" />
                                                    @if($errors->has('first_name'))
                                                    <div class="invalid-feedback">{{ $errors->first('first_name')}}
                                                    </div>
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.last_name') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="last_name" class="form-control"
                                                        value="@if(old('last_name')){{old('last_name')}}@elseif($id){{$user->last_name}}@endif"
                                                        placeholder="Enter Last Name" />
                                                    @if($errors->has('last_name'))
                                                    <div class="invalid-feedback">{{ $errors->first('last_name')}}
                                                    </div>
                                                    @endif

                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.email') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="email" class="form-control email"
                                                        class="form-control"
                                                        value="@if(old('email')){{old('email')}}@elseif($id){{$user->email}}@endif"
                                                        placeholder="Enter Email" />
                                                    @if($errors->has('email'))
                                                    <div class="invalid-feedback">{{ $errors->first('email')}}
                                                    </div>
                                                    @endif

                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.phone') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="mobile" v-model="mobile"
                                                        class="form-control"
                                                        value="@if(old('mobile')){{old('mobile')}}@elseif($id){{$user->mobile}}@endif"
                                                        placeholder="Enter Mobile" />
                                                    @if($errors->has('mobile'))
                                                    <div class="invalid-feedback">{{ $errors->first('mobile')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.password') }}
                                                        <span class="required">*</span></label>
                                                    <input type="password" id="password" name="password" class="form-control password"
                                                        placeholder="@if(!$id) Enter Password @endif" value="" />@if($id
                                                    && !$errors->has('password'))<span
                                                        class="star">••••••••</span>@endif
                                                    @if($errors->has('password'))
                                                    <div class="invalid-feedback">{{ $errors->first('password')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('user.confirm_password') }}
                                                        <span class="required">*</span></label>
                                                    <input type="password" id="confirm_password" name="password_confirmation"
                                                        class="form-control confirm-password"
                                                        placeholder="@if(!$id) Enter Password Again @endif"
                                                        value="" />@if($id &&
                                                    !$errors->has('password'))<span class="star">••••••••</span>@endif
                                                    @if($errors->has('password_confirmation'))
                                                    <div class="invalid-feedback">
                                                        {{ $errors->first('password_confirmation')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-action text-right">
                                        <input type="submit" class="btn step-btn" value="{{ @trans('user.save') }}">
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

<script>
$("#frmAddEditUser").each(function() {
    $(this).validate({
        rules: {
            'first_name': 'required',
            'last_name':  'required',
            'email':{
                'required' : true,
                'maxlength': 255
            },
            'mobile':{
                'required' : true,
                'digits'   : true,
                'minlength': 10,
                'maxlength': 10
            }
        },
        messages: {
            'first_name': 'First name is required',
            'last_name':  'Last name is required',
            'email':{
                'required' : 'Email is required',
                'maxlength': 'Email may not be greater than 255 characters.'
            },
            'mobile':{
                'required' : 'Mobile is required',
                'digits'   : 'The mobile must be 10 digits.',
                'minlength': 'The mobile must be 10 digits.',
                'maxlength': 'The mobile must be 10 digits.'
            }
        },
        highlight: function(element) {
            $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        success: function(element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        errorClass: 'invalid-feedback',
    });
});

$.validator.addMethod('passRegex', function (value) { 
    return /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@()$%^&*=_{}[\]:;\"'|\\<>,.\/~`±§+-]).{8,30}$/.test(value); 
}, 'The password must be 8–30 characters, and include a number, a symbol, a lower and a upper case letter');

@if($id)
    $(".email").keyup(function() {
        if ($(".email").val() != $(".emailHidden").val()) {
            $(".password").next('span').remove();
            $(".confirm-password").next('span').remove();

            $('#password').rules("add", {
                required : true,
                passRegex : true,
                messages: { required: "Password is required" }
            });

            $('#confirm_password').rules("add", {
                required : true,
                equalTo : '#password',
                messages: { required: "Confirm Password is required",
                            equalTo : "The password confirmation does not match." }
            });
        } else {
            if(!$(".password").next('span').length) {
                $(".password").after('<span class="star">••••••••</span>');
                $(".confirm-password").after('<span class="star">••••••••</span>');
            }
            $('#password-error').remove();
            $('#confirm_password-error').remove();
            $('#password').rules("remove");
            $('#confirm_password').rules("remove");
        }
    });
    $( ".password" ).keyup(function() {
        $(".star").remove();
    });
@else
     $('#password').rules("add", {
        required : true,
        passRegex : true,
        messages: { required: "Password is required" }
    });

    $('#confirm_password').rules("add", {
        required : true,
        equalTo : '#password',
        messages: { required: "Confirm Password is required",
                    equalTo : "The password confirmation does not match." }
    });
@endif

</script>
@endpush