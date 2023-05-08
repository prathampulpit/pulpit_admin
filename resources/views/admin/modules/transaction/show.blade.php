@extends('admin.layouts.main')

@section('title')
Transactions Details
@endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec detail-page">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('transaction.transactions') }}</a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="{{ route('admin.transactions.index',['panel' => Session::get('panel')]) }}">{{ @trans('user.list') }}</a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('transaction.detail') }}</li>
                </ol>
            </nav>
            
            <div class="profile-detail">
                <div class="person-details">
                    <div class="about">
                        <h2 class="cndidate-name">{{ $trans->name }}</h2>
                    </div>
                </div>
            </div>

            <div class="detail-content mt-4">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#detail">{{ @trans('user.details') }}</a>
                    </li>
                </ul>

              <!-- Tab panes -->
                <div class="tab-content box">
                    <div id="detail" class="tab-pane fade show active">

                    <div class="row">
                        <div class="col-lg-12">
                            
                            <h3 class="detail-heading">{{ @trans('transaction.details') }}</h3>

                            <div class="info">
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.transaction_id') }}</label>
                                    <p class="display-info">{{$trans->trans_id}}</p>
                                </div> 
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.ara_receipt') }}</label>
                                    <p class="display-info">{{$trans->ara_receipt}}</p>
                                </div>                                
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.transaction_amount') }}</label>
                                    @if($trans->trans_type == 'Mobile money')
                                        <p class="display-info">TZS{{number_format($trans->mwallet_trans_amount)}}</p>
                                    @elseif($trans->trans_type != 'Stash')
                                        <p class="display-info">TZS{{number_format($trans->amount)}}</p>
                                    @else
                                        <p class="display-info">TZS{{number_format($trans->stash_amount)}}</p>
                                    @endif
                                </div>

                                @if($trans->master_pay_with_qwikrewards == '1')
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('transaction.qwikrewards_amount') }}</label>
                                        <p class="display-info">TZS{{number_format($trans->mastercard_qwikrewards_amount)}}</p>
                                    </div>
                                @endif

                                @if($trans->bill_pay_with_qwikrewards == '1')
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('transaction.qwikrewards_amount') }}</label>
                                        <p class="display-info">TZS{{number_format($trans->bill_qwikrewards_amount)}}</p>
                                    </div>
                                @endif

                                @if(!empty($trans->tip_amount))
                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('transaction.tip_amount') }}</label>
                                        <p class="display-info">TZS{{number_format($trans->tip_amount)}}</p>
                                    </div>

                                    <div class="form-group">
                                        <label class="control-label">{{ @trans('transaction.tip_reference') }}</label>
                                        <p class="display-info">{{$trans->tip_reference}}</p>
                                    </div>
                                @endif

                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.transaction_type') }}</label>
                                    @if(!empty($trans->party_name))
                                        <p class="display-info">{{$trans->trans_type}} - {{$trans->party_name}}</p>
                                    @else
                                        <p class="display-info">{{$trans->trans_type}}</p>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.status') }}</label>
                                    @if($trans->trans_status == 1)
                                        <span class="em-badge green">Success</span>
                                    @elseif($trans->trans_status == 2)
                                        <span class="em-badge orange">Pending</span>
                                    @else
                                        <span class="em-badge red">Failed</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.transaction_date') }}</label>
                                    <p class="display-info">{{$trans->trans_datetime}}</p>
                                </div>  
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.account_number') }}</label>
                                    <p class="display-info">{{$trans->account_number}}</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.category_name') }}</label>
                                    <p class="display-info">{{$trans->category_name}}</p>
                                </div>
                                <div class="form-group">
                                    <label class="control-label">{{ @trans('transaction.receipt') }}</label>
                                    <p class="display-info">{{$trans->receipt}}</p>
                                </div>                                                       
                            </div>

                        </div>
                    </div>
                    </div>
                    <div id="doc" class="tab-pane fade">
                    <ul class="p-0 m-0 doc-thumb">
                        <li>
                            <div class="thumb document-img"  href="#attachModal" data-toggle="modal" data-target="#attachModal" onclick="getImage('{{$document_file_path.$trans->document_file_name}}')">
                                <img src="{{$document_file_path.$trans->document_file_name}}">
                                <p><label class="control-label"></label><span class="display-info">{{$trans->document_name}}</span></p>
                            </div>
                        </li>
                    </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection
<div class="modal fade" id="attachModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Image View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <img id="image-gallery-image" src="">
            </div>
        </div>
    </div>
</div>
@push('pageJs')
<script type="text/javascript">
function getImage(imageName){
    $('#image-gallery-image').attr('src', imageName);
}
</script>
@endpush