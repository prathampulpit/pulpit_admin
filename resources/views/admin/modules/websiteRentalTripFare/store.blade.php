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
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('tripFare.rental') }} {{ @trans('tripFare.tripFare') }}</h2>
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
                                <h6 class="main-content-label mb-1">{{ @trans('tripFare.rental') }} {{ @trans('tripFare.edit_tripFare') }} - {{ @trans('tripFare.website') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('tripFare.rental') }} {{ @trans('tripFare.add_tripFare') }} - {{ @trans('tripFare.website') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.websiteRentalTripFare.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf

                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row  row-sm mg-b-20">
                                    <div class="col-lg-3">
                                        <p class="mg-b-10">{{ @trans('tripFare.vehicle') }}</p>
                                        <select class="form-control" name="vehicle_type_id" id="vehicle_type_id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($vehicleTypes as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->vehicle_type_id) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                     <div class="col-lg-3">
                                        <p class="mg-b-10">{{ @trans('tripFare.gst') }}(%): <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('gst')){{old('gst')}}@elseif($id){{$data->gst}}@endif" name="gst" placeholder="Enter {{ @trans('tripFare.gst') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                        this.value = this.value.replace(/[^0-9\.]/g, '')">
                                        </div>
                                    </div> 
                                    <div class="col-lg-3">
                                        <p class="mg-b-10">{{ @trans('tripFare.advance_booking') }}(%): <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('advance_booking')){{old('advance_booking')}}@elseif($id){{$data->advance_booking}}@endif" name="advance_booking" placeholder="Enter {{ @trans('tripFare.advance_booking') }}" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                        this.value = this.value.replace(/[^0-9\.]/g, '')">
                                        </div>
                                    </div>
                                </div> 

                                <div class="row ">  

                                    <div class="col-md-12">  
                                        <div class="row">  
                                            <div class="col-lg-3">
                                                <p class="mg-b-10">Hours: <span class="tx-danger">*</span></p> 
                                            </div>
                                            <div class="col-lg-3">
                                                <p class="mg-b-10">KM: <span class="tx-danger">*</span></p> 
                                            </div>  
                                            <div class="col-lg-3">
                                                <p class="mg-b-10">Prices: <span class="tx-danger">*</span></p> 
                                            </div>  
                                            <div class="col-lg-3">
                                                <p class="mg-b-10"><a href="javascript:;" id="add_range" class="btn btn-primary btn-sm">+</a></p> 
                                            </div>  
                                        </div>
                                    </div>
                                    <div class="col-md-12">  

                                        <div id="ranges">
                                            <?php $i = 0; ?>
                                            @if(!empty($ranges))
                                            @foreach($ranges as $row)
                                            <div class="row row-sm mg-b-20" id="remove_ranges{{$i}}">  
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" value="@if(old('hours')){{old('hours')}}@elseif($id){{$row->hours}}@endif" name="hours[]" placeholder="Enter Hours" required="" type="text"  onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" value="@if(old('km')){{old('km')}}@elseif($id){{$row->km}}@endif" name="km[]" placeholder="Enter KM" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div> 
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" value="@if(old('prices')){{old('prices')}}@elseif($id){{$row->prices}}@endif" name="prices[]" placeholder="Enter Prices" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div> 
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <a class="btn btn-danger btn-sm remove_ranges" href="javascript:;" data-id="{{$i}}" >Remove</a>
                                                    </div>
                                                </div>  
                                            </div>
                                            <?php $i++; ?>
                                            @endforeach
                                            @else
                                            <div class="row row-sm mg-b-20">  
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" value="0" name="hours[]" placeholder="Enter Hours" required="" type="text"  onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" value="0" name="km[]"  placeholder="Enter KM" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div>  
                                                <div class="col-lg-3">
                                                    <div class="mg-b-10" id="fnWrapper">
                                                        <input class="form-control" name="prices[]"  value="0" placeholder="Enter Prices" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value))
                                                                    this.value = this.value.replace(/[^0-9\.]/g, '')">
                                                    </div>
                                                </div>

                                            </div>
                                            @endif

                                        </div>
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <hr/> 
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('tripFare.description') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <textarea class="form-control" name="description" placeholder="Enter {{ @trans('tripFare.description') }}" cols="100">@if(old('description')){{old('description')}}@elseif($id){{$data->description}}@endif</textarea>
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

    var i = "<?php $i++; ?>";
    $('body').on('click', '#add_range', function () {
        var html = '<div class="row row-sm mg-b-20" id="remove_ranges' + i + '">';
        html += '<div class="col-lg-3">';
        html += '<div class="mg-b-10" id="fnWrapper">';
        html += '<input class="form-control" value="0" name="hours[]" placeholder="Enter Hours" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g, "")">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-lg-3">';
        html += ' <div class="mg-b-10" id="fnWrapper">';
        html += ' <input class="form-control" value="0" name="km[]" placeholder="Enter KM" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g, "")">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-lg-3">';
        html += ' <div class="mg-b-10" id="fnWrapper">';
        html += ' <input class="form-control" value="0" name="prices[]" placeholder="Enter Prices" required="" type="text" onkeyup="if (/[^0-9\.]/g.test(this.value)) this.value = this.value.replace(/[^0-9\.]/g, "")">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-lg-3">';
        html += ' <div class="mg-b-10" id="fnWrapper">';
        html += ' <a class="btn btn-danger btn-sm remove_ranges" href="javascript:;" data-id="' + i + '" >Remove</a>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        $('#ranges').prepend(html);
        i++;
    });

    $('body').on('click', '.remove_ranges', function () {
        var id = $(this).attr('data-id');
        $('#remove_ranges' + id).remove();
    });


    @if (Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif

            $(document).ready(function () {


    var myForm = $('#selectForm2');


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
            setup: function (editor) {
            editor.on('keyUp', function () {
            tinyMCE.triggerSave();
                    //if (!$.isEmptyObject(myForm.validate().submitted))
                    //myForm.validate().form();
            });
            },
    });
    }
    );

    function editProfile() {
        $('#editProfile').modal();
    }

    function submitEditProfile() {
        $("#selectForm2").submit();
    }

    $('body').on('click', '#add_more', function () {


        html = '<div class="row row-sm mg-b-20">';
        html += '<div class="col-lg-3">';
        html += '<div class="mg-b-10" id="fnWrapper">';
        html += '<input class="form-control" name="hours" required="" type="number" placeholder="From KM Range">';
        html += '</div>';
        html += '</div>';
        html += '<div class="col-lg-3">';
        html += '<div class="mg-b-10" id="fnWrapper">';
        html += '<input class="form-control" name="to_km_range[]"  required="" type="number" placeholder="To KM Range">';
        html += '</div>';
        html += '</div> ';
        html += '</div> ';
        $('#ranges').prepend(html);
    });
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
    $(document).ready(function () {
        $('#city_id').select2({
            placeholder: 'Select City'
        });

        $('#vehicle_type_id').select2({
            placeholder: 'Select Vehicle Type'
        });
    });
</script>
@endpush