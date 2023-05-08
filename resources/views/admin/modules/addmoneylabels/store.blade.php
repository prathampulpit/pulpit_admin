@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Money Labels
@else
Add Money Labels
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('add_money_labels.manage_add_money_label') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.category.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('add_money_labels.edit_add_money_label') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('add_money_labels.add_money_label') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.addmoneylabels.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('add_money_labels.edit_add_money_label') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('add_money_labels.add_money_label') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('add_money_labels.display_name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="display_name" id="display_name" class="form-control" value="@if($id) {{ $item['display_name'] }} @endif" placeholder="Enter Display Name" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('add_money_labels.display_name_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="display_name_sw" id="display_name_sw" class="form-control" value="@if($id) {{ $item['display_name_sw'] }} @endif" placeholder="Enter Display Name SW" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('add_money_labels.sub_title') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="sub_title" id="sub_title" class="form-control" value="@if($id) {{ $item['sub_title'] }} @endif" placeholder="Enter Sub Title" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('add_money_labels.sub_title_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="sub_title_sw" id="sub_title_sw" class="form-control" value="@if($id) {{ $item['sub_title_sw'] }} @endif" placeholder="Enter Sub Title SW" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group file-upload">
                                                    <label class="control-label">{{ @trans('add_money_labels.icon') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input fileupload"
                                                            id="icon" name="icon">
                                                        <label class="custom-file-label" for="logo"><img
                                                                src="{{ asset('img/admin/file-upload.svg')}}"
                                                                alt="upload">{{ @trans('category.upload') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($item['icon']))
                                            <div class="col-md-12 previewDiv">
                                                <div class="thumbnail-wrapper">
                                                    <img width="100" height="100" class="preview"
                                                        src="{{ env('APP_URL') . '/storage/' }}{{ config('custom.upload.addmoney') }}/{{ $item['icon'] }}">
                                                    <a href="javascript:void(0)" class="deleteLogo">
                                                        <i class="material-icons delete">close</i>
                                                    </a>
                                                </div>
                                            </div>
                                            @else
                                            <div class="col-md-12 previewDiv" style="display:none;">
                                                <div class="thumbnail-wrapper">
                                                    <img width="100" height="100" class="preview" src="">
                                                </div>
                                            </div>
                                            @endif
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

{!! JsValidator::formRequest('App\Http\Requests\AddMoneyLabels\StoreAddMoneyLabels', '#frmAddEditUser') !!}
<script>
@if($id)
    $('.deleteLogo').click(function() {
        var deleteLogoUrl = "{{ route('admin.addmoneylabels.delete_icon',['panel' => Session::get('panel')])}}";
        var id = "{{ $item->id }}";
        axios.post(deleteLogoUrl, {
            id : id
        })
        .then(function(response) {
            $('.previewDiv').css('display','none');
        })
        .catch(function(error) {

        });
    });
@endif
</script>
<script type="text/javascript">
@if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
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
</script>
@endpush