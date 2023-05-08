@extends('admin.layouts.main')

@section('title')
    @if ($id)
        Edit Trip
    @else
        Add Trip
    @endif
@endsection

@section('content')
    <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css"> -->
    <!-- <script src="https://code.jquery.com/jquery-3.4.1.js"></script> -->
    <style>
        .hidden {
            display: none;
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
                        <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('trip.add_trip') }}</h2>
                        <!-- <ol class="breadcrumb">
                                                    <li class="breadcrumb-item"><a href="#">Forms</a></li>
                                                    <li class="breadcrumb-item active" aria-current="page">Form Validation</li>
                                                </ol> -->
                    </div>
                    <!-- <div class="d-flex">
                                                    <div class="justify-content-center">
                                                        <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                                                        <i class="fe fe-download mr-2"></i> Import
                                                        </button>
                                                        <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                                                        <i class="fe fe-filter mr-2"></i> Filter
                                                        </button>
                                                        <button type="button" class="btn btn-primary my-2 btn-icon-text">
                                                        <i class="fe fe-download-cloud mr-2"></i> Download Report
                                                        </button>
                                                    </div>
                                                </div> -->
                </div>
                <!-- End Page Header -->
                <!-- Row -->
                {{-- <input id="datetimepicker" type="text">
                <link rel="stylesheet" type="text/css" href="jquery.datetimepicker.css"/>
                <script src="./jquery.datetimepicker.js"></script>
                
                <script type="text/javascript">
                    $(function() {
                        $('#datetimepicker').datepicker({
                            viewMode: 'years',
                        });
                    });
                </script> --}}

                <div class="row row-sm justify-content-md-center">
                    <div class="col-xl-12 col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="row">
                                <div class="col-md-6">
                                    @if ($id)
                                        <h6 class="main-content-label mb-1">{{ @trans('trip.edit_trip') }}</h6>
                                    @else
                                        <h6 class="main-content-label mb-1">{{ @trans('trip.add_trip') }}</h6>
                                    @endif
                                    <p class="text-muted card-sub-title">&nbsp;</p>
                                </div>
                                <div class="col-md-6">
                                    <a href="javascript:;" class="btn btn-primary btn-sm" id="add_frauds" style="float: right;">Add Fraud List</a>
                                </div>
                                </div>
                                <form method="POST" id="selectForm2" name="selectForm2" enctype="multipart/form-data"
                                    action="{{ route('admin.trip.store', ['panel' => Session::get('panel')]) }}">
                                    @csrf
                                    @if ($id)
                                        <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                    @endif
                                    @php
                                        $vehicle_type = App\Models\VehicleTypes::all();
                                    @endphp
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <input type="hidden" name="stateCodeAddress" id="stateCodeAddress" value="">
                                    <input type="hidden" name="stateCodeDropAddress" id="stateCodeDropAddress"
                                        value="">
                                    <div class="row">
                                        <div class="col">
                                            <p class="mg-b-10">Trip Owner Name: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control" value="" name="trip_owner_name"
                                                    placeholder="Enter Trip Owner Name" required="" type="text">
                                            </div>
                                        </div>
                                        <div class="col">
                                            <p class="mg-b-10">Mobile No: <span class="tx-danger">*</span></p>
                                            <input class="form-control" value="" name="mobile_no" id="mobile_no"
                                                pattern="[1-9]{1}[0-9]{9}" maxlength="10" placeholder="Enter Mobile Number"
                                                required="" type="text" >
                                            <div id="errorMessageDrivePage"></div>
                                        </div>
                                        <div class="col">
                                            <p class="mg-b-10">{{ @trans('trip.vehicle_type') }}: <span
                                                    class="tx-danger">*</span>
                                            </p>
                                            <select class="form-control mySelect2" id="vehicle_type" name="vehicle_type"
                                                v-model="vehicle_type">

                                                @foreach ($vehicle_type as $vehicle_type)
                                                    <option value="{{ $vehicle_type->id }}">{{ $vehicle_type->name }}
                                                    </option>
                                                @endforeach
                                                <option value="4">Other</option>
                                            </select>
                                            <div class="other hidden"><br>
                                                <input type="text" name="other" placeholder="Enter Vehical Type"
                                                    class="form-control">
                                            </div>
                                            <br>
                                        </div>
                                        <div class="col">
                                            <p class="mg-b-10">{{ @trans('trip.trip_type') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <select class="form-control mySelect2" id="trip_type" name="trip_type"
                                                v-model="trip_type" required>

                                                <option value="Local" selected>Local</option>
                                                <option value="Rental">Rental</option>
                                                <option value="Outstation Trip">Outstation Trip</option>
                                            </select>
                                            <br>
                                            <select class="form-control Outstation hidden" id="Outstation"
                                                name="outstation_trip">

                                                <option value="OneWay" selected>OneWay</option>
                                                <option value="Round Trip">Round Trip</option>
                                            </select><br>

                                        </div>
                                    </div>

                                    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
                                    <div class="row">
                                        <div class="col pickup_location">
                                            <label>PickUp Location: <span class="tx-danger">*</span></label>
                                            <input type="text" name="autocomplete_pickup" id="pickup_location"
                                                class="form-control" placeholder="Choose Pickup Location" required>

                                        </div>
                                        <div class="form-group" id="latitudeArea">
                                            <label>Latitude</label>
                                            <input type="text" id="latitude" name="latitude" class="form-control">
                                        </div>
                                        <div class="form-group" id="longtitudeArea">
                                            <label>Longitude</label>
                                            <input type="text" name="longitude" id="longitude" class="form-control">
                                        </div>
                                        <link
                                            href="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.css"
                                            rel="stylesheet" />
                                        <script src="https://cdnjs.cloudflare.com/ajax/libs/datepicker/0.6.5/datepicker.min.js"></script>

                                        <div class="col">
                                            <p class="mg-b-10">Pickup date & Time: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10">
                                                @php
                                                    $today = Carbon\Carbon::now()
                                                        ->timezone('Asia/Kolkata')
                                                        ->toDateTimeString();
                                                    $today_date = explode(' ', $today);
                                                    $time = explode(':', $today_date[1]);
                                                    $todate = $today_date[0] . 'T' . $time[0] . ':' . $time[1];
                                                @endphp
                                                <input type="hidden" id="today_date" value="{{ $todate }}">
                                                <input type="datetime-local" class="form-control" name="pickup_date_time"
                                                    id="pickup_date" value="{{ $todate }}" required>
                                            </div>
                                            <div class="hidden pickup_time" style="color: red;">Pickup time is Invalid..
                                            </div>
                                            <div class="hidden pickup_date" style="color: red;">Pickup date is Invalid..
                                            </div>
                                        </div>

                                        <div class="col">
                                            <label>Ride Amount: </label>
                                            <input type="number" name="ride_amount" id="ride_amount"
                                                class="form-control" placeholder="Enter Ride Amount"
                                                onchange="checkAmount()">

                                        </div>
                                        <div class="col">
                                            <p class="mg-b-10">Driver Earning: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control" id="driver_earning" name="driver_earning"
                                                    placeholder="Enter Driver Earnings" type="number"
                                                    onchange="checkAmount()">
                                            </div>
                                            <div class="hidden driver_message" style="color: red;">Driver Earning Amount
                                                Is Greater Then Ride Amount..</div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col drop_location">
                                            <label>Drop Location</label>
                                            <input type="text" name="autocomplete_drop" id="drop_location"
                                                class="form-control" placeholder="Choose Drop Location">

                                        </div>
                                        <div class="form-group" id="latitudeArea_drop">
                                            <label>Latitude</label>
                                            <input type="text" id="latitude_drop" name="latitude_drop"
                                                class="form-control">
                                        </div>
                                        <div class="form-group" id="longtitudeArea_drop">
                                            <label>Longitude</label>
                                            <input type="text" name="longitude_drop" id="longitude_drop"
                                                class="form-control">
                                        </div>

                                        <div class="col hidden returnDateTime">
                                            <p class="mg-b-10">Return Date & Time:</p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control" value="" name="return_date_time"
                                                    id="return_datetime" type="datetime-local">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col select_plan hidden">
                                            <p class="mg-b-10">Select Plan:</p>
                                        </div>
                                    </div>
                                    <div class="row">

                                        <div class="col select_plan hidden">
                                            <p class="mg-b-10">Select Hours:</p>
                                            <select class="form-control" id="plan_hours" name="plan_hours">
                                                <option value="" disabled selected>Choose Option</option>
                                                <option value="4">4</option>
                                                <option value="6">6</option>
                                                <option value="8">8</option>
                                                <option value="10">10</option>
                                                <option value="12">12</option>
                                                <option value="other">Other</option>
                                            </select><br>
                                        </div>
                                        <div class="col select_plan hidden">
                                            <p class="mg-b-10">Select KM:</p>
                                            <input type="text" class="form-control" name="select_km" id="select_km"
                                                value="" disabled>
                                        </div>
                                        <div class="col select_plan hidden">
                                            <p class="mg-b-10">Amount:</p>
                                            <input type="text" class="form-control" name="amount" id="amount"
                                                value="" placeholder="Enter Amount">
                                        </div>

                                    </div>

                                    <div class="row">
                                        <div class="col  rental hidden">
                                            <p class="mg-b-10">Extra KM Fare:</p>
                                            <input class="form-control" type="number" name="extra_km_fare"
                                                id="extra_km_fare">
                                        </div>
                                        <div class="col  rental hidden">
                                            <p class="mg-b-10">Extra Time Fare:</p>
                                            <input class="form-control" type="number" name="extra_time_fare"
                                                id="extra_time_fare">
                                        </div>
                                        <div class="col vendor_earnings hidden">
                                            <p class="mg-b-10">Vendor Earnings:</p>
                                            <input class="form-control" type="number" name="vendor_earnings"
                                                id="vendor_earnings" disabled>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col select_outstation hidden">
                                            <p class="mg-b-10">Driver Allowance: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <select class="form-control" id="driver_allowance"
                                                    name="driver_allowance">
                                                    <option value="" selected>selected</option>
                                                    <option value="12">12 Hours</option>
                                                    <option value="24">24 Hours</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col min_km_limit_hide hidden">
                                            <p class="mg-b-10">Extra Per KM: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <div class="form-group">
                                                    <input type="text" name="outstation_extra_per_km"
                                                        id="extra_per_km" placeholder="Extra Per KM"
                                                        class="form-control">
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col extra_per_km_amount hidden">
                                            <p class="mg-b-10 ">Amount : <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control" id="extra_per_km_amount"
                                                    class="extra_per_km_amount" name="outstation_extra_per_km_amount"
                                                    placeholder="Amount" type="text">
                                            </div>
                                        </div>

                                        <div class="col km_limit_outstation hidden">
                                            <p class="mg-b-10">KM Limit: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <div class="form-group">
                                                    <input type="text" name="km_limit_outstation"
                                                        id="km_limit_outstation" placeholder="KM Limit"
                                                        class="form-control">
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col toll_tax hidden">
                                            <p class="mg-b-10">Toll Tax Include: </p>

                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="toll_tax"
                                                    id="flexRadioDefault1" checked>
                                                <label class="form-check-label" for="flexRadioDefault1">
                                                    Yes
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="toll_tax"
                                                    id="flexRadioDefault2">
                                                <label class="form-check-label" for="flexRadioDefault2">
                                                    No
                                                </label>
                                            </div>
                                        </div>

                                    </div>

                            </div>

                            <div id=answer class="col">
                                <p><b>Distance: </b><span id="distance"></span></p>
                                <!-- <button class="btn btn-primary col" onclick="call_fun()" type="button"
                                                                                            style="width:7%;">Calculate</button> -->
                            </div>

                            <div class="mg-t-30">
                                <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block"
                                    id="submit_btn">{{ @trans('trip.add_trip') }}</button>
                            </div>
                            {{-- <input type="text" id="first">
                            <input type="text" id="second">
                            <button type="button" onclick="compare()">Compare!</button>

                            <script>
                            
                            function compare()
                            {
                             var firstNumber = document.getElementById("first").value;
                             var secondNumber = document.getElementById("second").value;
                             if(firstNumber == secondNumber)
                             {
                              alert("The numbers are equal");
                             }
                             else if(firstNumber > secondNumber)
                             {
                              alert("The first number is larger");
                             }
                             else
                             {
                              alert("The second number is larger");
                             }
                            }
                            
                            </script> --}}

                            </form>

                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row-->
        </div>
    </div>
    </div>
    <!-- End Main Content-->

    
    <!-- Image Upload Modal -->
<div class="modal" id="FraudsManageModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-sm">

        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Frauds</h4>
                <button type="button" class="close" data-dismiss="modal" id="close_frauds_modal"> &times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <div >
                      <form action="{{ route('admin.trip.fraud_store', ['panel' => Session::get('panel')]) }}"
                  method="post">
                                @csrf
                                
                      <center><div class="form-group">
                                    <label class="control-label">Mobile Number
                                        <span class="required">*</span></label>
                                <div class="mg-b-12" id="fnWrapper">
                                    <input type="text" name="fraud_mobile_number" id="fraud_mobile_number" class="dropify"  required />
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary px-4">Submit</button>
                            </div>
                          </center>
                      </form>
                </div>
            </div>
        </div>

    </div>
</div>
    
@endsection

@push('pageModals')
@endpush

@push('pageJs')
    <script>
        
        $('#close_frauds_modal').on('click',function(){
              $('#FraudsManageModal').modal('hide'); 
        });
        $('#add_frauds').on('click',function(){
              $('#FraudsManageModal').modal('show'); 
        });
         $('#fraud_mobile_number').keypress(function (e) {    
    
                var charCode = (e.which) ? e.which : event.keyCode    
    
                if (String.fromCharCode(charCode).match(/[^0-9]/g))    
                    return false;                         
            }); 
        function checkAmount() {
            // alert("hello");

            const rideAmount = parseInt(document.getElementById("ride_amount").value);
            const driverAllowance = parseInt(document.getElementById("driver_earning").value);

            const vendor_earnings = rideAmount - driverAllowance;
            $('#vendor_earnings').val(vendor_earnings);
            // console.log("==========" + rideAmount + driverAllowance);

            if (rideAmount < driverAllowance) {
                console.log("button diasbled");
                $('#submit_btn').attr('disabled', true);
                $('.driver_message').css('display', 'block');

            } else {
                console.log("button enabled");
                $('#submit_btn').attr('disabled', false);
                $('.driver_message').css('display', 'none');
            }
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <script type="text/javascript"
        src="https://maps.google.com/maps/api/js?key={{env('GOOGLE_MAPS_API_KEY')}}&libraries=places"></script>


    <script>
        function call_fun() {

            var latitude = $('#latitude').val();
            var longitude = $('#longitude').val();
            var latitude_drop = $('#latitude_drop').val();
            var longitude_drop = $('#longitude_drop').val();

            //pickup location 
            var pickup_location = $('#pickup_location').val();
            var explode_location = pickup_location.split(",");
            console.log(explode_location);

            var index_location = explode_location.slice(-2, -1);
            console.log(index_location);

            var breack_address = index_location.toString();
            console.log(breack_address);
            $('#stateCodeAddress').val(breack_address);

            //drop location
            var drop_location = $('#drop_location').val();
            var explode_drop_location = drop_location.split(",");
            console.log(explode_drop_location);

            var index_drop_location = explode_drop_location.slice(-2, -1);
            console.log(index_drop_location);

            var breack_drop_address = index_drop_location.toString();
            console.log(breack_drop_address);
            $('#stateCodeDropAddress').val(breack_drop_address);

            // var Api = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" + latitude + "," + longitude + "&key=AIzaSyBIr31FcmJILNyqNLLi4Rau6WuHIsqUsAA";

            // // console.log(Api);
            // let request = new XMLHttpRequest();
            // request.open("GET",Api);
            // request.send();
            // request.onload = () => {
            //     console.log(request);
            //     if(request.status == 200)
            //     {
            //         console.log(JSON.parse(request.response));
            //         console.log(JSON.parse(request.response['results']));
            //     }
            //     else{
            //         console.log(`error ${request.status} ${request.statusText}`);
            //     }
            // }



            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '{{ route('admin.km_location', ['panel' => Session::get('panel')]) }}',
                type: 'GET',
                dataType: 'json',
                data: {
                    'latitude': latitude,
                    'longitude': longitude,
                    'latitude_drop': latitude_drop,
                    'longitude_drop': longitude_drop,

                },
                success: function(response_msg) {

                    if (response_msg.success == true) {
                        $('#distance').text(response_msg.dist);
                    } else {
                        console.log("something went wrong !!");
                    }
                },
            });


        }
    </script>

    <script>
        $(document).ready(function() {
            $("#latitudeArea").addClass("d-none");
            $("#longtitudeArea").addClass("d-none");

            $("#latitudeArea_drop").addClass("d-none");
            $("#longtitudeArea_drop").addClass("d-none");
        });
    </script>
    <script>
        google.maps.event.addDomListener(window, 'load', initialize);

        function initialize() {
            var options = {
                componentRestrictions: {
                    country: "in"
                }
            };
            var input = document.getElementById('pickup_location');
            var autocomplete = new google.maps.places.Autocomplete(input, options);
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                var latitude = $('#latitude').val(place.geometry['location'].lat());
                var longitude = $('#longitude').val(place.geometry['location'].lng());
                var stateIndex = place.address_components;
                // console.log(stateIndex);
                // stateIndex = stateIndex - 2;  
                // var stateCode = place.address_components[stateIndex]['short_name'];  
                console.log(place);
                call_fun();
            });


            var input_drop = document.getElementById('drop_location');
            var autocomplete_drop = new google.maps.places.Autocomplete(input_drop, options);
            autocomplete_drop.addListener('place_changed', function() {
                var place1 = autocomplete_drop.getPlace();
                $('#latitude_drop').val(place1.geometry['location'].lat());
                $('#longitude_drop').val(place1.geometry['location'].lng());
                call_fun();
            });
        }
    </script>

    <script>
        //Converts coordinates in degrees to radians.
        function toRad(degrees) {
            return degrees * Math.PI / 180;
        }

        var time1 = "";
        var time2 = "";
        //Using the Timezone DB API
        function getTime1(lat, lng) {
            var tz = new TimeZoneDB;
            var returnValue = "Blank";
            tz.getJSON({
                key: "W5OG3X1XCNK1",
                lat: lat,
                lng: lng
            }, function(data) {
                setTime(data.timestamp, 1);
            });
        }

        function getTime2(lat, lng) {
            var tz = new TimeZoneDB;
            var returnValue = "Blank";
            tz.getJSON({
                key: "W5OG3X1XCNK1",
                lat: lat,
                lng: lng
            }, function(data) {
                setTime(data.timestamp, 2);
            });
        }

        function updateTime() {
            timeDifference = time1 - time2;
            document.getElementById('timeDiff').innerHTML = Math.abs(timeDifference) + " hours.";
        }

        function setTime(data, timeToSet) {
            var date = new Date(data * 1000);
            if (timeToSet == 1) {
                time1 = date.getHours();
            } else {
                time2 = date.getHours();
            }
            updateTime();
        }
    </script>

    <script>
        $('#vehicle_type').on('change', function() {
            var type = $('#vehicle_type').find(":selected").text();
            if (type == "Other") {
                $('.other').css('display', 'block');
            } else {
                $('.other').css('display', 'none');
            }
        });
    </script>
    <script>
        $('#driver_allowance').on('change', function() {
            var driver_allowance = $('#driver_allowance').find(":selected").val();
            if (driver_allowance == 12 || driver_allowance == 24) {

                // $('.min_km_limit_hide').css('display', 'none');
                $('.extra_per_km_amount').css('display', 'block');
            } else {
                $('.extra_per_km_amount').css('display', 'none');
                // $('.min_km_limit_hide').css('display', 'block');
            }
        });

        $('#pickup_date').on('change', function() {
            var selectedDate = $(this).val();
            var today = $('#today_date').val();

            const date1 = new Date(selectedDate);
            const date2 = new Date(today);
            var diffDays = getDifferenceInDays(date1, date2);
            console.log(Math.floor(getDifferenceInDays(date1, date2)));
            console.log(getDifferenceInHours(date1, date2));
            var diffMin = getDifferenceInMinutes(date1, date2);
            // console.log(getDifferenceInSeconds(date1, date2));
            var formattedDate1 = date1.getDate() + '/' + date1.getMonth() + '/' + date1.getFullYear();
            var formattedDate2 = date2.getDate() + '/' + date2.getMonth() + '/' + date2.getFullYear();
            if (formattedDate1.localeCompare(formattedDate2) === 0) {
                if (Math.floor(diffMin) < 0) {
                    // alert('Pickup time is Invalid');
                    $('.pickup_time').css('display', 'block');
                    $('.pickup_date').css('display', 'none');
                    $('#submit_btn').attr('disabled', true);
                } else {
                    $('.pickup_time').css('display', 'none');
                    $('.pickup_date').css('display', 'none');
                    $('#submit_btn').attr('disabled', false);
                }
            } else if (Math.floor(diffDays) < 0) {
                // alert('Pickup date is Invalid');
                $('.pickup_date').css('display', 'block');
                $('.pickup_time').css('display', 'none');
                $('#submit_btn').attr('disabled', true);
            } else {
                $('.pickup_date').css('display', 'none');
                $('.pickup_time').css('display', 'none');
                $('#submit_btn').attr('disabled', false);
            }

        });

        function getDifferenceInDays(date1, date2) {
            const diffInMs = date1 - date2;
            return diffInMs / (1000 * 60 * 60 * 24);
        }

        function getDifferenceInHours(date1, date2) {
            const diffInMs = Math.abs(date2 - date1);
            return diffInMs / (1000 * 60 * 60);
        }

        function getDifferenceInMinutes(date1, date2) {
            const diffInMs = date1 - date2;
            return diffInMs / (1000 * 60);
        }

        function getDifferenceInSeconds(date1, date2) {
            const diffInMs = Math.abs(date2 - date1);
            return diffInMs / 1000;
        }
    </script>
    <script>
        $('#trip_type').on('change', function() {
            var trip_type = $('#trip_type').find(":selected").val();
            if (trip_type == "Outstation Trip") {
                $('.Outstation').css('display', 'block');
                $('.select_outstation').css('display', 'block');
                $('.drop_location').css('display', 'block');
                $('.toll_tax').css('display', 'block');
                $('.select_plan').css('display', 'none');
                $('.rental').css('display', 'none');
                $('.vendor_earnings').css('display', 'block');
                $('.min_km_limit').css('display', 'block');
                $('.km_limit_outstation').css('display', 'block');
                $('.extra_per_km_amount').css('display', 'none');
                $('.min_km_limit_hide').css('display', 'block');


            } else if (trip_type == "Rental") {
                $('.Outstation').css('display', 'none');
                $('.select_outstation').css('display', 'none');
                $('.drop_location').css('display', 'none');
                $('.toll_tax').css('display', 'none');
                $('.select_plan').css('display', 'block');
                $('.rental').css('display', 'block');
                $('.vendor_earnings').css('display', 'block');
                $('.min_km_limit').css('display', 'none');
                $('.km_limit_outstation').css('display', 'none');
                $('.extra_per_km_amount').css('display', 'none');
                $('.min_km_limit_hide').css('display', 'none');


            } else {
                $('.Outstation').css('display', 'none');
                $('.select_outstation').css('display', 'none');
                $('.drop_location').css('display', 'block');
                $('.toll_tax').css('display', 'none');
                $('.select_plan').css('display', 'none');
                $('.rental').css('display', 'none');
                $('.vendor_earnings').css('display', 'none');
                $('.min_km_limit').css('display', 'none');
                $('.km_limit_outstation').css('display', 'none');
                $('.min_km_limit_hide').css('display', 'none');

            }

        });
    </script>
    <script>
        $('#plan_hours').on('change', function() {
            var plan_hours = $('#plan_hours').find(":selected").val();
            if (plan_hours == 4) {
                $('#select_km').val(40);
                $('#select_km').attr('disabled', true);
                $('#select_km_input').css('display', 'none');

            } else if (plan_hours == 6) {
                $('#select_km').val(60);
                $('#select_km').attr('disabled', true);
                $('#select_km_input').css('display', 'none');


            } else if (plan_hours == 8) {
                $('#select_km').val(80);
                $('#select_km').attr('disabled', true);
                $('#select_km_input').css('display', 'none');


            } else if (plan_hours == 10) {
                $('#select_km').val(100);
                $('#select_km').attr('disabled', true);
                $('#select_km_input').css('display', 'none');


            } else if (plan_hours == 12) {
                $('#select_km').val(120);

            } else if (plan_hours == "other") {
                $('#select_km').val(0);
                $('#select_km').attr('disabled', false);
                $('.select_km_input').css('display', 'block');
            }
        });
    </script>

    <script>
        $('.Outstation').on('change', function() {
            var round_trip = $('#Outstation').find(":selected").val();
            if (round_trip == "Round Trip") {
                $('.vendor_earnings').css('display', 'block');
                $('.returnDateTime').css('display', 'block');
            } else {
                // $('.vendor_earnings').css('display', 'block');
                $('.returnDateTime').css('display', 'none');
            }
        });
    </script>
    <script>
        @if (Session::has('message'))
            Snackbar.show({
                pos: 'bottom-right',
                text: "{!! session('message') !!}",
                actionText: 'Okay'
            });
        @endif

        $(document).ready(function() {
            $('#city_id').select2({
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('api.cities.search') }}',
                    dataType: 'json',
                },
            });
        });

        function editProfile() {
            $('#editProfile').modal();
        }

        function submitEditProfile() {
            $("#selectForm2").submit();
        }
        $(document).ready(function () {    
    
            $('#mobile_no').keypress(function (e) {    
    
                var charCode = (e.which) ? e.which : event.keyCode    
    
                if (String.fromCharCode(charCode).match(/[^0-9]/g))    
                    return false;                         
            }); 
            
           
            
            
            $('#mobile_no').change(function (e) {    
                $('#submit_btn').prop('disabled', true);
                $('#errorMessageDrivePage').html('');
                           mobile = $(this).val();
                $.ajax({
                    type: 'GET',
                            url: "{{ route('admin.trip.checkFraud',['panel' => Session::get('panel')]) }}",
                            data: {
                            'mobile': mobile 
                            },
                            dataType: 'json',
                            success: function(response) {
                                if(response.status == true){
                                    $('#errorMessageDrivePage').html('<span class="text-danger">This user is fraud!</span>');
                                    $('#submit_btn').prop('disabled', true);
                                }else{
                                    $('#errorMessageDrivePage').html('<span class="text-success">User verified!</span>');
                                    $('#submit_btn').prop('disabled', false);
                                }
                             
                            }, error:function(xhr, status, error) {
                                console.log(error);
                                $('#submit_btn').prop('disabled', false);
//                                 
//                                $('#driving_license').html('Search');
//                                $('#errorMessageDrivePage').html('<span class="text-danger">' + error);
                            } 
                    });
            }); 
    
        });   
 
    </script>

    <!-- Internal Form-validation js-->
    <script src="{{ asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>
@endpush