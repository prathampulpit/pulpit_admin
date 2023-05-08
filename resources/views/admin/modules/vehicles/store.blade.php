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

                <!-- Page Header -->
                <div class="page-header">
                    <div>
                        <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('vehicleBrandModels.vehicleBrandModels') }}
                        </h2>
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
                <div class="row row-sm justify-content-md-center">
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
                                <form method="post"
                                    action="{{ route('admin.vehicleBrandModels.store', ['panel' => Session::get('panel')]) }}"
                                    class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                    @csrf
                                    @if ($id)
                                        <input type="hidden" name="user_id" id="user_id" value="{{ $data->user_id }}">
                                        <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                    @endif
                                    @if($vehicle_id)
                                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicle_id }}">
                                    @endif
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">Vehicle Number</p>
                                            <input class="form-control" type="text" name="vehicle_number" id=""
                                                value="@if (old('vehicle_number')) {{ old('vehicle_number') }}@elseif($id){{ $data->vehicle_number }} @endif">
                                        </div>
                                        <div class="col-sm">
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
                                        <div class="col-sm">
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

                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">

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
                                        <div class="col-sm">
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
                                                        @if (isset($data->vehicle_fuel_type_name) && $val->name == $data->vehicle_fuel_type_name) selected @endif>
                                                        {{ $val->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @php
                                            // dd($data);
                                        @endphp
                                        <div class="col-sm">
                                            <p class="mg-b-10">Insurance Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                value="@if (old('insurance_exp_date')) {{ old('insurance_exp_date') }}@elseif($id){{ $data->insurance_exp_date }} @endif"
                                                name="insurance_exp_date" placeholder="MM-DD-YYYY" required=""
                                                type="text">
                                        </div>

                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">Permit Exp Date

                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                value="@if (old('permit_exp_date')) {{ old('permit_exp_date') }}@elseif($id){{ $data->permit_exp_date }} @endif"
                                                name="permit_exp_date" placeholder="MM-DD-YYYY" required=""
                                                type="text">
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">Fitness Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                value="@if (old('fitness_exp_date')) {{ old('fitness_exp_date') }}@elseif($id){{ $data->fitness_exp_date }} @endif"
                                                name="fitness_exp_date" placeholder="MM-DD-YYYY" required=""
                                                type="text">
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">PUC Exp Date
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control fc-datepicker hasDatepicker"
                                                value="@if (old('puc_exp_date')) {{ old('puc_exp_date') }}@elseif($id){{ $data->puc_exp_date }} @endif"
                                                name="puc_exp_date" placeholder="MM-DD-YYYY" required=""
                                                type="text">
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">Registration Year
                                                <span class="tx-danger">*</span>
                                            </p>
                                            <input class="form-control datepicker-year hasDatepicker"
                                                value="@if (old('registration_year')) {{ old('registration_year') }}@elseif($id){{ $data->registration_year }} @endif"
                                                name="registration_year" placeholder="YYYY" required=""
                                                type="text">
                                        </div>
                                        <div class="col-sm"></div>
                                        <div class="col-sm"></div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">RC Front: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="rc_front_url" id="rc_front_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->rc_front_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">RC Back: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="rc_back_url" id="rc_back_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->rc_back_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">Insurance Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="insurance_doc_url" id="insurance_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->insurance_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">Permit Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="permit_doc_url" id="permit_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->permit_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">Fitness Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="fitness_doc_url" id="fitness_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->fitness_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                        <div class="col-sm">
                                            <p class="mg-b-10">PUC Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="puc_doc_url" id="puc_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->puc_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                            <p class="mg-b-10">Agreement Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="agreement_doc_url" id="agreement_doc_url" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->agreement_doc_url}}" @endif/>
                                            </div>
                                        </div>
                                        <!-- <div class="col-sm">
                                            <p class="mg-b-10">Agreement Document: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="driving_license_back" id="driving_license_back" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_back_url}}" @endif/>
                                            </div>
                                        </div> -->
                                        <!-- <div class="col-sm">
                                            <label class="main-content-label tx-13 mg-b-20">Vehicle Images</label>
                                            <p class="mg-b-10">Vehicle Images: <span class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input type="file" name="police_verification" id="police_verification" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                            </div>
                                        </div> -->
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm">
                                        <label class="main-content-label tx-13 mg-b-20">Vehicle Images</label>
                                        </div>
                                    </div>
                                    <br>
                                    @if($vehicle->isEmpty())
                                         <div class="row">
                                            <div class="col-sm">
                                                <p class="mg-b-10">Front: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Front" id="Front" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_front_url}}" @endif/>
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <p class="mg-b-10">Back: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Back" id="Back" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_back_url}}" @endif/>
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <p class="mg-b-10">Desktop: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Desktop" id="Desktop" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                                </div>
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-sm">
                                                <p class="mg-b-10">Left: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Left" id="Left" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_front_url}}" @endif/>
                                                </div>
                                            </div>
                                             <div class="col-sm">
                                                <p class="mg-b-10">Right: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Right" id="Right" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->dl_back_url}}" @endif/>
                                                </div>
                                            </div>
                                            <div class="col-sm">
                                                <p class="mg-b-10">Interior: <span class="tx-danger">*</span></p>
                                                <div class="mg-b-10" id="fnWrapper">
                                                    <input type="file" name="Interior" id="Interior" class="dropify" data-height="200" @if(!$id)  @else data-default-file="{{$data->police_verification_url}}" @endif/>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="row">
                                        @foreach($vehicle as $val)
                                            @php
                                                if($data){
                                                    $image = DB::table('vehicle_photo_mapping')->where([['vehicle_id',$data->id],['vehicle_photos_view_master_id',$val->id]])->first();
                                                }else{
                                                    $image = [];
                                                }
                                            @endphp
                                            @if($image)
                                                <div class="col-sm">
                                                    <p class="mg-b-10">{{ $val->view_name }}: <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="{{ $val->view_name }}" id="{{ $val->view_name }}" class="dropify" data-height="200"@if(!$id)  @else data-default-file="{{$image->image_url}}" @endif/>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="col-sm">
                                                    <p class="mg-b-10">{{ $val->view_name }}: <span class="tx-danger">*</span></p>
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input type="file" name="{{ $val->view_name }}" id="{{ $val->view_name }}" class="dropify" data-height="200" @if(!$id)   @endif/>
                                                    </div>
                                                </div>
                                            @endif
                                            
                                        @endforeach
                                        </div>
                                       
                                    @endif
                                    <style>
                                        .ui-datepicker-calendar {
                                            display: none;
                                        }
                                    </style>
                                    {{-- <input class="date-own form-control" style="width: 300px;" type="text"> --}}
                                    <div class="mg-t-30">
                                        <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block"
                                            value="{{ @trans('user.save') }}">
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
        });
    </script>
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
@endpush
