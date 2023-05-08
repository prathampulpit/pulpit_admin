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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('cms_pages.cms') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.cms_pages.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('cms_pages.edit_cms') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('cms_pages.add_cms') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.cms_pages.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('cms_pages.edit_cms') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('cms_pages.add_cms') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('cms_pages.name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="page_name" id="page_name" required class="form-control" value="@if($id){{ $item['page_name'] }}@endif" placeholder="Enter Page Name" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('cms_pages.content') }} <span class="required">*</span> </label>
                                                    <textarea name="content" id="content"
                                                        class="form-control editor"
                                                        placeholder="Page Content" required>@if($id){{ $item['content'] }}@endif</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('cms_pages.content_sw') }} <span class="required">*</span> </label>
                                                    <textarea name="content_sw" id="content"
                                                        class="form-control editor"
                                                        placeholder="Page Content" required>@if($id){{ $item['content_sw'] }}@endif</textarea>
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
            menubar: true,
            branding: false,
            browser_spellcheck: true,
            mobile: {
                plugins: 'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker textpattern noneditable help formatpainter pageembed charmap mentions quickbars linkchecker emoticons advtable'
            },
            menu: {
                tc: {
                title: 'TinyComments',
                items: 'addcomment showcomments deleteallconversations'
                }
            },
            menubar: 'file edit view insert format tools table tc help',
            toolbar: 'undo redo | bold italic underline strikethrough | fontselect fontsizeselect formatselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist checklist | forecolor backcolor casechange permanentpen formatpainter removeformat | pagebreak | charmap emoticons | fullscreen  preview save print | insertfile image media pageembed template link anchor codesample | a11ycheck ltr rtl | showcomments addcomment',
            quickbars_selection_toolbar: 'bold italic | quicklink h2 h3 blockquote quickimage quicktable',
            a11y_advanced_options: true,
            toolbar_mode: 'sliding',
            height: 600,
            plugins: [
                'print preview powerpaste casechange importcss tinydrive searchreplace autolink autosave save directionality advcode visualblocks visualchars fullscreen image link media mediaembed template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists checklist wordcount tinymcespellchecker a11ychecker imagetools textpattern noneditable help formatpainter permanentpen pageembed charmap tinycomments mentions quickbars linkchecker emoticons advtable',
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