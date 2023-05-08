@extends('admin.layouts.main')

@section('title') Ad Offers @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
    
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Ad {{ @trans('offers.offers') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                        <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.offers.create',['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('offers.add_offers') }} </a>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
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
                                                        <th><b>{{ @trans('offers.offer_url') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('offer_url')" @click="sort('offer_url')"></a>
                                                        <th><b>{{ @trans('offers.offer_type') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('offer_type')" @click="sort('offer_type')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('offers.offer_for') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('offer_for')" @click="sort('offer_for')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('offers.status') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('status')" @click="sort('status')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('offers.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td><img v-bind:src="item.offer_url" width='50'/></td>
                                                        <td v-if="item.offer_type == '0'">Home</td>
                                                        <td v-else-if="item.offer_type == '1'">Trip</td>
                                                        <td v-else>Cab</td>
                                                        <td v-if="item.offer_for == '0'">Customers</td>
                                                        <td v-else-if="item.offer_for == '1'">Partners</td>
                                                        <td>
                                                            <label class="switch">
                                                                <input type="checkbox" @click="toggleStatus(item.id)" :checked=item.status>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </td>
                                                        <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/offers/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-edit btn-sm"><i class="material-icons">edit</i>
                                                            </a>       
                                                            <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-edit btn-sm"><i class="material-icons">delete</i>
                                                            </a>                 
                                                        </td>
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
        $(".table-data .table-responsive").freezeTable({
            'columnNum': 0,
            /* 'scrollable': true, */
        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.offers.index_json',['panel' => Session::get('panel')])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/offers/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.offers.destroy',['panel' => Session::get('panel')])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/offers.js') }}"></script>
@endpush