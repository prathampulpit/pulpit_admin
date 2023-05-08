<?php

namespace App\Http\Controllers\Customer;

use App\Events\Customer\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\ChangePasswordRequest;
use App\Http\Requests\Customer\ChangeProfileRequest;
use App\Http\Requests\Customer\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Customer;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Models\UserType;
use App\Models\UserPurchasedPlans;
use App\Models\SubscriptionPlans;
use App\Models\Earnings;
use App\Models\TripBookings;
use App\Models\States;
use App\Models\Cities;
use App\Models\VehicleBrands;
use App\Models\VehicleBrandModels;
use App\Models\VehicleFuelType;
use App\Models\VehicleColour;
use App\Models\UserWorkProfile;
use App\Models\AgentUsers;
use App\Models\BankAccount;
use App\Models\Drivers;
use App\Models\Cabs;
use App\Models\Vehicles;
use App\Models\VehiclePhotoMapping;
use App\Models\VehicleDrivingMapping;
use App\Models\UserBankMapping;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\UserRepository;
use App\Repositories\VehiclesRepository;
use App\Repositories\NotificationsRepository;
use App\Repositories\CustomerRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;
use DB;

class CustomerController extends Controller
{
    protected $customerRepository;

    public function __construct(
        VehiclesRepository $vehiclesRepository,
        NotificationsRepository $notificationsRepository,
        CustomerRepository $customerRepository
    ) {
        $this->vehiclesRepository = $vehiclesRepository;
        $this->notificationsRepository = $notificationsRepository;
        $this->customerRepository = $customerRepository;
    }

    public function index($panel, Request $request, $filter = null)
    { 
        $customer = Auth::user();
        $id = $customer['id'];

        $role_id = $customer['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];
        $states = States::all();
        $state_id = $request->state_id;
        $city_id = $request->city_id;
        if(isset($request->city_id)){
            $state = States::where('id',$state_id)->first(); 
            if(!empty($state)){
                $cities = Cities::where('stateCode',$state->isoCode)->get();
            }else{
                $cities = Cities::all();
            }
        }else{
            $cities = Cities::all();
        }
        if (in_array("4", $role_id_arr) || $user_role == 'administrator') {
            $profile_path = config('custom.upload.user.profile');
            $file_path = $profile_path;

            $user_type = userType::where('status', '!=', '2')->get();
            
            
            $today = date("Y-m-d");
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $week = date('Y-m-d', strtotime("-7 days"));
            $this_month = date('Y-m');
            $last_month = date("Y-m", strtotime('-1 month', strtotime($today)));
            $fiften_date = date('Y-m-d', strtotime("-15 days"));
            // dd($today,$yesterday,$week,$this_month,$last_month,$fiften_date);
            //$today_joined = Customer::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['today_joined'] = $today;
            $countcompany['state_id'] = $request->state_id;
            $countcompany['city_id'] = $request->city_id;
            $countcompany['type_id'] = '6';
            $today_joined = $this->customerRepository->getByParams($countcompany);

            //$yesterday_joined = Customer::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$yesterday."'")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '6';
            $countcompany['yesterday_joined'] = $yesterday;
            $yesterday_joined = $this->customerRepository->getByParams($countcompany);

            //$week_joined = Customer::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$week."' ")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '6';
            $countcompany['week_joined'] = $week;
            $week_joined = $this->customerRepository->getByParams($countcompany);

            //$this_month_joined = Customer::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$this_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '6';
            $countcompany['this_month_joined'] = $this_month;
            $this_month_joined = $this->customerRepository->getByParams($countcompany);

            //$last_month_joined = Customer::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$last_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '6';
            $countcompany['last_month_joined'] = $last_month;
            $last_month_joined = $this->customerRepository->getByParams($countcompany);

            //$all_joined = Customer::count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type'] = 'customer';
            $all_joined = $this->customerRepository->getByParams($countcompany);

            //$active_users = Customer::whereRaw("user_status = 1")->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$fiften_date."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type'] = 'customer';
            $countcompany['status'] = '1';
            $countcompany['week'] = $fiften_date;
            $active_users = $this->customerRepository->getByParams($countcompany);
            //$inactive_users = Customer::whereRaw("user_status = 0")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type'] = 'customer';
            $countcompany['status'] = '0';
            $inactive_users = $this->customerRepository->getByParams($countcompany);

            //$paid_users = UserPurchasedPlans::groupBy('user_id')->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type'] = 'customer';
            $countcompany['paid_users'] = '1';
            $paid_users = $this->customerRepository->getByParams($countcompany);
            $unpaid_users = $all_joined - $paid_users;

            /* $expired_users = UserPurchasedPlans::leftJoin('customers', function($join) {
                $join->on('user_purchased_plans.user_id', '=', 'customers.id');
            })->groupBy('user_purchased_plans.user_id')->whereRaw("DATE_FORMAT(start_datetime, '%Y-%m-%d') < '".$today."' AND DATE_FORMAT(start_datetime, '%Y-%m-%d') > '".$today."' ")->count(); */

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '6';
            $countcompany['expired_users'] = '1';
            $expired_users = $this->customerRepository->getByParams($countcompany);

            $uninstall_users = '0';
            // graph data
            $data = [];
            $customers = Customer::all();
            foreach($customers as $c){
                $trip = TripBookings::where('customer_id',$c->id)->count();
                $customer = [];
                $customer['trip'] = $trip;
                if(isset($c->first_name)){
                    $customer['user_name']  = $c->first_name . ' ' .$c->last_name;
                }else{
                    $customer['user_name'] = $c->mobile_number;
                }
                array_push($data, $customer);
            }
            
            $graphData = collect($data)->sortBy('trip')->reverse()->toArray();
            $day_name_arr = '';
            $graph_trans_data_arr = '';
            $k = 0;
            foreach($graphData as $g){
                $day_name_arr .= "'" . $g['user_name'] . "',";
                $expired_users = $g['trip'];
                $k++;
                $graph_trans_data_arr .= $expired_users . ",";
                if($k == 5){
                    break;
                }
            }
            $day_name_arr .= '';
            $day_name_arr1 = rtrim($day_name_arr, ",");
            $day_name = $day_name_arr1 . "";

            $graph_trans_data_arr .= '';
            $graph_trans_data_arr1 = rtrim($graph_trans_data_arr, ",");
            $graph_trans_data = $graph_trans_data_arr1;

            return view('admin.modules.customers.index', compact('file_path', 'day_name','user_type','graph_trans_data', 'today_joined', 'yesterday_joined', 'week_joined', 'this_month_joined', 'last_month_joined', 'all_joined', 'active_users', 'inactive_users', 'paid_users', 'unpaid_users', 'expired_users', 'uninstall_users','states','state_id','cities','city_id','filter'));
        } else {
            abort(403);
        }
    }

    public function index_json($panel, Request $request, $filter = null)
    {
        $customer = Auth::user();
        //$in = $this->customerRepository->hieararchy($customer, false);
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->customerRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['filter'] = $filter;
        $customers = $this->customerRepository->getPanelUsers($request, $params);
        foreach($customers as $customer){
            $trip = TripBookings::where('customer_id',$customer->id)->count();
            $customer['trip'] = $trip;
            // $latitude = $customer->latitude;
            // $longitude = $customer->longitude;
            // $url = "https://maps.google.com/maps/api/geocode/json?key=AIzaSyCxFnBNeaJ1TRuA-vCu6qhqpgU2F5c5bgM&callback=initMap&latlng=$latitude,$longitude";

            // // send http request
            // $geocode = file_get_contents($url);
            // $json = json_decode($geocode);
            // if($json->status == 'OK'){
            //     $address = $json->results[0]->formatted_address;
            // }else{
            //     $address = "-";
            // }
            // $customer['address'] = $address;
        }
        // dd($customers);
        return $customers;
    }

    public function referal_request()
    {
        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/' . $profile_path . "/";
        return view('admin.modules.customers.referal_request', compact('file_path'));
    }

    public function referal_request_json(Request $request)
    {
        $customer = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['referal_register_type'] = '3';
            $total = $this->customerRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['referal_register_type'] = '3';
        $customers = $this->customerRepository->getPanelUsers($request, $params);
        return $customers;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("4", $role_id_arr) || $user_role == 'administrator') {

            $params = [];

            $profile_id = "";
            $user_type_id = "";
            $bank_account_id = "";

            $customer = null;
            if ($id) {
                $params = [];
                $params['user_id'] = $id;
                $params['response_type'] = "single";
                $customer = $this->customerRepository->getByParams($params);

                $profile_id = $customer['profile_id'];
                $user_type_id = $customer['user_type_id'];
                $bank_account_id = $customer['bank_account_id'];
            }


            if ($user_type_id == 2 || $user_type_id == 3) {
                $data = AgentUsers::find($profile_id);
                $bank = BankAccount::find($bank_account_id);
            } else if ($user_type_id == '5') { //Driver cum owner
                $data = Drivers::find($profile_id);
                $bank = BankAccount::find($bank_account_id);
            } else if ($user_type_id == '4') { //Driver
                $data = Drivers::find($profile_id);
                $bank = array();
            } else {
                $data = array();
                $bank = array();
            }

            /* echo "<pre>";
            print_r($customer);
            exit; */

            //$agent_users = AgentUsers::all();
            $agent_users = DB::table('agent_users')
                ->join('user_work_profile', 'user_work_profile.profile_id', '=', 'agent_users.id')
                ->join('customers', 'user_work_profile.user_id', '=', 'customers.id')
                ->select('agent_users.*', 'customers.mobile_number')
                ->get();

            $states = states::all();
            $cities = Cities::all();
            $brands = VehicleBrands::where('status', '1')->get();
            $years = array("2020", "2021", "2022");
            return view('admin.modules.customers.store', [
                'user' => $customer,
                'id' => $id,
                'user_role' => $user_role,
                'admin' => $admin,
                'states' => $states,
                'cities' => $cities,
                'brands' => $brands,
                'years' => $years,
                'data' => $data,
                'bank' => $bank,
                'profile_id' => $profile_id,
                'user_type_id' => $user_type_id,
                'bank_account_id' => $bank_account_id,
                'agent_users' => $agent_users
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $customer = Auth::user();

        /* $role_id = $customer['role_id'];
        $admin_id = $customer['id'];
        $admin_name = $customer['first_name'].' '.$customer['last_name'];
        $notes = 'Verified by '.$admin_name;
        
        $role = Roles::find($role_id);
        $user_role = $role['slug'];

        $loginsert = new PortalActivities();
        $loginsert->user_id = $customer['id'];
        $loginsert->module_name = 'Edit Customer';
        $loginsert->request_data = json_encode($_POST);
        $loginsert->response_data = 'NA';
        $loginsert->save();

        $log_id = $loginsert->id; */


        //$id = $request->get('id');

        /* $email = $request->get('email');
        if(!empty($email)){
            $check_email = Customer::where('email', $email)->where('id', '!=', $id)->first();
            if(!empty($check_email)){

                $log_update = PortalActivities::find($log_id);
                $log_update->response_data = "Email already exits!.";
                $log_update->save();

                return redirect()->back()->with('message', trans('Email already exits!.'));
            }
        } */

        $id = $request->get('id', null);
        $usertype = $request->get('usertype');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $name = $first_name . " " . $last_name;
        $state = $request->get('state');
        $city_id = $request->get('city_id');
        $emailid = $request->get('emailid');
        $mobile_number = $request->get('mobile_number');
        $gender = $request->get('gender');
        $travel_name = $request->get('travel_name');
        $owner_name = $request->get('first_name') . " " . $request->get('last_name');
        $total_year_of_business = $request->get('total_year_of_business');
        $adhar_no = $request->get('adhar_no');
        $license_no = $request->get('license_no');
        $expiry_date = $request->get('expiry_date');
        $expiry_date = date("Y-m-d", strtotime($expiry_date));
        $street_address = $request->get('street_address');
        $pin_code = $request->get('pin_code');
        $pan_card_no = $request->get('pan_card_no');
        $office_no = $request->get('office_no');
        $adhar_card_no = $request->get('adhar_card_no');
        $account_holder_name = $request->get('account_holder_name');
        $ifsc_code = $request->get('ifsc_code');
        $bank_name = $request->get('bank_name');
        $branch_name = $request->get('branch_name');
        $account_number = $request->get('account_number');
        $confirm_account_number = $request->get('confirm_account_number');

        if (!empty($request->get('id'))) {
            $customer = Customer::find($id);
        } else {
            $customer = new Customer();

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/+91' . $mobile_number . '/AUTOGEN/OTP',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
            ));

            $response = curl_exec($curl);
            curl_close($curl);

            $otp_result = json_decode($response, true);
            if ($otp_result['Status'] == 'Success') {
                $customer->is_otp = '2';
                $customer->otp = $otp_result['Details'];
            }
        }
        $customer->emailid = $emailid;
        $customer->first_name = $first_name;
        $customer->last_name = $last_name;
        $customer->mobile_number = $mobile_number;
        $customer->gender = $gender;
        $customer->state = $state;
        $customer->city_id = $city_id;
        $customer->created_at = date("Y-m-d H:i:s");
        $customer->updated_at = date("Y-m-d H:i:s");
        $customer->save();

        if (!empty($request->get('id'))) {
            $user_id = $id;
        } else {
            $user_id = $customer->id;
        }

        if ($request->has('logo')) {
            $file = $request->file('logo');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $agent_logo = env('S3_BUCKET_URL') . $filePath;
        } else {
            $agent_logo = "";
        }

        if ($request->has('driving_license_front')) {
            $file = $request->file('driving_license_front');
            $driving_license_front_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_front_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $driving_license_front = env('S3_BUCKET_URL') . $filePath;
        } else {
            $driving_license_front = "";
        }

        if ($request->has('driving_license_back')) {
            $file = $request->file('driving_license_back');
            $driving_license_back_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $driving_license_back_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $driving_license_back = env('S3_BUCKET_URL') . $filePath;
        } else {
            $driving_license_back = "";
        }

        if ($request->has('police_verification')) {
            $file = $request->file('police_verification');
            $police_verification = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $police_verification;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $police_verification_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            $police_verification_url = "";
        }

        if ($request->has('pan_card')) {
            $file = $request->file('pan_card');
            $pan_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $pan_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $pan_card_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            $pan_card_url = "";
        }

        if ($request->has('adhar_card')) {
            $file = $request->file('adhar_card');
            $adhar_card = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $adhar_card;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $adhar_card_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            $adhar_card_url = "";
        }

        if ($request->has('cheque_book')) {
            $file = $request->file('cheque_book');
            $cheque_book = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $cheque_book;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $cheque_book_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            $cheque_book_url = "";
        }


        /* https://pulpit-media-storage.s3-ap-south-1.amazonaws.com/image_cropper_1633957680100.jpg */

        /* Agent & Travel Agency*/
        if ($usertype == '2' || $usertype == '3') {
            if (!empty($request->get('profile_id'))) {
                $profile_id = $request->get('profile_id');
                $newrecord = AgentUsers::find($profile_id);
            } else {
                $newrecord = new AgentUsers();
            }
            $newrecord->user_type_id = $usertype;
            $newrecord->travel_name = $travel_name;
            $newrecord->owner_name = $owner_name;
            $newrecord->office_no = $office_no;
            $newrecord->total_business_year = $total_year_of_business;
            $newrecord->city_id = $city_id;
            $newrecord->logo = $agent_logo;
            $newrecord->pan_card = $pan_card_no;
            $newrecord->adhar_card = $adhar_card_no;
            if (!empty($pan_card_url)) {
                $newrecord->pan_card_url = $pan_card_url;
            }

            if (!empty($adhar_card_url)) {
                $newrecord->adhar_card_url = $adhar_card_url;
            }
            //$newrecord->registration_document_url = $registration_document_url;
            //$newrecord->registration_document = $registration_document;
            $newrecord->created_at = date("Y-m-d H:i:s");
            $newrecord->updated_at = date("Y-m-d H:i:s");
            $newrecord->save();

            $profile_id = $newrecord->id;

            $bank_account = new BankAccount();
            $bank_account->account_holder_name = $account_holder_name;
            $bank_account->ifsc_code = $ifsc_code;
            $bank_account->bank_name = $bank_name;
            $bank_account->branch_name = $branch_name;
            $bank_account->account_number = $account_number;
            if (!empty($cheque_book_url)) {
                $bank_account->document_url = $cheque_book_url;
            }
            $bank_account->save();
        } else if ($usertype == '5') { //Driver Cum Owner 
            if (!empty($request->get('profile_id'))) {
                $profile_id = $request->get('profile_id');
                $new = Drivers::find($profile_id);
            } else {
                $new = new Drivers();
            }
            $new->first_name = $first_name;
            $new->last_name = $last_name;
            $new->adhar_card_no = $adhar_card_no;
            $new->driving_licence_no = $license_no;
            $new->driving_licence_expiry_date = $expiry_date;
            $new->street_address = $street_address;
            $new->city = $city_id;
            $new->pincode = $pin_code;

            if (!empty($driving_license_front)) {
                $new->dl_front_url = $driving_license_front;
            }

            if (!empty($driving_license_back)) {
                $new->dl_back_url = $driving_license_back;
            }

            if (!empty($police_verification_url)) {
                $new->police_verification_url = $police_verification_url;
            }
            $new->pan_card_url = $pan_card_url;
            $new->pan_card_number = $pan_card_no;
            $new->adhar_card_url = $adhar_card_url;
            $new->created_at = date("Y-m-d H:i:s");
            $new->updated_at = date("Y-m-d H:i:s");
            $new->save();
            $profile_id = $new->id;

            $bank_account = new BankAccount();
            $bank_account->account_holder_name = $account_holder_name;
            $bank_account->ifsc_code = $ifsc_code;
            $bank_account->bank_name = $bank_name;
            $bank_account->branch_name = $branch_name;
            $bank_account->account_number = $account_number;
            $bank_account->document_url = $cheque_book_url;
            $bank_account->save();
        } else if ($usertype == '4') { //Driver
            $agent_id = $request->get('agent_id');

            if (!empty($request->get('profile_id'))) {
                $profile_id = $request->get('profile_id');
                $new = Drivers::find($profile_id);
            } else {
                $new = new Drivers();
            }
            $new->first_name = $first_name;
            $new->agent_id = $agent_id;
            $new->mobile_numebr = $mobile_numebr;
            $new->last_name = $last_name;
            $new->adhar_card_no = "";
            $new->driving_licence_no = $license_no;
            $new->driving_licence_expiry_date = $expiry_date;
            $new->street_address = $street_address;
            $new->city = $city_id;
            $new->pincode = $pin_code;
            if (!empty($driving_license_front)) {
                $new->dl_front_url = $driving_license_front;
            }

            if (!empty($driving_license_back)) {
                $new->dl_back_url = $driving_license_back;
            }

            if (!empty($police_verification_url)) {
                $new->police_verification_url = $police_verification_url;
            }
            $new->pan_card_url = $pan_card_url;
            $new->pan_card_number = "";
            $new->adhar_card_url = $adhar_card_url;
            $new->created_at = date("Y-m-d H:i:s");
            $new->updated_at = date("Y-m-d H:i:s");
            $new->save();
            $profile_id = $new->id;
        } else {
            $profile_id = $user_id;
        }
        /*  */

        if ($usertype == '6') {
            $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $user_id)->first();
            $profile_id = $user_id;
        } else {
            $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $profile_id)->first();
        }

        if (empty($UserWorkProfile)) {
            $insert = new UserWorkProfile();
            $insert->user_id = $user_id;
            $insert->user_type_id = $usertype;
            $insert->profile_id = $profile_id;
            $insert->save();
        }

        if (!empty($request->get('id'))) {
            $message = 'Customer Updated Successfully';
            return redirect(route('admin.customers.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'Customer Added Successfully';
            return redirect(route('admin.customers.otp', ['panel' => Session::get('panel'), 'id' => $user_id]))->withMessage($message);
        }

        /* $log_update = PortalActivities::find($log_id);
        $log_update->response_data = $message;
        $log_update->save(); */

        //$message = 'Status change successfully!';
        //return redirect(route('admin.customers.index', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);

    }

    public function statecity()
    {
        $id = $_POST['id'];
        $data = Cities::where('stateCode', $id)->get();
        $s = '<select class="form-control" name="city_id" id="city_id" required=""><option value="" label="Choose one"></option>';
        foreach ($data as $val) {
            $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
        }
        $s .= '</select>';
        echo $s;
        exit;
    }

    public function models()
    {
        $id = $_POST['id'];
        $data = VehicleBrandModels::where('brand_id', $id)->get();
        $s = '<select class="form-control" name="city_id" id="city_id" required=""><option value="" label="Choose one"></option>';
        foreach ($data as $val) {
            $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
        }
        $s .= '</select>';
        echo $s;
        exit;
    }

    public function vehicleFuelType()
    {
        $id = $_POST['id'];
        $data = VehicleFuelType::where('model_id', $id)->get();
        $s = '<select class="form-control" name="fuel_type_id" id="fuel_type_id" required=""><option value="" label="Choose one"></option>';
        foreach ($data as $val) {
            $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
        }
        $s .= '</select>';
        echo $s;
        exit;
    }

    public function vehicleColour()
    {
        $id = $_POST['id'];
        $data = VehicleColour::where('model_id', $id)->get();
        $s = '<select class="form-control" name="colour_id" id="colour_id" required=""><option value="" label="Choose one"></option>';
        foreach ($data as $val) {
            $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
        }
        $s .= '</select>';
        echo $s;
        exit;
    }

    public function show($panel, $id)
    {
        $customer = Auth::user();
        $role_id = $customer['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['customer_id'] = $id;
        $params['response_type'] = "single";
        $customer = $this->customerRepository->getByParams($params);
        $plan = UserPurchasedPlans::where('user_id', $id)->orderBy('id', 'DESC')->first();
        $plan_details = array();
        if (!empty($plan)) {
            $subscription_plan_id = $plan->subscription_plan_id;
            $plan_details = SubscriptionPlans::find($subscription_plan_id);
        }

        $earnings = Earnings::where('user_id', $id)->sum('comission');
        $customer_trip = TripBookings::where('customer_id', $id)->count();
        $driver_trip = TripBookings::where('driver_id', $id)->count();
        $money_spent = Earnings::where('user_id', $id)->sum('fare');

        $countcompany['user_id'] = $id;
        $vehicles = $this->vehiclesRepository->getByParams($countcompany);
        //$vehicles = Vehicles::where('user_id',$id)->get();
        //$cabs = Cabs::where('user_id',$id)->get();

        $trips = TripBookings::select('trip_post_master.id as trip_id', 'trip_post_master.trip_type as trip_type','cab_post.cab_post_type as cab_type',
        DB::raw("CONVERT_TZ(trip_post_master.created_at,'+00:00','+05:30') as date"),'trip_bookings.trip_end_datetime as return_date','trip_bookings.trip_start_datetime as pickup_date',
        'trip_post_master.pickup_location','trip_bookings.trip_status','trip_bookings.fare as fare')->join('trip_post_master', 'trip_bookings.trip_id','trip_post_master.id')
        ->join('cab_post', 'trip_bookings.cab_id','cab_post.id')->where('trip_bookings.customer_id', $id)->where('trip_bookings.trip_status','3')->get();
        
        $runningTrips = TripBookings::select('trip_post_master.id as trip_id', 'trip_post_master.trip_type as trip_type','cab_post.cab_post_type as cab_type',
        DB::raw("CONVERT_TZ(trip_post_master.created_at,'+00:00','+05:30') as date"),'trip_bookings.trip_end_datetime as return_date','trip_bookings.trip_start_datetime as pickup_date',
        'trip_post_master.pickup_location','trip_bookings.trip_status','trip_bookings.fare as fare')->join('trip_post_master', 'trip_bookings.trip_id','trip_post_master.id')
        ->join('cab_post', 'trip_bookings.cab_id','cab_post.id')->where('trip_bookings.customer_id', $id)->where('trip_bookings.trip_status','1')->get();

        $completedTrips = TripBookings::select('trip_post_master.id as trip_id', 'trip_post_master.trip_type as trip_type','cab_post.cab_post_type as cab_type',
        DB::raw("CONVERT_TZ(trip_post_master.created_at,'+00:00','+05:30') as date"),'trip_bookings.trip_end_datetime as return_date','trip_bookings.trip_start_datetime as pickup_date',
        'trip_post_master.pickup_location','trip_bookings.trip_status','trip_bookings.fare as fare')->join('trip_post_master', 'trip_bookings.trip_id','trip_post_master.id')
        ->join('cab_post', 'trip_bookings.cab_id','cab_post.id')->where('trip_bookings.customer_id', $id)
        ->where(function ($query) {
            $query->where('trip_bookings.trip_status', '2')->orWhere('trip_bookings.trip_status','1');
        })->get();
        $stateName = [];
        $cityName = [];
        if(isset($customer->state_id)){
            $stateName = States::where('id',$customer->state_id)->first();
        }
        if(isset($customer->city_id)){
            $cityName = Cities::where('id',$customer->city_id)->first();
        }
        $address = '';
        if($stateName && $cityName){
            $address = $stateName->name . ',' . $cityName;
        }elseif($stateName){
            $address = $stateName->name;
        }elseif($cityName){
            $address = $cityName->name;
        }else{
            $address = '-';
        }
        return view('admin.modules.customers.show', [
            'user' => $customer, 
            'user_role' => $this->user_role, 
            'plan' => $plan, 
            'plan_details' => $plan_details, 
            'earnings' => $earnings, 
            'customer_trip' => $customer_trip, 
            'driver_trip' => $driver_trip, 
            'money_spent' => $money_spent, 
            'referrals_done' => '0', 
            'trip_status' => 'Completed', 
            'current_location' => $address, 
            'subscription_status' => 'Active', 
            'vehicles' => $vehicles, 
            'trips' => $trips, 
            'runningTrips' => $runningTrips, 
            'completedTrips' => $completedTrips
        ]);
    }

    public function otp($panel, $id)
    {
        $customer = Auth::user();
        $role_id = $customer['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $customer = $this->customerRepository->getByParams($params);

        return view('admin.modules.customers.otp', [
            'mobile_number' => $customer->mobile_number, 'id' => $id
        ]);
    }

    public function resendotp($panel, $id)
    {
        $customer = Auth::user();
        $role_id = $customer['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $customer = Customer::find($id);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/+91' . $customer->mobile_number . '/AUTOGEN/OTP',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $otp_result = json_decode($response, true);
        if ($otp_result['Status'] == 'Success') {

            $customer->is_otp = '2';
            $customer->otp = $otp_result['Details'];
            $customer->save();
        }

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $customer = $this->customerRepository->getByParams($params);

        /* return view('admin.modules.user.otp', [
            'mobile_number' => $customer->mobile_number,'id'=>$id
        ]); */

        $message = 'OTP sent successfully.';
        return redirect(route('admin.modules.customers.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }


    public function sendotp(Request $request)
    {
        $customer = Auth::user();

        $id = $request->get('id', null);
        $customer = Customer::find($id);
        $sessionId = $customer->otp;

        $otp = $request->get('otp');

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/VERIFY/' . $sessionId . '/' . $otp,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $otp_result = json_decode($response, true);
        if ($otp_result['Status'] == 'Success' && $otp_result['Details'] == 'OTP Matched') {
            $message = 'OTP verified Successfully';
            $customer->is_otp = '1';
            $customer->save();
            return redirect(route('admin.customers.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'OTP does not match!';
            return redirect(route('admin.modules.customers.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        }
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request)
    {
        $id = $request->get('id');
        $customer = Customer::find($id);
        $customer->user_status = $request->get('user_status');
        $customer->save();

        /* return redirect()->back()->withMessage('message', 'Status change successfully!'); */

        $message = 'Status change successfully!';
        return redirect(route('admin.modules.customers.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    /**
     * Reset attempt
     */
    public function resetAttempt(Request $request)
    {
        $id = $request->get('user_id');
        $customer = Customer::find($id);
        $customer->login_attempt = $request->get('login_attempt');
        $customer->save();

        $message = 'Attemp reset successfully!';
        echo "success";
        //return redirect(route('admin.customers.show', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
    }

    /**
     * Reset Otp attempt
     */
    public function resetOtpAttempt(Request $request)
    {
        $id = $request->get('user_id');
        $customer = Customer::find($id);
        $customer->otp_attempt = '0';
        $customer->save();

        $message = 'OTP attempt reset successfully!';
        echo "success";
        //return redirect(route('admin.customers.show', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
    }

    /**
     * USSD Status
     */
    public function changeUssdStatus(Request $request)
    {
        $id = $request->get('user_id');
        $ussd_enable = $request->get('ussd_enable');
        $customer = Customer::find($id);
        $customer->ussd_enable = $ussd_enable;
        $customer->save();

        $message = 'USSD status change successfully.';
        echo "success";
    }


    /**
     * Document verification
     */
    public function documentVerification(Request $request)
    {
        $customer = Auth::user();
        $last_updated_id = $customer['id'];

        $id = $request->get('id');
        $key = $request->get('key');
        $type = $request->get('type');
        $status = $request->get('status');
        $user_id = $request->get('user_id');

        $lastUpdate = Customer::find($user_id);
        $lastUpdate->last_updated_id = $last_updated_id;
        $lastUpdate->save();

        if ($type == 'agent') {
            $update = AgentUsers::find($id);
            $update->$key = $status;
            $update->save();

            $pan_card_url_status = $update->pan_card_url_status;
            $adhar_card_url_status = $update->adhar_card_url_status;
            $registration_document_url_status = $update->registration_document_url_status;
            $adhar_card_back_url_status = $update->adhar_card_back_url_status;
            $logo_status = $update->logo_status;
            /* if($logo_status == NULL){
                if($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $registration_document_url_status == 1 && $adhar_card_back_url_status == 1){
            }else{
                if($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $registration_document_url_status == 1 && $adhar_card_back_url_status == 1 && $logo_status == 1){
            } */

            if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $registration_document_url_status == 1 && $adhar_card_back_url_status == 1) {

                if ($logo_status == NULL) {
                    $update1 = AgentUsers::find($id);
                    $update1->all_document_verify = '1';
                    $update1->save();
                } else {
                    if ($logo_status == 1) {
                        $update1 = AgentUsers::find($id);
                        $update1->all_document_verify = '1';
                        $update1->save();
                    }
                }

                //$profile_id = $update['profile_id'];
                $work = UserWorkProfile::where('user_type_id', '=', '2')->where('profile_id', '=', $id)->first();
                if (!empty($work)) {
                    $customers = Customer::find($work->user_id);
                    if (!empty($customers)) {
                        $device_type = $customers->device_type;
                        $device_token = $customers->device_token;

                        if (!empty($device_token)) {
                            $notificationText = "Your document verified successfully.";
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                            /* echo "coming";
                            print_r($r); */

                            $plan_details = UserPurchasedPlans::where('user_id', '=', $work->user_id)->first();
                            if (!empty($plan_details)) {
                                $plan_id = $plan_details['id'];
                                $subscription_plan_id = $plan_details['subscription_plan_id'];
                                $subs = SubscriptionPlans::find($subscription_plan_id);
                                if (!empty($subs)) {
                                    $plan_validity = $subs['plan_validity'];

                                    $today = date("Y-m-d H:i:s");
                                    $next_due_date = date('Y-m-d H:i:s', strtotime("+$plan_validity days"));

                                    $update_plan = UserPurchasedPlans::find($plan_id);
                                    $update_plan->start_datetime = $today;
                                    $update_plan->end_datetime = $next_due_date;
                                    $update_plan->save();
                                }
                            }
                        }
                    }
                }
                //exit;
            } else if ($pan_card_url_status == 0 && $adhar_card_url_status == 0 && $registration_document_url_status == 0 && $adhar_card_back_url_status == 0 && $logo_status == 0) {
                //$profile_id = $update->profile_id;
                $work = UserWorkProfile::where('user_type_id', '=', '2')->where('profile_id', '=', $id)->first();
                if (!empty($work)) {
                    $customers = Customer::find($work->user_id);
                    if (!empty($customers)) {
                        $device_type = $customers->device_type;
                        $device_token = $customers->device_token;

                        if (!empty($device_token)) {
                            $notificationText = "Your document verification was rejected.";
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                        }
                    }
                }
            }
        } else if ($type == 'driver') {
            $update = Drivers::find($id);
            $update->$key = $status;
            $update->save();

            $dl_front_url_status = $update->dl_front_url_status;
            $dl_back_url_status = $update->dl_back_url_status;
            $police_verification_url_status = $update->police_verification_url_status;
            $d_pan_card_url_status = $update->d_pan_card_url_status;
            $d_adhar_card_url_status = $update->d_adhar_card_url_status;
            $d_adhar_card_back_url_status = $update->d_adhar_card_back_url_status;
            //$bank_document_url_status = $update->bank_document_url_status;
            if ($dl_front_url_status == 1 && $dl_back_url_status == 1 && $d_pan_card_url_status == 1 && $d_adhar_card_url_status == 1 && $d_adhar_card_back_url_status == 1) {
                $update1 = Drivers::find($id);
                $update1->all_document_verify = '1';
                $update1->save();

                //if($bank_document_url_status == 1){
                $driver_id = $update1->id;
                $work = VehicleDrivingMapping::where('driver_id', '=', $driver_id)->first();

                if (!empty($work)) {
                    $vehicle_id = $work->vehicle_id;
                    $vehicles = Vehicles::find($vehicle_id);
                    if (!empty($vehicles)) {
                        $customers = Customer::find($vehicles->user_id);
                        if (!empty($customers)) {
                            $device_type = $customers->device_type;
                            $device_token = $customers->device_token;

                            if (!empty($device_token)) {
                                $notificationText = "Your document verified successfully.";
                                $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                            }

                            $plan_details = UserPurchasedPlans::where('user_id', '=', $vehicles->user_id)->first();
                            if (!empty($plan_details)) {
                                $plan_id = $plan_details['id'];
                                $subscription_plan_id = $plan_details['subscription_plan_id'];
                                $subs = SubscriptionPlans::find($subscription_plan_id);
                                if (!empty($subs)) {
                                    $plan_validity = $subs['plan_validity'];

                                    $today = date("Y-m-d H:i:s");
                                    $next_due_date = date('Y-m-d H:i:s', strtotime("+$plan_validity days"));

                                    $update_plan = UserPurchasedPlans::find($plan_id);
                                    $update_plan->start_datetime = $today;
                                    $update_plan->end_datetime = $next_due_date;
                                    $update_plan->save();
                                }
                            }
                        }
                    }
                }
                //}
            } else if ($dl_front_url_status == 0 && $dl_back_url_status == 0 && $d_pan_card_url_status == 0 && $d_adhar_card_url_status == 0 && $d_adhar_card_back_url_status == 0) {

                $driver_id = $update->id;
                $work = VehicleDrivingMapping::where('driver_id', '=', $driver_id)->first();

                if (!empty($work)) {
                    $vehicle_id = $work->vehicle_id;
                    $vehicles = Vehicles::find($vehicle_id);
                    if (!empty($vehicles)) {
                        $customers = Customer::find($vehicles->user_id);
                        if (!empty($customers)) {
                            $device_type = $customers->device_type;
                            $device_token = $customers->device_token;

                            if (!empty($device_token)) {
                                $notificationText = "Your document verification was rejected.";
                                $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                            }
                        }
                    }
                }
            }
        } else if ($type == 'bank_account') {
            $update = BankAccount::find($id);
            $update->$key = $status;
            $update->save();
        } else if ($type == 'vehicle' || $type == 'vehicles') {
            $update = Vehicles::find($id);
            $update->$key = $status;
            $update->save();

            $rc_front_url_status = $update->rc_front_url_status;
            $rc_back_url_status = $update->rc_back_url_status;
            $insurance_doc_url_status = $update->insurance_doc_url_status;
            $permit_doc_url_status = $update->permit_doc_url_status;
            $fitness_doc_url_status = $update->fitness_doc_url_status;
            $puc_doc_url_status = $update->puc_doc_url_status;
            $agreement_doc_url_status = $update->agreement_doc_url_status;

            if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {

                $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $id)->where('vehicle_photos_view_master_id', '1')->first();
                if (!empty($vehiclePhoto)) {
                    $vehiclePhotoId = $vehiclePhoto['id'];

                    $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                    $image_url_status = $update2->image_url_status;
                    if ($image_url_status == 1) {
                        $update1 = Vehicles::find($id);
                        $update1->all_document_verify = '1';
                        $update1->save();

                        $customers = Customer::find($update1->user_id);
                        if (!empty($customers)) {
                            $device_type = $customers->device_type;
                            $device_token = $customers->device_token;

                            if (!empty($device_token)) {
                                $notificationText = "Your document verified successfully.";
                                $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                            }
                        }

                        $plan_details = UserPurchasedPlans::where('user_id', '=', $update1->user_id)->where('vehicle_id', '=', $id)->first();
                        if (!empty($plan_details)) {
                            $plan_id = $plan_details['id'];
                            $subscription_plan_id = $plan_details['subscription_plan_id'];
                            $subs = SubscriptionPlans::find($subscription_plan_id);
                            if (!empty($subs)) {
                                $plan_validity = $subs['plan_validity'];

                                $today = date("Y-m-d H:i:s");
                                $next_due_date = date('Y-m-d H:i:s', strtotime("+$plan_validity days"));

                                $update_plan = UserPurchasedPlans::find($plan_id);
                                $update_plan->start_datetime = $today;
                                $update_plan->end_datetime = $next_due_date;
                                $update_plan->save();
                            }
                        }
                    }
                }
            } else if ($rc_front_url_status == 0 && $rc_back_url_status == 0 && $insurance_doc_url_status == 0) {

                $customers = Customer::find($update->user_id);
                if (!empty($customers)) {
                    $device_type = $customers->device_type;
                    $device_token = $customers->device_token;

                    if (!empty($device_token)) {
                        $notificationText = "Your document verification was rejected.";
                        $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");
                    }
                }
            }
        } else if ($type == 'vehiclePhotoMapping') {
            $update = VehiclePhotoMapping::find($id);
            $update->$key = $status;
            $update->save();

            $vehicle_id = $update->vehicle_id;
            $update1 = Vehicles::find($vehicle_id);
            $rc_front_url_status = $update1->rc_front_url_status;
            $rc_back_url_status = $update1->rc_back_url_status;
            $insurance_doc_url_status = $update1->insurance_doc_url_status;

            if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1 && ($update->image_url_status == 1 && $update->vehicle_photos_view_master_id == 1)) {
                $update1 = Vehicles::find($vehicle_id);
                $update1->all_document_verify = '1';
                $update1->save();

                $customers = Customer::find($update1->user_id);
                if (!empty($customers)) {
                    $device_type = $customers->device_type;
                    $device_token = $customers->device_token;

                    if (!empty($device_token)) {
                        $notificationText = "Your document verified successfully.";
                        $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit",);
                    }
                }

                $plan_details = UserPurchasedPlans::where('user_id', '=', $update1->user_id)->where('vehicle_id', '=', $vehicle_id)->first();
                if (!empty($plan_details)) {
                    $plan_id = $plan_details['id'];
                    $subscription_plan_id = $plan_details['subscription_plan_id'];
                    $subs = SubscriptionPlans::find($subscription_plan_id);
                    if (!empty($subs)) {
                        $plan_validity = $subs['plan_validity'];

                        $today = date("Y-m-d H:i:s");
                        $next_due_date = date('Y-m-d H:i:s', strtotime("+$plan_validity days"));

                        $update_plan = UserPurchasedPlans::find($plan_id);
                        $update_plan->start_datetime = $today;
                        $update_plan->end_datetime = $next_due_date;
                        $update_plan->save();
                    }
                }
            }
        } else if ($type == 'user') {
            $update = Customer::find($id);
            $update->$key = $status;
            $update->save();
        }

        $this->updateApprovedRequest($user_id);

        echo "success";
    }

    public function changeImage(Request $request)
    {

        $id = $request->get('id');
        $type = $request->get('hidd_type');
        $pk_id = $request->get('pk_id');
        $pk_key = $request->get('pk_key');
        $image_key_val = $request->get('image_key_val');
        $module_name = $request->get('module_name');

        $customer = Auth::user();
        /* echo "<pre>";
        print_r($customer);
        exit; */
        $last_updated_id = $customer['id'];

        if ($type == 'agent' || $type == 'bank_account' || $type == 'driver' || $type == 'user') {
            $lastUpdate = Customer::find($id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();
        }

        if ($type == 'agent') {
            $update = AgentUsers::find($pk_id);

            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }
            $update->all_document_verify = '0';
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            //if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $adhar_card_back_url_status == 1) {

            if ($pk_key == 'pan_card_url_status' || $pk_key == 'adhar_card_url_status' || $pk_key == 'adhar_card_back_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        } else if ($type == 'bank_account') {

            $update = BankAccount::find($pk_id);

            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }
            //$update->all_document_verify = '0';
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            if ($pk_key == 'bank_document_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        } else if ($type == 'driver') {

            $update = Drivers::find($pk_id);
            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }
            $update->all_document_verify = '0';
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            if ($pk_key == 'dl_front_url_status' || $pk_key == 'dl_back_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        } else if ($type == 'vehicles') {
            $update = Vehicles::find($pk_id);
            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }
            $update->all_document_verify = '0';
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            $lastUpdate = Customer::find($update->user_id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();

            if ($pk_key == 'rc_front_url_status' || $pk_key == 'rc_back_url_status' || $pk_key == 'insurance_doc_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $pk_id]))->withMessage($message);
        } else if ($type == 'vehiclePhotoMapping') {

            $update = VehiclePhotoMapping::find($pk_id);

            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }

            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            $vehicles = Vehicles::find($update->vehicle_id);

            $lastUpdate = Customer::find($vehicles->user_id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();

            //
            if ($pk_key == 'image_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $update->vehicle_id]))->withMessage($message);
        } else if ($type == 'user') {
            $update = Customer::find($id);
            if ($request->has($image_key_val)) {
                $file = $request->file($image_key_val);
                $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = "/" . $document_file_name;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
                $agent_logo = env('S3_BUCKET_URL') . $filePath;
                $update->$pk_key = '2';
                $update->$image_key_val = $agent_logo;
            }

            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            $message = 'Image Updated Successfully';
            return redirect(route('admin.' . $module_name . '.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        }
    }

    public function deleteImage(Request $request)
    {

        $id = $request->get('id');
        $type = $request->get('hidd_type');
        $pk_id = $request->get('pk_id');
        $pk_key = $request->get('pk_key');
        $image_key_val = $request->get('image_key_val');
        $module_name = $request->get('module_name');

        $customer = Auth::user();
        $last_updated_id = $customer['id'];

        if ($type == 'agent' || $type == 'bank_account' || $type == 'driver' || $type == 'user') {
            $lastUpdate = Customer::find($id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();
        }

        if ($type == 'agent') {
            $update = AgentUsers::find($pk_id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";
            if ($pk_key == 'pan_card_url_status' || $pk_key == 'adhar_card_url_status' || $pk_key == 'adhar_card_back_url_status') {
                $update->all_document_verify = '2';
            }
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            if ($pk_key == 'pan_card_url_status' || $pk_key == 'adhar_card_url_status' || $pk_key == 'adhar_card_back_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            echo "success";
        } else if ($type == 'bank_account') {

            $update = BankAccount::find($pk_id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            if ($pk_key == 'bank_document_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            echo "success";
        } else if ($type == 'driver') {

            $update = Drivers::find($pk_id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";

            if ($pk_key == 'dl_front_url_status' || $pk_key == 'dl_back_url_status') {
                $update->all_document_verify = '2';
            }
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            if ($pk_key == 'dl_front_url_status' || $pk_key == 'dl_back_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            echo "success";
        } else if ($type == 'vehicles') {
            $update = Vehicles::find($pk_id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";
            if ($pk_key == 'rc_front_url_status' || $pk_key == 'rc_back_url_status' || $pk_key == 'insurance_doc_url_status') {
                $update->all_document_verify = '2';
            }
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            $lastUpdate = Customer::find($update->user_id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();

            if ($pk_key == 'rc_front_url_status' || $pk_key == 'rc_back_url_status' || $pk_key == 'insurance_doc_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            echo "success";
        } else if ($type == 'vehiclePhotoMapping') {

            $update = VehiclePhotoMapping::find($pk_id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            $vehicles = Vehicles::find($update->vehicle_id);

            $lastUpdate = Customer::find($vehicles->user_id);
            $lastUpdate->last_updated_id = $last_updated_id;
            $lastUpdate->save();

            //
            if ($pk_key == 'image_url_status') {
                $userUpdate = Customer::find($id);
                $userUpdate->is_approved = 2;
                $userUpdate->save();
            }

            echo "success";
        } else if ($type == 'user') {
            $update = Customer::find($id);
            $update->$pk_key = '2';
            $update->$image_key_val = "";
            $update->updated_at = date("Y-m-d H:i:s");
            $update->save();

            echo "success";
        }
    }

    public function updateApprovedRequest($id)
    {
        //$customers = Customer::where('status',1)->where('type','user')->where('is_approved','2')->offset($offset)->limit($limit)->get();
        $customers = Customer::where('status', 1)->where('type', 'user')->where('is_approved', '2')->where('id', $id)->get();
        //echo "<pre>"; print_r($customers);

        foreach ($customers as $val) {
            $user_id = $val['id'];
            $user_type_id = $val['user_type_id'];

            if ($user_type_id == '2') { //Agent

                $isAgentStatus = 2;
                $isBankStatus = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '2')->where('status', '=', 1)->where('user_id', '=', $user_id)->first();
                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $agent = AgentUsers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($agent)) {
                        $pan_card_url_status = $agent->pan_card_url_status;
                        $adhar_card_url_status = $agent->adhar_card_url_status;
                        $registration_document_url_status = $agent->registration_document_url_status;
                        $adhar_card_back_url_status = $agent->adhar_card_back_url_status;
                        $logo_status = $agent->logo_status;

                        if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $adhar_card_back_url_status == 1) {
                            /* $userUpdate = Customer::find($user_id);
                            $userUpdate->is_approved = 1;
                            $userUpdate->save(); */
                            $isAgentStatus = 1;
                        } elseif ($pan_card_url_status == 0 || $adhar_card_url_status == 0 || $adhar_card_back_url_status == 0) {
                            /* $userUpdate = Customer::find($user_id);
                            $userUpdate->is_approved = 0;
                            $userUpdate->save(); */
                            $isAgentStatus = 0;
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                if ($isAgentStatus == 1 && $isBankStatus == 1) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isAgentStatus == 0 || $isBankStatus == 0) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '3') { //Travel Agency

                $isAgentApproved = 2;
                $isTravelApproved = 2;
                $isBankStatus = 2;

                /* Owner details check in agent user table with 3 user type */
                $agentWork = UserWorkProfile::where('user_type_id', '=', '3')->where('status', 1)->where('user_id', '=', $user_id)->first();
                if (!empty($agentWork)) {
                    $profile_id = $agentWork->profile_id;

                    $agent = AgentUsers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($agent)) {
                        $pan_card_url_status = $agent->pan_card_url_status;
                        $adhar_card_url_status = $agent->adhar_card_url_status;
                        $registration_document_url_status = $agent->registration_document_url_status;
                        $adhar_card_back_url_status = $agent->adhar_card_back_url_status;
                        $logo_status = $agent->logo_status;

                        if ($pan_card_url_status == 1 && $adhar_card_url_status == 1 && $adhar_card_back_url_status == 1) {
                            $isAgentApproved = 1;
                        } elseif ($pan_card_url_status == 0 || $adhar_card_url_status == 0 || $adhar_card_back_url_status == 0) {
                            $isAgentApproved = 0;
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                /* Vehicle details check user with user id */
                $vehicles = Vehicles::where('status', 1)->where('user_id', $user_id)->first();
                if (!empty($vehicles)) {
                    $vehicle_id = $vehicles->id;
                    $rc_front_url_status = $vehicles->rc_front_url_status;
                    $rc_back_url_status = $vehicles->rc_back_url_status;
                    $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                    $permit_doc_url_status = $vehicles->permit_doc_url_status;
                    $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                    $puc_doc_url_status = $vehicles->puc_doc_url_status;
                    $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                    if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {

                        $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                        if (!empty($vehiclePhoto)) {
                            $vehiclePhotoId = $vehiclePhoto['id'];

                            $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                            $image_url_status = $update2->image_url_status;
                            if ($image_url_status == 1) {
                                $isTravelApproved = 1;
                            } else if ($image_url_status == 0) {
                                $isTravelApproved = 0;
                            }
                        }
                    } else if ($rc_front_url_status == 0 || $rc_back_url_status == 0 || $insurance_doc_url_status == 0) {

                        $isTravelApproved = 0;
                        /* $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                        if (!empty($vehiclePhoto)) {
                            $vehiclePhotoId = $vehiclePhoto['id'];

                            $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                            $image_url_status = $update2->image_url_status;
                            if ($image_url_status == 0) {
                                $isTravelApproved = 0;
                            }
                        } */
                    }
                }

                //echo $isAgentApproved."===".$isTravelApproved;

                if ($isAgentApproved == 1 && $isTravelApproved == 1 && $isBankStatus == 1) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isAgentApproved == 0 || $isTravelApproved == 0 || $isBankStatus == 0) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '4') { //Driver

                $isDriverApproved = 2;
                $isVehicleApproved = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '4')->where('status', '=', '1')->where('user_id', '=', $user_id)->first();
                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $drivers = Drivers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($drivers)) {
                        $dl_front_url_status = $drivers->dl_front_url_status;
                        $dl_back_url_status = $drivers->dl_back_url_status;
                        $police_verification_url_status = $drivers->police_verification_url_status;
                        $d_pan_card_url_status = $drivers->d_pan_card_url_status;
                        $d_adhar_card_url_status = $drivers->d_adhar_card_url_status;
                        $d_adhar_card_back_url_status = $drivers->d_adhar_card_back_url_status;

                        if ($dl_front_url_status == 1 && $dl_back_url_status == 1) {
                            $isDriverApproved = 1;
                        } elseif ($dl_front_url_status == 0 || $dl_back_url_status == 0) {
                            $isDriverApproved = 0;
                        }

                        /* $vehicleDrivingMapping = VehicleDrivingMapping::where('driver_id', $profile_id)->where('status','=','1')->first();
                        if(!empty($vehicleDrivingMapping)){
                            $vehicle_id = $vehicleDrivingMapping->vehicle_id;
                            
                            $vehicles = Vehicles::where('status',1)->where('id',$vehicle_id)->first();
                            if (!empty($vehicles)) {
                                $rc_front_url_status = $vehicles->rc_front_url_status;
                                $rc_back_url_status = $vehicles->rc_back_url_status;
                                $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                                $permit_doc_url_status = $vehicles->permit_doc_url_status;
                                $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                                $puc_doc_url_status = $vehicles->puc_doc_url_status;
                                $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                                if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                    $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                    if (!empty($vehiclePhoto)) {
                                        $vehiclePhotoId = $vehiclePhoto['id'];

                                        $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                        $image_url_status = $update2->image_url_status;
                                        if ($image_url_status == 1) {
                                            $isVehicleApproved = 1;
                                        }
                                    }
                                } elseif ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                    $isVehicleApproved = 0;
                                }
                            }
                        } */
                    }
                }

                if ($isDriverApproved == 1) { /* && $isVehicleApproved = 1 */
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isDriverApproved == 0) { /*  && $isVehicleApproved = 0 */
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '5') { //Driver Cum Owner
                $isDriverApproved = 2;
                $isVehicleApproved = 2;
                $isBankStatus = 2;
                $work = UserWorkProfile::where('user_type_id', '=', '5')->where('status', '=', '1')->where('user_id', '=', $user_id)->first();
                if (!empty($work)) {
                    $profile_id = $work->profile_id;

                    $drivers = Drivers::where('status', 1)->where('id', $profile_id)->first();
                    if (!empty($drivers)) {
                        $dl_front_url_status = $drivers->dl_front_url_status;
                        $dl_back_url_status = $drivers->dl_back_url_status;
                        $police_verification_url_status = $drivers->police_verification_url_status;
                        $d_pan_card_url_status = $drivers->d_pan_card_url_status;
                        $d_adhar_card_url_status = $drivers->d_adhar_card_url_status;
                        $d_adhar_card_back_url_status = $drivers->d_adhar_card_back_url_status;

                        if ($dl_front_url_status == 1 && $dl_back_url_status == 1 && $d_pan_card_url_status == 1 && $d_adhar_card_url_status == 1 && $d_adhar_card_back_url_status == 1) {
                            $isDriverApproved = 1;
                        } elseif ($dl_front_url_status == 0 || $dl_back_url_status == 0 || $d_pan_card_url_status == 0 || $d_adhar_card_url_status == 0 || $d_adhar_card_back_url_status == 0) {
                            $isDriverApproved = 0;
                        }

                        $vehicles = Vehicles::where('status', 1)->where('user_id', $user_id)->first();
                        if (!empty($vehicles)) {
                            $vehicle_id = $vehicles->id;
                            $rc_front_url_status = $vehicles->rc_front_url_status;
                            $rc_back_url_status = $vehicles->rc_back_url_status;
                            $insurance_doc_url_status = $vehicles->insurance_doc_url_status;
                            $permit_doc_url_status = $vehicles->permit_doc_url_status;
                            $fitness_doc_url_status = $vehicles->fitness_doc_url_status;
                            $puc_doc_url_status = $vehicles->puc_doc_url_status;
                            $agreement_doc_url_status = $vehicles->agreement_doc_url_status;

                            if ($rc_front_url_status == 1 && $rc_back_url_status == 1 && $insurance_doc_url_status == 1) {
                                $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                if (!empty($vehiclePhoto)) {
                                    $vehiclePhotoId = $vehiclePhoto['id'];

                                    $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                    $image_url_status = $update2->image_url_status;
                                    if ($image_url_status == 1) {
                                        $isVehicleApproved = 1;
                                    } else if ($image_url_status == 0) {
                                        $isVehicleApproved = 0;
                                    }
                                }
                            } elseif ($rc_front_url_status == 0 || $rc_back_url_status == 0 || $insurance_doc_url_status == 0) {
                                $isVehicleApproved = 0;
                                /* $vehiclePhoto = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', '1')->first();
                                if (!empty($vehiclePhoto)) {
                                    $vehiclePhotoId = $vehiclePhoto['id'];

                                    $update2 = VehiclePhotoMapping::find($vehiclePhotoId);
                                    $image_url_status = $update2->image_url_status;
                                    if ($image_url_status == 0) {
                                        $isVehicleApproved = 0;
                                    }
                                } */
                            }
                        }
                    }
                }

                /* Check bank details */
                $bankdetails = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankdetails)) {
                    $bank_account_id = $bankdetails->bank_account_id;
                    $bankaccount = BankAccount::where('id', $bank_account_id)->first();
                    if (!empty($bankaccount)) {
                        $bank_document_url_status = $bankaccount->bank_document_url_status;
                        if ($bank_document_url_status == 1) {
                            $isBankStatus = 1;
                        } else if ($bank_document_url_status == 0) {
                            $isBankStatus = 0;
                        }
                    }
                }

                if ($isDriverApproved == 1 && $isVehicleApproved == 1 && $isBankStatus == 1) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 1;
                    $userUpdate->save();
                } else if ($isDriverApproved == 0 || $isVehicleApproved == 0 || $isBankStatus == 1) {
                    $userUpdate = Customer::find($user_id);
                    $userUpdate->is_approved = 0;
                    $userUpdate->save();
                }
            } else if ($user_type_id == '6') {
                $userUpdate = Customer::find($user_id);
                $userUpdate->is_approved = 1;
                $userUpdate->save();
            }
        }
    }

    public function toggleStatus($panel, $id)
    {
        $result = $this->customerRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->customerRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }
        $result = $this->customerRepository->delete($id);
        return (int) $result;
    }

    public function showChangePasswordForm()
    {
        return view('admin.modules.customers.change_password');
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!(\Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with('message', trans('Incorrect current password.'));
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with('message', trans('Password are not match.'));
        }
        //Change Password
        $customer = Auth::user();
        $customer->password = bcrypt($request->get('password'));
        $customer->password_changed_at = Carbon::now();
        $customer->save();

        return redirect()->back()->with("message", trans("Password changed successfully !"));
    }

    public function showChangeProfileForm()
    {
        return view('admin.modules.customers.change_profile');
    }
    public function changeUserProfile(ChangeProfileRequest $request)
    {
        $id = $request->get('id');

        $array['first_name'] = $request->get('first_name');
        $array['last_name'] = $request->get('last_name');
        $array['email'] = $request->get('email');
        $array['mobile_number'] = $request->get('mobile');

        $save = $this->customerRepository->changeProfile($array, $id);
        if ($save) {
            session()->flash('message', 'Details edited successfully');
            return redirect()->back();
        }
    }

    public function uploadprofile(Request $request)
    {
        $profileUpdate = null;
        $uploadPath = config('custom.upload.user.profile');
        $base64_image = request('image');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);

            $file = base64_decode($data);
            $path = $uploadPath . "/" . auth()->user()->id;

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $fileName = $this->uploadRepository->uploadProfile($file, $path);

            if (auth()->user()->profile_image == "") {
                $profileUpdate = event(new ProfileUpdated('profilepic', 1.5));
            } else {
                $customer = Auth::user();
                $customer->profile_image = $fileName;
                $customer->save();

                $profileUpdate = '';
            }

            return [
                'score' => $profileUpdate,
                'image' => auth()->user()->profile_image_formatted,
            ];
        }
    }
}
