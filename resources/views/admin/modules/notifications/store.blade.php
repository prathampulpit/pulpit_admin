@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Notification
@else
Add Notification
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
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('notifications.notifications') }}</h2>
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
                <div class="col-xl-6 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('notifications.edit_notifications') }}
                                </h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('notifications.add_notifications') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post"
                                action="{{ route('admin.notifications.store',['panel' => Session::get('panel')]) }}"
                                class="parsley-style-1" id="selectForm2" name="selectForm2"
                                enctype="multipart/form-data">
                                @csrf

                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.username') }}</p>
                                        <select class="form-control" name="user_id" id="user_id">
                                            <option value="" label="Choose one"></option>
                                            <option value="0">All</option>
                                            @foreach($users as $val)
                                            <option value="{{ $val->id }}">{{ $val->first_name." ".$val->last_name }} - {{ $val->mobile_number }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.notificationType') }}</p>
                                        <select class="form-control" name="notification_type" id="notification_type"
                                            required="">
                                            <option value="" label="Choose one"></option>
                                            <option value="promotional">promotional</option>
                                            <option value="information">Information</option>
                                            <option value="updates">updates</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.typeNotification') }}</p>
                                        <select class="form-control" name="type_notification" id="type_notification"
                                            required="">
                                            <option value="" label="Choose one"></option>
                                            <option value="image_only">Image only</option>
                                            <option value="image_text">Image + text</option>
                                            <option value="sms_only">SMS only</option>
                                            <option value="sms_push">SMS + Push</option>
                                            <option value="email_text_sms">Email + Text + SMS</option>
                                            <option value="sms_email">SMS + Email</option>
                                            <option value="push_only">Push Only</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.targetAudience') }}</p>
                                        <select class="form-control" name="target_audience" id="target_audience"
                                            required="">
                                            <option value="" label="Choose one"></option>
                                            <option value="rider">Rider</option>
                                            <option value="drivers">Drivers</option>
                                            <option value="agent">Agent</option>
                                            <option value="travels">Travels</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.targetAudience') }}</p>
                                        <select class="form-control" name="audience" id="audience"
                                            required="">
                                            <option value="" label="Choose one"></option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="user_with_app">User With App</option>
                                            <option value="user_who_dont_have_app">User Who Don't Have App (SMS)
                                            </option>
                                            <option value="2">Unapproved Users (Yellow)</option>
                                            <option value="All">All</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.title') }}: <span
                                                class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control"
                                                value="@if(old('title')){{old('title')}}@elseif($id){{$data->title}}@endif"
                                                name="title" required="" placeholder="Enter Title" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.description') }}</p>
                                        <div class="mg-b-10">
                                            <textarea name="description" id="description"
                                                class="form-control">@if($id){{ $data->description }}@endif</textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Image</p>
                                        <div class="mg-b-10">
                                            <!-- <input type="file" name="image_name" id="image_name" class="dropify"
                                                data-height="200"> -->
                                                <input type="file" class="custom-file-input dropify" id="image_name" name="image_name"
                                                    accept=".png,.jpeg,.jpg">
                                        </div>
                                    </div>
                                </div>

                                <div class="mg-t-30">
                                    <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
                                    <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="load2"
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
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

function editProfile() {
    $('#editProfile').modal();
}

function submitEditProfile() {
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
    $('#user_id').select2({
        placeholder: 'Select User'
    });
});
</script>
@endpush