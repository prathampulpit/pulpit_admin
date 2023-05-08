@extends('admin.layouts.main')

@section('title')
User Detail
@endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec detail-page">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('dashboard.dashboard') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.dashboard.index',['panel' => Session::get('panel')]) }}">{{ @trans('user.list') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('user.detail') }}</li>
                </ol>
            </nav>

            <div class="profile-detail">
                <div class="person-details">
                    <span class="can-img rounded-pic">
                        <img src="{{ App\Models\User::getImageUsingStsToken('user/'.$user->profile_picture) }}">
                    </span>
                    <div class="about">
                        <h2 class="cndidate-name">{{ $user->name }}</h2>
                    </div>
                </div>
            </div>

            <div class="detail-content mt-4">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#detail">{{ @trans('user.details') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#doc">{{ @trans('user.documents') }}</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#cnct">Contacts</a>
                    </li> -->
                </ul>

                <!-- Tab panes -->
                <div class="tab-content box">
                    <div id="detail" class="tab-pane fade show active">

                        <div class="row">
                            <div class="col-lg-12">
                                @if($user_role == 'administrator' || $user_role == 'backoffice')
                                <div class="btn-group-em float-right mt-3" role="group">
                                    <a href="{{url(Session::get('panel').'/users/edit')}}/{{$user->id}}" class="btn btn-bordered btn-sm">{{ @trans('user.edit') }}</a>
                                </div>
                                @if(empty($card_number) && $user->user_status != 2 )
                                <div class="btn-group-em float-right mt-3" role="group">
                                    &nbsp;
                                </div>
                                <div class="btn-group-em float-right mt-3" role="group">
                                    <!-- <a onclick="createVcn('{{$user->id}}')" href="javascript::void(0)" class="btn btn-bordered btn-sm">{{ @trans('user.create_vcn') }}</a> -->
                                    <button type="button" class="btn btn-bordered btn-sm create-vcn" id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> loading">{{ @trans('user.create_vcn') }}</button>
                                </div>
                                @endif
                                @endif
                                <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                                <h3 class="detail-heading">{{ @trans('user.details') }}</h3>

                                <div class="info">
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.name') }}</label>
                                        <p class="display-info">{{$user->name}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.email') }}</label>
                                        <p class="display-info">{{$user->email}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.phone') }}</label>
                                        <p class="display-info">{{$user->mobile_number}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.dob') }}</label>
                                        <p class="display-info">{{$user->dob}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.gender') }}</label>
                                        <p class="display-info">{{$user->gender}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.nationality') }}</label>
                                        @if($user->nationality_id == 2)
                                        <p class="display-info">NON-Tanzanian</p>
                                        @else
                                        <p class="display-info">Tanzanian</p>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.document_number') }}</label>
                                        <p class="display-info">{{$user->document_number}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.document_type') }}</label>
                                        <p class="display-info">{{$user->document_name}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.status') }}</label>
                                        @if($user->user_status == 1)
                                        <span class="em-badge green">Active</span>
                                        @elseif($user->user_status == 2)
                                        <span class="em-badge red">Rejected</span>
                                        @elseif($user->user_status == 3)
                                        <span class="em-badge red">Waiting for approval</span>
                                        @else
                                        <span class="em-badge red">Inactive</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.virtual_card_number') }}</label>
                                        <p class="display-info">{{$card_number}}</p>
                                    </div>

                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="doc" class="tab-pane fade">
                        <ul class="p-0 m-0 doc-thumb">
                            <li>
                                <div class="thumb document-img" href="#attachModal" data-toggle="modal" data-target="#attachModal" onclick="getImage('{{ App\Models\User::getImageUsingStsToken('documents/'.$user->document_file_name) }}')">
                                    <img src="{{ App\Models\User::getImageUsingStsToken('documents/'.$user->document_file_name) }}">
                                    <p><label class="control-label"></label><span class="display-info">{{$user->document_name}}</span></p>
                                </div>
                            </li>

                            @if($user->user_type == '2')
                            <li>
                                <div class="thumb document-img" href="#attachModal" data-toggle="modal" data-target="#attachModal" onclick="getImage('{{ App\Models\User::getImageUsingStsToken('documents/'.$user->resident_permit) }}')">
                                    <img src="{{ App\Models\User::getImageUsingStsToken('documents/'.$user->resident_permit) }}">
                                    <p><label class="control-label"></label><span class="display-info">Resident Permit</span></p>
                                </div>
                            </li>

                            <li>
                                <div class="thumb document-img" href="#attachModal" data-toggle="modal" data-target="#attachModal" onclick="getImage('{{ App\Models\User::getImageUsingStsToken('documents/'.$user->work_permit) }}')">
                                    <img src="{{ App\Models\User::getImageUsingStsToken('documents/'.$user->work_permit) }}">
                                    <p><label class="control-label"></label><span class="display-info">Work Permit</span></p>
                                </div>
                            </li>
                            @endif

                            <li>
                                <div class="thumb document-img" href="#attachModal" data-toggle="modal" data-target="#attachModal" onclick="getImage('{{ App\Models\User::getImageUsingStsToken('user/'.$user->selfie_picture) }}')">
                                    <img src="{{ App\Models\User::getImageUsingStsToken('user/'.$user->selfie_picture) }}">
                                    <p><label class="control-label"></label><span class="display-info">Selfie picture</span></p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
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
                <form method="POST" action="{{ route('admin.users.changesStatus',['panel' => Session::get('panel')]) }}">
                    @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">
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

                    <hr />

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
    function getImage(imageName) {
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
                if (errorcode == 1 && success == true) {
                    var masked_card = dataobject.masked_card;
                    $('#card_number').text(masked_card);

                    Snackbar.show({
                        pos: 'bottom-right',
                        text: message,
                        actionText: 'Okay'
                    });
                } else {
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
</script>
@endpush