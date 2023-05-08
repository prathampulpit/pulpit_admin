@extends('admin.layouts.main')

@section('title')
Settings
@endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec detail-page">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('setting.settings') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('setting.detail') }}</li>
                </ol>
            </nav>
            
            <div class="profile-detail">
                <div class="person-details">
                    <div class="about">
                        <h2 class="cndidate-name"></h2>
                    </div>
                </div>
            </div>

            <div class="detail-content mt-4">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#detail">{{ @trans('setting.details') }}</a>
                    </li>
                </ul>

              <!-- Tab panes -->
                <div class="tab-content box">
                    <div id="detail" class="tab-pane fade show active">
                        <div class="row">
                            <div class="col-lg-12">
                                @if($user_role == 'administrator')
                                <div class="btn-group-em float-right mt-3" role="group">
                                    <!-- <a href="#editModal" data-toggle="modal" data-target="#editModal" class="btn btn-bordered btn-sm">{{ @trans('setting.edit') }}</a> -->
                                    <a href="{{url(Session::get('panel').'/settings/edit')}}/1" class="btn btn-bordered btn-sm">{{ @trans('setting.edit') }}</a>
                                </div>
                                @endif
                                <h3 class="detail-heading">{{ @trans('setting.details') }}</h3>

                                <div class="info">
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.digest_spike') }}</label>
                                        <p class="display-info">{{$settings->digest_spike}}%</p>
                                    </div>                                
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.graphs_months_interval') }}</label>
                                        <p class="display-info">{{$settings->graphs_months_interval}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.minimum_funds_for_add_card') }}</label>
                                        <p class="display-info">TZS{{$settings->minimum_funds_for_add_card}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.ara_to_other_country') }}</label>
                                        @if($settings->ara_to_other_country == 1)
                                            <span class="em-badge green">Enable</span>
                                        @else
                                            <span class="em-badge red">Disable</span>
                                        @endif
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.bubble_text_en') }}</label>
                                        <p class="display-info">{{$settings->bubble_text}}</p>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.bubble_text_sw') }}</label>
                                        <p class="display-info">{{$settings->bubble_text_sw}}</p>
                                    </div> 
                                                                         
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.refer_friend_text') }}</label>
                                        <p class="display-info">{{$settings->refer_friend_text}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.refer_friend_error_message_en') }}</label>
                                        <p class="display-info">{{$settings->refer_friend_error_message_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.refer_friend_error_message_sw') }}</label>
                                        <p class="display-info">{{$settings->refer_friend_error_message_sw}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.maximum_referral_request_message_en') }}</label>
                                        <p class="display-info">{{$settings->maximum_referral_request_message_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.maximum_referral_request_message_sw') }}</label>
                                        <p class="display-info">{{$settings->maximum_referral_request_message_sw}}</p>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.agent_locator_distance') }}</label>
                                        <p class="display-info">{{$settings->agent_locator_distance}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.total_otp_attempt') }}</label>
                                        <p class="display-info">{{$settings->total_otp_attempt}}</p>
                                    </div> 

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.otp_attempt_min_time') }}</label>
                                        <p class="display-info">{{$settings->otp_attempt_min_time}}</p>
                                    </div> 
                                    
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.maximum_referral_request_limit') }}</label>
                                        <p class="display-info">{{$settings->maximum_referral_request_limit}}</p>
                                    </div> 
                                    
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.maximum_share_number_limit') }}</label>
                                        <p class="display-info">{{$settings->maximum_share_number_limit}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_instruction_title_en') }}</label>
                                        <p class="display-info">{{$settings->referral_instruction_title_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_instruction_title_sw') }}</label>
                                        <p class="display-info">{{$settings->referral_instruction_title_sw}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_instruction_en') }}</label>
                                        <p class="display-info">{{$settings->referral_instruction_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_instruction_sw') }}</label>
                                        <p class="display-info">{{$settings->referral_instruction_sw}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_enable') }}</label>
                                        <p class="display-info">
                                            @if($settings->referral_enable == '1')
                                                <input type="hidden" name="referral_enable" id="referral_enable" value="0">
                                                <span id="referral_enable_lable">Yes</span>
                                            @else
                                                <input type="hidden" name="referral_enable" id="referral_enable" value="1">
                                                <span id="referral_enable_lable">No</span>                                            
                                            @endif   

                                            <a href="javascript:void(0)" id="referral_status_btn" class="btn btn-bordered btn-sm referral-enable">
                                                @if($settings->referral_enable == '1')
                                                    Disable
                                                @else
                                                    Enable
                                                @endif
                                            </a>                         
                                        </p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_welcome_message_en') }}</label>
                                        <p class="display-info">{!! $settings->referral_welcome_message_en !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_welcome_message_sw') }}</label>
                                        <p class="display-info">{!! $settings->referral_welcome_message_sw !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_message_en') }}</label>
                                        <p class="display-info">{{$settings->referral_request_message_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_message_sw') }}</label>
                                        <p class="display-info">{{$settings->referral_request_message_sw}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_screen_title_en') }}</label>
                                        <p class="display-info">{{$settings->referral_request_screen_title_en}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_screen_title_sw') }}</label>
                                        <p class="display-info">{{$settings->referral_request_screen_title_sw}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_screen_content_en') }}</label>
                                        <p class="display-info">{!! $settings->referral_request_screen_content_en !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.referral_request_screen_content_sw') }}</label>
                                        <p class="display-info">{!! $settings->referral_request_screen_content_sw !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.refer_a_friend_success_message_en') }}</label>
                                        <p class="display-info">{!! $settings->refer_a_friend_success_message_en !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.refer_a_friend_success_message_sw') }}</label>
                                        <p class="display-info">{!!$settings->refer_a_friend_success_message_sw !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.contact_list_screen_message_en') }}</label>
                                        <p class="display-info">{!! $settings->contact_list_screen_message_en !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.contact_list_screen_message_sw') }}</label>
                                        <p class="display-info">{!!$settings->contact_list_screen_message_sw !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.max_no_of_physical_cards') }}</label>
                                        <p class="display-info">{!!$settings->max_no_of_physical_cards !!}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('setting.otp_timer') }}</label>
                                        <p class="display-info">{!!$settings->otp_timer !!}</p>
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
    <div class="modal-dialog modal-md modal-dialog-centered" style="max-width: 60%;">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Edit Settings</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            
            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" action="{{ route('admin.settings.store',['panel' => Session::get('panel')]) }}">
                @csrf
                    <input type="hidden" name="id" id="id" value="1">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.digest_spike') }}<span class="required">*</span></label>
                                <input type="text" name="digest_spike" class="form-control" value="{{$settings->digest_spike}}" placeholder="Enter Digest Spike" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('digest_spike')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.graphs_months_interval') }}<span class="required">*</span></label>
                                <input type="text" name="graphs_months_interval" class="form-control" value="{{$settings->graphs_months_interval}}" placeholder="Enter Graphs Months Interval" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('graphs_months_interval')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.maximum_referral_request_limit') }}<span class="required">*</span></label>
                                <input type="text" name="maximum_referral_request_limit" class="form-control" value="{{$settings->maximum_referral_request_limit}}" placeholder="Enter {{$settings->maximum_referral_request_limit}}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('maximum_referral_request_limit')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.maximum_share_number_limit') }}<span class="required">*</span></label>
                                <input type="text" name="maximum_share_number_limit" class="form-control" value="{{$settings->maximum_share_number_limit}}" placeholder="Enter {{$settings->maximum_share_number_limit}}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('maximum_share_number_limit')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.minimum_funds_for_add_card') }}<span class="required">*</span></label>
                                <input type="text" name="minimum_funds_for_add_card" class="form-control" value="{{$settings->minimum_funds_for_add_card}}" placeholder="Enter {{ @trans('setting.minimum_funds_for_add_card') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('minimum_funds_for_add_card')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.ara_to_other_country') }}<span class="required">*</span></label>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="customRadio3" name="ara_to_other_country" value="1" @if($settings->ara_to_other_country == '1') checked @endif>
                                    <label class="custom-control-label" for="customRadio3">Enable</label>
                                </div>
                                <div class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" id="customRadio" name="ara_to_other_country" value="0" @if($settings->ara_to_other_country == '0') checked @endif>
                                    <label class="custom-control-label" for="customRadio">Disable</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.maximum_referral_request_message_en') }}<span class="required">*</span></label>
                                <textarea name="maximum_referral_request_message_en" class="form-control" placeholder="Enter {{ @trans('setting.maximum_referral_request_message_en') }}" required>{{$settings->maximum_referral_request_message_en}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('maximum_referral_request_message_en')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.maximum_referral_request_message_sw') }}<span class="required">*</span></label>
                                <textarea name="maximum_referral_request_message_sw" class="form-control" placeholder="Enter {{ @trans('setting.maximum_referral_request_message_sw') }}" required>{{$settings->maximum_referral_request_message_sw}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('maximum_referral_request_message_sw')}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!--  -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.referral_instruction_title_en') }}<span class="required">*</span></label>
                                <input type="text" name="referral_instruction_title_en" class="form-control" value="{{$settings->referral_instruction_title_en}}" placeholder="Enter {{$settings->referral_instruction_title_en}}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('referral_instruction_title_en')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.referral_instruction_title_sw') }}<span class="required">*</span></label>
                                <input type="text" name="referral_instruction_title_sw" class="form-control" value="{{$settings->referral_instruction_title_sw}}" placeholder="Enter {{$settings->referral_instruction_title_sw}}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('referral_instruction_title_sw')}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.referral_instruction_en') }}<span class="required">*</span></label>
                                <textarea name="referral_instruction_en" class="form-control" placeholder="Enter {{ @trans('setting.referral_instruction_en') }}" required>{{$settings->referral_instruction_en}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('referral_instruction_en')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.referral_instruction_sw') }}<span class="required">*</span></label>
                                <textarea name="referral_instruction_sw" class="form-control" placeholder="Enter {{ @trans('setting.referral_instruction_sw') }}" required>{{$settings->referral_instruction_sw}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('referral_instruction_sw')}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>   
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.refer_friend_error_message_en') }}<span class="required">*</span></label>
                                <textarea name="refer_friend_error_message_en" class="form-control" placeholder="Enter {{ @trans('setting.refer_friend_error_message_en') }}" required>{{$settings->refer_friend_error_message_en}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('refer_friend_error_message_en')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.refer_friend_error_message_sw') }}<span class="required">*</span></label>
                                <textarea name="refer_friend_error_message_sw" class="form-control" placeholder="Enter {{ @trans('setting.refer_friend_error_message_sw') }}" required>{{$settings->refer_friend_error_message_sw}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('refer_friend_error_message_sw')}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div> 

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.refer_friend_text') }}<span class="required">*</span></label>
                                <textarea name="refer_friend_text" class="form-control" placeholder="Enter {{ @trans('setting.refer_friend_text') }}">{{$settings->refer_friend_text}}</textarea>
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('refer_friend_text')}}
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.total_otp_attempt') }}<span class="required">*</span></label>
                                <input type="text" name="total_otp_attempt" class="form-control" value="{{$settings->total_otp_attempt}}" placeholder="Enter {{ @trans('setting.total_otp_attempt') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('total_otp_attempt')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.otp_attempt_min_time') }}<span class="required">*</span></label>
                                <input type="text" name="otp_attempt_min_time" class="form-control" value="{{$settings->otp_attempt_min_time}}" placeholder="Enter {{ @trans('setting.otp_attempt_min_time') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('otp_attempt_min_time')}}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>  
                    
                    <div class="row">
                        <div class="col-md-4">                            
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.agent_locator_distance') }}<span class="required">*</span></label>
                                <input type="text" name="agent_locator_distance" class="form-control" value="{{$settings->agent_locator_distance}}" placeholder="Enter {{ @trans('setting.agent_locator_distance') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('agent_locator_distance')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">                            
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.bubble_text_en') }}<span class="required">*</span></label>
                                <input type="text" name="bubble_text" class="form-control" value="{{$settings->bubble_text}}" placeholder="Enter {{ @trans('setting.bubble_text_en') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('bubble_text')}}
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="control-label">{{ @trans('setting.bubble_text_sw') }}<span class="required">*</span></label>
                                <input type="text" name="bubble_text_sw" class="form-control" value="{{$settings->bubble_text_sw}}" placeholder="Enter {{ @trans('setting.bubble_text_sw') }}" />
                                @if($errors->has('name'))
                                <div class="invalid-feedback">{{ $errors->first('bubble_text_sw')}}
                                </div>
                                @endif
                            </div>
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
@push('pageJs')
<script>
    var indexUrlJson = "{{route('admin.cms_pages.index_json',['panel' => Session::get('panel')])}}";
    var deleteUrl = "{{ url(Session::get('panel').'/cms_pages/destroy') }}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif

    $('#referral_status_btn').on('click', function() {
        var id = '1';
        var referral_enable = $('#referral_enable').val();
        var siteurl = "{{url(Session::get('panel').'/settings/changeReferralStatus')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
                "id": id,
                "referral_enable":referral_enable
            },
            success: function(response) {
                if(response == 'success'){

                    if(referral_enable == 1){
                        $('#referral_enable_lable').text('Yes');
                        $('#referral_enable').val('0');
                        $('.referral-enable').text('Disable');        
                    }else{
                        $('#referral_enable_lable').text('No');
                        $('#referral_enable').val('1'); 
                        $('.referral-enable').text('Enable');  
                    }

                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Referral status change successfully.',
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
                //$this.button('reset');
            }
        });
    });
</script>
@endpush