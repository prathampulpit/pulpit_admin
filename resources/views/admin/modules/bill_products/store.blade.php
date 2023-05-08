@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Bill Product
@else
Add Bill Product
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">


            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('bill_products.bill_product') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.bill_products.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('bill_products.edit_bill_product') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('bill_products.add_bill_product') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.bill_products.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('bill_products.edit_bill_product') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('bill_products.add_bill_product') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bill_products.name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="name" id="name" class="form-control" value="@if($id) {{ $item['product_name'] }} @endif" placeholder="Enter Name" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bill_products.short_name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="short_name" id="short_name" class="form-control" value="@if($id) {{ $item['short_name'] }} @endif" placeholder="Enter Short Name" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bill_products.no_of_transaction') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="no_of_transaction" id="no_of_transaction" class="form-control" value="@if($id) {{ $item['no_of_transaction'] }} @endif" placeholder="Enter No Of Trans" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bill_products.amount_limit_per_transaction') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="amount_limit_per_transaction" id="amount_limit_per_transaction" class="form-control" value="@if($id) {{ $item['amount_limit_per_transaction'] }} @endif" placeholder="Enter Amount Limit Per Transations" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group file-upload">
                                                    <label
                                                        class="control-label">{{ @trans('bill_products.icon') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input fileupload"
                                                            id="icon" name="icon">
                                                        <label class="custom-file-label" for="logo"><img
                                                                src="{{ asset('img/admin/file-upload.svg')}}"
                                                                alt="upload">{{ @trans('bill_products.upload') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($item['icon']))
                                            <div class="col-md-12 previewDiv">
                                                <div class="thumbnail-wrapper">
                                                    <img width="100" height="100" class="preview"
                                                        src="{{ env('APP_URL') . '/storage/' }}{{ config('custom.upload.billerImages') }}/{{ $item['icon'] }}">
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