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
<!--                                <form id="frmLogin" method="post"
                                    action="{{ route('validate-login',['panel' => $panel])}}" id="loginForm">-->
                                <form id="frmLogin" method="post" action="#" id="loginForm">
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
                                    <p><span class="text-danger" id="errorMessage"></span>
                                        <span class="text-success" id="successMessage"></span>
                                        <input type="hidden" name="session_id" id="session_id"> 
                                    </p>
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
                                    <div class="row"  id="otpMessageDiv" style="display:none;">
                                        <div class="col-md-4">
                                            <div class="form-group text-left" >
                                                <label class="">OTP</label>
                                                <input name="otp" id="otp" type="text" placeholder="OTP" class="form-control"/>
                                                <label id="otpMessage" class="text-danger"></label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">    
                                                <a href="{{ url('/',['panel' => $panel])}}" class="btn btn-primary btn-sm" style="margin-top:30px;">Reset</a>
                                        </div>
                                    </div>
                                    <!-- <button class="btn ripple btn-main-primary btn-block">Sign In</button> -->
                                    <div class="form-action mt-3">
                                        <!-- <button type="submit" class="btn ripple btn-main-primary btn-block login-btn">@lang('app.sign_in')</button> -->
                                        <!--<button type="submit" class="btn ripple btn-main-primary btn-block login-btn"
                                            id="load2"
                                            data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">@lang('app.sign_in')</button> -->
                                    <a type="submit" class="btn ripple btn-main-primary btn-block login-btn"
                                            id="load2" style="color:white;"
                                   data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading" data-id="1">Login</a>

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
 <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>   



 
<script>
     $('body').on('click', '#load2', function(){
            var siteurl = "{{ route('validate-login',['panel' => $panel])}}";
            $('#errorMessage').html('');
            $('#successMessage').html('');
           
            var otp = $('#otp').val();
            var session_id = $('#session_id').val();
            if(!otp && session_id){
                $('#errorMessage').html("Please enter OTP!");
                return false;
            }
             $.ajax({
                url: siteurl,
                        type: "POST",
                        data: $("#frmLogin").serialize(),
                        dataType:"json",
                        success: function(response) {
                          console.log("----");
                          console.log(response);
                          
                            if(response.status == "error" || response.status == "Error" ||  response.status == "verifed_otp_error"){
                                $('#errorMessage').html(response.message);
                                
                                 return false;
                            }
                            if(response.status == "otp_success"){
                                $('#otpMessageDiv').show(); 
                                $('#session_id').val(response.session_id);
                                $('#successMessage').html(response.message);
                                return false;
                            }
                         //     window.location.href="{{url('/')}}/super-admin/dashboard";
                            if(response.status == "verifed_otp_success"){ // OTP Matched and Redirect
                                $('#successMessage').html(response.message);
                                 $('#session_id').val('');
                               window.location.href="{{url('/')}}/super-admin/dashboard";

                            }
                        }, error:function(response){
                              if( response.status === 422 ) {
                                var errors = $.parseJSON(response.responseText);
                                $('#errorMessage').html(errors.message)
                                
                            }
                                 
                }
            }); 
        });

</script>

@endpush