@extends('admin.layouts.main')
@section('title')
Change Profile
@endsection

@section('content')

<?php 
$admin = Auth::user();
$role_id = $admin['role_id'];
?>
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{route('admin.changeProfile.save')}}">
                                    @csrf
                                    <div class="form-body">
                                        <input type="hidden" name="id" value="{{Auth::user()->id}}">
                                        <h2 class="form-sec-title">{{ @trans('user.change_profile') }}</h2>
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="profile-img ">
                                                    <div class="profile-img justify-content-start">
                                                        <div class="custom-file rounded-pic" style="height:100px;width:100px">
                                                            <input type="hidden" name="profilepost" id="profilepost" value="" />
                                                            <label class="custom-file-label p-0 rounded-pic" style="height:100px;" for="profile_image">
                                                                @if(Auth::user()->profile_image != "default.jpg")
                                                                <img id="output" class="thumb-user-image" src="{{asset('/img/default.jpg')}}" alt="upload">
                                                                @else
                                                                <img id="output" class="thumb-user-image" src="{{asset('/img/default.jpg')}}" alt="upload">
                                                                @endif
                                                            </label>
                                                            <input type="file" class="custom-file-input user-profile" id="profile_image" name="profile_image" accept=".png,.jpeg,.jpg" style="display: none;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-10">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ @trans('user.first_name') }}
                                                                <span class="required">*</span></label>
                                                            <input type="text" name="first_name" class="form-control"
                                                                value="{{Auth::user()->first_name}}"
                                                                placeholder="Enter First Name"/>
                                                            @if($errors->has('first_name'))
                                                            <div class="invalid-feedback">{{ $errors->first('first_name')}}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ @trans('user.last_name') }}
                                                                <span class="required">*</span></label>
                                                            <input type="text" name="last_name" class="form-control"
                                                                value="{{Auth::user()->last_name}}"
                                                                placeholder="Enter Last Name"/>
                                                            @if($errors->has('last_name'))
                                                            <div class="invalid-feedback">{{ $errors->first('last_name')}}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
        
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ @trans('user.email') }}
                                                                <span class="required">*</span></label>
                                                            <input type="text" name="email" class="form-control"
                                                                class="form-control"
                                                                value="{{Auth::user()->email}}"
                                                                placeholder="Enter Email"/>
                                                            @if($errors->has('email'))
                                                            <div class="invalid-feedback">{{ $errors->first('email')}}
                                                            </div>
                                                            @endif
        
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="control-label">{{ @trans('user.phone') }}
                                                                <span class="required">*</span></label>
                                                            <input type="text" name="mobile" v-model="mobile"
                                                                class="form-control"
                                                                value="{{Auth::user()->mobile_number}}"
                                                                placeholder="Enter Mobile"/>
                                                            @if($errors->has('mobile'))
                                                            <div class="invalid-feedback">{{ $errors->first('mobile')}}
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                    @if($role_id == '1')
                                    <div class="form-action text-right">
                                        <input type="submit" class="btn step-btn" value="{{ @trans('user.save') }}">
                                    </div>
                                    @endif

                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
@push('pageModals')
<div id="uploadimageModal" class="modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="title-wrapper">
                    <h2 class="box-title">Crop Porofile Image</h2>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="row">
                    <div class="col-md-8 text-center">
                        <div id="image" style="width:350px; margin-top:30px"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-bordered w-50" data-dismiss="modal">Cancel</button>
                <button class="btn btn-transparent w-50 crop_image">Crop & Upload</button>
            </div>
        </div>
    </div>
</div>

@endpush


@push('pageJs')
{!! JsValidator::formRequest('App\Http\Requests\User\ChangeProfileRequest', '#frmAddEditUser') !!}
<script>
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif

    $(document).ready(function() {
        $image_crop = $('#image').croppie({
            enableExif: true,
            viewport: {
                width: 200,
                height: 200,
                type: 'circle' //square
            },
            boundary: {
                width: 400,
                height: 400
            }
        });

        $(document).on('change', '#profile_image', function() {
            var reader = new FileReader();
            reader.onload = function(event) {
                $image_crop.croppie('bind', {
                    url: event.target.result
                }).then(function() {
                    console.log('jQuery bind complete');
                });
            }
            reader.readAsDataURL(this.files[0]);
            $('#uploadimageModal').modal('show');
        });

        $(document).on('click', '.crop_image', function(event) {
            $image_crop.croppie('result', {
                type: 'canvas',
                size: 'viewport'
            }).then(function(response) {
                $.ajax({
                    url: "{{route('admin.changeProfile.uploadprofile')}}",
                    type: "POST",
                    data: {
                        "_token": "{{csrf_token()}}",
                        "image": response
                    },
                    success: function(data) {
                        $("#output").attr('src', data.image);
                        $('#uploadimageModal').modal('hide');
                        $("#progressBarVal").attr("value", data.score);
                        $("#progressBarText").html(data.score);
                    }
                });
            })
        });
    });
</script>

@endpush