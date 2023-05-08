@extends('admin.layouts.main')

@section('title') Polygon Records @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Polygon Records</h2>
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
                        <a class="btn btn-primary my-2 btn-icon-text"
                            href="{{ route('admin.polygonRecords.create',['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('polygonRecords.add_polygonRecords') }} </a>
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

                            <div class="row">
                                <div class="col-lg-6">
                                    <div
                                        class="float-left form-group select btm-arrow mb-3 d-flex without-label align-items-center">
                                        <label style="white-space: nowrap;"
                                            class="mb-0 gray">{{ @trans('usertype.record_per_page') }}&nbsp;</label>
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
                                        <input type="text" class="form-control" name="search_text" id="search_text"
                                            v-model="search_text" @input="search" placeholder="Search">
                                        <!-- <i class="material-icons" v-if="search_text" @click="clearSearch">close</i> -->
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="table-responsive table-data" v-if="items.length">
                                    <div class="col-sm-12">
                                        <div class="table-checkable">
                                            <table id="example"
                                                class="table table-striped table-bordered text-nowrap dataTables_wrapper dt-bootstrap4 no-footer">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th><b>Area Name</b><a href="javascript:void(0)"
                                                                :class="classSort('area_name')"
                                                                @click="sort('area_name')"></a>
                                                        </th>
                                                        <th><b>City Name</b><a href="javascript:void(0)"
                                                                :class="classSort('city_name')"
                                                                @click="sort('city_id')"></a>
                                                        </th>
                                                        <th><b>Circle Radius</b><a href="javascript:void(0)"
                                                                :class="classSort('circle_radius')"
                                                                @click="sort('circle_radius')"></a>
                                                        </th>
                                                        <th><b>Coordinates</b><a href="javascript:void(0)"
                                                                :class="classSort('coordinates')"
                                                                @click="sort('coordinates')"></a>
                                                        </th>
                                                        <th><b>Service</b><a href="javascript:void(0)"
                                                                :class="classSort('service')"
                                                                @click="sort('service')"></a>
                                                        </th>
                                                        <th><b>Status</b><a href="javascript:void(0)"
                                                                :class="classSort('status')"
                                                                @click="sort('status')"></a>
                                                        </th>
                                                        <th><b>Action</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td>@{{ item.area_name }}</td>
                                                        <td>@{{ item.name }}</td>
                                                        <td>@{{ item.circle_radius }}</td>
                                                        <td>@{{ item.coordinates }}</td>
                                                        <td v-if="item.service=='1'">
                                                            <button type="button"
                                                                class="btn ripple btn-success btn-sm">Yes</button>
                                                        </td>
                                                        <td v-else="item.service=='0'">
                                                            <button type="button"
                                                                class="btn ripple btn-warning btn-sm">No</button>
                                                        </td>
                                                        <td>
                                                            <label class="switch">
                                                                <input type="checkbox" @click="toggleStatus(item.id)"
                                                                    :checked=item.status>
                                                                <span class="slider round"></span>
                                                            </label>
                                                        </td>
                                                        <td class="act-btn">
                                                            <a :href="'{{url(Session::get('panel').'/polygonRecords/edit')}}/'+ item.id"
                                                                title="Edit" class="btn-edit btn-sm"><i
                                                                    class="material-icons">edit</i>
                                                            </a>
                                                            <a href="javascript:void(0)" @click="confirm(item.id)"
                                                                title="Delete" class="btn-edit btn-sm"><i
                                                                    class="material-icons">delete</i>
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
                <h4 class="modal-title" id="filter">Update Tax Amount</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>

            <div class="modal-body">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <form method="post"
                                action="{{ route('admin.polygonRecords.store',['panel' => Session::get('panel')]) }}"
                                class="parsley-style-1" id="selectForm2" name="selectForm2"
                                enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="id" id="id">
                                <div class="row row-sm mg-b-20">
                                    <div class="col-lg-12">
                                        <p class="mg-b-10">Tax Amount: <span class="tx-danger">*</span></p>
                                        <div class="mg-b-10" id="fnWrapper">
                                            <input class="form-control" value="" name="tax_amount"
                                                placeholder="Enter Tax Amount" required="" type="text">
                                        </div>
                                    </div>
                                </div>
                                <div class="mg-t-30">
                                    <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
                                    <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block" id="load2"
                                        data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
                                </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="modal-footer">
                <button type="submit" class="btn btn-primary my-2 btn-icon-text">Save</button>
            </div> -->
            </form>
        </div>
    </div>
</div>

@endpush


@push('pageJs')
<script type="text/javascript">
function initListUsers() {
    $(".table-data .table-responsive").freezeTable({
        'columnNum': 0,
        /* 'scrollable': true, */
    });
}
</script>
<script>
/* function setid(id){
        $('#id').val(id);
    } */
var user_indexUrlJson = "{{route('admin.polygonRecords.index_json',['panel' => Session::get('panel')])}}";
var toggle_status_url = "{{url(Session::get('panel').'/polygonRecords/toggle-status')}}";
var user_deleteUrl = "{{route('admin.polygonRecords.destroy',['panel' => Session::get('panel')])}}";
@if(Session::has('message'))
Snackbar.show({
    pos: 'bottom-right',
    text: "{!! session('message') !!}",
    actionText: 'Okay'
});
@endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/polygonRecords.js') }}"></script>
<!-- Internal Form-validation js-->
<!-- <script src="{{asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script> -->

<!-- Internal Fileuploads js-->
<!-- <script src="{{asset('assets/plugins/fileuploads/js/fileupload.js') }}"></script>
<script src="{{asset('assets/plugins/fileuploads/js/file-upload.js') }}"></script> -->
@endpush