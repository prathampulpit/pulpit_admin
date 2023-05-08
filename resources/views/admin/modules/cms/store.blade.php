@extends('admin.layouts.main')


@section('title')
@if($id)
Edit CMS Content
@else
Add CMS Content
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">


            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('cms.cms') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.cms.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('cms.edit_cms') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('cms.add_cms') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.cms.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('cms.edit_cms') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('cms.add_cms') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('cms.name') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="@if(old('name')){{old('name')}}@elseif($id){{$item->name}}@endif"
                                                        placeholder="Enter Page Name" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('name')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label
                                                        class="control-label">{{ @trans('cms.content') }}
                                                        <span class="required">*</span>
                                                    </label>
                                                    <textarea name="content" id="content"
                                                        class="form-control editor"
                                                        placeholder="Page Content" required>@if($id){{ $item->content }}@endif</textarea>
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

{!! JsValidator::formRequest('App\Http\Requests\Cms\StoreCmsContent', '#frmAddEditUser') !!}

<script type="text/javascript">
$(document).ready(function() {
        var myForm = $('#frmAddEditUser');
        $.data(myForm[0], 'validator').settings.ignore = "null";

        tinymce.init({
            selector: 'textarea.editor',
            height: 300,
            menubar: false,
            branding: false,
            browser_spellcheck: true,
            toolbar: 'undo redo | bold italic underline strikethrough | link | alignleft aligncenter alignright alignjustify | bullist numlist  | removeformat | preview',
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