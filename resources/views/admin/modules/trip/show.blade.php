@extends('admin.layouts.main')

@section('title')
Trip Detail
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Trip Detail</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a
                                href="{{ route('admin.trip.index',['panel' => Session::get('panel')]) }}">Trip</a></li>
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
                                    <div class="p-4">
                                        <h4 class="main-content-label tx-13 mg-b-20">Master Details</h4>
                                        <hr>
                                        
                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.trip_type') }}</span>
                                                                    <div> {{$user->trip_type}}</div>
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
                                                                    <span>{{ @trans('trip.pickup_location') }}</span>
                                                                    <div> {{$user->pickup_location}}</div>
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
                                                                    <span>Drop Location</span>
                                                                    <div> {{$user->drop_location}}</div>
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
                                                                    <span>{{ @trans('trip.pickup_date_time') }}</span>
                                                                    <div>
                                                                        {{ date("d M Y H:i:s", strtotime($user->pickup_date." ".$user->pickup_time))}}
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
                                                                    <span>{{ @trans('trip.fare') }}</span>
                                                                    <div> {{$user->fare}}</div>
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
                                                                    <span>{{ @trans('trip.commission_price') }}</span>
                                                                    <div> {{$user->commission_price}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Status</span>
                                                                    @if($user->trip_status == '2')
                                                                        <div>Completed</div>
                                                                    @elseif($user->trip_status == '0')
                                                                        <div>Booked</div>
                                                                    @elseif($user->trip_status == '3')
                                                                        <div>Canceled</div>
                                                                    @elseif($user->trip_status == '1')
                                                                        <div>Started</div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @if($user->trip_type == 'Outstation Trip' && !empty($trip_post_outstation))
                                    <div class="p-4">

                                        <h4 class="main-content-label tx-13 mg-b-20">Outstation Trip Details</h4>
                                        <hr>

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.return_date_time') }}</span>
                                                                    <div>
                                                                        {{ date("d M Y H:i:s", strtotime($trip_post_outstation->return_date.' '.$trip_post_outstation->return_time)) }}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.is_fixed_price') }}</span>
                                                                    <div> {{$trip_post_outstation->is_fixed_price}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.fare_per_km') }}</span>
                                                                    <div> {{$trip_post_outstation->fare_per_km}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.is_fixed_price') }}</span>
                                                                    <div> {{$trip_post_outstation->is_fixed_price}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.commission_per_km') }}</span>
                                                                    <div> {{$trip_post_outstation->commission_per_km}}
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

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.is_tax_included') }}</span>
                                                                    <div> {{$trip_post_outstation->is_tax_included}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.driver_allowance_included') }}</span>
                                                                    <div>
                                                                        {{$trip_post_outstation->driver_allowance_included}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.full_allowance') }}</span>
                                                                    <div> {{$trip_post_outstation->full_allowance}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.day_allowance') }}</span>
                                                                    <div> {{$trip_post_outstation->day_allowance}}</div>
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

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.night_allowance') }}</span>
                                                                    <div> {{$trip_post_outstation->night_allowance}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.extra_km_fare') }}</span>
                                                                    <div> {{$trip_post_outstation->extra_km_fare}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.extra_time_fare') }}</span>
                                                                    <div> {{$trip_post_outstation->extra_time_fare}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Destination
                                                                        Location</span>
                                                                    <div> {{$trip_post_outstation->drop_location}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    @endif

                                    @if($user->trip_type == 'Hourly' && !empty($trip_post_rental))
                                    <div class="p-4">

                                        <h4 class="main-content-label tx-13 mg-b-20">Hourly Details</h4>
                                        <hr>

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.time_duration') }}</span>
                                                                    <div> {{$trip_post_rental->time_duration}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.km_limit') }}</span>
                                                                    <div> {{$trip_post_rental->km_limit}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.has_addons') }}</span>
                                                                    <div> {{$trip_post_rental->has_addons}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.extra_km_fare') }}</span>
                                                                    <div> {{$trip_post_rental->extra_km_fare}}</div>
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

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.extra_time_fare') }}</span>
                                                                    <div> {{$trip_post_rental->extra_time_fare}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Destination
                                                                        Location</span>
                                                                    <div> {{$trip_post_rental->drop_location}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    @endif

                                    @if($user->trip_type == 'InCity' && !empty($trip_post_local))
                                    <div class="p-4">

                                        <h4 class="main-content-label tx-13 mg-b-20">InCity Details</h4>
                                        <hr>

                                        <div class="m-t-30 d-sm-flex">

                                            <div class="mg-sm-r-20 mg-b-10">
                                                <div class="main-profile-contact-list">
                                                    <div class="media">
                                                        <div class="media-body"> <span>Destination Location</span>
                                                            <div> {{$trip_post_local->drop_location}}</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    @endif

                                    <div class="border-top"></div>

                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Contact</label>
                                        @if(!isset($user->trip_owner_name))
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.name') }}</span>
                                                                    <div> {{$user->first_name . ' ' . $user->last_name}}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.mobile_number') }}</span>
                                                                    <div> {{$user->mobile_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Email</span>
                                                                    <div> {{$user->emailid}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Gender</span>
                                                                    <div> {{$user->gender}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        @else
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.name') }}</span>
                                                                    <div> {{$user->trip_owner_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>{{ @trans('trip.mobile_number') }}</span>
                                                                    <div> {{$user->trip_owner_mobile_no}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Email</span>
                                                                    <div> {{$user->emailid}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->
                                                <!-- <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Gender</span>
                                                                    <div> {{$user->gender}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> -->

                                            </div>
                                        </div>
                                        @endif

                                    </div>

                                    @if($user->trip_status == '0' || $user->trip_status == '2')
                                    <div class="border-top"></div>

                                    <div class="p-4">
                                        <h4 class="main-content-label tx-13 mg-b-20">Cab Details</h4>
                                        <hr>
                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="table-responsive table-data">
                                                    <div class="col-sm-12">
                                                        <div class="table-checkable">
                                                            <table id="example2"
                                                                class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                                                width="100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Cab Post Type
                                                                        </td>
                                                                        <td width="30%">{{$user->cab_post_type}}</td>

                                                                        <td class="text-bold" width="20%">Available For
                                                                        </td>
                                                                        <td width="30%">{{$user->available_for}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Start Location
                                                                        </td>
                                                                        <td width="30%">{{$user->start_location}}</td>

                                                                        <td class="text-bold" width="20%">End Location
                                                                        </td>
                                                                        <td width="30%">{{$user->end_location}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Start Date
                                                                        </td>
                                                                        <td width="30%">
                                                                            {{ date("d M Y", strtotime($user->start_date))}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Start Time
                                                                        </td>
                                                                        <td width="30%">{{$user->start_time}}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>

                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Cab associated vehicle</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Vehicle Number</span>
                                                                    <div> {{$user->vehicle_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Vehicle Owner Name</span>
                                                                    <div> {{$user->owner_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Vehicle City</span>
                                                                    <div>{{$user->city}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Vehicle State</span>
                                                                    <div> {{$user->state}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>

                                    @if(!empty($customers))
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Customer Details</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Customer Name</span>
                                                                    <div> {{$customers['name']}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Customer Mobile Number</span>
                                                                    <div> {{$customers['mobile_number']}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Customer Address 1</span>
                                                                    <div> {{$customers['address_line1']}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body">
                                                                    <span>Customers Address 2</span>
                                                                    <div> {{$customers['address_line2']}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>
                                    @endif

                                    <div class="p-4">
                                        <!-- <label class="main-content-label tx-13 mg-b-20">Trip Bookings</label> -->
                                        <h4 class="main-content-label tx-13 mg-b-20">Trip Bookings</h4>
                                        <hr>

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">

                                                <div class="table-responsive table-data">
                                                    <div class="col-sm-12">
                                                        <div class="table-checkable">
                                                            <table id="example2"
                                                                class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                                                width="100%">
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Booking
                                                                            Datetime</td>
                                                                        <td width="30%">
                                                                            {{ date("d M Y H:i:s", strtotime($user->booking_datetime))}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Start Trip KMS
                                                                            Reading</td>
                                                                        <td width="30%">
                                                                            {{ $user->strat_trip_kms_reading }}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">End Trip KMS
                                                                            Reading</td>
                                                                        <td width="30%">
                                                                            {{ $user->end_trip_kms_reading }}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Fare</td>
                                                                        <td width="30%">{{$user->booking_fare}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Commission Price</td>
                                                                        <td width="30%">
                                                                            {{$user->booking_commission_price}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Booking Fare
                                                                            Per KM</td>
                                                                        <td width="30%">{{$user->booking_fare_per_km}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Booking
                                                                            Commission Per KM</td>
                                                                        <td width="30%">
                                                                            {{$user->booking_commission_per_km}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Trip Start
                                                                            Datetime </td>
                                                                        <td width="30%">
                                                                            {{ date("d M Y H:i:s", strtotime($user->trip_start_datetime))}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Trip End
                                                                            Datetime </td>
                                                                        <td width="30%">
                                                                            {{ date("d M Y H:i:s", strtotime($user->trip_end_datetime))}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Trip Status
                                                                        </td>
                                                                        <td width="30%">
                                                                            @if($user->trip_status == '2')
                                                                            <button type="button"
                                                                                class="ripple btn-success btn-sm">Completed</button>
                                                                            @elseif($user->trip_status == '0')
                                                                            <button type="button"
                                                                                class="ripple btn-dark btn-sm">Booked</button>
                                                                            @else
                                                                            <button type="button"
                                                                                class="ripple btn-warning btn-sm">Started</button>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Payment Type
                                                                        </td>
                                                                        <td width="30%">
                                                                            @if($user->payment_type == '1')
                                                                            <button type="button"
                                                                                class="ripple btn-success btn-sm">Pro</button>
                                                                            @elseif($user->payment_type == '2')
                                                                            <button type="button"
                                                                                class="ripple btn-dark btn-sm">Post</button>
                                                                            @else
                                                                            <button type="button"
                                                                                class="ripple btn-warning btn-sm">Part</button>
                                                                            @endif
                                                                        </td>

                                                                        <td class="text-bold" width="20%">Advance Paid
                                                                            Amount</td>
                                                                        <td width="30%">{{$user->advance_paid_amount}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Payment Status
                                                                        </td>
                                                                        <td width="30%">{{$user->payment_status}}</td>

                                                                        <td class="text-bold" width="20%">Driver
                                                                            Allowance</td>
                                                                        <td width="30%">{{$user->driver_allowance}}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">End Trip
                                                                            Odometer Image</td>
                                                                        <td width="30%">
                                                                            {{$user->end_trip_odometer_image}}
                                                                        </td>

                                                                        <td class="text-bold" width="20%">End Trip
                                                                            Odometer Image</td>
                                                                        <td width="30%">
                                                                            {{$user->end_trip_odometer_image}}
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-bold" width="20%">Notes</td>
                                                                        <td width="30%">{{$user->note}}</td>

                                                                        <td class="text-bold" width="20%"></td>
                                                                        <td width="30%"></td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    @endif
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
                <form method="POST" id="frmAddEditUser"
                    action="{{ route('admin.users.changesStatus',['panel' => Session::get('panel')]) }}">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}"
                            placeholder="Enter Name" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.email') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control" value="{{$user->email}}"
                            placeholder="Enter Email" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.phone') }}<span class="required">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control"
                            value="{{$user->mobile_number}}" placeholder="Enter Mobile Number" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.dob') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control dob" value="{{$user->dob}}"
                            placeholder="Enter Date Of Birth" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">User Status</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio3" name="user_status"
                                value="3" @if($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio" name="user_status"
                                value="1" @if($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1" name="user_status"
                                value="0" @if($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2" name="user_status"
                                value="2" @if($user->user_status == '2') checked @endif>
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
<div class="modal fade" id="attachModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Image View</h4>
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

<link rel="stylesheet" type="text/css"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js">
</script>
<script type="text/javascript">
function getImage(imageName) {
    $('#image-gallery-image').attr('src', imageName);
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
</script>
@endpush