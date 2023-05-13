@extends('admin.layouts.main')

@section('title')
Customer Details
@endsection

@section('content')

@php
    use App\Models\TripPostLocal;
    use App\Models\TripPostRental;
    use App\Models\TripPostOutstations;
@endphp
<style>
    .img_style{
        height: 3rem;
        width: 3rem;
        border-radius: 6rem;
    }
</style>
<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Customer Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.customers.index',['panel' => Session::get('panel')]) }}">Customer</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
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
                                        <label class="main-content-label tx-13 mg-b-20">Customer</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            @if(!empty($user->profile_pic))
                                                                <div class="media">
                                                                    <div class="media-icon bg-primary-transparent text-primary">
                                                                        <img alt="docuemtn image" class="img-thumbnail img_style" src="{{$user->profile_pic}}" onclick="getImage('{{$user->agent_logo}}','Agent Logo')">
                                                                    </div>
                                                                    <div class="media-body"> <span>Customer Name</span>
                                                                        <div>{{$user->first_name.' '.$user->last_name}}</div>
                                                                    </div>
                                                                </div>
                                                            @else
                                                                <div class="media">
                                                                    <div class="media-icon bg-primary-transparent text-primary">
                                                                        <i class="icon ion-pull-request"></i>
                                                                    </div>
                                                                    <div class="media-body"> <span>Customer Name</span>
                                                                        <div>{{$user->first_name.' '.$user->last_name}}</div>
                                                                    </div>
                                                                </div>
                                                            @endif 
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-md-phone-portrait"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.mobile_number') }}</span>
                                                                    <div>{{$user->mobile_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-email sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>Email Address</span>
                                                                    <div>{{$user->email}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-email sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                                    <div>{{$customer_trip}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-money sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.money_spent') }}</span>
                                                                    <div>{{$money_spent}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-rss-alt sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>Refferal Done</span>
                                                                    <div>{{$referrals_done}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-location-pin sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.current_location') }}</span>
                                                                    <div>{{$current_location}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-rss-alt sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.referal_code') }}</span>
                                                                    <div>{{$user->reference_code}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <!-- @if($user->user_type_id == '2' || $user->user_type_id == '3')
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Agent</label>

                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-speakerphone"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.travel_name') }}</span>
                                                                    <div>{{$user->travel_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-pull-request"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.owner_name') }}</span>
                                                                    <div>{{$user->owner_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-radio-waves"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.office_no') }}</span>
                                                                    <div>{{$user->office_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-arrow-down-a"></i> </div>
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
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-help-circled"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.pan_card') }}</span>
                                                                    <div>{{$user->pan_card}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-help-circled"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.adhar_card') }}</span>
                                                                    <div>{{$user->adhar_card}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-stats-bars"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.bank_account_id') }}</span>
                                                                    <div>{{$user->bank_account_id}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-money sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.agent_earnings') }}</span>
                                                                    <div>{{$earnings}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    @endif -->

                                    <!-- @if($user->user_type_id == '4' || $user->user_type_id == '5')
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Driver</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-pull-request"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.driver_mobile_numebr') }}</span>
                                                                    <div>{{$user->driver_mobile_numebr}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-pull-request"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.name') }}</span>
                                                                    <div>{{$user->driver_first_name.' '.$user->driver_last_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-help-circled"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.adhar_card_no') }}</span>
                                                                    <div>{{$user->adhar_card_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-pricetag"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.driving_licence_no') }}</span>
                                                                    <div>{{$user->driving_licence_no}}</div>
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
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-calendar sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.driving_licence_expiry_date') }}</span>
                                                                    <div>{{$user->driving_licence_expiry_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-location-pin sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.street_address') }}</span>
                                                                    <div>{{$user->street_address}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-car sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                                    <div>{{$driver_trip}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @endif -->

                                    <!-- <?php if(!empty($plan_details)){?> -->
                                    <!-- <div class="border-top"></div>

                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">subscription plan</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-pull-request"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.plan_name') }}</span>
                                                                    <div>{{$plan_details->name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-calendar sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.plan_validity') }}</span>
                                                                    <div>{{$plan_details->plan_validity}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-money sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.price') }}</span>
                                                                    <div>{{$plan_details->price}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-calendar sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.start_date') }}</span>
                                                                    <div>{{ date("d-m-Y H:i:s", strtotime($plan->start_datetime))}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-calendar sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.end_date') }}</span>
                                                                    <div>{{ date("d-m-Y H:i:s", strtotime($plan->end_datetime))}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="ti-money sidemenu-icon"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('user.subscription_status') }}</span>
                                                                    <div>{{$subscription_status}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                                    <!-- <?php } ?> -->


                                    <!-- <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Customer Profile</label>
                                        <div class="d-sm-flex">
                                                                                        
                                            <div class="row row-sm">                                            
                                                @if(!empty($user->profile_pic))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Customer Profile Photo</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->profile_pic}}" onclick="getImage('{{$user->agent_logo}}','Agent Logo')"> -->

                                                    <!-- <span class="text-success" id="logo_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="logo_status_rejected" style="display:none;">Rejected</span> -->

                                                    <!-- @if( $user->agent_logo_status == 2)
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 logo_status_btn" onclick="documentVerify('agent','{{$user->agent_id}}','logo_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 logo_status_btn" onclick="documentVerify('agent','{{$user->agent_id}}','logo_status','0')">Reject</button>
                                                    @else
                                                        @if( $user->agent_logo_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif -->
                                                <!-- </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div> -->

                                    <!-- <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Driver Documents</label>
                                        <div class="d-sm-flex">
                                                                                        
                                            <div class="row row-sm">
                                                @if(!empty($user->d_pan_card_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Pan Card</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->d_pan_card_url}}" onclick="getImage('{{$user->d_pan_card_url}}','Pan Card')">

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
                                                    
                                                </div>
                                                @endif

                                                @if(!empty($user->d_adhar_card_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Adhar Card</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->d_adhar_card_url}}" onclick="getImage('{{$user->d_adhar_card_url}}','Adhar Card')">

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
                                                </div>
                                                @endif

                                                @if(!empty($user->dl_front_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">DL Front</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->dl_front_url}}" onclick="getImage('{{$user->dl_front_url}}','DL Front')">

                                                    <span class="text-success" id="dl_front_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="dl_front_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->dl_front_url_status == 2)
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 dl_front_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','dl_front_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 dl_front_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','dl_front_url_status','0')">Reject</button>
                                                    @else
                                                        @if( $user->dl_front_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                </div>
                                                @endif

                                                @if(!empty($user->dl_back_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">DL Back</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->dl_back_url}}" onclick="getImage('{{$user->dl_back_url}}','DL Back')">

                                                    <span class="text-success" id="dl_back_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="dl_back_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->dl_back_url_status == 2)
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 dl_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','dl_back_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 dl_back_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','dl_back_url_status','0')">Reject</button>
                                                    @else
                                                        @if( $user->dl_back_url_status == 1)
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

                                                @if(!empty($user->police_verification_url))
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Police Verification</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->police_verification_url}}" onclick="getImage('{{$user->police_verification_url}}','Police Verification')">

                                                    <span class="text-success" id="police_verification_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="police_verification_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->police_verification_url_status == 2)
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 police_verification_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','police_verification_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 police_verification_url_status_btn" onclick="documentVerify('driver','{{$user->driver_id}}','police_verification_url_status','0')">Reject</button>
                                                    @else
                                                        @if( $user->police_verification_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                </div>
                                                @endif

                                                @if(($user->user_type_id == '4' || $user->user_type_id == '5') && !empty($user->bank_document_url))
                                                <div class="col-6 col-md-3 text-center" >
                                                    <h6 class="text-center">Bank Cheque Document</h6>
                                                    <img alt="docuemtn image" class="img-thumbnail" src="{{$user->bank_document_url}}" onclick="getImage('{{$user->bank_document_url}}','Bank Document')">

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
                                                </div>
                                                @endif

                                            </div>
                                        </div>
                                    </div> -->
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->

            <!-- Running Trip Details -->
            <!-- <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div> 
                                <h6 class="main-content-label mb-1">Running Trip</h6>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">                                
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example2" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('trip.id') }}</b></th>
                                                        <th><b>{{ @trans('trip.type') }}</b></th>
                                                        <th><b>{{ @trans('trip.cab_type') }}</b></th>
                                                        <th><b>{{ @trans('trip.date') }}</b></th>
                                                        <th><b>{{ @trans('trip.pickup_date_time') }}</b></th>                                                        
                                                        <th><b>{{ @trans('trip.pickup_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.return_date_time') }}</b></th>
                                                        <th><b>{{ @trans('trip.drop_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.status') }}</b></th>
                                                        <th><b>{{ @trans('trip.amount') }}</b></th>
                                                        <th><b>{{ @trans('trip.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($runningTrips))
                                                        @foreach($runningTrips as $key =>  $v)
                                                            <tr>
                                                                @php
                                                                    $dropData = [];
                                                                    if($v['trip_type'] == 'InCity'){
                                                                        $dropData = TripPostLocal::where('trip_post_master_id',$v['trip_id'])->first();
                                                                    }elseif($v['trip_type'] == 'Hourly'){
                                                                        $dropData = TripPostRental::where('trip_post_master_id',$v['trip_id'])->first();
                                                                    }else{
                                                                        $dropData = TripPostOutstations::where('trip_post_master_id',$v['trip_id'])->first();
                                                                    }
                                                                @endphp
                                                                <td>{{ ++$key }}</td>
                                                                <td>{{$v['trip_id']}}</td>
                                                                <td>{{$v['trip_type']}}</td>
                                                                <td>{{$v['cab_type']}}</td>
                                                                <td>{{$v['date']}}</td>
                                                                <td>{{$v['pickup_date']}}</td>
                                                                <td>{{$v['pickup_location']}}</td>
                                                                <td>{{$v['return_date']}}</td>
                                                                @if (isset($dropData) && isset($dropData->drop_location))
                                                                    <td>{{$dropData->drop_location}}</td>
                                                                @else
                                                                    <td>-</td>
                                                                @endif
                                                                <td><button type="button" class="btn ripple btn-warning btn-sm">Started</button></td>
                                                                <td>{{$v['fare']}}</td>
                                                                <td class="act-btn">                                
                                                                    <a href="<?= url(Session::get('panel').'/trip/show'); ?>/<?= $v['trip_id']; ?>" title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                    </a>
                                                                </td>
                                                                <td class="act-btn">                                
                                                                    <a :href="'{{url(Session::get('panel').'/userType/edit')}}/'+ item.id" title="Edit"
                                                                        class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                                    </a>       
                                                                    <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                        class="btn-sm btn-edit"><i class="material-icons">delete</i>
                                                                    </a>                 
                                                                </td>
                                                            </tr>
                                                        @endforeach 
                                                    @else
                                                        <tr class="odd">
                                                            <td valign="top" colspan="12" class="dataTables_empty" style="text-align: center;">No data available in table</td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
            <!-- End -->

            <!-- Completed Trip -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div> 
                                <h6 class="main-content-label mb-1">Completed Trip </h6>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">                                
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example2" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('trip.id') }}</b></th>
                                                        <th><b>{{ @trans('trip.type') }}</b></th>
                                                        <th><b>{{ @trans('trip.cab_type') }}</b></th>
                                                        <th><b>{{ @trans('trip.date') }}</b></th>
                                                        <th><b>{{ @trans('trip.pickup_date_time') }}</b></th>                                                        
                                                        <th><b>{{ @trans('trip.pickup_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.return_date_time') }}</b></th>
                                                        <th><b>{{ @trans('trip.drop_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.status') }}</b></th>
                                                        <th><b>{{ @trans('trip.amount') }}</b></th>
                                                        <th><b>{{ @trans('trip.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if (!empty($completedTrips))
                                                        @foreach($completedTrips as $key => $d)
                                                        <tr>
                                                            @php
                                                                $dropData = [];
                                                                if($d['trip_type'] == 'InCity'){
                                                                    $dropData = TripPostLocal::where('trip_post_master_id',$d['trip_id'])->first();
                                                                }elseif($d['trip_type'] == 'Hourly'){
                                                                    $dropData = TripPostRental::where('trip_post_master_id',$d['trip_id'])->first();
                                                                }else{
                                                                    $dropData = TripPostOutstations::where('trip_post_master_id',$d['trip_id'])->first();
                                                                }
                                                            @endphp
                                                            <td>{{ ++$key }}</td>
                                                            <td>{{$d['trip_id']}}</td>
                                                            <td>{{$d['trip_type']}}</td>
                                                            <td>{{$d['cab_type']}}</td>
                                                            <td>{{$d['date']}}</td>
                                                            <td>{{$d['pickup_date']}}</td>
                                                            <td>{{$d['pickup_location']}}</td>
                                                            <td>{{$d['return_date']}}</td>
                                                            @if (isset($dropData) && isset($dropData->drop_location))
                                                                <td>{{$dropData->drop_location}}</td>
                                                            @else
                                                                <td>-</td>
                                                            @endif  
                                                            @if ($d->trip_status == 2)
                                                                <td><button type="button" class="btn ripple btn-success btn-sm">Completed</button></td>
                                                            @else
                                                                <td><button type="button" class="btn ripple btn-info btn-sm">On Going</button></td>
                                                            @endif
                                                            <td>{{$d['fare']}}</td>
                                                            <td class="act-btn">                                
                                                                <a href="<?= url(Session::get('panel').'/trip/show'); ?>/<?= $d['trip_id']; ?>" title="View" class="btn-sm btn-view">
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
                                                        @endforeach
                                                    @else
                                                        <tr class="odd">
                                                            <td valign="top" colspan="12" class="dataTables_empty" style="text-align: center;">No data available in table</td>
                                                        </tr>
                                                    @endif
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

            <!-- Cancel Trip/Upcoming Details -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div> 
                                <h6 class="main-content-label mb-1">Cancel Trip</h6>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">                                
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example2" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer" width="100%">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('trip.id') }}</b></th>
                                                        <th><b>{{ @trans('trip.type') }}</b></th>
                                                        <th><b>{{ @trans('trip.cab_type') }}</b></th>
                                                        <th><b>{{ @trans('trip.date') }}</b></th>
                                                        <th><b>{{ @trans('trip.pickup_date_time') }}</b></th>                                                        
                                                        <th><b>{{ @trans('trip.pickup_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.return_date_time') }}</b></th>
                                                        <th><b>{{ @trans('trip.drop_location') }}</b></th>
                                                        <th><b>{{ @trans('trip.status') }}</b></th>
                                                        <th><b>{{ @trans('trip.amount') }}</b></th>
                                                        <th><b>{{ @trans('trip.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($trips as $key => $v)
                                                        <tr>
                                                            @php
                                                                $dropData = [];
                                                                if($v['trip_type'] == 'InCity'){
                                                                    $dropData = TripPostLocal::where('trip_post_master_id',$v['trip_id'])->first();
                                                                }elseif($v['trip_type'] == 'Hourly'){
                                                                    $dropData = TripPostRental::where('trip_post_master_id',$v['trip_id'])->first();
                                                                }else{
                                                                    $dropData = TripPostOutstations::where('trip_post_master_id',$v['trip_id'])->first();
                                                                }
                                                            @endphp
                                                            <td>{{ ++$key }}</td>
                                                            <td>{{$v['trip_id']}}</td>
                                                            <td>{{$v['trip_type']}}</td>
                                                            <td>{{$v['cab_type']}}</td>
                                                            <td>{{$v['date']}}</td>
                                                            <td>{{$v['pickup_date']}}</td>
                                                            <td>{{$v['pickup_location']}}</td>
                                                            <td>{{$v['return_date']}}</td>
                                                            @if (isset($dropData) && isset($dropData->drop_location))
                                                                <td>{{$dropData->drop_location}}</td>
                                                            @else
                                                                <td>-</td>
                                                            @endif
                                                            
                                                            <td><button type="button" class="btn ripple btn-danger btn-sm">Canceled</button></td>
                                                            <td>{{$v['fare']}}</td>
                                                            <td class="act-btn">                                
                                                                <a href="<?= url(Session::get('panel').'/trip/show'); ?>/<?= $v['trip_id']; ?>" title="View" class="btn-sm btn-view">
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
                                                    @endforeach
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
                <h4 class="modal-title regular body-font-size">Edit Customer Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" id="frmAddEditUser" action="{{ route('admin.customers.changesStatus',['panel' => Session::get('panel')]) }}">
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
                    
                    <hr/>
                    
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
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0 pb-0">
                <h4 class="modal-title regular body-font-size img-title">Image View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <img id="image-gallery-image" src="">
            </div>
        </div>
    </div>
</div>
@push('pageJs')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.css') }}">

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>

<script src="{{ asset('assets/plugins/datatable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/plugins/datatable/dataTables.bootstrap4.min.js') }}"></script>

<script type="text/javascript">
function getImage(imageName,title){    
    $('#image-gallery-image').attr('src', imageName);
    $(".img-title").text(title);
    $("#attachModal").modal();
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
            if(errorcode == 1 && success == true){
                var masked_card = dataobject.masked_card;
                $('#card_number').text(masked_card);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: message,
                    actionText: 'Okay'
                });
            }else{
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
            if(response == 'success'){
                $('#reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
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
            if(response == 'success'){
                $('#otp_reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
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
            "ussd_enable":ussd_enable
        },
        success: function(response) {
            if(response == 'success'){

                if(ussd_enable == 1){
                    $('#ussd_enable_lable').text('On');
                    $('#ussd_enable').val('0');
                    $('.ussd-enable').text('Disable');        
                }else{
                    $('#ussd_enable_lable').text('Off');
                    $('#ussd_enable').val('1'); 
                    $('.ussd-enable').text('Enable');  
                }

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'USSD status change successfully.',
                    actionText: 'Okay'
                });
            }else{
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

function documentVerify(type,id,key,status){
    var siteurl = "{{url(Session::get('panel').'/users/documentVerification')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "id": id,
            "type":type,
            "key":key,
            "status": status
        },
        success: function(response) {
            if(response == 'success'){

                if(status == '1'){
                    $('#'+key+'_approved').css('display','');
                }else{
                    $('#'+key+'_rejected').css('display','');
                }
                $('.'+key+'_btn').css('display','none');

                if(status == '1'){
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Document verify successfully.',
                        actionText: 'Okay'
                    });
                }else{
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Document Rejected!',
                        actionText: 'Okay'
                    });
                }
            }else{
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

$('#example2').DataTable({
    language: {
        searchPlaceholder: 'Search...',
        sSearch: '',
        lengthMenu: '_MENU_ Record Per Page',
    }
});
</script>
@endpush