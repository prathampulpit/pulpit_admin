@extends('admin.layouts.main')

@section('title')
@if ($id)
Edit Vehicle Brand Models
@else
Add Vehicle Brand Models
@endif
@endsection

@section('content')
<!-- Main Content-->
<div class="main-content side-content pt-0">
    <loading :active.sync="isLoading"></loading>
    <div class="container-fluid">
        <div class="inner-body"> 
            <!-- Row -->
            <div class="row row-sm justify-content-md-center" style="margin-top: 40px;">
                <div class="col-xl-12 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if ($id)
                                <h6 class="main-content-label mb-1">
                                    {{ @trans('vehicleBrandModels.edit_vehicleBrandModels') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">
                                    {{ @trans('vehicleBrandModels.add_vehicleBrandModels') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>

                            <form method="GET"
                                  action="{{ route('admin.vehicles.manage', ['panel' => Session::get('panel')]) }}"
                                  class="parsley-style-1" id="search_vehicle_form" name="search_vehicle_form" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4 col-sm-6">
                                        <p class="mg-b-10">Vehicle Number</p>
                                        <input class="form-control" type="text" name="vehicle_number" id="vehicle_number"
                                               value="@if($vehicle_number){{ $vehicle_number }} @endif">
                                        <div id="errorMessageVehiclePage"></div>
                                    </div>
                                    <div class="col-lg-2 col-md-2 col-sm-6">
                                        <button type="button" name="search" id="search_vehicles" class="btn btn-primary " style="margin-top: 30px;">Search Vehicles</button>
                                    </div> 
                                </div>
                            </form>
                            <hr/>
                            <form method="post"
                                  action="{{ route('admin.vehicles.save', ['panel' => Session::get('panel')]) }}"
                                  class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                @if ($id)
                                <input type="hidden" name="user_id" id="user_id" value="{{ $data->user_id }}">
                                <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                @endif
                                @if($vehicle_id)
                                <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicle_id }}">
                                @endif



                                <?php
                                
                                $owner_name = !empty($vehicle->owner_name) ? $vehicle->owner_name : '';
                                $owner_mobile_no = !empty($vehicle->owner_mobile_no) ? $vehicle->owner_mobile_no : '';
                                $permit_no = !empty($vehicle->permit_no) ? $vehicle->permit_no : '';
                                $puc_number = !empty($vehicle->puc_number) ? $vehicle->puc_number : '';

                                $insurance_exp_date = $vehicle->insurance_validity;
                                $permit_validity_upto = $vehicle->permit_validity_upto;
                                $fitness_upto = $vehicle->fitness_upto;
                                $puc_exp_date = $vehicle->puc_valid_upto;
                                $registration_date = $vehicle->registration_date;
                                $fuel_type = $vehicle->fuel_type;
                                $street_address = $vehicle->permanent_address;
                                $current_address = $vehicle->current_address;
                                $getCityandStateData = App\Models\Settings::getPincode($street_address);

                                $city = !empty($vehicle->city) ? $vehicle->city : '';
                                if (empty($city)) {
                                    $city = !empty($getCityandStateData->city) ? $getCityandStateData->city : '';
                                }
                                $state = !empty($vehicle->state) ? $vehicle->state : '';
                                if (empty($state)) {
                                    $state = !empty($getCityandStateData->state) ? $getCityandStateData->state : '';
                                }
                                $pincode = !empty($getCityandStateData->pincode) ? $getCityandStateData->pincode : '';
                                ?>



                                <div id="show_form_box">

                                    <div class="row" style="margin-top:10px; ">
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">Owner Name:</p>
                                            <div class="mg-b-10">
                                                <input type="text" name="owner_name" id="owner_name" value="{{$owner_name}}" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">Owner Phone Number:</p>
                                            <div class="mg-b-10">
                                                <input type="text" name="owner_mobile_no" id="owner_mobile_no" value="{{$owner_mobile_no}}" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">Permit Number:</p>
                                            <div class="mg-b-10">
                                                <input type="text" name="permit_no" id="permit_no" value="{{$permit_no}}" class="form-control"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">PUC Number:</p>
                                            <div class="mg-b-10">
                                                <input type="text" name="puc_number" id="puc_number" value="{{$puc_number}}" class="form-control"/>
                                            </div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">Vehicles Type
                                                <span class="tx-danger">*</span>
                                            </p>

                                            @php
                                            $vehicle_type = App\Models\VehicleTypes::all();
                                            @endphp
                                            <select class="form-control" name="vehicle_type" id="vehicle_type"
                                                    required="" onchange="vehicle_types()">
                                                <option value="" label="Choose one"></option>
                                                @foreach ($vehicle_type as $val)
                                                <option value="{{ $val->id }}"
                                                        @if (isset($data->vehicle_type_name) && $val->name == $data->vehicle_type_name) selected @endif>
                                                    {{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">{{ @trans('vehicleBrandModels.brand') }} Name</p>
                                            <select class="form-control" name="brand_id" id="brand_id" required
                                                    onchange="vehicle_brand(), brand_names()">
                                                <option value="" label="Choose one"></option>
                                                @foreach ($vehicleBrands as $val)
                                                <option value="{{ $val->id }}"
                                                        @if ($id) @if ($val->id == $data->brand_id) selected @endif
                                                    @endif>{{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                            <div id="brand_error"></div>
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3">

                                            <p class="mg-b-10">Model Name <span class="tx-danger">*</span></p>
                                            {{-- <input class="form-control" type="text" name="model_name" id="model_name"
                                                value="@if (old('model_name')) {{ old('model_name') }}@elseif($id){{ $data->model_name }} @endif"> --}}
                                            @php
                                            $model_id = App\Models\VehicleBrandModels::all();
                                            @endphp
                                            <select class="form-control" onchange="model_names()" name="model_name"
                                                    id="model_name">
                                                <option value="" label="Choose one"></option>
                                                @foreach ($model_id as $val)
                                                <option value="{{ $val->id }}"
                                                        @if (isset($data->model_name) && $val->name == $data->model_name) selected @endif>
                                                    {{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                            <div id="model_error"></div>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3">
                                            <p class="mg-b-10">Vehicles Fuel Type
                                                <span class="tx-danger">*</span>
                                            </p>

                                            @php
                                            $VehicleFuel = App\Models\VehicleFuelType::all();
                                            @endphp
                                            <select class="form-control" name="vehicle_fuel_type" id="vehicle_fuel_type"
                                                    required="">
                                                <option value="" label="Choose one"></option>
                                                @foreach ($VehicleFuel as $val)
                                                <option value="{{ $val->id }}"
                                                        @if ($val->name == $fuel_type) selected @endif>
                                                    {{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @php
                                        // dd($data);
                                        @endphp

                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">Insurance Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                   value="@if(!empty($insurance_exp_date)){{ $insurance_exp_date }} @endif"
                                                   name="insurance_exp_date" placeholder="MM-DD-YYYY" required=""
                                                   type="text">
                                        </div>


                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">Permit Exp Date

                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                   value="@if(!empty($permit_validity_upto)){{ $permit_validity_upto }} @endif"
                                                   name="permit_exp_date" placeholder="MM-DD-YYYY" required=""
                                                   type="text">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">Fitness Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                   value="@if(!empty($fitness_upto)) {{ $fitness_upto }} @endif"
                                                   name="fitness_exp_date" placeholder="MM-DD-YYYY" required=""
                                                   type="text">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">PUC Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                   value="@if(!empty($puc_exp_date)) {{ $puc_exp_date }} @endif"
                                                   name="puc_exp_date" placeholder="MM-DD-YYYY" required=""
                                                   type="text">
                                        </div>

                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">Registration Year
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control datepicker-year hasDatepicker"
                                                   value="@if($registration_date){{ date('Y',strtotime($registration_date)) }} @endif"
                                                   name="registration_year" placeholder="YYYY" required=""
                                                   type="text">
                                        </div>


                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">City
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control" name="city" placeholder="City"   type="text" value='@if(!empty($city)){{trim($city)}}@endif'>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">State
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control" name="state" placeholder="State"  type="text" value='@if(!empty($state)){{trim($state)}}@endif'>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3" style='margin-top:10px;'>
                                            <p class="mg-b-10">Pincode
                                            </p>
                                            <input class="form-control" name="pincode" placeholder="Pincode"  type="text" value='@if(!empty($pincode)){{trim($pincode)}}@endif'>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4" style='margin-top:10px;'>
                                            <p class="mg-b-10">Street Address
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <textarea class="form-control" name="street_address" placeholder="Street Address" required="" type="text">@if(!empty($street_address)){{trim($street_address)}}@endif
                                            </textarea>
                                        </div>
                                        <div class="col-lg-4 col-md-4 col-sm-4" style='margin-top:10px;'>
                                            <p class="mg-b-10">Current Address
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <textarea class="form-control" name="current_address" placeholder="Current Address" required="" type="text">@if(!empty($current_address)){{trim($current_address)}}@endif
                                            </textarea>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">RC Front: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="rc_front_url" id="rc_front_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->rc_front_url}}" @endif required="true"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">RC Back: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="rc_back_url" id="rc_back_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->rc_back_url}}" @endif required="true"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Insurance Document:</p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="insurance_doc_url" id="insurance_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->insurance_doc_url}}" @endif/>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Permit Document:</p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="permit_doc_url" id="permit_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->permit_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Fitness Document:</p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="fitness_doc_url" id="fitness_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->fitness_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">PUC Document: </p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="puc_doc_url" id="puc_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->puc_doc_url}}" @endif/>
                                            </div>
                                        </div>

                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Agreement Document: </p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="agreement_doc_url" id="agreement_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->agreement_doc_url}}" @endif/>
                                            </div>
                                        </div> 
                                        <div class="col-lg-12 col-md-12 col-sm-12">
                                            <label class="main-content-label tx-13 mg-b-20">Vehicle Images</label>
                                        </div>
                                     
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Front: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Front" id="Front" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_front_url}}" @endif required="true"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Back: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Back" id="Back" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_back_url}}" @endif required="true"/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Desktop: </p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Desktop" id="Desktop" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Left: </p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Left" id="Left" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_front_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Right:</p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Right" id="Right" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_back_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-md-2 col-sm-2">
                                            <p class="mg-b-10">Interior: </p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="Interior" id="Interior" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .ui-datepicker-calendar {
                                            display: none;
                                        }
                                    </style>
                                    {{-- <input class="date-own form-control" style="width: 300px;" type="text"> --}}
                                    <div class="mg-t-30">
                                        <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="manage_vehicle_save_buttton"
                                               value="{{ @trans('user.save') }}">
                                    </div>



                                </div>



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
@endsection

@push('pageModals')
@endpush

@push('pageJs')
<!-- Internal Form-validation js form-elements-->
<script src="{{asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
<script src="{{asset('assets/js/form-validation.js') }}"></script>
<!-- <script src="{{asset('assets/js/form-elements.js') }}"></script> -->
<script src="{{asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.js') }}"></script>

<!-- Internal Fileuploads js-->
<script src="{{asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script>

<!-- InternalFancy uploader js-->
<script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.fancy-fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/fancy-uploader.js') }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script>
                                                $('.fc-datepicker').datepicker({
                                                showOtherMonths: true,
                                                        selectOtherMonths: true,
                                                        dateFormat: 'yy-mm-dd'
                                                });
                                                $('.datepicker-year').datepicker({
                                                format: "yyyy",
                                                        weekStart: 1,
                                                        orientation: "bottom",
                                                        keyboardNavigation: false,
                                                        viewMode: "years",
                                                        minViewMode: "years"
                                                });</script>
<script>
    function model_names() {
    const $validModelName = $('#model_error');
    $validModelName.text('');
    var model_name = $("#model_name").find(":selected").text();
    // alert(model_name);

    if (model_name == "Select Model Name") {
    $validModelName.text('Please Select Model Name');
    $validModelName.css('color', 'red');
    console.log("Not valid");
    } else {
    console.log("valid");
    $validModelName.text('');
    }
    }
    function brand_names() {
    const $validModelName = $('#brand_error');
    $validModelName.text('');
    var model_name = $("#brand_id").find(":selected").text();
    // alert(model_name);

    if (model_name == "Select Brand Name") {
    $validModelName.text('Please Select Brand Name');
    $validModelName.css('color', 'red');
    console.log("Not valid");
    } else {
    console.log("valid");
    $validModelName.text('');
    }
    }
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
</script>
<script>
    function vehicle_brand() {
    var brand_id = $("#brand_id").find(":selected").val();
    // alert(brand_id);
    // alert(state);
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: 'GET',
            url: "{{ route('admin.vehicleBrandModels.select', ['panel' => Session::get('panel')]) }}",
            data: {
            'brand_id': brand_id,
            },
            dataType: 'json',
            success: function(response) {
            console.log(response);
            var partyNameArrays_e = [];
            var partyNameArray = [];
            var partyNameArrayBefore = [];
            let optionLists;
            partyNameArrays_e = {
            'text': "Select Model Name",
                    'value': "0"
            };
            $('#model_name').empty();
            partyNameArray.push(partyNameArrays_e);
            response.model.forEach(element => {
            partyNameArrayBefore = {
            'text': element.name,
                    'value': element.id
            };
            partyNameArray.push(partyNameArrayBefore);
            });
            optionLists = document.getElementById('model_name').options;
            length_e = optionLists.length;
            partyNameArray.forEach(option => {
            optionLists.add(
                    new Option(option.text, option.value, option
                            .selected)
                    )
            });
            model_names()
            }

    });
    }
    function vehicle_types() {
    var type_id = $("#vehicle_type").find(":selected").val();
    // alert(type_id);
    // alert(state);
    $.ajaxSetup({
    headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
    });
    $.ajax({
    type: 'GET',
            url: "{{ route('admin.vehicleTypeBrand.select', ['panel' => Session::get('panel')]) }}",
            data: {
            'type_id': type_id,
            },
            dataType: 'json',
            success: function(response) {
            console.log(response);
            var partyNameArrays_e = [];
            var partyNameArray = [];
            var partyNameArrayBefore = [];
            let optionLists;
            partyNameArrays_e = {
            'text': "Select Brand Name",
                    'value': "0"
            };
            $('#brand_id').empty();
            partyNameArray.push(partyNameArrays_e);
            response.model.forEach(element => {
            partyNameArrayBefore = {
            'text': element.name,
                    'value': element.id
            };
            partyNameArray.push(partyNameArrayBefore);
            });
            optionLists = document.getElementById('brand_id').options;
            length_e = optionLists.length;
            partyNameArray.forEach(option => {
            optionLists.add(
                    new Option(option.text, option.value, option
                            .selected)
                    )
            });
            brand_names()
            }

    });
    }
</script>
<script>
    $('body').on('click', '#search_vehicles', function(){
    $('#search_vehicles').html('Searching..');
    vehicle_number = $('#vehicle_number').val();
    $('#errorMessageVehiclePage').html('');
    if (!vehicle_number){
    $('#errorMessageVehiclePage').html('<span class="text-danger">Please enter vehicle number!</span>');
    return false;
    }

    $('.manage_vehicle_save_buttton').prop('disabled', true);
    $('#search_vehicle_form').submit();
    /*
     $.ajaxSetup({
     headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
     }
     });
     $('#errorMessageVehiclePage').html('');
     $.ajax({
     type: 'POST',
     url: "{{ route('admin.vehicles.getVehicleLicenceDetails',['panel' => Session::get('panel')]) }}",
     data: {
     'vehicle_number': vehicle_number
     },
     dataType: 'json',
     success: function(response) {
     $('.manage_vehicle_save_buttton').prop('disabled', false);
     $('#search_vehicles').html('Search');
     if (response.status == "error"){
     $('#errorMessageVehiclePage').html('<span class="text-danger">' + response.message);
     return false
     }
     $('#show_form_box').html(response.data);
     console.log(response);
     }, error:function(xhr, status, error) {
     console.log(error);
     $('.manage_vehicle_save_buttton').prop('disabled', false);
     $('#search_vehicles').html('Search');
     $('#errorMessageVehiclePage').html('<span class="text-danger">' + error);
     }
     
     });*/
    });

</script>
@endpush
