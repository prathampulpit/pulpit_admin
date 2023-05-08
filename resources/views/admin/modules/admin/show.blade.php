@extends('admin.layouts.main')

@section('title')
Trip Detail
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Trip Detail</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.trip.index',['panel' => Session::get('panel')]) }}">Trip</a></li>
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
                                    <div class="p-4">
                                        <h4 class="main-content-label tx-13 mg-b-20">Admin Details</h4>
                                        <hr>

                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-navicon-round"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('admin.fist_name') }}</span>
                                                                    <div> {{$user->first_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-navicon-round"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('admin.last_name') }}</span>
                                                                    <div> {{$user->last_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-navicon-round"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('admin.email') }}</span>
                                                                    <div> {{$user->email}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-navicon-round"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('admin.mobile_number') }}</span>
                                                                    <div> {{$user->mobile_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-icon bg-primary-transparent text-primary"> <i class="icon ion-navicon-round"></i> </div>
                                                                <div class="media-body"> <span>{{ @trans('admin.role') }}s</span>
                                                                    <div>
                                                                        @foreach($roles as $v)
                                                                        {{ $v['name'] }} <br> 
                                                                        @endforeach
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
            </div>
            <!-- End Row -->
        </div>
    </div>
</div>
<!-- End Main Content-->
@endsection

<!-- Change Status -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Edit User Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" id="frmAddEditUser" action="{{ route('admin.users.changesStatus',['panel' => Session::get('panel')]) }}">
                @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}" placeholder="Enter Name" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.email') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control" value="{{$user->email}}" placeholder="Enter Email" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.phone') }}<span class="required">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{$user->mobile_number}}" placeholder="Enter Mobile Number" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.dob') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control dob" value="{{$user->dob}}" placeholder="Enter Date Of Birth" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">User Status</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio3" name="user_status" value="3" @if($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio" name="user_status" value="1" @if($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1" name="user_status" value="0" @if($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2" name="user_status" value="2" @if($user->user_status == '2') checked @endif>
                            <label class="custom-control-label" for="customRadio2">Rejected</label>
                        </div>
                    </div>
                    
                    <hr/>
                    
                    <!-- Modal footer -->
                    <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                        <button type="submit" class="btn btn-bordered-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>        
    </div>
</div>

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
@if(Session::has('message'))
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

    var id = $('#user_id').val();
    var siteurl = "{{URL::to('/')}}/api/v1/createVcnForWeb";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            var message = response.message;
            var errorcode = response.errorcode;
            var success = response.success;
            var dataobject = response.data;
            if(errorcode == 1 && success == true){
                var masked_card = dataobject.masked_card;
                $('#card_number').text(masked_card);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: message,
                    actionText: 'Okay'
                });
            }else{
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

    var id = $('#user_id').val();
    var siteurl = "{{url(Session::get('panel').'/users/resetAttempt')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            if(response == 'success'){
                $('#reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
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

    var id = $('#user_id').val();
    var siteurl = "{{url(Session::get('panel').'/users/resetOtpAttempt')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            if(response == 'success'){
                $('#otp_reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
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
    var id = $('#user_id').val();
    var ussd_enable = $('#ussd_enable').val();
    var siteurl = "{{url(Session::get('panel').'/users/changeUssdStatus')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id,
            "ussd_enable":ussd_enable
        },
        success: function(response) {
            if(response == 'success'){

                if(ussd_enable == 1){
                    $('#ussd_enable_lable').text('On');
                    $('#ussd_enable').val('0');
                    $('.ussd-enable').text('Disable');        
                }else{
                    $('#ussd_enable_lable').text('Off');
                    $('#ussd_enable').val('1'); 
                    $('.ussd-enable').text('Enable');  
                }

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'USSD status change successfully.',
                    actionText: 'Okay'
                });
            }else{
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