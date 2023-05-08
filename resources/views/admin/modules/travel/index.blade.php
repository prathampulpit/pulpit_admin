@extends('admin.layouts.main')

@section('title')
    Travel
@endsection

@section('content')
    <!-- Main Content-->
    <style>
        #chartLine {
            height: 400px !important;
        }
    </style>
    <div class="main-content side-content pt-0">

        <loading :active.sync="isLoading"></loading>

        <div class="container-fluid">
            <div class="inner-body">

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('travel.travel') }}
                            {{ @trans('travel.users') }}</h2>
                        <!-- <ol class="breadcrumb">
                                                                                                            <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                                                                                                            <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                                                                                                        </ol> -->
                    </div>
                    <div class="d-flex">
                        <!-- <div class="justify-content-center">
                                                                                                        <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.partner.create', ['panel' => Session::get('panel')]) }}"><i class="fe fe-plus mr-2"></i> {{ @trans('user.add_user') }} </a>
                                                                                                        </div> -->
                        <!-- <form method="post" action="{{ route('admin.travel.index', ['panel' => Session::get('panel')]) }}" class="" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                                                                                        <div class="row">
                                                                                                            @csrf
                                                                                                            <div class="col-lg-1">
                                                                                                            </div>
                                                                                                            <div class="col-lg-3">
                                                                                                                <select class="form-control" id="state_id1" name="state_id" style="margin-left: -28px;" onchange="getCity1()">
                                                                                                                    <option value="" selected>PAN India</option>
                                                                                                                    <?php foreach($states as $r){?>
                                                                                                                    <option value="<?= $r['id'] ?>" @if ($state_id == $r['id'])
    selected
    @endif><?= $r['name'] ?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </div>

                                                                                                            <div class="col-lg-2 city_lists1">
                                                                                                                <select class="form-control" id="city_id1" name="city_id" style="margin-left: -28px;">
                                                                                                                    <option value="">Select City</option>
                                                                                                                    <?php foreach($cities as $r){?>
                                                                                                                        <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </div>

                                                                                                            <div class="col-lg-3">
                                                                                                                <select class="form-control" id="vehicle_type_id" name="vehicle_type_id" style="margin-left: -28px;" onchange="getPlans1()">
                                                                                                                    <option value="" selected>Select Vehicle Types</option>
                                                                                                                    <?php foreach($vehicle_types as $r){?>
                                                                                                                    <option value="<?= $r['id'] ?>" @if ($vehicle_type_id == $r['id'])
    selected
    @endif><?= $r['name'] ?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </div>

                                                                                                            <div class="col-lg-2 plan_lists1">
                                                                                                                <select class="form-control" id="plan_id1" name="plan_id" style="margin-left: -28px;">
                                                                                                                    <option value="" selected>Select Subscription Plan</option>
                                                                                                                    <?php foreach($plans as $r){?>
                                                                                                                    <option value="<?= $r['id'] ?>" @if ($plan_id == $r['id'])
    selected
    @endif><?= $r['name'] ?></option>
                                                                                                                    <?php } ?>
                                                                                                                </select>
                                                                                                            </div>

                                                                                                            <div class="col-lg-0">
                                                                                                                <div class="table-top-panel d-flex align-items-center">
                                                                                                                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                                                                                                        <button type="submit" class="btn ripple btn-primary"><i class="ti-search" data-toggle="tooltip" title="" data-original-title="ti-search"></i></button>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </div>
                                                                                                        </form> -->
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
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'day']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Today's Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $today_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'yesterday']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Yesterdays's Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $yesterday_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'week']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Weekly Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $week_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'this_month']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">This Month</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $this_month_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'last_month']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Last Month</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $last_month_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel')]) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">All Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $all_joined }}</span>
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
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'fiften_days']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Active User 15 days</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $active_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'inactive']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Inactive user</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $inactive_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'paid']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Paid User</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $paid_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'unpaid']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">unpaid user</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $unpaid_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.travel.index', ['panel' => Session::get('panel'), 'filter' => 'expired_users']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Expired User</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $expired_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer">
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Uninstalled History</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $uninstall_users }}</span>
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
                                        <canvas id="chartLine" class="chart-dropshadow2 ht-420"></canvas>
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
                                            placeholder="Select Date" format="YYYY-MM-DD"></date-picker>
                                    </div>

                                    <div class="col-lg-2">
                                        <select class="form-control" id="state_id" v-model="state_id"
                                            style="margin-left: -28px;" onchange="getCity()">
                                            <option value="all" selected>PAN India</option>
                                            <?php foreach($states as $r){?>
                                            <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 city_lists">
                                        <select class="form-control" id="city_id" v-model="city_id"
                                            style="margin-left: -28px;">
                                            <option value="">Select City</option>
                                            <?php foreach($cities as $r){?>
                                            <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-2">
                                        <select class="form-control" id="vehicle_type_id" v-model="vehicle_type_id"
                                            style="margin-left: -28px;" onchange="getPlans()">
                                            <option value="" selected>Select Vehicle Types</option>
                                            <?php foreach($vehicle_types as $r){?>
                                            <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-2 plan_lists">
                                        <select class="form-control" id="plan_id" v-model="plan_id"
                                            style="margin-left: -28px;">
                                            <option value="" selected>Select Subscription Plan</option>
                                            <?php foreach($plans as $r){?>
                                            <option value="<?= $r['id'] ?>"><?= $r['name'] ?>
                                                @if ($r['vehicle_type_id'] == 1)
                                                    [Hatch Back]
                                                @elseif($r['vehicle_type_id'] == 2)
                                                    [Sedan]
                                                @elseif($r['vehicle_type_id'] == 3)
                                                    [SUV]
                                                @else
                                                @endif
                                            </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div class="col-lg-1">
                                        <div class="table-top-panel d-flex align-items-center">
                                            <div
                                                class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                                <button type="button" class="btn ripple btn-primary"
                                                    @click="master_search"><i class="ti-search" data-toggle="tooltip"
                                                        title="" data-original-title="ti-search"></i></button>
                                                <i class="material-icons" style="cursor: pointer;" v-if="start_date"
                                                    @click="clearSearch">close</i>
                                            </div>
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
                                            <label style="white-space: nowrap;"
                                                class="mb-0 gray">{{ @trans('usertype.record_per_page') }}&nbsp;</label>
                                            <select class="form-control" id="per_page" v-model="per_page"
                                                @input="perPage">
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
                                            <input type="text" class="form-control" name="search_text"
                                                id="search_text" v-model="search_text" @input="search"
                                                placeholder="Search">
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
                                                        <tr>
                                                            <th>#</th>
                                                            <th><b>Image</b></th>
                                                            <th><b>Owner Name</b><a href="javascript:void(0)"
                                                                    :class="classSort('name')" @click="sort('name')"></a>
                                                            </th>
                                                            <th><b>Total Driver</b></th>
                                                            <th><b>Total Cabs</b></th>
                                                            <th><b>{{ @trans('user.phone') }}</b><a
                                                                    href="javascript:void(0)"
                                                                    :class="classSort('mobile_number')"
                                                                    @click="sort('mobile_number')"></a></th>
                                                            <th><b>Status</b></th>

                                                            <th><b>Joined Date</b><a href="javascript:void(0)"
                                                                    :class="classSort('created_at')"
                                                                    @click="sort('created_at')"></a></th>
                                                            <th><b>Plan Type</b></th>
                                                            <th><b>Subscription End On</b></th>
                                                            <th><b>{{ @trans('user.action') }}</b></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr v-for="(item,index) in items">
                                                            <!-- danger -->

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
                                                                    <span style="background: aliceblue;" class="badge bg-white">@{{ index + from }}</span>
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

                                                            <td v-if="item.profile_pic != null">
                                                                <img style=" height: 4rem !important;
                                                            border-radius: 50px !important;
                                                            width: 4rem !important;"
                                                                    v-bind:src="item.profile_pic" class="image_table"
                                                                    width='50' />
                                                            </td>
                                                            <td v-else>
                                                                <img style=" height: 4rem !important;
                                                            border-radius: 50px !important;
                                                            width: 4rem !important;"
                                                                    src="{{ asset('noimage.png') }}" class="image_table"
                                                                    width='50' />
                                                            </td>

                                                            <td><a :href="'{{ url(Session::get('panel') . '/travel/show') }}/' + item
                                                                .id"
                                                                    title="View">@{{ capitalize(item.first_name) }}
                                                                    @{{ capitalize(item.last_name) }}</a></td>
                                                            <td>@{{ item.total_drivers }}</td>
                                                            <td>@{{ item.total_cabs }}</td>
                                                            <td><a :href="'{{ url(Session::get('panel') . '/travel/show') }}/' + item
                                                                .id"
                                                                    title="View">@{{ item.mobile_number }}</a></td>

                                                            <td v-if="item.availability_status=='1'">
                                                                <button type="button"
                                                                    class="btn ripple btn-success btn-sm">Online</button>
                                                            </td>
                                                            <td v-else>
                                                                <button type="button"
                                                                    class="btn ripple btn-danger btn-sm">Offline</button>
                                                            </td>
                                                            <td v-if="item.created_at!='-0001-11-30 00:00:00'">
                                                                @{{ formatDate(item.created_at) }}</td>
                                                            <td v-else>-</td>

                                                            <td>@{{ item.subscription_name }}</td>
                                                            <td v-if="item.end_datetime!=''">@{{ formatDate(item.end_datetime) }}</td>
                                                            <td v-else>-</td>

                                                            <td class="act-btn">
                                                                <a :href="'{{ url(Session::get('panel') . '/travel/show') }}/' +
                                                                item.id"
                                                                    title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                                <!-- <a :href="'{{ url(Session::get('panel') . '/travel/edit') }}/' +
                                                                item.id" title="Edit"
                                                                                                                                                    class="btn btn-edit"><i class="material-icons">edit</i>
                                                                                                                                                </a>  -->
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
    <script type="text/javascript">
        function initListUsers() {
            var container = $('div'),
                scrollTo = $('#example');

            container.animate({
                scrollTop: container.scrollTop()
            });

            $(".table-scrollable").freezeTable({
                'columnNum': 3,
                'scrollable': true,
            });
        }
    </script>
    <script>
        var user_indexUrlJson =
            "{{ route('admin.travel.index_json', ['panel' => Session::get('panel'), 'filter' => $filter]) }}";
        var toggle_status_url = "{{ url(Session::get('panel') . '/travel/toggle-status') }}";
        var user_deleteUrl = "{{ route('admin.travel.destroy', ['panel' => Session::get('panel'), 'id' => 1]) }}";
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
                // console.log(graph_trans_data);
                data: {
                    labels: [<?= $day_name ?>],
                    datasets: [{
                            label: 'No Of Users',
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
                                max: <?= $all_joined + 70 ?>,
                                stepSize: <?= $all_joined / 2 ?>
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

        function getPlans() {
            var id = $('#vehicle_type_id').val();
            var siteurl = "{{ url(Session::get('panel') . '/getplans') }}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {
                    console.log(response);
                    $('.plan_lists').html(response);
                }
            });
        }

        function getPlans1() {
            var id = $('#vehicle_type_id').val();
            var siteurl = "{{ url(Session::get('panel') . '/getplans') }}";
            $.ajax({
                url: siteurl,
                type: "POST",
                data: {
                    "_token": "{{ csrf_token() }}",
                    "id": id
                },
                success: function(response) {
                    $('.plan_lists1').html(response);
                }
            });
        }

        function getCity() {
            var state_id = $('#state_id').val();
            $.ajax({
                url: '{{ route('admin.register.statecity') }}',
                type: "POST",
                data: {
                    id: state_id,
                    pagetype: 'index'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                success: function(result) {
                    //$(".city_lists").html(result);

                    $("#city_id").html(result);
                    console.log(result);

                }
            });
        }

        function getCity1() {
            var state_id = $('#state_id1').val();
            $.ajax({
                url: '{{ route('admin.register.statecity') }}',
                type: "POST",
                data: {
                    id: state_id,
                    pagetype: 'indextop'
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                cache: false,
                success: function(result) {
                    $(".city_lists1").html(result);
                    //$("#city_id1").html(result);
                }
            });
        }
    </script>
    <script type="text/javascript" src="{{ asset('js/admin/travel.js') }}"></script>
    <script type="text/javascript" src="https://unpkg.com/vue2-datepicker@2.6.4/lib/index.js"></script>

    <!-- Circle Progress js-->
    <script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>
@endpush
