@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Subscription Plans
@else
Add Subscription Plans
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
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('subscriptionPlans.subscriptionPlans') }}</h2>
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
                                <h6 class="main-content-label mb-1">{{ @trans('subscriptionPlans.edit_subscriptionPlans') }}</h6>
                                @else
                                <h6 class="main-content-label mb-1">{{ @trans('subscriptionPlans.add_subscriptionPlans') }}</h6>
                                @endif
                                <p class="text-muted card-sub-title">&nbsp;</p>
                            </div>
                            <form method="post" action="{{ route('admin.subscriptionPlans.store',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2">
                                @csrf
                                
                                @if($id)
                                <input type="hidden" name="id" id="id" value="{{$data->id}}">
                                @endif

                                <div class="row row-sm mg-b-20">                                    
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Type: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input name="is_agent" id="radio1" type="radio" value="1" @if($id) @if($data->is_agent == '1') checked @endif @endif>
                                            <label for="radio1">Agent</label>
                                            <input name="is_agent" id="radio2" type="radio" value="0" @if($id) @if($data->is_agent == '0') checked @endif @endif>
                                            <label for="radio2">Vehicle</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20 vehicle">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('subscriptionPlans.vehicleType') }}: <span class="tx-danger">*</span></p>
                                        <select class="form-control" name="vehicle_type_id" id="vehicle_type_id">
                                            <option value="" label="Choose one"></option>
                                            @foreach($vehicleTypes as $val)
                                            <option value="{{ $val->id }}" @if($id) @if($val->id == $data->vehicle_type_id) selected @endif @endif>{{ $val->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('subscriptionPlans.name') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('name')){{old('name')}}@elseif($id){{$data->name}}@endif" name="name" placeholder="Enter Name" required="" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('subscriptionPlans.plan_validity') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('plan_validity')){{old('plan_validity')}}@elseif($id){{$data->plan_validity}}@endif" name="plan_validity" placeholder="Enter Plan Validity" required="" type="text">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">{{ @trans('subscriptionPlans.price') }}: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="@if(old('price')){{old('price')}}@elseif($id){{$data->price}}@endif" name="price" placeholder="Enter Price" required="" type="number" step="0.01">
                                        </div>
                                    </div>
                                </div>

                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Benifits: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <!-- <input class="form-control" value="@if(old('price')){{old('price')}}@elseif($id){{$data->price}}@endif" name="price" placeholder="Enter Price" required="" type="number" step="0.01"> -->
                                            @foreach($planPremiumBenifitsMaster as $val)
                                            <div class="col-lg-6 mg-t-20 mg-lg-t-0">
                                                <label class="ckbox"><input type="checkbox" name="benifit_id[]" @if(in_array($val['id'], $map_ids)) checked @endif value="{{$val['id']}}"><span>{{$val['name']}}</span></label><br>
                                            </div>
                                            @endforeach
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

    $("input[name=is_agent]:radio").click(function () {
        if ($('input[name=is_agent]:checked').val() == "1") {
            $('.vehicle').css('display','none');
        }else{
            $('.vehicle').css('display','');
        }
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
@endpush