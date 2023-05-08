@extends('admin.layouts.main')

@section('title') Partner @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
    
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('user.partner') }} {{ @trans('user.users') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                    <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.partner.create',['panel' => Session::get('panel')]) }}"><i class="fe fe-plus mr-2"></i> {{ @trans('user.add_user') }} </a>
                    </div>
                </div>
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12">
                    <div class="card custom-card">
                        <div class="card-body">
                            <style>
                                .daterangpicker-vuejs {
                                    width: 309px !important;
                                    position: fixed !important;
                                }
                            </style>
                            <div class="row">
                                <div class="col-lg-3 daterangpicker-vuejs">
                                    <date-picker v-model="start_date" :lang="picker12Lang" valueType="format" placeholder="Select Date" format="YYYY-MM-DD"></date-picker>
                                </div>
                                
                                <div class="col-lg-2">
                                    <select class="form-control" id="user_type" v-model="user_type" style="margin-left: -28px;">
                                        <option value="all" selected>All</option>
                                        <?php foreach($user_type as $r){?>
                                        <option value="<?= $r['id']; ?>"><?= $r['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>

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
                                                        <th><b>{{ @trans('user.name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('name')" @click="sort('name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('user.phone') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('mobile_number')" @click="sort('mobile_number')"></a></th>
                                                        <!-- <th><b>{{ @trans('user.email') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('emailid')" @click="sort('emailid')"></a></th> -->
                                                        <th><b>User Type</b><a href="javascript:void(0)"
                                                                :class="classSort('user_type')" @click="sort('user_type')"></a></th>
                                                        <!-- <th><b>OTP Status</b><a href="javascript:void(0)"
                                                                :class="classSort('is_otp')" @click="sort('is_otp')"></a></th>
                                                                
                                                        <th><b>{{ @trans('user.status') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('user_status')" @click="sort('user_status')"></a></th> -->
                                                        <th><b>{{ @trans('user.created_at') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('created_at')" @click="sort('created_at')"></a></th>
                                                        <th><b>{{ @trans('user.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items"><!-- danger -->
                                                        <td v-if="(item.agent_logo_status =='1' || item.agent_logo_status ==null) && (item.pan_card_url_status=='1' || item.pan_card_url_status==null) && (item.adhar_card_url_status=='1' || item.adhar_card_url_status==null) && (item.registration_document_url_status=='1' || item.registration_document_url_status==null) && (item.bank_document_url_status=='1' || item.bank_document_url_status==null) && (item.dl_front_url_status=='1' || item.dl_front_url_status==null) && (item.dl_back_url_status=='1' || item.dl_back_url_status==null) && (item.police_verification_url_status=='1' || item.police_verification_url_status==null) && (item.d_pan_card_url_status=='1' || item.d_pan_card_url_status==null) && (item.d_adhar_card_url_status=='1' || item.d_adhar_card_url_status==null) && (item.is_otp=='1' || item.is_otp=='0')">
                                                            <span class="badge bg-success">@{{ index + from }}</span>
                                                        </td>
                                                        <td v-else>
                                                            <span class="badge bg-danger">@{{ index + from }}</span>
                                                        </td>

                                                        <td>@{{ item.first_name }} @{{item.last_name }}</td>
                                                        <td>@{{ item.mobile_number }}</td>
                                                        
                                                        <td>@{{ item.user_type_name }}</td>
                                                        <td v-if="item.created_at!='-0001-11-30 00:00:00'">@{{ item.created_at }}</td>
                                                        <td v-else>-</td>

                                                        <!-- <td v-if="item.is_otp=='1' || item.is_otp=='0' ">
                                                            <span class="badge bg-pill bg-primary-light">Verified</span>
                                                        </td>
                                                        <td v-else>
                                                            
                                                            <a :href="'{{url(Session::get('panel').'/users/resendotp')}}/'+ item.id" class="btn ripple btn-danger btn-sm">Not Verify</a>
                                                        </td>

                                                        <td v-if="item.user_status=='1'">
                                                            <button type="button" class="btn ripple btn-success btn-sm">Active</button>
                                                        </td>
                                                        <td v-else>
                                                            <button type="button" class="btn ripple btn-danger btn-sm">Inactive</button>
                                                        </td> -->
                                                        <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/partner/show')}}/'+ item.id" title="View"
                                                                class="btn-sm btn-view">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>                                        
                                                            <a :href="'{{url(Session::get('panel').'/partner/edit')}}/'+ item.id" title="Edit"
                                                                class="btn btn-edit"><i class="material-icons">edit</i>
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
            'scrollable': true,
        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.partner.index_json',['panel' => Session::get('panel')])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/partner/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.partner.destroy',['panel' => Session::get('panel'), 'id' => 1])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/partner.js') }}"></script>
<script type="text/javascript" src="https://unpkg.com/vue2-datepicker@2.6.4/lib/index.js"></script>
@endpush