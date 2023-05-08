@extends('admin.layouts.main')

@section('title')
    Driver
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
                        <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('driver.driver') }}
                            {{ @trans('driver.users') }}</h2>
                        <!-- <ol class="breadcrumb">
                                                                                                                                                                                                                                                                                                                                                                    <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                                                                                                                                                                                                                                                                                                                                                                    <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                                                                                                                                                                                                                                                                                                                                                                </ol> -->
                    </div>
                    <!-- <div class="d-flex">
                                                                                                                                                                                                                                                                                                                                                                <div class="justify-content-center">
                                                                                                                                                                                                                                                                                                                                                                <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.partner.create', ['panel' => Session::get('panel')]) }}"><i class="fe fe-plus mr-2"></i> {{ @trans('user.add_user') }} </a>
                                                                                                                                                                                                                                                                                                                                                                </div>
                                                                                                                                                                                                                                                                                                                                                            </div> -->
                    <!-- <form method="post" action="{{ route('admin.driver.index', ['panel' => Session::get('panel')]) }}" class="" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                                                                                                                                                                                                                                                                                                                                                <div class="row">
                                                                                                                                                                                                                                                                                                                                                                    @csrf
                                                                                                                                                                                                                                                                                                                                                                    <div class="col-lg-2">
                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                    <div class="col-lg-4">
                                                                                                                                                                                                                                                                                                                                                                        <select class="form-control" id="state_id1" name="state_id" style="margin-left: -28px;" onchange="getCity1()">
                                                                                                                                                                                                                                                                                                                                                                            <option value="" selected>PAN India</option>
                                                                                                                                                                                                                                                                                                                                                                            <?php foreach($states as $r){?>
                                                                                                                                                                                                                                                                                                                                                                            <option value="<?= $r['id'] ?>" @if ($state_id == $r['isoCode'])
    selected
    @endif><?= $r['name'] ?></option>
                                                                                                                                                                                                                                                                                                                                                                            <?php } ?>
                                                                                                                                                                                                                                                                                                                                                                        </select>
                                                                                                                                                                                                                                                                                                                                                                    </div>

                                                                                                                                                                                                                                                                                                                                                                    <div class="col-lg-3 city_lists1">
                                                                                                                                                                                                                                                                                                                                                                        <select class="form-control" id="city_id1" name="city_id" style="margin-left: -28px;">
                                                                                                                                                                                                                                                                                                                                                                            <option value="">Select City</option>
                                                                                                                                                                                                                                                                                                                                                                            <?php foreach($cities as $r){?>
                                                                                                                                                                                                                                                                                                                                                                                <option value="<?= $r['id'] ?>"><?= $r['name'] ?></option>
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
                <!-- End Page Header -->

                <div class="row">
                    <div class="col-sm-8 col-lg-8 col-xl-8">
                        <div class="row row-sm">
                            <div class="col-md-12">
                                <div class="card custom-card">
                                    <div class="row row-sm">
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'day']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Today's Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $today_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'yesterday']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Yesterdays's Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $yesterday_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'week']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Weekly Joined</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $week_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'this_month']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">This Month</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $this_month_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'last_month']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Last Month</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $last_month_joined }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel')]) }}";'>
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
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'fiften_days']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Active User 15 days</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $active_users }}</span>
                                                </h2>
                                            </div>
                                        </div>
                                        <div class="col-xl-4 col-lg-6 col-sm-6 pe-0 ps-0 border-end pointer"
                                            onclick='location.href = "{{ route('admin.driver.index', ['panel' => Session::get('panel'), 'filter' => 'inactive']) }}";'>
                                            <div class="card-body text-center">
                                                <h6 class="mb-0">Inactive user</h6>
                                                <h2 class="mb-1 mt-2 number-font">
                                                    <span class="counter">{{ $inactive_users }}</span>
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
                                                                    :class="classSort('owner_name')"
                                                                    @click="sort('owner_name')"></a>
                                                            </th>
                                                            <th><b>Travel Name</b><a href="javascript:void(0)"
                                                                    :class="classSort('travel_name')"
                                                                    @click="sort('travel_name')"></a>
                                                            </th>
                                                            <th><b>Driver Name</b><a href="javascript:void(0)"
                                                                    :class="classSort('drivers.first_name')"
                                                                    @click="sort('drivers.last_name')"></a></th>
                                                            <!-- <th><b>{{ @trans('user.email') }}</b><a href="javascript:void(0)"
                                                                                                                                                                                                                                                                                                                                                                                                            :class="classSort
                                                                                                                                                                                                                                                                                                                                                                                                                (
                                                                                                                                                                                                                                                                                                                                                                                                                    'emailid'
                                                                                                                                                                                                                                                                                                                                                                                                                )" @click="sort('emailid')"></a></th> -->
                                                            <th><b>CAB Type</b><a href="javascript:void(0)"
                                                                    :class="classSort('vehicle_types.name')"
                                                                    @click="sort('vehicle_types.name')"></a></th>
                                                            <th><b>CAB NO</b><a href="javascript:void(0)"
                                                                    :class="classSort('is_otp')"
                                                                    @click="sort('is_otp')"></a></th>

                                                            <th><b>Mobile No</b><a href="javascript:void(0)"
                                                                    :class="classSort('users.mobile_number')"
                                                                    @click="sort('users.mobile_number')"></a></th>
                                                            <th><b>Authorized Driver</b></th>
                                                            <th><b>Status</b><a href="javascript:void(0)"
                                                                    :class="classSort('users.status')"
                                                                    @click="sort('users.status')"></a></th>
                                                            <th><b>Joined Date</b><a href="javascript:void(0)"
                                                                    :class="classSort('users.created_at')"
                                                                    @click="sort('users.created_at')"></a></th>
                                                            <th><b>{{ @trans('user.action') }}</b></th>
                                                        </tr>
                                                    </thead>

                                                    <tbody>
                                                        <tr v-for="(item,index) in items">
                                                            <!-- danger -->
                                                            <td v-if="item.is_approved=='1'">
                                                                <span
                                                                    class="badge bg-success">@{{ index + from }}</span>
                                                            </td>
                                                            <td v-else-if="item.is_approved=='2'">
                                                                <span
                                                                    class="badge bg-warning">@{{ index + from }}</span>
                                                            </td>
                                                            <td v-else>
                                                                <span class="badge bg-danger">@{{ index + from }}</span>
                                                            </td>
                                                            
                                                            <td><img v-bind:src="item.profile_pic" width='50' /></td>
                                                            <td><a :href="'{{ url(Session::get('panel') . '/driver/show') }}/' + item
                                                                .id"
                                                                    title="View">@{{ capitalize(item.owner_name) }}</a></td>
                                                            <td><a :href="'{{ url(Session::get('panel') . '/driver/show') }}/' + item
                                                                .id"
                                                                    title="View">@{{ capitalize(item.travel_name) }}</a></td>
                                                            <td><a :href="'{{ url(Session::get('panel') . '/driver/show') }}/' + item
                                                                .id"
                                                                    title="View">
                                                                    @{{ capitalize(item.driver_first_name) }} @{{ capitalize(item.driver_last_name) }}
                                                                </a></td>
                                                            <td>@{{ item.vehicle_type_name }}</td>
                                                            <td>@{{ allcharcapitalize(item.vehicle_number) }}</td>
                                                            <td><a :href="'{{ url(Session::get('panel') . '/driver/show') }}/' + item
                                                                .id"
                                                                    title="View">@{{ item.mobile_number }}</a></td>

                                                            <td v-if="item.driver_authorised_driver == '1'">


                                                                {{-- <a
                                                                    :href="'{{ url(Session::get('panel') . '/driver/show') }}/' +
                                                                    item
                                                                        .id"> --}}

                                                                <a href="javascript:void(0)"
                                                                    @click="AuthroziedConfirm(item.id)"
                                                                    title="Authorized">

                                                                    <label class="switch">
                                                                        <input type="checkbox" checked>
                                                                        <span class="slider round"></span>
                                                                    </label>

                                                                </a>
                                                            </td>
                                                            <td v-else>
                                                                {{-- <a data-id="@{{ item.id }}"
                                                                    data-authorised="@{{ item.driver_authorised_driver }}"
                                                                    onclick="driver_authorised_driver()"> --}}
                                                                <a href="javascript:void(0)"
                                                                    @click="AuthroziedConfirm(item.id)"
                                                                    title="Authorized">

                                                                    <label class="switch">
                                                                        <input type="checkbox">
                                                                        <span class="slider round"></span>
                                                                    </label>

                                                                </a>
                                                            </td>

                                                            <td v-if="item.user_status=='1'">
                                                                <button type="button"
                                                                    class="btn ripple btn-success btn-sm">Active</button>
                                                            </td>
                                                            <td v-else>
                                                                <button type="button"
                                                                    class="btn ripple btn-danger btn-sm">Inactive</button>
                                                            </td>

                                                            <td v-if="item.created_at!='-0001-11-30 00:00:00'">
                                                                @{{ formatDate(item.created_at) }}</td>
                                                            <td v-else>-</td>

                                                            <!-- <td v-if="item.is_otp=='1' || item.is_otp=='0' ">                                                                                                                                                                                                                                                          </td> -->
                                                            <td class="act-btn">
                                                                <a :href="'{{ url(Session::get('panel') . '/driver/show') }}/' +
                                                                item.id"
                                                                    title="View" class="btn-sm btn-view">
                                                                    <i class="material-icons">remove_red_eye</i>
                                                                </a>
                                                                <!-- <a :href="'{{ url(Session::get('panel') . '/driver/edit') }}/' +
                                                                item.id" title="Edit"
                                                                                                                                                                                                                                                                                                                                                                                                            class="btn-sm btn-edit"><i class="material-icons">edit</i>
                                                                                                                                                                                                                                                                                                                                                                                                        </a> -->
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
    <div class="modal" tabindex="-1" role="dialog" id="modalAuthorizedConfirm">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ @trans('delete_modal.confirm') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you Sure driver Authorized ?</p>
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-cncl"
                        data-dismiss="modal">{{ @trans('delete_modal.cancel') }}</button>
                    @if (Route::currentRouteName() == 'admin.jobs.show')
                        <button type="submit" class="btn btn-danger"
                            @click="authorized">{{ @trans('delete_modal.confirm') }}</button>
                    @else
                        <button type="submit" class="btn btn-danger" @click="authorized">authorized</button>
                    @endif
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
                'columnNum': 5,
                'scrollable': true,
            });
        }
    </script>
    <script>
        var user_indexUrlJson =
            "{{ route('admin.driver.index_json', ['panel' => Session::get('panel'), 'param' => $param]) }}";
        var toggle_status_url = "{{ url(Session::get('panel') . '/travel/toggle-status') }}";
        var user_deleteUrl = "{{ route('admin.driver.destroy', ['panel' => Session::get('panel'), 'id' => 1]) }}";
        var user_authorized = "{{ route('admin.driver.authorized', ['panel' => Session::get('panel'), 'id' => 1]) }}";
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
                                max: <?= $all_joined + 5 ?>,
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
    <script type="text/javascript" src="{{ asset('js/admin/driver.js') }}"></script>
    <script type="text/javascript" src="https://unpkg.com/vue2-datepicker@2.6.4/lib/index.js"></script>

    <!-- Circle Progress js-->
    <script src="{{ asset('assets/js/circle-progress.min.js') }}"></script>
    <script src="{{ asset('assets/js/chart-circle.js') }}"></script>
@endpush
