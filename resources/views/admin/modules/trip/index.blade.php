@extends('admin.layouts.main')

@section('title')
    Trip
@endsection

@section('content')
    <style lang="">
        .count_style {
            background: #EAEDF7 !important;
            border-radius: 10px !important;
            padding: 18px !important;
        }

        .note-editable {
            height: 200px !important;
        }

        .trip_box {
            display: flex;
            justify-content: space-evenly !important;
        }
    </style>
    <!-- Main Content-->
    <div class="main-content side-content pt-0">
        <loading :active.sync="isLoading"></loading>

        <div class="container-fluid">
            <div class="inner-body">

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('trip.trip') }}</h2>
                        <!-- <ol class="breadcrumb">
                                                                                                                            <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                                                                                                                            <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                                                                                                                        </ol> -->
                    </div>
                    @php
                        $states = App\Models\States::all();
                        if (!empty($trip) && $trip['state'] != 0) {
                            $cities = $trip['cities'];
                        } else {
                            $cities = App\Models\Cities::all();
                        }
                    @endphp
                    <form method="post" action="{{ route('trip_filter', ['dropdown-filter', $data_trip['trip_for']]) }}"
                        class="" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                        <div class="row">
                            @csrf
                            @php
                                $vehicle_type = App\Models\VehicleTypes::all();
                            @endphp
                            <div class="col-sm-2 col-lg-2 col-xl-2">
                                <div class="form-group">
                                    <select class="form-control" name="trip_from" id="trip_from" value=""
                                        style="">
                                        <option selected>Trip From</option>
                                        <option value="1"
                                            {{ !empty($trip) ? ($trip['trip_from'] != 0 ? ($trip['trip_from'] == '1' ? 'selected' : '') : '') : '' }}>
                                            Rider</option>
                                        <option value="2"
                                            {{ !empty($trip) ? ($trip['trip_from'] != 0 ? ($trip['trip_from'] == '2' ? 'selected' : '') : '') : '' }}>
                                            partner</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-lg-2 col-xl-2">
                                <div class="form-group">
                                    <select class="form-control" name="select_state" id="select_state" value=""
                                        onclick="select_state_city()" style="">
                                        <option selected value="0">Select State</option>
                                        @foreach ($states as $state_item)
                                            <option value="{{ $state_item->name }}"
                                                {{ !empty($trip) ? ($trip['state'] != 0 ? ($trip['state'] == $state_item->name ? 'selected' : '') : '') : '' }}>
                                                {{ $state_item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-lg-2 col-xl-2">
                                <div class="form-group">
                                    <select class="form-control" name="select_city" id="select_city" value=""
                                        style="">

                                        <option selected value="0">Select City</option>
                                        @if (!empty($trip['city']))
                                            @foreach ($cities as $city_item)
                                                <option value="{{ $city_item->name }}"
                                                    {{ !empty($trip) ? ($trip['city'] != 0 ? ($trip['city'] == $city_item->name ? 'selected' : '') : '') : '' }}>
                                                    {{ $city_item->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-3 col-lg-3 col-xl-3">


                                <div class="form-group">
                                    <select class="form-control" name="cab_type_wise" id="cab_type_wise" value=""
                                        style="">
                                        <option selected value="0">Select Cab Type Wise</option>
                                        @foreach ($vehicle_type as $vehicle_type)
                                            <option value="{{ $vehicle_type->id }}"
                                                {{ !empty($trip) ? ($trip['cab_type_wise'] != 0 ? ($trip['cab_type_wise'] == $vehicle_type->id ? 'selected' : '') : '') : '' }}>
                                                {{ $vehicle_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-2 col-lg-2 col-xl-2">
                                <div class="form-group">
                                    <select class="form-control" name="trip_type_wise" id="trip_type_wise" value="">
                                        <option selected value="0">Select Trip Type Wise</option>
                                        <option value="Local"
                                            {{ !empty($trip) ? ($trip['trip_type_wise'] != 0 ? ($trip['trip_type_wise'] == 'Local' ? 'selected' : '') : '') : '' }}>
                                            Local</option>
                                        <option value="Rental"
                                            {{ !empty($trip) ? ($trip['trip_type_wise'] != 0 ? ($trip['trip_type_wise'] == 'Rental' ? 'selected' : '') : '') : '' }}>
                                            Rental</option>
                                        <option value="Outstation"
                                            {{ !empty($trip) ? ($trip['trip_type_wise'] != 0 ? ($trip['trip_type_wise'] == 'Outstation' ? 'selected' : '') : '') : '' }}>
                                            Outstation</option>
                                        <option value="Live"
                                            {{ !empty($trip) ? ($trip['trip_type_wise'] != 0 ? ($trip['trip_type_wise'] == 'Live' ? 'selected' : '') : '') : '' }}>
                                            Live</option>
                                        <option value="Bidding"
                                            {{ !empty($trip) ? ($trip['trip_type_wise'] != 0 ? ($trip['trip_type_wise'] == 'Bidding' ? 'selected' : '') : '') : '' }}>
                                            Bidding</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-1 col-lg-1 col-xl-1">
                                <div class="form-group">
                                    <div class="table-top-panel d-flex align-items-center">
                                        <div
                                            class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                            {{-- <a onclick='location.href = "{{ url('/admin/trip/filter/serach-filter') }}"'> --}}
                                            <button type="submit" class="btn ripple btn-primary" id=""><i
                                                    class="ti-search" data-toggle="tooltip" title=""
                                                    data-original-title="ti-search"></i></button>
                                            {{-- </a> --}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="d-flex" style="justify-content: flex-end !important;">
                    <div class="justify-content-center">
                        <a class="btn btn-primary my-2 btn-icon-text"
                            href="{{ route('admin.trip.create', ['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('trip.add_trip') }} </a>
                    </div>
                </div>
                <!-- End Page Header -->

                <div class="row abc col-sm-12 col-lg-12 col-xl-12"
                    style="background: white;padding:2rem 1rem 2rem 1rem;font-size: larger;
                display: flex;border-radius:10px;
                text-align: center;font-weight: 500;justify-content: space-around;">
                    <div style="align-self: center;">
                        <div>Completed Trips</div>
                    </div>


                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/today-completed/' . $data_trip['trip_for']) }}"'>
                        <div>{{ $data_trip['today_trip'] }}</div>
                        <div> Today Trips</div>
                    </div>
                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/yesterday-completed/' . $data_trip['trip_for']) }}"'>
                        <div>{{ $data_trip['yesterday_trip'] }}</div>
                        <div> YesterDay Trips</div>
                    </div>
                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/last-week-completed/' . $data_trip['trip_for']) }}"'>
                        <div>{{ $data_trip['last_week_trip'] }}</div>
                        <div>Weekly Trips</div>
                    </div>
                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/current-month-completed/' . $data_trip['trip_for']) }}"'>
                        <div>{{ $data_trip['current_month_trip'] }}</div>
                        <div>This Month Trip</div>
                    </div>
                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/last-month-completed/' . $data_trip['trip_for']) }}"'>
                        <div>{{ $data_trip['last_month_trip'] }}</div>
                        <div>Last Month Trip</div>
                    </div>
                    <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                        onclick='location.href = "{{ url('/admin/trip/filter/all-completed/' . $data_trip['trip_for']) }}"'>

                        <div>{{ $data_trip['all_trip_count'] }}</div>
                        <div>All Till Now</div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row abc col-sm-12 col-lg-12 col-xl-12"
                style="background: white;padding:2rem 1rem 2rem 1rem;font-size: larger;
            display: flex;border-radius:10px;
            text-align: center;font-weight: 500;justify-content: space-around;">
                <div style="align-self: center;">
                    <div>Maunally Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/today-maunally/' . $data_trip['trip_for']) }}"'>
                    {{ $data_trip['post_maunally_trip_today'] }}
                    <div> Today Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/yesterday-maunally/' . $data_trip['trip_for']) }}"'>
                    {{ $data_trip['post_maunally_trip_yesterday'] }}
                    <div> YesterDay Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/last-week-maunally/' . $data_trip['trip_for']) }}"'>
                    {{ $data_trip['post_maunally_trip_last_week'] }}
                    <div>Weekly Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/current-month-maunally/' . $data_trip['trip_for']) }}"'>
                    {{ $data_trip['post_maunally_trip_current_month'] }}
                    <div>This Month Trip</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/last-month-maunally/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['post_maunally_trip_last_month'] }}</div>
                    <div>Last Month Trip</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/all-maunally/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['post_maunally_trip_all'] }}</div>
                    <div>All Till Now</div>
                </div>
            </div><br>
            <div class="row abc col-sm-12 col-lg-12 col-xl-12"
                style="background: white;padding:2rem 1rem 2rem 1rem;font-size: larger;
            display: flex;border-radius:10px;
            text-align: center;font-weight: 500;justify-content: space-around;">
                <div style="align-self: center;">
                    <div>Not Allocated</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/today-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_today'] }}</div>
                    <div> Today Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/yesterday-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_yesterday'] }}</div>
                    <div> YesterDay Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/last-week-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_last_week'] }}</div>
                    <div>Weekly Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/current-month-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_current_month'] }}</div>
                    <div>This Month Trip</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/last-month-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_last_month'] }}</div>
                    <div>Last Month Trip</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;    cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/all-allocated/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['not_allocated_trip_all'] }}</div>
                    <div>All Till Now</div>
                </div>
            </div><br>
            <div class="row abc col-sm-12 col-lg-12 col-xl-12"
                style="background: white;padding:2rem 1rem 2rem 1rem;font-size: larger;
            display: flex;border-radius:10px;
            text-align: center;font-weight: 500;justify-content: space-around;">
                <div style="align-self: center;">
                    <div>Trip Type</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/agent-trip/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['agent_trip'] }}</div>
                    <div> Agents Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/dco-trip/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['dco_trip'] }}</div>
                    <div>Live Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/rider-trip/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['rider_trip'] }}</div>
                    <div>Rider Trips</div>
                </div>
                <div style="background: #EAEDF7 !important;border-radius: 10px !important;padding: 18px !important;cursor: pointer;"
                    onclick='location.href = "{{ url('/admin/trip/filter/travel-trip/' . $data_trip['trip_for']) }}"'>
                    <div>{{ $data_trip['travel_trip'] }}</div>
                    <div>Travel Trip</div>
                </div>

            </div><br>
            {{-- <div class="row trip_box">
                <div class="col-sm-6 col-lg-6 col-xl-6">
                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <h5 class="title_label"
                                    style="display: flex !important;
                                    justify-content: center !important;
                                    padding: 10px !important;">
                                    Completed Trips</h5>
                                <div class="row row-sm">
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/today-completed') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Today's Trips</h6>

                                            <h2 class="mb-1 mt-2 number-font">

                                                <span class="counter"> </span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/yesterday-completed') }}"'>

                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Yesterdays's Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['yesterday_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-week-completed') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Weekly Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['last_week_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/current-month-completed') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">This Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['current_month_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-month-completed') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Last Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['last_month_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/all-completed') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">All Till Now</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['all_trip_count'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-sm-6 col-lg-6 col-xl-6">
                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <h5 class="title_label"
                                    style="display: flex !important;
                                    justify-content: center !important;
                                    padding: 10px !important;">
                                    Maunally Trips Posted</h5>
                                <div class="row row-sm">
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/today-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Today's Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['post_maunally_trip_today'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/yesterday-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Yesterdays's Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['post_maunally_trip_yesterday'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-week-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Weekly Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['post_maunally_trip_last_week'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/current-month-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">This Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['post_maunally_trip_current_month'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-month-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Last Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['post_maunally_trip_last_month'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/all-maunally') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">All Till Now</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['post_maunally_trip_all'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row trip_box">

                <div class="col-sm-6 col-lg-6 col-xl-6">
                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <h5 class="title_label"
                                    style="display: flex !important;
                                    justify-content: center !important;
                                    padding: 10px !important;">
                                    Not Allocated</h5>
                                <div class="row row-sm">

                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/today-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Today's Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['not_allocated_trip_today'] }}</span>

                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/yesterday-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Yesterdays's Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">

                                                <span
                                                    class="counter">{{ $data_trip['not_allocated_trip_yesterday'] }}</span>

                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-week-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Weekly Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['not_allocated_trip_last_week'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/current-month-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">This Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['not_allocated_trip_current_month'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/last-month-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Last Month</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span
                                                    class="counter">{{ $data_trip['not_allocated_trip_last_month'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/all-allocated') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">All Till Now</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['not_allocated_trip_all'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-sm-6 col-lg-6 col-xl-6">
                    <div class="row row-sm">
                        <div class="col-md-12">
                            <div class="card custom-card">
                                <h5 class="title_label"
                                    style="display: flex !important;
                                    justify-content: center !important;
                                    padding: 10px !important;">
                                    Trip Type</h5>
                                <div class="row row-sm">

                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/agent-trip') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Agents Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['agent_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/dco-trip') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">DCO Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['dco_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/rider-trip') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Rider Trips</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['rider_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                        onclick='location.href = "{{ url('/admin/trip/filter/travel-trip') }}"'>
                                        <div class="card-body text-center">
                                            <h6 class="mb-0">Travel Trip</h6>
                                            <h2 class="mb-1 mt-2 number-font">
                                                <span class="counter">{{ $data_trip['travel_trip'] }}</span>
                                            </h2>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div> --}}
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">


                            <div class="row">
                                &nbsp;
                            </div>
                            @php
                                $param = '';
                            @endphp

                            <div class="row">
                                <div class="col-lg-6">
                                    <div
                                        class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <label style="white-space: nowrap;"
                                            class="mb-0 gray">{{ @trans('usertype.record_per_page') }}&nbsp;</label>
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
                                        <!-- <img src="{{ asset('img/admin/ic_search.png') }}" srcset="{{ asset('img/admin/ic_search@2x.png 2x') }}" alt="search"> -->
                                        <input type="text" class="form-control" name="search_text" id="search_text"
                                            v-model="search_text" @input="search" placeholder="Search">
                                    </div>
                                </div>

                            </div>

                            <div class="row">

                                <div class="table-responsive table-data" v-if="items.length">
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example"
                                                class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer">

                                                {{-- @php
                                                        dd($trip_filter);
                                                    @endphp --}}
                                                @if (empty($trip_filter))
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th><b>Status</b><a href="javascript:void(0)"
                                                                    :class="classSort('trip_status')"
                                                                    @click="sort('trip_status')"></a>
                                                            </th>
                                                            <th><b>{{ @trans('trip.trip_type') }}</b><a
                                                                    href="javascript:void(0)"
                                                                    :class="classSort('trip_type')"
                                                                    @click="sort('trip_type')"></a>
                                                            </th>
                                                            {{-- <th><b>{{ @trans('trip.user_name') }}</b><a href="javascript:void(0)" :class="classSort('first_name')" @click="sort('first_name')"></a> --}}
                                                            </th>

                                                            <th><b>{{ @trans('trip.vehicle_type') }}</b><a
                                                                    href="javascript:void(0)"
                                                                    :class="classSort('vehicle_type')"
                                                                    @click="sort('vehicle_type')"></a>
                                                            </th>


                                                            <th><b>{{ @trans('trip.pickup_location') }}</b><a
                                                                    href="javascript:void(0)"
                                                                    :class="classSort('pickup_location')"
                                                                    @click="sort('pickup_location')"></a>
                                                            </th>
                                                            <th><b>Drop Location</b><a href="javascript:void(0)"
                                                                    :class="classSort('drop_location')"
                                                                    @click="sort('drop_location')"></a>
                                                            </th>
                                                            <th><b>Pickup Date Time</b><a href="javascript:void(0)"
                                                                    :class="classSort('pickup_date')"
                                                                    @click="sort('pickup_date')"></a>
                                                            </th>
                                                            {{-- <th><b>Return Date Time</b><a href="javascript:void(0)"
                                                                        :class="classSort('returrn_date')"
                                                                        @click="sort('returrn_date')"></a>
                                                                    </th> --}}
                                                            <th><b>{{ @trans('trip.action') }}</b></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>


                                                        <tr v-for="(item,index) in items">

                                                            <td>@{{ index + from }}</td>
                                                            {{-- status --}}
                                                            @if ($data_trip['trip_for'] == 1)
                                                                <td v-if="item.status=='2'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-primary btn-sm">Reach Pickup
                                                                        Location</button>
                                                                </td>

                                                                <td v-else-if="item.status=='0'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-dark btn-sm">Pending</button>
                                                                </td>
                                                                <td v-else-if="item.status=='1'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-success btn-sm">Accept</button>
                                                                </td>
                                                                <td v-else-if="item.status=='3'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-info btn-sm">Start
                                                                        Trip</button>
                                                                </td>
                                                                <td v-else-if="item.status=='4'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-info btn-sm">End
                                                                        Trip</button>
                                                                </td>
                                                                <td v-else-if="item.status=='5'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-success btn-sm">Completed</button>
                                                                </td>
                                                                <td v-else-if="item.status=='6'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-danger btn-sm">Canceled</button>
                                                                </td>
                                                            @else
                                                                <td v-if="item.trip_status=='2'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-success btn-sm">Completed</button>
                                                                </td>

                                                                <td v-else-if="item.trip_status=='0'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-dark btn-sm">Booked</button>
                                                                </td>
                                                                <td v-else-if="item.trip_status=='1'">
                                                                    <button type="button"
                                                                        class="btn ripple btn-warning btn-sm">Started</button>
                                                                </td>
                                                                <td
                                                                    v-else-if="item.max_is_accepted==='' || item.max_is_accepted=== null">
                                                                    -
                                                                </td>
                                                            @endif
                                                            <td
                                                                v-else-if="item.max_is_accepted==='' || item.max_is_accepted=== null">
                                                                -
                                                            </td>
                                                            <td v-else-if="item.max_is_accepted!='1'">
                                                                <button type="button"
                                                                    class="btn ripple btn-info btn-sm">On
                                                                    Going</button>
                                                            </td>
                                                            <td v-else-if="item.tb_trip_id==null || item.tb_trip_id==null">
                                                                -
                                                            </td>
                                                            <td v-else>
                                                                <button type="button"
                                                                    class="btn ripple btn-info btn-sm">On
                                                                    Going</button>
                                                            </td>
                                                            <td>@{{ item.trip_type }}</td>
                                                            {{-- <td>@{{ item.first_name }} @{{ item.last_name }}</td> --}}

                                                            <td v-if="item.vehicle_type != null">
                                                                @{{ item.vehicle_type }}
                                                            </td>

                                                            <td v-else>
                                                                @{{ item.vehical_manual_type }}
                                                            </td>



                                                            <td><span data-placement="bottom" data-toggle="tooltip"
                                                                    :title="item.pickup_location">@{{ item.mini_pickup_location }}...</span>
                                                            </td>

                                                             <td v-if="item.trip_type=='InCity'"><span
                                                                    data-placement="bottom" data-toggle="tooltip"
                                                                    :title="item.drop_location">@{{ item.mini_drop_location }}...</span>
                                                            </td> 
<!--                                                            <td v-else-if="item.trip_type=='Hourly'">
                                                                -
                                                            </td>-->

                                                             <td v-else><span data-placement="bottom" data-toggle="tooltip"
                                                                    {{-- :title="item.drop_location_for_outstation">@{{ item.mini_drop_location_for_outstation }}...</span> --}}
                                                                    :title="item.drop_location_for_outstation">@{{ item.mini_drop_location }}...</span>
                                                            </td> 
                                                            <td v-if="item.pickup_date_time != '-0001-11-30 00:00:00'">
                                                                @{{ formatDate(item.pickup_date_time) }}</td>
                                                            <td v-else>-</td>
                                                            <td class="act-btn">
                                                                <a :href="'{{ url(Session::get('panel') . '/trip/show') }}/' +
                                                                item.id"
                                                                    title="View" class="btn-view btn-sm">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                                <!-- <a :href="'{{ url(Session::get('panel') . '/trip/edit') }}/' +
                                                                item: href ="'{{ url(Session::get('panel') . '/trip/destroy/') }}/' +
                                                                    item.id" .id" title="Edit"@click="confirm(item.id)"
                                                                                                                                                                            class="btn-edit btn-sm"><i class="material-icons">edit</i>
                                                                                                                                                                        </a> -->
                                                                <a :href="'{{ url(Session::get('panel') . '/trip/destroy/') }}/' + item.id" title="Delete" class="btn-edit btn-sm delete" @onclick="destroy(item.id)"><i
                                                                        class="material-icons">delete</i>
                                                                    {{-- <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal">Open Modal</button> --}}
                                                                </a>
                                                                <div class="modal fade" id="myModal" role="dialog">
                                                                    <div class="modal-dialog">

                                                                        <!-- Modal content-->
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">

                                                                                <h4 class="modal-title">Delete</h4>
                                                                                <button type="button" class="close"
                                                                                    data-dismiss="modal">&times;</button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <p>Are you sure to delete?</p>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <a :href="'{{ url(Session::get('panel') . '/trip/destroy/') }}/' + item.id"
                                                                                    style="background: green;color: white;padding: 6px 18px;border-radius: 5px;">Yes
                                                                                </a>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                    {{-- @elseif($trip_filter == 0)
                                                <thead>
                                                    <tr>
                                                        <th>id</th>
                                                        <th>trip status</th>
                                                        <th>trip type</th>
                                                        <th>vehical type</th>
                                                        <th>pickup loaction</th>
                                                        <th>drop location</th>
                                                        <th>pickup date time</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <p>No data..</p>
                                                </tbody> --}}
                                                @else
                                                    <thead>
                                                        <tr>
                                                            <th>id</th>
                                                            <th>trip status</th>
                                                            <th>vehical type</th>
                                                            <th>trip type</th>
                                                            <th>pickup loaction</th>
                                                            <th>drop location</th>
                                                            <th>pickup date time</th>
                                                            <th>Trip From</th>
                                                            <th>CAB Type</th>
                                                            <th>Driver Name</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($trip_filter as $key => $trip_filter)
                                                            <tr>
                                                                <td>{{ ++$key }}</td>
                                                                <td>
                                                                    @if ($data_trip['trip_for'] == 1)
                                                                        @if (isset($trip_filter->status))
                                                                            @if ($trip_filter->status == 0)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-dark btn-sm">
                                                                                    Pending
                                                                                </button>
                                                                            @elseif($trip_filter->status == 1)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-success btn-sm">
                                                                                    Accept
                                                                                </button>
                                                                            @elseif($trip_filter->status == 2)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-primary btn-sm">
                                                                                    Rich Pickup Location
                                                                                </button>
                                                                            @elseif($trip_filter->status == 3)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-info btn-sm">
                                                                                    Start Trip
                                                                                </button>
                                                                            @elseif($trip_filter->status == 4)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-info btn-sm">
                                                                                    End Trip
                                                                                </button>
                                                                            @elseif($trip_filter->status == 5)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-success btn-sm">
                                                                                    Completed
                                                                                </button>
                                                                            @elseif($trip_filter->status == 6)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-danger btn-sm">
                                                                                    Cancel
                                                                                </button>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    @else
                                                                        @if (isset($trip_filter->trip_status))
                                                                            @if ($trip_filter->trip_status == 0)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-dark btn-sm">
                                                                                    Boooked
                                                                                </button>
                                                                            @elseif($trip_filter->trip_status == 1)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-info btn-sm">
                                                                                    Started
                                                                                </button>
                                                                            @elseif($trip_filter->trip_status == 2)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-success btn-sm">
                                                                                    Completed
                                                                                </button>
                                                                            @elseif($trip_filter->trip_status == 3)
                                                                                <button type="button"
                                                                                    class="btn ripple btn-info btn-sm">
                                                                                    On Going
                                                                                </button>
                                                                            @else
                                                                                -
                                                                            @endif
                                                                        @else
                                                                            -
                                                                        @endif
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if ($trip_filter->vehicle_type_id == 1)
                                                                        Hatch Back
                                                                    @elseif($trip_filter->vehicle_type_id == 2)
                                                                        Sedan
                                                                    @elseif($trip_filter->vehicle_type_id == 3)
                                                                        SUV
                                                                    @else
                                                                        {{ $trip_filter->vehical_manual_type }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $trip_filter->trip_type }}</td>
                                                                <td>{{ $trip_filter->pickup_location }}</td>
                                                                <td>
                                                                    @if ($trip_filter->drop_location == null)
                                                                        -
                                                                    @else
                                                                        {{ $trip_filter->drop_location }}
                                                                    @endif
                                                                </td>
                                                                <td>{{ $trip_filter->pickup_date . ' ' . $trip_filter->pickup_time }}
                                                                </td>
                                                                <td>-</td>
                                                                @php
                                                                    $cabs = App\Models\Cabs::where('id', $trip_filter->cab_id)->first();
                                                                    $driver = App\Models\Drivers::where('id', $trip_filter->driver_id)->first();
                                                                @endphp
                                                                <td>
                                                                    @if (isset($cabs->cab_post_type))
                                                                        {{ $cabs->cab_post_type }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                                <td>
                                                                    @if (isset($driver->first_name))
                                                                        {{ $driver->first_name . ' ' . $driver->last_name }}
                                                                    @else
                                                                        -
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                @endif


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

    {{-- @if (!empty($data_trip['trip_for'])) --}}
    <input type="hidden" id="trip_for" value="{{ $data_trip['trip_for'] }}">
    {{-- @endif --}}
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
    <script>
        function select_state_city() {
            var state = $("#select_state").find(":selected").val();
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
                    $('#select_city').empty();
                    partyNameArray.push(partyNameArrays_e);
                    response.city_name.forEach(element => {
                        partyNameArrayBefore = {
                            'text': element.name,
                            'value': element.name
                        };
                        partyNameArray.push(partyNameArrayBefore);
                    });
                    optionLists = document.getElementById('select_city').options;
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
        function initListUsers() {
            var container = $('div'),
                scrollTo = $('#example');

            container.animate({
                scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
            });

            $(".table-data .table-responsive").freezeTable({
                'columnNum': 0,
                /* 'scrollable': true, */
            });
        }
        function deleteTrip(){}
    </script>
    {{-- @if ($data_trip['trip_for'] == 0) --}}
    <script>
        var trip = $('#trip_for').val();
        // alert(trip);
        if (trip == 0) {
            // alert("partner");
            var user_indexUrlJson =
                "{{ route('admin.trip.index_json', ['panel' => Session::get('panel'), 'param' => $param]) }}";
            var toggle_status_url = "{{ url(Session::get('panel') . '/users/toggle-status') }}";
            var user_deleteUrl = "{{ route('admin.trip.destroy', ['panel' => Session::get('panel')]) }}";
            @if (Session::has('message'))
                Snackbar.show({
                    pos: 'bottom-right',
                    text: "{!! session('message') !!}",
                    actionText: 'Okay'
                });
            @endif
        } else {
            // alert("ustomer");
            var user_indexUrlJson =
                "{{ route('admin.trip.index_json_customer', ['panel' => Session::get('panel'), 'param' => $param]) }}";
            var toggle_status_url = "{{ url(Session::get('panel') . '/users/toggle-status') }}";
            var user_deleteUrl = "{{ route('admin.trip.destroy', ['panel' => Session::get('panel')]) }}";
            @if (Session::has('message'))
                Snackbar.show({
                    pos: 'bottom-right',
                    text: "{!! session('message') !!}",
                    actionText: 'Okay'
                });
            @endif
        }
    </script>
    {{-- @else
    <script>
    
        var user_indexUrlJson =
            "{{ route('admin.trip.index_json_customer', ['panel' => Session::get('panel'), 'param' => $param]) }}";
        var toggle_status_url = "{{ url(Session::get('panel') . '/users/toggle-status') }}";
        var user_deleteUrl = "{{ route('admin.trip.destroy', ['panel' => Session::get('panel')]) }}";
        @if (Session::has('message'))
            Snackbar.show({
                pos: 'bottom-right',
                text: "{!! session('message') !!}",
                actionText: 'Okay'
            });
        @endif
    </script>
    @endif --}}
    <script type="text/javascript" src="{{ asset('js/admin/trip.js') }}"></script>
@endpush
