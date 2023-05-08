@extends('admin.layouts.main')

@section('title')
Vehicle Detail
@endsection

@section('content')

<!-- Main Content-->
<div class="main-content side-content pt-0">

    <div class="container-fluid">
        <div class="inner-body">
        <input type="hidden" name="user_id" id="user_id" value="{{ $user->user_id }}">
            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h2 class="main-content-title tx-24 mg-b-5">Vehicle Detail</h2>
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.vehicles.index',['panel' => Session::get('panel')]) }}">Vehicle</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Details</li>
                    </ol>
                </div>
                
            </div>
            <!-- End Page Header -->

            <!-- Row -->
            <div class="row row-sm">
                <div class="col-lg-12 col-md-12">
                    <div class="card custom-card main-content-body-profile">
                        <div class="tab-content">
                            
                            <div class="main-content-body tab-pane p-4 border-top-0 active" id="about">
                                
                                <div class="card-body p-0 border p-0 rounded-10">
                                    <div class="p-4">
                                        <h4 class="main-content-label tx-13 mg-b-20">Vehicle Details</h4>
                                        <hr>
                                        <div class="m-t-30 d-sm-flex">
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_number') }}</span>
                                                                    <div> {{ strtoupper($user->vehicle_number) }}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.brand_name') }}</span>
                                                                    <div> {{$user->brand_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.model_name') }}</span>
                                                                    <div> {{$user->model_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_type_name') }}</span>
                                                                    <div> {{$user->vehicle_type_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-40 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.vehicle_fuel_type_name') }}</span>
                                                                    <div> {{$user->vehicle_fuel_type_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Contact</label>
                                        <div class="d-sm-flex">
                                            
                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.name') }}</span>
                                                                    <div> {{$user->first_name.' '.$user->last_name}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>{{ @trans('vehicles.mobile_number') }}</span>
                                                                    <div> {{$user->mobile_number}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="border-top"></div>
                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Documents</label>
                                        <div class="">

                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Insurance Exp Date</span>
                                                                    <div>{{$user->insurance_exp_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Permit Exp Date</span>
                                                                    <div> {{$user->permit_exp_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Fitness Exp Date</span>
                                                                    <div> {{$user->fitness_exp_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>PUC Exp Date</span>
                                                                    <div> {{$user->puc_exp_date}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                                                                        
                                            <div class="row row-sm">
                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">RC Front</h6>
                                                    <div class="image-div">
                                                    
                                                    @if(!empty($user->rc_front_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->rc_front_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->rc_front_url}}" onclick='getImage("{{$user->rc_front_url}}","RC Front")'>
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->rc_front_url}}','RC Front')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="rc_front_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="rc_front_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->rc_front_url_status == 2)
                                                        @if(!empty($user->rc_front_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 rc_front_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','rc_front_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 rc_front_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','rc_front_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->rc_front_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','rc_front_url_status','rc_front_url','vehicles')">Change Image</button>

                                                </div>

                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">RC Back</h6>
                                                    <div class="image-div">
                                                    @if(!empty($user->rc_back_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->rc_back_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->rc_back_url}}" onclick="getImage('{{$user->rc_back_url}}','RC Back')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->rc_back_url}}','RC Back')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="rc_back_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="rc_back_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->rc_back_url_status == 2)
                                                    @if(!empty($user->rc_back_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 rc_back_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','rc_back_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 rc_back_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','rc_back_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->rc_back_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','rc_back_url_status','rc_back_url','vehicles')">Change Image</button>
                                                </div>
                                                
                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Insurance Document</h6>
                                                    <div class="image-div">
                                                    @if(!empty($user->insurance_doc_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->insurance_doc_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->insurance_doc_url}}" onclick="getImage('{{$user->insurance_doc_url}}','Insurance Document')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->insurance_doc_url}}','Insurance Document')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="insurance_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="insurance_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->insurance_doc_url_status == 2)
                                                    @if(!empty($user->insurance_doc_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 insurance_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','insurance_doc_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 insurance_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','insurance_doc_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->insurance_doc_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','insurance_doc_url_status','insurance_doc_url','vehicles')">Change Image</button>
                                                </div>
                                                
                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Permit Document</h6>
                                                    <div class="image-div">
                                                    <!--  -->
                                                    @if(!empty($user->permit_doc_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->permit_doc_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->permit_doc_url}}" onclick="getImage('{{$user->permit_doc_url}}','Permit Document')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->permit_doc_url}}','Permit Document')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="permit_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="permit_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->permit_doc_url_status == 2)
                                                    @if(!empty($user->permit_doc_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 permit_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','permit_doc_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 permit_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','permit_doc_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->permit_doc_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','permit_doc_url_status','permit_doc_url','vehicles')">Change Image</button>
                                                </div>
                                                

                                                <div class="col-12 col-md-12">
                                                &nbsp;
                                                </div>

                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Fitness Document</h6>
                                                    <div class="image-div">
                                                    @if(!empty($user->fitness_doc_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->fitness_doc_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->fitness_doc_url}}" onclick="getImage('{{$user->fitness_doc_url}}','Fitness Document')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->fitness_doc_url}}','Fitness Document')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="fitness_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="fitness_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->fitness_doc_url_status == 2)
                                                        @if(!empty($user->fitness_doc_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 fitness_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','fitness_doc_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 fitness_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','fitness_doc_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->fitness_doc_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','fitness_doc_url_status','fitness_doc_url','vehicles')">Change Image</button>
                                                </div>
                                                
                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">PUC Document</h6>
                                                    <div class="image-div">
                                                    @if(!empty($user->puc_doc_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->puc_doc_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->puc_doc_url}}" onclick="getImage('{{$user->puc_doc_url}}','PUC Document')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->puc_doc_url}}','PUC Document')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="puc_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="puc_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->puc_doc_url_status == 2)
                                                        @if(!empty($user->puc_doc_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 puc_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','puc_doc_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 puc_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','puc_doc_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->puc_doc_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','puc_doc_url_status','puc_doc_url','vehicles')">Change Image</button>
                                                </div>
                                                
                                                
                                                <div class="col-6 col-md-3 text-center">
                                                    <h6 class="text-center">Agreement Document</h6>
                                                    <div class="image-div">
                                                    
                                                    @if(!empty($user->agreement_doc_url))
                                                    <?php 
                                                    $Infos = pathinfo($user->agreement_doc_url);
                                                    $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                    if( strtolower($extension) != 'pdf'){
                                                    ?>
                                                        <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$user->agreement_doc_url}}" onclick="getImage('{{$user->agreement_doc_url}}','Agreement Document')">
                                                    <?php } else{ ?>
                                                        <img style="cursor:pointer;" onclick="getPdf('{{$user->agreement_doc_url}}','Agreement Document')" src="{{ asset('pdf.png') }}" width="75%">
                                                    <?php } ?>
                                                    @else
                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                    @endif
                                                    </div>
                                                    <span class="text-success" id="agreement_doc_url_status_approved" style="display:none;">Approved</span>
                                                    <span class="text-danger" id="agreement_doc_url_status_rejected" style="display:none;">Rejected</span>

                                                    @if( $user->agreement_doc_url_status == 2)
                                                    @if(!empty($user->agreement_doc_url))
                                                        <button type="button" class="btn ripple btn-success btn-sm mt-2 agreement_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','agreement_doc_url_status','1')">Approve</button>
                                                        <button type="button" class="btn ripple btn-danger btn-sm mt-2 agreement_doc_url_status_btn" onclick="documentVerify('vehicle','{{$user->id}}','agreement_doc_url_status','0')">Rejected</button>
                                                        @endif
                                                    @else
                                                        @if( $user->agreement_doc_url_status == 1)
                                                            <span class="text-success">Approved</span>
                                                        @else
                                                            <span class="text-danger">Rejected</span>
                                                        @endif
                                                    @endif

                                                    &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehicles','{{$user->id}}','agreement_doc_url_status','agreement_doc_url','vehicles')">Change Image</button>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    
                                    <div class="border-top"></div>

                                    <div class="p-4">
                                        <label class="main-content-label tx-13 mg-b-20">Vehicle Images</label>
                                        <div class="">

                                            <div class="row col-md-12">
                                                <div class="col-md-3 pl-0">
                                                    <div class="mg-sm-r-20 mg-b-10">
                                                        <div class="main-profile-contact-list">
                                                            <div class="media">
                                                                <div class="media-body"> <span>Registration Year</span>
                                                                    <div> {{$user->registration_year}}</div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>   

                                            <div class="row row-sm">
                                                @if(!empty($vehicle))
                                                    <?php $i = 1;?> 
                                                    @foreach($vehicle as $val)
                                                        
                                                        <div class="col-6 col-md-3 text-center">
                                                            <div class="image-div">
                                                                <h6 class="text-center">{{$val->view_name}}</h6>
                                                                <!-- <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$val->image_url}}" onclick="getImage('{{$val->image_url}}','{{$val->view_name}}')"> -->

                                                                @if(!empty($val->image_url))
                                                                <?php 
                                                                $Infos = pathinfo($val->image_url);
                                                                $extension = !empty($Infos['extension']) ? $Infos['extension'] : '';;
                                                                if( strtolower($extension) != 'pdf'){
                                                                ?>
                                                                    <img alt="docuemtn image" class="img-thumbnail image-class" src="{{$val->image_url}}" onclick="getImage('{{$val->image_url}}','{{$val->view_name}}')">
                                                                <?php } else{ ?>
                                                                    <img style="cursor:pointer;" onclick="getPdf('{{$val->image_url}}','{{$val->view_name}}')" src="{{ asset('pdf.png') }}" width="75%">
                                                                <?php } ?>
                                                                @else
                                                                <img alt="docuemtn image" class="img-thumbnail image-class" src="{{ asset('noimage.png') }}">
                                                                @endif
                                                            </div>
                                                            <br/>
                                                            <span class="text-success" id="image_url_status_approved{{$val->id}}" style="display:none;">Approved</span>
                                                            <span class="text-danger" id="image_url_status_rejected{{$val->id}}" style="display:none;">Rejected</span>
                                                                    
                                                            @if( $val->image_url_status == 2)
                                                                @if(!empty($val->image_url))
                                                                <button type="button" class="btn ripple btn-success btn-sm mt-2 image_url_status_btn{{$val->id}}" onclick="documentVerify('vehiclePhotoMapping','{{$val->id}}','image_url_status','1')">Approve</button>
                                                                <button type="button" class="btn ripple btn-danger btn-sm mt-2 image_url_status_btn{{$val->id}}" onclick="documentVerify('vehiclePhotoMapping','{{$val->id}}','image_url_status','0')">Rejected</button>
                                                                @endif
                                                            @else
                                                                @if( $val->image_url_status == 1)
                                                                    <span class="text-success">Approved</span>
                                                                @else
                                                                    <span class="text-danger">Rejected</span>
                                                                @endif
                                                            @endif

                                                            &nbsp; <button type="button" class="btn ripple btn-primary btn-sm mt-2" onclick="imageUpload('vehiclePhotoMapping','{{$val->id}}','image_url_status','image_url','vehicles')">Change Image</button>
                                                        </div>

                                                        @if($i == 4)
                                                        <div class="col-12 col-md-12">
                                                        &nbsp;
                                                        </div>
                                                        <div class="col-12 col-md-12">
                                                        &nbsp;
                                                        </div>
                                                        @endif

                                                        <?php $i++; ?>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>
    </div>
</div>
<!-- End Main Content-->
@endsection

<!-- Change Status -->
<div class="modal fade" id="editModal">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Edit User Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <form method="POST" id="frmAddEditUser" action="{{ route('admin.users.changesStatus',['panel' => Session::get('panel')]) }}">
                @csrf
                    <input type="hidden" name="id" id="id" value="{{$user->id}}">

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{$user->name}}" placeholder="Enter Name" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.email') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control" value="{{$user->email}}" placeholder="Enter Email" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.phone') }}<span class="required">*</span></label>
                        <input type="text" name="mobile_number" id="mobile_number" class="form-control" value="{{$user->mobile_number}}" placeholder="Enter Mobile Number" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">{{ @trans('user.dob') }}<span class="required">*</span></label>
                        <input type="text" name="dob" id="dob" class="form-control dob" value="{{$user->dob}}" placeholder="Enter Date Of Birth" />
                        @if($errors->has('name'))
                        <div class="invalid-feedback">{{ $errors->first('user')}}
                        </div>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="control-label">User Status</label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio3" name="user_status" value="3" @if($user->user_status == '3') checked @endif>
                            <label class="custom-control-label" for="customRadio3">Waiting for approval</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio" name="user_status" value="1" @if($user->user_status == '1') checked @endif>
                            <label class="custom-control-label" for="customRadio">Approved</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio1" name="user_status" value="0" @if($user->user_status == '0') checked @endif>
                            <label class="custom-control-label" for="customRadio1">Inactive</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="customRadio2" name="user_status" value="2" @if($user->user_status == '2') checked @endif>
                            <label class="custom-control-label" for="customRadio2">Rejected</label>
                        </div>
                    </div>
                    
                    <hr/>
                    
                    <!-- Modal footer -->
                    <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                        <button type="submit" class="btn btn-bordered-primary px-4">Submit</button>
                    </div>
                </form>
            </div>
        </div>        
    </div>
</div>

<!-- Image View Modal -->
<div class="modal" id="attachModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0 pb-0">
                <h4 class="modal-title regular body-font-size img-title">Image View</h4>
                <button type="button" class="close" data-dismiss="modal" onclick="closeImgModel()">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="before-zoom">
                <div class="modal-body" id="panzoom">
                    <img id="image-gallery-image" class="mainimage" src="">
                </div>
            </div>

            <div class="modal-footer">
                <!-- <button type="button" class="btn-primary" onclick="zoomin()">Zoom In</button>
                <button type="button" class="btn-primary" onclick="zoomout()">Zoom Out</button> -->
                <button type="button" class="btn-primary" id="rotate" onclick="rotate()">Rotate</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="cancel_reason_model">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0">
                <h4 class="modal-title regular body-font-size">Edit User Status</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                
                <input type="hidden" name="hidden_id" id="hidden_id" value="">
                <input type="hidden" name="hidden_type" id="hidden_type" value="">
                <input type="hidden" name="hidden_key" id="hidden_key" value="">

                <div class="form-group">
                    <label class="control-label">Reason<span class="required">*</span></label>
                    <textarea type="text" id="cancel_reason" name="cancel_reason" class="form-control" value=""></textarea>
                </div>

                <hr/>
                
                <!-- Modal footer -->
                <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                    <button type="button" onclick="documentVerifyForCancel()" class="btn btn-bordered-primary px-4">Submit</button>
                </div>
            </div>
        </div>        
    </div>
</div>

<!-- PDF View Modal -->
<div class="modal" id="attachPdfModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <!-- Modal Header -->
            <div class="modal-header new-light-bg border-0 pb-0">
                <h4 class="modal-title regular body-font-size pdf-title">PDF View</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                <embed id="view-pdf" src="sample.pdf" width="720" height="475" />
            </div>

        </div>
    </div>
</div>

<!-- Image Upload Modal -->
<div class="modal" id="imageUploadModal" data-toggle="modal" data-backdrop="false">
    <div class="modal-dialog modal-md modal-dialog-centered">
        
    <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header new-light-bg border-0">
            <h4 class="modal-title regular body-font-size">Update Image</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <!-- Modal body -->
        <div class="modal-body">
            <form method="POST" id="frmAddEditUser" action="{{ route('admin.users.changeImage',['panel' => Session::get('panel')]) }}" enctype="multipart/form-data">
            @csrf
                <input type="hidden" name="id" id="id" value="{{ $user->user_id }}">
                <input type="hidden" name="hidd_type" id="hidd_type" value="">
                <input type="hidden" name="pk_id" id="pk_id" value="">
                <input type="hidden" name="pk_key" id="pk_key" value="">                
                <input type="hidden" name="image_key_val" id="image_key_val" value="">
                <input type="hidden" name="module_name" id="module_name" value="">
                <div class="form-group">
                    <label class="control-label">{{ @trans('user.name') }}<span class="required">*</span></label>
                    <div class="mg-b-10" id="fnWrapper">
                        <input type="file" name="police_verification" id="image_key" class="dropify driver" data-height="200"/>
                    </div>
                </div>
                <hr/>
                <!-- Modal footer -->
                <div class="modal-footer justify-content-center border-top-0 pt-0 pb-4 bg-white">
                    <button type="submit" class="btn btn-primary px-4">Submit</button>
                </div>
            </form>
        </div>
        </div>

    </div>
</div>
@push('pageJs')

<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.1.0/css/font-awesome.min.css">
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.2.0/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@panzoom/panzoom/dist/panzoom.min.js"></script>
<script type="text/javascript">
$( document ).ready(function() {
    const element = document.getElementById('panzoom')
    const panzoom = Panzoom(element, {
        // options here
    });

    // enable mouse wheel
    const parent = element.parentElement
    parent.addEventListener('wheel', panzoom.zoomWithWheel);
    

    // This demo binds to shift + wheel
    parent.addEventListener('wheel', function(event) {
    if (!event.shiftKey) return
    panzoom.zoomWithWheel(event)
    })

    // Pass options
    $(".panzoom").panzoom({
        minScale: 0,
        $zoomRange: $("input[type='range']")
    });
});

function closeImgModel(){
    //console.log("close button click here...");
    $("#image-gallery-image").attr('src','');
    $("#image-gallery-image").css({'transform': ''});
    $("#rotate").attr('onclick','rotate()');
    $("#panzoom").css('transform','none');
}

$(document).on('keydown', function(event) {
    if (event.key == "Escape") {
        closeImgModel();
        $('#attachModal').modal('hide');
        $('#attachPdfModal').modal('hide');
    }
});

function rotate(){
    $("#image-gallery-image").css({'transform': 'rotate(-90deg)'});
    $("#rotate").attr('onclick','rotate1()');
}

function rotate1(){
    $("#image-gallery-image").css({'transform': 'rotate(-180deg)'});
    $("#rotate").attr('onclick','rotate2()');
}

function rotate2(){
    $("#image-gallery-image").css({'transform': 'rotate(-270deg)'});
    $("#rotate").attr('onclick','rotate3()');
}

function rotate3(){
    $("#image-gallery-image").css({'transform': ''});
    $("#rotate").attr('onclick','rotate()');
}

function getImage(imageName,title){    
    $('#image-gallery-image').attr('src', imageName);
    $(".img-title").text(title);
    $("#attachModal").modal();
}

function getPdf(filename,title){
    $('#view-pdf').attr('src', filename);
    $(".pdf-title").text(title);
    $("#attachPdfModal").modal();
}

function imageUpload(type,id,key,image_key,module_name){    
    $('#hidd_type').val(type);
    $('#pk_id').val(id);
    $('#pk_key').val(key);
    $('#image_key').attr('name', image_key);
    $('#image_key_val').val(image_key);
    $('#module_name').val(module_name);
    $("#imageUploadModal").modal();
}

@if(Session::has('message'))
    Snackbar.show({
        pos: 'bottom-right',
        text: "{!! session('message') !!}",
        actionText: 'Okay'
    });
@endif

$('#load2').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
    }, 10000);

    var id = $('#user_id').val();
    var siteurl = "{{URL::to('/')}}/api/v1/createVcnForWeb";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            var message = response.message;
            var errorcode = response.errorcode;
            var success = response.success;
            var dataobject = response.data;
            if(errorcode == 1 && success == true){
                var masked_card = dataobject.masked_card;
                $('#card_number').text(masked_card);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: message,
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: message,
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
            $this.button('reset');
        }
    });
});

$('#reset_btn').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
    }, 10000);

    var id = $('#user_id').val();
    var siteurl = "{{url(Session::get('panel').'/users/resetAttempt')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            if(response == 'success'){
                $('#reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
            $this.button('reset');
        }
    });
});

$('#otp_reset_btn').on('click', function() {
    var $this = $(this);
    $this.button('loading');
    setTimeout(function() {
       $this.button('reset');
    }, 10000);

    var id = $('#user_id').val();
    var siteurl = "{{url(Session::get('panel').'/users/resetOtpAttempt')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id
        },
        success: function(response) {
            if(response == 'success'){
                $('#otp_reset_number').text(0);

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'Attemp reset successfully!',
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
            $this.button('reset');
        }
    });
});

$('#ussd_status_btn').on('click', function() {
    var id = $('#user_id').val();
    var ussd_enable = $('#ussd_enable').val();
    var siteurl = "{{url(Session::get('panel').'/users/changeUssdStatus')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "user_id": id,
            "ussd_enable":ussd_enable
        },
        success: function(response) {
            if(response == 'success'){

                if(ussd_enable == 1){
                    $('#ussd_enable_lable').text('On');
                    $('#ussd_enable').val('0');
                    $('.ussd-enable').text('Disable');        
                }else{
                    $('#ussd_enable_lable').text('Off');
                    $('#ussd_enable').val('1'); 
                    $('.ussd-enable').text('Enable');  
                }

                Snackbar.show({
                    pos: 'bottom-right',
                    text: 'USSD status change successfully.',
                    actionText: 'Okay'
                });
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
        }
    });
});

function documentVerify(type,id,key,status){
    
    
    /* if(status == 0){
        alert(status);
        //$('#cancel_reason_model').modal('show');
        $('#cancel_reason_model').modal('toggle');

        $('#hidden_id').val(id);
        $('#hidden_type').val(type);
        $('#hidden_key').val(key);
        return false;
    } */

    var statustext = 'Approve';
    if(status == 1){
        statustext = 'Approve';
    } else {
        statustext = 'Reject';
    }

    var user_id = $('#user_id').val();

    if (confirm('Are you sure you want to '+ statustext +' this?')) {
        var siteurl = "{{url(Session::get('panel').'/users/documentVerification')}}";
        $.ajax({
            url: siteurl,
            type: "POST",
            data: {
                "_token": "{{csrf_token()}}",
                "id": id,
                "type":type,
                "key":key,
                "status": status,
                "user_id": user_id
            },
            success: function(response) {
                if(response == 'success'){

                    if (type=='vehiclePhotoMapping') {
                        if(status == '1'){
                            $('#'+key+'_approved'+id).css('display','');
                        }else{
                            $('#'+key+'_rejected'+id).css('display','');
                        }
                        $('.'+key+'_btn'+id).css('display','none');
                    }else{
                        if(status == '1'){
                            $('#'+key+'_approved').css('display','');
                        }else{
                            $('#'+key+'_rejected').css('display','');
                        }
                        $('.'+key+'_btn').css('display','none');
                    }

                    
                    if(status == '1'){
                        Snackbar.show({
                            pos: 'bottom-right',
                            text: 'Document verify successfully.',
                            actionText: 'Okay'
                        });
                    }else{
                        Snackbar.show({
                            pos: 'bottom-right',
                            text: 'Document Rejected!',
                            actionText: 'Okay'
                        });
                    }
                }else{
                    Snackbar.show({
                        pos: 'bottom-right',
                        actionTextColor: '#fff',
                        textColor: '#fff',
                        text: 'Somthing went wrong. Please try again!',
                        backgroundColor: '#cc0000',
                        actionText: 'Okay'
                    });
                }
            }
        });
    }
}

function documentVerifyForCancel(){
    var id = $('#hidden_id').val();
    var type = $('#hidden_type').val();
    var key = $('#hidden_key').val();

    var user_id = $('#user_id').val();

    var siteurl = "{{url(Session::get('panel').'/users/documentVerification')}}";
    $.ajax({
        url: siteurl,
        type: "POST",
        data: {
            "_token": "{{csrf_token()}}",
            "id": id,
            "type":type,
            "key":key,
            "status": 0,
            "user_id": user_id
        },
        success: function(response) {
            if(response == 'success'){

                if (type=='vehiclePhotoMapping') {
                    if(status == '1'){
                        $('#'+key+'_approved'+id).css('display','');
                    }else{
                        $('#'+key+'_rejected'+id).css('display','');
                    }
                    $('.'+key+'_btn'+id).css('display','none');
                }else{
                    if(status == '1'){
                        $('#'+key+'_approved').css('display','');
                    }else{
                        $('#'+key+'_rejected').css('display','');
                    }
                    $('.'+key+'_btn').css('display','none');
                }

                
                if(status == '1'){
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Document verify successfully.',
                        actionText: 'Okay'
                    });
                }else{
                    Snackbar.show({
                        pos: 'bottom-right',
                        text: 'Document Rejected!',
                        actionText: 'Okay'
                    });
                }
            }else{
                Snackbar.show({
                    pos: 'bottom-right',
                    actionTextColor: '#fff',
                    textColor: '#fff',
                    text: 'Somthing went wrong. Please try again!',
                    backgroundColor: '#cc0000',
                    actionText: 'Okay'
                });
            }
        }
    });
}
</script>
@endpush