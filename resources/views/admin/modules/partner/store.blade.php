@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Partner User Details
@else
Add Partner User Details
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
                    <h2 class="main-content-title tx-24 mg-b-5">Partner User</h2>
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
            <div class="row row-sm justify-content-md-center">
                <div class="col-xl-10 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('user.edit_user') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('user.add_user') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.users.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$user->id}}">
                                <input type="hidden" name="profile_id" id="profile_id" value="{{$profile_id}}">
                                <input type="hidden" name="bank_account_id" id="bank_account_id" value="{{$bank_account_id}}">
                                @endif
                                
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('user.register_as') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10 row" id="fnWrapper">
                                            <div class="col-lg-2">
                                                <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="6" @if($id) @if($user_type_id == 6) checked @endif @endif> 
                                                <span>Customer</span></label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="4" @if($id) @if($user_type_id == 4) checked @endif @endif>
                                                <span>Driver</span></label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="5" @if($id) @if($user_type_id == 5) checked @endif @endif>
                                                <span>Driver Cum Owner</span></label>
                                            </div>
                                            <div class="col-lg-2">
                                                <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="2" @if($id) @if($user_type_id == 2) checked @endif @endif>
                                                <span>Agent</span></label>
                                            </div>
                                            <div class="col-lg-3">
                                                <label class="rdiobox"><input name="usertype" id="usertype" type="radio" value="3" @if($id) @if($user_type_id == 3) checked @endif @endif>
                                                <span>Travel Agency</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="row row-sm mg-b-20 driver driver-only">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('user.agnet_name') }}</p>
                                        <select class="form-control select2" name="agent_id" id="agent_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($agent_users as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->agent_id) selected @endif @endif>{{ $val->owner_name." - ".$val->mobile_number }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.first_name') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('first_name')){{old('first_name')}}@elseif($id){{$user->first_name}}@endif" name="first_name" placeholder="Enter {{@trans('user.first_name')}}" required="" type="text" maxlength="50">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.last_name') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('last_name')){{old('last_name')}}@elseif($id){{$user->last_name}}@endif" name="last_name" placeholder="Enter {{@trans('user.last_name')}}" required="" type="text" maxlength="50">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.states') }}</p>
                                        <select class="form-control" name="state" id="state" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($states as $val)
                                            <option value="{{ $val->isoCode }}" @if($id) @if($val->isoCode == $user->state) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.city') }}</p>
                                        <select class="form-control" name="city_id" id="city_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @if($id)                                            
                                            @foreach($cities as $val)
                                            <option value="{{ $val->isoCode }}" @if($id) @if($val->id == $user->city_id) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.email') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('emailid')){{old('emailid')}}@elseif($id){{$user->emailid}}@endif" name="emailid" placeholder="Enter {{@trans('user.email')}}" required="" type="text" maxlength="100">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.mobile_number') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('mobile_number')){{old('mobile_number')}}@elseif($id){{$user->mobile_number}}@endif" name="mobile_number" placeholder="Enter {{@trans('user.mobile_number')}}" required="" type="text" maxlength="10">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.gender') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10 row" id="fnWrapper">
                                            <div class="col-lg-6">
                                                <label class="rdiobox"><input name="gender" type="radio" value="Male" @if($id) @if($user->gender == 'Male') checked @endif @endif> <span>Male</span></label>
                                            </div>
                                            <div class="col-lg-6">
                                                <label class="rdiobox"><input name="gender" type="radio" value="Female" @if($id) @if($user->gender == 'Female') checked @endif @endif>
                                                <span>Female</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20 travel">
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.logo') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="logo" id="logo" class="dropify travel" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->logo}}" @endif/>
                                        </div>
                                    </div>
                                </div>

                                <!-- Vehicle Details -->
                                <div class="row row-sm mg-b-20 travel">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.travel_name') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('travel_name')){{old('travel_name')}}@elseif($id){{$data->travel_name}}@endif" name="travel_name" placeholder="Enter {{@trans('user.travel_name')}}" required="" type="text" maxlength="50">
                                        </div>
                                    </div>
                                    <!-- <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.owner_name') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('owner_name')){{old('owner_name')}}@elseif($id){{$data->owner_name}}@endif" name="owner_name" placeholder="Enter {{@trans('user.owner_name')}}" required="" type="text" maxlength="50">
                                        </div>
                                    </div> -->
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.total_year_of_business') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('total_year_of_business')){{old('total_year_of_business')}}@elseif($id){{$data->total_business_year}}@endif" name="total_year_of_business" placeholder="Enter {{@trans('user.total_year_of_business')}}" required="" type="text">
                                        </div>
                                    </div>
                                </div>
                                <!-- End -->

                                <!-- Documents -->
                                <div class="row row-sm mg-b-20 driver travel">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.adhar_no') }}</p>
                                        <input class="form-control driver travel" value="@if(old('adhar_no')){{old('adhar_no')}}@elseif($id){{$data->adhar_card}}@endif" name="adhar_no" placeholder="Enter {{@trans('user.adhar_no')}}" required="" type="text" maxlength="18">
                                    </div>                                    
                                </div>

                                <div class="row row-sm mg-b-20 driver">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.expiry_date') }}</p>
                                        <input class="form-control driver fc-datepicker hasDatepicker" value="@if(old('expiry_date')){{old('expiry_date')}}@elseif($id){{$data->driving_licence_expiry_date}}@endif" name="expiry_date" placeholder="YYYY-MM-DD" required="" type="text">
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.street_address') }}</p>
                                        <input class="form-control driver" value="@if(old('street_address')){{old('street_address')}}@elseif($id){{$data->street_address}}@endif" name="street_address" placeholder="Enter {{@trans('user.street_address')}}" required="" type="text">
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20 driver">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.pin_code') }}</p>
                                        <input class="form-control driver" value="@if(old('pin_code')){{old('pin_code')}}@elseif($id){{$data->pincode}}@endif" name="pin_code" placeholder="Enter {{@trans('user.pin_code')}}" required="" type="text">
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.license_no') }}</p>
                                        <input class="form-control driver" value="@if(old('license_no')){{old('license_no')}}@elseif($id){{$data->driving_licence_no}}@endif" name="license_no" placeholder="Enter {{@trans('user.license_no')}}" required="" type="text">
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20 driver">
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.driving_license_front') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="driving_license_front" id="driving_license_front" class="dropify driver" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->dl_front_url}}" @endif/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.driving_license_back') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="driving_license_back" id="driving_license_back" class="dropify driver" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->dl_back_url}}" @endif/>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.police_verification') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="police_verification" id="police_verification" class="dropify driver" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                        </div>
                                    </div>
                                </div>
                                <!-- End -->

                                <!-- Owner Document -->
                                <div class="row row-sm mg-b-20 travel">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.pan_card') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="pan_card" id="pan_card" class="dropify travel" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->pan_card_url}}" @endif/>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.adhar_card') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="adhar_card" id="adhar_card" class="dropify travel" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->adhar_card_url}}" @endif/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20 travel">
                                
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.office_no') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('office_no')){{old('office_no')}}@elseif($id){{$data->office_no}}@endif" name="office_no" placeholder="Enter {{@trans('user.office_no')}}" required="" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.pan_card_no') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('pan_card_no')){{old('pan_card_no')}}@elseif($id){{$data->pan_card_no}}@endif" name="pan_card_no" placeholder="Enter {{@trans('user.pan_card_no')}}" required="" type="text">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('user.adhar_card_no') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control travel" value="@if(old('adhar_card_no')){{old('adhar_card_no')}}@elseif($id){{$data->adhar_card_no}}@endif" name="adhar_card_no" placeholder="Enter {{@trans('user.adhar_card_no')}}" required="" type="text">
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank details -->
                                @php
                                    
                                @endphp
                                <div class="row row-sm mg-b-20 bank">
                                    <div class="col-lg-12">
                                        <h6 class="main-content-label mb-1">Bank Details</h6>
                                        <hr>
                                    </div>

                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.account_holder_name') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('account_holder_name')){{old('account_holder_name')}}@elseif($id){{$bank['account_holder_name']}}@endif @endif" name="account_holder_name" placeholder="Enter {{@trans('user.account_holder_name')}}" required="" type="text">
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.ifsc_code') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('ifsc_code')){{old('ifsc_code')}}@elseif($id){{$user->ifsc_code}}@endif @endif" name="ifsc_code" placeholder="Enter {{@trans('user.ifsc_code')}}" required="" type="text">
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20 bank">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.bank_name') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('bank_name')){{old('bank_name')}}@elseif($id){{$bank['bank_name']}}@endif @endif" name="bank_name" placeholder="Enter {{@trans('user.bank_name')}}" required="" type="text">
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.branch_name') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('branch_name')){{old('branch_name')}}@elseif($id){{$bank['branch_name']}}@endif @endif" name="branch_name" placeholder="Enter {{@trans('user.branch_name')}}" required="" type="text">
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20 bank">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.account_number') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('account_number')){{old('account_number')}}@elseif($id){{$bank['account_number']}}@endif @endif" name="account_number" placeholder="Enter {{@trans('user.account_number')}}" required="" type="text">
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('user.confirm_account_number') }}</p>
                                        <input class="form-control bank" value="@if(!empty($bank)) @if(old('confirm_account_number')){{old('confirm_account_number')}}@elseif($id){{$bank['account_number']}}@endif @endif" name="confirm_account_number" placeholder="Enter {{@trans('user.confirm_account_number')}}" required="" type="text">
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20 bank">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('user.cheque_book') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="cheque_book" id="cheque_book" class="dropify bank" data-height="200" @if(!empty($bank)) @if(!$id) required="" @else data-default-file="{{$bank['document_url']}}" @endif @endif/>
                                        </div>
                                    </div>
                                </div>
                                <!-- End -->

                                <div class="mg-t-30">
                                    <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
                                    <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row-->
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

    $('#state').on('change', function() {
        var state_id = this.value;
        $.ajax({
          
            url: '{{ url(Session::get('panel') . '/statecity') }}',
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
        $.ajax({
            url: '{{ url(Session::get('panel') . '/models') }}',
            type: "POST",
            data: {
                id: brand_id
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

    $('#model_id').on('change', function() {
        var model_id = this.value;
        $.ajax({
            url: '{{ url(Session::get('panel') . '/vehicleFuelType') }}',
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
            url: '{{ url(Session::get('panel') . '/vehicleColour') }}',
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

        } else if ($('input[name=usertype]:checked').val() == "3") {
            $('.driver').css('display','');
            $('.travel').css('display','');
            $('.bank').css('display','');

            $('.driver').attr('required', true);
            $('.bank').attr('required', true);
            $('.travel').attr('required', true);
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
<script src="{{asset('assets/js/form-validation.js') }}"></script>
<!-- <script src="{{asset('assets/js/form-elements.js') }}"></script> -->
<script src="{{asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<!-- Internal Fileuploads js-->
<script src="{{asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

<!-- InternalFancy uploader js-->
<script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
    $('.fc-datepicker').datepicker({
		showOtherMonths: true,
		selectOtherMonths: true,
        dateFormat: 'yy-mm-dd'
	});
</script>
@endpush