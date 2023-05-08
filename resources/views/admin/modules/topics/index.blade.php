@extends('admin.layouts.main')

@section('title') Topics @endsection

@section('content')

<main>

    <loading :active.sync="isLoading"></loading>

    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec" v-cloak>

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('topics.topics') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('app.list') }}</li>
                </ol>
            </nav>

            <div class="top-bar d-flex align-items-center">
                <h2>{{ @trans('topics.manage_topics') }}</h2>

                <div class="right-table-container ml-auto">
                    <a href="{{ route('admin.topics.create',['panel' => Session::get('panel')]) }}" class="btn btn-em btn-primary-em">{{ @trans('topics.add_topics') }}</a>
                </div>
            </div>

            <div class="table-wrapper">
                <div class="table-top-panel d-flex align-items-center">
                    <div class="form-group search-input float-left mb-0">
                        <img src="{{asset('img/admin/ic_search.png')}}" srcset="{{asset('img/admin/ic_search@2x.png 2x')}}" alt="search">
                        <input type="text" name="search_text" id="search_text" v-model="search_text" @input="search" placeholder="Search">
                        <i class="material-icons" v-if="search_text" @click="clearSearch">close</i>
                    </div>
                    <div class="form-group select btm-arrow mb-0 d-flex without-label align-items-center mx-auto"
                        v-if="items.length">
                        <label style="white-space: nowrap;"
                            class="mb-0 gray">{{ @trans('app.record_per_page') }}</label>
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
                                    <th>{{ @trans('topics.type') }}<a href="javascript:void(0)"
                                            :class="classSort('type')" @click="sort('type')"></a>
                                    </th>
                                    <th>{{ @trans('topics.name_en') }}<a href="javascript:void(0)"
                                            :class="classSort('name_en')" @click="sort('name_en')"></a>
                                    </th>
                                    <th>{{ @trans('topics.name_sw') }}<a href="javascript:void(0)"
                                            :class="classSort('name_sw')" @click="sort('name_sw')"></a>
                                    </th>
                                    <th>{{ @trans('topics.status') }}</th>
                                    <th>{{ @trans('app.action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item,index) in items">
                                    <td>@{{ index + from }}</td>
                                    <td>@{{ item.type }}</td>
                                    <td>@{{ item.name_en }}</td>
                                    <td>@{{ item.name_sw }}</td>
                                    <td>
                                        <label class="switch">
                                            <input type="checkbox" @click="toggleStatus(item.id)" :checked=item.status>
                                            <span class="slider round"></span>
                                        </label>
                                    </td>
                                    <td class="act-btn">
                                        <a :href="'{{url(Session::get('panel').'/topics/edit')}}/'+ item.id" title="Edit"
                                            class="btn btn-edit"><i class="material-icons">edit</i>
                                        </a>
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
                        {{ @trans('app.no_records_to_show') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

@endsection
@push('pageModals')

@include('admin.components.modal_delete_js')

@endpush


@push('pageJs')
<script type="text/javascript">
    function initList()
    {
        $(".table-data .table-responsive").freezeTable({
            'columnNum': 0,
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
    var indexUrlJson = "{{route('admin.topics.index_json',['panel' => Session::get('panel')])}}";
    var deleteUrl = "{{ url(Session::get('panel').'/topics/destroy') }}";
    var toggle_status_url = "{{url(Session::get('panel').'/topics/toggle-status')}}";
    @if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
    @endif
</script>
<script type="text/javascript" src="{{ asset('js/admin/topics.js') }}"></script>
@endpush