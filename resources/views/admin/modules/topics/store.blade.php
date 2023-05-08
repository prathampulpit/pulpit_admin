@extends('admin.layouts.main')

@section('title')
@if($id)
Edit Topic
@else
Add Topic
@endif

@endsection

@section('content')
<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">
            
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('topics.manage_topics') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.topics.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('topics.edit_topics') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('topics.topics') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.topics.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('topics.edit_topics') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('topics.topics') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('topics.type') }}
                                                    <span class="required" aria-required="true">*</span></label>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="customRadio3" name="type" value="Dispute Transaction" checked @if($item['type'] == 'Dispute Transaction') checked @endif>
                                                        <label class="custom-control-label" for="customRadio3">Dispute Transaction</label>
                                                    </div>
                                                    <div class="custom-control custom-radio">
                                                        <input type="radio" class="custom-control-input" id="customRadio" name="type" value="Contact and Support" @if($item['type'] == 'Contact and Support') checked @endif>
                                                        <label class="custom-control-label" for="customRadio">Contact and Support</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('topics.name_en') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="name_en" id="name_en" class="form-control" value="@if($id) {{ $item['name_en'] }} @endif" placeholder="Enter Topic In English" required />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('topics.name_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="name_sw" id="name_sw" class="form-control" value="@if($id) {{ $item['name_sw'] }} @endif" placeholder="Enter Topic In Swahili" required />
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

{!! JsValidator::formRequest('App\Http\Requests\Topics\StoreTopics', '#frmAddEditUser') !!}
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