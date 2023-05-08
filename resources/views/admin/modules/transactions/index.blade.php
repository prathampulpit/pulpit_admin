@extends('admin.layouts.main')

@section('title') Transactions @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
    
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('transactions.transactions') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                        <!-- <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                            <i class="fe fe-download mr-2"></i> Import
                            </button>
                        <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                            <i class="fe fe-filter mr-2"></i> Filter
                            </button> -->
                        <!-- <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.transactions.create',['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('transactions.add_transactions') }} </a> -->
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <!-- <div>
                                <div class="table-top-panel d-flex align-items-center">
                                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto">
                                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('user.register_by') }}</label>
                                        <select class="form-control" id="is_ara_lite" v-model="is_ara_lite" @input="registerBy">
                                            <option value="all" selected>All</option>
                                            <option value="0">Ara app </option>
                                            <option value="1">Ara lite</option>                          
                                        </select>
                                    </div>
                                </div>
                            </div> -->

                            <style>
                                .daterangpicker-vuejs {
                                    width: 309px !important;
                                    position: fixed !important;
                                }
                            </style>
                            <div class="row">
                                <div class="col-lg-3 daterangpicker-vuejs">
                                    <date-picker v-model="start_date" :lang="picker12Lang" valueType="format" placeholder="0000-00-00" format="YYYY-MM-DD"></date-picker>
                                </div>
                                
                                <!-- <div class="col-lg-2">
                                    <select class="form-control" id="trans_types" v-model="trans_types" style="margin-left: -28px;">
                                        <option value="all" selected>All</option>
                                        <?php foreach($trans_types as $r){?>
                                        <option value="<?= $r['id']; ?>"><?= $r['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div> -->

                                <div class="col-lg-1">
                                    <div class="table-top-panel d-flex align-items-center">
                                        <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                            <button type="button" class="btn ripple btn-primary" @click="master_search"><i class="ti-search" data-toggle="tooltip" title="" data-original-title="ti-search"></i></button>
                                            <i class="material-icons" style="cursor: pointer;" v-if="start_date" @click="clearSearch">close</i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                &nbsp;
                            </div>


                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('usertype.record_per_page') }}&nbsp;</label>
                                        <select class="form-control" id="per_page" v-model="per_page" @input="perPage">
                                            <option value="25">25 </option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                            <option value="all">All</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="form-group search-input float-right mb-3">
                                        <!-- <img src="{{asset('img/admin/ic_search.png')}}" srcset="{{asset('img/admin/ic_search@2x.png 2x')}}" alt="search"> -->
                                        <input type="text" class="form-control" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
                                        <!-- <i class="material-icons" v-if="search_text" @click="clearSearch">close</i> -->
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">                                
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example" class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>{{ @trans('transactions.trans_id') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('payment_txn_id')" @click="sort('payment_txn_id')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('transactions.username') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('first_name')" @click="sort('first_name')"></a>
                                                        </th>
                                                        <th><b>Plan {{ @trans('transactions.name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('name')" @click="sort('name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('transactions.amount') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('price')" @click="sort('price')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('transactions.start_datetime') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('start_datetime')" @click="sort('start_datetime')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('transactions.end_datetime') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('end_datetime')" @click="sort('end_datetime')"></a>
                                                        </th>
                                                        <!-- <th><b>{{ @trans('transactions.action') }}</b></th> -->
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td>@{{ item.payment_txn_id }}</td>
                                                        <td>@{{ item.first_name }} @{{ item.last_name }}</td>
                                                        <td>@{{ item.name }}</td>
                                                        <td>₹@{{ item.price }}</td>
                                                        <td v-if="item.start_datetime != '-0001-11-30 00:00:00'">@{{ formatDate(item.start_datetime) }}</td>
                                                        <td v-else>-</td>
                                                        <td v-if="item.end_datetime != '-0001-11-30 00:00:00'">@{{ formatDate(item.end_datetime) }}</td>
                                                        <td v-else>-</td>
                                                        <!-- <td class="act-btn">                                
                                                            <a href="javascript:void(0)" title="Edit" class="btn-edit btn-sm ripple btn-primary">Approve</a>       
                                                            <a href="javascript:void(0)" @click="confirm2(item.id)" title="Delete" class="btn-edit btn-secondary btn-sm">Reject</a>
                                                        </td> -->
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="pagination d-flex" v-if="items.length">
                                        @include('admin.common.paginate_js')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->

            <div class="row" v-if="!items.length && loaded == true">
                <div class="col-md-12">
                    <div class="alert alert-warning" role="alert">
                        {{ @trans('user.no_records_to_show') }}
                    </div>
                </div>
            </div>
                            
        </div>
    </div>
</div>
<!-- End Main Content-->

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
        var container = $('div'),
            scrollTo = $('#example');

        container.animate({
            scrollTop: container.scrollTop()
        });
        
        $(".table-data .table-responsive").freezeTable({
            'columnNum': 0,
            /* 'scrollable': true, */
        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.transactions.index_json',['panel' => Session::get('panel')])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/users/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.transactions.destroy',['panel' => Session::get('panel')])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/transactions.js') }}"></script>
<script type="text/javascript" src="https://unpkg.com/vue2-datepicker@2.6.4/lib/index.js"></script>
@endpush