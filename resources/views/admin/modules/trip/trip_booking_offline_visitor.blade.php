@extends('admin.layouts.main')

@section('title') {{ @trans('trip.website') }} {{ @trans('trip.trip') }} Visitor @endsection

@section('content')

<?php 
$trip_type = !empty(request()->trip_type) ? request()->trip_type : 1; 

if($trip_type ==1){ 
    $trip_text = "Local";
}else if($trip_type ==2){
    $trip_color = "success";
    $trip_text = "Rental";
}else if($trip_type ==3){
    $trip_text = "OutStation";
} 
?>

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{$trip_text}} - {{ @trans('trip.website') }} {{ @trans('trip.trip') }} Visitor</h2>
                </div> 
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-lg-3">
                                    <div class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('usertype.record_per_page') }}&nbsp;</label>
                                        <select class="form-control" id="per_page" v-model="per_page" @input="perPage">
                                            <option value="25">25 </option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <div class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <a href="{{ route('admin.customer_trip.customer_trip_offline_visitor',['panel' => Session::get('panel')]) }}?trip_type=1" class="btn btn-primary">Local Trip</a>&nbsp;
                                        <a href="{{ route('admin.customer_trip.customer_trip_offline_visitor',['panel' => Session::get('panel')]) }}?trip_type=2" class="btn btn-primary">Rental Trip</a>&nbsp;
                                        <a href="{{ route('admin.customer_trip.customer_trip_offline_visitor',['panel' => Session::get('panel')]) }}?trip_type=3" class="btn btn-primary">OutStation Trip</a>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group search-input float-right mb-3">
                                        <input type="text" class="form-control" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">                                
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>Customer Name</b><a href="javascript:void(0)"
                                                                                   :class="classSort('first_name')" @click="sort('first_name')"></a>

                                                        <th><b>Email</b><a href="javascript:void(0)"
                                                                           :class="classSort('email')" @click="sort('email')"></a>
                                                        <th><b>Phone Number</b><a href="javascript:void(0)"
                                                                                  :class="classSort('phone_number')" @click="sort('phone_number')"></a>
                                                        </th>
                                                        <th><b>Pickup Address</b><a href="javascript:void(0)"
                                                                                    :class="classSort('pickup_address')" @click="sort('pickup_address')"></a>
                                                        </th>
                                                        <th><b>Drop Address </b><a href="javascript:void(0)"
                                                                                   :class="classSort('drop_address')" @click="sort('drop_address')"></a>
                                                        </th>
                                                        <th><b>Booked Date</b><a href="javascript:void(0)"
                                                                                 :class="classSort('booked_date')" @click="sort('booked_date')"></a>
                                                        </th>
                                                        <th><b>Booked Time</b><a href="javascript:void(0)"
                                                                                 :class="classSort('booked_time')" @click="sort('booked_time')"></a>
                                                        </th>
                                                        <th><b>Total <br/>Amount</b><a href="javascript:void(0)"
                                                                                           :class="classSort('grand_total')" @click="sort('grand_total')"></a>
                                                        </th>
                                                        <th><b>Advance Booking<a href="javascript:void(0)"
                                                                                 :class="classSort('advance_booking_amount')" @click="sort('advance_booking_amount')"></a>
                                                        </th>
                                                        <th><b>Status</b><a href="javascript:void(0)"
                                                                            :class="classSort('status')" @click="sort('status')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('subscriptionCoupons.action') }}</b></th>

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td>@{{ item.first_name }} @{{ item.last_name }}</td>
                                                        <td>@{{ item.email }}</td>
                                                        <td>@{{ item.phone_number }}</td>
                                                        <td>@{{ item.pickup_address }}</td>
                                                        <td>@{{ item.drop_address }}</td>
                                                        <td>@{{ item.booked_date }}</td>
                                                        <td>@{{ item.booked_time }}</td>
                                                        <td>₹ @{{ item.grand_total }}</td>
                                                        <td>₹ @{{ item.advance_booking_amount }}</td>

                                                        <td>
                                                            <span class="text-danger">Pending</span>
                                                        </td>
                                                        <!--<td>@{{ item.payment_status }}</td>-->
                                                        <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/trip/customer-trip-offline-visitor/show')}}/'+ item.id" title="Edit"
                                                               class="btn-view btn-sm"><i class="material-icons">remove_red_eye</i>
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


{{-- <h5>7</h5> --}}
{{-- @include('admin.modules.trip.trip_booking_offline_visitor') --}}
<!-- End Main Content-->

@endsection
@push('pageModals') 


@push('pageJs')
<script type="text/javascript">
    $('.nav-visitor-trip-customer').addClass('active');

    function initListUsers()
    {
    $(".table-data .table-responsive").freezeTable({
    'columnNum': 0,
            /* 'scrollable': true, */
    });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.trip.customer_trip_offline_visitor_json',['panel' => Session::get('panel')])}}?trip_type={{$trip_type}}";
    var toggle_status_url = "{{url(Session::get('panel').'/users/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.trip.destroy',['panel' => Session::get('panel')])}}";
    @if (Session::has('message'))
            Snackbar.show({
            pos: 'bottom-right',
                    text: "{!! session('message') !!}",
                    actionText: 'Okay'
            });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/tripOffline.js') }}"></script>
@endpush
