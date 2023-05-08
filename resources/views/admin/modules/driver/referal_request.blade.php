@extends('admin.layouts.main')

@section('title') Customers @endsection

@section('content')

<main>

    <loading :active.sync="isLoading"></loading>

    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec" v-cloak>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('user.referal_request') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('user.list') }}</li>
                </ol>
            </nav>

            <div class="top-bar d-flex align-items-center">
                <h2>{{ @trans('user.referal_request') }}</h2>

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
                        <img src="{{asset('img/admin/ic_search.png')}}" srcset="../../../img/admin/ic_search@2x.png 2x" alt="search">
                        <input type="text" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
                        <i class="material-icons" v-if="search_text" @click="clearSearch">close</i>
                    </div>

                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto">
                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('user.register_by') }}</label>
                        <select class="form-control" id="is_ara_lite" v-model="is_ara_lite" @input="registerBy">
                            <option value="all" selected>All</option>
                            <option value="0">Ara app </option>
                            <option value="1">Ara lite</option>                          
                        </select>
                    </div>

                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto" v-if="items.length">
                        <label style="white-space: nowrap;" class="mb-0 gray">{{ @trans('user.record_per_page') }}</label>
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
                                    <th>{{ @trans('user.name') }}<a href="javascript:void(0)"
                                            :class="classSort('name')" @click="sort('name')"></a>
                                    </th>
                                    
                                    <th>{{ @trans('user.phone') }}<a href="javascript:void(0)"
                                            :class="classSort('mobile_number')" @click="sort('mobile_number')"></a></th>
                                    <th>{{ @trans('user.referal_request') }}<a href="javascript:void(0)" :class="classSort('referal_register_type')" @click="sort('referal_register_type')"></a></th>
                                    <th>{{ @trans('user.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in items">
                                    <td>@{{ index + from }}</td>
                                    
                                    <td>@{{ item.name }}</td>
                                    
                                    <td>•••• @{{ item.mask_mobile_number }}</td>
                                    
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" @click="toggleReferalStatus(item.id)" :checked=item.referal_register_type>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="act-btn">                                
                                        <a :href="'{{url(Session::get('panel').'/users/show')}}/'+ item.id" title="View"
                                            class="btn btn-view">
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
            'columnNum': 0,
            'scrollable': false,
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
    var user_indexUrlJson = "{{route('admin.users.referal_request_json',['panel' => Session::get('panel')])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/users/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.users.destroy',['panel' => Session::get('panel'), 'id' => 1])}}";
    var toggle_referal_status_url = "{{url(Session::get('panel').'/users/toggle-referal-status')}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/referal_request.js') }}"></script>
@endpush