@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Setting
@else
Add Setting
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('setting.setting') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.settings.store',['panel' => Session::get('panel')]) }}">{{ @trans('setting.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('setting.edit_setting') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('setting.add_setting') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-12 offset-xl-0 col-lg-12 offset-lg-0">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.settings.store',['panel' => Session::get('panel')]) }}">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('setting.edit_setting') }}</h2>
                                        <input type="hidden" name="id" value="1">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('setting.add_setting') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.digest_spike') }}<span class="required">*</span></label>
                                                    <input type="text" name="digest_spike" class="form-control" value="{{$settings->digest_spike}}" placeholder="Enter Digest Spike" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('digest_spike')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
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
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.max_no_of_physical_cards') }}<span class="required">*</span></label>
                                                    <input type="text" name="max_no_of_physical_cards" class="form-control" value="{{$settings->max_no_of_physical_cards}}" placeholder="Enter {{$settings->max_no_of_physical_cards}}" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('max_no_of_physical_cards')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                        </div>

                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.maximum_share_number_limit') }}<span class="required">*</span></label>
                                                    <input type="text" name="maximum_share_number_limit" class="form-control" value="{{$settings->maximum_share_number_limit}}" placeholder="Enter {{$settings->maximum_share_number_limit}}" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('maximum_share_number_limit')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.minimum_funds_for_add_card') }}<span class="required">*</span></label>
                                                    <input type="text" name="minimum_funds_for_add_card" class="form-control" value="{{$settings->minimum_funds_for_add_card}}" placeholder="Enter {{ @trans('setting.minimum_funds_for_add_card') }}" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('minimum_funds_for_add_card')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.otp_timer') }}<span class="required">*</span></label>
                                                    <input type="text" name="otp_timer" class="form-control" value="{{$settings->otp_timer}}" placeholder="Enter {{ @trans('setting.otp_timer') }}" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('otp_timer')}}
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

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_message_en') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_message_en" class="form-control" placeholder="Enter {{ @trans('setting.referral_request_message_en') }}">{{$settings->referral_request_message_en}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_message_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_message_sw') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_message_sw" class="form-control" placeholder="Enter {{ @trans('setting.referral_request_message_sw') }}">{{$settings->referral_request_message_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_message_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_screen_title_en') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_screen_title_en" class="form-control" placeholder="Enter {{ @trans('setting.referral_request_screen_title_en') }}">{{$settings->referral_request_screen_title_en}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_screen_title_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_screen_title_sw') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_screen_title_sw" class="form-control" placeholder="Enter {{ @trans('setting.referral_request_screen_title_sw') }}">{{$settings->referral_request_screen_title_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_screen_title_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!--  -->
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_screen_content_en') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_screen_content_en" class="form-control editor" placeholder="Enter {{ @trans('setting.referral_request_screen_content_en') }}">{{$settings->referral_request_screen_content_en}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_screen_content_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_request_screen_content_sw') }}<span class="required">*</span></label>
                                                    <textarea name="referral_request_screen_content_sw" class="form-control editor" placeholder="Enter {{ @trans('setting.referral_request_screen_content_sw') }}">{{$settings->referral_request_screen_content_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_request_screen_content_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_welcome_message_en') }}<span class="required">*</span></label>
                                                    <textarea name="referral_welcome_message_en" id="content" class="form-control editor" placeholder="Page Content" required>{{$settings->referral_welcome_message_en}}</textarea>
                                                                                    
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_welcome_message_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.referral_welcome_message_en') }}<span class="required">*</span></label>
                                                    <textarea name="referral_welcome_message_sw" id="content" class="form-control editor" placeholder="Page Content" required>{{$settings->referral_welcome_message_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('referral_welcome_message_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.contact_list_screen_message_en') }}<span class="required">*</span></label>
                                                    <textarea name="contact_list_screen_message_en" id="content" class="form-control" placeholder="Page Content" required>{{$settings->contact_list_screen_message_en}}</textarea>
                                                                                    
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('contact_list_screen_message_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.contact_list_screen_message_sw') }}<span class="required">*</span></label>
                                                    <textarea name="contact_list_screen_message_sw" id="content" class="form-control" placeholder="Page Content" required>{{$settings->contact_list_screen_message_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('contact_list_screen_message_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.refer_a_friend_success_message_en') }}<span class="required">*</span></label>
                                                    <textarea name="refer_a_friend_success_message_en" id="content" class="form-control editor" placeholder="Page Content" required>{{$settings->refer_a_friend_success_message_en}}</textarea>
                                                                                    
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('refer_a_friend_success_message_en')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('setting.refer_a_friend_success_message_sw') }}<span class="required">*</span></label>
                                                    <textarea name="refer_a_friend_success_message_sw" id="content" class="form-control editor" placeholder="Page Content" required>{{$settings->refer_a_friend_success_message_sw}}</textarea>
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('refer_a_friend_success_message_sw')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="form-action text-right">
                                        <input type="submit" class="btn step-btn" value="{{ @trans('user.save') }}">
                                    </div>
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

@push('pageJs')

<script>
    $(document).ready(function() {
        var myForm = $('#frmAddEditUser');
        $.data(myForm[0], 'validator').settings.ignore = "null";

        tinymce.init({
            selector: 'textarea.editor',
            height: 300,
            menubar: false,
            branding: false,
            browser_spellcheck: true,
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
            plugins: [
                'link preview wordcount lists'
            ],
            setup: function(editor) {
                editor.on('keyUp', function() {
                    tinyMCE.triggerSave();

                    if (!$.isEmptyObject(myForm.validate().submitted))
                        myForm.validate().form();
                });
            },

        });
    });
    
$("#frmAddEditUser").each(function() {
    $(this).validate({
        rules: {
            'first_name': 'required',
            'last_name':  'required',
            'email':{
                'required' : true,
                'maxlength': 255
            },
            'mobile':{
                'required' : true,
                'digits'   : true,
                'minlength': 10,
                'maxlength': 10
            }
        },
        messages: {
            'first_name': 'First name is required',
            'last_name':  'Last name is required',
            'email':{
                'required' : 'Email is required',
                'maxlength': 'Email may not be greater than 255 characters.'
            },
            'mobile':{
                'required' : 'Mobile is required',
                'digits'   : 'The mobile must be 10 digits.',
                'minlength': 'The mobile must be 10 digits.',
                'maxlength': 'The mobile must be 10 digits.'
            }
        },
        highlight: function(element) {
            $(element).closest('.form-control').removeClass('is-valid').addClass('is-invalid');
        },
        unhighlight: function(element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        success: function(element) {
            $(element).closest('.form-control').removeClass('is-invalid').addClass('is-valid');
        },
        errorClass: 'invalid-feedback',
    });
});

$.validator.addMethod('passRegex', function (value) { 
    return /^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@()$%^&*=_{}[\]:;\"'|\\<>,.\/~`±§+-]).{8,30}$/.test(value); 
}, 'The password must be 8–30 characters, and include a number, a symbol, a lower and a upper case letter');

@if($id)
    $(".email").keyup(function() {
        if ($(".email").val() != $(".emailHidden").val()) {
            $(".password").next('span').remove();
            $(".confirm-password").next('span').remove();

            $('#password').rules("add", {
                required : true,
                passRegex : true,
                messages: { required: "Password is required" }
            });

            $('#confirm_password').rules("add", {
                required : true,
                equalTo : '#password',
                messages: { required: "Confirm Password is required",
                            equalTo : "The password confirmation does not match." }
            });
        } else {
            if(!$(".password").next('span').length) {
                $(".password").after('<span class="star">••••••••</span>');
                $(".confirm-password").after('<span class="star">••••••••</span>');
            }
            $('#password-error').remove();
            $('#confirm_password-error').remove();
            $('#password').rules("remove");
            $('#confirm_password').rules("remove");
        }
    });
    $( ".password" ).keyup(function() {
        $(".star").remove();
    });
@else
     $('#password').rules("add", {
        required : true,
        passRegex : true,
        messages: { required: "Password is required" }
    });

    $('#confirm_password').rules("add", {
        required : true,
        equalTo : '#password',
        messages: { required: "Confirm Password is required",
                    equalTo : "The password confirmation does not match." }
    });
@endif

</script>
@endpush