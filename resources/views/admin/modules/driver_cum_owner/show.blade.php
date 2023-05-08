@extends('admin.layouts.main')

@section('title')
Driver Cum Owner Details
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">
            <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Driver Cum Owner Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index',['panel' => Session::get('panel')]) }}">Driver Cum Owner</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
                <div>
                    <!-- <form action="{{ route('admin.users.reset',['panel' => Session::get('panel')]) }}" method="post">
                        @csrf -->
                        <input type="hidden" name="user_reset_id" id="user_reset_id" value="{{ $user->id }}">
                        <button class="btn btn-primary" onclick="confirmModal()">Reset Profile</button>
                    <!-- </form> -->
                </div>
                @if(!empty($last_updated_user_details))
                <div>
                    <h4 class="mg-b-5"><span style="color: #686868;">Last Updated By:</span> {{$last_updated_user_details->first_name}} {{$last_updated_user_details->last_name}}</h4>
                </div>
                @endif
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12 col-md-12">
                    <div class="card custom-card main-content-body-profile">
                        <div class="tab-content">
                            <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                                <div class="card-body p-0 border p-0 rounded-10">
                                    <!-- <div class="border-top"></div> -->
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">User</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Owner Name</span>
                                                                    <div class="user_edit">{{ ucwords(strtolower($user->first_name)).' '.ucwords(strtolower($user->last_name)) }}</div>
                                                                   
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Driver Name</span>
                                                                    <div class="user_edit">{{ ucwords(strtolower($user->first_name)).' '.ucwords(strtolower($user->last_name)) }}</div>
                                                                  

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>CAB Type</span>
                                                                    @if(!empty($vehicle_type))
                                                                    <div class="user_edit">{{$vehicle_type['name']}}</div>
                                                                  

                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>CAB NO</span>
                                                                    @if(isset($vehicle_details->vehicle_number))
                                                                    <div class="user_edit">{{ strtoupper($vehicle_details->vehicle_number) }}</div>
                                                                  

                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.mobile_number') }}</span>
                                                                    <div class="user_edit">{{$user->mobile_number}}</div>
                                                                    

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.email') }}</span>
                                                                    <div class="user_edit">{{$user->emailid}}</div>
                                                                    

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Refer code</span>
                                                                    <div class="user_edit">{{$user->reference_code}}</div>
                                                                   

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>State Name</span>
                                                                    <div class="user_edit">{{$user->state_name}}</div>
                                                                  

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>City Name</span>
                                                                    <div class="user_edit">{{$user->city_name}}</div>
                                                                  

                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>No .of people refered to</span>
                                                                    <div>111</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                                <!-- <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.trip_status') }}</span>
                                                                    <div>{{$trip_status}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                            </div>
                                        </div>
                                        <button type="button" class="user_edit btn-primary btn-sm mt-2" onclick="editUserDetails()">Submit</button>
                                    </div> <!-- user -->

                                    @if($user->user_type_id == '2' || $user->user_type_id == '3')
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Agent</label>

                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.travel_name') }}</span>
                                                                    <div>{{ ucwords(strtolower($user->travel_name)) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.owner_name') }}</span>
                                                                    <div>{{ ucwords(strtolower($user->owner_name)) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.office_no') }}</span>
                                                                    <div>{{$user->office_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.total_business_year') }}</span>
                                                                    <div>{{$user->total_business_year}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-sm-flex">
                                            &nbsp;
                                        </div>

                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.pan_card') }}</span>
                                                                    <div>{{$user->pan_card}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.adhar_card') }}</span>
                                                                    <div>{{$user->adhar_card}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.bank_account_id') }}</span>
                                                                    <div>{{$user->bank_account_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.agent_earnings') }}</span>
                                                                    <div>{{$earnings}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div> <!-- agent -->
                                    @endif

                                    @if( ($user->user_type_id == '4' || $user->user_type_id == '5') && !empty($driver_details) )
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Driver</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.driver_mobile_numebr') }}</span>
                                                                    <div>{{$driver_details->mobile_numebr}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.name') }}</span>
                                                                    <div>{{ucwords(strtolower($driver_details->first_name)).' '.ucwords(strtolower($driver_details->last_name))}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.adhar_card_no') }}</span>
                                                                    <div>{{$driver_details->adhar_card_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.driving_licence_no') }}</span>
                                                                    <div>{{$driver_details->driving_licence_no}}</div>
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
                                                                <div class="media-body"> <span>{{ @trans('user.driving_licence_expiry_date') }}</span>
                                                                    <div>{{$driver_details->driving_licence_expiry_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.street_address') }}</span>
                                                                    <div>{{$driver_details->street_address}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                                    <div>{{$driver_trip}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> <!-- driver -->
                                    @endif

                                    <?php if (!empty($plan_details)) { ?>
                                        <div class="border-top"></div>

                                        <div class="p-4">
                                            <label class="main-content-label tx-13 mg-b-20">subscription plan</label>
                                            <div class="d-sm-flex">
                                                <div class="row col-md-12">
                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.plan_name') }}</span>
                                                                        <div>{{$plan_details->name}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.plan_validity') }}</span>
                                                                        <div>{{$plan_details->plan_validity}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.price') }}</span>
                                                                        <div>{{$plan_details->price}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.start_date') }}</span>
                                                                        <div>{{ date("d-m-Y H:i:s", strtotime($plan->start_datetime))}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.end_date') }}</span>
                                                                        <div>{{ date("d-m-Y H:i:s", strtotime($plan->end_datetime))}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>{{ @trans('user.subscription_status') }}</span>
                                                                        <div>{{$subscription_status}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <!-- subscription plan -->


                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Owner Documents</label>
                                        <div class="">
                                            <div class="row row-sm">
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Aadhaar No </span>
                                                                    @if(!empty($driver_details->adhar_card_no))
                                                                    <div>{{$driver_details->adhar_card_no}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Pan No </span>
                                                                    @if(!empty($driver_details->adhar_card_no))
                                                                    <div>{{$driver_details->pan_card_number}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Bank Name </span>
                                                                    @if(!empty($banks->bank_name))
                                                                    <div>{{ ucwords(strtolower($banks->bank_name)) }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Branch Name </span>
                                                                    @if(!empty($banks->branch_name))
                                                                    <div>{{ ucwords(strtolower($banks->branch_name)) }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Account Number </span>
                                                                    @if(!empty($banks->account_number))
                                                                    <div>{{$banks->account_number}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>IFSC Code</span>
                                                                    @if(!empty($banks->ifsc_code))
                                                                    <div>{{$banks->ifsc_code}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>

                                            <br>

                                            <div class="row row-sm">

                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Profile Image</h6>
                                                    <div class="image-div">
                                                        @if(!empty($user->profile_pic))
                                                        <img alt="Profile pic" class="img-thumbnail image-class" src="{{$user->profile_pic}}" onclick="getImage('{{$user->profile_pic}}','Profile Image')">
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="profile_pic_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="profile_pic_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->profile_pic_status == 2 )
                                                    @if(!empty($user->profile_pic))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 profile_pic_status_btn" onclick="documentVerify('user','{{$user->id}}','profile_pic_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 profile_pic_status_btn" onclick="documentVerify('user','{{$user->id}}','profile_pic_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $user->profile_pic_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp;
                                                    <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('user','{{$user->id}}','profile_pic_status','profile_pic','driver_cum_owner')">Change Image</button>
                                                </div>

                                                @if(!empty($user->d_adhar_card_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Aadhaar Card Front</h6>
                                                    <div class="image-div">

                                                        <?php
                                                        $Infos = pathinfo($user->d_adhar_card_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->d_adhar_card_url}}" onclick="getImage('{{$user->d_adhar_card_url}}','Adhar Card')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$user->d_adhar_card_url}}','Aadhaar Card')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>

                                                    </div>
                                                    <span class="text-success" id="d_adhar_card_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="d_adhar_card_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->d_adhar_card_url_status == 2)
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 d_adhar_card_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_url_status','0')">Reject</button>
                                                    @else
                                                    @if( $user->d_adhar_card_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$user->driver_id}}','d_adhar_card_url_status','adhar_card_url','driver_cum_owner')">Change Image</button>
                                                </div>
                                                @endif

                                                @if(!empty($user->d_adhar_card_back_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Aadhaar Card Back</h6>
                                                    <div class="image-div">

                                                        <?php
                                                        $Infos = pathinfo($user->d_adhar_card_back_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->d_adhar_card_back_url}}" onclick="getImage('{{$user->d_adhar_card_back_url}}','Aadhaar Card Back')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$user->d_adhar_card_back_url}}','Aadhaar Card Back')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-success" id="d_adhar_card_back_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="d_adhar_card_back_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->d_adhar_card_back_url_status == 2)
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 d_adhar_card_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','0')">Reject</button>
                                                    @else
                                                    @if( $user->d_adhar_card_back_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','adhar_card_back_url','driver_cum_owner')">Change Image</button>
                                                </div>
                                                @endif

                                                @if(!empty($user->adhar_card_back_url))
                                                <!-- <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Adhar Card Back</h6>
                                                    <div class="image-div">
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->adhar_card_back_url}}" onclick="getImage('{{$user->adhar_card_back_url}}','Adhar Card')">
                                                    </div>
                                                    <span class="text-success" id="d_adhar_card_back_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="d_adhar_card_back_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->d_adhar_card_back_url_status == 2)
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 d_adhar_card_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','0')">Reject</button>
                                                    @else
                                                        @if( $user->d_adhar_card_back_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$user->driver_id}}','d_adhar_card_back_url_status','adhar_card_back_url','driver_cum_owner')">Change Image</button>
                                                </div> -->
                                                @endif

                                                @if(!empty($user->d_pan_card_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Pan Card</h6>
                                                    <div class="image-div">

                                                        <?php
                                                        $Infos = pathinfo($user->d_pan_card_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->d_pan_card_url}}" onclick="getImage('{{$user->d_pan_card_url}}','Pan Card')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$user->d_pan_card_url}}','Pan Card')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                    </div>

                                                    <span class="text-success" id="d_pan_card_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="d_pan_card_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->d_pan_card_url_status == 2)
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 d_pan_card_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_pan_card_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 d_pan_card_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','d_pan_card_url_status','0')">Reject</button>
                                                    @else
                                                    @if( $user->d_pan_card_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$user->driver_id}}','d_pan_card_url_status','pan_card_url','driver_cum_owner')">Change Image</button>
                                                </div>
                                                @endif

                                                @if(($user->user_type_id == '4' || $user->user_type_id == '5') && !empty($user->bank_document_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Bank Cheque Document</h6>
                                                    <div class="image-div">

                                                        <?php
                                                        $Infos = pathinfo($user->bank_document_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->bank_document_url}}" onclick="getImage('{{$user->bank_document_url}}','Bank Document')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$user->bank_document_url}}','Bank Document')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                    </div>
                                                    <span class="text-success" id="bank_document_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="bank_document_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->bank_document_url_status == 2)
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 bank_document_url_status_btn" onclick="documentVerify('bank_account','{{$user->bank_account_id}}','bank_document_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 bank_document_url_status_btn" onclick="documentVerify('bank_account','{{$user->bank_account_id}}','bank_document_url_status','0')">Reject</button>
                                                    @else
                                                    @if( $user->bank_document_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('bank_account','{{$user->bank_account_id}}','bank_document_url_status','document_url','driver_cum_owner')">Change Image</button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div> <!-- owner docs -->

                                    @if(!empty($driver_details))
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Driver Documents</label>
                                        <div class="">

                                            <div class="row row-sm">
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">

                                                                <div class="media-body"> <span>DL NO </span>
                                                                    <div>{{$driver_details->driving_licence_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Driving Licence Expiry date </span>
                                                                    <div>{{$driver_details->driving_licence_expiry_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <div class="row row-sm">

                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">DL Front</h6>
                                                    <div class="image-div">
                                                        @if(!empty($driver_details->dl_front_url))
                                                        <?php
                                                        $Infos = pathinfo($driver_details->dl_front_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$driver_details->dl_front_url}}" onclick="getImage('{{$driver_details->dl_front_url}}','DL Front')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$driver_details->dl_front_url}}','DL Front')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="dl_front_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="dl_front_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $driver_details->dl_front_url_status == 2)
                                                    @if(!empty($driver_details->dl_front_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 dl_front_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','dl_front_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 dl_front_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','dl_front_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $driver_details->dl_front_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif
                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$driver_details->id}}','dl_front_url_status','dl_front_url','driver_cum_owner')">Change Image</button>
                                                </div>

                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">DL Back</h6>
                                                    <div class="image-div">
                                                        @if(!empty($driver_details->dl_back_url))
                                                        <?php
                                                        $Infos = pathinfo($driver_details->dl_back_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$driver_details->dl_back_url}}" onclick="getImage('{{$driver_details->dl_back_url}}','DL Back')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$driver_details->dl_back_url}}','DL Back')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="dl_back_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="dl_back_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $driver_details->dl_back_url_status == 2)
                                                    @if(!empty($driver_details->dl_back_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 dl_back_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','dl_back_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 dl_back_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','dl_back_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $driver_details->dl_back_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif
                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$driver_details->id}}','dl_back_url_status','dl_back_url','driver_cum_owner')">Change Image</button>
                                                </div>



                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Police Verification</h6>
                                                    <div class="image-div">
                                                        @if(!empty($driver_details->police_verification_url))
                                                        <?php
                                                        $Infos = pathinfo($driver_details->police_verification_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$driver_details->police_verification_url}}" onclick="getImage('{{$driver_details->police_verification_url}}','Police Verification')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$driver_details->police_verification_url}}','Police Verification')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="police_verification_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="police_verification_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $driver_details->police_verification_url_status == 2)
                                                    @if(!empty($driver_details->police_verification_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 police_verification_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','police_verification_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 police_verification_url_status_btn" onclick="documentVerify('driver','{{$driver_details->id}}','police_verification_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $driver_details->police_verification_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif
                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('driver','{{$driver_details->id}}','police_verification_url_status','police_verification_url','driver_cum_owner')">Change Image</button>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    <!-- driver docs -->

                                    @if(!empty($vehicle_details))
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Connected Cab Docs</label>
                                        <div class="">
                                            <div class="row row-sm">
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Vehicle No </span>
                                                                    <div>{{ strtoupper($vehicle_details->vehicle_number) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Brand name </span>
                                                                    @if(!empty($brands))
                                                                    <div>{{ $brands['name'] }}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Modal </span>
                                                                    @if(!empty($models))
                                                                    <div>{{$models['name']}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Vehicle Type </span>
                                                                    @if(!empty($vehicle_type))
                                                                    <div>{{$vehicle_type['name']}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Fuel type </span>
                                                                    @if(!empty($fuelType))
                                                                    <div>{{$fuelType['name']}}</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Registation year </span>
                                                                    <div>{{ $vehicle_details->registration_year }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Insurance Expiry date </span>
                                                                    <div>{{ date("d M Y", strtotime($vehicle_details->insurance_exp_date)) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Permit Expiry date </span>
                                                                    <div>{{ date("d M Y", strtotime($vehicle_details->permit_exp_date)) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Fitness Expiry date </span>
                                                                    <div>{{ date("d M Y", strtotime($vehicle_details->fitness_exp_date)) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>PUC Expiry date </span>
                                                                    <div>{{ date("d M Y", strtotime($vehicle_details->puc_exp_date))}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-10">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Agreement Expiry date </span>
                                                                    <div></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-12 col-md-12 text-center">
                                                &nbsp;
                                            </div>

                                            <div class="row row-sm">

                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Insurance</h6>
                                                    <div class="image-div">
                                                        @if(!empty($vehicle_details->insurance_doc_url))
                                                        <?php
                                                        $Infos = pathinfo($vehicle_details->insurance_doc_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$vehicle_details->insurance_doc_url}}" onclick="getImage('{{$vehicle_details->insurance_doc_url}}','Insurance')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$vehicle_details->insurance_doc_url}}','Insurance')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif

                                                    </div>
                                                    <span class="text-success" id="insurance_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="insurance_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $vehicle_details->insurance_doc_url_status == 2)
                                                    @if(!empty($vehicle_details->insurance_doc_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 insurance_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','insurance_doc_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 insurance_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','insurance_doc_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $vehicle_details->insurance_doc_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$vehicle_details->id}}','insurance_doc_url_status','insurance_doc_url','driver_cum_owner')">Change Image</button>
                                                </div>



                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Permit</h6>
                                                    <div class="image-div">
                                                        @if(!empty($vehicle_details->permit_doc_url))
                                                        <?php
                                                        $Infos = pathinfo($vehicle_details->permit_doc_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$vehicle_details->permit_doc_url}}" onclick="getImage('{{$vehicle_details->permit_doc_url}}','Permit')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$vehicle_details->permit_doc_url}}','Permit')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="permit_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="permit_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $vehicle_details->permit_doc_url_status == 2)
                                                    @if(!empty($vehicle_details->permit_doc_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 permit_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','permit_doc_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 permit_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','permit_doc_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $vehicle_details->permit_doc_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$vehicle_details->id}}','permit_doc_url_status','permit_doc_url','driver_cum_owner')">Change Image</button>
                                                </div>



                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Fitness</h6>
                                                    <div class="image-div">
                                                        @if(!empty($vehicle_details->fitness_doc_url))
                                                        <?php
                                                        $Infos = pathinfo($vehicle_details->fitness_doc_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$vehicle_details->fitness_doc_url}}" onclick="getImage('{{$vehicle_details->fitness_doc_url}}','Fitness')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$vehicle_details->fitness_doc_url}}','Fitness')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="fitness_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="fitness_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $vehicle_details->fitness_doc_url_status == 2)
                                                    @if(!empty($vehicle_details->fitness_doc_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 fitness_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','fitness_doc_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 fitness_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','fitness_doc_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $vehicle_details->fitness_doc_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$vehicle_details->id}}','fitness_doc_url_status','fitness_doc_url','driver_cum_owner')">Change Image</button>
                                                </div>


                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">PUC</h6>
                                                    <div class="image-div">
                                                        @if(!empty($vehicle_details->puc_doc_url))
                                                        <?php
                                                        $Infos = pathinfo($vehicle_details->puc_doc_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$vehicle_details->puc_doc_url}}" onclick="getImage('{{$vehicle_details->puc_doc_url}}','PUC')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$vehicle_details->puc_doc_url}}','PUC')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="puc_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="puc_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $vehicle_details->puc_doc_url_status == 2)
                                                    @if(!empty($vehicle_details->puc_doc_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 puc_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','puc_doc_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 puc_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->id}}','puc_doc_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $vehicle_details->puc_doc_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif
                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$vehicle_details->id}}','puc_doc_url_status','puc_doc_url','driver_cum_owner')">Change Image</button>
                                                </div>

                                                <div class="col-12 col-md-12 text-center">
                                                    &nbsp;
                                                </div>

                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Agreement</h6>
                                                    <div class="image-div">
                                                        @if(!empty($vehicle_details->agreement_doc_url))
                                                        <?php
                                                        $Infos = pathinfo($vehicle_details->agreement_doc_url);
                                                        $extension = $Infos['extension'];
                                                        if (strtolower($extension) != 'pdf') {
                                                        ?>
                                                            <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$vehicle_details->agreement_doc_url}}" onclick="getImage('{{$vehicle_details->agreement_doc_url}}','Agreement')">
                                                        <?php } else { ?>
                                                            <img style="cursor:pointer;" onclick="getPdf('{{$vehicle_details->agreement_doc_url}}','Agreement')" src="{{ env('APP_URL')}}/public/pdf.png" width="75%">
                                                        <?php } ?>
                                                        @else
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ env('APP_URL')}}/public/noimage.png">
                                                        @endif
                                                    </div>
                                                    <span class="text-success" id="agreement_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="agreement_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $vehicle_details->agreement_doc_url_status == 2)
                                                    @if(!empty($vehicle_details->agreement_doc_url))
                                                    <button type="button" class="btn ripple btn-success btn-sm mt-2 agreement_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->vehicle_id}}','agreement_doc_url_status','1')">Approve</button>
                                                    <button type="button" class="btn ripple btn-danger btn-sm mt-2 agreement_doc_url_status_btn" onclick="documentVerify('vehicles','{{$vehicle_details->vehicle_id}}','agreement_doc_url_status','0')">Reject</button>
                                                    @endif
                                                    @else
                                                    @if( $vehicle_details->agreement_doc_url_status == 1)
                                                    <span class="text-success">Approved</span>
                                                    @else
                                                    <span class="text-danger">Rejected</span>
                                                    @endif
                                                    @endif
                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$vehicle_details->vehicle_id}}','agreement_doc_url_status','agreement_doc_url','driver_cum_owner')">Change Image</button>
                                                </div>

                                            </div>

                                        </div>

                                    </div> <!-- connected cab doc -->
                                    @endif
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
                            <div>
                                <h6 class="main-content-label mb-1">Vehicles</h6>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="vehicles" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('vehicles.vehicle_number') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.brand_name') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.model_name') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.vehicle_type_name') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.vehicle_fuel_type_name') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.registration_year') }}</b></th>
                                                        <th><b>{{ @trans('vehicles.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($vehicles as $v) { ?>
                                                        <tr>
                                                            <td><?= $i; ?></td>
                                                            <td>{{$v['vehicle_number']}}</td>
                                                            <td>{{$v['brand_name']}}</td>
                                                            <td>{{$v['model_name']}}</td>
                                                            <td>{{$v['vehicle_type_name']}}</td>
                                                            <td>{{$v['vehicle_fuel_type_name']}}</td>
                                                            <td>{{$v['registration_year']}}</td>
                                                            <td class="act-btn">
                                                                <a href="<?= url(Session::get('panel') . '/vehicles/show'); ?>/<?= $v['id']; ?>" title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                            </td>
                                                            <!-- <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/userType/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                            </a>       
                                                            <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                            </a>                 
                                                        </td> -->
                                                        </tr>
                                                    <?php $i++;
                                                    } ?>
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
                                            <table id="tripdetails" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
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
                                                    foreach ($trip as $c) { ?>
                                                        <tr>
                                                            <td><?= $i; ?></td>
                                                            <td>{{$c['trip_type']}}</td>
                                                            <td> - </td>
                                                            <td>{{$c['trip_type']}}</td>
                                                            <td>{{$c['vehicle_type']}}</td>
                                                            <td>{{$c['vehicle_number']}}</td>
                                                            <td>{{$c['booking_datetime']}}</td>
                                                            <td>{{$c['trip_start_datetime']}}</td>
                                                            <td></td>
                                                            <td>{{$c['trip_end_datetime']}}</td>
                                                            <td>{{$c['drop_location']}}</td>
                                                            <td></td>
                                                            <td class="act-btn">
                                                                <a href="<?= url(Session::get('panel') . '/cabs/show'); ?>/<?= $c['id']; ?>" title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                            </td>
                                                            <!-- <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/userType/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                            </a>       
                                                            <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                            </a>                 
                                                        </td> -->
                                                        </tr>
                                                    <?php $i++;
                                                    } ?>
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
            <div class="row row-sm" style="display:none;">
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
                                            <table id="cabs" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
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
                                                    foreach ($cabs as $c) { ?>
                                                        <tr>
                                                            <td><?= $i; ?></td>
                                                            <td>{{$c['cab_post_type']}}</td>
                                                            <td>{{$c['start_date']}}</td>
                                                            <td>{{$c['start_time']}}</td>

                                                            <td><span data-placement="bottom" data-toggle="tooltip" title="{{$c['start_location']}}"><?= $c['start_location']; ?></span></td>

                                                            <td><span data-placement="bottom" data-toggle="tooltip" title="{{$c['end_location']}}"><?= $c['end_location']; ?></span></td>
                                                            <td>{{$c['vehicle_number']}}</td>

                                                            <td class="act-btn">
                                                                <a href="<?= url(Session::get('panel') . '/cabs/show'); ?>/<?= $c['id']; ?>" title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                            </td>
                                                            <!-- <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/userType/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                            </a>       
                                                            <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                            </a>                 
                                                        </td> -->
                                                        </tr>
                                                    <?php $i++;
                                                    } ?>
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

            <!-- Driver Details -->
            @if($user->user_type_id == '3')
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                <h6 class="main-content-label mb-1">Drivers</h6>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="driverdetails" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('user.name') }}</b></th>
                                                        <th><b>{{ @trans('user.mobile_number') }}</b></th>
                                                        <th><b>{{ @trans('user.adhar_card_no') }}</b></th>
                                                        <th><b>{{ @trans('user.driving_licence_no') }}</b></th>
                                                        <th><b>{{ @trans('user.expiry_date') }}</b></th>
                                                        <th><b>{{ @trans('user.street_address') }}</b></th>
                                                        <th><b>{{ @trans('user.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $i = 1;
                                                    foreach ($drivers as $d) { ?>
                                                        <tr>
                                                            <td><?= $i; ?></td>
                                                            <td>{{$d['first_name']." ".$d['last_name']}}</td>
                                                            <td>{{$d['mobile_number']}}</td>
                                                            <td>{{$d['adhar_card_no']}}</td>
                                                            <td>{{$d['driving_licence_no']}}</td>
                                                            <td>{{$d['driving_licence_expiry_date']}}</td>
                                                            <td>{{$d['street_address']}}</td>
                                                            <td class="act-btn">
                                                                <a href="<?= url(Session::get('panel') . '/drivers/show'); ?>/<?= $d['id']; ?>" title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                            </td>
                                                            <!-- <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/userType/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                            </a>       
                                                            <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                            </a>                 
                                                        </td> -->
                                                        </tr>
                                                    <?php $i++;
                                                    } ?>
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
                                            <table id="referraldetails" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
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
                                                    foreach ($referrals as $r) { ?>
                                                        <tr>
                                                            <td><?= $i; ?></td>
                                                            <td>{{$r['first_name'].' '.$r['last_name']}}</td>
                                                            <td>{{$r['mobile_number']}}</td>
                                                            <td>{{$r['user_type_name']}}</td>
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
                                                    <?php $i++;
                                                    } ?>
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
                                                        <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                            <div class="card-body text-center">
                                                                <h6 class="mb-0">Today's</h6>
                                                                <h2 class="mb-1 mt-2 number-font">
                                                                    <span class="counter">000</span>
                                                                </h2>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                            <div class="card-body text-center">
                                                                <h6 class="mb-0">Yesterdays's</h6>
                                                                <h2 class="mb-1 mt-2 number-font">
                                                                    <span class="counter">000</span>
                                                                </h2>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                            <div class="card-body text-center">
                                                                <h6 class="mb-0">Weekly</h6>
                                                                <h2 class="mb-1 mt-2 number-font">
                                                                    <span class="counter">000</span>
                                                                </h2>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
                                                            <div class="card-body text-center">
                                                                <h6 class="mb-0">This</h6>
                                                                <h2 class="mb-1 mt-2 number-font">
                                                                    <span class="counter">000</span>
                                                                </h2>
                                                            </div>
                                                        </div>
                                                        <div class="col-xl-2 col-lg-6 col-sm-6 pe-0 ps-0 border-end">
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
        </div>
    </div>
</div>
<!-- End Main Content-->
@endsection

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
                <form method="POST" id="frmAddEditUser" action="{{ route('admin.users.changesStatus',['panel' => Session::get('panel')]) }}">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}" placeholder="Enter Name" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.email') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control" value="{{$user->email}}" placeholder="Enter Email" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.phone') }}<span class="required">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{$user->mobile_number}}" placeholder="Enter Mobile Number" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.dob') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control dob" value="{{$user->dob}}" placeholder="Enter Date Of Birth" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">User Status</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio3" name="user_status" value="3" @if($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio" name="user_status" value="1" @if($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1" name="user_status" value="0" @if($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2" name="user_status" value="2" @if($user->user_status == '2') checked @endif>
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
                <button type="button" class="close" data-dismiss="modal" onclick="closeImgModel()">&times;</button>
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
                <form method="POST" id="frmAddEditUser" action="{{ route('admin.users.changeImage',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">
                    <input type="hidden" name="hidd_type" id="hidd_type" value="">
                    <input type="hidden" name="pk_id" id="pk_id" value="">
                    <input type="hidden" name="pk_key" id="pk_key" value="">
                    <input type="hidden" name="image_key_val" id="image_key_val" value="">
                    <input type="hidden" name="module_name" id="module_name" value="">
                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                        <div class="mg-b-10" id="fnWrapper">
                            <input type="file" name="police_verification" id="image_key" class="dropify driver" data-height="200" />
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
<div class="modal" tabindex="-1" role="dialog" id="modalDeleteConfirm">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
              <form action="{{ route('admin.users.reset',['panel' => Session::get('panel')]) }}" method="post">
                    @csrf
                    <input type="hidden" name="user_reset_id" id="user_id" value="{{ $user->id }}">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ @trans('delete_modal.confirm') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                            @if(Route::currentRouteName() == 'admin.jobs.show')
                            <p>{{ @trans('delete_modal.delete_applicant') }}</p>
                            @else
                            <p>Are you sure you want to Reset Profile?</p>
                            @endif
                    </div>
                    <div class="modal-footer d-flex justify-content-between">
                        <button type="button" class="btn btn-cncl" data-dismiss="modal">{{ @trans('delete_modal.cancel') }}</button>
                        @if(Route::currentRouteName() == 'admin.jobs.show')
                        <button type="submit" class="btn btn-danger" @click="destroy">{{ @trans('delete_modal.confirm') }}</button>
                        @else
                        <button type="submit" class="btn btn-danger" @click="destroy">Reset Profile</button>
                        @endif
                    </div>
              </form>
        </div>
    </div>
</div>
@push('pageJs')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom/dist/panzoom.min.js"></script>

<script type="text/javascript">
    function confirmModal(){
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

    @if(Session::has('message'))
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
        var siteurl = "{{URL::to('/')}}/api/v1/createVcnForWeb";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
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
        var siteurl = "{{url(Session::get('panel').'/users/resetAttempt')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
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
        var siteurl = "{{url(Session::get('panel').'/users/resetOtpAttempt')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
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
        var siteurl = "{{url(Session::get('panel').'/users/changeUssdStatus')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
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
            var siteurl = "{{url(Session::get('panel').'/users/documentVerification')}}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: {
                    "_token": "{{csrf_token()}}",
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
    $('#tripdetails').DataTable({
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
</script>

@endpush