@extends('admin.layouts.main')

@section('title') Users @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('user.users') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                        <a class="btn btn-primary my-2 btn-icon-text"
                            href="{{ route('admin.register.create',['panel' => Session::get('panel')]) }}"><i
                                class="fe fe-plus mr-2"></i> {{ @trans('user.add_user') }} </a>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <div class="row">
                <div class="col-sm-8 col-lg-8 col-xl-8">
                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <div class="row row-sm">
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'day']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Today's Joined</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$today_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'yesterday']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Yesterdays's Joined</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$yesterday_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'week']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Weekly Joined</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$week_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'this_month']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">This Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$this_month_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'last_month']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Last Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$last_month_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel')]) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">All Joined</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{$all_joined}}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <div class="row row-sm">
                                    <!--  -->
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'fiften_days']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Active User 15 days</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $active_users }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'inactive']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Inactive user</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $inactive_users }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'paid']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Paid User</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $paid_users }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'unpaid']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">unpaid user</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $unpaid_users }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'expired_users']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Expired User</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $expired_users }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                        onclick='location.href = "{{ route('admin.register.index',['panel'=>
                                        Session::get('panel'),'sub'=>'pending']) }}";'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Pending</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $pending }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-4 col-lg-4 col-xl-4">
                    <div class="card custom-card overflow-hidden">
                        <div class="card-body pl-0">
                            <div class>
                                <div class="container">
                                    <canvas id="chartLine" class="chart-dropshadow2 ht-250"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <style>
                                .daterangpicker-vuejs {
                                    width: 309px !important;
                                    position: fixed !important;
                                }
                            </style>
                            <div class="row">
                                <div class="col-lg-3 daterangpicker-vuejs">
                                    <date-picker v-model="start_date" :lang="picker12Lang" valueType="format"
                                        placeholder="Select Date" format="YYYY-MM-DD" style="width:92%;"></date-picker>
                                </div>

                                <div class="col-lg-4">
                                    <select class="form-control" id="user_type" v-model="user_type"
                                        style="margin-left: -28px;">
                                        <option value="all" selected>All</option>
                                        <?php foreach ($user_type as $r) { ?>
                                        @if ($r['id'] == 2)
                                        <option value="<?= $r['id']; ?>">
                                            <?= $r['name']; ?>
                                        </option>
                                        @elseif($r['id'] == 3)
                                        <option value="<?= $r['id']; ?>">
                                            <?= $r['name']; ?>
                                        </option>
                                        @endif
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-lg-4">
                                    <select class="form-control" id="user_interest_status"
                                        v-model="user_interest_status" style="margin-left: -28px;">
                                        <option value="" selected>Select User Status</option>
                                        <option value="2">Red (Not Interested)</option>
                                        <option value="1">White (White Number Plate)</option>
                                        <option value="4">Yellow</option>
                                        <option value="3">Green</option>
                                    </select>
                                </div>

                                <div class="col-lg-1">
                                    <div class="table-top-panel d-flex align-items-center">
                                        <div
                                            class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                            <button type="button" class="btn ripple btn-primary"
                                                @click="master_search" style="width: 100%;"> Submit</button>
                                            <i class="material-icons" style="cursor: pointer;" v-if="start_date"
                                                @click="clearSearch">close</i>
                                        </div>
                                    </div>
                                </div>
                                <!-- <i class="ti-search" data-toggle="tooltip"
                                                    title="" data-original-title="ti-search"></i> -->
                               
                                <div class="col-lg-1">
                                    <div class="table-top-panel d-flex align-items-center">
                                        @php
                                         $u_id = auth()->user()->id;
                                        @endphp
                                        @if($u_id == 292)
                                        <a class="btn btn-primary my-2 btn-icon-text"
                                            href="{{ route('admin.register.downloadexport',['panel' => Session::get('panel')]) }}"><i
                                                class="fe fe-download mr-2"></i> Excel </a>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                &nbsp;
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div
                                        class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <label style="white-space: nowrap;" class="mb-0 gray">{{
                                            @trans('usertype.record_per_page') }}&nbsp;</label>
                                        <select class="form-control" id="per_page" v-model="per_page" @input="perPage">
                                            <option value="25">25 </option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group search-input float-right mb-3">
                                        <!-- <img src="{{asset('img/admin/ic_search.png')}}" srcset="{{asset('img/admin/ic_search@2x.png 2x')}}" alt="search"> -->
                                        <input type="text" class="form-control" name="search_text" id="search_text"
                                            v-model="search_text" @input="search" placeholder="Search" style="width:300px;">
                                        <!-- <i class="material-icons" v-if="search_text" @click="clearSearch">close</i> -->
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">
                                    <div class="col-sm-12">
                                        <div class="table-checkable table-scrollable"
                                            style="height: 500px; overflow-x: scroll;">
                                            <table id="example"
                                                class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer">
                                                <thead>
                                                    <tr data-id="1">
                                                        <th>#</th>
                                                        <th><b>First Name</b><a href="javascript:void(0)"
                                                                :class="classSort('users.first_name')"
                                                                @click="sort('users.first_name')"></a></th>
                                                       
                                                        <th><b>Last Name</b><a href="javascript:void(0)"
                                                                :class="classSort('users.last_name')"
                                                                @click="sort('users.last_name')"></a></th>
                                                        <th><b>User Type</b><a href="javascript:void(0)"
                                                                :class="classSort('user_type.name')"
                                                                @click="sort('user_type.name')"></a></th>
                                                                 <th><b>App Version</b><a href="javascript:void(0)"
                                                                :class="classSort('users.app_version')"
                                                                @click="sort('users.app_version')"></a></th>
                                                        <th><b>Date Time</b><a href="javascript:void(0)"
                                                                :class="classSort('users.created_at')"
                                                                @click="sort('users.created_at')"></a></th>

                                                        <th><b>Owner Name</b><a href="javascript:void(0)"
                                                                :class="classSort('agent_users.owner_name')"
                                                                @click="sort('agent_users.owner_name')"></a>
                                                        </th>
                                                        <th><b>Driver Name</b><a href="javascript:void(0)"
                                                                :class="classSort('drivers.first_name')"
                                                                @click="sort('drivers.first_name')"></a>
                                                        </th>
                                                        <th><b>CAB NO</b></th>
                                                        <th><b>{{ @trans('user.phone') }}</b><a
                                                                href="javascript:void(0)"
                                                                :class="classSort('users.mobile_number')"
                                                                @click="sort('users.mobile_number')"></a></th>
                                                        <!-- <th><b>{{ @trans('user.email') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('emailid')" @click="sort('emailid')"></a></th> -->
                                                        <th><b>Last Updated</b><a href="javascript:void(0)"
                                                                :class="classSort('user_status')"
                                                                @click="sort('user_status')"></a></th>
                                                        <th><b>Plan Name</b><a href="javascript:void(0)"
                                                                :class="classSort('subscription_name')"
                                                                @click="sort('subscription_name')"></a></th>
                                                        <!-- <th><b>Subscription End Date</b></th> -->
                                                        <th><b>Subscription Start Date</b><a href="javascript:void(0)"
                                                                :class="classSort('user_purchased_plans.start_datetime')"
                                                                @click="sort('user_purchased_plans.start_datetime')"></a>
                                                        </th>
                                                        <th><b>Subscription End Date</b><a href="javascript:void(0)"
                                                                :class="classSort('user_purchased_plans.end_datetime')"
                                                                @click="sort('user_purchased_plans.end_datetime')"></a>
                                                        </th>
                                                        <!-- <th><b>{{ @trans('user.status') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('user_status')" @click="sort('user_status')"></a></th> -->

                                                        <th><b>{{ @trans('user.action') }}</b></th>
                                                        <th><b>V2 Traveler <br/>Details</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <!-- danger -->

                                                        {{-- <td data-id="@{{ index + from }}"
                                                            v-if="item.is_approved=='1'">
                                                            <span class="badge bg-success">@{{ index + from }}</span>
                                                        </td> --}}
                                                        <!-- (item.agent_logo_status =='2' || item.agent_logo_status ==null) && (item.pan_card_url_status=='2' || item.pan_card_url_status==null) && (item.adhar_card_url_status=='2' || item.adhar_card_url_status==null) && (item.registration_document_url_status=='2' || item.registration_document_url_status==null) && (item.user_type_name == 'Agent') -->
                                                        {{-- <td v-else-if="item.is_approved == '2'">
                                                            <span class="badge bg-warning">@{{ index + from }}</span>
                                                        </td> --}}
                                                        <!-- <td v-else-if="(item.dl_front_url_status=='2' || item.dl_front_url_status==null) && (item.dl_back_url_status=='2' || item.dl_back_url_status==null) && (item.d_pan_card_url_status=='2' || item.d_pan_card_url_status==null) && (item.d_adhar_card_url_status=='2' || item.d_adhar_card_url_status==null) && (item.user_type_name == 'Travel Agency')">
                                                            <span class="badge bg-warning">@{{ index + from }}</span>
                                                        </td> -->
                                                        <!-- <td v-else-if="(item.dl_front_url_status=='2' || item.dl_front_url_status==null) && (item.dl_back_url_status=='2' || item.dl_back_url_status==null) && (item.d_pan_card_url_status=='2' || item.d_pan_card_url_status==null) && (item.d_adhar_card_url_status=='2' || item.d_adhar_card_url_status==null) && (item.user_type_name == 'Driver cum owner')">
                                                            <span class="badge bg-warning">@{{ index + from }}</span>
                                                        </td> -->
                                                        {{-- <td v-else>
                                                            <span class="badge bg-danger">@{{ index + from }}</span>
                                                        </td> --}}
                                                        <td v-if="item.user_interest_status == null">
                                                            <div v-if="item.is_approved =='1'">
                                                                <span class="badge bg-success">@{{ index + from }}</span>
                                                            </div>
                                                            <div v-else-if="item.is_approved =='2'">
                                                                <span class="badge bg-warning">@{{ index + from }}</span>
                                                            </div>
                                                            <div v-else-if="item.is_approved == '0'">
                                                                <span class="badge bg-warning">@{{ index + from }}</span>
                                                            </div>
                                                            <!-- <div v-else>
                                                                    <span
                                                                        class="badge bg-danger">@{{ index + from }}</span>
                                                                </div> -->
                                                        </td>
                                                        <td v-else>
                                                            <div v-if="item.user_interest_status =='1'">
                                                                <span style="background: aliceblue;"
                                                                    class="badge bg-white">@{{ index + from }}</span>
                                                            </div>
                                                            <div v-else-if="item.user_interest_status =='2'">
                                                                <span class="badge bg-danger">@{{ index + from }}</span>
                                                            </div>
                                                            <div v-else-if="item.is_approved =='1'">
                                                                <span class="badge bg-success">@{{ index + from }}</span>
                                                            </div>
                                                            <div v-else-if="item.is_approved =='2'">
                                                                <span class="badge bg-warning">@{{ index + from }}</span>
                                                            </div>
                                                            <!-- <div v-else>
                                                                    <span class="badge bg-danger">@{{ index + from }}</span>
                                                                </div> -->
                                                        </td>
                                                       

                                                        <!-- <td>@{{item.first_name}}</td> -->
                                                        <td class="act-btn" v-if="item.user_type_name == ''">
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.first_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.first_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else-if="item.user_type_name == 'Agent'">
                                                            <a :href="'{{url(Session::get('panel').'/agent/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.first_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Driver cum owner'">
                                                            <a :href="'{{url(Session::get('panel').'/driver_cum_owner/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.first_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else>
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.first_name) }}
                                                            </a>
                                                        </td>

                                                        <!-- <td>@{{item.last_name}}</td> -->

                                                        <td class="act-btn" v-if="item.user_type_name == ''">
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else-if="item.user_type_name == 'Agent'">
                                                            <a :href="'{{url(Session::get('panel').'/agent/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Driver cum owner'">
                                                            <a :href="'{{url(Session::get('panel').'/driver_cum_owner/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else>
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.last_name) }}
                                                            </a>
                                                        </td>

                                                        <td>@{{ item.user_type_name }}</td>
                                                          <td>@{{ item.app_version }}</td>
                                                        <td v-if="item.created_at != '-0001-11-30 00:00:00'">
                                                            @{{ formatDate(item.created_at) }}</td>
                                                        <td v-else>-</td>

                                                        <!-- <td>@{{ item.owner_name }}</td> -->
                                                        <td class="act-btn" v-if="item.user_type_name == ''">
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.owner_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.owner_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else-if="item.user_type_name == 'Agent'">
                                                            <a :href="'{{url(Session::get('panel').'/agent/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.owner_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Driver cum owner'">
                                                            <a :href="'{{url(Session::get('panel').'/driver_cum_owner/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.owner_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else>
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.owner_name) }}
                                                            </a>
                                                        </td>

                                                        <!-- <td>@{{ item.driver_first_name }} @{{ item.driver_last_name }}</td> -->
                                                        <td class="act-btn" v-if="item.user_type_name == ''">
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.driver_first_name) }}
                                                                @{{ capitalize(item.driver_last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.driver_first_name) }}
                                                                @{{ capitalize(item.driver_last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else-if="item.user_type_name == 'Agent'">
                                                            <a :href="'{{url(Session::get('panel').'/agent/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.driver_first_name) }}
                                                                @{{ capitalize(item.driver_last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Driver cum owner'">
                                                            <a :href="'{{url(Session::get('panel').'/driver_cum_owner/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.driver_first_name) }}
                                                                @{{ capitalize(item.driver_last_name) }}
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else>
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View">
                                                                @{{ capitalize(item.driver_first_name) }}
                                                                @{{ capitalize(item.driver_last_name) }}
                                                            </a>
                                                        </td>

                                                        <td v-if="item.user_type_name == 'Agent'"></td>
                                                        <td v-else>@{{ allcharcapitalize(item.vehicle_number) }}</td>

                                                        <td>@{{ item.mobile_number }}</td>

                                                        <td v-if="item.last_updated_id != '0'">@{{item.last_updated_by}}
                                                        </td>
                                                        <td v-else><span class="badge bg-danger">Pending to
                                                                Allocate</span></td>

                                                        <td>@{{ item.subscription_name }}</td>
                                                        <td v-if="item.start_datetime != '-0001-11-30 00:00:00'">
                                                            @{{ formatDate(item.start_datetime) }}</td>
                                                        <td v-else>-</td>
                                                        <td v-if="item.end_datetime != '-0001-11-30 00:00:00'">
                                                            @{{ formatDate(item.end_datetime) }}</td>
                                                        <td v-else>-</td>

                                                        <!-- <td v-if="item.is_otp=='1' || item.is_otp=='0' ">
                                                            <span class="badge bg-pill bg-primary-light">Verified</span>
                                                        </td>
                                                        <td v-else>
                                                            
                                                            <a :href="'{{url(Session::get('panel').'/users/resendotp')}}/'+ item.id" class="btn ripple btn-danger btn-sm">Not Verify</a>
                                                        </td>

                                                        <td v-if="item.user_status=='1'">
                                                            <button type="button" class="btn ripple btn-success btn-sm">Active</button>
                                                        </td>
                                                        <td v-else>
                                                            <button type="button" class="btn ripple btn-danger btn-sm">Inactive</button>
                                                        </td> -->
                                                        <td class="act-btn" v-if="item.user_type_name == ''">
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>
                                                            <a :href="'{{url(Session::get('panel').'/register/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-sm btn-edit"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>
                                                            <a :href="'{{url(Session::get('panel').'/register/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-sm btn-edit"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else-if="item.user_type_name == 'Agent'">
                                                            <a :href="'{{url(Session::get('panel').'/agent/show')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>
                                                            <a :href="'{{url(Session::get('panel').'/register/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-sm btn-edit"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                        </td>
                                                        <td class="act-btn"
                                                            v-else-if="item.user_type_name == 'Driver cum owner'">
                                                            <a :href="'{{url(Session::get('panel').'/driver_cum_owner/show')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>
                                                            <a :href="'{{url(Session::get('panel').'/register/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-sm btn-edit"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                        </td>
                                                        <td class="act-btn" v-else>
                                                            <a :href="'{{url(Session::get('panel').'/register/show')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>
                                                            <a :href="'{{url(Session::get('panel').'/register/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-sm btn-edit"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                        </td>
                                                         <td class="act-btn"
                                                            v-if="item.user_type_name == 'Travel Agency'">
                                                            <a :href="'{{url(Session::get('panel').'/travel/show_v2')}}/'+ item.id"
                                                                title="View" class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a> 
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="pagination d-flex" v-if="items.length">
                                        @include('admin.common.paginate_js')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->

            <div class="row" v-if="!items.length && loaded == true">
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        {{ @trans('user.no_records_to_show') }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Main Content-->

@endsection
@push('pageModals')

@include('admin.components.modal_delete_js')

<div class="modal right filter-modal" id="filter" tabindex="-1" role="dialog" aria-labelledby="filter">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="filter">{{ @trans('user.filter') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-md-12">

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">

                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="submit" class="btn btn-em" @click="filter">{{ @trans('user.update_filter') }}</button>
                <button type="button" class="btn btn-cncl" @click="resetAllFilter">{{ @trans('user.reset_all')
                    }}</button>
            </div>
        </div>
    </div>
</div>
@endpush


@push('pageJs')
<script type="text/javascript">
    function initListUsers() {
        var container = $('div'),
            scrollTo = $('#example');

        /* container.animate({
            scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
        }); */
        container.animate({
            scrollTop: container.scrollTop()
        });

        $(".table-scrollable").freezeTable({
            'columnNum': 2,
            'scrollable': true,
        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.register.index_json',['panel' => Session::get('panel'),'sub'=>$sub])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/register/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.register.destroy',['panel' => Session::get('panel'), 'id' => 1])}}";
    @if (Session:: has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif

    $(function () {
        /*LIne-Chart */
        var ctx = document.getElementById("chartLine").getContext('2d');
        var myChart = new Chart(ctx, {

            data: {
                labels: [<?= $day_name; ?>],
            datasets: [{
                label: 'No Of Users',
                data: [<?= $graph_trans_data; ?>],
            borderWidth: 3,
            backgroundColor: 'transparent',
            borderColor: '#6259ca',
            pointBackgroundColor: '#ffffff',
            pointRadius: 0,
            type: 'line',
        },
                /* {

                    label: 'AMOUNT USED',
                    data: [200, 530, 110, 110, 480, 520, 780, 435, 475, 738, 454, 454, 230, ],
                    borderWidth: 3,
                    backgroundColor: 'transparent',
                    borderColor: 'rgb(183, 179, 220,0.5)',
                    pointBackgroundColor: '#ffffff',
                    pointRadius: 0,
                    type: 'line',
                    borderDash: [7, 3],

                } */
            ]
    },
        options: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            enabled: true,
        },
        tooltips: {
            mode: 'index',
            intersect: false,
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            xAxes: [{
                ticks: {
                    fontColor: "#c8ccdb",
                },
                barPercentage: 0.7,
                display: true,
                gridLines: {
                    color: 'rgba(119, 119, 142, 0.2)',
                    zeroLineColor: 'rgba(119, 119, 142, 0.2)',
                }
            }],
            yAxes: [{
                ticks: {
                    fontColor: "#77778e",
                },
                display: true,
                gridLines: {
                    color: 'rgba(119, 119, 142, 0.2)',
                    zeroLineColor: 'rgba(119, 119, 142, 0.2)',
                },
                ticks: {
                    min: 0,
                    max: <?= $all_joined + 5; ?>,
                stepSize: <?= $all_joined / 2; ?>
                    },
        scaleLabel: {
            display: true,
            labelString: 'Thousands',
            fontColor: 'transparent'
        }
    }]
            },
        legend: {
        display: true,
        width: 30,
        height: 30,
        borderRadius: 50,
        labels: {
            fontColor: "#77778e"
        },
    },
        }
    });
});
</script>
<script type="text/javascript" src="{{ asset('js/admin/register.js') }}"></script>
<script type="text/javascript" src="https://unpkg.com/vue2-datepicker@2.6.4/lib/index.js"></script>

<!-- Circle Progress js-->
<script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
<script src="{{ asset('assets/js/chart-circle.js') }}"></script>
@endpush