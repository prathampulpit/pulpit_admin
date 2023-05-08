@extends('admin.layouts.main')

@section('title')
Customer Detail
@endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec detail-page">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('user.users') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.users.index',['panel' => Session::get('panel')]) }}">{{ @trans('user.list') }}</a>
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
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="tab" href="#trans">Transactions</a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content box">
                    <div id="detail" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-lg-12">

                                <!-- <a href="#editModal" data-toggle="modal" data-target="#editModal" class="btn btn-bordered btn-sm">{{ @trans('user.edit') }}</a> -->
                                @if($user_role == 'administrator' || $user_role == 'backoffice')
                                <div class="btn-group-em float-right mt-3" role="group">
                                    <a href="{{url(Session::get('panel').'/users/edit')}}/{{$user->id}}" class="btn btn-bordered btn-sm">{{ @trans('user.edit') }}</a><!-- {{$user->id}} -->
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
                                        <p class="display-info">{{'•••• '.substr($user->mobile_number, -4)}}</p>
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

                                    @if(!empty($linkcards))
                                    @foreach($linkcards as $l)
                                    <div class="form-group">

                                        @if($l['type'] == 'Physical')
                                        <label class="control-label">{{ @trans('user.physical_card_number') }}</label>
                                        @else
                                        <label class="control-label">{{ @trans('user.virtual_card_number') }}</label>
                                        @endif
                                        <p class="display-info" id="card_number">{{$l['card_number']}}</p>
                                    </div>
                                    @endforeach
                                    @endif

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.created_date') }}</label>
                                        <p class="display-info" id="card_number">{{$user->created_at}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.login_attempt') }}</label>
                                        <p class="display-info"><span id="reset_number">{{$user->login_attempt}}</span> <a href="javascript::void()" id="reset_btn" class="btn btn-bordered btn-sm">{{ @trans('user.reset') }}</a></p>
                                        <input type="hidden" name="user_id" id="user_id" value="{{$user->id}}">
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.otp_attempt') }}</label>
                                        <p class="display-info"><span id="otp_reset_number">{{$user->otp_attempt}}</span> <a href="javascript::void()" id="otp_reset_btn" class="btn btn-bordered btn-sm">{{ @trans('user.otp_reset') }}</a></p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('user.ussd_enable') }}</label>
                                        <p class="display-info">
                                            @if($user->ussd_enable == '1')
                                            <input type="hidden" name="ussd_enable" id="ussd_enable" value="0">
                                            <span id="ussd_enable_lable">On</span>
                                            <!-- <a href="javascript:void(0)" id="ussd_status_btn" class="btn btn-bordered btn-sm ussd-enable">Disable</a> -->
                                            @else
                                            <input type="hidden" name="ussd_enable" id="ussd_enable" value="1">
                                            <span id="ussd_enable_lable">Off</span>
                                            @endif

                                            <a href="javascript:void(0)" id="ussd_status_btn" class="btn btn-bordered btn-sm ussd-enable">
                                                @if($user->ussd_enable == '1')
                                                Disable
                                                @else
                                                Enable
                                                @endif
                                            </a>
                                        </p>
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

                    <div id="trans" class="tab-pane fade">
                        <div class="row">
                            <div class="col-lg-12">

                                <h3 class="detail-heading">{{ @trans('user.details') }}</h3>

                                <div class="table-data" v-if="items.length">
                                    <div class="table-responsive table-checkable">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>{{ @trans('transaction.transaction_id') }}<a href="javascript:void(0)" :class="classSort('trans_id')" @click="sort('trans_id')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.ara_receipt') }}<a href="javascript:void(0)" :class="classSort('ara_receipt')" @click="sort('ara_receipt')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.transaction_amount') }}<a href="javascript:void(0)" :class="classSort('transaction_amount')" @click="sort('transaction_amount')"></a></th>
                                                    <th>{{ @trans('transaction.account_number') }}<a href="javascript:void(0)" :class="classSort('account_number')" @click="sort('account_number')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.transaction_type') }}<a href="javascript:void(0)" :class="classSort('trans_type')" @click="sort('trans_type')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.transaction_date') }}<a href="javascript:void(0)" :class="classSort('trans_datetime')" @click="sort('trans_datetime')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.status') }}<a href="javascript:void(0)" :class="classSort('trans_status')" @click="sort('trans_status')"></a>
                                                    </th>
                                                    <th>{{ @trans('transaction.action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if(!empty($trans))
                                                <?php $i = 1; ?>
                                                @foreach($trans as $val)
                                                <tr>
                                                    <td>{{ $i }}</td>
                                                    <td>{{ $val['trans_id'] }}</td>
                                                    <td>{{ $val['ara_receipt'] }}</td>
                                                    <td>TZS {{ $val['amount'] }}</td>

                                                    <td>•••• {{ substr($val['account_number'], -4)}}</td>
                                                    <td>{{ $val['trans_type'] }}</td>
                                                    <td>{{ $val['trans_datetime'] }}</td>

                                                    @if($val['trans_status'] == '1')
                                                    <td><span class="em-badge green">Success</span></td>
                                                    @elseif($val['trans_status'] == '2')
                                                    <td><span class="em-badge orange">Pending</span></td>
                                                    @else
                                                    <td><span class="em-badge red">Failed</span></td>
                                                    @endif

                                                    <td class="act-btn">
                                                        <a href="{{URL::to('/')}}/super-admin/transactions/show/{{$val['id']}}" title="View" class="btn btn-view">
                                                            <i class="material-icons">remove_red_eye</i>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php $i++; ?>
                                                @endforeach
                                                @else
                                                <tr>
                                                    No Rocord Found!
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
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
                if (response == 'success') {
                    $('#reset_number').text(0);

                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Attemp reset successfully!',
                        actionText: 'Okay'
                    });
                } else {
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
                if (response == 'success') {
                    $('#otp_reset_number').text(0);

                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Attemp reset successfully!',
                        actionText: 'Okay'
                    });
                } else {
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
                "ussd_enable": ussd_enable
            },
            success: function(response) {
                if (response == 'success') {

                    if (ussd_enable == 1) {
                        $('#ussd_enable_lable').text('On');
                        $('#ussd_enable').val('0');
                        $('.ussd-enable').text('Disable');
                    } else {
                        $('#ussd_enable_lable').text('Off');
                        $('#ussd_enable').val('1');
                        $('.ussd-enable').text('Enable');
                    }

                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'USSD status change successfully.',
                        actionText: 'Okay'
                    });
                } else {
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