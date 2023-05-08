@extends('admin.layouts.main')

@section('title')
Cabs Details
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Cabs Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.cabs.index',['panel' => Session::get('panel')]) }}">Cabs</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12 col-md-12">
                    <div class="card custom-card main-content-body-profile">
                        <div class="tab-content">
                            <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                                @if(empty($cabs))
                                <div class="card-body p-0 border p-0 rounded-10">
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">cabs</label>
                                                                                    <a href="{{url('/super-admin/cabs')}}" class="btn btn-danger" style="float: right;">Back</a>

                                        <div class="">
                                            <Center><h3>Opps, There is no Cabs found!</h3></Center>
                                        </div>                                
                                    </div>                                
                                </div>                                
                                @else
                                <div class="card-body p-0 border p-0 rounded-10">
                                    <!-- <div class="border-top"></div> -->
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">cabs</label>
                                        <div class="d-sm-flex">

                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.name') }}</span>
                                                                    <div>{{$cabs->first_name.' '.$cabs->last_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Mobile Number</span>
                                                                    <div>{{$cabs->mobile_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.cab_post_type') }}</span>
                                                                    <div>{{$cabs->cab_post_type}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.available_for') }}</span>
                                                                    <div>{{$cabs->available_for}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.start_location') }}</span>
                                                                    <div>{{$cabs->start_location}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.end_location') }}</span>
                                                                    <div>{{$cabs->end_location}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('cabs.address') }}</span>
                                                                    <div>{{$cabs->address}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>



                                            </div>

                                        </div>
                                    </div>

                                    <div class="p-4">
                                        <h4 class="main-content-label tx-13 mg-b-20">Vehicle Details</h4>
                                        <hr>
                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_number') }}</span>
                                                                    <div><a href="{{url(Session::get('panel').'/vehicles/show')}}/{{$cabs->vehicle_id}}" title="View"> {{$cabs->vehicle_number}}</a></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.brand_name') }}</span>
                                                                    <div> {{$cabs->brand_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.model_name') }}</span>
                                                                    <div> {{$cabs->model_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_type_name') }}</span>
                                                                    <div> {{$cabs->vehicle_type_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_fuel_type_name') }}</span>
                                                                    <div> {{$cabs->vehicle_fuel_type_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Registration Year</span>
                                                                    <div> {{$cabs->registration_year}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Owner Name</span>
                                                                    <div> {{$cabs->owner_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                </div>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
</div>
<!-- End Main Content-->
@endsection
<!-- Image View Modal -->
<div class="modal fade" id="attachModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Image View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <img id="image-gallery-image" src="">
            </div>
        </div>
    </div>
</div>
@push('pageJs')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script type="text/javascript">
function getImage(imageName){
$('#image-gallery-image').attr('src', imageName);
}
@if (Session::has('message'))
        Snackbar.show({
        pos: 'bottom-right',
                text: "{!! session('message') !!}",
                actionText: 'Okay'
        });
        @endif

        $('#load2').on('click', function() {
var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
        $this.button('reset');
        }, 10000);
        var id = $('#cabs_id').val();
        var siteurl = "{{URL::to('/')}}/api/v1/createVcnForWeb";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "cabs_id": id
                },
                success: function(response) {
                var message = response.message;
                        var errorcode = response.errorcode;
                        var success = response.success;
                        var dataobject = response.data;
                        if (errorcode == 1 && success == true){
                var masked_card = dataobject.masked_card;
                        $('#card_number').text(masked_card);
                        Snackbar.show({
                        pos: 'bottom-right',
                                text: message,
                                actionText: 'Okay'
                        });
                } else{
                Snackbar.show({
                pos: 'bottom-right',
                        actionTextColor: '#fff',
                        textColor: '#fff',
                        text: message,
                        backgroundColor: '#cc0000',
                        actionText: 'Okay'
                });
                }
                $this.button('reset');
                }
        });
});
        $('#reset_btn').on('click', function() {
var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
        $this.button('reset');
        }, 10000);
        var id = $('#cabs_id').val();
        var siteurl = "{{url(Session::get('panel').'/cabss/resetAttempt')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "cabs_id": id
                },
                success: function(response) {
                if (response == 'success'){
                $('#reset_number').text(0);
                        Snackbar.show({
                        pos: 'bottom-right',
                                text: 'Attemp reset successfully!',
                                actionText: 'Okay'
                        });
                } else{
                Snackbar.show({
                pos: 'bottom-right',
                        actionTextColor: '#fff',
                        textColor: '#fff',
                        text: 'Somthing went wrong. Please try again!',
                        backgroundColor: '#cc0000',
                        actionText: 'Okay'
                });
                }
                $this.button('reset');
                }
        });
});
        $('#otp_reset_btn').on('click', function() {
var $this = $(this);
        $this.button('loading');
        setTimeout(function() {
        $this.button('reset');
        }, 10000);
        var id = $('#cabs_id').val();
        var siteurl = "{{url(Session::get('panel').'/cabss/resetOtpAttempt')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "cabs_id": id
                },
                success: function(response) {
                if (response == 'success'){
                $('#otp_reset_number').text(0);
                        Snackbar.show({
                        pos: 'bottom-right',
                                text: 'Attemp reset successfully!',
                                actionText: 'Okay'
                        });
                } else{
                Snackbar.show({
                pos: 'bottom-right',
                        actionTextColor: '#fff',
                        textColor: '#fff',
                        text: 'Somthing went wrong. Please try again!',
                        backgroundColor: '#cc0000',
                        actionText: 'Okay'
                });
                }
                $this.button('reset');
                }
        });
});
        $('#ussd_status_btn').on('click', function() {
var id = $('#cabs_id').val();
        var ussd_enable = $('#ussd_enable').val();
        var siteurl = "{{url(Session::get('panel').'/cabss/changeUssdStatus')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "cabs_id": id,
                        "ussd_enable":ussd_enable
                },
                success: function(response) {
                if (response == 'success'){

                if (ussd_enable == 1){
                $('#ussd_enable_lable').text('On');
                        $('#ussd_enable').val('0');
                        $('.ussd-enable').text('Disable');
                } else{
                $('#ussd_enable_lable').text('Off');
                        $('#ussd_enable').val('1');
                        $('.ussd-enable').text('Enable');
                }

                Snackbar.show({
                pos: 'bottom-right',
                        text: 'USSD status change successfully.',
                        actionText: 'Okay'
                });
                } else{
                Snackbar.show({
                pos: 'bottom-right',
                        actionTextColor: '#fff',
                        textColor: '#fff',
                        text: 'Somthing went wrong. Please try again!',
                        backgroundColor: '#cc0000',
                        actionText: 'Okay'
                });
                }
                }
        });
});
</script>
@endpush