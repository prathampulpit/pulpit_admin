@extends('admin.layouts.main')

@section('title') Admin @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
    
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('admin.admin') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                        <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.admin.create',['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('admin.add_user') }} </a>
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
                            <!-- <div class="row">
                                <div class="col-lg-2">
                                    <select class="form-control mySelect2" id="trip_type" v-model="trip_type">
                                        <option value="all" selected>All</option>
                                        <option value="Local Trip">Local Trip</option>
                                        <option value="Rental Trip">Rental Trip</option>
                                        <option value="Outstation Trip">Outstation Trip</option>
                                    </select>
                                </div>

                                <div class="col-lg-1">
                                    <div class="table-top-panel d-flex align-items-center">
                                        <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center">
                                            <button type="button" class="btn ripple btn-primary" @click="master_search"><i class="ti-search" data-toggle="tooltip" title="" data-original-title="ti-search"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            
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
                                                        <th><b>{{ @trans('admin.first_name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('first_name')" @click="sort('first_name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('admin.last_name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('last_name')" @click="sort('last_name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('admin.email') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('email')" @click="sort('email')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('admin.mobile_number') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('mobile_number')" @click="sort('mobile_number')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('admin.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td>@{{ item.first_name }}</td>
                                                        <td>@{{ item.last_name }}</td>
                                                        <td>@{{ item.emailid }}</td>
                                                        <td>@{{ item.mobile_number }}</td>
                                                        <td class="act-btn">          
                                                            <a :href="'{{url(Session::get('panel').'/admin/show')}}/'+ item.id" title="View"
                                                                class="btn-view btn-sm">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>

                                                            <a :href="'{{url(Session::get('panel').'/admin/edit')}}/'+ item.id" title="Edit"
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
    var user_indexUrlJson = "{{route('admin.admin.index_json',['panel' => Session::get('panel')])}}";
    var user_deleteUrl = "{{route('admin.admin.destroy',['panel' => Session::get('panel')])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/admin.js') }}"></script>
@endpush