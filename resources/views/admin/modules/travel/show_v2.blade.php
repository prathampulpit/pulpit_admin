@extends('admin.layouts.main')

@section('title')
User Details
@endsection

@section('content')
<style>
    .image-div {
        height: 260px !important;
        width: 100% !important;
    }
    .edit_submit {
        display: none;
    }

    .edit_travel {
        display: none;
    }

    .information {
        display: flex;
        justify-content: space-between;
    }

    .breadcrumb {
        background: none;
        padding-left: 0 !important;
        padding: 0;
        margin-bottom: 0;
    }

    .hide {
        display: none;
        width: 42rem;
    }

    td,
    th {
        border: 1px solid #dddddd;
        text-align: left;
        padding: 8px;
    }

    tr:nth-child(even) {
        /* background-color: #dddddd; */
    }


    .msg_style {
        border: 1px solid #d7d2d4;
        border-radius: 10px;
        padding: 18px;
        margin-bottom: 20px;
        margin-left: 18px;

    }

    .scroll {
        overflow-y: scroll;
        max-height: 78rem;

    }
</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            {{-- <div class="information">
                    <div class="page-header">
            <div>
                <h2 class="main-content-title tx-24 mg-b-5">Travel Details</h2>
                <ol class="breadcrumb"><li class="breadcrumb-item"><a
                            href="{{ route('admin.users.index', ['panel' => Session::get('panel')]) }}">Travel</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Details</li>
       </ol>
                        </div>
<div>

            <input type="hidden" name="user_reset_id" id="user_reset_id" value="{{ $user->id }}">
            <button type="button" class="btn btn-primary" onclick="confirmModal()">Reset Profile</button>

        </div>

    </div>
    @if (!empty($last_updated_user_details))
    <div>
        <h4 class="mg-b-5"><span style="color: #686868;">Last Updated By:</span>
            {{ $last_updated_user_details->first_name }} {{ $last_updated_user_details->last_name }}
        </h4>
    </div>
    @endif
</div> --}}
<div class="row" style="padding: 19px;">
    <div class="col-xl-3 col-lg-3 col-sm-3 col-md-3">
        <h2 class="main-content-title tx-24 mg-b-5">Travel Details</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a
                    href="{{ route('admin.users.index', ['panel' => Session::get('panel')]) }}">Travel</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Details</li>
        </ol>
    </div>
    @if (!empty($last_updated_user_details))
    <div class="col-xl-6 col-lg-6 col-sm-6 col-md-6">

        <div  style="float: right">
            <h4 class="mg-b-5"><span style="color: #686868;">Last Updated By:</span>
                {{ $last_updated_user_details->first_name }} {{ $last_updated_user_details->last_name }}
            </h4>
        </div>


    </div>
    @endif
    <div class="col-xl-3 col-lg-3 col-sm-3 col-md-3">
        <div style="float: right">
            <input type="hidden" name="user_reset_id" id="user_reset_id" value="{{ $user->id }}">
            <button type="button" class="btn btn-primary" onclick="confirmModal()">Reset Profile</button>

            <button type="button" class="btn btn-primary" onclick="HistoryShow()">Comment & History</button>
        </div>
    </div>
</div>

<!-- End Page Header -->
<input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
<input type="hidden" name="" id="user_data" value="{{ $user }}">
@if (!empty($banks))
<input type="hidden" name="" id="bankId" value="{{ $banks }}">
@endif
@if (!empty($agent_details))
<input type="hidden" name="agent_id" id="agent_id" value="{{ $agent_details->id }}">
@endif
@if (empty($user))
<div class="row row-sm">
    <div class="col-lg-12 col-md-12 text-center">
        Sorry, Something went wrong!...
    </div>
</div>
@else
<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card main-content-body-profile">
            <div class="tab-content">

                <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                    <div class="" style="display: flex;">
                        <div class="card-body p-0 border p-0 rounded-10">
                            <!-- <div class="border-top"></div> -->

                            <div class="p-4">
                                <label class="main-content-label tx-13 mg-b-20">Owner</label>
                                <div class="d-flex-end float-right">
                                    {{-- <div class="justify-content-end"> --}}
                                    <form id='submit-form'>
                                        <button type='submit' class="btn btn-primary"><i
                                                class="ti-pencil"></i></button>
                                    </form>
                                </div>
                                <div class="d-sm-flex">
                                    <div class="row col-md-12">
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.name') }}</span>
                                                            <div class="user_edit">
                                                                {{ ucwords(strtolower($user->first_name)) . ' ' . ucwords(strtolower($user->last_name)) }}
                                                            </div>
                                                            <input type="hidden"
                                                                   class="form-control rider_name"
                                                                   name="" id="user_edit_detail"
                                                                   value="{{ ucwords(strtolower($user->first_name)) . ' ' . ucwords(strtolower($user->last_name)) }}">
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>Mobile</span>
                                                            <div class="">
                                                                {{ $user->mobile_number }}</div>
                                                            {{-- <input type="hidden"
                                                                                    class="form-control mobile_number"
                                                                                    name="mobile_number" id="user_edit_detail"
                                                                                    value="{{ $user->mobile_number }}"> --}}
                                                        </div>


                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.email') }}</span>
                                                            <div class="">{{ $user->emailid }}
                                                            </div>
                                                            {{-- <input type="hidden"
                                                                                    class="form-control emailid" name="emailid"
                                                                                    id="user_edit_detail"
                                                                                    value="{{ $user->emailid }}"> --}}

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="result"></div>
                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>No Of Trip</span>
                                                            <div>{{ $customer_trip }}</div>
                                                            {{-- <input type="hidden" class="form-control customer_trip" name="customer_trip" id="user_edit_detail" value="{{$customer_trip}}"> --}}

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- <div class="col-md-3 pl-0">
                                                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                                                <div class="main-profile-contact-list">
                                                                                                    <div class="media">
                                                                                                        <div class="media-body"> <span>{{ @trans('user.money_spent') }}</span>
                                                                                                            <div>{{ $money_spent }}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div> -->
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"> <span>Reference
                                                                Code</span>
                                                            <div class="">
                                                                {{ $user->reference_code }}</div>
                                                            {{-- <input type="hidden"
                                                                                    class="form-control reference_code"
                                                                                    name="reference_code"
                                                                                    id="user_edit_detail"
                                                                                    value="{{ $user->reference_code }}"> --}}

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"> <span>State</span>
                                                            <div class="user_edit">
                                                                {{ $user->state_name }}
                                                            </div>
                                                            <select
                                                                class="form-control wizard-required edit_submit state_name"
                                                                name="state" id="stateid"
                                                                onclick="select_state_city()">


                                                                <option value=""
                                                                        label="Select State">
                                                                </option>
                                                                @foreach ($states as $item)
                                                                <option
                                                                    value="{{ $item->id }}"
                                                                    @if ($item->name == $user->state_name) selected @endif>
                                                                    {{ $item->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"> <span>City</span>
                                                            <div class="user_edit">
                                                                {{ $user->city_name }}
                                                            </div>
                                                            <select
                                                                class="form-control wizard-required edit_submit city_name"
                                                                name="city_id" id="city_id">
                                                                <option value=""
                                                                        label="Select City"></option>

                                                                @foreach ($cities as $val)
                                                                <option
                                                                    value="{{ $val->id }}"
                                                                    @if ($val->name == $user->city_name) selected @else @endif>
                                                                    {{ $val->name }}</option>
                                                                @endforeach

                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                        // dd($user);
                                        @endphp
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"> <span>Status</span>
                                                            <div class="user_edit">
                                                                @if ($user->user_interest_status == 1)
                                                                White
                                                                @elseif($user->user_interest_status == 2)
                                                                Red
                                                                @else
                                                                -
                                                                @endif
                                                            </div>
                                                            <input type="hidden"
                                                                   value="{{ $user->user_interest_status }}"
                                                                   id="new_user_interest_status">
                                                            <select class="form-control edit_submit"
                                                                    id="user_interest_status"
                                                                    name="int_status">
                                                                <option value="0"
                                                                        @if ($user->user_interest_status == 0) selected @endif>
                                                                    Selected</option>
                                                                <option value="2"
                                                                        @if ($user->user_interest_status == 2) selected @endif>
                                                                    Red (Not Interested)</option>
                                                                <option value="1"
                                                                        @if ($user->user_interest_status == 1) selected @endif>
                                                                    White (White Number Plate)</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- <div class="col-md-3 pl-0">
                                                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                                                <div class="main-profile-contact-list">
                                                                                                    <div class="media">
                                                                                                        <div class="media-body"> <span>{{ @trans('user.referrals_done') }}</span>
                                                                                                            <div>{{ $referrals_done }}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div> -->

                                        <!-- <div class="col-md-3" style="display:none;">
                                                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                                                <div class="main-profile-contact-list">
                                                                                                    <div class="media">
                                                                                                        <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-location-pin sidemenu-icon"></i> </div>
                                                                                                        <div class="media-body"> <span>{{ @trans('user.current_location') }}</span>
                                                                                                            <div>{{ $current_location }}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div> -->

                                        <!-- <div class="col-md-3" style="display:none;">
                                                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                                                <div class="main-profile-contact-list">
                                                                                                    <div class="media">
                                                                                                        <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-map-alt sidemenu-icon"></i> </div>
                                                                                                        <div class="media-body"> <span>{{ @trans('user.trip_status') }}</span>
                                                                                                            <div>{{ $trip_status }}</div>
                                                                                                        </div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>  -->

                                    </div>
                                </div>
                                <button type="button" class="edit_submit btn-primary btn-sm mt-2"
                                        id="travelUser" onclick="editUserDetails()">Submit</button>
                            </div> <!-- user -->

                            @if ($user->user_type_id == '2' || $user->user_type_id == '3')
                            <div class="border-top"></div>
                            <div class="p-4">
                                <label class="main-content-label tx-13 mg-b-20">Personal Information</label>
                                <div class="d-flex-end float-right">
                                    <form id='agent_id'>
                                        <button type='submit' class="btn btn-primary"><i
                                                class="ti-pencil"></i></button>
                                    </form>
                                </div>
                                {{-- @php
                                                            dd($agent_details);
                                                        @endphp  --}}
                                <div class="d-sm-flex">
                                    <div class="row col-md-12">
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>Traveller Name</span>
                                                            @if (!empty($agent_details->travel_name))
                                                            <div class="travel_edit">
                                                                {{ $agent_details->travel_name }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden"
                                                                   class="form-control travel_name"
                                                                   name="travel_name"
                                                                   id="travel_name"
                                                                   value="@if (!empty($agent_details->travel_name)) {{ $agent_details->travel_name }} @else @endif">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.owner_name') }}</span>
                                                            @if (!empty($agent_details->owner_name))
                                                            <div class="travel_edit">
                                                                {{ ucwords(strtolower($agent_details->owner_name)) }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden"
                                                                   name="owner_name" id="owner_name"
                                                                   class="form-control owner_name"
                                                                   value="@if (!empty($agent_details->owner_name)) {{ ucwords(strtolower($agent_details->owner_name)) }} @else @endif">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.office_no') }}</span>
                                                            @if (!empty($agent_details->office_no))
                                                            <div class="travel_edit">
                                                                {{ $agent_details->office_no }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden"
                                                                   id="agent_office_no"
                                                                   name="agent_office_no"
                                                                   maxlength="10"
                                                                   class="form-control agent_office_no"
                                                                   value=" @if (!empty($agent_details->office_no)) {{ $agent_details->office_no }} @else @endif">
                                                        </div>

                                                    </div>
                                                    <div class="mob_err" style="color: red;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.total_business_year') }}</span>
                                                            @if (!empty($agent_details->total_business_year))
                                                            <div class="travel_edit">
                                                                {{ $agent_details->total_business_year }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden"
                                                                   id="total_business_year"
                                                                   name="total_business_year"
                                                                   class="form-control total_business_year"
                                                                   value="@if (!empty($agent_details->total_business_year)) {{ $agent_details->total_business_year }} @else @endif">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <hr/>
                                    </div>
                                </div>

                                <div class="d-sm-flex">
                                    <div class="row col-md-12">
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.pan_card') }}</span>
                                                            @if (!empty($agent_details->total_business_year))
                                                            <div class="travel_edit">
                                                                {{ $agent_details->pan_card }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden" id="pan_card"
                                                                   oninput="panCardNumber()"
                                                                   maxlength="10" name="pan_card"
                                                                   class="form-control pan_card"
                                                                   value="@if (!empty($agent_details->total_business_year)) {{ $agent_details->pan_card }} @else @endif">
                                                        </div>
                                                    </div>
                                                    <div class="valid_pan_card"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" style="margin-left: -12px; ">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.adhar_card') }}</span>@if (!empty($agent_details->adhar_card))
                                                            <div class="travel_edit">{{ $agent_details->adhar_card }}</div>
                                                            @endif
                                                            <input type="hidden" id="adhar_card"
                                                                   name="adhar_card" maxlength="12"
                                                                   onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')"
                                                                   class="form-control adhar_card"
                                                                   oninput="adharCardValidation()"
                                                                   value="@if (!empty($agent_details->adhar_card)) {{ $agent_details->adhar_card }} @else @endif">
                                                        </div>
                                                    </div>
                                                    <div class="valid_adhar_card"></div>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-12 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" style="margin-left: -12px; ">
                                                <hr/>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.bank_account_id') }}</span>
                                                            @if (!empty($banks->account_number))
                                                            <div class="travel_edit">
                                                                {{ $banks->account_number }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden"
                                                                   id="account_number"
                                                                   name="account_number"
                                                                   onkeyup="if (/\D/g.test(this.value)) this.value = this.value.replace(/\D/g, '')"
                                                                   oninput="accountNumber()"
                                                                   class="form-control account_number"
                                                                   value="@if (!empty($banks->account_number)) {{ $banks->account_number }} @else @endif">
                                                        </div>
                                                    </div>
                                                    <div id="validAccountNo"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" style="margin-left: -28px; ">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"><span>Bank Name</span>
                                                            @if (!empty($banks->bank_name))
                                                            <div class="travel_edit">
                                                                {{ ucwords(strtolower($banks->bank_name)) }}
                                                            </div>
                                                            @endif

                                                            <input type="hidden" id="bank_name"
                                                                   name="bank_name"
                                                                   onKeyPress="return ValidateAlpha(event);"
                                                                   class="form-control bank_name"
                                                                   value="@if (!empty($banks->bank_name)) {{ ucwords(strtolower($banks->bank_name)) }} @else @endif">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" >
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"><span>Branch Name</span>
                                                            @if (!empty($banks->branch_name))
                                                            <div class="travel_edit">
                                                                {{ ucwords(strtolower($banks->branch_name)) }}
                                                            </div>
                                                            @endif
                                                            <input type="hidden" id="branch_name"
                                                                   name="branch_name"
                                                                   class="form-control branch_name"
                                                                   value="@if (!empty($banks->branch_name)) {{ ucwords(strtolower($banks->branch_name)) }} @else @endif">

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" style="margin-left: -12px; ">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"><span>IFSC
                                                                Code</span>
                                                            @if (!empty($banks->ifsc_code))
                                                            <div class="travel_edit">
                                                                {{ $banks->ifsc_code }}</div>
                                                            @endif
                                                            <input type="hidden" id="ifsc_code"
                                                                   name="ifsc_code"
                                                                   oninput="ifsc_check()"
                                                                   class="form-control ifsc_code"
                                                                   value="@if (!empty($banks->ifsc_code)) {{ $banks->ifsc_code }} @else @endif">


                                                        </div>
                                                    </div>
                                                    <div id="ifsc_code_error"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10" style="margin-left: -22px; " >
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.agent_earnings') }}</span>

                                                            <div class="travel_edit">
                                                                {{ $earnings }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @php
                                $agentDatas = json_encode($agent_details);
                                @endphp
                                <input type="hidden" name="" id="agent_data"
                                       value="{{ $agentDatas }}">
                                <button type="button" class="edit_travel btn-primary btn-sm mt-2"
                                        id="edit_travel_details"
                                        onclick="editTravelDetails()">Submit</button>

                            </div> <!-- agent -->
                            @endif

                            @if ($user->user_type_id == '4' || $user->user_type_id == '5')
                            <div class="border-top"></div>

                            <div class="p-4">
                                <label class="main-content-label tx-13 mg-b-20">Driver</label>
                                <div class="d-sm-flex">
                                    <div class="row col-md-12">
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.driver_mobile_numebr') }}</span>
                                                            <div>{{ $user->driver_mobile_numebr }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.name') }}</span>
                                                            <div>
                                                                {{ $user->driver_first_name . ' ' . $user->driver_last_name }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.adhar_card_no') }}</span>
                                                            <div>{{ $user->adhar_card_no }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.driving_licence_no') }}</span>
                                                            <div>{{ $user->driving_licence_no }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="m-t-30 d-sm-flex">
                                    &nbsp;
                                </div>
                                <div class="d-sm-flex">
                                    <div class="row col-md-12">
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.driving_licence_expiry_date') }}</span>
                                                            <div>
                                                                {{ $user->driving_licence_expiry_date }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.street_address') }}</span>
                                                            <div>{{ $user->street_address }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3 pl-0">
                                            <div class="mg-sm-r-40 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body">
                                                            <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                            <div>{{ $driver_trip }}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- driver -->
                            @endif

                            <div class="border-top"></div>
                            <div class="p-4">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="main-content-label tx-13 mg-b-20">Agent Documents</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div style="float: right;">
                                            <a href="{{url('/super-admin/travel/')}}/verify_personal_document/{{$user->id}}/1" class="btn btn-success btn-sm agent_document_verify"   data-user-id="{{$user->id}}">Approve All</a>
                                            <a href="{{url('/super-admin/travel/')}}/verify_personal_document/{{$user->id}}/0" class="btn btn-danger btn-sm agent_document_verify"  data-user-id="{{$user->id}}">Reject All</a>
                                        </div>
                                    </div>
                                </div>
                                <div class="">

                                    <div class="row row-sm">

                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">
                                            <h6 class="text-center">Profile Image</h6>
                                            <div class="image-div">
                                                @if (!empty($user->profile_pic))
                                                <img alt="Profile pic"
                                                     class="img-thumbnail image-class"
                                                     src="{{ $user->profile_pic }}"
                                                     onclick="getImage('{{ $user->profile_pic }}', 'Profile Image')">
                                                @else
                                                <img alt="docuemtn image"
                                                     class="img-thumbnail image-class"
                                                     src="{{ env('APP_URL') }}/public/noimage.png">
                                                @endif
                                            </div>
                                            <span class="text-success"
                                                  id="profile_pic_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger" id="profile_pic_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->profile_pic_status == 2)
                                            @if (!empty($user->profile_pic))
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 profile_pic_status_btn"
                                                    onclick="documentVerify('user','{{ $user->id }}', 'profile_pic_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 profile_pic_status_btn"
                                                    onclick="documentVerify('user','{{ $user->id }}', 'profile_pic_status', '0')">Reject</button>
                                            @endif
                                            @else
                                            @if ($user->profile_pic_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                            &nbsp;
                                            @if (!empty($user->profile_pic))
                                            <button type="button"
                                                    class="btn ripple btn-primary btn-sm mt-2"
                                                    onclick="imageUpload('user','{{ $user->id }}', 'profile_pic_status', 'profile_pic', 'travel')">Change
                                                Image</button>
                                            @endif
                                        </div>

                                        @if (!empty($agent_details->logo))
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">                                                                                        <h6 class="text-center">Agent Logo</h6>
                                            <div class="image-div">
                                                <img alt="docuemtn image"
                                                     class="img-thumbnail image-class"
                                                     src="{{ $agent_details->logo }}"
                                                     onclick="getImage('{{ $agent_details->logo }}', 'Agent Logo')">
                                            </div>
                                            <span class="text-success" id="logo_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger" id="logo_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($agent_details->logo_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 logo_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'logo_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 logo_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'logo_status', '0')">Reject</button>
                                            @else
                                            @if ($agent_details->logo_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif
                                            &nbsp; <button type="button"
                                                           class="btn ripple btn-primary btn-sm mt-2"
                                                           onclick="imageUpload('agent','{{ $agent_details->id }}', 'logo_status', 'logo', 'travel')">Change
                                                Image</button>
                                        </div>
                                        @endif


                                        @if (!empty($agent_details))
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">                                                                                        <h6 class="text-center">Aadhaar Card Front</h6>
                                            <div class="image-div">
                                                @if (!empty($agent_details->adhar_card_url))
                                                <?php
                                                $Infos = pathinfo($agent_details->adhar_card_url);
                                                $extension = $Infos['extension'];
                                                if (strtolower($extension) != 'pdf') {
                                                    ?>
                                                    <img alt="docuemtn image"
                                                         class="img-thumbnail image-class"
                                                         src="{{ $agent_details->adhar_card_url }}"
                                                         onclick="getImage('{{ $agent_details->adhar_card_url }}', 'Adhar Card Front')">
                                                     <?php } else { ?>
                                                     <!-- <a class="img-thumbnail" href="{{ $agent_details->adhar_card_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->
                                                    <img style="cursor:pointer;"
                                                         onclick="getPdf('{{ $agent_details->adhar_card_url }}', 'Adhar Card Fron')"
                                                         src="{{ env('APP_URL') }}/public/pdf.png"
                                                         width="75%">
                                                     <?php } ?>
                                                @else
                                                <img alt="docuemtn image"
                                                     class="img-thumbnail image-class"
                                                     src="{{ env('APP_URL') }}/public/noimage.png">
                                                @endif
                                            </div>
                                            <span class="text-success"
                                                  id="adhar_card_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="adhar_card_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($agent_details->adhar_card_url_status == 2)
                                            @if (!empty($agent_details->adhar_card_url))
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 adhar_card_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'adhar_card_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 adhar_card_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'adhar_card_url_status', '0')">Reject</button>
                                            @endif
                                            @else
                                            @if ($agent_details->adhar_card_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                            &nbsp; <button type="button"
                                                           class="btn ripple btn-primary btn-sm mt-2"
                                                           onclick="imageUpload('agent','{{ $agent_details->id }}', 'adhar_card_url_status', 'adhar_card_url', 'travel')">Change
                                                Image</button>
                                        </div>



                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">                                                                                        <h6 class="text-center">Aadhaar Card Back</h6>
                                            <div class="image-div">
                                                @if (!empty($agent_details->adhar_card_back_url))
                                                <?php
                                                $Infos = pathinfo($agent_details->adhar_card_back_url);
                                                $extension = $Infos['extension'];
                                                if (strtolower($extension) != 'pdf') {
                                                    ?>
                                                    <img alt="docuemtn image"
                                                         class="img-thumbnail image-class"
                                                         src="{{ $agent_details->adhar_card_back_url }}"
                                                         onclick="getImage('{{ $agent_details->adhar_card_back_url }}', 'Adhar Card Back')">
                                                     <?php } else { ?>
                                                     <!-- <a class="img-thumbnail" href="{{ $agent_details->adhar_card_back_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->

                                                    <img style="cursor:pointer;"
                                                         onclick="getPdf('{{ $agent_details->adhar_card_back_url }}', 'Adhar Card Back')"
                                                         src="{{ env('APP_URL') }}/public/pdf.png"
                                                         width="75%">

                                                <?php } ?>
                                                @else
                                                <img alt="docuemtn image"
                                                     class="img-thumbnail image-class"
                                                     src="{{ env('APP_URL') }}/public/noimage.png">
                                                @endif
                                            </div>
                                            <span class="text-success"
                                                  id="adhar_card_back_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="adhar_card_back_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($agent_details->adhar_card_back_url_status == 2)
                                            @if (!empty($agent_details->adhar_card_back_url))
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 adhar_card_back_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'adhar_card_back_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 adhar_card_back_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'adhar_card_back_url_status', '0')">Reject</button>
                                            @endif
                                            @else
                                            @if ($agent_details->adhar_card_back_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif
                                            &nbsp; <button type="button"
                                                           class="btn ripple btn-primary btn-sm mt-2"
                                                           onclick="imageUpload('agent','{{ $agent_details->id }}', 'adhar_card_back_url_status', 'adhar_card_back_url', 'travel')">Change
                                                Image</button>
                                        </div>
                                        @if (!empty($agent_details->pan_card_url))
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">                                                                                        <h6 class="text-center">Pan Card</h6>
                                            <div class="image-div">

                                                <?php
                                                $Infos = pathinfo($agent_details->pan_card_url);
                                                $extension = $Infos['extension'];
                                                if (strtolower($extension) != 'pdf') {
                                                    ?>
                                                    <img alt="docuemtn image"
                                                         class="img-thumbnail image-class"
                                                         src="{{ $agent_details->pan_card_url }}"
                                                         onclick="getImage('{{ $agent_details->pan_card_url }}', 'Pan Card')">
                                                     <?php } else { ?>
                                                     <!-- <a class="img-thumbnail" href="{{ $agent_details->pan_card_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->

                                                    <img style="cursor:pointer;"
                                                         onclick="getPdf('{{ $agent_details->pan_card_url }}', 'Pan Card')"
                                                         src="{{ env('APP_URL') }}/public/pdf.png"
                                                         width="75%">

                                                <?php } ?>

                                            </div>
                                            <span class="text-success"
                                                  id="pan_card_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="pan_card_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($agent_details->pan_card_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 pan_card_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'pan_card_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 pan_card_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'pan_card_url_status', '0')">Reject</button>
                                            @else
                                            @if ($agent_details->pan_card_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                            &nbsp; <button type="button"
                                                           class="btn ripple btn-primary btn-sm mt-2"
                                                           onclick="imageUpload('agent','{{ $agent_details->id }}', 'pan_card_url_status', 'pan_card_url', 'travel')">Change
                                                Image</button>
                                        </div>
                                        @endif



                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">
                                            <h6 class="text-center">Registration Document</h6>
                                            <div class="image-div">
                                                @if (!empty($agent_details->registration_document_url))
                                                <?php
                                                $Infos = pathinfo($agent_details->registration_document_url);
                                                $extension = $Infos['extension'];
                                                if (strtolower($extension) != 'pdf') {
                                                    ?>
                                                    <img alt="docuemtn image"
                                                         class="img-thumbnail image-class"
                                                         src="{{ $agent_details->registration_document_url }}"
                                                         onclick="getImage('{{ $agent_details->registration_document_url }}', 'Registration Document')">
                                                     <?php } else { ?>
                                                     <!-- <a class="img-thumbnail" href="{{ $agent_details->registration_document_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->

                                                    <img style="cursor:pointer;"
                                                         onclick="getPdf('{{ $agent_details->registration_document_url }}', 'Registration Document')"
                                                         src="{{ env('APP_URL') }}/public/pdf.png"
                                                         width="75%">

                                                <?php } ?>
                                                @else
                                                <img alt="docuemtn image"
                                                     class="img-thumbnail image-class"
                                                     src="{{ env('APP_URL') }}/public/noimage.png">
                                                @endif
                                            </div>

                                            <span class="text-success"
                                                  id="registration_document_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="registration_document_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($agent_details->registration_document_url_status == 2)
                                            @if (!empty($agent_details->registration_document_url))
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 registration_document_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'registration_document_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 registration_document_url_status_btn"
                                                    onclick="documentVerify('agent','{{ $agent_details->id }}', 'registration_document_url_status', '0')">Reject</button>
                                            @endif
                                            @else
                                            @if ($agent_details->registration_document_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                            &nbsp; <button type="button"
                                                           class="btn ripple btn-primary btn-sm mt-2"
                                                           onclick="imageUpload('agent','{{ $agent_details->id }}', 'registration_document_url_status', 'registration_document_url', 'travel')">Change
                                                Image</button>
                                        </div>

                                        @endif

                                        @if (($user->user_type_id == '2' || $user->user_type_id == '3') && !empty($banks))
                                        <div class="col-xl-2 col-lg-2 col-md-2 col-sm-6 text-center">
                                            <h6 class="text-center">Bank Cheque Document</h6>
                                            <div class="image-div">
                                                @if (!empty($banks->document_url))
                                                <?php
                                                $Infos = pathinfo($banks->document_url);
                                                $extension = $Infos['extension'];
                                                if (strtolower($extension) != 'pdf') {
                                                    ?>
                                                    <img alt="docuemtn image"
                                                         class="img-thumbnail image-class"
                                                         src="{{ $banks->document_url }}"
                                                         onclick="getImage('{{ $banks->document_url }}', 'Bank Document')">
                                                     <?php } else { ?>
                                                     <!-- <a class="img-thumbnail" href="{{ $banks->document_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->
                                                    <img style="cursor:pointer;"
                                                         onclick="getPdf('{{ $agent_details->document_url }}', 'Bank Document')"
                                                         src="{{ env('APP_URL') }}/public/pdf.png"
                                                         width="75%">
                                                     <?php } ?>
                                                @else
                                                <button type="button"
                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                        onclick="imageUpload('bank_account','{{ $banks->id }}', 'bank_document_url_status', 'document_url', 'travel')" style="margin-top:50% !important">
                                                    @if (!empty($banks->document_url)) Change Image @else Upload Image @endif</button>

<!--                                                                                            <img alt="docuemtn image"
              class="img-thumbnail image-class"
              src="{{ env('APP_URL') }}/public/noimage.png">-->
                                                @endif

                                            </div>
                                            <span class="text-success"
                                                  id="bank_document_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="bank_document_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($banks->bank_document_url_status == 2)
                                            @if (!empty($banks->document_url))
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 bank_document_url_status_btn"
                                                    onclick="documentVerify('bank_account','{{ $banks->id }}', 'bank_document_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 bank_document_url_status_btn"
                                                    onclick="documentVerify('bank_account','{{ $banks->id }}', 'bank_document_url_status', '0')">Reject</button>
                                            @endif
                                            @else
                                            @if ($banks->bank_document_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                            <!--                                                                                        &nbsp; <button type="button"
                                                                                                                                                   class="btn ripple btn-primary btn-sm mt-2"
                                                                                                                                                   onclick="imageUpload('bank_account','{{ $banks->id }}', 'bank_document_url_status', 'document_url', 'travel')">
                                                                                                                                        @if (!empty($banks->document_url)) Change Image @else Upload Image @endif</button>-->
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div> <!-- agent doc -->

                            <?php if (!empty($plan_details)) { ?>
                                <div class="border-top"></div>

                                <div class="p-4">
                                    <label class="main-content-label tx-13 mg-b-20">subscription
                                        plan</label>
                                    <div class="d-sm-flex">
                                        <div class="row col-md-12">
                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.plan_name') }}</span>
                                                                <div>{{ $plan_details->name }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.plan_validity') }}</span>
                                                                <div>{{ $plan_details->plan_validity }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.price') }}</span>
                                                                <div>{{ $plan_details->price }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.start_date') }}</span>
                                                                <div>
                                                                    {{ date('d-m-Y H:i:s', strtotime($plan->start_datetime)) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.end_date') }}</span>
                                                                <div>
                                                                    {{ date('d-m-Y H:i:s', strtotime($plan->end_datetime)) }}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-3 pl-0">
                                                <div class="mg-sm-r-40 mg-b-10">
                                                    <div class="main-profile-contact-list">
                                                        <div class="media">
                                                            <div class="media-body">
                                                                <span>{{ @trans('user.subscription_status') }}</span>
                                                                <div>{{ $subscription_status }}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div> <!-- subscription plan -->
                            <?php } ?>

                            <!-- <div class="border-top"></div> -->
                            <div class="p-4" style="display:none;">
                                <label class="main-content-label tx-13 mg-b-20">Driver
                                    Documents</label>
                                <div class="d-sm-flex">

                                    <div class="row row-sm">
                                        @if (!empty($user->d_pan_card_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">Pan Card</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->d_pan_card_url }}"
                                                 onclick="getImage('{{ $user->d_pan_card_url }}', 'Pan Card')">

                                            <span class="text-success"
                                                  id="d_pan_card_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="d_pan_card_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->d_pan_card_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 d_pan_card_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'd_pan_card_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 d_pan_card_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'd_pan_card_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->d_pan_card_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                        </div>
                                        @endif

                                        @if (!empty($user->d_adhar_card_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">Aadhaar Card</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->d_adhar_card_url }}"
                                                 onclick="getImage('{{ $user->d_adhar_card_url }}', 'Adhar Card')">

                                            <span class="text-success"
                                                  id="d_adhar_card_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="d_adhar_card_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->d_adhar_card_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 d_adhar_card_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'd_adhar_card_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'd_adhar_card_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->d_adhar_card_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif
                                        </div>
                                        @endif

                                        @if (!empty($user->dl_front_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">DL Front</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->dl_front_url }}"
                                                 onclick="getImage('{{ $user->dl_front_url }}', 'DL Front')">

                                            <span class="text-success"
                                                  id="dl_front_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="dl_front_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->dl_front_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 dl_front_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'dl_front_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 dl_front_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'dl_front_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->dl_front_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                        </div>
                                        @endif

                                        @if (!empty($user->dl_back_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">DL Back</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->dl_back_url }}"
                                                 onclick="getImage('{{ $user->dl_back_url }}', 'DL Back')">

                                            <span class="text-success"
                                                  id="dl_back_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="dl_back_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->dl_back_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 dl_back_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'dl_back_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 dl_back_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'dl_back_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->dl_back_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif
                                        </div>
                                        @endif

                                        <div class="col-12 col-md-12">
                                            &nbsp;
                                        </div>

                                        @if (!empty($user->police_verification_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">Police Verification</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->police_verification_url }}"
                                                 onclick="getImage('{{ $user->police_verification_url }}', 'Police Verification')">

                                            <span class="text-success"
                                                  id="police_verification_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="police_verification_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->police_verification_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 police_verification_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'police_verification_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 police_verification_url_status_btn"
                                                    onclick="documentVerify('driver','{{ $user->driver_id }}', 'police_verification_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->police_verification_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif

                                        </div>
                                        @endif

                                        @if (($user->user_type_id == '4' || $user->user_type_id == '5') && !empty($user->bank_document_url))
                                        <div class="col-6 col-md-3 text-center">
                                            <h6 class="text-center">Bank Cheque Document</h6>
                                            <img alt="docuemtn image" class="img-thumbnail"
                                                 src="{{ $user->bank_document_url }}"
                                                 onclick="getImage('{{ $user->bank_document_url }}', 'Bank Document')">

                                            <span class="text-success"
                                                  id="bank_document_url_status_approved"
                                                  style="display:none;">Approved</span>
                                            <span class="text-danger"
                                                  id="bank_document_url_status_rejected"
                                                  style="display:none;">Rejected</span>

                                            @if ($user->bank_document_url_status == 2)
                                            <button type="button"
                                                    class="btn ripple btn-success btn-sm mt-2 bank_document_url_status_btn"
                                                    onclick="documentVerify('bank_account','{{ $user->bank_account_id }}', 'bank_document_url_status', '1')">Approve</button>
                                            <button type="button"
                                                    class="btn ripple btn-danger btn-sm mt-2 bank_document_url_status_btn"
                                                    onclick="documentVerify('bank_account','{{ $user->bank_account_id }}', 'bank_document_url_status', '0')">Reject</button>
                                            @else
                                            @if ($user->bank_document_url_status == 1)
                                            <span class="text-success">Approved</span>
                                            @else
                                            <span class="text-danger">Rejected</span>
                                            @endif
                                            @endif
                                        </div>
                                        @endif

                                    </div>
                                </div>
                            </div> <!-- driver doc -->

                        </div>
                        <div class="hide history_show rounded-10 border p-0">
                            <div class="p-4 scroll">
                                <form
                                    action="{{ route('admin.register.history_comment', ['panel' => Session::get('panel'), 'id' => $user->id]) }}"
                                    method="POST">
                                    @csrf
                                    <input type="hidden" name="user_id" id=""
                                           value="{{ $user->id }}">
                                    <textarea class="form-control" name="comment_text" required id="" cols="30" rows="5"
                                              placeholder="Enter Comment"></textarea><br>
                                    <button class="btn btn-primary" type="submit">Add
                                        Comment</button><br><br>
                                </form>
                                @foreach ($user_history as $key => $history)
                                <div class="msg_style">
                                    <div style="margin-bottom: 9px;">{{ $history->created_at }}
                                    </div>
                                    <div style="font-weight: 500">
                                        {{ $history->message }}
                                    </div>
                                </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- End Row -->
<!-- Vehicles Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6">
                        <h6 class="main-content-label mb-1">Vehicles</h6>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6">

                        <div class="d-flex-end float-right">

                            <!--<a href="<?= url(Session::get('panel') . '/vehicles/create') ?>/<?= $user->id ?>">
                                <button type="submit" class="btn btn-default text-right btn-sm" style="float: right;background-color: #15a39a;color:white;">Add</button>
                            </a>-->
                            <a href="javascript:;" class="btn btn-default text-right btn-sm VehicleManage" data-user-id="{{$user->id}}" style="float: right;background-color: #15a39a;color:white;">
                                Add
                            </a>

                        </div>
                    </div>
                </div>
                <div class="row" id="vehiclesDetails" style="margin-top: 10px;">
                    @if(!empty($vehicles))
                    <?php $i = 1; ?> 
                    <?php $according = 500; ?> 
                    @foreach($vehicles as $row)  

                    <div class="card-body p-0 border p-0 rounded-10" style="margin-top: 5px;">
                        <div class="p-4" >
                            <div class="row">

                                <div class="col-md-12 accordion-header" id="panelsStayOpen-heading{{$according}}">
                                    <button class="accordion-button btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#panelsStayOpen-collapse{{$according}}" 
                                            aria-expanded="true" aria-controls="panelsStayOpen-collapse{{$according}}" >
                                        {{$row->vehicle_number}}
                                    </button> 
                                    <div style="float:right">
                                        <a href="{{url('/super-admin/travel/')}}/verify_vehicle_document/{{$row->id}}/1" class="btn btn-success btn-sm agent_document_verify"  >Approve All</a>
                                        <a href="{{url('/super-admin/travel/')}}/verify_vehicle_document/{{$row->id}}/0" class="btn btn-danger btn-sm agent_document_verify"  >Reject All</a>
                                    </div>

                                </div>
                            </div>
                            <div id="panelsStayOpen-collapse{{$according}}"  class="row accordion-collapse collapse " aria-labelledby="panelsStayOpen-heading{{$according}}" style="margin-top: 10px;">

                                <!--                            <div class="row">-->
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Vehicle Name</span>
                                                <div class="user_edit show_image" title="Images">
                                                    {{$row->vehicle_number}}
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Brand Name</span>
                                                <div class="">
                                                    {{$row->brand_name}}</div> 
                                            </div> 
                                        </div> 
                                    </div> 
                                </div> 
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Model Name</span>
                                                <div class="">{{$row->model_name}}</div> 
                                            </div>
                                        </div>
                                    </div>
                                    <!--<div id="result"></div>-->
                                </div>

                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Vehicle Type</span>
                                                {{$row->vehicle_type_name}}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Vehicle Fuel Type</span>
                                                <div>{{$row->vehicle_fuel_type_name}}</div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Vehicle Registration</span>
                                                <div>{{$row->registration_year}}</div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr/>
                                </div>

                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Insurance Expiry Date</span>
                                                <div class="user_edit">
                                                    {{$row->insurance_exp_date}}
                                                </div>
                                                <input type="hidden" class="form-control rider_name" name="" id="user_edit_detail" value="Mitesh Prajapati">
                                            </div>


                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Permit Exp Date</span>
                                                <div class="user_edit">
                                                    {{$row->permit_exp_date}}
                                                </div> 
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Fitness Exp Date
                                                </span>
                                                <div class="user_edit">
                                                    {{$row->permit_exp_date}}
                                                </div>
                                                <input type="hidden" class="form-control rider_name" name="" id="user_edit_detail" value="Mitesh Prajapati">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>PUC Exp Date</span>
                                                <div class="user_edit">
                                                    {{$row->puc_exp_date}}
                                                </div>
                                                <input type="hidden" class="form-control rider_name" name="" id="user_edit_detail" value="Mitesh Prajapati">
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Agreement</span> 
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>RC</span> 
                                            </div> 
                                        </div>
                                    </div>
                                </div>


                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <div>
                                                    @if(!empty($row->insurance_doc_url))
                                                    <?php
                                                    $Infos = pathinfo($row->insurance_doc_url);
                                                    $extension = $Infos['extension'];
                                                    if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                                                                                                                                                                                                                <!--<img alt="docuemtn image" class="img-thumbnail image-class" src="{{$row->insurance_doc_url}}" onclick="getImage('{{$row->insurance_doc_url}}', 'Insurance Document')">-->

                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->insurance_doc_url}}" onclick="getImage('{{$row->insurance_doc_url}}', 'Insurance Document')"><i class="fa fa-eye"></i></button>                                                                                     

                                                    <?php } else { ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="getPdf('{{$row->insurance_doc_url}}', 'Insurance Document')" src="{{ asset('pdf.png') }}"><i class="fa fa-eye"></i></button>                                                                                     
                                                    <!--<img style="cursor:pointer;"  width="75%">-->
                                                    <?php } ?>
                                                    @else
                                                    <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$row->id}}', 'insurance_doc_url_status', 'insurance_doc_url', 'vehicles')">Upload Image</button>                                                                                    
                                                    @endif
                                                </div>

                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 ">
                                    <div >
                                        <!--  -->
                                        @if(!empty($row->permit_doc_url))
                                        <?php
                                        $Infos = pathinfo($row->permit_doc_url);
                                        $extension = $Infos['extension'];
                                        if (strtolower($extension) != 'pdf') {
                                            ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->permit_doc_url}}" onclick="getImage('{{$row->permit_doc_url}}', 'Permit Document')"><i class="fa fa-eye"></i></button>                                                                                      
                                        <?php } else { ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->permit_doc_url}}', 'Permit Document')" src="{{ asset('pdf.png') }}" width="75%"><i class="fa fa-eye"></i></button>
                                        <?php } ?>
                                        @else
                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$row->id}}', 'permit_doc_url_status', 'permit_doc_url', 'vehicles')">Change Image</button>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-2 ">
                                    <div >
                                        <!--  -->
                                        @if(!empty($row->fitness_doc_url))
                                        <?php
                                        $Infos = pathinfo($row->fitness_doc_url);
                                        $extension = $Infos['extension'];
                                        if (strtolower($extension) != 'pdf') {
                                            ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->fitness_doc_url}}" onclick="getImage('{{$row->fitness_doc_url}}', 'Fitness Document')"><i class="fa fa-eye"></i></button>                                                                                      
                                        <?php } else { ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->fitness_doc_url}}', 'Fitness Document')" src="{{ asset('pdf.png') }}"><i class="fa fa-eye"></i></button>
                                        <?php } ?>
                                        @else
                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$row->id}}', 'fitness_doc_url_status', 'fitness_doc_url', 'vehicles')">Change Image</button>
                                        @endif
                                    </div>

                                </div>

                                <div class="col-md-2 ">
                                    <div >
                                        <!--  -->
                                        @if(!empty($row->puc_doc_url))
                                        <?php
                                        $Infos = pathinfo($row->puc_doc_url);
                                        $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';
                                        if (strtolower($extension) != 'pdf') {
                                            ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->puc_doc_url}}" onclick="getImage('{{$row->puc_doc_url}}', 'PUC Document')"><i class="fa fa-eye"></i></button>                                                                                      
                                        <?php } else { ?>
                                            <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->puc_doc_url}}', 'PUC Document')" src="{{ asset('pdf.png') }}"><i class="fa fa-eye"></i></button>
                                        <?php } ?>
                                        @else
                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$row->id}}', 'puc_doc_url_status', 'puc_doc_url', 'vehicles')">Upload</button>
                                        @endif
                                    </div>

                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <div class="user_edit">
                                                    @if(!empty($row->agreement_doc_url))
                                                    <?php
                                                    $Infos = pathinfo($row->agreement_doc_url);
                                                    $extension = $Infos['extension'];
                                                    if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->agreement_doc_url}}" onclick="getImage('{{$row->agreement_doc_url}}', 'Agreement Document')"><i class="fa fa-eye"></i></button>                                                                                      
                                                    <?php } else { ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->agreement_doc_url}}', 'Agreement Document')" src="{{ asset('pdf.png') }}" width="75%"><i class="fa fa-eye"></i></button>
                                                    <?php } ?>
                                                    @endif
                                                </div>
                                                <!--<input type="hidden" class="form-control rider_name" name="" id="user_edit_detail" value="Mitesh Prajapati">-->
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">

                                            <div class="media-body">
                                                <div class="user_edit">
                                                    @if(!empty($row->agreement_doc_url))
                                                    <?php
                                                    $Infos = pathinfo($row->agreement_doc_url);
                                                    $extension = $Infos['extension'];
                                                    if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$row->agreement_doc_url}}" onclick="getImage('{{$row->agreement_doc_url}}', 'Agreement Document')">Front</button>                                                                                      
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="getPdf('{{$row->rc_back_url}}', 'RC Back')" src="{{ asset('pdf.png') }}">Back</button>                                                                                      
                                                    <?php } else { ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->agreement_doc_url}}', 'Agreement Document')" src="{{ asset('pdf.png') }}" width="75%">Front</button>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{$row->agreement_doc_url}}', 'Agreement Document')" src="{{ asset('pdf.png') }}" width="75%">Front</button>
                                                    <?php } ?>
                                                    @endif
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <hr/>
                                    <div class="main-profile-contact-list">
                                        <span>Vehicle Images</span>

                                        <div class="media">
                                            <div class="media-body">

                                                <div class="user_edit">

                                                    <?php
                                                    $id = $row->id;
                                                    $vehicleImages = \DB::table('vehicle_photo_mapping')
                                                            ->join('vehicle_photos_view_master', 'vehicle_photos_view_master.id', '=', 'vehicle_photo_mapping.vehicle_photos_view_master_id')
                                                            ->select('vehicle_photo_mapping.image_url', 'view_name', 'vehicle_photo_mapping.image_url_status', 'vehicle_photo_mapping.id')
                                                            ->where('vehicle_photo_mapping.vehicle_id', '=', $id)
                                                            ->get();

                                                    foreach ($vehicleImages as $imagesList) {
                                                        $vehicle_image_url = $imagesList->image_url;
                                                        $vehicle_view_name = $imagesList->view_name;
                                                        ?> 
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{$vehicle_image_url}}" onclick="getImage('{{$vehicle_image_url}}', 'Agreement Document')">{{$vehicle_view_name}}</button>                                                                                         
                                                    <?php }
                                                    ?>
                                                    <a href="{{url('/')}}/super-admin/vehicles/edit/{{$row->id}}" target="_blank" class="btn btn-default text-right btn-sm" style="float: right;background-color: #15a39a;color:white;">Edit</a>
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php $according++; ?>
                    @endforeach
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->
<!-- Vehicles Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6">
                        <h6 class="main-content-label mb-1">Drivers</h6>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-6 col-sm-6">

                        <div class="d-flex-end float-right">

                            <!--<a href="<?= url(Session::get('panel') . '/driver/create') ?>" target="_blank">
                                <button type="submit" class="btn btn-default text-right btn-sm" style="float: right;background-color: #15a39a;color:white;">Add</button>
                            </a>-->
                            <a href="javascript:;"  class="btn btn-default text-right btn-sm DriverManage" data-user-id="{{$user->id}}" style="float: right;background-color: #15a39a;color:white;">
                                Add 
                            </a>
                        </div>
                    </div>
                </div>
                <div class="row" id="vehiclesDetails" style="margin-top: 10px;">
                    @if(!empty($drivers))
                    <?php $i = 1; ?> 
                    @foreach ($drivers as $d)

                    <div class="card-body p-0 border p-0 rounded-10" style="margin-top: 5px;">
                        <div class="p-4" > 

                            <div class="row">
                                <div class="col-md-12 accordion-header" id="panelsStayOpen-heading{{$d['id']}}">
                                    <button class="accordion-button btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" 
                                            data-bs-target="#panelsStayOpen-collapse{{$d['id']}}" 
                                            aria-expanded="true" aria-controls="panelsStayOpen-collapse{{$d['id']}}">
                                        {{ $d['first_name'] . ' ' . $d['last_name'] }}
                                    </button>
                                    <div style="float:right">
                                        <a href="javascript:;" class="btn btn-default text-right btn-sm EditDriverManage"  data-user-id="{{$user->id}}"  data-id="{{$d['id']}}" style="background-color: #15a39a;color:white;">Edit</a>
                                        <a href="{{url('/super-admin/travel/')}}/verify_driver_document/{{$d['id']}}/1" class="btn btn-success btn-sm agent_document_verify"  >Approve All</a>
                                        <a href="{{url('/super-admin/travel/')}}/verify_driver_document/{{$d['id']}}/0" class="btn btn-danger btn-sm agent_document_verify"  >Reject All</a>
                                    </div>

                                </div>
                            </div>
                            <div id="panelsStayOpen-collapse{{$d['id']}}"  class="row accordion-collapse collapse " aria-labelledby="panelsStayOpen-heading{{$d['id']}}" style="margin-top: 10px;">
                                <div class="col-md-2 accordion-body">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>{{ @trans('user.name') }}</span>
                                                <div class="user_edit show_image" title="Images">
                                                    {{ $d['first_name'] . ' ' . $d['last_name'] }}
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2 accordion-body">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Father Name</span>
                                                <div class="user_edit show_image" title="Images">
                                                    {{ $d['father_name']}}
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>{{ @trans('user.mobile_number') }}</span>
                                                <div class="">{{ $d['mobile_numebr'] }}</div> 
                                            </div> 
                                        </div> 
                                    </div> 
                                </div> 
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Date of Birth</span>
                                                <div class="">{{ $d['dob'] }}</div> 
                                            </div> 
                                        </div> 
                                    </div> 
                                </div> 


                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>{{ @trans('user.driving_licence_no') }}</span>
                                                {{ $d['driving_licence_no'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Gender</span>
                                                {{ $d['gender'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 10px;">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>{{ @trans('user.expiry_date') }}</span>
                                                {{ $d['driving_licence_expiry_date'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 10px;">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Issue Date</span>
                                                {{ $d['issue_date'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" style="margin-top: 10px;">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Total Years of Experience</span>
                                                {{ $d['year_of_experience'] }} Year
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>{{ @trans('user.street_address') }}</span>
                                                {{ $d['street_address'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" style="margin-top: 10px;">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Email</span>
                                                {{ $d['email'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <hr/>
                                </div>


                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>Pincode</span>
                                                {{ $d['pincode'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>City</span>
                                                {{ $d['city'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media">
                                            <div class="media-body">
                                                <span>State</span>
                                                {{ $d['state'] }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="main-profile-contact-list">
                                        <div class="media"> 
                                            <div class="media-body">
                                                <span>Documents</span>
                                                <div class="user_edit">
                                                    @if(!empty($d['dl_front_url']))
                                                    <?php
                                                    $Infos = pathinfo($d['dl_front_url']);

                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';
                                                    if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{ $d['dl_front_url'] }}" onclick="getImage('{{ $d['dl_front_url'] }}', 'Front URL')">Front</button>                                                                                      
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{ $d['dl_back_url'] }}" onclick="getImage('{{ $d['dl_back_url'] }}', 'Back URL')">Back</button>                                                                                      
                                                    <?php } else { ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{ $d['dl_front_url'] }}', 'Front')" src="{{ asset('pdf.png') }}" width="75%">Front</button>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{ $d['dl_back_url'] }}', 'Back')" src="{{ asset('pdf.png') }}" width="75%">Back</button>
                                                    <?php } ?>
                                                    @endif
                                                </div>
                                            </div> 
                                        </div>
                                    </div>
                                </div>

                                <?php
                                $user_id = $d['user_id'];
                                $user_work_profiles_for_drivers = App\Models\UserWorkProfile::where('user_id', $user_id)->where('status', '1')->whereIn('user_type_id', array(4))->orderBy('id', 'DESC')->first();

                                $driver_details = array();
                                if (!empty($user_work_profiles_for_drivers)) {
                                    $profile_id = $user_work_profiles_for_drivers['profile_id'];
                                    $driver_details = \App\Models\Drivers::find($profile_id);
                                }

                                if (!empty($driver_details)) {
                                    $driver = App\Models\Drivers::where('id', $driver_details->id)->first();
                                } else {
                                    $driver = [];
                                }
                                ?>
                                <div class="col-md-2">
                                    <div class="mg-sm-r-40 mg-b-10">
                                        <div class="main-profile-contact-list">
                                            <span>Documents</span>

                                            <div class="media">
                                                <div class="media-body">
                                                    @if(!empty($driver))
                                                    <!--<span>Authorised Driver</span>-->
                                                    <a {{-- href="url()" --}} {{-- href="{{ route('admin.driver.authorized', ['panel' => Session::get('panel'),'id'=> 1,'data'=> $driver_details->id]) }}" --}}
                                                       {{-- @click="AuthroziedConfirm(item.id)" --}} title="Authorized">
                                                        <form
                                                            action="{{ route('admin.driver.authorized.status', ['panel' => Session::get('panel')]) }}"
                                                            method="POST">
                                                            @csrf
                                                            <input type="hidden" name="user_id" id="user_id" value="{{ $d['user_id']  }}">

                                                            @if ($driver->authorised_driver == null || $driver->authorised_driver == 0)
                                                            <label class="switch"> 

                                                                <button type="submit" class="btn btn-danger btn-sm">
                                                                    {{-- <input type="checkbox" checked > --}}
                                                                    Not Authorized
                                                                </button>
                                                                <span
                                                                    class="slider round"></span>
                                                            </label>
                                                            @else
                                                            <label class="switch">
                                                                <button type="submit" class="btn btn-success btn-sm">
                                                                    {{-- <input type="checkbox"> --}}
                                                                    Authorized
                                                                </button>
                                                                <span
                                                                    class="slider round"></span>
                                                            </label>
                                                            @endif
                                                        </form>
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    @if($d['police_verification_url'])

                                    <div class="main-profile-contact-list">
                                        <div class="media"> 
                                            <div class="media-body">
                                                <span>Police Verification Document</span>
                                                <div class="user_edit">

                                                    <?php
                                                    $Infos = pathinfo($d['police_verification_url']);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';
                                                    if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2" src="{{ $d['police_verification_url'] }}" onclick="getImage('{{ $d['police_verification_url'] }}', 'Front URL')"><i class="fa fa-eye"></i></button>                                                                                      
                                                    <?php } else { ?>
                                                        <button type="button" class="btn ripple btn-primary btn-sm mt-2"  onclick="getPdf('{{ $d['police_verification_url'] }}', 'Front')" src="{{ asset('pdf.png') }}" width="75%"><i class="fa fa-eye"></i></button>
                                                    <?php } ?>

                                                </div>
                                            </div> 
                                        </div>
                                    </div>

                                    @endif
                                </div>

                                <div class="col-md-12">  
                                    <a href="<?= url(Session::get('panel') . '/driver/show') ?>/<?= $d['user_id'] ?>" target="_blank" class="btn btn-default text-right btn-sm" style="float: right;background-color: #15a39a;color:white;margin-top:20px;">Edit</a>

                                </div>
                            </div>


                        </div>
                    </div>
                    @endforeach
                    @endif


                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<!-- Driver Details -->
<!--
  @if ($user->user_type_id == '3')
  <div class="row row-sm">
      <div class="col-lg-12">
          <div class="card custom-card">

              <div class="card-body">
                  <div>
                      <h6 class="main-content-label mb-1">Drivers</h6>
                  </div>
                  <div class="d-flex-end float-right">

                      <a href="<?= url(Session::get('panel') . '/driver/create') ?>">
                          <button type="submit" class="btn btn-primary">+ Add</button>
                      </a>
                  </div>
                  <div class="row">
                      <div class="table-responsive table-data" v-if="items.length">
                          <div class="col-sm-12">
                              
                               


                              <div class="table-checkable">
                                  <table id="driverdetails"
                                         class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                         width="100%">
                                      <thead>
                                          <tr>
                                              <th>#</th>
                                              <th><b>{{ @trans('user.name') }}</b></th>
                                              <th><b>{{ @trans('user.mobile_number') }}</b></th>
                                              <th><b>{{ @trans('user.adhar_card_no') }}</b></th>
                                              <th><b>{{ @trans('user.driving_licence_no') }}</b>
                                              </th>
                                              <th><b>{{ @trans('user.expiry_date') }}</b></th>
                                              <th><b>{{ @trans('user.street_address') }}</b></th>
                                              <th><b>{{ @trans('user.action') }}</b></th>
                                          </tr>
                                      </thead>
                                      <tbody>
<?php
$i = 1;
foreach ($drivers as $d) {
    ?>
                                                                                                                                                              <tr>
                                                                                                                                                                  <td><?= $i ?></td>
                                                                                                                                                                  <td>{{ $d['first_name'] . ' ' . $d['last_name'] }}
                                                                                                                                                                  </td>
                                                                                                                                                                  <td>{{ $d['mobile_numebr'] }}</td>
                                                                                                                                                                  <td>{{ $d['adhar_card_no'] }}</td>
                                                                                                                                                                  <td>{{ $d['driving_licence_no'] }}</td>
                                                                                                                                                                  <td>{{ $d['driving_licence_expiry_date'] }}</td>
                                                                                                                                                                  <td>{{ $d['street_address'] }}</td>
                                                                                                                                                                  <td class="act-btn">
                                                                                                                                                                      <a href="<?= url(Session::get('panel') . '/driver/show') ?>/<?= $d['user_id'] ?>"
                                                                                                                                                                         title="View" class="btn-sm btn-view">
                                                                                                                                                                          <i class="material-icons">remove_red_eye</i>
                                                                                                                                                                      </a>
                                                                                                                                                                      {{-- <a href="<?= url(Session::get('panel') . '/driver/show') ?>/<?= $d['user_id'] ?>"
                                                                                                                                                                                                                                                                      title="View" class="btn-sm btn-view">
                                                                                                                                                                      <i class="material-icons">edit</i>
                                                                                                                                                                                                  </a> --}}
                                                                                                                                                                          </td>
                                                                                                                                                                          
                                                                                                                                                              </tr>
    <?php
    $i++;
}
?>
                                      </tbody>
                                  </table>
                              </div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
  </div>
  @endif
-->
<!-- End -->

<!-- Trip Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Trip</h6>
                </div>

                <div class="row">
                    <div class="table-responsive table-data" v-if="items.length">
                        <div class="col-sm-12">
                            <div class="table-checkable">
                                <table id="tripdetails"
                                       class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                       width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><b>Trip No</b></th>
                                            <th><b>Trip ID</b></th>
                                            <th><b>Trip Type</b></th>
                                            <th><b>Cab type</b></th>
                                            <th><b>Cab No</b></th>
                                            <th><b>Date</b></th>
                                            <th><b>Est-Start time </b></th>
                                            <th><b>Start Location </b></th>
                                            <th><b>Est-Drop time </b></th>
                                            <th><b>Drop Location </b></th>
                                            <th><b>Driver Name </b></th>
                                            <th><b>{{ @trans('usertype.action') }}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($trip as $c) {
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td>{{ $c['trip_type'] }}</td>
                                                <td> - </td>
                                                <td>{{ $c['trip_type'] }}</td>
                                                <td>{{ $c['vehicle_type'] }}</td>
                                                <td>{{ strtoupper($c['vehicle_number']) }}</td>
                                                <td>{{ $c['booking_datetime'] }}</td>
                                                <td>{{ $c['trip_start_datetime'] }}</td>
                                                <td></td>
                                                <td>{{ $c['trip_end_datetime'] }}</td>
                                                <td>{{ $c['drop_location'] }}</td>
                                                <td></td>
                                                <td class="act-btn">
                                                    <a href="<?= url(Session::get('panel') . '/cabs/show') ?>/<?= $c['id'] ?>"
                                                       title="View" class="btn-sm btn-view">
                                                        <i class="material-icons">remove_red_eye</i>
                                                    </a>
                                                </td>
                                                <!-- <td class="act-btn">
                                                                                                    <a :href="'{{ url(Session::get('panel') . '/userType/edit') }}/' +
                                                                                                    item.id" title="Edit"
                                                                                                        class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                                                                    </a>
                                                                                                    <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                                                        class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                                                                    </a>
                                                                                                </td> -->
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<!-- Cab Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Cabs</h6>
                </div>

                <div class="row">
                    <div class="table-responsive table-data" v-if="items.length">
                        <div class="col-sm-12">
                            <div class="table-checkable">
                                <table id="cabs"
                                       class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                       width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><b>{{ @trans('cabs.cab_post_type') }}</b></th>
                                            <th><b>{{ @trans('cabs.start_date') }}</b></th>
                                            <th><b>{{ @trans('cabs.start_time') }}</b></th>
                                            <th><b>{{ @trans('cabs.start_location') }}</b></th>
                                            <th><b>{{ @trans('cabs.end_location') }}</b></th>
                                            <th><b>{{ @trans('cabs.vehicle') }}</b></th>
                                            <th><b>{{ @trans('usertype.action') }}</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($cabs as $c) {
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td>{{ $c['cab_post_type'] }}</td>
                                                <td>{{ $c['start_date'] }}</td>
                                                <td>{{ $c['start_time'] }}</td>

                                                <td><span data-placement="bottom" data-toggle="tooltip"
                                                          title="{{ $c['start_location'] }}"><?= $c['start_location'] ?></span>
                                                </td>

                                                <td><span data-placement="bottom" data-toggle="tooltip"
                                                          title="{{ $c['end_location'] }}"><?= $c['end_location'] ?></span>
                                                </td>
                                                <td>{{ strtoupper($c['vehicle_number']) }}</td>

                                                <td class="act-btn">
                                                    <a href="<?= url(Session::get('panel') . '/cabs/show') ?>/<?= $c['id'] ?>"
                                                       title="View" class="btn-sm btn-view">
                                                        <i class="material-icons">remove_red_eye</i>
                                                    </a>
                                                </td>
                                                <!-- <td class="act-btn">
                                                                                                    <a :href="'{{ url(Session::get('panel') . '/userType/edit') }}/' +
                                                                                                    item.id" title="Edit"
                                                                                                        class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                                                                    </a>
                                                                                                    <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                                                        class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                                                                    </a>
                                                                                                </td> -->
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<!-- Referral Details -->
<div class="row row-sm">
    <div class="col-lg-12">
        <div class="card custom-card">
            <div class="card-body">
                <div>
                    <h6 class="main-content-label mb-1">Referral and Earn</h6>
                </div>

                <div class="row">
                    <div class="table-responsive table-data" v-if="items.length">
                        <div class="col-sm-12">
                            <div class="table-checkable">
                                <table id="referraldetails"
                                       class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                       width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><b>User</b></th>
                                            <th><b>Mobile Number</b></th>
                                            <th><b>User Type</b></th>
                                            <th><b>Documentation Status</b></th>
                                            <th><b>Payment Status</b></th>
                                            <th><b>Is Claimed</b></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $i = 1;
                                        foreach ($referrals as $r) {
                                            ?>
                                            <tr>
                                                <td><?= $i ?></td>
                                                <td>{{ $r['first_name'] . ' ' . $r['last_name'] }}</td>
                                                <td>{{ $r['mobile_number'] }}</td>
                                                <td>{{ $r['user_type_name'] }}</td>
                                                <td>
                                                    <?php
                                                    if ($r['documentation_status'] == '1') {
                                                        ?>
                                                        <span>Yes</span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span>No</span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($r['payment_status'] == '1') {
                                                        ?>
                                                        <span>Yes</span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span>No</span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($r['is_claimed'] == '1') {
                                                        ?>
                                                        <span>Yes</span>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <span>No</span>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            <?php
                                            $i++;
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End -->

<!-- Row -->
<div class="row row-sm">
    <div class="col-lg-12 col-md-12">
        <div class="card custom-card main-content-body-profile">
            <div class="tab-content">
                <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                    <div class="card-body p-0 border p-0 rounded-10">

                        <div class="p-4">
                            <label class="main-content-label tx-13 mg-b-20">Totals Earnings</label>

                            <div class="row row-sm">
                                <div class="col-md-12">
                                    <div class="card custom-card">
                                        <div class="row row-sm">
                                            <div
                                                class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">Today's</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div
                                                class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">Yesterdays's</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div
                                                class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">Weekly</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div
                                                class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">This</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div
                                                class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">Last Month</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                            <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0">
                                                <div class="card-body text-center">
                                                    <h6 class="mb-0">All</h6>
                                                    <h2 class="mb-1 mt-2 number-font">
                                                        <span class="counter">000</span>
                                                    </h2>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- total earns -->

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- End Row -->
@endif
</div>
</div>
</div>
<!-- End Main Content-->
@endsection

@if (!empty($user))
<!-- Change Status -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Edit User Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" id="frmAddEditUser"
                      action="{{ route('admin.users.changesStatus', ['panel' => Session::get('panel')]) }}">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $user->id }}">

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span
                                class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $user->name }}"
                               placeholder="Enter Name" />
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user') }}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.email') }}<span
                                class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control"
                               value="{{ $user->email }}" placeholder="Enter Email" />
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user') }}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.phone') }}<span
                                class="required">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                               value="{{ $user->mobile_number }}" placeholder="Enter Mobile Number" />
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user') }}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.dob') }}<span
                                class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control dob"
                               value="{{ $user->dob }}" placeholder="Enter Date Of Birth" />
                        @if ($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user') }}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">User Status</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio3"
                                   name="user_status" value="3"
                                   @if ($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio"
                                   name="user_status" value="1"
                                   @if ($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1"
                                   name="user_status" value="0"
                                   @if ($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2"
                                   name="user_status" value="2"
                                   @if ($user->user_status == '2') checked @endif>
                            <label class="custom-control-label" for="customRadio2">Rejected</label>
                        </div>
                    </div>

                    <hr />

                    <!-- Modal footer -->
                    <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                        <button type="submit" class="btn btn-bordered-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Image View Modal -->
<div class="modal" id="attachModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0 pb-0">
                <h4 class="modal-title regular body-font-size img-title">Image View</h4>
                <button type="button" class="close" data-dismiss="modal"
                        onclick="closeImgModel()">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="before-zoom">
                <div class="modal-body" id="panzoom">
                    <img id="image-gallery-image" class="mainimage" src="">
                </div>
            </div>

            <div class="modal-footer">
                <!-- <button type="button" class="btn-primary" onclick="zoomin()">Zoom In</button>
            <button type="button" class="btn-primary" onclick="zoomout()">Zoom Out</button> -->
                <button type="button" class="btn-primary" id="rotate" onclick="rotate()">Rotate</button>
            </div>
        </div>
    </div>
</div>

<!-- PDF View Modal -->
<div class="modal" id="attachPdfModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0 pb-0">
                <h4 class="modal-title regular body-font-size pdf-title">PDF View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <embed id="view-pdf" src="sample.pdf" width="720" height="475" />
            </div>

        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal" id="imageUploadModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-md modal-dialog-centered">

        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Update Image</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" id="frmAddEditUser"
                      action="{{ route('admin.users.changeImage', ['panel' => Session::get('panel')]) }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{ $user->id }}">
                    <input type="hidden" name="hidd_type" id="hidd_type" value="">
                    <input type="hidden" name="pk_id" id="pk_id" value="">
                    <input type="hidden" name="pk_key" id="pk_key" value="">
                    <input type="hidden" name="image_key_val" id="image_key_val" value="">
                    <input type="hidden" name="module_name" id="module_name" value="">
                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span
                                class="required">*</span></label>
                        <div class="mg-b-10" id="fnWrapper">
                            <input type="file" name="police_verification" id="image_key"
                                   class="dropify driver" data-height="200" />
                        </div>
                    </div>
                    <hr />
                    <!-- Modal footer -->
                    <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                        <button type="submit" class="btn btn-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>


<!-- Image Upload Modal -->
<div class="modal" id="DriverManageModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-lg">

        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Driver</h4>
                <button type="button" class="close" data-dismiss="modal" id="close_driver_modal"> &times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div id="DriverPageForm">

                </div>
            </div>
        </div>

    </div>
</div>


<div class="modal" tabindex="-1" role="dialog" id="modalDeleteConfirm">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.reset', ['panel' => Session::get('panel')]) }}"
                  method="post">
                @csrf
                <input type="hidden" name="user_reset_id" id="user_id" value="{{ $user->id }}">
                <div class="modal-header">
                    <h5 class="modal-title">{{ @trans('delete_modal.confirm') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @if (Route::currentRouteName() == 'admin.jobs.show')
                    <p>{{ @trans('delete_modal.delete_applicant') }}</p>
                    @else
                    <p>Are you sure you want to Reset Profile?</p>
                    @endif
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-cncl"
                            data-dismiss="modal">{{ @trans('delete_modal.cancel') }}</button>
                    @if (Route::currentRouteName() == 'admin.jobs.show')
                    <button type="submit" class="btn btn-danger"
                            @click="destroy">{{ @trans('delete_modal.confirm') }}</button>
                    @else
                    <button type="submit" class="btn btn-danger" @click="destroy">Reset Profile</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@push('pageJs')
<link rel="stylesheet" type="text/css"
      href="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}">

<link rel="stylesheet" type="text/css"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js">
</script>

<script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom/dist/panzoom.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
        integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
        integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
crossorigin="anonymous"></script>

<script>

                    function getCities() {
                    var state = $("#stateid").find(":selected").text();
                    // alert(state);
                    $.ajaxSetup({
                    headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                    });
                    $.ajax({
                    type: 'GET',
                            url: "{{ route('admin.trip.stateCode', ['panel' => Session::get('panel')]) }}",
                            data: {
                            'state': state,
                            },
                            dataType: 'json',
                            success: function(response) {
                            console.log(response);
                            var partyNameArrays_e = [];
                            var partyNameArray = [];
                            var partyNameArrayBefore = [];
                            let optionLists;
                            partyNameArrays_e = {
                            'text': "Select Option",
                                    'value': "0"
                            };
                            $('#city_id').empty();
                            partyNameArray.push(partyNameArrays_e);
                            response.city_name.forEach(element => {
                            partyNameArrayBefore = {
                            'text': element.name,
                                    'value': element.name
                            };
                            partyNameArray.push(partyNameArrayBefore);
                            });
                            optionLists = document.getElementById('city_id').options;
                            length_e = optionLists.length;
                            partyNameArray.forEach(option => {
                            optionLists.add(
                                    new Option(option.text, option.value, option
                                            .selected)
                                    )
                            });
                            }

                    });
                    }

</script> 
<script type="text/javascript">

    function confirmModal() {
    var id = $('#user_reset_id').val();
    $('#user_id').val(id);
    $('#modalDeleteConfirm').modal();
    }
    $(document).ready(function() {
    const element = document.getElementById('panzoom')
            const panzoom = Panzoom(element, {
            // options here
            });
    // enable mouse wheel
    const parent = element.parentElement
            parent.addEventListener('wheel', panzoom.zoomWithWheel);
    // This demo binds to shift + wheel
    parent.addEventListener('wheel', function(event) {
    if (!event.shiftKey) return
            panzoom.zoomWithWheel(event)
    })

            // Pass options
            $(".panzoom").panzoom({
    minScale: 0,
            $zoomRange: $("input[type='range']")
    });
    });
    function closeImgModel() {
    //console.log("close button click here...");
    $("#image-gallery-image").attr('src', '');
    $("#image-gallery-image").css({
    'transform': ''
    });
    $("#rotate").attr('onclick', 'rotate()');
    $("#panzoom").css('transform', 'none');
    }

    $(document).on('keydown', function(event) {
    if (event.key == "Escape") {
    closeImgModel();
    $('#attachModal').modal('hide');
    $('#attachPdfModal').modal('hide');
    }
    });
    function rotate() {
    $("#image-gallery-image").css({
    'transform': 'rotate(-90deg)'
    });
    $("#rotate").attr('onclick', 'rotate1()');
    }

    function rotate1() {
    $("#image-gallery-image").css({
    'transform': 'rotate(-180deg)'
    });
    $("#rotate").attr('onclick', 'rotate2()');
    }

    function rotate2() {
    $("#image-gallery-image").css({
    'transform': 'rotate(-270deg)'
    });
    $("#rotate").attr('onclick', 'rotate3()');
    }

    function rotate3() {
    $("#image-gallery-image").css({
    'transform': ''
    });
    $("#rotate").attr('onclick', 'rotate()');
    }

    function getImage(imageName, title) {
    $('#image-gallery-image').attr('src', imageName);
    $(".img-title").text(title);
    $("#attachModal").modal();
    }

    function getPdf(filename, title) {
    $('#view-pdf').attr('src', filename);
    $(".pdf-title").text(title);
    $("#attachPdfModal").modal();
    }

    function imageUpload(type, id, key, image_key, module_name) {
    $('#hidd_type').val(type);
    $('#pk_id').val(id);
    $('#pk_key').val(key);
    $('#image_key').attr('name', image_key);
    $('#image_key_val').val(image_key);
    $('#module_name').val(module_name);
    $("#imageUploadModal").modal();
    }

    @if (Session::has('message'))
            Snackbar.show({
            pos: 'bottom-right',
                    text: "{!! session('message') !!}",
                    actionText: 'Okay'
            });
    @endif

            $('#load2').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
    $this.button('reset');
    }, 10000);
    var id = $('#user_id').val();
    var siteurl = "{{ URL::to('/') }}/api/v1/createVcnForWeb";
    $.ajax({
    url: siteurl,
            type: "POST",
            data: {
            "_token": "{{ csrf_token() }}",
                    "user_id": id
            },
            success: function(response) {
            var message = response.message;
            var errorcode = response.errorcode;
            var success = response.success;
            var dataobject = response.data;
            if (errorcode == 1 && success == true) {
            var masked_card = dataobject.masked_card;
            $('#card_number').text(masked_card);
            Snackbar.show({
            pos: 'bottom-right',
                    text: message,
                    actionText: 'Okay'
            });
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: message,
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
            });
            }
            $this.button('reset');
            }
    });
    });
    $('#reset_btn').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
    $this.button('reset');
    }, 10000);
    var id = $('#user_id').val();
    var siteurl = "{{ url(Session::get('panel') . '/users/resetAttempt') }}";
    $.ajax({
    url: siteurl,
            type: "POST",
            data: {
            "_token": "{{ csrf_token() }}",
                    "user_id": id
            },
            success: function(response) {
            if (response == 'success') {
            $('#reset_number').text(0);
            Snackbar.show({
            pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
            });
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
            });
            }
            $this.button('reset');
            }
    });
    });
    $('#otp_reset_btn').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
    $this.button('reset');
    }, 10000);
    var id = $('#user_id').val();
    var siteurl = "{{ url(Session::get('panel') . '/users/resetOtpAttempt') }}";
    $.ajax({
    url: siteurl,
            type: "POST",
            data: {
            "_token": "{{ csrf_token() }}",
                    "user_id": id
            },
            success: function(response) {
            if (response == 'success') {
            $('#otp_reset_number').text(0);
            Snackbar.show({
            pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
            });
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
            });
            }
            $this.button('reset');
            }
    });
    });
    $('#ussd_status_btn').on('click', function() {
    var id = $('#user_id').val();
    var ussd_enable = $('#ussd_enable').val();
    var siteurl = "{{ url(Session::get('panel') . '/users/changeUssdStatus') }}";
    $.ajax({
    url: siteurl,
            type: "POST",
            data: {
            "_token": "{{ csrf_token() }}",
                    "user_id": id,
                    "ussd_enable": ussd_enable
            },
            success: function(response) {
            if (response == 'success') {

            if (ussd_enable == 1) {
            $('#ussd_enable_lable').text('On');
            $('#ussd_enable').val('0');
            $('.ussd-enable').text('Disable');
            } else {
            $('#ussd_enable_lable').text('Off');
            $('#ussd_enable').val('1');
            $('.ussd-enable').text('Enable');
            }

            Snackbar.show({
            pos: 'bottom-right',
                    text: 'USSD status change successfully.',
                    actionText: 'Okay'
            });
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
            });
            }
            }
    });
    });
    function documentVerify(type, id, key, status) {

    var statustext = 'Approve';
    if (status == 1) {
    statustext = 'Approve';
    } else {
    statustext = 'Reject';
    }

    var user_id = $('#user_id').val();
    if (confirm('Are you sure you want to ' + statustext + ' this?')) {
    var siteurl = "{{ url(Session::get('panel') . '/users/documentVerification') }}";
    $.ajax({
    url: siteurl,
            type: "POST",
            data: {
            "_token": "{{ csrf_token() }}",
                    "id": id,
                    "type": type,
                    "key": key,
                    "status": status,
                    "user_id": user_id
            },
            success: function(response) {
            if (response == 'success') {

            if (status == '1') {
            $('#' + key + '_approved').css('display', '');
            } else {
            $('#' + key + '_rejected').css('display', '');
            }
            $('.' + key + '_btn').css('display', 'none');
            if (status == '1') {
            Snackbar.show({
            pos: 'bottom-right',
                    text: 'Document verify successfully.',
                    actionText: 'Okay'
            });
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    text: 'Document Rejected!',
                    actionText: 'Okay'
            });
            }
            } else {
            Snackbar.show({
            pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
            });
            }
            }
    });
    }
    }

    $('#vehicles').DataTable({
    language: {
    searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ Record Per Page',
    }
    });
    $('#cabs').DataTable({
    language: {
    searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ Record Per Page',
    }
    });
    $('#driverdetails').DataTable({
    language: {
    searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ Record Per Page',
    }
    });
    $('#referraldetails').DataTable({
    language: {
    searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ Record Per Page',
    }
    });
    $('#tripdetails').DataTable({
    language: {
    searchPlaceholder: 'Search...',
            sSearch: '',
            lengthMenu: '_MENU_ Record Per Page',
    }
    });</script>
<script>
    $(document).on('submit', '#agent_id', function(event) {
    event.preventDefault();
    $('.travel_edit').css('display', 'none');
    $('.travel_name,.owner_name,.agent_office_no,.total_business_year,.pan_card,.adhar_card,.account_number,.bank_name,.branch_name,.ifsc_code')
            .prop('type', 'text');
    $('.edit_travel').css('display', 'block');
    });
    function editTravelDetails() {
    var travel_name = $('#travel_name').val();
    var owner_name = $('#owner_name').val();
    var agent_office_no = $('#agent_office_no').val();
    var total_business_year = $('#total_business_year').val();
    var pan_card = $('#pan_card').val();
    var adhar_card = $('#adhar_card').val();
    var account_number = $('#account_number').val();
    var bank_name = $('#bank_name').val();
    var branch_name = $('#branch_name').val();
    var ifsc_code = $('#ifsc_code').val();
    var agent_id = $('#agent_id').val();
    var user_id = $('#user_id').val();
    var agent_data = $('#agent_data').val();
    var bank_data = $('#bankId').val();
    var siteurl = "{{ route('admin.register_agent.update', ['panel' => Session::get('panel')]) }}";
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: 'POST',
            url: siteurl,
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            'travel_name': travel_name,
                    'owner_name': owner_name,
                    'agent_office_no': agent_office_no,
                    'total_business_year': total_business_year,
                    'pan_card': pan_card,
                    'adhar_card': adhar_card,
                    'account_number': account_number,
                    'bank_name': bank_name,
                    'branch_name': branch_name,
                    'ifsc_code': ifsc_code,
                    'agent_id': agent_id,
                    'agent_data': agent_data,
                    'user_id': user_id,
                    'bank_data': bank_data,
            },
            dataType: 'json',
            success: function(response_msg) {
            console.log(response_msg.success);
            if (response_msg.success == true) {
            // toastr.success('User Data Updated..');
            window.location.reload();
            } else {
            toastr.error('User Data Update Failed');
            }
            }
    });
    }


</script>


<script>
    $(document).ready(function($) {
    $(document).on('submit', '#submit-form', function(event) {
    event.preventDefault();
    $('.user_edit').css('display', 'none');
    $('input[id="user_edit_detail"]').prop('type', 'text');
    $('.edit_submit').css('display', 'block');
    });
    });
    function editUserDetails() {
    var rider_name = $('.rider_name').val();
    var state_name = $('.state_name').val();
    var city_name = $('.city_name').val();
    var user_id = $('#user_id').val();
    var user_data = $('#user_data').val();
    var user_interest_status = $('#user_interest_status').find(":selected").val();
    var new_user_interest_status = $('#new_user_interest_status').val();
    var siteurl = "{{ route('admin.register.update', ['panel' => Session::get('panel')]) }}";
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: 'POST',
            url: siteurl,
            headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
            'rider_name': rider_name,
                    'state_name': state_name,
                    'city_name': city_name,
                    'user_id': user_id,
                    'user_data': user_data,
                    'user_interest_status': user_interest_status,
                    'new_user_interest_status': new_user_interest_status,
            },
            dataType: 'json',
            success: function(response_msg) {
            if (response_msg.success == true) {
            // toastr.success('User Data Updated..');
            window.location.reload();
            } else {
            toastr.error('User Data Update Failed');
            }
            }
    });
    }
</script>
<script>
    function select_state_city() {
    var state = $(".state_name").find(":selected").text();
    // alert(state);
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: 'GET',
            url: "{{ route('admin.trip.stateCode', ['panel' => Session::get('panel')]) }}",
            data: {
            'state': state,
            },
            dataType: 'json',
            success: function(response) {
            console.log(response);
            var partyNameArrays_e = [];
            var partyNameArray = [];
            var partyNameArrayBefore = [];
            let optionLists;
            partyNameArrays_e = {
            'text': "Select Option",
                    'value': "0"
            };
            $('#city_id').empty();
            partyNameArray.push(partyNameArrays_e);
            response.city_name.forEach(element => {
            partyNameArrayBefore = {
            'text': element.name,
                    'value': element.name
            };
            partyNameArray.push(partyNameArrayBefore);
            });
            optionLists = document.getElementById('city_id').options;
            length_e = optionLists.length;
            partyNameArray.forEach(option => {
            optionLists.add(
                    new Option(option.text, option.value, option
                            .selected)
                    )
            });
            }

    });
    }
</script>
<script>
    $('#agent_office_no').on('keyup', function() {
    var mob = $('#agent_office_no').val();
    if (mob) {
    var regx = /^[0-9]+$/;
    if (regx.test(mob) == false) {
    $(".mob_err").html("Please enter only number");
    $("#edit_travel_details").attr("disabled", true);
    } else if (mob.length < 10 || mob.length > 10) {
    $(".mob_err").html("Only 10 character allowed");
    $("#edit_travel_details").attr("disabled", true);
    } else {
    $(".mob_err").html("");
    $("#edit_travel_details").attr("disabled", false);
    }
    } else {
    $(".mob_err").html("");
    $("#edit_travel_details").attr("disabled", true);
    }
    });</script>
{{-- <script>
        const validateEmail = (email) => {
            return email.match(
                /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
        };

        const validate = () => {
            const $result = $('#result');
            const email = $('.emailid').val();
            $result.text('');

            if (validateEmail(email)) {
                // $result.text(email + ' is valid :)');
                // $result.css('color', 'green');
                $("#travelUser").attr("disabled", false);
            } else {
                $result.text('Please Enter Valid Email address');
                $result.css('color', 'red');
                $("#travelUser").attr("disabled", true);
            }
            return false;
        }

        $('.emailid').on('input', validate);
    </script> --}}
<script>
    function accountNumber() {

    const $result = $('#validAccountNo');
    $result.text('');
    var account_no = $('.account_number').val();
    if (account_no.length >= 9 && account_no.length <= 12) {
    $("#edit_travel_details").attr("disabled", false);
    } else {
    $result.text('Please Enter Valid Account Number');
    $result.css('color', 'red');
    $("#edit_travel_details").attr("disabled", true);
    }
    }
    </script>

    <script>
        function panCardNumber() {
        const $validPanCard = $('.valid_pan_card');
        $validPanCard.text('');
        var panVal = $('.pan_card').val();
        var regpan = /^([a-zA-Z]){5}([0-9]){4}([a-zA-Z]){1}?$/;
        if (regpan.test(panVal)) {
        $("#edit_travel_details").attr("disabled", false);
        } else {
        $("#edit_travel_details").attr("disabled", true);
        $validPanCard.text('Please Enter Valid PanCard Number');
        $validPanCard.css('color', 'red');
        }
        }
    </script>
    <script>
        function adharCardValidation() {

        const $validAdharCard = $('.valid_adhar_card');
        $validAdharCard.text('');
        var regex = /^\d{12}$/;
        if (regex.test($(".adhar_card").val())) {

        $("#edit_travel_details").attr("disabled", false);
        } else {

        $("#edit_travel_details").attr("disabled", true);
        $validAdharCard.text('Please Enter Valid AdharCard Number');
        $validAdharCard.css('color', 'red');
        // $("#edit_travel_details").attr("disabled", true);
        }
        }
    </script>
    <script>
        function ValidateAlpha(evt) {
        var keyCode = (evt.which) ? evt.which : evt.keyCode
                if ((keyCode < 65 || keyCode > 90) && (keyCode < 97 || keyCode > 123) && keyCode != 32)

                return false;
        return true;
        }
    </script>
    <script>
        function ifsc_check() {
        const $validIFSCCode = $('#ifsc_code_error');
        $validIFSCCode.text('');
        var ifsc = $('#ifsc_code').val();
        var Api = "https://ifsc.razorpay.com/" + ifsc;
        console.log(Api);
        let request = new XMLHttpRequest();
        request.open("GET", Api);
        request.send();
        request.onload = () => {
        console.log(request);
        if (request.status == 200) {
        console.log("Yes");
        var response = JSON.parse(request.response);
        var branchName = response['BRANCH'];
        var bankName = response['BANK'];
        console.log(branchName, bankName);
        $("#edit_travel_details").attr("disabled", false);
        $("#branch_name").val(branchName);
        $("#bank_name").val(bankName);
        } else {
        console.log(`error ${request.status} ${request.statusText}`);
        $validIFSCCode.text('Please Enter Valid IFSC Code');
        $validIFSCCode.css('color', 'red');
        $("#edit_travel_details").attr("disabled", true);
        }
        }
        }
    </script>
    <script>
        function HistoryShow() {
        $(".history_show").toggle();
        }
    </script>

    <script>

        $('#close_driver_modal').on('click', function(){
        $('#DriverManageModal').modal('hide');
        });
        $('body').on('click', '#driving_license', function(){
        $('#driving_license').html('Searching..');
        license_no = $('#license_no').val();
        driver_bod = $('#driver_bod').val();
        $('.manage_driver_save_buttton').prop('disabled', true);
        $.ajaxSetup({
        headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
        });
        $('#errorMessageDrivePage').html('');
        $.ajax({
        type: 'POST',
                url: "{{ route('admin.driver.getDrivingLicenceDetails',['panel' => Session::get('panel')]) }}",
                data: {
                'license_no': license_no,
                        'driver_bod': driver_bod,
                },
                dataType: 'json',
                success: function(response) {
                $('.manage_driver_save_buttton').prop('disabled', false);
                $('#driving_license').html('Search');
                if (response.status == "error"){
                $('#errorMessageDrivePage').html('<span class="text-danger">' + response.message);
                return false
                }
                $('#street_address').val(response.street_address);
                $('#first_name').val(response.first_name);
                $('#last_name').val(response.last_name);
                $('#father_name').val(response.father_name);
                $('#issue_date').val(response.issue_date);
                $('#expiry_date').val(response.expiry_date);
                $('#pincode').val(response.pincode);
                $('#state_id').val(response.state);
                $('#city_id').val(response.city);
                if (response.gender == "Female"){
                $('#gender_female').prop('checked', 'true');
                $('#gender_male').prop('checked', 'false');
                } else{
                $('#gender_female').prop('checked', '');
                $('#gender_male').prop('checked', 'true');
                }

                console.log(response);
                }, error:function(xhr, status, error) {
        console.log(error);
        $('.manage_driver_save_buttton').prop('disabled', false);
        $('#driving_license').html('Search');
        $('#errorMessageDrivePage').html('<span class="text-danger">' + error);
        }


//                    
        });
        });
  
        $('body').on('click', '.DriverManage', function(){
        user_id = $(this).attr('data-user-id');
        var siteurl = "{{ route('admin.driver.manageDriver',['panel' => Session::get('panel')]) }}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{ csrf_token() }}",
                        "id": user_id
                },
                success: function(response) {
                $('#DriverManageModal').modal('show');
                $('#DriverPageForm').html(response);
                }, error:function(response){
        alert('Something went wrong!')
        }
        });
        });
        $('body').on('click', '.EditDriverManage', function(){
        user_id = $(this).attr('data-user-id');
        driver_id = $(this).attr('data-id');
        $('#DriverManageModal').modal('show');
        var siteurl = "{{ route('admin.driver.manageDriver',['panel' => Session::get('panel')]) }}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{ csrf_token() }}",
                        "user_id": user_id,
                        "driver_id" : driver_id
                },
                success: function(response) {
                $('#DriverPageForm').html(response);
                }, error:function(response){

        }
        });
        });
    </script>
    @endpush
