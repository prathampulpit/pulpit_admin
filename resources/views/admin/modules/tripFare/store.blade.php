@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Trip Fare
@else
Add Trip Fare
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
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('tripFare.tripFare') }}</h2>
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
                <div class="col-xl-8 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('tripFare.edit_tripFare') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('tripFare.add_tripFare') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.tripFare.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                
                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">Polygon</p>
                                        <select class="form-control" name="polygon_record_id" id="polygon_record_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($polygon_records as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->polygon_record_id) selected @endif @endif>{{ $val->area_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.city') }}</p>
                                        <select class="form-control" name="city_id" id="city_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($city as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->city_id) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.vehicle') }}</p>
                                        <select class="form-control" name="vehicle_type_id" id="vehicle_type_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($vehicleTypes as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->vehicle_type_id) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.base_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('base_fare')){{old('base_fare')}}@elseif($id){{$data->base_fare}}@endif" name="base_fare" placeholder="Enter {{ @trans('tripFare.base_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.minimum_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('minimum_fare')){{old('minimum_fare')}}@elseif($id){{$data->minimum_fare}}@endif" name="minimum_fare" placeholder="Enter {{ @trans('tripFare.minimum_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.base_distance') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('base_distance')){{old('base_distance')}}@elseif($id){{$data->base_distance}}@endif" name="base_distance" placeholder="Enter {{ @trans('tripFare.base_distance') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.base_distance_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('base_distance_fare')){{old('base_distance_fare')}}@elseif($id){{$data->base_distance_fare}}@endif" name="base_distance_fare" placeholder="Enter {{ @trans('tripFare.base_distance_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.base_time') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('base_time')){{old('base_time')}}@elseif($id){{$data->base_time}}@endif" name="base_time" placeholder="Enter {{ @trans('tripFare.base_time') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.base_time_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('base_time_fare')){{old('base_time_fare')}}@elseif($id){{$data->base_time_fare}}@endif" name="base_time_fare" placeholder="Enter {{ @trans('tripFare.base_time_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_one_distance') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_one_distance')){{old('break_one_distance')}}@elseif($id){{$data->break_one_distance}}@endif" name="break_one_distance" placeholder="Enter {{ @trans('tripFare.break_one_distance') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_one_distance_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_one_distance_fare')){{old('break_one_distance_fare')}}@elseif($id){{$data->break_one_distance_fare}}@endif" name="break_one_distance_fare" placeholder="Enter {{ @trans('tripFare.break_one_distance_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_one_time') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_one_time')){{old('break_one_time')}}@elseif($id){{$data->break_one_time}}@endif" name="break_one_time" placeholder="Enter {{ @trans('tripFare.break_one_time') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_one_time_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_one_time_fare')){{old('break_one_time_fare')}}@elseif($id){{$data->break_one_time_fare}}@endif" name="break_one_time_fare" placeholder="Enter {{ @trans('tripFare.break_one_time_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_two_distance') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_two_distance')){{old('break_two_distance')}}@elseif($id){{$data->break_two_distance}}@endif" name="break_two_distance" placeholder="Enter {{ @trans('tripFare.break_two_distance') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_two_distance_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_two_distance_fare')){{old('break_two_distance_fare')}}@elseif($id){{$data->break_two_distance_fare}}@endif" name="break_two_distance_fare" placeholder="Enter {{ @trans('tripFare.break_two_distance_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_two_time') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_two_time')){{old('break_two_time')}}@elseif($id){{$data->break_two_time}}@endif" name="break_two_time" placeholder="Enter {{ @trans('tripFare.break_two_time') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.break_two_time_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('break_two_time_fare')){{old('break_two_time_fare')}}@elseif($id){{$data->break_two_time_fare}}@endif" name="break_two_time_fare" placeholder="Enter {{ @trans('tripFare.break_two_time_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.waiting_time') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('waiting_time')){{old('waiting_time')}}@elseif($id){{$data->waiting_time}}@endif" name="waiting_time" placeholder="Enter {{ @trans('tripFare.waiting_time') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.waiting_time_fare') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('waiting_time_fare')){{old('waiting_time_fare')}}@elseif($id){{$data->waiting_time_fare}}@endif" name="waiting_time_fare" placeholder="Enter {{ @trans('tripFare.waiting_time_fare') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.price_surge') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('price_surge')){{old('price_surge')}}@elseif($id){{$data->price_surge}}@endif" name="price_surge" placeholder="Enter {{ @trans('tripFare.price_surge') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                               
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('tripFare.gst') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('gst')){{old('gst')}}@elseif($id){{$data->gst}}@endif" name="gst" placeholder="Enter {{ @trans('tripFare.gst') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g,'')">
                                        </div>
                                    </div>
                                </div>

                                <div class="mg-t-30">
                                    <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
                                    <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
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
<script>
@if(Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif

$(document).ready(function() {
    /* $('#city_id').select2({
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("api.cities.search") }}',
            dataType: 'json',
        },
    }); */

    var myForm = $('#selectForm2');
    //$.data(myForm[0], 'validator').settings.ignore = "null";

    tinymce.init({
        selector: 'textarea.editor',
        height: 300,
        menubar: false,
        branding: false,
        browser_spellcheck: true,
        toolbar: 'undo redo | bold italic underline strikethrough | link | alignleft aligncenter alignright alignjustify | bullist numlist  | removeformat | preview',
        plugins: [
            'link preview wordcount lists'
        ],
        setup: function(editor) {
            editor.on('keyUp', function() {
                tinyMCE.triggerSave();

                //if (!$.isEmptyObject(myForm.validate().submitted))
                    //myForm.validate().form();
            });
        },

    });
});

function editProfile(){
    $('#editProfile').modal();
}

function submitEditProfile(){
    $("#selectForm2").submit();
}
</script>

<!-- Internal Form-validation js-->
<script src="{{asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
<script src="{{asset('assets/js/form-validation.js') }}"></script>


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
<script src="{{asset('assets/js/form-editor.js') }}"></script>

<script>
    $(document).ready(function() {
        $('#city_id').select2({
            placeholder: 'Select City'
        });

        $('#vehicle_type_id').select2({
            placeholder: 'Select Vehicle Type'
        });
    });
</script>
@endpush