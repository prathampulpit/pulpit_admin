@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Category Product
@else
Add Category Product
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('category.category') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.category.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('category.edit_category') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('category.add_category') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.category.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('category.edit_category') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('category.add_category') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('category.name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="name" id="name" class="form-control" value="@if($id) {{ $item['category_name'] }} @endif" placeholder="Enter Name" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('category.name_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="name_sw" id="name_sw" class="form-control" value="@if($id) {{ $item['name_sw'] }} @endif" placeholder="Enter Name Swahili" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group file-upload">
                                                    <label class="control-label">{{ @trans('category.icon') }}</label>
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
                                                        src="{{ env('APP_URL') . '/storage/' }}{{ config('custom.upload.category') }}/{{ $item['icon'] }}">
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

{!! JsValidator::formRequest('App\Http\Requests\Cms\StoreCmsContent', '#frmAddEditUser') !!}
<script>
@if($id)
    $('.deleteLogo').click(function() {
        var deleteLogoUrl = "{{ route('admin.providers.delete_logo',['panel' => Session::get('panel')])}}";
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