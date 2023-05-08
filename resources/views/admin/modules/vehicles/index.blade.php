@extends('admin.layouts.main')

@section('title') Vehicles @endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">
    
    <loading :active.sync="isLoading"></loading>

    <div class="container-fluid">
        <div class="inner-body">

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">{{ @trans('vehicles.vehicles') }}</h2>
                    <!-- <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">Maps & Tables</a></li>
                        <li class="breadcrumb-item active" aria-current="page">DataTable</li>
                    </ol> -->
                </div>
                <div class="d-flex">
                    <div class="justify-content-center">
                        <!-- <a class="btn btn-primary my-2 btn-icon-text" href="{{ route('admin.vehicles.create',['panel' => Session::get('panel')]) }}">
                            <i class="fe fe-plus mr-2"></i> {{ @trans('vehicles.add_vehicles') }} </a> -->
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
                                <div class="col-lg-2">
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
                                <div class="col-lg-2">
                                    <select class="form-control" v-model="state_id" id="state_id" name="state_id" style="margin-left: -28px;"
                                        onchange="getCity()">
                                        <option value="" selected>PAN India</option>
                                        <?php foreach ($states as $r) { ?>
                                        <option value="<?= $r['name']; ?>" @if($state_id == $r['name']) selected @endif>
                                            <?= $r['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                        
                                <div class="col-lg-2 city_lists">
                                    <select class="form-control" v-model="city_id" id="city_id" name="city_id" style="margin-left: -28px;">
                                        <option value="">Select City</option>
                                        <?php foreach ($cities as $r) { ?>
                                        <option value="<?= $r['name']; ?>"  @if($city_id == $r['name']) selected @endif><?= $r['name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                
                                 <div class="col-lg-2">
                                    <select class="form-control"  v-model="vehicle_type" id="vehicle_type_id" name="vehicle_type_id" style="margin-left: -28px;" onchange="getPlans1()">
                                        <option value="" selected>Select Vehicle Types</option>
                                        <?php foreach($vehicle_types as $r){?>
                                        <option value="<?= $r['id']; ?>" @if($vehicle_type_id == $r['id']) selected @endif><?= $r['name']; ?></option>
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

                                <div class="col-lg-3">
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
                                                        <th><b>{{ @trans('vehicles.username') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('fist_name')" @click="sort('fist_name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('vehicles.vehicle_number') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('vehicle_number')" @click="sort('vehicle_number')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('vehicles.brand_name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('brand_id')" @click="sort('brand_id')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('vehicles.model_name') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('name')" @click="sort('name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('vehicles.registration_year') }}</b><a href="javascript:void(0)"
                                                                :class="classSort('name')" @click="sort('name')"></a>
                                                        </th>
                                                        <th><b>{{ @trans('vehicles.action') }}</b></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr v-for="(item,index) in items">
                                                        <td>@{{ index + from }}</td>
                                                        <td><a :href="'{{url(Session::get('panel').'/vehicles/show')}}/'+ item.id">@{{ item.first_name }} @{{ item.last_name }}</a></td>
                                                        <td><a :href="'{{url(Session::get('panel').'/vehicles/show')}}/'+ item.id">@{{ allcharcapitalize(item.vehicle_number) }}</a></td>
                                                        <td>@{{ item.brand_name }}</td>
                                                        <td>@{{ item.model_name }}</td>
                                                        <td>@{{ item.registration_year }}</td>
                                                        <td class="act-btn">                                
                                                            <a :href="'{{url(Session::get('panel').'/vehicles/show')}}/'+ item.id" title="View"
                                                                class="btn-view btn-sm">
                                                                <i class="material-icons">remove_red_eye</i>
                                                            </a>                                        
                                                            <a :href="'{{url(Session::get('panel').'/vehicles/edit')}}/'+ item.id" title="Edit"
                                                                class="btn-edit btn-sm"><i class="material-icons">edit</i>
                                                            </a>       
                                                             <!-- <a href="javascript:void(0)" @click="confirm(item.id)" title="Delete"
                                                                class="btn-edit btn-sm"><i class="material-icons">delete</i>
                                                            </a> -->                 
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
        var container = $('div'),
            scrollTo = $('#example');

        container.animate({
            scrollTop: scrollTo.offset().top - container.offset().top + container.scrollTop()
        });
        
        $(".table-data .table-responsive").freezeTable({
            'columnNum': 0,
            /* 'scrollable': true, */
        });
    }
    function getCity() {
        var state = $("#state_id").find(":selected").text();
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type: 'GET',
            url: "{{ route('admin.trip.stateCode', ['panel' => Session::get('panel')]) }}",
            data: {
                'state': state,

            },
            dataType: 'json',
            success: function(response) {
                console.log(response);
                var partyNameArrays_e = [];
                var partyNameArray = [];
                var partyNameArrayBefore = [];
                let optionLists;
                partyNameArrays_e = {
                    'text': "Select Option",
                    'value': "0"
                };
                $('#city_id').empty();
                partyNameArray.push(partyNameArrays_e);
                response.city_name.forEach(element => {
                    partyNameArrayBefore = {
                        'text': element.name,
                        'value': element.name
                    };
                    partyNameArray.push(partyNameArrayBefore);
                });
                optionLists = document.getElementById('city_id').options;
                length_e = optionLists.length;
                partyNameArray.forEach(option => {
                    optionLists.add(
                        new Option(option.text, option.value, option
                            .selected)
                    )
                });
            }

        });
    }
</script>
<script>
    var user_indexUrlJson = "{{route('admin.vehicles.index_json',['panel' => Session::get('panel'),'param'=> $param])}}";
    var toggle_status_url = "{{url(Session::get('panel').'/users/toggle-status')}}";
    var user_deleteUrl = "{{route('admin.vehicles.destroy',['panel' => Session::get('panel')])}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/vehicles.js') }}"></script>
@endpush