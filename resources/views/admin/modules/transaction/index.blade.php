@extends('admin.layouts.main')

@section('title') Transactions @endsection

@section('content')

<main>

    <loading :active.sync="isLoading"></loading>

    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec" v-cloak>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('transaction.transactions') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('transaction.list') }}</li>
                </ol>
            </nav>

            <div class="top-bar d-flex align-items-center">
                <h2>{{ @trans('transaction.transactions') }}</h2>

                <div class="right-table-container ml-auto">
                    {{-- <a v-if="!filtering && items.length" href="javascript:void(0)" class="btn btn-em"
                        data-toggle="modal" data-target="#filter">
                        <img src="{{ asset('img/admin/ic_filter.svg') }}" alt=""
                            class="mr-1">{{ @trans('user.filter') }}</a> --}}

                    <a v-if="filtering" href="javascript:void(0)" class="btn btn-em" data-toggle="modal"
                        data-target="#filter">
                        <img src="{{ asset('img/admin/ic_filter_edit.svg') }}" class="mr-1" alt="filter" /> Edit Filter</a>
                    
                    <!-- <a href="{{ route('admin.users.create',['panel' => Session::get('panel')]) }}"
                        class="btn btn-em btn-primary-em">{{ @trans('user.add_user') }}</a> -->
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-top-panel d-flex align-items-center">
                    <div class="form-group search-input float-left mb-0">
                        <img src="{{asset('img/admin/ic_search.png')}}" srcset="../../img/admin/ic_search@2x.png 2x" alt="search">
                        <input type="text" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
                        <i class="material-icons" v-if="search_text" @click="clearSearch">close</i>
                    </div>

                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto">
                        <label style="white-space: nowrap;" class="mb-0 gray">Trans Type</label>
                        <select class="form-control" id="trans_type" v-model="trans_type" @input="transType">
                            <option value="all" selected>All</option>
                            <option value="4">Send money</option>
                            <option value="5">Qwikcash</option>
                            <option value="1">Add Money</option>
                            <option value="3">Pay Bills</option>
                            <option value="6">Mastercard QR</option>                            
                            <option value="7">Stash</option>
                            <option value="2">Currency transfer</option>
                            <option value="9">Card</option>                            
                        </select>
                    </div>

                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto" v-if="items.length">
                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('transaction.record_per_page') }}</label>
                        <select class="form-control" id="per_page" v-model="per_page" @input="perPage">
                            <option value="25">25 </option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="all">All</option>
                        </select>
                    </div>
                    <div class="pagination d-flex justify-content-center ml-auto" v-if="items.length">
                        @include('admin.common.paginate_js')
                    </div>
                </div>

                <div class="table-data" v-if="items.length">
                    <div class="table-responsive table-checkable">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ @trans('transaction.transaction_id') }}<a href="javascript:void(0)" :class="classSort('trans_id')" @click="sort('trans_id')"></a>
                                    </th>
                                    <th>{{ @trans('transaction.ara_receipt') }}<a href="javascript:void(0)" :class="classSort('ara_receipt')" @click="sort('ara_receipt')"></a>
                                    </th>                                    
                                    <th>{{ @trans('transaction.transaction_amount') }}<a href="javascript:void(0)" :class="classSort('transaction_amount')" @click="sort('transaction_amount')"></a>
                                    <th>{{ @trans('transaction.transaction_type') }}<a href="javascript:void(0)" :class="classSort('trans_type')" @click="sort('trans_type')"></a>
                                    </th>
                                    <th>{{ @trans('transaction.transaction_date') }}<a href="javascript:void(0)" :class="classSort('trans_datetime')" @click="sort('trans_datetime')"></a>
                                    </th>
                                    <th>{{ @trans('transaction.status') }}<a href="javascript:void(0)" :class="classSort('trans_status')" @click="sort('trans_status')"></a>
                                    </th>
                                    <th>{{ @trans('transaction.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in items">
                                    <td>@{{ index + from }}</td>
                                    <td>@{{ item.trans_id }}</td>                                    
                                    <td>@{{ item.ara_receipt }}</td>
                                    
                                    <td v-if="item.trans_type == '10' ">TZS@{{ item.mwallet_trans_amount}}</td>
                                    <td v-else-if="item.trans_type != '7' ">TZS@{{ item.amount}}</td>
                                    <td v-else>TZS@{{ item.stash_amount }}</td>
                                        
                                    <td v-if="item.trans_type == '1'">Add Money</td>
                                    <td v-else-if="item.trans_type == '2'">Currency Transfer</td>
                                    <td v-else-if="item.trans_type == '3'">Pay Bills</td>
                                    <td v-else-if="item.trans_type == '4'">Send money</td>
                                    <td v-else-if="item.trans_type == '5'">Qwikcash</td>
                                    <td v-else-if="item.trans_type == '6'">Mastercard QR</td>
                                    <td v-else-if="item.trans_type == '7'">Stash</td>
                                    <td v-else-if="item.trans_type == '9'">Card</td>
                                    <td v-else-if="item.trans_type == '10'">Mobile money</td>
                                    <td v-else>Pay Bills</td>
                                    
                                    <td>@{{ item.trans_datetime }}</td>
                                    <td v-if="item.trans_status=='1'"><span class="em-badge green">Success</span></td>
                                    <td v-else-if="item.trans_status=='2'"><span class="em-badge orange">Pending</span></td>
                                    <td v-else><span class="em-badge red">Failed</span></td>
                                    <td class="act-btn">                                       
                                        <a :href="'{{url(Session::get('panel').'/transactions/show')}}/'+ item.id" title="View" class="btn btn-view">
                                        <i class="material-icons">remove_red_eye</i>
                                        </a>                                        
                                        <!-- <a :href="'{{url(Session::get('panel').'/users/edit')}}/'+ item.id" title="Edit"
                                            class="btn btn-edit"><i class="material-icons">edit</i>
                                        </a> -->                                        
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row" v-if="!items.length && loaded == true">
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        {{ @trans('user.no_records_to_show') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
@push('pageModals')

@include('admin.components.modal_delete_js')

<div class="modal right filter-modal" id="filter" tabindex="-1" role="dialog" aria-labelledby="filter">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="filter">{{ @trans('user.filter') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">

                        <div class="col-md-12">
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="submit" class="btn btn-em" @click="filter">{{ @trans('user.update_filter') }}</button>
                <button type="button" class="btn btn-cncl"
                    @click="resetAllFilter">{{ @trans('user.reset_all') }}</button>
            </div>
        </div>
    </div>
</div>
@endpush


@push('pageJs')
<script type="text/javascript">
    function initListUsers()
    {
        $(".table-data .table-responsive").freezeTable({
            'columnNum': 1,
            'scrollable': true,
        });
        // JS for highlight column and row on Table hover start
        $(".table-data table td").hover(function() {
            $(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).addClass('highlight');
        },
        function() {
            $(this).parents('table').find('td:nth-child(' + ($(this).index() + 1) + ')').add($(this).parent()).removeClass('highlight');
        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.transactions.index_json',['panel' => Session::get('panel')])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/transactions.js') }}"></script>
@endpush