@extends('admin.layouts.main')

@section('title')
    User Details
@endsection

@section('content')
    <style>
        .edit_submit {
            display: none;
        }

        .driver_edit {
            display: none;
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
    <!-- Main Content-->
    <div class="main-content side-content pt-0">

        <div class="container-fluid">
            <div class="inner-body">

                <!-- Page Header -->
                {{-- <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">User Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.users.index',['panel' => Session::get('panel')]) }}">User</a></li>
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
                @if (!empty($last_updated_user_details))
                <div>
                    <h4 class="mg-b-5"><span style="color: #686868;">Last Updated By:</span> {{$last_updated_user_details->first_name}} {{$last_updated_user_details->last_name}}</h4>
                </div>
                @endif
            </div> --}}
                <div class="row" style="padding: 19px;">
                    <div class="col">
                        <h2 class="main-content-title tx-24 mg-b-5">User Details</h2>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a
                                    href="{{ route('admin.users.index', ['panel' => Session::get('panel')]) }}">User</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Details</li>
                        </ol>
                    </div>
                    <div class="col">
                        <!-- <form action="{{ route('admin.users.reset', ['panel' => Session::get('panel')]) }}" method="post">
                                    @csrf -->
                        <input type="hidden" name="user_reset_id" id="user_reset_id" value="{{ $user->id }}">
                        <button class="btn btn-primary" onclick="confirmModal()">Reset Profile</button>
                        <!-- </form> -->
                    </div>
                    @if (!empty($last_updated_user_details))
                        <div>
                            <h4 class="mg-b-5"><span style="color: #686868;">Last Updated By:</span>
                                {{ $last_updated_user_details->first_name }} {{ $last_updated_user_details->last_name }}
                            </h4>
                        </div>
                    @endif
                    <div class="col" style="text-align: center;">
                        <button type="button" class="btn btn-primary" onclick="HistoryShow()">Comment & History</button>
                    </div>
                </div>
                <!-- End Page Header -->
                <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                <input type="hidden" name="user_data" id="user_data" value="{{ $user }}">
                @php
                    // dd($user);
                @endphp
                @if (!empty($driver_details))
                    <input type="hidden" name="drivers_id" id="drivers_id" value="{{ $driver_details->id }}">
                @endif
                <!-- Row -->
                <div class="row row-sm">
                    <div class="col-lg-12 col-md-12">
                        <div class="card custom-card main-content-body-profile">
                            <div class="tab-content">

                                <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                                    <div class="" style="display: flex;">
                                        <div class="card-body p-0 border p-0 rounded-10">

                                            <div class="p-4">
                                                <label class="main-content-label tx-13 mg-b-20">User</label>
                                                <div class="d-flex-end float-right">
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
                                                                                class="form-control user_edit_detail"
                                                                                name="" id="user_name"
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
                                                                            <span>{{ @trans('user.mobile_number') }}</span>
                                                                            <div class="">{{ $user->mobile_number }}
                                                                            </div>
                                                                            {{-- <input type="hidden" class="form-control user_edit_detail mobile_number" name="mobile_number" id="user_mobile_number" value="{{ $user->mobile_number }}"> --}}

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="mob_err" style="color: red;"></div>
                                                        </div>

                                                        <div class="col-md-4 pl-0">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div class="media-body">
                                                                            <span>{{ @trans('user.email') }}</span>
                                                                            <div class="">{{ $user->emailid }}</div>
                                                                            {{-- <input type="hidden" class="form-control user_edit_detail" name="" id="emailid" value="{{ $user->emailid }}"> --}}

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="result"></div>
                                                        </div>

                                                        <!-- <div class="col-md-3 pl-0">
                                                                <div class="mg-sm-r-40 mg-b-10">
                                                                    <div class="main-profile-contact-list">
                                                                        <div class="media">
                                                                            <div class="media-body"> <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                                                <div>{{ $customer_trip }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->
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
                                                                        <div class="media-body">
                                                                            <span>{{ @trans('user.referrals_done') }}</span>
                                                                            <div class="">{{ $referrals_done }}</div>
                                                                            {{-- <input type="hidden" class="form-control user_edit_detail" name="" id="referrals_done" value="{{ $referrals_done }}"> --}}

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            $states = App\Models\States::all();
                                                        @endphp
                                                        <div class="col-md-3 pl-0">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div class="media-body"> <span>State Name</span>
                                                                            <div class="user_edit">{{ $user->state_name }}
                                                                            </div>
                                                                            <select
                                                                                class="form-control wizard-required edit_submit state_name"
                                                                                name="state" id="stateid"
                                                                                onclick="select_state_city()">

                                                                                <option value=""
                                                                                    label="Select State"></option>
                                                                                @foreach ($states as $item)
                                                                                    <option value="{{ $item->id }}"
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
                                                                        <div class="media-body"> <span>City Name</span>
                                                                            <div class="user_edit">{{ $user->city_name }}
                                                                            </div>
                                                                            <select
                                                                                class="form-control wizard-required edit_submit city_name"
                                                                                name="city_id" id="city_id">
                                                                                <option value=""
                                                                                    label="Select City"></option>

                                                                                @foreach ($cities as $item)
                                                                                    <option value="{{ $item->id }}"
                                                                                        @if ($item->name == $user->city_name) selected @endif>
                                                                                        {{ $item->name }}</option>
                                                                                @endforeach

                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @php
                                                            if(!empty($driver_details)){
                                                                $driver = App\Models\Drivers::where('id', $driver_details->id)->first();
                                                            }else{
                                                                $driver = [];
                                                            }
                                                            
                                                        @endphp
                                                        <div class="col-md-3 pl-0">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div class="media-body">
                                                                            @if(!empty($driver))
                                                                            <span>Authorised Driver</span>
                                                                            <a {{-- href="url()" --}} {{-- href="{{ route('admin.driver.authorized', ['panel' => Session::get('panel'),'id'=> 1,'data'=> $driver_details->id]) }}" --}}
                                                                                {{-- @click="AuthroziedConfirm(item.id)" --}} title="Authorized">
                                                                                <form
                                                                                    action="{{ route('admin.driver.authorized.status', ['panel' => Session::get('panel')]) }}"
                                                                                    method="POST">
                                                                                    @csrf
                                                                                        <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">

                                                                                    @if ($driver->authorised_driver == null || $driver->authorised_driver == 0)
                                                                                        <label class="switch"> 

                                                                                            <button type="submit" class="btn btn-danger">
                                                                                                {{-- <input
                                                                                                    type="checkbox" checked
                                                                                                    > --}}
                                                                                                   Not Authorized
                                                                                                </button>
                                                                                            <span
                                                                                                class="slider round"></span>
                                                                                        </label>
                                                                                    @else
                                                                                        <label class="switch">
                                                                                            <button type="submit" class="btn btn-primary">
                                                                                                {{-- <input
                                                                                                    type="checkbox"
                                                                                                    > --}}
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

                                                        <!-- <div class="col-md-3 pl-0">
                                                                <div class="mg-sm-r-40 mg-b-10">
                                                                    <div class="main-profile-contact-list">
                                                                        <div class="media">
                                                                            <div class="media-body"> <span>{{ @trans('user.current_location') }}</span>
                                                                                <div>{{ $current_location }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->

                                                        <!-- <div class="col-md-3">
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
                                                    id="driverUser" onclick="editUserDetails()">Submit</button>
                                            </div>
                                            @php
                                                if (!empty($driver_details)) {
                                                    $abc = App\Models\AgentUsers::where('id', $driver_details->agent_id)->first();
                                                }
                                            @endphp
                                            @if ($user->user_type_id == '4' || $user->user_type_id == '5')
                                                <div class="border-top"></div>
                                                <div class="p-4">
                                                    <label class="main-content-label tx-13 mg-b-20">Driver</label>
                                                    <div class="d-flex-end float-right">
                                                        <form id='driver_id'>
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
                                                                                <span>{{ @trans('user.driver_mobile_numebr') }}</span>
                                                                                <div class="driver_edit">
                                                                                    {{ $driver_details->mobile_numebr }}
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    class="form-control driver_mobile_numebr"
                                                                                    name=""
                                                                                    id="driver_mobile_numebr"
                                                                                    value="{{ $driver_details->mobile_numebr }}">
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
                                                                                <div class="driver_edit">
                                                                                    {{ ucwords(strtolower($driver_details->first_name)) . ' ' . ucwords(strtolower($driver_details->last_name)) }}
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    class="form-control first_name"
                                                                                    name="" id="first_name"
                                                                                    value="{{ ucwords(strtolower($driver_details->first_name)) . ' ' . ucwords(strtolower($driver_details->last_name)) }}">

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
                                                                                <div class="driver_edit">
                                                                                    {{ $driver_details->adhar_card_no }}
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    class="form-control adhar_card_no"
                                                                                    name="" id="adhar_card_no"
                                                                                    value="{{ $driver_details->adhar_card_no }}">

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
                                                                                <div class="driver_edit">
                                                                                    {{ $driver_details->driving_licence_no }}
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    class="form-control driving_licence_no"
                                                                                    name="" id="driving_licence_no"
                                                                                    value="{{ $driver_details->driving_licence_no }}">

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
                                                                                <div class="driver_edit">

                                                                                    @if (!empty($driver_details))
                                                                                        {{ $driver_details->driving_licence_expiry_date }}
                                                                                    @endif
                                                                                </div>

                                                                                <input type="hidden"
                                                                                    class="form-control driving_licence_expiry_date"
                                                                                    name=""
                                                                                    id="driving_licence_expiry_date"
                                                                                    value="{{ $driver_details->driving_licence_expiry_date }}">
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
                                                                                <div class="driver_edit">
                                                                                    {{ $driver_details->street_address }}
                                                                                </div>
                                                                                <input type="hidden"
                                                                                    class="form-control street_address"
                                                                                    name="" id="street_address"
                                                                                    value="{{ $driver_details->street_address }}">

                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- <div class="col-md-3 pl-0">
                                                                <div class="mg-sm-r-40 mg-b-10">
                                                                    <div class="main-profile-contact-list">
                                                                        <div class="media">
                                                                            <div class="media-body"> <span>{{ @trans('user.no_of_trips_completed') }}</span>
                                                                                <div>{{ $driver_trip }}</div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> -->
                                                        </div>
                                                    </div>
                                                    <button type="button" class="edit_travel btn-primary btn-sm mt-2"
                                                        onclick="editDriverDetails()">Submit</button>

                                                </div>
                                            @endif

                                            <?php if(!empty($plan_details)){?>
                                            <div class="border-top"></div>

                                            <div class="p-4" style="display:none;">
                                                <label class="main-content-label tx-13 mg-b-20">subscription plan</label>
                                                <div class="d-sm-flex">
                                                    <div class="row col-md-12">
                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="icon ion-pull-request"></i>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <span>{{ @trans('user.plan_name') }}</span>
                                                                            <div>{{ $plan_details->name }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="ti-calendar sidemenu-icon"></i>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <span>{{ @trans('user.plan_validity') }}</span>
                                                                            <div>{{ $plan_details->plan_validity }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="ti-money sidemenu-icon"></i>
                                                                        </div>
                                                                        <div class="media-body">
                                                                            <span>{{ @trans('user.price') }}</span>
                                                                            <div>{{ $plan_details->price }}</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="ti-calendar sidemenu-icon"></i>
                                                                        </div>
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

                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="ti-calendar sidemenu-icon"></i>
                                                                        </div>
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

                                                        <div class="col-md-3">
                                                            <div class="mg-sm-r-40 mg-b-10">
                                                                <div class="main-profile-contact-list">
                                                                    <div class="media">
                                                                        <div
                                                                            class="media-icon bg-primary-transparent text-primary">
                                                                            <i class="ti-money sidemenu-icon"></i>
                                                                        </div>
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
                                            </div>
                                            <?php } ?>


                                            <div class="border-top"></div>
                                            <div class="p-4">
                                                <label class="main-content-label tx-13 mg-b-20">Driver Documents</label>
                                                <div class="">
                                                    <div class="col-md-3 pl-0">
                                                        <div class="mg-sm-r-40 mg-b-10">
                                                            <div class="main-profile-contact-list">
                                                                <div class="media">
                                                                    <div class="media-body"> <span>Driving Licence Expiry
                                                                            Date</span>

                                                                        @if (!empty($driver_details))
                                                                            <div>
                                                                                {{ $driver_details->driving_licence_expiry_date }}
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row row-sm">

                                                        <div class="col-6 col-md-3 text-center">
                                                            <h6 class="text-center">Profile Image</h6>
                                                            <div class="image-div">
                                                                @if (!empty($user->profile_pic))
                                                                    <img alt="Profile pic"
                                                                        class="img-thumbnail image-class"
                                                                        src="{{ $user->profile_pic }}"
                                                                        onclick="getImage('{{ $user->profile_pic }}','Profile Image')">
                                                                @else
                                                                    <img alt="docuemtn image"
                                                                        class="img-thumbnail image-class"
                                                                        src="{{ env('APP_URL') }}/public/noimage.png">
                                                                @endif
                                                            </div>
                                                            <span class="text-success" id="profile_pic_status_approved"
                                                                style="display:none;">Approved</span>
                                                            <span class="text-danger" id="profile_pic_status_rejected"
                                                                style="display:none;">Rejected</span>

                                                            @if ($user->profile_pic_status == 2)
                                                                @if (!empty($user->profile_pic))
                                                                    <button type="button"
                                                                        class="btn ripple btn-success btn-sm mt-2 profile_pic_status_btn"
                                                                        onclick="documentVerify('user','{{ $user->id }}','profile_pic_status','1')">Approve</button>
                                                                    <button type="button"
                                                                        class="btn ripple btn-danger btn-sm mt-2 profile_pic_status_btn"
                                                                        onclick="documentVerify('user','{{ $user->id }}','profile_pic_status','0')">Reject</button>
                                                                @endif
                                                            @else
                                                                @if ($user->profile_pic_status == 1)
                                                                    <span class="text-success">Approved</span>
                                                                @else
                                                                    <span class="text-danger">Rejected</span>
                                                                @endif
                                                            @endif

                                                            &nbsp;
                                                            <button type="button"
                                                                class="btn ripple btn-primary btn-sm mt-2"
                                                                onclick="imageUpload('user','{{ $user->id }}','profile_pic_status','profile_pic','driver')">Change
                                                                Image</button>
                                                        </div>

                                                        @if (!empty($driver_details))

                                                            @if (!empty($driver_details->pan_card_url))
                                                                <div class="col-6 col-md-3 text-center">
                                                                    <h6 class="text-center">Pan Card</h6>
                                                                    <div class="image-div">

                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->pan_card_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->pan_card_url }}"
                                                                            onclick="getImage('{{ $driver_details->pan_card_url }}','Pan Card')">
                                                                        <?php } else{ ?>
                                                                        <a class="img-thumbnail"
                                                                            href="{{ $driver_details->pan_card_url }}"
                                                                            target="_blank"><img
                                                                                src="{{ env('APP_URL') }}/public/pdf.png"
                                                                                width="75%"></a>
                                                                        <?php } ?>

                                                                    </div>

                                                                    <span class="text-success"
                                                                        id="d_pan_card_url_status_approved"
                                                                        style="display:none;">Approved</span>
                                                                    <span class="text-danger"
                                                                        id="d_pan_card_url_status_rejected"
                                                                        style="display:none;">Rejected</span>

                                                                    @if ($driver_details->pan_card_url_status == 2)
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 d_pan_card_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_pan_card_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 d_pan_card_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_pan_card_url_status','0')">Reject</button>
                                                                    @else
                                                                        @if ($driver_details->pan_card_url_status == 1)
                                                                            <span class="text-success">Approved</span>
                                                                        @else
                                                                            <span class="text-danger">Rejected</span>
                                                                        @endif
                                                                    @endif

                                                                    &nbsp; <button type="button"
                                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                                        onclick="imageUpload('driver','{{ $driver_details->id }}','d_pan_card_url_status','pan_card_url','driver')">Change
                                                                        Image</button>

                                                                </div>
                                                            @endif

                                                            @if (!empty($driver_details->adhar_card_url))
                                                                <div class="col-6 col-md-3 text-center">
                                                                    <h6 class="text-center">Aadhaar Card</h6>

                                                                    <div class="image-div">

                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->adhar_card_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->adhar_card_url }}"
                                                                            onclick="getImage('{{ $driver_details->adhar_card_url }}','Adhar Card')">
                                                                        <?php } else{ ?>
                                                                        <a class="img-thumbnail"
                                                                            href="{{ $driver_details->adhar_card_url }}"
                                                                            target="_blank"><img
                                                                                src="{{ env('APP_URL') }}/public/pdf.png"
                                                                                width="75%"></a>
                                                                        <?php } ?>

                                                                    </div>

                                                                    <span class="text-success"
                                                                        id="d_adhar_card_url_status_approved"
                                                                        style="display:none;">Approved</span>
                                                                    <span class="text-danger"
                                                                        id="d_adhar_card_url_status_rejected"
                                                                        style="display:none;">Rejected</span>

                                                                    @if ($driver_details->adhar_card_url_status == 2)
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 d_adhar_card_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_adhar_card_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_adhar_card_url_status','0')">Reject</button>
                                                                    @else
                                                                        @if ($driver_details->adhar_card_url_status == 1)
                                                                            <span class="text-success">Approved</span>
                                                                        @else
                                                                            <span class="text-danger">Rejected</span>
                                                                        @endif
                                                                    @endif

                                                                    &nbsp; <button type="button"
                                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                                        onclick="imageUpload('driver','{{ $driver_details->id }}','d_adhar_card_url_status','adhar_card_url','driver')">Change
                                                                        Image</button>
                                                                </div>
                                                            @endif

                                                            @if (!empty($driver_details->adhar_card_back_url))
                                                                <div class="col-6 col-md-3 text-center">
                                                                    <h6 class="text-center">Aadhaar Card</h6>
                                                                    <div class="image-div">

                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->adhar_card_back_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->adhar_card_back_url }}"
                                                                            onclick="getImage('{{ $driver_details->adhar_card_back_url }}','Adhar Card')">
                                                                        <?php } else{ ?>
                                                                        <a class="img-thumbnail"
                                                                            href="{{ $driver_details->adhar_card_back_url }}"
                                                                            target="_blank"><img
                                                                                src="{{ env('APP_URL') }}/public/pdf.png"
                                                                                width="75%"></a>
                                                                        <?php } ?>

                                                                    </div>
                                                                    <span class="text-success"
                                                                        id="d_adhar_card_back_url_status_approved"
                                                                        style="display:none;">Approved</span>
                                                                    <span class="text-danger"
                                                                        id="d_adhar_card_back_url_status_rejected"
                                                                        style="display:none;">Rejected</span>

                                                                    @if ($driver_details->adhar_card_back_url_status == 2)
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 d_adhar_card_back_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_adhar_card_back_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 d_adhar_card_back_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','d_adhar_card_back_url_status','0')">Reject</button>
                                                                    @else
                                                                        @if ($driver_details->adhar_card_back_url_status == 1)
                                                                            <span class="text-success">Approved</span>
                                                                        @else
                                                                            <span class="text-danger">Rejected</span>
                                                                        @endif
                                                                    @endif

                                                                    &nbsp; <button type="button"
                                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                                        onclick="imageUpload('driver','{{ $driver_details->id }}','d_adhar_card_back_url_status','adhar_card_back_url','driver')">Change
                                                                        Image</button>
                                                                </div>
                                                            @endif

                                                            @if (!empty($driver_details->dl_front_url))
                                                                <div class="col-6 col-md-3 text-center">
                                                                    <h6 class="text-center">DL Front</h6>
                                                                    <div class="image-div">
                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->dl_front_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->dl_front_url }}"
                                                                            onclick="getImage('{{ $driver_details->dl_front_url }}','DL Front')">
                                                                        <?php } else{ ?>
                                                                        <a class="img-thumbnail"
                                                                            href="{{ $driver_details->dl_front_url }}"
                                                                            target="_blank"><img
                                                                                src="{{ env('APP_URL') }}/public/pdf.png"
                                                                                width="75%"></a>
                                                                        <?php } ?>

                                                                    </div>

                                                                    <span class="text-success"
                                                                        id="dl_front_url_status_approved"
                                                                        style="display:none;">Approved</span>
                                                                    <span class="text-danger"
                                                                        id="dl_front_url_status_rejected"
                                                                        style="display:none;">Rejected</span>

                                                                    @if ($driver_details->dl_front_url_status == 2)
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 dl_front_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','dl_front_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 dl_front_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','dl_front_url_status','0')">Reject</button>
                                                                    @else
                                                                        @if ($driver_details->dl_front_url_status == 1)
                                                                            <span class="text-success">Approved</span>
                                                                        @else
                                                                            <span class="text-danger">Rejected</span>
                                                                        @endif
                                                                    @endif

                                                                    &nbsp; <button type="button"
                                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                                        onclick="imageUpload('driver','{{ $driver_details->id }}','dl_front_url_status','dl_front_url','driver')">Change
                                                                        Image</button>
                                                                </div>
                                                            @endif

                                                            @if (!empty($driver_details->dl_back_url))
                                                                <div class="col-6 col-md-3 text-center">
                                                                    <h6 class="text-center">DL Back</h6>
                                                                    <div class="image-div">
                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->dl_back_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->dl_back_url }}"
                                                                            onclick="getImage('{{ $driver_details->dl_back_url }}','DL Back')">
                                                                        <?php } else{ ?>
                                                                        <!-- <a class="img-thumbnail" href="{{ $driver_details->dl_back_url }}" target="_blank"><img src="{{ env('APP_URL') }}/public/pdf.png" width="75%"></a> -->

                                                                        <img style="cursor:pointer;"
                                                                            onclick="getPdf('{{ $driver_details->dl_back_url }}','Police Verification')"
                                                                            src="{{ env('APP_URL') }}/public/pdf.png"
                                                                            width="75%">

                                                                        <?php } ?>
                                                                    </div>

                                                                    <span class="text-success"
                                                                        id="dl_back_url_status_approved"
                                                                        style="display:none;">Approved</span>
                                                                    <span class="text-danger"
                                                                        id="dl_back_url_status_rejected"
                                                                        style="display:none;">Rejected</span>

                                                                    @if ($driver_details->dl_back_url_status == 2)
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 dl_back_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','dl_back_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 dl_back_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','dl_back_url_status','0')">Reject</button>
                                                                    @else
                                                                        @if ($driver_details->dl_back_url_status == 1)
                                                                            <span class="text-success">Approved</span>
                                                                        @else
                                                                            <span class="text-danger">Rejected</span>
                                                                        @endif
                                                                    @endif

                                                                    &nbsp; <button type="button"
                                                                        class="btn ripple btn-primary btn-sm mt-2"
                                                                        onclick="imageUpload('driver','{{ $driver_details->id }}','dl_back_url_status','dl_back_url','driver')">Change
                                                                        Image</button>
                                                                </div>
                                                            @endif



                                                            <div class="col-12 col-md-12">
                                                                &nbsp;
                                                            </div>


                                                            <div class="col-6 col-md-3 text-center">
                                                                <h6 class="text-center">Police Verification</h6>
                                                                <div class="image-div">
                                                                    @if (!empty($driver_details->police_verification_url))
                                                                        <?php 
                                                    $Infos = pathinfo($driver_details->police_verification_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                        <img alt="docuemtn image"
                                                                            class="img-thumbnail image-class"
                                                                            src="{{ $driver_details->police_verification_url }}"
                                                                            onclick="getImage('{{ $driver_details->police_verification_url }}','Police Verification')">
                                                                        <?php } else{ ?>
                                                                        <img style="cursor:pointer;"
                                                                            onclick="getPdf('{{ $driver_details->police_verification_url }}','Police Verification')"
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
                                                                    id="police_verification_url_status_approved"
                                                                    style="display:none;">Approved</span>
                                                                <span class="text-danger"
                                                                    id="police_verification_url_status_rejected"
                                                                    style="display:none;">Rejected</span>

                                                                @if ($driver_details->police_verification_url_status == 2)
                                                                    @if (!empty($driver_details->police_verification_url))
                                                                        <button type="button"
                                                                            class="btn ripple btn-success btn-sm mt-2 police_verification_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','police_verification_url_status','1')">Approve</button>
                                                                        <button type="button"
                                                                            class="btn ripple btn-danger btn-sm mt-2 police_verification_url_status_btn"
                                                                            onclick="documentVerify('driver','{{ $driver_details->id }}','police_verification_url_status','0')">Reject</button>
                                                                    @endif
                                                                @else
                                                                    @if ($driver_details->police_verification_url_status == 1)
                                                                        <span class="text-success">Approved</span>
                                                                    @else
                                                                        <span class="text-danger">Rejected</span>
                                                                    @endif
                                                                @endif
                                                                &nbsp; <button type="button"
                                                                    class="btn ripple btn-primary btn-sm mt-2"
                                                                    onclick="imageUpload('driver','{{ $driver_details->id }}','police_verification_url_status','police_verification_url','driver_cum_owner')">Change
                                                                    Image</button>
                                                            </div>

                                                        @endif

                                                        @if (($user->user_type_id == '4' || $user->user_type_id == '5') && !empty($banks->bank_document_url))
                                                            <div class="col-6 col-md-3 text-center" style="display:none;">
                                                                <h6 class="text-center">Bank Cheque Document</h6>

                                                                <div class="image-div">

                                                                    <?php 
                                                    $Infos = pathinfo($banks->document_url);
                                                    $extension = $Infos['extension'];
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                                    <img alt="docuemtn image"
                                                                        class="img-thumbnail image-class"
                                                                        src="{{ $banks->document_url }}"
                                                                        onclick="getImage('{{ $banks->document_url }}','Bank Document')">
                                                                    <?php } else{ ?>
                                                                    <a class="img-thumbnail"
                                                                        href="{{ $banks->document_url }}"
                                                                        target="_blank"><img
                                                                            src="{{ env('APP_URL') }}/public/pdf.png"
                                                                            width="75%"></a>
                                                                    <?php } ?>
                                                                </div>

                                                                <span class="text-success"
                                                                    id="bank_document_url_status_approved"
                                                                    style="display:none;">Approved</span>
                                                                <span class="text-danger"
                                                                    id="bank_document_url_status_rejected"
                                                                    style="display:none;">Rejected</span>

                                                                @if ($banks->bank_document_url_status == 2)
                                                                    <button type="button"
                                                                        class="btn ripple btn-success btn-sm mt-2 bank_document_url_status_btn"
                                                                        onclick="documentVerify('bank_account','{{ $banks->id }}','bank_document_url_status','1')">Approve</button>
                                                                    <button type="button"
                                                                        class="btn ripple btn-danger btn-sm mt-2 bank_document_url_status_btn"
                                                                        onclick="documentVerify('bank_account','{{ $banks->id }}','bank_document_url_status','0')">Reject</button>
                                                                @else
                                                                    @if ($banks->bank_document_url_status == 1)
                                                                        <span class="text-success">Approved</span>
                                                                    @else
                                                                        <span class="text-danger">Rejected</span>
                                                                    @endif
                                                                @endif

                                                                &nbsp; <button type="button"
                                                                    class="btn ripple btn-primary btn-sm mt-2"
                                                                    onclick="imageUpload('bank_account','{{ $banks->id }}','bank_document_url_status','document_url','driver')">Change
                                                                    Image</button>
                                                            </div>
                                                        @endif

                                                    </div>
                                                </div>
                                            </div>

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
                                                        <div style="margin-bottom: 9px;">{{ $history->created_at }}</div>
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
                                <div>
                                    <h6 class="main-content-label mb-1">Vehicles</h6>
                                </div>
                                <div class="d-flex-end float-right">

                                    <a href="<?= url(Session::get('panel') . '/vehicles/create') ?>/<?= $user->id ?>">
                                        <button type="submit" class="btn btn-primary">+ Add</button>
                                    </a>
                                </div>
                                <div class="row">
                                    <div class="table-responsive table-data" v-if="items.length">
                                        <div class="col-sm-12">
                                            <div class="table-checkable">
                                                <table id="vehicles"
                                                    class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                                    width="100%">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th><b>{{ @trans('vehicles.vehicle_number') }}</b></th>
                                                            <th><b>{{ @trans('vehicles.brand_name') }}</b></th>
                                                            <th><b>{{ @trans('vehicles.model_name') }}</b></th>
                                                            <th><b>{{ @trans('vehicles.vehicle_type_name') }}</b></th>
                                                            <th><b>{{ @trans('vehicles.vehicle_fuel_type_name') }}</b>
                                                            </th>
                                                            <th><b>{{ @trans('vehicles.registration_year') }}</b></th>
                                                            <th><b>{{ @trans('vehicles.action') }}</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php 
                                                    $i=1;
                                                    foreach($vehicles as $v){?>
                                                        <tr>
                                                            <td><?= $i ?></td>
                                                            <td>{{ strtoupper($v['vehicle_number']) }}</td>
                                                            <td>{{ $v['brand_name'] }}</td>
                                                            <td>{{ $v['model_name'] }}</td>
                                                            <td>{{ $v['vehicle_type_name'] }}</td>
                                                            <td>{{ $v['vehicle_fuel_type_name'] }}</td>
                                                            <td>{{ $v['registration_year'] }}</td>
                                                            <td class="act-btn">
                                                                <a href="<?= url(Session::get('panel') . '/vehicles/show') ?>/<?= $v['id'] ?>"
                                                                    title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                                <a href="<?= url(Session::get('panel') . '/vehicles/edit') ?>/<?= $v['id'] ?>"
                                                                    title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">edit</i>
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
                                                        <?php $i++; } ?>
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
                                                    $i=1;
                                                    foreach($cabs as $c){?>
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
                                                        <?php $i++; } ?>
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
                @if ($user->user_type_id == '3')
                    <div class="row row-sm" style="display:none;">
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
                                                    <table id="driverdetails"
                                                        class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer"
                                                        width="100%">
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
                                                    $i=1;
                                                    foreach($drivers as $d){?>
                                                            <tr>
                                                                <td><?= $i ?></td>
                                                                <td>{{ $d['first_name'] . ' ' . $d['last_name'] }}</td>
                                                                <td>{{ $d['mobile_number'] }}</td>
                                                                <td>{{ $d['adhar_card_no'] }}</td>
                                                                <td>{{ $d['driving_licence_no'] }}</td>
                                                                <td>{{ $d['driving_licence_expiry_date'] }}</td>
                                                                <td>{{ $d['street_address'] }}</td>
                                                                <td class="act-btn">
                                                                    <a href="<?= url(Session::get('panel') . '/drivers/show') ?>/<?= $d['id'] ?>"
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
                                                            <?php $i++; } ?>
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
                                                    $i=1;
                                                    foreach($referrals as $r){?>
                                                        <tr>
                                                            <td><?= $i ?></td>
                                                            <td>{{ $r['first_name'] . ' ' . $r['last_name'] }}</td>
                                                            <td>{{ $r['mobile_number'] }}</td>
                                                            <td>{{ $r['user_type_name'] }}</td>
                                                            <td>
                                                                <?php
                                                            if($r['documentation_status'] == '1'){
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
                                                            if($r['payment_status'] == '1'){
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
                                                            if($r['is_claimed'] == '1'){
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
                                                        <?php $i++; } ?>
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
                        <label class="control-label">{{ @trans('user.dob') }}<span class="required">*</span></label>
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
                            <input type="radio" class="custom-control-input" id="customRadio3" name="user_status"
                                value="3" @if ($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio" name="user_status"
                                value="1" @if ($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1" name="user_status"
                                value="0" @if ($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2" name="user_status"
                                value="2" @if ($user->user_status == '2') checked @endif>
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
            <!-- <div class="modal-body" id="panzoom">
                <img id="image-gallery-image" class="mainimage" src="">
            </div> -->

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
                            <input type="file" name="police_verification" id="image_key" class="dropify driver"
                                data-height="200" />
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
<div class="modal" tabindex="-1" role="dialog" id="modalDeleteConfirm">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('admin.users.reset', ['panel' => Session::get('panel')]) }}" method="post">
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

    <script>
        function abc() {
            var user = $('#user_id').val();
            alert(user);

            // $.ajaxSetup({
            //     headers: {
            //         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            //     }
            // });
            // $.ajax({
            //     type: 'GET',
            //     url: url,
            //     data: {
            //         // 'id':1,
            //         'user':user,
            //     },
            //     dataType: 'json',
            //     success: function(response_msg) {
            //         alert(response_msg.success);
            //         if (response_msg.success == true) {
            //             toastr.success('Your Data Is Updated...');
            //             window.location.reload();

            //         } else if (response_msg.success == 408) {
            //             toastr.error('Something Went Wrong...');
            //         }
            //     }
            // });
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

        function getPdf(filename, title) {
            $('#view-pdf').attr('src', filename);
            $(".pdf-title").text(title);
            $("#attachPdfModal").modal();
        }

        function getImage(imageName, title) {
            $('#image-gallery-image').attr('src', imageName);
            $(".img-title").text(title);
            $("#attachModal").modal();
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
    </script>
    <script>
        $(document).ready(function($) {
            $(document).on('submit', '#submit-form', function(event) {
                event.preventDefault();

                $('.user_edit').css('display', 'none');
                $('.user_edit_detail').prop('type', 'text');
                $('.edit_submit').css('display', 'block');
            });
        });

        function editUserDetails() {
            var user_name = $('#user_name').val();
            var stateid = $('#stateid').find(":selected").val();
            var city_id = $('#city_id').find(":selected").val();

            var user_id = $('#user_id').val();
            var user_data = $('#user_data').val();

            var siteurl = "{{ route('admin.driver.update', ['panel' => Session::get('panel')]) }}";
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
                    'user_id': user_id,
                    'user_name': user_name,
                    'stateid': stateid,
                    'city_id': city_id,
                    'user_data': user_data,
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
        $(document).on('submit', '#agent_id', function(event) {
            event.preventDefault();

            $('.travel_edit').css('display', 'none');
            $('.travel_name,.owner_name,.agent_office_no,.total_business_year,.pan_card,.adhar_card,.account_number,.bank_name,.branch_name,.ifsc_code,.earnings')
                .prop('type', 'text');
            $('.edit_travel').css('display', 'block');
        });

        function editDriverDetails() {
            var driver_mobile_numebr = $('#driver_mobile_numebr').val();
            var first_name = $('#first_name').val();
            var adhar_card_no = $('#adhar_card_no').val();
            var driving_licence_no = $('#driving_licence_no').val();
            var driving_licence_expiry_date = $('#driving_licence_expiry_date').val();
            var street_address = $('#street_address').val();
            var drivers_id = $('#drivers_id').val();
            var siteurl = "{{ route('admin.driver_edit', ['panel' => Session::get('panel')]) }}";

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
                    'driver_mobile_numebr': driver_mobile_numebr,
                    'first_name': first_name,
                    'adhar_card_no': adhar_card_no,
                    'driving_licence_no': driving_licence_no,
                    'driving_licence_expiry_date': driving_licence_expiry_date,
                    'street_address': street_address,
                    'drivers_id': drivers_id,
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
        function select_state_city() {
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
                            'value': element.id
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
        function HistoryShow() {
            $(".history_show").toggle();
        }
    </script>
@endpush
