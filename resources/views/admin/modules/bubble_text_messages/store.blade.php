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
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('bubble_text_messages.cms') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a
                            href="{{ route('admin.bubble_text_messages.index',['panel' => Session::get('panel') ]) }}">{{ @trans('app.list') }}</a>
                    </li>
                    @if($id)
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('bubble_text_messages.edit_cms') }}</li>
                    @else
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('bubble_text_messages.add_cms') }}</li>
                    @endif
                </ol>
            </nav>

            <div class="form-sec">
                <div class="container">
                    <div class="row">
                        <div class="col-xl-8 offset-xl-2 col-lg-10 offset-lg-1">
                            <div class="step-box">
                                <form id="frmAddEditUser" method="post"
                                    action="{{ route('admin.bubble_text_messages.store',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-body">
                                        @if($id)
                                        <h2 class="form-sec-title">{{ @trans('bubble_text_messages.edit_cms') }}</h2>
                                        <input type="hidden" name="id" value="{{$id}}">
                                        @else
                                        <h2 class="form-sec-title">{{ @trans('bubble_text_messages.add_cms') }}</h2>
                                        @endif

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bubble_text_messages.bubble_text_en') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="bubble_text_en" id="bubble_text_en" class="form-control" value="@if($id){{ $item['bubble_text_en'] }}@endif" placeholder="Enter bubble text en" />
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bubble_text_messages.bubble_text_sw') }}
                                                    <span class="required">*</span></label>
                                                    <input type="text" name="bubble_text_sw" id="bubble_text_sw" class="form-control" value="@if($id){{ $item['bubble_text_sw'] }}@endif" placeholder="Enter bubble text sw" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bubble_text_messages.expiry_date') }}
                                                        <span class="required">*</span></label>
                                                    <input type="text" name="expiry_date" id="dob" v-model="dob"
                                                        class="form-control"
                                                        value="@if(old('expiry_date')){{old('expiry_date')}}@elseif($id){{$item->expiry_date}}@endif"
                                                        placeholder="Enter Expiry Date" data-date-end-date="0d" readonly/>
                                                    @if($errors->has('dob'))
                                                    <div class="invalid-feedback">{{ $errors->first('expiry_date')}}
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">{{ @trans('bubble_text_messages.username') }}
                                                    <span class="required">*</span></label>
                                                    <select multiple="multiple" name="user_id[]" id="user_id" class="form-control select2">
                                                    @if($id)
                                                        @foreach($users as $v)
                                                            <option value="{{$v['id']}}" <?php echo (isset($u_id_arr) && in_array($v['id'], $u_id_arr) ) ? "selected" : "" ?>>{{$v['name']}}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($users as $v)
                                                            <option value="{{$v['id']}}">{{$v['name']}}</option>
                                                        @endforeach
                                                    @endif
                                                    </select>
                                                    @if($errors->has('user_id'))
                                                    <div class="invalid-feedback">{{ $errors->first('user_id')}}
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

{!! JsValidator::formRequest('App\Http\Requests\BubbleTextMessages\StoreBubbleTextMessage', '#frmAddEditUser') !!}

<script type="text/javascript">
$(document).ready(function() {
    $('#user_id').select2();          
});
</script>
@endpush