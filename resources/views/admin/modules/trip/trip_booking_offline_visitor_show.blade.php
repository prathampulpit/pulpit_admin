@extends('admin.layouts.main')

@section('title')
{{ @trans('trip.website') }} {{ @trans('trip.trip') }} Visitor
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">
            <input type="hidden" name="user_id" id="user_id" value="{{ $user->user_id }}">
            <input type="hidden" name="id" id="id" value="{{ $trip_booking->id }}">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('trip.website') }} {{ @trans('trip.trip') }} Visitor</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index',['panel' => Session::get('panel')]) }}">{{ @trans('trip.website') }} {{ @trans('trip.trip') }} Visitor</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ @trans('trip.details') }}</li>
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
                                        <h4 class="main-content-label tx-13 mg-b-20">Customer</h4>
                                        <hr>
                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.first_name') }}</span>
                                                                    <div> {{ strtoupper($trip_booking->first_name) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.last_name') }}</span>
                                                                    <div> {{$trip_booking->last_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.email') }}</span>
                                                                    <div> {{$trip_booking->email}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.phone_number') }}</span>
                                                                    <div> {{$trip_booking->phone_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.trip_booked_on') }}</span>
                                                                    <div>{{ @trans('trip.website') }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.created') }} {{ @trans('trip.date') }}</span>
                                                                    <div> {{$trip_booking->created_at}} UTC</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">{{ @trans('trip.trip') }}</label>
                                        <div class="d-sm-flex"> 
                                            <div class="row col-md-12">
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.name') }}</span>
                                                                    <div> {{$trip_booking->vehicle_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.pickup_location') }}</span>
                                                                    <div> {{$trip_booking->pickup_address}}</div>
                                                                    <div>Lat/Long  : {{$trip_booking->pickup_latitude}} / {{$trip_booking->pickup_longitude}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.drop_location') }}</span>
                                                                    <div> {{$trip_booking->drop_address}}</div>
                                                                    <div>Long : {{$trip_booking->drop_latitude}} / {{$trip_booking->drop_longitude}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.pickup_date_time') }}</span>
                                                                    <div> {{$trip_booking->booked_date}} {{$trip_booking->booked_time}}</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.trip_booked_on') }}</span>
                                                                    <div> {{$trip_booking->created_at}} UTC</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.updated') }} {{ @trans('trip.date') }}</span>
                                                                    <div> {{$trip_booking->updated_at}} UTC</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--                                    <div class="border-top"></div>
                                                                        <div class="p-4">
                                                                            <label class="main-content-label tx-13 mg-b-20">{{ @trans('vehicles.vehicles') }}</label>
                                                                            <div class="d-sm-flex"> 
                                                                                <div class="row col-md-12">
                                                                                    <div class="col-md-3 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.name') }}</span>
                                                                                                        <div> {{$trip_booking->vehicle_name}}</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.trip') }} {{ @trans('trip.gst') }}</span>
                                                                                                        <div> {{$trip_booking->GST}} %</div>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.gst') }}</span>
                                                                                                        <div>₹ {{$trip_booking->GSTCalculate}}</div> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.amount') }}</span>
                                                                                                        <div>₹ {{$trip_booking->grand_total}}</div> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.total') }} {{ @trans('trip.km') }}</span>
                                                                                                        <div>{{$trip_booking->KM}} KM</div> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-2 pl-0">
                                                                                        <div class="mg-sm-r-20 mg-b-10">
                                                                                            <div class="main-profile-contact-list">
                                                                                                <div class="media">
                                                                                                    <div class="media-body"> <span>{{ @trans('trip.payment_status') }}</span>
                                                                                                        <div>
                                                                                                            {{$trip_booking->payment_status}}
                                    
                                                                                                        </div> 
                                                                                                    </div>
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                    
                                                                                </div>
                                                                            </div>
                                                                        </div>-->
                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Payment</label>
                                        <div class="d-sm-flex"> 
                                            <div class="row col-md-12">
                                                <table style="width:100%">
                                                    <thead>
                                                        <tr>
                                                            <th>KM</th>
                                                            <th>{{ @trans('trip.amount') }}</th>
                                                            <th>GST %</th>
                                                            <th>GST Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>{{$trip_booking->KM}} KM</td>
                                                            <td>₹ {{$trip_booking->grand_total}}</td>
                                                            <td>{{$trip_booking->GST}} %</td>
                                                            <td>₹ {{$trip_booking->GSTCalculate}}</td> 
                                                        </tr>
                                                    </tbody>
                                                </table>
                                                <!--<div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.name') }}</span>
                                                                    <div> {{$trip_booking->vehicle_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.trip') }} {{ @trans('trip.gst') }}</span>
                                                                    <div> {{$trip_booking->GST}} %</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.gst') }}</span>
                                                                    <div>₹ {{$trip_booking->GSTCalculate}}</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.amount') }}</span>
                                                                    <div>₹ {{$trip_booking->grand_total}}</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.total') }} {{ @trans('trip.km') }}</span>
                                                                    <div>{{$trip_booking->KM}} KM</div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.payment_status') }}</span>
                                                                    <div>
                                                                        {{$trip_booking->payment_status}}

                                                                    </div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                -->
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">{{ @trans('trip.trip') }} {{ @trans('trip.status') }} </label>
                                        <div class="d-sm-flex"> 
                                            <div class="row col-md-12">
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('trip.status') }}</span>
                                                                    <div style="margin-top: 9px;">
                                                                        <select class="form-control" name="status" id="status">
                                                                            <option value="1" @if($trip_booking->status == 1) selected=selected @endif>Un-Assigned</option>
                                                                            <option value="2" @if($trip_booking->status == 2) selected=selected @endif>Assigned</option>
                                                                            <option value="3" @if($trip_booking->status == 3) selected=selected @endif>Completed</option>
                                                                            <option value="4" @if($trip_booking->status == 4) selected=selected @endif>Cancel</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> 
                                                                    <span>{{ @trans('trip.reason') }}</span>
                                                                    <div style="margin-top: 9px;"><textarea name="reason" id="reason" class="form-control" placeholder="{{ @trans('trip.reason') }}">{{$trip_booking->reason}}</textarea></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> 
                                                                    <div style="margin-top: 25px;float: right;"><button class="btn btn-primary" name="submit" id="update_status">{{ @trans('trip.save') }}</button></div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-2 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> 
                                                                    <?php $url = url(Session::get('panel') . '/customer-trip-offline-visitor'); ?>

                                                                    <div style="margin-top: 25px;"><a href="{{$url}}/" class="btn btn-danger">{{ @trans('trip.back') }}</a></div> 
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                

                                            </div>
                                        </div>
                                    </div>

                                </div>

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

@push('pageJs')  



<?php $url = url(Session::get('panel') . '/trip/customer_trip_offline_visitor/update_status'); ?>

<script>

    $('.nav-visitor-trip-customer').addClass('active');

    $('body').on('click', '#update_status', function () {
        var status = $('#status').val();
        var reason = $('#reason').val();
        var id = $('#id').val();
        $('#update_status').html("Please wait....");

        $.ajax({
            url: "{{ $url }}",
            type: "POST",
            data: {
                id: id,
                status: status,
                reason: reason
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            cache: false,
            success: function (result) {
                result = JSON.parse(result);
                toastr.success(result.message);
                $('#update_status').html("Save");

            }
        });
    });
    /*    function getCity1() {
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
     success: function (result) {
     $(".city_lists1").html(result);
     //$("#city_id1").html(result);
     }
     });
     } */
</script>


@endpush