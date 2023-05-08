@extends('admin.layouts.main')

@section('title') Messages @endsection

@section('content') 
<!-- Main Content-->
<div class="main-content side-content pt-0">

    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-group_name tx-24 mg-b-5">Messages</h2>

                </div>
                <!--                <div class="d-flex">
                                    <div class="justify-content-center">
                                        
                                        <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.messages.create',['panel' => Session::get('panel')]) }}">
                                            <i class="fe fe-plus mr-2"></i>{{ @trans('tripFare.add') }} Messages</a>
                                    </div>
                                </div>-->
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
                                        <input type="text" class="form-control" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
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
                                                        <th><b>Sr.No</b><a href="javascript:void(0)"
                                                                           :class="classSort('id')" @click="sort('id')"></a></th>
                                                        <th><b>{{ @trans('tripFare.action') }}</b></th>
                                                        <th><b>Messages</b><a href="javascript:void(0)"
                                                                              :class="classSort('message')" @click="sort('message')"></a>
                                                        </th>


                                                        <th><b>Phone</b><a href="javascript:void(0)"
                                                                           :class="classSort('phone')" @click="sort('phone')"></a>
                                                        </th>
                                                        <th><b>Location</b><a href="javascript:void(0)"
                                                                              :class="classSort('city1')" @click="sort('city1')"></a>
                                                        </th>
                                                        <th><b>Prices</b><a href="javascript:void(0)"
                                                                            :class="classSort('price')" @click="sort('price')"></a>
                                                        </th>
                                                        <th><b>Group Name</b><a href="javascript:void(0)"
                                                                                :class="classSort('group_name')" @click="sort('group_name')"></a></th>
                                                        <th><b>Group Id</b><a href="javascript:void(0)"
                                                                              :class="classSort('group_ID')" @click="sort('group_ID')"></a>
                                                        </th> 

                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td class="act-btn">
                                                            <a href="javascript:void(0)" class="btn btn-primary btn-sm add_trip" 
                                                               :data-id="item.id" 
                                                               style="background-color: #e05a49;border-color: #e05a49">Send</a>                 
                                                        </td> 
                                                        <td>
                                                            <span style="white-space: pre;">@{{ item.message }}</span>
                                                        </td> 
                                                        <td>@{{ item.phone }}</td>
                                                        <td>
                                                            @{{ item.city1 }} <br/>
                                                            <hr/>@{{ item.city2 }}
                                                        </td>
                                                        <td>@{{ item.price }}</td>
                                                        <td>@{{ item.group_name }}</td>
                                                        <td>@{{ item.group_ID }}</td>

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
                <h4 class="modal-group_name" id="filter">{{ @trans('user.filter') }}</h4>
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
    var user_indexUrlJson = "{{route('admin.messages.index_json',['panel' => Session::get('panel')])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/users/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.messages.destroy',['panel' => Session::get('panel')])}}";
    @if (Session::has('message'))
            Snackbar.show({
            pos: 'bottom-right',
                    text: "{!! session('message') !!}",
                    actionText: 'Okay'
            });
    @endif

            $('body').on('click', '.add_trip', function(){
    id = $(this).attr('data-id');
    });</script>

@if(env('APP_ENV') == "local")
<script>
 
  /*  setInterval(function() {
    $(document).ready(function()
    {
//        window.location.reload();
    function initListUsers()
    {
    $(".table-data .table-responsive").freezeTable({
    'columnNum': 0,
            
    });
    }
    var user_indexUrlJson = "{{route('admin.messages.index_json',['panel' => Session::get('panel')])}}";
    $.getScript("{{ asset('js/admin/messages.js') }}");
    });
    }, 5000);*/

</script>
@endif
<script type="text/javascript" src="{{ asset('js/admin/messages.js') }}"></script>
@endpush