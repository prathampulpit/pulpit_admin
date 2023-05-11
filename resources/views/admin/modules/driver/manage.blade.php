
<form method="post" action="{{ route('admin.driver.save',['panel' => Session::get('panel')]) }}" class="parsley-style-1" id="selectForm2" name="selectForm2" enctype="multipart/form-data">
    @csrf 
    <input type="hidden" name="user_id" id="user_id" value="{{$user_id}}"> 
    <input type="hidden" name="id" id="id" value="{{$id}}"> 

    <div class="row row-sm mg-b-20">
        <div class="col-lg-6">
            <p class="mg-b-10">{{ @trans('user.license_no') }}</p>
            <input class="form-control driver" value="@if(old('license_no')){{old('license_no')}}@elseif($id && !empty($data)){{$data->driving_licence_no}}@endif" name="license_no"  id="license_no" placeholder="Enter {{@trans('user.license_no')}}"  type="text" required="">
        </div>
        <div class="col-lg-4">
            <p class="mg-b-10">Birth Date</p>
            <input class="form-control" value="@if(!empty($data)){{date('Y-m-d',strtotime($data->dob))}}@endif" name="dob" id="driver_bod" placeholder="Enter Date of Birth"  type="date" required="">
        </div>
        <div class="col-lg-2">
            <a class="btn btn-primary btn-sm"  href="javascript:;" id="driving_license" style="margin-top: 35px;">Search</a>
        </div>
        <div class="col-lg-12" style='margin-top: 5px;'>
            <div id="errorMessageDrivePage"></div>
            <hr/>
        </div>
        <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.first_name') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input class="form-control"  name="first_name" id="first_name" placeholder="Enter {{@trans('user.first_name')}}" 
                        type="text" 
                       maxlength="50"
                       value="@if(!empty($data)){{$data->first_name}}@endif" required=""
                       >
            </div>
        </div>
        
        <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.last_name') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input class="form-control"  name="last_name" 
                       id="last_name" 
                       placeholder="Enter {{@trans('user.last_name')}}" 
                        type="text" maxlength="50"
                       value="@if(!empty($data)){{$data->last_name}}@endif" required=""
                       >
            </div>
        </div>
        <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">Father: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input class="form-control" value="@if(!empty($data)){{$data->father_name}}@endif" name="father_name" id="father_name" placeholder="Enter Father Name"  type="text" maxlength="50">
            </div>
        </div>
        <div class="col-lg-6" style='margin-top: 5px;'>
            <p class="mg-b-10">Address: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <textarea class="form-control"  name="street_address" id="street_address"  
                    placeholder="Enter Address" >@if(!empty($data)){{$data->street_address}}@endif </textarea>
            </div>
        </div>
<!--        <div class="col-lg-6" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.email') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input class="form-control" value="@if(old('emailid')){{old('emailid')}}@elseif($id && !empty($data)){{$user->emailid}}@endif" name="emailid" placeholder="Enter {{@trans('user.email')}}"  type="text" maxlength="100">
            </div>
        </div>-->
      
        <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.pin_code') }}</p>
            <input class="form-control driver" value="@if(!empty($data)){{$data->pincode}}@endif" id="pincode" name="pin_code" placeholder="Enter {{@trans('user.pin_code')}}"  type="text">
        </div>
        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.states') }}</p>
            <input type="text"  class="form-control"  name="state_id" id="state_id" value="@if(!empty($data)){{$data->state}}@endif">
                
            
        </div>
        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.city') }}</p>
            <input type="text" class="form-control" name="city_id" id="city_id"  value="@if(!empty($data)){{$data->city}}@endif"> 
                 
            </select>
        </div>
        
          <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">Yea of Experience</p>
            <input class="form-control driver" value="@if(old('year_of_experience')){{old('year_of_experience')}}@elseif($id && !empty($data)){{$data->year_of_experience}}@endif" id="year_of_experience" name="year_of_experience" placeholder="Enter Year Of Experience"  type="text">
        </div>
        <div class="col-lg-6" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.mobile_number') }}: <span class="tx-danger">*</span><br/><small>Note : Mobile number must be enter your register number!</small></p>
            <div class="mg-b-10" id="fnWrapper">
                <input class="form-control" value="@if(old('mobile_number')){{old('mobile_number')}}@elseif($id && !empty($data)){{$user->mobile_number}}@endif" name="mobile_number" placeholder="Enter {{@trans('user.mobile_number')}}"  type="text" maxlength="10">
            </div>
        </div>
        <div class="col-lg-6" style='margin-top: 15px;'>
            <p class="mg-b-10">{{ @trans('user.gender') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10 row" id="fnWrapper" style='margin-top: 20px;'>
                <div class="col-lg-6">
                    <label class="rdiobox"><input name="gender" type="radio" value="Male" id="gender_male"  @if(!empty($data)) @if($data->gender=="Male") checked="true" @endif @endif > 
                        <span>Male</span></label>
                </div>
                <div class="col-lg-6">
                     
                    <label class="rdiobox"><input name="gender" type="radio" value="Female" id="gender_female" @if(!empty($data)) @if($data->gender=="Female") checked="true" @endif @endif>
                        <span>Female</span></label>
                </div>
            </div>
        </div>
     
        <div class="col-lg-4" style='margin-top: 5px;'>
            <p class="mg-b-10">{{ @trans('user.expiry_date') }}</p>
            <input class="form-control driver fc-datepicker hasDatepicker" value="@if(old('expiry_date')){{old('expiry_date')}}@elseif($id && !empty($data)){{$data->driving_licence_expiry_date}}@endif" name="expiry_date" id="expiry_date" placeholder="YYYY-MM-DD"  type="date">
        </div>
        <div class="col-lg-3">
            <p class="mg-b-10">Issue Date</p>
            <input class="form-control driver fc-datepicker hasDatepicker" name="issue_date" id="issue_date" placeholder="YYYY-MM-DD"  type="date" value="@if(!empty($data)){{$data->issue_date}}@endif">
        </div>
        <!--        <div class="col-lg-6">
                    <p class="mg-b-10">{{ @trans('user.street_address') }}</p>
                    <input class="form-control driver" value="@if(old('street_address')){{old('street_address')}}@elseif($id && !empty($data)){{$data->street_address}}@endif" name="street_address" placeholder="Enter {{@trans('user.street_address')}}"  type="text">
                </div>-->
   
      

    </div>


    <div class="row row-sm mg-b-20 travel">
<!--        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.logo') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input type="file" name="logo" id="logo" class="dropify travel" data-height="200" @if(!$id) @else data-default-file="@if(!empty($data->logo)){{$data->logo}}@endif" @endif/>
            </div>
        </div>-->

        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.driving_license_front') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input type="file" name="driving_license_front" id="driving_license_front" class="dropify driver" data-height="200" @if(!$id)  @else data-default-file="@if(!empty($data)){{$data->dl_front_url}}@endif" @endif/>
            </div>
        </div>
        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.driving_license_back') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input type="file" name="driving_license_back" id="driving_license_back" class="dropify driver" data-height="200" @if(!$id)  @else data-default-file="@if(!empty($data)){{$data->dl_back_url}}@endif" @endif/>
            </div>
        </div>
        <div class="col-lg-4">
            <p class="mg-b-10">{{ @trans('user.police_verification') }}: <span class="tx-danger">*</span></p>
            <div class="mg-b-10" id="fnWrapper">
                <input type="file" name="police_verification" id="police_verification" class="dropify driver" data-height="200" @if(!$id)  @else data-default-file="@if(!empty($data)){{$data->police_verification_url}}@endif" @endif/>
            </div>
        </div>
    </div>
    <!-- End -->




    <div class="mg-t-30">
        <!-- <input type="submit" class="btn ripple btn-primary pd-x-20 btn-block" value="{{ @trans('user.save') }}"> -->
        <button type="submit" class="btn ripple btn-primary pd-x-20 btn-block manage_driver_save_buttton"  id="load2" data-loading-text="<i class='fa fa-spinner fa-spin '></i> Loading">{{ @trans('user.save') }}</button>
    </div>
</form>



@push('pageJs')


<script>
    function HistoryShow() {
        $(".history_show").toggle();
    }
</script>
@endpush