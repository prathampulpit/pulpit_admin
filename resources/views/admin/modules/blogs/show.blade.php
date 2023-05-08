@extends('admin.layouts.main')

@section('title')
Trip Fare Details
@endsection

@section('content')
<!-- include libraries(jQuery, bootstrap) -->

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Blogs Details</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.blogs.index',['panel' => Session::get('panel')]) }}">Blogs</a></li>
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
                                <div class="card-body p-0 border p-0 rounded-10">
                                    <!-- <div class="border-top"></div> -->
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">{{$data->title}}'s Blogs</label>
                                        <div class="d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-12">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Title</span>
                                                                    <div>{{$data->title}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-12">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Meta Keywords</span>
                                                                    <div>{{$data->meta_keywords}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Meta Author</span>
                                                                    <div>{{$data->meta_author}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Meta Description</span>
                                                                    <div>{{$data->meta_description}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div> 

                                                <div class="col-md-12">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Description</span>
                                                                    <div><?php echo $data->description ;?> </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>
                                </div>
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
        var id = $('#tripFare_id').val();
        var siteurl = "{{URL::to('/')}}/api/v1/createVcnForWeb";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "tripFare_id": id
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
        var id = $('#tripFare_id').val();
        var siteurl = "{{url(Session::get('panel').'/tripFares/resetAttempt')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "tripFare_id": id
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
        var id = $('#tripFare_id').val();
        var siteurl = "{{url(Session::get('panel').'/tripFares/resetOtpAttempt')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "tripFare_id": id
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
var id = $('#tripFare_id').val();
        var ussd_enable = $('#ussd_enable').val();
        var siteurl = "{{url(Session::get('panel').'/tripFares/changeUssdStatus')}}";
        $.ajax({
        url: siteurl,
                type: "POST",
                data: {
                "_token": "{{csrf_token()}}",
                        "tripFare_id": id,
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