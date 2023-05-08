@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Delete User
@else
Add Delete User
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
                    <h2 class="main-content-title tx-24 mg-b-5">Delete User</h2>
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
                                <h6 class="main-content-label mb-1">Delete User</h6>
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post"
                                action="{{ route('admin.deleteuser.destroy',['panel' => Session::get('panel')]) }}"
                                class="parsley-style-1" id="selectForm2" name="selectForm2" 
                                enctype="multipart/form-data">
                                @csrf

                                <!-- <div class="row row-sm mg-b-20">
                                    <div class="col-lg-6">
                                        <p class="mg-b-10">Mobile Number: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="" name="mobile_number" placeholder="Enter Mobile Number" required="" type="text">
                                        </div>
                                    </div>
                                </div> -->

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('notifications.username') }}</p>
                                        <select class="form-control" name="id" id="id" required="">
                                            <option value="" label="Choose one"></option>
                                            @foreach($users as $val)
                                            <option value="{{ $val->id }}">{{ $val->first_name." ".$val->last_name }} -
                                                {{ $val->mobile_number }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

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
<script>
@if(Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif
</script>

<!-- InternalFancy uploader js-->
<script src="{{asset('assets/plugins/fancyuploder/jquery.ui.widget.js') }}"></script>
<script src="{{asset('assets/plugins/fancyuploder/jquery.iframe-transport.js') }}"></script>

<script>
$(document).ready(function() {
    $('#id').select2({
        placeholder: 'Select User'
    });
});
</script>
@endpush