@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Faqs
@else
Add Faqs
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
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('faqs.faqs') }}</h2>
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
                <div class="col-xl-10 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('faqs.edit_faqs') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('faqs.add_faqs') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.faqs.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                
                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('faqs.question') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('question')){{old('question')}}@elseif($id){{$data->question}}@endif" name="question" placeholder="Enter Question" required="" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('faqs.answer') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10">
                                            <textarea name="answer" id="answer" class="form-control editor" required>@if($id){{ $data->answer }}@endif</textarea>
                                        </div>
                                    </div>
                                </div>

                                <b class="tx-15 mg-b-5">Youtube Links</b>
                                <hr>
                                <!-- Video Links -->
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Youtube:</p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if($id && isset($faq_media[0]['media_url'])){{$faq_media[0]['media_url']}}@endif" name="youtube[]" placeholder="Enter Youtube Link" type="url">
                                            <input type="hidden" name="youtube_id[]" value="@if($id && isset($faq_media[0]['id'])){{$faq_media[0]['id']}}@endif">
                                        </div>
                                    </div>
                                </div>
                                <b class="tx-15 mg-b-5">Images</b>
                                <hr>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('faqs.image1') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="image1" id="logo" class="dropify" data-height="200" @if($id && isset($faq_media_img[0]['media_url'])) data-default-file="{{$faq_media_img[0]['media_url']}}" @endif/>
                                            <input type="hidden" name="youtube_id_img[]" value="@if($id && isset($faq_media_img[0]['id'])){{$faq_media_img[0]['id']}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('faqs.image2') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="image2" id="logo" class="dropify" data-height="200" @if($id && isset($faq_media_img[1]['media_url'])) data-default-file="{{$faq_media_img[1]['media_url']}}" @endif/>
                                            <input type="hidden" name="youtube_id_img[]" value="@if($id && isset($faq_media_img[1]['id'])){{$faq_media_img[1]['id']}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <p class="mg-b-10">{{ @trans('faqs.image3') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="image3" id="logo" class="dropify" data-height="200" @if($id && isset($faq_media_img[2]['media_url'])) data-default-file="{{$faq_media_img[2]['media_url']}}" @endif/>
                                            <input type="hidden" name="youtube_id_img[]" value="@if($id && isset($faq_media_img[2]['id'])){{$faq_media_img[2]['id']}}@endif">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('faqs.image4') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="image4" id="logo" class="dropify" data-height="200" @if($id && isset($faq_media_img[3]['media_url'])) data-default-file="{{$faq_media_img[3]['media_url']}}" @endif/>
                                            <input type="hidden" name="youtube_id_img[]" value="@if($id && isset($faq_media_img[3]['id'])){{$faq_media_img[3]['id']}}@endif">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">{{ @trans('faqs.image5') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="image5" id="logo" class="dropify" data-height="200" @if($id && isset($faq_media_img[4]['media_url'])) data-default-file="{{$faq_media_img[4]['media_url']}}" @endif/>
                                            <input type="hidden" name="youtube_id_img[]" value="@if($id && isset($faq_media_img[4]['id'])){{$faq_media_img[4]['id']}}@endif">
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
    $('#city_id').select2({
        minimumInputLength: 2,
        ajax: {
            url: '{{ route("api.cities.search") }}',
            dataType: 'json',
        },
    });

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
@endpush