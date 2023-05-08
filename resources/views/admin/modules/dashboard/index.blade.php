@extends('admin.layouts.main')

@section('title')
    Dashboard
@endsection

@section('content')
    <style>
        .gm-style-iw button {
            top: 6px !important;
            right: 11px !important;
        }

        .gm-svpc div {
            left: 58% !important;
            top: 116% !important;
        }

        .gm-control-active img {
            margin-left: 22px;
        }
    </style>
    <div class="main-content side-content pt-0">
        <div class="container-fluid">
            <div class="inner-body">

                <!-- Page Header -->
                {{-- <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5"></h2> --}}
                <!-- <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Project Dashboard</li>
                                    </ol> -->
                {{-- </div>
                <div class="d-flex">
                    <div class="justify-content-center"> --}}
                <!-- <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                                        <i class="fe fe-download mr-2"></i> Import
                                        </button> {{ route('admin.dashboard.index', ['panel' => Session::get('panel')]) }} -->
                {{-- <button type="button" onclick='location.href = "{{ route('admin.dashboard.index', ['panel' => Session::get('panel'), 'day']) }}";' class="btn btn-white @if ($filter == 'day') btn-primary @endif btn-icon-text my-2 mr-2">
                            <i class="fe fe-filter mr-2"></i> Day
                        </button>
                        <button type="button" onclick='location.href = "{{ route('admin.dashboard.index', ['panel' => Session::get('panel'), 'week']) }}";' class="btn btn-white @if ($filter == 'week') btn-primary @endif btn-icon-text my-2 mr-2">
                            <i class="fe fe-filter mr-2"></i> Week
                        </button>
                        <button type="button" onclick='location.href = "{{ route('admin.dashboard.index', ['panel' => Session::get('panel'), 'month']) }}";' class="btn btn-white @if ($filter == 'month') btn-primary @endif btn-icon-text my-2 mr-2">
                            <i class="fe fe-filter mr-2"></i> Month
                        </button> --}}
                <!-- <button type="button" class="btn btn-primary my-2 btn-icon-text">
                                        <i class="fe fe-download-cloud mr-2"></i> Download Report
                                        </button> -->
                {{-- </div>
                </div>
            </div> --}}
                <!-- End Page Header -->
                <!-- Taxi live location start -->
                <div class="row" style="    margin: 10px 0 0 0;">
                    <div class="col-md-12">
                        <div class="card card-box">
                            <form method="post"
                                action="{{ route('admin.dashboard.filter', ['panel' => Session::get('panel')]) }}"
                                class="" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header">
                                    <div class="row" style="display: flex;">
                                        <div class="col-lg-3"></div>
                                        <div class="col-lg-2">
                                            <select class="form-control" id="vehicle_type_id" name="vehicle_type_id">
                                                <option value="" selected>Select Vehicle Types</option>
                                                <?php foreach ($vehicle_types as $r) { ?>
                                                <option value="<?= $r['id'] ?>" @if ($vehicleType == $r['id'])
                                                    selected
                                                    @endif><?= $r['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <select class="form-control" id="driver_type_id" name="driver_type_id">
                                                <option value="" selected>Select Driver Type</option>
                                                <option value="1"@if ($driverType == 1) selected @endif>
                                                    Online</option>
                                                <option value="2"@if ($driverType == 2) selected @endif>
                                                    Offline</option>
                                                <option value="3"@if ($driverType == 3) selected @endif>On
                                                    Going Trip</option>
                                            </select>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="table-top-panel d-flex align-items-center">
                                                <div
                                                    class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                                    <button type="submit" class="btn ripple btn-primary"><i
                                                            class="ti-search" data-toggle="tooltip" title=""
                                                            data-original-title="ti-search"></i></button>
                                                    <!-- <i class="material-icons" style="cursor: pointer;" v-if="start_date" @click="clearSearch">close</i> -->
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-lg-3"
                                            style="float:right;display: flex;justify-content: space-evenly;">
                                            <div class="onlineDriver">
                                                <div style="display:flex;">
                                                    <!-- <i class="ti-search" style="padding-right:5px;"></i> -->
                                                    <div style="padding:2px 4px 7px 2px;">
                                                        <div class="square1"
                                                            style=" height: 1rem;width:1rem;background-color: green;"></div>
                                                    </div>
                                                    <b>{{ $onlineDrivers }}</b>
                                                </div>
                                                <div>
                                                    <h6>Online</h6>
                                                </div>
                                            </div>
                                            <div class="offlineDriver">
                                                <div style="display:flex;">
                                                    <div style="padding:2px 4px 7px 2px;">
                                                        <div class="square"
                                                            style=" height: 1rem;width:1rem;background-color: grey;"></div>
                                                    </div>
                                                    <!-- <i class="ti-search" style="padding-right:5px;"></i> -->
                                                    <b>{{ $offlineDrivers }}</b>
                                                </div>
                                                <div>
                                                    <h6>Offline</h6>
                                                </div>
                                            </div>
                                            <div class="onGoingTripDriver">
                                                <div style="display:flex;">
                                                    <div style="padding:2px 4px 7px 2px;">
                                                        <div class="square"
                                                            style=" height: 1rem;width:1rem;background-color: blue;"></div>
                                                    </div>
                                                    <!-- <i class="ti-search" style="padding-right:5px;"></i> -->
                                                    <b>{{ $onGoingDrivers }}</b>
                                                </div>
                                                <div>
                                                    <h6>On Going</h6>
                                                </div>
                                            </div>
                                            <!-- <p style="color:grey; font-weight:bold;">{{ $offlineDrivers . ' Offline Drivers' }}</p>
                                                <p style="color:blue; font-weight:bold;">{{ $onGoingDrivers . ' On Going Trip Drivers' }}</p> -->
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="card-body" style="max-height:22rem;">
                                <div id="map" style="width: auto; height: 19rem; position: relative; overflow: hidden;"></div>
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Taxi live location end -->

                <!--Row-->
                <div class="row row-sm">

                    <div class="col-sm-12 col-lg-12 col-xl-8">
                        <!--Row-->
                        {{-- <div class="row row-sm  mt-lg-4">
                        <div class="col-sm-12 col-lg-12 col-xl-12">
                            <div class="card bg-primary custom-card card-box">
                                <div class="card-body p-4">
                                    <div class="row align-items-center">
                                        <div class="offset-xl-3 offset-sm-6 col-xl-8 col-sm-6 col-12 img-bg ">
                                            <h4 class="d-flex  mb-3">
                                                <span class="font-weight-bold text-white ">Mr. Yogesh</span>
                                            </h4>
                                            <p class="tx-white-7 mb-1">You have two projects to finish, you had
                                                completed <b class="text-warning">57%</b> from your montly level,
                                                Keep going to your level
                                        </div>
                                        <img src="{{ asset('assets/img/pngs/work3.png') }}" alt="user-img">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> --}}
                        <!--Row -->

                        <!--Row-->
                        <div class="row row-sm" style="margin-top:26px;">
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body"
                                        onclick='location.href = "{{ route('admin.transactions.index', ['panel' => Session::get('panel')]) }}";'
                                        role="button">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                                    enable-background="new 0 0 24 24" height="24" viewBox="0 0 24 24"
                                                    width="24">
                                                    <g>
                                                        <rect height="14" opacity=".3" width="14"
                                                            x="5" y="5" />
                                                        <g>
                                                            <rect fill="none" height="24" width="24" />
                                                            <g>
                                                                <path
                                                                    d="M19,3H5C3.9,3,3,3.9,3,5v14c0,1.1,0.9,2,2,2h14c1.1,0,2-0.9,2-2V5C21,3.9,20.1,3,19,3z M19,19H5V5h14V19z" />
                                                                <rect height="5" width="2" x="7"
                                                                    y="12" />
                                                                <rect height="10" width="2" x="15"
                                                                    y="7" />
                                                                <rect height="3" width="2" x="11"
                                                                    y="14" />
                                                                <rect height="2" width="2" x="11"
                                                                    y="10" />
                                                            </g>
                                                        </g>
                                                    </g>
                                                </svg>
                                            </div>
                                            <div class="card-item-title mb-2">
                                                <label class="main-content-label tx-13 font-weight-bold mb-1">Total
                                                    Revenue</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold text-break">
                                                        ₹{{ number_format($total_revenue, 2) }}</h4>
                                                    <!-- <small><b class="text-success">55%</b> higher</small> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body"
                                        onclick='location.href = "{{ route('admin.register.index', ['panel' => Session::get('panel'), 'sub' => 'subscribe']) }}";'
                                        role="button">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24"
                                                    viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8 0 1.82.62 3.49 1.64 4.83 1.43-1.74 4.9-2.33 6.36-2.33s4.93.59 6.36 2.33C19.38 15.49 20 13.82 20 12c0-4.41-3.59-8-8-8zm0 9c-1.94 0-3.5-1.56-3.5-3.5S10.06 6 12 6s3.5 1.56 3.5 3.5S13.94 13 12 13z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.number_of_users_subscribed') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold">{{ $number_of_users_subscribed }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body"
                                        onclick='location.href = "{{ route('admin.trip.index', ['panel' => Session::get('panel'), 'param' => 'complete']) }}";'
                                        role="button">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                                    height="24" viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.total_trips_completed') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold">{{ $total_trips_completed }}</h4>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                                    height="24" viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.agents_total_earning') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold text-break">
                                                        ₹{{ number_format($agents_total_earning, 2) }}</h4>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg"
                                                    height="24" viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.driver_total_earning') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold text-break">
                                                        ₹{{ number_format($driver_total_earning, 2) }}</h4>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24"
                                                    viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8 0 1.82.62 3.49 1.64 4.83 1.43-1.74 4.9-2.33 6.36-2.33s4.93.59 6.36 2.33C19.38 15.49 20 13.82 20 12c0-4.41-3.59-8-8-8zm0 9c-1.94 0-3.5-1.56-3.5-3.5S10.06 6 12 6s3.5 1.56 3.5 3.5S13.94 13 12 13z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.Customers_total_spent') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold text-break">
                                                        ₹{{ number_format($Customers_total_spent, 2) }}</h4>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body"
                                        onclick='location.href = "{{ route('admin.register.index', ['panel' => Session::get('panel'), 'sub' => 'pending']) }}";'
                                        role="button">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24"
                                                    viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8 0 1.82.62 3.49 1.64 4.83 1.43-1.74 4.9-2.33 6.36-2.33s4.93.59 6.36 2.33C19.38 15.49 20 13.82 20 12c0-4.41-3.59-8-8-8zm0 9c-1.94 0-3.5-1.56-3.5-3.5S10.06 6 12 6s3.5 1.56 3.5 3.5S13.94 13 12 13z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title  mb-2">
                                                <label
                                                    class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.pending_approvals_user') }}</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold">{{ $pending_approved_users }}</h4>

                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6 col-lg-6 col-xl-4">
                                <div class="card custom-card">
                                    <div class="card-body"
                                        onclick='location.href = "{{ route('admin.register.index', ['panel' => Session::get('panel'), 'sub' => 'complete']) }}";'
                                        role="button">
                                        <div class="card-item">
                                            <div class="card-item-icon card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" height="24"
                                                    viewBox="0 0 24 24" width="24">
                                                    <path d="M0 0h24v24H0V0z" fill="none" />
                                                    <path
                                                        d="M12 4c-4.41 0-8 3.59-8 8 0 1.82.62 3.49 1.64 4.83 1.43-1.74 4.9-2.33 6.36-2.33s4.93.59 6.36 2.33C19.38 15.49 20 13.82 20 12c0-4.41-3.59-8-8-8zm0 9c-1.94 0-3.5-1.56-3.5-3.5S10.06 6 12 6s3.5 1.56 3.5 3.5S13.94 13 12 13z"
                                                        opacity=".3" />
                                                    <path
                                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zM7.07 18.28c.43-.9 3.05-1.78 4.93-1.78s4.51.88 4.93 1.78C15.57 19.36 13.86 20 12 20s-3.57-.64-4.93-1.72zm11.29-1.45c-1.43-1.74-4.9-2.33-6.36-2.33s-4.93.59-6.36 2.33C4.62 15.49 4 13.82 4 12c0-4.41 3.59-8 8-8s8 3.59 8 8c0 1.82-.62 3.49-1.64 4.83zM12 6c-1.94 0-3.5 1.56-3.5 3.5S10.06 13 12 13s3.5-1.56 3.5-3.5S13.94 6 12 6zm0 5c-.83 0-1.5-.67-1.5-1.5S11.17 8 12 8s1.5.67 1.5 1.5S12.83 11 12 11z" />
                                                </svg>
                                            </div>
                                            <div class="card-item-title mb-2">
                                                <label class="main-content-label tx-13 font-weight-bold mb-1">Approved
                                                    Users</label>
                                                <span class="d-block tx-12 mb-0 text-muted"></span>
                                            </div>
                                            <div class="card-item-body">
                                                <div class="card-item-stat">
                                                    <h4 class="font-weight-bold">{{ $approved_users }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                            <div class="card custom-card">
                                                <div class="card-body" onclick='location.href = "{{ route('admin.register.index', ['panel' => Session::get('panel'), 'sub' => 'pending']) }}";' role="button">
                                                    <div class="card-item">
                                                        <div class="card-item-icon card-icon">
                                                            <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/></svg>
                                                        </div>
                                                        <div class="card-item-title  mb-2">
                                                            <label class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.pending_approvals_document') }}</label>
                                                            <span class="d-block tx-12 mb-0 text-muted">Previous month vs this months</span>
                                                        </div>
                                                        <div class="card-item-body">
                                                            <div class="card-item-stat">
                                                                <h4 class="font-weight-bold">{{ $pending_approvals_user }}</h4>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                            <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                            <div class="card custom-card">
                                                <div class="card-body" onclick='location.href = "{{ route('admin.vehicles.index', ['panel' => Session::get('panel'), 'param' => 'pending']) }}";' role="button">
                                                    <div class="card-item">
                                                        <div class="card-item-icon card-icon">
                                                            <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/></svg>
                                                        </div>
                                                        <div class="card-item-title  mb-2">
                                                            <label class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.pending_approvals_vehicle') }}</label>
                                                            <span class="d-block tx-12 mb-0 text-muted">Previous month vs this months</span>
                                                        </div>
                                                        <div class="card-item-body">
                                                            <div class="card-item-stat">
                                                                <h4 class="font-weight-bold">{{ $pending_approvals_vehicle }}</h4>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                            <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                            <div class="card custom-card">
                                                <div class="card-body" onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'param' => 'pending']) }}";' role="button">
                                                    <div class="card-item">
                                                        <div class="card-item-icon card-icon">
                                                            <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/></svg>
                                                        </div>
                                                        <div class="card-item-title  mb-2">
                                                            <label class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.pending_approvals_driver') }}</label>
                                                            <span class="d-block tx-12 mb-0 text-muted">Previous month vs this months</span>
                                                        </div>
                                                        <div class="card-item-body">
                                                            <div class="card-item-stat">
                                                                <h4 class="font-weight-bold">{{ $pending_approvals_driver }}</h4>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->

                            <!-- <div class="col-sm-12 col-md-12 col-lg-12 col-xl-4">
                                            <div class="card custom-card">
                                                <div class="card-body">
                                                    <div class="card-item">
                                                        <div class="card-item-icon card-icon">
                                                            <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" height="24" viewBox="0 0 24 24" width="24"><path d="M0 0h24v24H0V0z" fill="none"/><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm1.23 13.33V19H10.9v-1.69c-1.5-.31-2.77-1.28-2.86-2.97h1.71c.09.92.72 1.64 2.32 1.64 1.71 0 2.1-.86 2.1-1.39 0-.73-.39-1.41-2.34-1.87-2.17-.53-3.66-1.42-3.66-3.21 0-1.51 1.22-2.48 2.72-2.81V5h2.34v1.71c1.63.39 2.44 1.63 2.49 2.97h-1.71c-.04-.97-.56-1.64-1.94-1.64-1.31 0-2.1.59-2.1 1.43 0 .73.57 1.22 2.34 1.67 1.77.46 3.66 1.22 3.66 3.42-.01 1.6-1.21 2.48-2.74 2.77z" opacity=".3"/><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.31-8.86c-1.77-.45-2.34-.94-2.34-1.67 0-.84.79-1.43 2.1-1.43 1.38 0 1.9.66 1.94 1.64h1.71c-.05-1.34-.87-2.57-2.49-2.97V5H10.9v1.69c-1.51.32-2.72 1.3-2.72 2.81 0 1.79 1.49 2.69 3.66 3.21 1.95.46 2.34 1.15 2.34 1.87 0 .53-.39 1.39-2.1 1.39-1.6 0-2.23-.72-2.32-1.64H8.04c.1 1.7 1.36 2.66 2.86 2.97V19h2.34v-1.67c1.52-.29 2.72-1.16 2.73-2.77-.01-2.2-1.9-2.96-3.66-3.42z"/></svg>
                                                        </div>
                                                        <div class="card-item-title  mb-2">
                                                            <label class="main-content-label tx-13 font-weight-bold mb-1">{{ @trans('dashboard.pending_approvals_payment') }}</label>
                                                            <span class="d-block tx-12 mb-0 text-muted">Previous month vs this months</span>
                                                        </div>
                                                        <div class="card-item-body">
                                                            <div class="card-item-stat">
                                                                <h4 class="font-weight-bold">₹{{ number_format($pending_approvals_payment, 2) }}</h4>
                                                                
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                        </div>
                        <!--End row-->

                        <!--row-->
                        <div class="row row-sm">
                            <div class="col-sm-12 col-lg-12 col-xl-12">
                                <div class="card custom-card overflow-hidden">
                                    <div class="card-header border-bottom-0">
                                        <div>
                                            <label class="main-content-label mb-2">Subscribed Users</label>
                                            <!-- <span class="d-block tx-12 mb-0 text-muted">The Project Budget is a tool used by project managers to estimate the total cost of a project</span> -->
                                        </div>
                                    </div>
                                    <div class="card-body pl-0">
                                        <div class>
                                            <div class="container">
                                                <canvas id="chartLine" class="chart-dropshadow2 ht-250"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div><!-- col end -->
                            <!-- <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="card custom-card overflow-hidden">
                                                <div class="card-header  border-bottom-0 pb-0">
                                                    <div>
                                                        <div class="d-flex">
                                                            <label class="main-content-label my-auto pt-2">Today tasks</label>
                                                            <div class="ml-auto mt-3 d-flex">
                                                                <div class="mr-3 d-flex text-muted tx-13"><span class="legend bg-primary rounded-circle"></span>Project</div>
                                                                <div class="d-flex text-muted tx-13"><span class="legend bg-light rounded-circle"></span>Inprogress</div>
                                                            </div>
                                                        </div>
                                                        <span class="d-block tx-12 mt-2 mb-0 text-muted"> UX UI & Backend Developement. </span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-sm-6 my-auto">
                                                            <h6 class="mb-3 font-weight-normal">Project-Budget</h6>
                                                            <div class="text-left">
                                                                <h3 class="font-weight-bold mr-3 mb-2 text-primary">$5,240</h3>
                                                                <p class="tx-13 my-auto text-muted">May 28 - June 01 (2018)</p>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 my-auto">
                                                            <div class="forth circle">
                                                                <div class="chart-circle-value circle-style"><div class="tx-16 font-weight-bold">75%</div></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                            <!-- col end -->
                            <!-- <div class="col-sm-12 col-md-6 col-lg-6 col-xl-6">
                                            <div class="card custom-card">
                                                <div class="card-header  border-bottom-0 pb-0">
                                                    <div>
                                                        <div class="d-flex">
                                                            <label class="main-content-label my-auto pt-2">Top Inquiries</label>
                                                        </div>
                                                        <span class="d-block tx-12 mt-2 mb-0 text-muted"> project work involves a group of students investigating . </span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row mt-1">
                                                        <div class="col-5">
                                                            <span class="">Brand identity</span>
                                                        </div>
                                                        <div class="col-4 my-auto">
                                                            <div class="progress ht-6 my-auto">
                                                                <div class="progress-bar ht-6 wd-80p" role="progressbar"  aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="d-flex">
                                                                <span class="tx-13"><i class="text-success fe fe-arrow-up"></i><b>24.75%</b></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-5">
                                                            <span class="">UI & UX design</span>
                                                        </div>
                                                        <div class="col-4 my-auto">
                                                            <div class="progress ht-6 my-auto">
                                                                <div class="progress-bar ht-6 wd-70p" role="progressbar"  aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="d-flex">
                                                                <span class="tx-13"><i class="text-danger fe fe-arrow-down"></i><b>12.34%</b></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-5">
                                                            <span class="">Product design</span>
                                                        </div>
                                                        <div class="col-4 my-auto">
                                                            <div class="progress ht-6 my-auto">
                                                                <div class="progress-bar ht-6 wd-40p" role="progressbar"  aria-valuenow="15" aria-valuemin="0" aria-valuemax="100"></div>
                                                            </div>
                                                        </div>
                                                        <div class="col-3">
                                                            <div class="d-flex">
                                                                <span class="tx-13"><i class="text-success  fe fe-arrow-up"></i><b>12.75%</b></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                            <!-- col end -->
                            <!-- col end -->
                        </div><!-- Row end -->
                    </div><!-- col end -->
                    <div class="col-sm-12 col-lg-12 col-xl-4 mt-xl-4">
                        <div class="card custom-card card-dashboard-calendar pb-0">
                            <label class="main-content-label mb-2 pt-1">Recent transcations</label>
                            <span class="d-block tx-12 mb-2 text-muted">Projects where development work is on
                                completion</span>
                            <table class="table table-hover m-b-0 transcations mt-2">
                                <tbody>
                                    <?php foreach ($trans as $val) { ?>
                                    <tr>
                                        <td class="wd-5p">
                                            <div class="main-img-user avatar-md">
                                                <img alt="avatar" class="rounded-circle mr-3"
                                                    src="{{ asset('assets/img/users/5.jpg') }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-middle ml-3">
                                                <div class="d-inline-block">
                                                    <h6 class="mb-1"><?= $val['first_name'] . ' ' . $val['last_name'] ?>
                                                    </h6>
                                                    <p class="mb-0 tx-13 text-muted"><?= $val['trans_type_name'] ?></p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-right">
                                            <div class="d-inline-block">
                                                <h6 class="mb-2 tx-15 font-weight-semibold">₹<?= $val['price'] ?><i
                                                        class="ml-2 text-success m-l-10"></i></h6>
                                                <p class="mb-0 tx-11 text-muted">
                                                    <?= date('d F Y', strtotime($val['start_datetime'])) ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="row row-sm">
                                                <div class="col-6">
                                                    <div class="card-item-title">
                                                        <label class="main-content-label tx-13 font-weight-bold mb-2">Project Launch</label>
                                                        <span class="d-block tx-12 mb-0 text-muted">the project is going to launch</span>
                                                    </div>
                                                    <p class="mb-0 tx-24 mt-2"><b class="text-primary">145 days</b></p>
                                                    <a href="#" class="text-muted">12 Monday, Oct 2020 </a>
                                                </div>
                                                <div class="col-6">
                                                    <img src="{{ asset('assets/img/pngs/work.png') }}" alt="image" class="best-emp">
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                        <!-- <div class="card custom-card">
                                        <div class="card-header border-bottom-0 pb-0 d-flex pl-3 ml-1">
                                            <div>
                                                <label class="main-content-label mb-2 pt-2">On goiong projects</label>
                                                <span class="d-block tx-12 mb-2 text-muted">Projects where development work is on completion</span>
                                            </div>
                                        </div>
                                        <div class="card-body pt-2 mt-0">
                                            <div class="list-card">
                                                <div class="d-flex">
                                                    <div class="demo-avatar-group my-auto float-right">
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/1.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/2.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/3.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/4.jpg') }}">
                                                        </div>
                                                        <div class="">Design team</div>
                                                    </div>
                                                    <div class="ml-auto float-right">
                                                        <div class="">
                                                            <a href="#" class="option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fe fe-more-horizontal"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right" style="">
                                                                <a class="dropdown-item" href="#">Today</a>
                                                                <a class="dropdown-item" href="#">Last Week</a>
                                                                <a class="dropdown-item" href="#">Last Month</a>
                                                                <a class="dropdown-item" href="#">Last Year</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-item mt-4">
                                                    <div class="card-item-icon bg-transparent card-icon">
                                                        <span class="peity-donut" data-peity='{ "fill": ["#6259ca", "rgba(204, 204, 204,0.3)"], "innerRadius": 15, "radius": 20}'>6/7</span>
                                                    </div>
                                                    <div class="card-item-body">
                                                        <div class="card-item-stat">
                                                            <small class="tx-10 text-primary font-weight-semibold">25 August 2020</small>
                                                            <h6 class=" mt-2">Mobile app design</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="list-card mb-0">
                                                <div class="d-flex">
                                                    <div class="demo-avatar-group my-auto float-right">
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/5.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/6.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/7.jpg') }}">
                                                        </div>
                                                        <div class="main-img-user avatar-xs">
                                                            <img alt="avatar" class="rounded-circle" src="{{ asset('assets/img/users/8.jpg') }}">
                                                        </div>
                                                        <div class="">Design team</div>
                                                    </div>
                                                    <div class="ml-auto float-right">
                                                        <div class="">
                                                            <a href="#" class="option-dots" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fe fe-more-horizontal"></i></a>
                                                            <div class="dropdown-menu dropdown-menu-right" style="">
                                                                <a class="dropdown-item" href="#">Today</a>
                                                                <a class="dropdown-item" href="#">Last Week</a>
                                                                <a class="dropdown-item" href="#">Last Month</a>
                                                                <a class="dropdown-item" href="#">Last Year</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="card-item mt-4">
                                                    <div class="card-item-icon bg-transparent card-icon">
                                                        <span class="peity-donut" data-peity='{ "fill": ["#6259ca", "rgba(204, 204, 204,0.3)"], "innerRadius": 15, "radius": 20}'>5/7</span>
                                                    </div>
                                                    <div class="card-item-body">
                                                        <div class="card-item-stat">
                                                            <small class="tx-10 text-primary font-weight-semibold">12 JUNE 2020</small>
                                                            <h6 class=" mt-2">Website Redesign</h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                        <!-- <div class="card custom-card">
                                        <div class="card-body">
                                            <div class="d-flex">
                                                <label class="main-content-label my-auto">Website Design</label>
                                                <div class="ml-auto  d-flex">
                                                    <div class="mr-3 d-flex text-muted tx-13">Running</div>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <div>
                                                    <span class="tx-15 text-muted">Task completed : 7/10</span>
                                                </div>
                                                <div class="container mt-2 mb-2">
                                                <canvas id="bar-chart" class="ht-180"></canvas>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col">
                                                    <div class="mt-4">
                                                        <div class="d-flex mb-2">
                                                            <h5 class="tx-15 my-auto text-muted font-weight-normal">Client :</h5>
                                                            <h5 class="tx-15 my-auto ml-3">John Deo</h5>
                                                        </div>
                                                        <div class="d-flex mb-0">
                                                            <h5 class="tx-13 my-auto text-muted font-weight-normal">Deadline :</h5>
                                                            <h5 class="tx-13 my-auto text-muted ml-2">25 Dec 2020</h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col col-auto">
                                                    <div class="mt-3">
                                                        <div class="">
                                                            <img alt="" class="ht-50" src="{{ asset('assets/img/media/project-logo.png') }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->
                    </div><!-- col end -->
                </div><!-- Row end -->
            </div>
        </div>
    </div>
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
                    <button type="submit" class="btn btn-em"
                        @click="filter">{{ @trans('user.update_filter') }}</button>
                    <button type="button" class="btn btn-cncl"
                        @click="resetAllFilter">{{ @trans('user.reset_all') }}</button>
                </div>
            </div>
        </div>
    </div>
@endpush


@push('pageJs')
    <!-- google map -->
    <script type="text/javascript">
        function initMap() {
            const myLatLng = {
                lat: 23.0225,
                lng: 72.5714
            };
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 9,
                center: myLatLng,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                gestureHandling: "cooperative",
                // scrollwheel: scrollable,
                // draggable: draggable,
                styles: [{
                    "stylers": [{
                        "saturation": -100
                    }]
                }],
            });
            var drivers = <?php echo $allDrivers; ?>;
            var locations = [];


            drivers.forEach(function(element, index) {
                if (element.is_online == '1') {
                    var online = 'Online';
                } else {
                    var online = 'Offline';
                }
                if (element.is_busy == '1') {
                    var busy = 'Busy';
                } else {
                    var busy = 'Available';
                }
                var driver = {
                    title: 'Name :- ' + element.first_name + ' ' + element.last_name + '; Mobile Number:- ' +
                        element.mobile_numebr + '; Online/Offline :- ' + online + '; Busy/Available :- ' +
                        busy + ';',
                    position: {
                        lat: parseFloat(element.latitude),
                        lng: parseFloat(element.longitude)
                    },
                    icon: {
                        url: element.icon,
                        size: new google.maps.Size(25, 25),
                    },
                    contentString: '<div id="content">' +
                        '<div id="siteNotice">' +
                        "</div>" +
                        '<h5 id="firstHeading" class="firstHeading" style="padding-top: 4px;text-align: center;border-bottom: 1px solid grey;padding-bottom: 6px;background:#25233c;border-radius:5px;color:white;">Drivers Information</h5>' +
                        '<div id="bodyContent">' +
                        '<a href="https://admin.pulpitmobility.com/super-admin/driver/show/' + element.user_id +
                        '"> <b style="font-weight: 900;">Driver Name:- </b>' + element.first_name + ' ' +
                        element.last_name + '</a>' +
                        '<br><br><a href="http://admin.pulpitmobility.com/super-admin/vehicles/show/' + element
                        .vehicle_id + '"> <b style="font-weight: 900;">Vehicle Number:- </b>' + element
                        .vehicle_number + '</a>' +
                        // '<br><br><b style="font-weight: 900;">Last ping location:- </b> Surat' +
                        '<br><br><b style="font-weight: 900;">Online Hours/ Offline Since:- </b>' + element
                        .times +
                        "</p>" +
                        "</div>" +
                        "</div>"
                };
                locations.push(driver);
            });
            locations.forEach(function(element, index) {
                const infowindow = new google.maps.InfoWindow({
                    content: element.contentString,
                    ariaLabel: "Uluru",
                });
                const marker = new google.maps.Marker({
                    position: element.position,
                    map: map,
                    title: element.title,
                    icon: element.icon,
                });
                marker.addListener("click", () => {
                    infowindow.open({
                        anchor: marker,
                        map,
                    });
                });
            });
        }
        window.initMap = initMap;
    </script>
    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&callback=initMap"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=myMap"></script> -->
    <script src="{{ asset('assets/js/map/modernizr.min.js') }}"></script>
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCxFnBNeaJ1TRuA-vCu6qhqpgU2F5c5bgM&callback=initMap" async
        defer></script> -->
    <!-- <script src="{{ asset('assets/js/map/gmap-home.js') }}"></script> -->



    <!-- end js include path -->
    <script type="text/javascript">
        function initListUsers() {
            $(".table-data .table-responsive").freezeTable({
                'columnNum': 3,
                'scrollable': true,
            });
            // JS for highlight column and row on Table hover start
            /* $(".table-data table td").hover(function() {
                $(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).addClass('highlight');
            },
            function() {
                $(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).removeClass('highlight');
            }); */
        }
    </script>
    <script>
        var user_indexUrlJson = "{{ route('admin.dashboard.index_json', ['panel' => Session::get('panel')]) }}";
        var user_deleteUrl = "{{ route('admin.users.destroy', ['panel' => Session::get('panel'), 'id' => 1]) }}";
        @if (Session::has('message'))
            Snackbar.show({
                pos: 'bottom-right',
                text: "{!! session('message') !!}",
                actionText: 'Okay'
            });
        @endif

        $(function() {
            /*LIne-Chart */
            var ctx = document.getElementById("chartLine").getContext('2d');
            var myChart = new Chart(ctx, {

                data: {
                    labels: [<?= $day_name ?>],
                    datasets: [{
                            label: 'No Of Subscribed Users',
                            data: [<?= $graph_trans_data ?>],
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
                                max: <?= $total_subscription_user + 5 ?>,
                                stepSize: <?= $total_subscription_user / 2 ?>
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

    <!-- Circle Progress js-->
    <script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>

    <!-- Internal Dashboard js-->
    <!-- <script src="{{ asset('assets/js/index.js') }}"></script> -->
@endpush
