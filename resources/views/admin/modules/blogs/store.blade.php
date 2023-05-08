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
                    <h2 class="main-content-title tx-24 mg-b-5">Blogs</h2>

                </div>

            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm justify-content-md-center">
                <div class="col-xl-8 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">Blogs - {{ @trans('tripFare.website') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">Blogs - {{ @trans('tripFare.website') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.blogs.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf

                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">

                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Title: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('title')){{old('title')}}@elseif($id){{$data->title}}@endif" name="title" placeholder="Enter Title" required="" type="text" value="@if($id){{$data->title}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">

                                        <p class="mg-b-10">Meta Keywords: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('meta_keywords')){{old('meta_keywords')}}@elseif($id){{$data->meta_keywords}}@endif" name="meta_keywords" placeholder="Enter Keywords" required="" type="text" value="@if($id){{$data->meta_keywords}}@endif">
                                        </div>
                                    </div>

                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Meta Authors: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('meta_author')){{old('meta_author')}}@elseif($id){{$data->meta_author}}@endif" name="meta_author" placeholder="Enter Meta Authors" required="" type="text" value="@if($id){{$data->meta_author}}@endif">
                                        </div>
                                    </div> 
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Meta Description: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('meta_description')){{old('meta_description')}}@elseif($id){{$data->meta_description}}@endif" name="meta_description" placeholder="Enter Description" required="" type="text" value="@if($id){{$data->meta_description}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Short Description: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('short_description')){{old('short_description')}}@elseif($id){{$data->short_description}}@endif" name="short_description" placeholder="Enter Short Description" required="" type="text" value="@if($id){{$data->short_description}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">Image Upload: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" name="image_upload" placeholder="Browse Image" type="file"  onchange="document.getElementById('image_show').src = window.URL.createObjectURL(this.files[0])">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <p class="mg-b-10">Image: <span class="tx-danger">*</span></p> 
                                        <img src="@if($id){{ asset($data->images) }} @else {{ asset('pulPitLogo.png') }} @endif" style="height: 50px;" id="image_show"/> 
                                        <!--<img src="{{ asset('pulPitLogo.png') }}" style="height: 50px;" id="image_show"/>--> 
                                    </div>

                                </div> 
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <div class="mg-b-10" id="fnWrapper">
\                                            <textarea class="form-control" name="description" placeholder="Enter Description" cols="100" id="summernote">@if(old('description')){{old('description')}}@elseif($id){{$data->description}}@endif</textarea>
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
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script>

 $('#summernote').summernote({
    placeholder: 'Hello Bootstrap 4',
    tabsize: 2,
    height: '100%'
});

@if (Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif

        $(document).ready(function () {
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
    html += '<input class="form-control" name="from_km_range[]" required="" type="number" placeholder="From KM Range">';
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