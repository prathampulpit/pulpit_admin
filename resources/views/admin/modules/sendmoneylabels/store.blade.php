@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Send Money Labels
@else
Add Send Money Labels
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('send_money_labels.manage_send_money_label') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.category.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('send_money_labels.edit_send_money_label') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('send_money_labels.send_money_label') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.sendmoneylabels.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('send_money_labels.edit_send_money_label') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('send_money_labels.send_money_label') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('send_money_labels.display_name') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="display_name" id="display_name" class="form-control" value="@if($id) {{ $item['display_name'] }} @endif" placeholder="Enter Display Name" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('send_money_labels.display_name_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="display_name_sw" id="display_name_sw" class="form-control" value="@if($id) {{ $item['display_name_sw'] }} @endif" placeholder="Enter Display Name SW" required />
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('send_money_labels.sub_title') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="sub_title" id="sub_title" class="form-control" value="@if($id) {{ $item['sub_title'] }} @endif" placeholder="Enter Sub Title" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('send_money_labels.sub_title_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="sub_title_sw" id="sub_title_sw" class="form-control" value="@if($id) {{ $item['sub_title_sw'] }} @endif" placeholder="Enter Sub Title SW" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group file-upload">
                                                    <label class="control-label">{{ @trans('send_money_labels.icon') }}</label>
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
                                                        src="{{ env('APP_URL') . '/storage/' }}{{ config('custom.upload.sendmoney') }}/{{ $item['icon'] }}">
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

{!! JsValidator::formRequest('App\Http\Requests\SendMoneyLabels\StoreSendMoneyLabels', '#frmAddEditUser') !!}
<script>
@if($id)
    $('.deleteLogo').click(function() {
        var deleteLogoUrl = "{{ route('admin.sendmoneylabels.delete_icon',['panel' => Session::get('panel')])}}";
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
</script>
@endpush