@extends('admin.layouts.main')

@section('title')
@if($id)
Edit User Details
@else
Add User Details
@endif

@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
<loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">New Register</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Forms</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Form Validation</li>
                    </ol> -->
                </div>
                <!-- <div class="d-flex">
                    <div class="justify-content-center">
                        <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                        <i class="fe fe-download mr-2"></i> Import
                        </button>
                        <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                        <i class="fe fe-filter mr-2"></i> Filter
                        </button>
                        <button type="button" class="btn btn-primary my-2 btn-icon-text">
                        <i class="fe fe-download-cloud mr-2"></i> Download Report
                        </button>
                    </div>
                </div> -->
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="col-lg-12 col-md-12">
                                <div class="form-wizard">
                                    <form action="" method="post" id="frmstep" role="form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="action_type" id="action_type" value="{{$action_type}}">
                                        <input type="hidden" name="profile_id" id="profile_id" value="{{$profile_id}}">
                                        <input type="hidden" name="id" id="id" value="{{$id}}">
                                        <div class="form-wizard-header">
                                            <!-- <p>Fill all form field to go next step</p> -->
                                            <ul class="list-unstyled form-wizard-steps clearfix">
                                                <li class="active"><span>1</span></li>
                                                <li><span>2</span></li>
                                                <li><span>3</span></li>
                                                <li><span>4</span></li>
                                            </ul>
                                        </div>
                                        <fieldset class="wizard-fieldset show">
                                            <h5>Personal Information</h5>
                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" value="@if(old('first_name')){{old('first_name')}}@elseif($id){{$user->first_name}}@endif" name="first_name" id="fname">
                                                        <label for="fname" class="wizard-form-text-label">First Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" value="@if(old('last_name')){{old('last_name')}}@elseif($id){{$user->last_name}}@endif" name="last_name" id="lname">
                                                        <label for="lname" class="wizard-form-text-label">Last Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="state" id="stateid">
                                                            <option value="" label="Select State"></option>
                                                            @foreach($states as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->state_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="city_id" id="city_id">
                                                            <option value="" label="Select City"></option>
                                                            @if($id)                                            
                                                            @foreach($cities as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->city_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>     

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control" value="@if(old('email')){{old('email')}}@elseif($id){{$user->emailid}}@endif" name="email" id="email">
                                                        <label for="email" class="wizard-form-text-label">Email Id</label>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" value="@if(old('mobile_number')){{old('mobile_number')}}@elseif($id){{$user->mobile_number}}@endif" name="mobile_number" id="mobile_number">
                                                        <label for="lname" class="wizard-form-text-label">Mobile Number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                Gender
                                                <div class="wizard-form-radio">
                                                    <!-- <input name="radio-name" id="radio1" type="radio"> -->
                                                    <input name="gender" id="radio1" type="radio" value="Male" @if($id) @if($user->gender == 'Male') checked @endif @endif class="wizard-required">
                                                    <label for="radio1">Male</label>
                                                </div>
                                                <div class="wizard-form-radio">
                                                    <input name="gender" id="radio2" type="radio" value="Female" @if($id) @if($user->gender == 'Female') checked @endif @endif class="wizard-required">
                                                    <label for="radio2">Female</label>
                                                </div>
                                                <div class="wizard-form-error"></div>
                                            </div>
                                            
                                            <input type="hidden" name="register_step" id="register_step" value="1">
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                            </div>
                                        </fieldset>	

                                        <fieldset class="wizard-fieldset">
                                            <h5>Register As</h5>

                                            <div class="row row-sm mg-b-20">
                                                <div class="col-lg-12">
                                                    <!-- <p class="mg-b-10">{{ @trans('user.register_as') }}: <span class="tx-danger">*</span></p> -->
                                                    <div class="mg-b-10 row" id="fnWrapper">
                                                        <div class="col-lg-2">
                                                            <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="2" @if($id) @if($user->user_type_id == 2) checked @endif @endif class="wizard-required" @if(!empty($user->user_type_id)) disabled @endif>
                                                            <span>Agent</span></label>
                                                        </div>
                                                        <!-- <div class="col-lg-2">
                                                            <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="4" @if($id) @if($user_type_id == 4) checked @endif @endif class="wizard-required">
                                                            <span>Driver</span></label>
                                                        </div> -->
                                                        <!-- <div class="col-lg-3">
                                                            <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="5" @if($id) @if($user->user_type_id == 5) checked @endif @endif class="wizard-required">
                                                            <span>Driver Cum Owner</span></label>
                                                        </div> -->
                                                        <div class="col-lg-3">
                                                            <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="3" @if($id) @if($user->user_type_id == 3) checked @endif @endif @if(!empty($user->user_type_id)) disabled @endif class="wizard-required">
                                                            <span>Travel Agency</span></label>
                                                        </div>
                                                        <div class="wizard-form-error"></div>

                                                        @if(!empty($user->user_type_id) && $user->user_type_id == 3)
                                                            <input type="hidden" name="usertype" value="3">
                                                        @elseif(!empty($user->user_type_id) && $user->user_type_id == 2)
                                                            <input type="hidden" name="usertype" value="2">
                                                        @endif
                                                    </div>
                                                    
                                                </div>
                                            </div>
                                            <input type="hidden" name="register_step" id="register_step1" value="2">
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                            </div>
                                        </fieldset>

                                        <fieldset class="wizard-fieldset">
                                            <h5 class="step-3-text">Agent Information</h5>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('agent_name')){{old('agent_name')}}@elseif($id){{$user->travel_name}}@endif" name="agent_name" type="text" maxlength="50">
                                                        <label for="lname" class="wizard-form-text-label">Agent Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('owner_name')){{old('owner_name')}}@elseif($id){{$user->owner_name}}@endif" name="owner_name" id="owner_name" type="text" maxlength="50">
                                                        <label for="lname" class="wizard-form-text-label">Owner Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('office_no')){{old('office_no')}}@elseif($id){{$user->office_no}}@endif" name="office_no" type="text" maxlength="50">
                                                        <label for="lname" class="wizard-form-text-label">Office contact number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('total_business_year')){{old('total_business_year')}}@elseif($id){{$user->total_business_year}}@endif" name="total_business_year" type="text" maxlength="50">
                                                        <label for="lname" class="wizard-form-text-label">Total year of travel business*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row row-sm mg-b-20 agent">
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <select class="form-control select2" name="registration_document" id="registration_document">
                                                            <option value="" label="Travel / Agent Registrations documents">Travel / Agent Registrations documents</option>
                                                            
                                                            <option value="Private Limited Company" @if($id) @if($user->registration_document == 'Private Limited Company') selected @endif @endif>Private Limited Company</option>
                                                            <option value="OCP" @if($id) @if($user->registration_document == 'OCP') selected @endif @endif>OCP</option>
                                                            <option value="LLP" @if($id) @if($user->registration_document == 'LLP') selected @endif @endif>LLP</option>
                                                            <option value="MSME/Proprirtotship" @if($id) @if($user->registration_document == 'MSME/Proprirtotship') selected @endif @endif>MSME/Proprirtotship</option>
                                                            <option value="Partnership Deed" @if($id) @if($user->registration_document == 'Partnership Deed') selected @endif @endif>Partnership Deed</option>
                                                            <option value="Other" @if($id) @if($user->registration_document == 'Other') selected @endif @endif>Other</option>
                                                        </select>
                                                    </div>
                                                    <div class="wizard-form-error"></div>
                                                </div>
                                            </div>
                                            
                                            <div class="row agent">
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10">{{ @trans('user.logo') }}: <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="logo" id="logo" class="dropify dropify-event" data-height="200" @if(!$id) @else data-default-file="{{$user->logo}}" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="logo_status" data-pk-id="@if(isset($user->agent_id)){{$user->agent_id}}@endif" data-module="agent" @endif onchange="encodeImgtoBase64('logo')"  />
                                                        <input type="hidden" name="logo" id="base64_logo" value="">
                                                        <input type="hidden" name="edit_logo" id="edit_logo" value="@if($id){{$user->logo}}@endif">
                                                    </div>
                                                </div>
                                                <div class="wizard-form-error"></div>

                                                <div class="col-lg-6">
                                                    <p class="mg-b-10">Upload registration Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">

                                                        <input type="file" name="registration_document_url" id="registration_document_url" class="dropify bank dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="registration_document_url_status" data-pk-id="@if(isset($user->agent_id)){{$user->agent_id}}@endif" data-module="agent" @if(!empty($user)) @if(!$id) @else data-default-file="{{$user->registration_document_url}}" @endif @endif onchange="encodeImgtoBase64('registration_document_url')"/>

                                                        <input type="hidden" name="registration_document_url" id="base64_registration_document_url" value="">
                                                        <input type="hidden" name="edit_registration_document_url" id="edit_registration_document_url" value="@if($id){{$user->registration_document_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="wizard-form-error"></div>
                                            </div>

                                            <div class="row agent">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('adhar_card')){{old('adhar_card')}}@elseif($id){{$user->adhar_card}}@endif" name="adhar_card" type="text" maxlength="20" onkeypress="return /[0-9]/i.test(event.key)">
                                                        <label for="lname" class="wizard-form-text-label">Adhar card number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input class="form-control wizard-required" value="@if(old('pan_card_no')){{old('pan_card_no')}}@elseif($id){{$user->pan_card}}@endif" name="pan_card_no" type="text" maxlength="10">
                                                        <label for="lname" class="wizard-form-text-label">Pan card number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row agent">
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10">{{ @trans('user.pan_card') }}: <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="pan_card" id="pan_card" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="pan_card_url_status" data-pk-id="@if(isset($user->agent_id)){{$user->agent_id}}@endif" data-module="agent" @if(!$id) required="" @else data-default-file="{{$user->pan_card_url}}" @endif onchange="encodeImgtoBase64('pan_card')"/>
                                                        <input type="hidden" name="pan_card" id="base64_pan_card" value="">
                                                        <input type="hidden" name="edit_pan_card" id="edit_pan_card_url" value="@if($id){{$user->pan_card_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10">Adhar Card Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="addar_card_front" id="addar_card_front" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="adhar_card_url_status" data-pk-id="@if(isset($user->agent_id)){{$user->agent_id}}@endif" data-module="agent" @if(!$id) required="" @else data-default-file="{{$user->adhar_card_url}}" @endif onchange="encodeImgtoBase64('addar_card_front')"/>                                                        
                                                        <input type="hidden" name="addar_card_front" id="base64_addar_card_front" value="">
                                                        <input type="hidden" name="edit_addar_card_front" id="edit_adhar_card_url" value="@if($id){{$user->adhar_card_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10">Adhar Card Back <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="addar_card_back" id="addar_card_back" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="adhar_card_url_status" data-pk-id="@if(isset($user->agent_id)){{$user->agent_id}}@endif" data-module="agent" @if(!$id) required="" @else data-default-file="{{$user->adhar_card_back_url}}" @endif onchange="encodeImgtoBase64('addar_card_back')"/>
                                                        <input type="hidden" name="addar_card_back" id="base64_addar_card_back" value="">
                                                        <input type="hidden" name="edit_addar_card_back" id="edit_adhar_card_back_url" value="@if($id){{$user->adhar_card_back_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="register_step" id="register_step2" value="3">
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn float-right">Next</a>
                                            </div>
                                        </fieldset>	

                                        <fieldset class="wizard-fieldset">
                                            <h5>Bank Information</h5>
                                            <input type="hidden" name="bank_account_id" id="bank_account_id" value="@if($id){{ $user->bank_account_id }}@endif">
                                            <div class="row agent">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="account_holder_name" name="account_holder_name" value="@if(old('account_holder_name')){{old('account_holder_name')}}@elseif($id && !empty($bank)){{$bank['account_holder_name']}}@endif">
                                                        <label for="account_holder_name" class="wizard-form-text-label">Account Holder Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="ifsc_code" name="ifsc_code" value="@if(old('ifsc_code')){{old('ifsc_code')}}@elseif($id && !empty($bank)){{$bank['ifsc_code']}}@endif">
                                                        <label for="ifsc_code" class="wizard-form-text-label">IFSC Code*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row agent">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="bank_name" name="bank_name" value="@if(old('bank_name')){{old('bank_name')}}@elseif($id && !empty($bank)){{$bank['bank_name']}}@endif">
                                                        <label for="bank_name" class="wizard-form-text-label">Bank Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="branch_name" name="branch_name" value="@if(old('branch_name')){{old('branch_name')}}@elseif($id && !empty($bank)){{$bank['branch_name']}}@endif">
                                                        <label for="branch_name" class="wizard-form-text-label">Branch Name*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row agent">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="account_number" name="account_number" value="@if(old('account_number')){{old('account_number')}}@elseif($id && !empty($bank)){{$bank['account_number']}}@endif" onkeypress="return /[0-9]/i.test(event.key)">
                                                        <label for="acon" class="wizard-form-text-label">Account Number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="confirm_account_number" name="confirm_account_number" value="@if(old('confirm_account_number')){{old('confirm_account_number')}}@elseif($id && !empty($bank)){{$bank['account_number']}}@endif" onkeypress="return /[0-9]/i.test(event.key)">
                                                        <label for="acon" class="wizard-form-text-label">Confirm Account Number*</label>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10">Upload cheque or Passbook <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="cheque_book" id="cheque_book" class="dropify dropify-event travel wizard-required" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="bank_document_url_status" data-pk-id="@if(isset($user->bank_account_id)){{$user->bank_account_id}}@endif" data-module="bank_account" @if(!$id) required="" @else @if(!empty($bank)) data-default-file="{{$bank['document_url']}}" @endif @endif onchange="encodeImgtoBase64('cheque_book')"/>
                                                        <input type="hidden" name="cheque_book" id="base64_cheque_book" value="">
                                                        <input type="hidden" name="edit_cheque_book" id="edit_document_url" value="@if($id && !empty($bank)){{$bank['document_url']}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn float-left">Previous</a>
                                                <button type="button" class="form-wizard-submit float-right" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">Submit</button>
                                            </div>
                                        </fieldset>	
                                    </form>
                                </div>

                                <!-- Select Brand and Models -->
                                <div class="form-wizard1" style="display:none;">
                                    <form action="" method="post" id="frmstep1" role="form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" class="action_type_step2" name="action_type" id="action_type" value="{{$action_type}}">
                                        <input type="hidden" name="user_id" id="user_id">
                                        <input type="hidden" name="hid_agent_id" id="hid_agent_id">
                                        <input type="hidden" name="hid_usertype" id="hid_usertype">
                                        <input type="hidden" name="profile_id" id="profile_id" value="{{$profile_id}}">
                                        <input type="hidden" name="id" id="id" value="{{$id}}">
                                        <input type="hidden" class="vehicle_id" name="vehicle_id" id="vehicle_id" value="@if($id){{$user->vehicle_id}}@endif">                                        
                                        <input type="hidden" class="driver_id" name="driver_id" id="driver_id" value="@if($id){{$user->t_driver_id}}@endif">

                                        <div class="form-wizard-header1">
                                            <ul class="list-unstyled form-wizard-steps1 clearfix">
                                                <li class="active"><span>1</span></li>
                                                <li><span>2</span></li> 
                                                <li><span>3</span></li>  
                                                <li><span>4</span></li>                                           
                                            </ul>
                                        </div>
                                        <fieldset class="wizard-fieldset1 show">
                                            <h5>Vehicle Information</h5>
                                            <div class="row">    
                                                <?php
                                                $vehicle_number_first_four_char = "";
                                                $vehicle_number_two_char = "";
                                                $vehicle_number_last_four_char = "";
                                                if(!empty($user->vehicle_number)){
                                                    $vehicle_arr = explode("-", $user->vehicle_number);
                                                    if(!empty($vehicle_arr)){
                                                        if(isset($vehicle_arr[0])){
                                                            $vehicle_number_first_four_char = $vehicle_arr[0];
                                                        }
                                                        if(isset($vehicle_arr[1])){
                                                            $vehicle_number_two_char = $vehicle_arr[1];
                                                        }

                                                        if(isset($vehicle_arr[2])){
                                                            $vehicle_number_last_four_char = $vehicle_arr[2];
                                                        }
                                                    }
                                                }
                                                ?>
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="vehicle_number_first_four_char" id="vehicle_number" value="@if(old('vehicle_number')){{old('vehicle_number')}}@elseif($id){{$vehicle_number_first_four_char}}@endif" minlength="4" maxlength="4" style="text-transform:uppercase">
                                                        <label for="vehicle_number" class="wizard-form-text-label1">Vehicle Number*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="vehicle_number_two_char" id="vehicle_number" value="@if(old('vehicle_number')){{old('vehicle_number')}}@elseif($id){{$vehicle_number_two_char}}@endif" minlength="2" maxlength="2" style="text-transform:uppercase">
                                                        <label for="vehicle_number" class="wizard-form-text-label1">&nbsp;</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-2">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="vehicle_number_last_four_char" id="vehicle_number" value="@if(old('vehicle_number')){{old('vehicle_number')}}@elseif($id){{$vehicle_number_last_four_char}}@endif" minlength="2" maxlength="4">
                                                        <label for="vehicle_number" class="wizard-form-text-label1">&nbsp;</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="state" id="state" value="@if(old('state')){{old('state')}}@elseif($id){{$user->state}}@endif">
                                                        <label for="state" class="wizard-form-text-label1">State*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="city" id="city" value="@if(old('city')){{old('city')}}@elseif($id){{$user->city}}@endif">
                                                        <label for="city" class="wizard-form-text-label1">City*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="owner_name_rc" id="owner_name_rc" value="@if(old('owner_name_rc')){{old('owner_name_rc')}}@elseif($id){{$user->v_owner_name}}@endif">
                                                        <label for="owner_name_rc" class="wizard-form-text-label1">Owner Name RC*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> Upload RC Book Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="rc_front_url" id="rc_front_url" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="rc_front_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->rc_front_url}}" @endif onchange="encodeImgtoBase64('rc_front_url')"  />
                                                        <input type="hidden" name="rc_front_url" id="base64_rc_front_url" value="">
                                                        <input type="hidden" name="edit_rc_front_url" id="edit_rc_front_url" value="@if($id){{$user->rc_front_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> Upload RC Book Back <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="rc_back_url" id="rc_back_url" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="rc_back_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->rc_back_url}}" @endif onchange="encodeImgtoBase64('rc_back_url')"  />
                                                        <input type="hidden" name="rc_back_url" id="base64_rc_back_url" value="">
                                                        <input type="hidden" name="edit_rc_back_url" id="edit_rc_back_url" value="@if($id){{$user->rc_back_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-next-btn1 float-right">Next</a>
                                            </div>
                                        </fieldset>	

                                        <fieldset class="wizard-fieldset1">
                                            <h5>Select Brand & Model</h5>
                                            
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="vehicle_type_id" id="vehicle_type_id" required="">
                                                            <option value="" label="Select Vehicle Type"></option>
                                                            @foreach($vehicle_types as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->vehicle_type_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="brand_id" id="brand_id" required="">
                                                            <option value="" label="Select Brand"></option>
                                                            @foreach($brands as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->brand_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="model_id" id="model_id" required="">
                                                            <option value="" label="Select Model"></option>
                                                            @if($id)                                            
                                                                @foreach($models as $val)
                                                                <option value="{{ $val->id }}" @if($id) @if($val->id == $user->model_id) selected @endif @endif>{{ $val->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="registration_year" id="registration_year" required="">
                                                            <option value="" label="Select Registration Year">Select Registration Year</option>
                                                            @foreach($years as $val)
                                                            <option value="{{ $val }}" @if($id) @if($val == $user->registration_year) selected @endif @endif>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="fuel_type_id" id="fuel_type_id" required="">
                                                            <option value="" label="Select Fuel Type">Select Fuel Type</option>
                                                            @if($id)                                            
                                                                @foreach($fuel_types as $val)
                                                                <option value="{{ $val->id }}" @if($id) @if($val->id == $user->fuel_type_id) selected @endif @endif>{{ $val->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="colour_id" id="colour_id" required="">
                                                            <option value="" label="Select Vehicle Colour">Select Vehicle Colour</option>
                                                            @foreach($vehicle_colours as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->colour_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-2">
                                                    <p class="mg-b-10"> Vehicle Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="vehicle_front" id="vehicle_front" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if($user_type_id == 3 && !empty($vehicle_photo_mapping)){{$vehicle_photo_mapping[0]['image_url']}}@endif" @endif onchange="encodeImgtoBase64('vehicle_front')"  />
                                                        <input type="hidden" name="vehicle_front" id="base64_vehicle_front" value="">
                                                        <input type="hidden" name="edit_vehicle_front" value="@if($id && !empty($vehicle_photo_mapping) && $user_type_id == 3){{$vehicle_photo_mapping[0]['image_url']}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <p class="mg-b-10"> Vehicle Back <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="vehicle_back" id="vehicle_back" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if($user_type_id == 3 && !empty($vehicle_photo_mapping) && isset($vehicle_photo_mapping[1]['image_url']) ){{$vehicle_photo_mapping[1]['image_url']}}@endif" @endif onchange="encodeImgtoBase64('vehicle_back')"  />
                                                        <input type="hidden" name="vehicle_back" id="base64_vehicle_back" value="">
                                                        <input type="hidden" name="edit_vehicle_back" value="@if($id && !empty($vehicle_photo_mapping) && $user_type_id == 3 && isset($vehicle_photo_mapping[1]['image_url'])){{$vehicle_photo_mapping[1]['image_url']}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <p class="mg-b-10"> Vehicle Left <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="vehicle_left" id="vehicle_left" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if($user_type_id == 3 && !empty($vehicle_photo_mapping) && isset($vehicle_photo_mapping[2]['image_url'])){{$vehicle_photo_mapping[2]['image_url']}}@endif" @endif onchange="encodeImgtoBase64('vehicle_left')"  />
                                                        <input type="hidden" name="vehicle_left" id="base64_vehicle_left" value="">
                                                        <input type="hidden" name="edit_vehicle_left" value="@if($id && !empty($vehicle_photo_mapping) && $user_type_id == 3 && isset($vehicle_photo_mapping[2]['image_url'])){{$vehicle_photo_mapping[2]['image_url']}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <p class="mg-b-10"> Vehicle Right <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="vehicle_right" id="vehicle_right" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if($user_type_id == 3 && !empty($vehicle_photo_mapping) && !empty($vehicle_photo_mapping[3]['image_url'])){{$vehicle_photo_mapping[3]['image_url']}}@endif" @endif onchange="encodeImgtoBase64('vehicle_right')"  />
                                                        <input type="hidden" name="vehicle_right" id="base64_vehicle_right" value="">
                                                        <input type="hidden" name="edit_vehicle_right" value="@if($id && !empty($vehicle_photo_mapping) && $user_type_id == 3 && isset($vehicle_photo_mapping[3]['image_url'])){{$vehicle_photo_mapping[3]['image_url']}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-2">
                                                    <p class="mg-b-10"> Vehicle Interior <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="vehicle_interior" id="vehicle_interior" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if($user_type_id == 3 && !empty($vehicle_photo_mapping && isset($vehicle_photo_mapping[4]['image_url']))){{$vehicle_photo_mapping[4]['image_url']}}@endif" @endif onchange="encodeImgtoBase64('vehicle_interior')"  />
                                                        <input type="hidden" name="vehicle_interior" id="base64_vehicle_interior" value="">
                                                        <input type="hidden" name="edit_vehicle_interior" value="@if($id && !empty($vehicle_photo_mapping) && $user_type_id == 3 && isset($vehicle_photo_mapping[4]['image_url'])){{$vehicle_photo_mapping[4]['image_url']}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <input type="hidden" name="register_step" id="register_step3" value="4">
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn1 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn1 float-right">Next</a>
                                            </div>
                                        </fieldset>

                                        <fieldset class="wizard-fieldset1">
                                            <h5>Vehicle Documents</h5>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Insurance Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper"> <!-- dropify-event -->
                                                        <input type="file" name="insurance_doc_url1" id="insurance_doc_url1" class="dropify dropify-event" data-height="200" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="insurance_doc_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->insurance_doc_url}}" @endif onchange="encodeImgtoBase64('insurance_doc_url1')"  />
                                                        <input type="hidden" name="insurance_doc_url1" id="base64_insurance_doc_url1" value="">
                                                        <input type="hidden" name="edit_insurance_doc_url1" id="edit_insurance_doc_url" value="@if($id){{$user->insurance_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Permit Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="permit_doc_url1" id="permit_doc_url1" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="permit_doc_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->permit_doc_url}}" @endif onchange="encodeImgtoBase64('permit_doc_url1')"  />
                                                        <input type="hidden" name="permit_doc_url1" id="base64_permit_doc_url1" value="">
                                                        <input type="hidden" name="edit_permit_doc_url1" id="edit_permit_doc_url" value="@if($id){{$user->permit_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Fitness Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="fitness_doc_url1" id="fitness_doc_url1" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="fitness_doc_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->fitness_doc_url}}" @endif onchange="encodeImgtoBase64('fitness_doc_url1')"  />
                                                        <input type="hidden" name="fitness_doc_url1" id="base64_fitness_doc_url1" value="">
                                                        <input type="hidden" name="edit_fitness_doc_url1" id="edit_fitness_doc_url" value="@if($id){{$user->fitness_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> PUC Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="puc_doc_url1" id="puc_doc_url1" class="dropify dropify-event" data-height="200" data-id="@if(isset($user->id)){{$user->id}}@endif" data-pk-key="puc_doc_url_status" data-pk-id="@if(isset($user->vehicle_id)){{$user->vehicle_id}}@endif" data-module="vehicles" @if(!$id) @else data-default-file="{{$user->puc_doc_url}}" @endif onchange="encodeImgtoBase64('puc_doc_url1')"  />
                                                        <input type="hidden" name="puc_doc_url1" id="base64_puc_doc_url1" value="">
                                                        <input type="hidden" name="edit_puc_doc_url1" id="edit_puc_doc_url" value="@if($id){{$user->puc_doc_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required fc-datepicker" name="insurance_exp_date1" id="insurance_exp_date1" value="@if(old('insurance_exp_date1')){{old('insurance_exp_date1')}}@elseif($id){{$user->insurance_exp_date}}@endif">
                                                        <label for="insurance_exp_date1" class="wizard-form-text-label1">Insurance Expiry Date*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="permit_exp_date1" id="permit_exp_date1" value="@if(old('permit_exp_date1')){{old('permit_exp_date1')}}@elseif($id){{$user->permit_exp_date}}@endif">
                                                        <label for="permit_exp_date1" class="wizard-form-text-label1">Permit Expiry Date</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="fitness_exp_date1" id="fitness_exp_date1" value="@if(old('fitness_exp_date1')){{old('fitness_exp_date1')}}@elseif($id){{$user->fitness_exp_date}}@endif">
                                                        <label for="fitness_exp_date1" class="wizard-form-text-label1">Fitness Expiry Date</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>                                                
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="puc_exp_date1" id="puc_exp_date1" value="@if(old('puc_exp_date1')){{old('puc_exp_date1')}}@elseif($id){{$user->puc_exp_date}}@endif">
                                                        <label for="puc_exp_date1" class="wizard-form-text-label1">PUC Expiry Date</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Agreement Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="agreement_doc_url" id="agreement_doc_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->agreement_doc_url}}" @endif onchange="encodeImgtoBase64('agreement_doc_url')"  />
                                                        <input type="hidden" name="agreement_doc_url" id="base64_agreement_doc_url" value="">
                                                        <input type="hidden" name="edit_agreement_doc_url" value="@if($id){{$user->agreement_doc_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn1 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn1 float-right">Next</a>
                                            </div>
                                        
                                        </fieldset>

                                        <fieldset class="wizard-fieldset1">
                                            <h5>Driver Details</h5>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_mobile_numebr" id="driver_mobile_numebr" value="@if(old('driver_mobile_numebr')){{old('driver_mobile_numebr')}}@elseif($id && !empty($driver)){{$driver->mobile_numebr}}@endif">
                                                        <label for="driver_mobile_numebr" class="wizard-form-text-label1">Mobile Number*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_first_name" id="driver_first_name" value="@if(old('driver_first_name')){{old('driver_first_name')}}@elseif($id && !empty($driver)){{$driver->first_name}}@endif">
                                                        <label for="driver_first_name" class="wizard-form-text-label1">First Name*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_last_name" id="driver_last_name" value="@if(old('driver_last_name')){{old('driver_last_name')}}@elseif($id && !empty($driver)){{$driver->last_name}}@endif">
                                                        <label for="driver_last_name" class="wizard-form-text-label1">Last Name*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="year_of_experience" id="year_of_experience" value="@if(old('year_of_experience')){{old('year_of_experience')}}@elseif($id && !empty($driver)){{$driver->year_of_experience}}@endif">
                                                        <label for="year_of_experience" class="wizard-form-text-label1">Year Of Experience*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="adhar_card_no" id="adhar_card_no" value="@if(old('adhar_card_no')){{old('adhar_card_no')}}@elseif($id && !empty($driver)){{$driver->adhar_card_no}}@endif" onkeypress="return /[0-9]/i.test(event.key)" maxlength="20">
                                                        <label for="adhar_card_no" class="wizard-form-text-label1">Adhar Card Number*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driving_licence_no" id="driving_licence_no" value="@if(old('driving_licence_no')){{old('driving_licence_no')}}@elseif($id && !empty($driver)){{$driver->driving_licence_no}}@endif">
                                                        <label for="driving_licence_no" class="wizard-form-text-label1">Driving Licence No*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required fc-datepicker" name="driving_licence_expiry_date" id="driving_licence_expiry_date" value="@if(old('driving_licence_expiry_date')){{old('driving_licence_expiry_date')}}@elseif($id && !empty($driver)){{$driver->driving_licence_expiry_date}}@endif">
                                                        <label for="driving_licence_expiry_date" class="wizard-form-text-label1">Driving Licence Expiry Date*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="pan_card_number" id="pan_card_number" value="@if(old('pan_card_number')){{old('pan_card_number')}}@elseif($id && !empty($driver)){{$driver->pan_card_number}}@endif">
                                                        <label for="pan_card_number" class="wizard-form-text-label1">Pan Card Number*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div> -->
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="street_address" id="street_address" value="@if(old('street_address')){{old('street_address')}}@elseif($id && !empty($driver)){{$driver->street_address}}@endif">
                                                        <label for="street_address" class="wizard-form-text-label1">Street Address*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="city_name" id="city_name" value="@if(old('city_name')){{old('city_name')}}@elseif($id && !empty($driver)){{$driver->city}}@endif">
                                                        <label for="city_name" class="wizard-form-text-label1">City*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="pincode" id="pincode" value="@if(old('pincode')){{old('pincode')}}@elseif($id && !empty($driver)){{$driver->pincode}}@endif">
                                                        <label for="pincode" class="wizard-form-text-label1">Pincode*</label>
                                                        <div class="wizard-form-error1"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> DL Front Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dl_front_url" id="dl_front_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->dl_front_url}}@endif" @endif onchange="encodeImgtoBase64('dl_front_url')"  />
                                                        <input type="hidden" name="dl_front_url" id="base64_dl_front_url" value="">
                                                        <input type="hidden" name="edit_dl_front_url" value="@if($id && !empty($driver)){{$driver->dl_front_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> DL Back Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dl_back_url" id="dl_back_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->dl_back_url}}@endif" @endif onchange="encodeImgtoBase64('dl_back_url')"  />
                                                        <input type="hidden" name="dl_back_url" id="base64_dl_back_url" value="">
                                                        <input type="hidden" name="edit_dl_back_url" value="@if($id && !empty($driver)){{$driver->dl_back_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Police Verification <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="police_verification_url" id="police_verification_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->police_verification_url}}@endif" @endif onchange="encodeImgtoBase64('police_verification_url')"  />
                                                        <input type="hidden" name="police_verification_url" id="base64_police_verification_url" value="">
                                                        <input type="hidden" name="edit_police_verification_url" value="@if($id && !empty($driver)){{$driver->police_verification_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- <div class="row">    
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Pan Card Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="d_pan_card_url" id="d_pan_card_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->pan_card_url}}@endif" @endif onchange="encodeImgtoBase64('d_pan_card_url')"  />
                                                        <input type="hidden" name="d_pan_card_url" id="base64_d_pan_card_url" value="">
                                                        <input type="hidden" name="edit_d_pan_card_url" value="@if($id && !empty($driver)){{$driver->pan_card_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Adhar Card Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="d_adhar_card_url" id="d_adhar_card_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->adhar_card_url}}@endif" @endif onchange="encodeImgtoBase64('d_adhar_card_url')"  />
                                                        <input type="hidden" name="d_adhar_card_url" id="base64_d_adhar_card_url" value="">
                                                        <input type="hidden" name="edit_d_adhar_card_url" value="@if($id && !empty($driver)){{$driver->adhar_card_url}}@endif">

                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Adhar Card Back <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="adhar_card_back_url" id="adhar_card_back_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="@if(!empty($driver)){{$driver->adhar_card_back_url}}@endif" @endif onchange="encodeImgtoBase64('adhar_card_back_url')"  />
                                                        <input type="hidden" name="adhar_card_back_url" id="base64_adhar_card_back_url" value="">
                                                        <input type="hidden" name="edit_adhar_card_back_url" value="@if($id && !empty($driver)){{$driver->adhar_card_back_url}}@endif">
                                                    </div>
                                                </div>
                                            </div> -->
                                            
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn1 float-left">Previous</a>
                                                <button type="button" class="form-wizard-submit1 float-right" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">Submit</button>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>

                                <!-- Driver cum owner -->
                                <div class="form-wizard2" style="display:none;">
                                    <form action="" method="post" id="frmstep2" role="form" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" class="action_type" name="action_type" id="action_type" value="{{$action_type}}">
                                        <input type="hidden" class="id" name="id" id="id" value="{{$id}}">
                                        <input type="hidden" name="user_id2" id="user_id2">
                                        <input type="hidden" name="hid_agent_id2" id="hid_agent_id2">
                                        <input type="hidden" name="hid_usertype2" id="hid_usertype2">
                                        
                                        <input type="hidden" name="hid_first_name" id="hid_first_name">
                                        <input type="hidden" name="hid_last_name" id="hid_last_name">
                                        <input type="hidden" name="hid_state" id="hid_state">
                                        <input type="hidden" name="hid_city_id" id="hid_city_id">                                        
                                        <input type="hidden" name="hid_email" id="hid_email">
                                        <input type="hidden" name="hid_mobile_number" id="hid_mobile_number">
                                        <input type="hidden" name="hid_gender" id="hid_gender">
                                        <!-- <input type="hidden" name="vehicle_id" id="vehicle_id" @if($id) value="{{$user->vehicle_id}}" @endif> -->
                                        <input type="hidden" class="vehicle_id_step3" name="vehicle_id" id="vehicle_id" value="@if($id){{$user->vehicle_id}}@endif">
                                        <input type="hidden" class="driver_id_step3" name="driver_id" id="driver_id" value="@if($id){{$user->driver_id}}@endif">
                                        <input type="hidden" name="bank_account_id" id="bank_account_id" value="@if($id){{ $user->bank_account_id }}@endif">

                                        <div class="form-wizard-header2">
                                            <ul class="list-unstyled form-wizard-steps2 clearfix">
                                                <li class="active"><span>1</span></li>
                                                <li><span>2</span></li> 
                                                <li><span>3</span></li> 
                                                <li><span>4</span></li>   
                                                <li><span>5</span></li>                                          
                                            </ul>
                                        </div>
                                        <fieldset class="wizard-fieldset2 show">
                                            <h5>Vehicle Information</h5>
                                            <div class="row">    
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="vehicle_number" id="vehicle_number" value="@if(old('vehicle_number')){{old('vehicle_number')}}@elseif($id){{$user->vehicle_number}}@endif">
                                                        <label for="vehicle_number" class="wizard-form-text-label2">Vehicle Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="state1" id="state1" value="@if(old('state1')){{old('state1')}}@elseif($id){{$user->state}}@endif">
                                                        <label for="state" class="wizard-form-text-label2">State*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="city1" id="city1" value="@if(old('city1')){{old('city1')}}@elseif($id){{$user->city}}@endif">
                                                        <label for="city" class="wizard-form-text-label2">City*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-12">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="owner_name_rc2" id="owner_name_rc2" value="@if(old('owner_name_rc2')){{old('owner_name_rc2')}}@elseif($id){{$user->v_owner_name}}@endif">
                                                        <label for="owner_name_rc2" class="wizard-form-text-label2">Owner Name RC*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> Upload RC Book Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="rc_front_url2" id="rc_front_url2" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->rc_front_url}}" @endif onchange="encodeImgtoBase64('rc_front_url2')"  />
                                                        <input type="hidden" name="rc_front_url2" id="base64_rc_front_url2" value="">
                                                        <input type="hidden" name="edit_rc_front_url2" value="@if($id){{$user->rc_front_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> Upload RC Book Back <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="rc_back_url2" id="rc_back_url2" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->rc_back_url}}" @endif onchange="encodeImgtoBase64('rc_back_url2')"  />
                                                        <input type="hidden" name="rc_back_url2" id="base64_rc_back_url2" value="">
                                                        <input type="hidden" name="edit_rc_back_url2" value="@if($id){{$user->rc_back_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-next-btn2 float-right">Next</a>
                                            </div>
                                        </fieldset>	

                                        <fieldset class="wizard-fieldset2">
                                            <h5>Select Brand & Model</h5>

                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="vehicle_type_id2" id="vehicle_type_id2">
                                                            <option value="" label="Select Vehicle Type"></option>
                                                            @foreach($vehicle_types as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->vehicle_type_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="brand_id2" id="brand_id2">
                                                            <option value="" label="Select Brand"></option>
                                                            @foreach($brands as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->brand_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="model_id2" id="model_id2">
                                                            <option value="" label="Select Model"></option>
                                                            @if($id)                                            
                                                                @foreach($models as $val)
                                                                <option value="{{ $val->id }}" @if($id) @if($val->id == $user->model_id) selected @endif @endif>{{ $val->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="registration_year2" id="registration_year2">
                                                            <option value="" label="Select Registration Year">Select Registration Year</option>
                                                            @foreach($years as $val)
                                                            <option value="{{ $val }}" @if($id) @if($val == $user->registration_year) selected @endif @endif>{{ $val }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="fuel_type_id2" id="fuel_type_id2">
                                                            <option value="" label="Select Fuel Type">Select Fuel Type</option>
                                                            @if($id)                                            
                                                                @foreach($fuel_types as $val)
                                                                <option value="{{ $val->id }}" @if($id) @if($val->id == $user->fuel_type_id) selected @endif @endif>{{ $val->name }}</option>
                                                                @endforeach
                                                            @endif
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-6">
                                                    <div class="form-group">
                                                        <select class="form-control wizard-required" name="colour_id2" id="colour_id2" required="">
                                                            <option value="" label="Select Vehicle Colour">Select Vehicle Colour</option>
                                                            @foreach($vehicle_colours as $val)
                                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $user->colour_id) selected @endif @endif>{{ $val->name }}</option>
                                                            @endforeach
                                                        </select>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn2 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn2 float-right">Next</a>
                                            </div>
                                        </fieldset>

                                        <fieldset class="wizard-fieldset2">
                                            <h5>Vehicle Documents</h5>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Insurance Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="insurance_doc_url" id="insurance_doc_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->insurance_doc_url}}" @endif onchange="encodeImgtoBase64('insurance_doc_url')"  />
                                                        <input type="hidden" name="insurance_doc_url" id="base64_insurance_doc_url" value="">
                                                        <input type="hidden" name="edit_insurance_doc_url" value="@if($id){{$user->insurance_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Permit Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="permit_doc_url" id="permit_doc_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->permit_doc_url}}" @endif onchange="encodeImgtoBase64('permit_doc_url')"  />
                                                        <input type="hidden" name="permit_doc_url" id="base64_permit_doc_url" value="">
                                                        <input type="hidden" name="edit_permit_doc_url" value="@if($id){{$user->permit_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Fitness Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="fitness_doc_url" id="fitness_doc_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->fitness_doc_url}}" @endif onchange="encodeImgtoBase64('fitness_doc_url')"  />
                                                        <input type="hidden" name="fitness_doc_url" id="base64_fitness_doc_url" value="">
                                                        <input type="hidden" name="edit_fitness_doc_url" value="@if($id){{$user->fitness_doc_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> PUC Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="puc_doc_url" id="puc_doc_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->puc_doc_url}}" @endif onchange="encodeImgtoBase64('puc_doc_url')"  />
                                                        <input type="hidden" name="puc_doc_url" id="base64_puc_doc_url" value="">
                                                        <input type="hidden" name="edit_puc_doc_url" value="@if($id){{$user->puc_doc_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required fc-datepicker" name="insurance_exp_date" id="insurance_exp_date" value="@if(old('insurance_exp_date')){{old('insurance_exp_date')}}@elseif($id){{$user->insurance_exp_date}}@endif">
                                                        <label for="insurance_exp_date" class="wizard-form-text-label2">Insurance Expiry Date</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="permit_exp_date" id="permit_exp_date" value="@if(old('permit_exp_date')){{old('permit_exp_date')}}@elseif($id){{$user->permit_exp_date}}@endif">
                                                        <label for="permit_exp_date" class="wizard-form-text-label2">Permit Expiry Date</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="fitness_exp_date" id="fitness_exp_date" value="@if(old('fitness_exp_date')){{old('fitness_exp_date')}}@elseif($id){{$user->fitness_exp_date}}@endif">
                                                        <label for="fitness_exp_date" class="wizard-form-text-label2">Fitness Expiry Date</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>                                                
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control fc-datepicker" name="puc_exp_date" id="puc_exp_date" value="@if(old('puc_exp_date')){{old('puc_exp_date')}}@elseif($id){{$user->puc_exp_date}}@endif">
                                                        
                                                        <label for="puc_exp_date" class="wizard-form-text-label2">PUC Expiry Date</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <p class="mg-b-10"> Agreement Document</p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="agreement_doc_url1" id="agreement_doc_url1" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->agreement_doc_url}}" @endif onchange="encodeImgtoBase64('agreement_doc_url1')"  />
                                                        <input type="hidden" name="agreement_doc_url1" id="base64_agreement_doc_url1" value="">
                                                        <input type="hidden" name="edit_agreement_doc_url1" value="@if($id){{$user->agreement_doc_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn2 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn2 float-right">Next</a>
                                            </div>
                                        
                                        </fieldset>

                                        <fieldset class="wizard-fieldset2">
                                            <h5>Owner Docs</h5>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="dc_adhar_card_no" id="dc_adhar_card_no" value="@if(old('dc_adhar_card_no')){{old('dc_adhar_card_no')}}@elseif($id){{$user->driver_adhar_card_no}}@endif" onkeypress="return /[0-9]/i.test(event.key)" maxlength="20">
                                                        <label for="dc_adhar_card_no" class="wizard-form-text-label2">Adhar Card Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="dc_pan_card_number" id="dc_pan_card_number" value="@if(old('dc_pan_card_number')){{old('dc_pan_card_number')}}@elseif($id){{$user->pan_card_number}}@endif" maxlength="10">
                                                        <label for="dc_pan_card_number" class="wizard-form-text-label2">Pan Card Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Pan Card Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dc_pan_card_url" id="dc_pan_card_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->dc_pan_card_url}}" @endif onchange="encodeImgtoBase64('dc_pan_card_url')"  />
                                                        <input type="hidden" name="dc_pan_card_url" id="base64_dc_pan_card_url" value="">
                                                        <input type="hidden" name="edit_dc_pan_card_url" value="@if($id){{$user->dc_pan_card_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Adhar Card Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dc_adhar_card_url" id="dc_adhar_card_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->dc_adhar_card_url}}" @endif onchange="encodeImgtoBase64('dc_adhar_card_url')"  />
                                                        <input type="hidden" name="dc_adhar_card_url" id="base64_dc_adhar_card_url" value="">
                                                        <input type="hidden" name="edit_dc_adhar_card_url" value="@if($id){{$user->dc_adhar_card_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10"> Adhar Card Front <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dc_adhar_card_back_url" id="dc_adhar_card_back_url" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->dc_adhar_card_back_url}}" @endif onchange="encodeImgtoBase64('dc_adhar_card_back_url')"  />
                                                        <input type="hidden" name="dc_adhar_card_back_url" id="base64_dc_adhar_card_back_url" value="">
                                                        <input type="hidden" name="edit_dc_adhar_card_back_url" value="@if($id){{$user->dc_adhar_card_back_url}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn2 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn2 float-right">Next</a>
                                            </div>
                                        </fieldset>

                                        <fieldset class="wizard-fieldset2">
                                            <h5>Driver Details</h5>

                                            <div class="row">    
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_mobile_numebr2" id="driver_mobile_numebr2" value="@if(old('driver_mobile_numebr2')){{old('driver_mobile_numebr2')}}@elseif($id){{$user->driver_mobile_numebr}}@endif">
                                                        <label for="driver_mobile_numebr2" class="wizard-form-text-label2">Mobile Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_first_name2" id="driver_first_name2" value="@if(old('driver_first_name2')){{old('driver_first_name2')}}@elseif($id){{$user->driver_first_name}}@endif">
                                                        <label for="driver_first_name2" class="wizard-form-text-label2">First Name*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driver_last_name2" id="driver_last_name2" value="@if(old('driver_last_name2')){{old('driver_last_name2')}}@elseif($id){{$user->driver_last_name}}@endif">
                                                        <label for="driver_last_name2" class="wizard-form-text-label2">Last Name*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="year_of_experience2" id="year_of_experience2" value="@if(old('year_of_experience2')){{old('year_of_experience2')}}@elseif($id){{$user->year_of_experience}}@endif">
                                                        <label for="year_of_experience2" class="wizard-form-text-label2">Year Of Experience*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="driving_licence_no2" id="driving_licence_no2" value="@if(old('year_of_experience2')){{old('year_of_experience2')}}@elseif($id){{$user->year_of_experience}}@endif">
                                                        <label for="driving_licence_no2" class="wizard-form-text-label2">Driving Licence No*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required fc-datepicker" name="driving_licence_expiry_date2" id="driving_licence_expiry_date2" value="@if(old('driving_licence_expiry_date2')){{old('driving_licence_expiry_date2')}}@elseif($id){{$user->driving_licence_expiry_date}}@endif">
                                                        <label for="driving_licence_expiry_date2" class="wizard-form-text-label2">Driving Licence Expiry Date*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="street_address2" id="street_address2" value="@if(old('street_address2')){{old('street_address2')}}@elseif($id){{$user->street_address}}@endif">
                                                        <label for="street_address2" class="wizard-form-text-label2">Street Address*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="city_name2" id="city_name2" value="@if(old('city_name2')){{old('city_name2')}}@elseif($id){{$user->driver_city_name}}@endif">
                                                        <label for="city_name2" class="wizard-form-text-label2">City*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" name="pincode2" id="pincode2" value="@if(old('pincode2')){{old('pincode2')}}@elseif($id){{$user->pincode}}@endif">
                                                        <label for="pincode2" class="wizard-form-text-label2">Pincode*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">    
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> DL Front Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dl_front_url2" id="dl_front_url2" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->dl_front_url}}" @endif onchange="encodeImgtoBase64('dl_front_url2')"  />
                                                        <input type="hidden" name="dl_front_url2" id="base64_dl_front_url2" value="">
                                                        <input type="hidden" name="edit_dl_front_url2" value="@if($id){{$user->dl_front_url}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <p class="mg-b-10"> DL Back Document <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="dl_back_url2" id="dl_back_url2" class="dropify" data-height="200" @if(!$id) @else data-default-file="{{$user->dl_back_url}}" @endif onchange="encodeImgtoBase64('dl_back_url2')"  />
                                                        <input type="hidden" name="dl_back_url2" id="base64_dl_back_url2" value="">
                                                        <input type="hidden" name="edit_dl_back_url2" value="@if($id){{$user->dl_back_url}}@endif">
                                                    </div>
                                                </div>

                                            </div>
                                            
                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn2 float-left">Previous</a>
                                                <a href="javascript:;" class="form-wizard-next-btn2 float-right">Next</a>
                                            </div>
                                        </fieldset>
                                        
                                        <fieldset class="wizard-fieldset2">
                                            <h5>Bank Information</h5>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="account_holder_name2" name="account_holder_name2" value="@if(old('account_holder_name2')){{old('account_holder_name')}}@elseif($id && !empty($bank)){{$bank['account_holder_name']}}@endif">
                                                        <label for="account_holder_name2" class="wizard-form-text-label2">Account Holder Name*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="ifsc_code2" name="ifsc_code2" value="@if(old('ifsc_code2')){{old('ifsc_code2')}}@elseif($id && !empty($bank)){{$bank['ifsc_code']}}@endif">
                                                        <label for="ifsc_code2" class="wizard-form-text-label2">IFSC Code*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="bank_name2" name="bank_name2" value="@if(old('bank_name2')){{old('bank_name2')}}@elseif($id && !empty($bank)){{$bank['bank_name']}}@endif">
                                                        <label for="bank_name2" class="wizard-form-text-label2">Bank Name*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="branch_name2" name="branch_name2" value="@if(old('branch_name2')){{old('branch_name2')}}@elseif($id && !empty($bank)){{$bank['branch_name']}}@endif">
                                                        <label for="branch_name2" class="wizard-form-text-label2">Branch Name*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="account_number2" name="account_number2" value="@if(old('account_number2')){{old('account_number2')}}@elseif($id && !empty($bank)){{$bank['account_number']}}@endif" onkeypress="return /[0-9]/i.test(event.key)">
                                                        <label for="account_number2" class="wizard-form-text-label2">Account Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group">
                                                        <input type="text" class="form-control wizard-required" id="confirm_account_number2" name="confirm_account_number2" value="@if(old('confirm_account_number2')){{old('confirm_account_number2')}}@elseif($id && !empty($bank)){{$bank['account_number']}}@endif" onkeypress="return /[0-9]/i.test(event.key)">
                                                        <label for="account_number2" class="wizard-form-text-label2">Confirm Account Number*</label>
                                                        <div class="wizard-form-error2"></div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-lg-4">
                                                    <p class="mg-b-10">Upload cheque or Passbook <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="cheque_book2" id="cheque_book2" class="dropify wizard-required" data-height="200" @if(!$id) required="" @else @if(!empty($bank)) data-default-file="{{$bank['document_url']}}" @endif @endif onchange="encodeImgtoBase64('cheque_book2')"/>
                                                        <input type="hidden" name="cheque_book2" id="base64_cheque_book2" value="">
                                                        <input type="hidden" name="edit_cheque_book2" value="@if($id && !empty($bank)){{$bank['document_url']}}@endif">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group clearfix">
                                                <a href="javascript:;" class="form-wizard-previous-btn2 float-left">Previous</a>
                                                <button type="button" class="form-wizard-submit2 float-right" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">Submit</button>
                                            </div>
                                        </fieldset>
                                    </form>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Main Content-->

@endsection

@push('pageModals')
@endpush

@push('pageJs')
<script>
@if(Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif

$(document).ready(function() {
    $('.select2').select2({
        selectOnClose: true
    });

    /* $('.form-wizard-submit').on('click', function() {
        var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
            $this.button('reset');
        }, 5000);
    }); */

    $('#stateid').on('change', function() {
        var state_id = this.value;
        $.ajax({
            url: '{{ route("admin.register.statecity") }}',
            type: "POST",
            data: {
                id: state_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#city_id").html(result);
            }
        });
    });

    $('#brand_id').on('change', function() {
        var brand_id = this.value;
        var vehicle_type_id = $('#vehicle_type_id').val();
        $.ajax({
            url: '{{ route("admin.register.models") }}',
            type: "POST",
            data: {
                id: brand_id,
                vehicle_type_id: vehicle_type_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#model_id").html(result);
            }
        });
    });

    $('#brand_id2').on('change', function() {
        var brand_id = this.value;
        var vehicle_type_id = $('#vehicle_type_id2').val();
        $.ajax({
            url: '{{ route("admin.register.models") }}',
            type: "POST",
            data: {
                id: brand_id,
                vehicle_type_id: vehicle_type_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#model_id2").html(result);
            }
        });
    });

    $('#model_id').on('change', function() {
        var model_id = this.value;
        $.ajax({
            url: '{{ route("admin.register.vehicleFuelType") }}',
            type: "POST",
            data: {
                id: model_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#fuel_type_id").html(result);
            }
        });

        $.ajax({
            url: '{{ route("admin.register.vehicleColour") }}',
            type: "POST",
            data: {
                id: model_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#colour_id").html(result);
            }
        });
        
    });

    $('#model_id2').on('change', function() {
        var model_id = this.value;
        $.ajax({
            url: '{{ route("admin.register.vehicleFuelType") }}',
            type: "POST",
            data: {
                id: model_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#fuel_type_id2").html(result);
            }
        });

        $.ajax({
            url: '{{ route("admin.register.vehicleColour") }}',
            type: "POST",
            data: {
                id: model_id
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function(result){
                $("#colour_id2").html(result);
            }
        });
        
    });
    
    $('.driver').css('display','none');
    $('.bank').css('display','none');
    $('.travel').css('display','none');

    $('.driver').removeAttr('required');
    $('.bank').removeAttr('required');
    $('.travel').removeAttr('required');

    <?php if($user_type_id == 6){?>
        $('.driver').css('display','none');
        $('.bank').css('display','none');
        $('.travel').css('display','none');
        $('.driver-only').css('display','none');
        
        $('#agent_id').removeAttr('required');
        $('.driver').removeAttr('required');
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required');
    <?php } else if($user_type_id == 4){ ?>
        $('.driver').css('display','');
        $('.travel').css('display','none');
        $('.bank').css('display','none');

        /* $('.driver').attr('required', true);
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required'); */
        $('.driver').removeAttr('required');
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required');
    <?php } else if($user_type_id == 5){ ?>
        $('.driver').css('display','');
        //$('.travel').css('display','');
        $('.bank').css('display','');
        $('.driver-only').css('display','none');
        $('.travel').css('display','none');

        /* $('.driver').attr('required', true);
        $('.bank').attr('required', true);
        $('.travel').attr('required', true); */
        $('.driver').removeAttr('required');
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required');
    <?php } else if($user_type_id == 2){ ?>
        $('.driver').css('display','');
        $('.travel').css('display','');
        $('.bank').css('display','');

        /* $('.driver').attr('required', true);
        $('.bank').attr('required', true);
        $('.travel').attr('required', true); */
        $('.driver').removeAttr('required');
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required');
    <?php } else if($user_type_id == 3){ ?>
        $('.driver').css('display','');
        $('.travel').css('display','');
        $('.bank').css('display','');

       /*  $('.driver').attr('required', true);
        $('.bank').attr('required', true);
        $('.travel').attr('required', true); */
        $('.driver').removeAttr('required');
        $('.bank').removeAttr('required');
        $('.travel').removeAttr('required');
    <?php } ?>

    $("input[name=usertype]:radio").click(function () {

        //alert($('input[name=usertype]:checked').val());

        if ($('input[name=usertype]:checked').val() == "6") {
            $('.driver').css('display','none');
            $('.bank').css('display','none');
            $('.travel').css('display','none');
            $('.driver-only').css('display','none');
        
            $('#agent_id').removeAttr('required');
            $('.driver').removeAttr('required');
            $('.bank').removeAttr('required');
            $('.travel').removeAttr('required');
            
        } else if ($('input[name=usertype]:checked').val() == "4") {
            $('.driver').css('display','');
            $('.travel').css('display','none');
            $('.bank').css('display','none');

            $('.driver').attr('required', true);
            $('.bank').removeAttr('required');
            $('.travel').removeAttr('required');
            
        } else if ($('input[name=usertype]:checked').val() == "5") {
            $('.driver').css('display','');
            $('.bank').css('display','');
            $('.travel').css('display','none');

            $('.driver').attr('required', true);
            $('.bank').attr('required', true);
            $('.travel').attr('required', true);

        } else if ($('input[name=usertype]:checked').val() == "2") {
            $('.driver').css('display','');
            $('.travel').css('display','');
            $('.bank').css('display','');

            $('.driver').attr('required', true);
            $('.bank').attr('required', true);
            $('.travel').attr('required', true);

            $('.step-3-text').text('Agent Information');

        } else if ($('input[name=usertype]:checked').val() == "3") {
            $('.driver').css('display','');
            $('.travel').css('display','');
            $('.bank').css('display','');

            $('.driver').attr('required', true);
            $('.bank').attr('required', true);
            $('.travel').attr('required', true);

            $('.step-3-text').text('Travel Information');
        }
    });   
});

function editProfile(){
    $('#editProfile').modal();
}

function submitEditProfile(){
    $("#selectForm2").submit();
}
</script>

<!-- Internal Form-validation js form-elements-->
<script src="{{asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
<!-- <script src="{{asset('assets/js/form-validation.js') }}"></script> -->
<!-- <script src="{{asset('assets/js/form-elements.js') }}"></script> -->
<script src="{{asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<!-- Internal Fileuploads js-->
<script src="{{asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>
<script src="{{asset('assets/plugins/jquery-steps/jquery.steps.min.js') }}"></script>

<script src="{{asset('assets/plugins/accordion-Wizard-Form/jquery.accordion-wizard.min.js') }}"></script>

<!-- InternalFancy uploader js-->
<script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="{{asset('assets/plugins/jquery-ui/ui/widgets/datepicker.js') }}"></script>
<!-- <script src="{{asset('assets/js/form-elements.js') }}"></script> -->

<script>
    $('.fc-datepicker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
        dateFormat: 'dd-mm-yy'
	});

    function encodeImgtoBase64(type) {
        var fileInput = document.getElementById(type);
        
        var reader = new FileReader();
        reader.readAsDataURL(fileInput.files[0]);

        reader.onload = function () {
            console.log(reader.result);//base64encoded string
            $("#base64_"+type).val(reader.result);
        };
    }

    function vehicle_validation($Number){
        $pattern = "^[a-zA-z]{2}\s[0-9]{2}\s[0-9]{4}$";
        var result = $Number.match($pattern);
        console.log("VEHICLE VALIDATION: "+result);
        /* if (eregi($pattern, $Number)){
            return true;
        }else {
            jQuery('#vehicle_number').siblings(".wizard-form-error1").slideDown();
            return false;
        } */
    }

    jQuery(document).ready(function() {
        
        // click on next button
        jQuery('.form-wizard-next-btn').click(function() {
            var parentFieldset = jQuery(this).parents('.wizard-fieldset');
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
            var next = jQuery(this);
            var nextWizardStep = true;
            parentFieldset.find('.wizard-required').each(function(){
                var thisValue = jQuery(this).val();
                if(thisValue == ""){
                    thisValue = jQuery(this).find(":selected").text();
                }
                
                console.log("First Step: "+thisValue);
                
                if( thisValue == "") {
                    jQuery(this).siblings(".wizard-form-error1").slideDown();
                    nextWizardStep = false;
                }
                else {
                    jQuery(this).siblings(".wizard-form-error").slideUp();
                }
            });

            if(nextWizardStep) {

                var register_step = $('#register_step').val();
                register_step = $('#register_step1').val();
                register_step = $('#register_step2').val();

                console.log("Register Step: "+register_step);

                if (register_step  == 1 || register_step == 3) {

                    var formajax = $('#frmstep')[0];
                    var dataajax = new FormData(formajax);
                    
                    /* $('#frmstep').serialize() */
                    var id = $('#user_id').val();
                    var siteurl = "{{route('admin.register.storefirst',['panel' => Session::get('panel')])}}";
                    var nextWizardStep;
                    $.ajax({
                        url: siteurl,
                        type: "POST",
                        async: false,
                        data: dataajax,
                        processData: false,
                        contentType: false,
                        success: function(response) {

                            if(response != 'mobile_number_exits'){
                                $('#id').val(response);
                                $('#user_id2').val(response);

                                $('#profile_id').val(response);
                                var result = response.split('-');
                                console.log("PROFILE ID: "+ result[1]);
                                if(result){
                                    $('#action_type').val('edit');
                                    $('.action_type').val('edit');
                                    
                                    $('#user_id').val(result[0]);                                
                                    $('#user_id2').val(result[0]);
                                    $('#id').val(result[0]);
                                    $('.id').val(result[0]);
                                    $('#profile_id').val(result[1]);
                                    $('#hid_agent_id').val(result[1]);
                                }
                                //nextWizardStep = true;
                                
                                var fname = $('#fname').val();
                                var lname = $('#lname').val();
                                $('#owner_name').val(fname+' '+lname);

                                next.parents('.wizard-fieldset').removeClass("show","400");
                                currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
                                next.parents('.wizard-fieldset').next('.wizard-fieldset').addClass("show","400");
                                jQuery(document).find('.wizard-fieldset').each(function(){
                                    if(jQuery(this).hasClass('show')){
                                        var formAtrr = jQuery(this).attr('data-tab-content');
                                        jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
                                            if(jQuery(this).attr('data-attr') == formAtrr){
                                                jQuery(this).addClass('active');
                                                var innerWidth = jQuery(this).innerWidth();
                                                var position = jQuery(this).position();
                                                jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
                                            }else{
                                                jQuery(this).removeClass('active');
                                            }
                                        });

                                        if ($('input[name=usertype]:checked').val() == "5") {
                                            $('.form-wizard1').css('display','none');
                                            $('.form-wizard').css('display','none');
                                            $('.form-wizard2').css('display','');

                                            $('#hid_first_name').val(fname);
                                            $('#hid_last_name').val(lname);
                                            $('#hid_state').val($('#stateid').val());
                                            $('#hid_city_id').val($('#city_id').val());
                                            $('#hid_email').val($('#email').val());
                                            $('#hid_mobile_number').val($('#mobile_number').val());
                                            $('#hid_gender').val($('input[name=gender]:checked').val());
                                        }else if($('input[name=usertype]:checked').val() == "3"){
                                            $('.step-3-text').text('Travel Information');
                                        }else if($('input[name=usertype]:checked').val() == "2"){
                                            $('.step-3-text').text('Agent Information');
                                        }
                                    }
                                });

                            }else{
                                Snackbar.show({
                                    pos: 'bottom-right',
                                    text: "Mobile number already exit in our system!",
                                    textColor: "#721c24",
                                    backgroundColor: "#f8d7da",
                                    actionText: 'Okay',
                                    actionTextColor: '#721c24'
                                });
                                nextWizardStep = false;
                            }
                        }
                    });
                }

                //alert(nextWizardStep);
            }
        });

        //click on previous button
        jQuery('.form-wizard-previous-btn').click(function() {
            var counter = parseInt(jQuery(".wizard-counter").text());;
            var prev =jQuery(this);
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
            prev.parents('.wizard-fieldset').removeClass("show","400");
            prev.parents('.wizard-fieldset').prev('.wizard-fieldset').addClass("show","400");
            currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
            jQuery(document).find('.wizard-fieldset').each(function(){
                if(jQuery(this).hasClass('show')){
                    var formAtrr = jQuery(this).attr('data-tab-content');
                    jQuery(document).find('.form-wizard-steps .form-wizard-step-item').each(function(){
                        if(jQuery(this).attr('data-attr') == formAtrr){
                            jQuery(this).addClass('active');
                            var innerWidth = jQuery(this).innerWidth();
                            var position = jQuery(this).position();
                            jQuery(document).find('.form-wizard-step-move').css({"left": position.left, "width": innerWidth});
                        }else{
                            jQuery(this).removeClass('active');
                        }
                    });
                }
            });
        });
        //click on form submit button
        jQuery(document).on("click",".form-wizard .form-wizard-submit" , function(){
            var parentFieldset = jQuery(this).parents('.wizard-fieldset');
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps .active');
            parentFieldset.find('.wizard-required').each(function() {
                var thisValue = jQuery(this).val();
                if(thisValue == ""){
                    thisValue = jQuery(this).find(":selected").text();
                }
                
                if( thisValue == "" ) {
                    jQuery(this).siblings(".wizard-form-error").slideDown();
                }
                else {
                    jQuery(this).siblings(".wizard-form-error").slideUp();
                }
            });

            var $this = $(this);
            $this.button('loading');

            var formajax = $('#frmstep')[0];
            var dataajax = new FormData(formajax);

            var siteurl = "{{route('admin.register.storefirst',['panel' => Session::get('panel')])}}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: dataajax,
                processData: false,
                contentType: false,
                success: function(response) {
                    if ($('input[name=usertype]:checked').val() == "2") {
                        window.location.href = "{{route('admin.register.index', ['panel' => Session::get('panel')])}}";
                    }else if ($('input[name=usertype]:checked').val() == "3") {
                        var result = response.split('-');

                        $('.form-wizard1').css('display','');
                        $('.form-wizard').css('display','none');
                        $('#user_id').val(result[0]);
                        $('#hid_agent_id').val(result[1]);
                        $('#hid_usertype').val(3);                        
                    }
                    $this.button('reset');
                }
            });
        });

        // focus on input field check empty or not
        jQuery(".form-control").on('focus', function(){
            var tmpThis = jQuery(this).val();
            if(tmpThis == '' ) {
                jQuery(this).parent().addClass("focus-input");
            }
            else if(tmpThis !='' ){
                jQuery(this).parent().addClass("focus-input");
            }
        }).on('blur', function(){
            var tmpThis = jQuery(this).val();
            if(tmpThis == '' ) {
                jQuery(this).parent().removeClass("focus-input");
                jQuery(this).siblings('.wizard-form-error').slideDown("3000");
            }
            else if(tmpThis !='' ){
                jQuery(this).parent().addClass("focus-input");
                jQuery(this).siblings('.wizard-form-error').slideUp("3000");
            }
        });

        /* Travel */
        // click on next button
        jQuery('.form-wizard-next-btn1').click(function() {
            var parentFieldset = jQuery(this).parents('.wizard-fieldset1');
            var currentActiveStep = jQuery(this).parents('.form-wizard1').find('.form-wizard-steps1 .active');
            var next = jQuery(this);
            var nextWizardStep = true;
            parentFieldset.find('.wizard-required').each(function(){
                var thisValue = jQuery(this).val();
                if(thisValue == ""){
                    thisValue = jQuery(this).find(":selected").text();
                }
                
                if( thisValue == "") {
                    jQuery(this).siblings(".wizard-form-error1").slideDown();
                    nextWizardStep = false;
                }
                else {
                    jQuery(this).siblings(".wizard-form-error1").slideUp();
                }
            });

            if(nextWizardStep) {
                
                var formajax = $('#frmstep1')[0];
                var dataajax = new FormData(formajax);

                var register_step = $('#register_step3').val();
                if(register_step == 4){
                    var id = $('#user_id').val();
                    var siteurl = "{{route('admin.register.storesecond',['panel' => Session::get('panel')])}}";
                    $.ajax({
                        url: siteurl,
                        type: "POST",
                        data: dataajax,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            //$('#id').val(response);
                            var result = response.split('-');
                            console.log("PROFILE ID: "+ result[1]);
                            if(result){
                                $('.action_type_step2').val('edit');
                                
                                $('#user_id').val(result[0]);
                                $('#id').val(result[0]);
                                $('.vehicle_id').val(result[1]);
                                $('.driver_id').val(result[2]);
                            }
                        }
                    });
                }

                /* var fname = $('#fname').val();
                var lname = $('#lname').val();
                $('#owner_name').val(fname+' '+lname); */

                next.parents('.wizard-fieldset1').removeClass("show","400");
                currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
                next.parents('.wizard-fieldset1').next('.wizard-fieldset1').addClass("show","400");
                jQuery(document).find('.wizard-fieldset1').each(function(){
                    if(jQuery(this).hasClass('show')){
                        var formAtrr = jQuery(this).attr('data-tab-content');
                        jQuery(document).find('.form-wizard-steps1 .form-wizard-step-item1').each(function(){
                            if(jQuery(this).attr('data-attr') == formAtrr){
                                jQuery(this).addClass('active');
                                var innerWidth = jQuery(this).innerWidth();
                                var position = jQuery(this).position();
                                jQuery(document).find('.form-wizard-step-move1').css({"left": position.left, "width": innerWidth});
                            }else{
                                jQuery(this).removeClass('active');
                            }
                        });
                    }
                });
            }
        });
        //click on previous button
        jQuery('.form-wizard-previous-btn1').click(function() {
            var counter = parseInt(jQuery(".wizard-counter").text());
            var prev =jQuery(this);
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps1 .active');
            prev.parents('.wizard-fieldset1').removeClass("show","400");
            prev.parents('.wizard-fieldset1').prev('.wizard-fieldset1').addClass("show","400");
            currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
            jQuery(document).find('.wizard-fieldset1').each(function(){
                if(jQuery(this).hasClass('show')){
                    var formAtrr = jQuery(this).attr('data-tab-content');
                    jQuery(document).find('.form-wizard-steps1 .form-wizard-step-item1').each(function(){
                        if(jQuery(this).attr('data-attr') == formAtrr){
                            jQuery(this).addClass('active');
                            var innerWidth = jQuery(this).innerWidth();
                            var position = jQuery(this).position();
                            jQuery(document).find('.form-wizard-step-move1').css({"left": position.left, "width": innerWidth});
                        }else{
                            jQuery(this).removeClass('active');
                        }
                    });
                }
            });
        });
        //click on form submit button
        jQuery(document).on("click",".form-wizard1 .form-wizard-submit1" , function(){
            var parentFieldset = jQuery(this).parents('.wizard-fieldset1');
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps1 .active');
            parentFieldset.find('.wizard-required').each(function() {
                var thisValue = jQuery(this).val();
                if(thisValue == ""){
                    thisValue = jQuery(this).find(":selected").text();
                }

                if( thisValue == "" ) {
                    jQuery(this).siblings(".wizard-form-error1").slideDown();
                }
                else {
                    jQuery(this).siblings(".wizard-form-error1").slideUp();
                }
            });

            var $this = $(this);
            $this.button('loading');

            var formajax = $('#frmstep1')[0];
            var dataajax = new FormData(formajax);

            var siteurl = "{{route('admin.register.storesecond',['panel' => Session::get('panel')])}}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: dataajax,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href = "{{route('admin.register.index', ['panel' => Session::get('panel')])}}";
                }
            });
        });

        /* Driver cum Owner */
        // click on next button
        jQuery('.form-wizard-next-btn2').click(function() {
            var parentFieldset = jQuery(this).parents('.wizard-fieldset2');
            var currentActiveStep = jQuery(this).parents('.form-wizard2').find('.form-wizard-steps2 .active');
            var next = jQuery(this);
            var nextWizardStep = true;
            parentFieldset.find('.wizard-required').each(function(){
                var thisValue = jQuery(this).val();
                if(thisValue == ""){
                    thisValue = jQuery(this).find(":selected").text();
                }

                if( thisValue == "") {
                    jQuery(this).siblings(".wizard-form-error2").slideDown();
                    nextWizardStep = false;
                }
                else {
                    jQuery(this).siblings(".wizard-form-error2").slideUp();
                }
            });

            if(nextWizardStep) {

                var formajax = $('#frmstep2')[0];
                var dataajax = new FormData(formajax);

                var siteurl = "{{route('admin.register.storethird',['panel' => Session::get('panel')])}}";
                $.ajax({
                    url: siteurl,
                    type: "POST",
                    data: dataajax,
                    processData: false,
                    contentType: false,
                    success: function(response) {

                        $('.id').val(response);
                        $('#user_id2').val(response);
                        $('.action_type').val('edit');

                        var result = response.split('-');
                        console.log("PROFILE ID: "+ result[1]);
                        if(result){
                            $('#action_type').val('edit');
                            $('.action_type').val('edit');
                            
                            $('#user_id').val(result[0]);
                            $('#user_id2').val(result[0]);
                            $('.id').val(result[0]);
                            $('.vehicle_id_step3').val(result[1]);
                            $('.driver_id_step3').val(result[2]);
                        }
                    }
                });

                /* var fname = $('#fname').val();
                var lname = $('#lname').val();
                $('#owner_name').val(fname+' '+lname); */

                next.parents('.wizard-fieldset2').removeClass("show","400");
                currentActiveStep.removeClass('active').addClass('activated').next().addClass('active',"400");
                next.parents('.wizard-fieldset2').next('.wizard-fieldset2').addClass("show","400");
                jQuery(document).find('.wizard-fieldset2').each(function(){
                    if(jQuery(this).hasClass('show')){
                        var formAtrr = jQuery(this).attr('data-tab-content');
                        jQuery(document).find('.form-wizard-steps2 .form-wizard-step-item2').each(function(){
                            if(jQuery(this).attr('data-attr') == formAtrr){
                                jQuery(this).addClass('active');
                                var innerWidth = jQuery(this).innerWidth();
                                var position = jQuery(this).position();
                                jQuery(document).find('.form-wizard-step-move2').css({"left": position.left, "width": innerWidth});
                            }else{
                                jQuery(this).removeClass('active');
                            }
                        });
                    }
                });
            }
        });
        //click on previous button
        jQuery('.form-wizard-previous-btn2').click(function() {
            var counter = parseInt(jQuery(".wizard-counter").text());
            var prev =jQuery(this);
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps2 .active');
            prev.parents('.wizard-fieldset2').removeClass("show","400");
            prev.parents('.wizard-fieldset2').prev('.wizard-fieldset2').addClass("show","400");
            currentActiveStep.removeClass('active').prev().removeClass('activated').addClass('active',"400");
            jQuery(document).find('.wizard-fieldset2').each(function(){
                if(jQuery(this).hasClass('show')){
                    var formAtrr = jQuery(this).attr('data-tab-content');
                    jQuery(document).find('.form-wizard-steps2 .form-wizard-step-item2').each(function(){
                        if(jQuery(this).attr('data-attr') == formAtrr){
                            jQuery(this).addClass('active');
                            var innerWidth = jQuery(this).innerWidth();
                            var position = jQuery(this).position();
                            jQuery(document).find('.form-wizard-step-move2').css({"left": position.left, "width": innerWidth});
                        }else{
                            jQuery(this).removeClass('active');
                        }
                    });
                }
            });
        });
        //click on form submit button
        jQuery(document).on("click",".form-wizard2 .form-wizard-submit2" , function(){
            var parentFieldset = jQuery(this).parents('.wizard-fieldset2');
            var currentActiveStep = jQuery(this).parents('.form-wizard').find('.form-wizard-steps2 .active');
            parentFieldset.find('.wizard-required').each(function() {
                var thisValue = jQuery(this).val();
                if( thisValue == "" ) {
                    jQuery(this).siblings(".wizard-form-error2").slideDown();
                }
                else {
                    jQuery(this).siblings(".wizard-form-error2").slideUp();
                }
            });

            var $this = $(this);
            $this.button('loading');

            var formajax = $('#frmstep2')[0];
            var dataajax = new FormData(formajax);

            var siteurl = "{{route('admin.register.storethird',['panel' => Session::get('panel')])}}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: dataajax,
                processData: false,
                contentType: false,
                success: function(response) {
                    window.location.href = "{{route('admin.register.index', ['panel' => Session::get('panel')])}}";
                }
            });
        });
    });

    $('.dropify').dropify();
    // Used events
    var drEvent = $('.dropify-event').dropify();
        drEvent.on('dropify.beforeClear', function(event, element) {
        /* var image_key_val = $(this).attr("name");
        if(image_key_val =='pan_card'){
            var image_key_val = 'pan_card_url';
        }else if(image_key_val =='addar_card_front'){
            var image_key_val = 'adhar_card_url';
        }else if(image_key_val =='addar_card_back'){
            var image_key_val = 'adhar_card_back_url';
        }
        $('#edit_'+image_key_val).val("NULL"); */

        var id = $(this).attr("data-id");
        var pk_id = $(this).attr("data-pk-id");
        var file_name_with_path = $(this).attr("data-default-file");
        var image_key_val = $(this).attr("name");
        if(image_key_val =='pan_card'){
            var image_key_val = 'pan_card_url';
        }else if(image_key_val =='addar_card_front'){
            var image_key_val = 'adhar_card_url';
        }else if(image_key_val =='addar_card_back'){
            var image_key_val = 'adhar_card_back_url';
        }else if(image_key_val == 'cheque_book'){
            var image_key_val = 'document_url';
        }else if(image_key_val == 'insurance_doc_url1'){
            var image_key_val = 'insurance_doc_url';
        }else if(image_key_val == 'permit_doc_url1'){
            var image_key_val = 'permit_doc_url';
        }else if(image_key_val == 'fitness_doc_url1'){
            var image_key_val = 'fitness_doc_url';
        }else if(image_key_val == 'puc_doc_url1'){
            var image_key_val = 'puc_doc_url';
        }
        var pk_key = $(this).attr("data-pk-key");
        var module_name = $(this).attr("data-module");
        
        $('#edit_'+image_key_val).val("");

        var siteurl = "{{url(Session::get('panel').'/users/deleteImage')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
                "pk_id": pk_id,
                "hidd_type":module_name,
                "pk_key":pk_key,
                "image_key_val": image_key_val,
                "id": id,
                "module_name": module_name
            },
            success: function(response) {
                if(response == 'success'){
                    //$('#edit_'+image_key_val).val("");
                }
            }
        });

        //return confirm("Do you really want to delete \"" + element.file.name + "\" ?");
    });
    drEvent.on('dropify.afterClear', function(event, element) {
        /* alert('File deleted'); */
    });
</script>
@endpush