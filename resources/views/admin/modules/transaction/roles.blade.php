@extends('admin.layouts.main')

@section('title') Roles @endsection

@section('content')

<main>
    <div class="two-col">

        @include("admin.common.sidebar")

        <div class="right-sec">

            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">{{ @trans('user.users') }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ @trans('user.role') }}</li>
                </ol>
            </nav>

            <div class="top-bar">
                <h2>{{ @trans('user.role') }}</h2>
                <a href="javascript:void(0)" class="btn btn-em float-right btn-sm"
                    @click="add">{{ @trans('user.add_role') }}</a>
            </div>

            <div class="table-data">
                <div class="table-responsive fixed-col-table">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ @trans('user.feature_access') }}</th>
                                @foreach($roles as $role)
                                <th>
                                    <span data-toggle="tooltip" title="{{ $role->description }}">
                                        {{ $role->display_name }}
                                    </span>
                                    <a href="javascript:void(0)">
                                        <i class="material-icons small-font align-top"
                                            @click="edit('{{ $role->id }}','{{ $role->display_name }}','{{ $role->description }}')">edit</i>
                                    </a>
                                </th>

                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)

                            @if ($module['id'] == 'role')
                            @continue
                            @endif

                            <tr>
                                <td>{{ $module['display']}}</td>

                                @foreach($roles as $role)
                                <td>

                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="{{ $module['id'] }}.{{ $role->name }}.read"
                                                @click="assignPermissionToRole"
                                                @if($role->permissions->contains('name',$module['id'] .'-read'))
                                            checked="checked"
                                            @endif>
                                            <label class="custom-control-label"
                                                for="{{ $module['id'] }}.{{ $role->name }}.read">{{ @trans('user.read') }}</label>
                                        </div>
                                    </div>

                                    @if(
                                    $module['id'] != 'candidate-senior' &&
                                    $module['id'] != 'candidate-impersonate'
                                    )
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="{{ $module['id'] }}.{{ $role->name }}.write"
                                                @click="assignPermissionToRole"
                                                @if($role->permissions->contains('name',$module['id'].'-write'))
                                            checked="checked"
                                            @endif>
                                            <label class="custom-control-label"
                                                for="{{ $module['id'] }}.{{ $role->name }}.write">{{ @trans('user.write') }}</label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input"
                                                id="{{ $module['id'] }}.{{ $role->name }}.delete"
                                                @click="assignPermissionToRole"
                                                @if($role->permissions->contains('name',$module['id'].'-delete'))
                                            checked="checked"
                                            @endif>
                                            <label class="custom-control-label"
                                                for="{{ $module['id'] }}.{{ $role->name }}.delete">{{ @trans('user.delete') }}</label>
                                        </div>
                                    </div>
                                    @endif

                                </td>
                                @endforeach
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>
@endsection

@push('pageModals')

<div class="modal" tabindex="-1" role="dialog" id="modalAddRole">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">

            <form @submit.prevent="store">

                <div class="modal-header">
                    <h5 class="modal-title">{{ @trans('user.add_role') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="container-fluid">

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">{{ @trans('user.role_name') }}<span
                                            class="required">*</span></label>
                                    <input type="text" class="form-control" id="display_name" name="display_name"
                                        placeholder="Enter Name" v-model="display_name">
                                    <span class="invalid-feedback"
                                        v-if="errors.display_name">@{{ errors.display_name[0] }}</span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="name">{{ @trans('user.short_description') }}</label>
                                    <textarea class="form-control" name="description" id="description"
                                        v-model="description"></textarea>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" id="id" name="id" />

                    </div>
                </div>

                <div class="modal-footer">
                    <div class="tab-btn">
                        <div style="float: left;">
                            <button type="button" class="btn btn-cncl"
                                data-dismiss="modal">{{ @trans('user.cancel') }}</button>
                        </div>
                        <div style="float:right;">
                            {{--  <button v-if="id" type="button" class="btn btn-danger"
                                @click="confirm">{{ @trans('user.delete') }}</button>  --}}
                            <button type="submit" class="btn btn-em">{{ @trans('user.save') }}</button>
                        </div>
                    </div>

                </div>

            </form>

        </div>
    </div>
</div>

@include('admin.components.modal_delete_js')

@endpush


@push('pageJs')
<script type="text/javascript">
    var saveRolePermissions = "{{ route('admin.role_permissions.save',['panel' => Session::get('panel') ]) }}";
    var storeRoleUrl = "{{ route('admin.roles.store',['panel' => Session::get('panel') ]) }}";
    var deleteUrl = "{{ route('admin.roles.destroy',['panel' => Session::get('panel'),'id' => null]) }}";
</script>

<script type="text/javascript" src="{{ asset('js/admin/role.js') }}"></script>
@endpush