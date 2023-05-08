@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Ad Offer
@else
Add Ad Offer
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
                    <h2 class="main-content-title tx-24 mg-b-5">Ad {{ @trans('offers.offers') }}</h2>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm justify-content-md-center">
                <div class="col-xl-6 col-lg-12 col-md-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div>
                                @if($id)
                                <h6 class="main-content-label mb-1">{{ @trans('offers.edit_offers') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('offers.add_offers') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.offers.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
                                @csrf
                                
                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('offers.logo') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input type="file" name="logo" id="logo" class="dropify" data-height="200" @if(!$id) required="" @else data-default-file="{{$data->offer_url}}" @endif/>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">                                    
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Offer Type: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input name="offer_type" id="radio1" type="radio" value="0" @if($id) @if($data->offer_type == '0') checked @endif @endif>
                                            <label for="radio1">Home</label>
                                            <input name="offer_type" id="radio2" type="radio" value="1" @if($id) @if($data->offer_type == '1') checked @endif @endif>
                                            <label for="radio2">Trip</label>
                                            <input name="offer_type" id="radio3" type="radio" value="2" @if($id) @if($data->offer_type == '2') checked @endif @endif>
                                            <label for="radio3">Cab</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Offer For: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <select class="form-control" id="offer_for" name="offer_for">
                                                <option value="" selected>Select Offer For</option>
                                                <option value="0" @if($id)@if($data->offer_for == 0) selected @endif @endif>Customer</option>
                                                <option value="1" @if($id)@if($data->offer_for == 1) selected @endif @endif>Partner</option>
                                            </select>
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
@endpush