@extends('admin.layouts.main')


@section('title')
@if($id)
Edit Provider
@else
Add Provider
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">


            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('provider.providers') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.users.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('provider.edit_provider') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('provider.add_provider') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.providers.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('provider.edit_provider') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('provider.add_provider') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('provider.name') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="name" class="form-control"
                                                        value="@if(old('name')){{old('name')}}@elseif($id){{$item->name}}@endif"
                                                        placeholder="Enter Provider Name" />
                                                    @if($errors->has('name'))
                                                    <div class="invalid-feedback">{{ $errors->first('name')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group file-upload">
                                                    <label
                                                        class="control-label">{{ @trans('provider.provider_logo') }}</label>
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input fileupload"
                                                            id="logo" name="logo">
                                                        <label class="custom-file-label" for="logo"><img
                                                                src="{{ asset('/img/file-upload.svg')}}"
                                                                alt="upload">{{ @trans('provider.upload') }}</label>
                                                    </div>
                                                </div>
                                            </div>
                                            @if(isset($item->logo))
                                                <div class="col-md-12 previewDiv">
                                                    <div class="thumbnail-wrapper">
                                                        <img width="100" height="100" class="preview"
                                                            src="{{ config('custom.download.url') }}{{ config('custom.upload.provider.logo_path') }}/{{ $item->logo }}">
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

{!! JsValidator::formRequest('App\Http\Requests\Provider\StoreProvider', '#frmAddEditUser') !!}

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
@endpush