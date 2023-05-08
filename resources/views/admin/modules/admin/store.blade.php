@extends('admin.layouts.main')

@section('title')
    @if ($id)
        Edit Admin
    @else
        Add Admin
    @endif
@endsection

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
                            <li class="breadcrumb-item"><a href="#">Forms</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Form Validation</li>
                        </ol> -->
                    </div>
                    <!-- <div class="d-flex">
                        <div class="justify-content-center">
                            <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                            <i class="fe fe-download mr-2"></i> Import
                            </button>
                            <button type="button" class="btn btn-white btn-icon-text my-2 mr-2">
                            <i class="fe fe-filter mr-2"></i> Filter
                            </button>
                            <button type="button" class="btn btn-primary my-2 btn-icon-text">
                            <i class="fe fe-download-cloud mr-2"></i> Download Report
                            </button>
                        </div>
                    </div> -->
                </div>
                <!-- End Page Header -->

                <!-- Row -->
                <div class="row row-sm justify-content-md-center">
                    <div class="col-xl-8 col-lg-12 col-md-12">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div>
                                    @if ($id)
                                        <h6 class="main-content-label mb-1">{{ @trans('admin.edit_user') }}</h6>
                                    @else
                                        <h6 class="main-content-label mb-1">{{ @trans('admin.add_user') }}</h6>
                                    @endif
                                    <p class="text-muted card-sub-title">&nbsp;</p>
                                </div>
                                <form method="post"
                                    action="{{ route('admin.admin.store', ['panel' => Session::get('panel')]) }}"
                                    class="parsley-style-1" id="selectForm2" name="selectForm2">
                                    @csrf

                                    @if ($id)
                                        <input type="hidden" name="id" id="id" value="{{ $data->id }}">
                                    @endif

                                    <div class="row row-sm mg-b-20">
                                        <div class="col-lg-6">
                                            <p class="mg-b-10">{{ @trans('admin.first_name') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control"
                                                    value="@if (old('first_name')) {{ old('first_name') }}@elseif($id){{ $data->first_name }} @endif"
                                                    name="first_name" placeholder="Enter First Name" required=""
                                                    type="text">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="mg-b-10">{{ @trans('admin.last_name') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control"
                                                    value="@if (old('last_name')) {{ old('last_name') }}@elseif($id){{ $data->last_name }} @endif"
                                                    name="last_name" placeholder="Enter Last Name" required=""
                                                    type="text">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row row-sm mg-b-20">
                                        <div class="col-lg-6">
                                            <p class="mg-b-10">{{ @trans('admin.email') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control"
                                                    value="@if (old('email')) {{ old('email') }}@elseif($id){{ $data->emailid }} @endif"
                                                    name="email" placeholder="Enter Email" required="" type="text">
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <p class="mg-b-10">{{ @trans('admin.mobile_number') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control"
                                                    value="@if (old('mobile_number')){{old('mobile_number')}}@elseif($id){{$data->mobile_number}}@endif"
                                                    id="mobile" name="mobile_number" placeholder="Enter Mobile Number"
                                                    required="" type="text" maxlength="10" minlength="10" onkeypress="phoneno()">
                                            </div>
                                            <div class="mob_err" style="color: red;"></div>
                                        </div>
                                    </div>

                                    <div class="row row-sm mg-b-20">
                                        <div class="col-lg-6">
                                            <p class="mg-b-10">{{ @trans('admin.password') }}: <span
                                                    class="tx-danger">*</span></p>
                                            <div class="mg-b-10" id="fnWrapper">
                                                <input class="form-control"
                                                    value=""
                                                    name="password" placeholder="Enter password" type="password">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-sm mg-b-20">
                                        <div class="col-lg-12">
                                            <p class="mg-b-10">{{ @trans('admin.role') }}</p>

                                            @if ($id)
                                                <?php
                                                $ids = $data->role_id;
                                                $id_arr = explode(',', $ids);
                                                ?>
                                            @endif
                                            @foreach ($roles as $r)
                                                <div class="custom-control" style="padding-left: 0rem;">
                                                    <label class="ckbox"><input type="checkbox" class="box"
                                                            id="role_id{{ $r['id'] }}" name="role_id[]"
                                                            value="{{ $r['id'] }}"
                                                            @if ($id) @if (in_array($r['id'], $id_arr)) checked @endif
                                                            @endif
                                                        required><span>{{ $r['name'] }}</span></label>

                                                </div>
                                            @endforeach
                                            <label class="ckbox"><input type="checkbox" id="role_all"
                                                    name="role_all[]" value="all"
                                                    @if ($id) @if ($data->role_id == '4,5,6,7,8') checked @endif
                                                    @endif
                                                onchange="check_uncheck()"><span>All</span></label>
                                        </div>
                                    </div>

                                    <div class="mg-t-30">
                                        <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block"
                                            value="{{ @trans('user.save') }}" id="save">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Row-->
            </div>
        </div>
    </div>
    <!-- End Main Content-->
@endsection

@push('pageModals')
@endpush

@push('pageJs')
    <script>
        function check_uncheck() {
            if ($("#role_all").prop('checked') == true) {
                $('.box').prop('checked', true);

            } else {
                $('#role_all').prop('checked', false);
                $('.box').prop('checked', false);

            }
        }
    </script>
    <script>
        @if (Session::has('message'))
            Snackbar.show({
                pos: 'bottom-right',
                text: "{!! session('message') !!}",
                actionText: 'Okay'
            });
        @endif

        $(document).ready(function() {
            $('#city_id').select2({
                minimumInputLength: 2,
                ajax: {
                    url: '{{ route('api.cities.search') }}',
                    dataType: 'json',
                },
            });
        });

        function editProfile() {
            $('#editProfile').modal();
        }

        function submitEditProfile() {
            $("#selectForm2").submit();
        }
    </script>

    <!-- Internal Form-validation js-->
    <script src="{{ asset('assets/plugins/parsleyjs/parsley.min.js') }}"></script>
    <script src="{{ asset('assets/js/form-validation.js') }}"></script>

    <script>
        // $('#mobile').on('keyup', function() {
        //     // console.log(8888);
        //     var mob = $('#mobile').val();
        //     // alert(mob);
        //     if (mob) {
        //         var regx = /^[0-9]+$/;
        //         if (regx.test(mob) == false) {
        //             $(".mob_err").html("Please enter only number");
        //             $("#save").attr("disabled", true);
        //         } else if (mob.length < 10 || mob.length > 10) {
        //             $(".mob_err").html("Only 10 character allowed");
        //             $("#save").attr("disabled", true);
        //         } else {
        //             $(".mob_err").html("");
        //             $("#save").attr("disabled", false);
        //         }
        //     } else {
        //         $(".mob_err").html("");
        //         $("#save").attr("disabled", true);
        //     }
        // });
        // $(document).ready(function() {
        //     $('#mobile').keypress(function(e) {
        //         var charCode = (e.which) ? e.which : event.keyCode.
        //         if(String.fromCharCode(charCode).match(/[^0-9]/g))
                
        //         return false;
        //     });
        // });
                
            $('#mobile').keypress(function(e) {
                var a = [];
                var k = e.which;

                for (i = 48; i < 58; i++)
                    a.push(i);

                if (!(a.indexOf(k)>=0))
                    e.preventDefault();


                // if($(this).val().length > 1) {
                //     console.log("done");
                // } else {
                //     console.log("No done");
                // }
                
            });
            function phoneno(){  
                var mobile_length = $('#mobile').val();
                var mobile = mobile_length.length;
                console.log(mobile);
                if(mobile < 10)
                {
                    console.log("done");
                }else{
                    console.log("2");
                }
        }
    </script>
@endpush
