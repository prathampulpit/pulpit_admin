<?php

namespace App\Http\Controllers\Driver;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\User;
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
use App\Models\Referrals;
use App\Models\UserBankMapping;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\UserRepository;
use App\Repositories\DriverRepository;
use App\Repositories\VehiclesRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;
use DB;
use App\Models\UserHistory;

class DriverController extends Controller {

    protected $userRepository;

    public function __construct(
            UserRepository $userRepository,
            VehiclesRepository $vehiclesRepository,
            DriverRepository $driverRepository
    ) {
        $this->userRepository = $userRepository;
        $this->vehiclesRepository = $vehiclesRepository;
        $this->driverRepository = $driverRepository;
    }

    public function driverAuthorized(Request $request, $data, $id = null, $ids) {
        dd($data, $id, $ids);
        $user_data = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $ids], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->latest('user_work_profile.created_at')->first();
        if ($user_data->authorised_driver == null || $user_data->authorised_driver = 0) {
            $result = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $ids], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->update(['drivers.authorised_driver' => 1]);
        } else {
            $result = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $ids], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->update(['drivers.authorised_driver' => 0]);
        }
        return (int) $result;
    }

    public function driverAuthorizedStatus(Request $request) {

        // try {
        $user_data = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $request->user_id], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->latest('user_work_profile.created_at')->first();
        if ($user_data->authorised_driver == null || $user_data->authorised_driver = 0) {
            $result = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $request->user_id], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->update(['drivers.authorised_driver' => 1]);
        } else {
            $result = UserWorkProfile::join('drivers', 'user_work_profile.profile_id', 'drivers.id')->where([['user_id', $request->user_id], ['user_type_id', 4]])->select(['drivers.id as id', 'drivers.authorised_driver'])->update(['drivers.authorised_driver' => 0]);
        }
        return redirect()->back();
        // return response()->json(['success' => true]);
        // } catch (\Throwable $th) {
        //     return response()->json(['success' => 404]);
        // }
    }

    public function driverUpdate(Request $request) {
        try {
            $user_id = auth()->user();
            $user_data = json_decode($request->user_data);
            $state_name_data = States::where('name', $user_data->state_name)->first();
            $city_name_data = Cities::where('name', $user_data->city_name)->first();
            $abc = $request->user_data;
            // dd($request->all());
            // dd($user_data);
            $user_name = $user_data->first_name . ' ' . $user_data->last_name;
            $name = explode(" ", $request->user_name);
            $user = array();

            if (!empty($request->user_name)) {
                if ($user_name != $request->user_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $user['first_name'] = $name[0];
            $user['last_name'] = $name[1];

            // dd($user_data->id .'-'.$request->stateid);
            if (!empty($request->stateid)) {
                if ($state_name_data->id != $request->stateid) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "State updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $user['state'] = $request->stateid;
            // dd($city_name_data->id .'-'. $request->city_id);
            if (!empty($request->city_id)) {
                if ($city_name_data->id != $request->city_id) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "City updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $user['city_id'] = $request->city_id;
            User::where('id', $request->user_id)->update($user);

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => 404]);
        }
    }

    public function index($panel, Request $request, $param = null) {
        // dd(1);
        $user = Auth::user();
        $id = $user['id'];

        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("4", $role_id_arr) || $user_role == 'administrator') {
            $profile_path = config('custom.upload.user.profile');
            $file_path = $profile_path;

            $state_id = $request->state_id;
            $plan_id = $request->plan_id;

            $user_type = userType::where('status', '!=', 2)->where('id', '!=', 6)->get();

            $today = date("Y-m-d");
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $week = date('Y-m-d', strtotime("-7 days"));
            $this_month = date('Y-m');
            $last_month = date('Y-m', strtotime(date('Y-m') . " -1 month"));
            $fiften_date = date('Y-m-d', strtotime("-15 days"));

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['today_joined'] = $today;
            $countcompany['type_id'] = '4';
            $today_joined = $this->driverRepository->getByParams($countcompany);

            //$yesterday_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$yesterday."'")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['yesterday_joined'] = $yesterday;
            $yesterday_joined = $this->driverRepository->getByParams($countcompany);

            //$week_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$week."' ")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['week'] = $week;
            $week_joined = $this->driverRepository->getByParams($countcompany);

            //$this_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$this_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['this_month_joined'] = $this_month;
            $this_month_joined = $this->driverRepository->getByParams($countcompany);

            //$last_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$last_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['last_month_joined'] = $last_month;
            $last_month_joined = $this->driverRepository->getByParams($countcompany);

            //$all_joined = User::count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $all_joined = $this->driverRepository->getByParams($countcompany);

            //$active_users = User::whereRaw("user_status = 1")->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$fiften_date."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['user_status'] = '1';
            $countcompany['fiften_date'] = $fiften_date;
            $countcompany['state_id'] = $request->state_id;
            $active_users = $this->driverRepository->getByParams($countcompany);

            //$inactive_users = User::whereRaw("user_status = 0")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['user_status'] = '0';
            $countcompany['state_id'] = $request->state_id;
            $inactive_users = $this->driverRepository->getByParams($countcompany);

            //$paid_users = UserPurchasedPlans::groupBy('user_id')->count();
            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['paid_users'] = '1';
            $countcompany['state_id'] = $request->state_id;
            $paid_users = $this->driverRepository->getByParams($countcompany);
            $unpaid_users = $all_joined - $paid_users;

            /* $expired_users = UserPurchasedPlans::leftJoin('users', function($join) {
              $join->on('user_purchased_plans.user_id', '=', 'users.id');
              })->groupBy('user_purchased_plans.user_id')->whereRaw("DATE_FORMAT(start_datetime, '%Y-%m-%d') < '".$today."' AND DATE_FORMAT(start_datetime, '%Y-%m-%d') > '".$today."' ")->count(); */

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['type_id'] = '4';
            $countcompany['expired_users'] = '1';
            $countcompany['state_id'] = $request->state_id;
            $expired_users = $this->driverRepository->getByParams($countcompany);

            $uninstall_users = '0';

            /* Graph Data */
            $day_name_arr = '';
            $graph_trans_data_arr = '';
            for ($i = 6; $i >= 0; $i--) {
                $day_name_arr .= "'" . date('M', strtotime("-$i month")) . "',";
                $date = date('Y-m', strtotime("-$i month"));

                $countcompany = array();
                $countcompany['count'] = true;
                $countcompany['type_id'] = '4';
                $countcompany['this_month_joined'] = $date;
                $countcompany['state_id'] = $request->state_id;
                $expired_users = $this->driverRepository->getByParams($countcompany);
                $graph_trans_data_arr .= $expired_users . ",";
            }
            $day_name_arr .= '';
            $day_name_arr1 = rtrim($day_name_arr, ",");
            $day_name = $day_name_arr1 . "";

            $graph_trans_data_arr .= '';
            $graph_trans_data_arr1 = rtrim($graph_trans_data_arr, ",");
            $graph_trans_data = $graph_trans_data_arr1;

            $states = States::all();
            $plans = SubscriptionPlans::where('is_agent', '!=', '1')->get();
            $cities = Cities::all();
            return view('admin.modules.driver.index', compact('file_path', 'user_type', 'today_joined', 'yesterday_joined', 'week_joined', 'this_month_joined', 'last_month_joined', 'all_joined', 'active_users', 'inactive_users', 'paid_users', 'unpaid_users', 'expired_users', 'uninstall_users', 'day_name', 'graph_trans_data', 'states', 'plans', 'state_id', 'plan_id', 'param', 'cities'));
        } else {
            abort(403);
        }
    }

    public function index_json($panel, Request $request, $param = null) {
        $user = Auth::user();
        //$in = $this->driverRepository->hieararchy($user, false);
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->driverRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        //$params['register_step'] = '6';
        if (!empty($param)) {
            $params['all_document_verify'] = $param;
        }
        $params['filter'] = $param;
        $users = $this->driverRepository->getPanelPartnerUsers($request, $params);
        /* echo "<pre>";
          print_r($users);
          exit; */
        return $users;
    }

    public function createEdit($panel, $id = null) {

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

            $user = null;
            if ($id) {
                $params = [];
                $params['user_id'] = $id;
                $params['response_type'] = "single";
                $user = $this->driverRepository->getByParams($params);

                $profile_id = $user['profile_id'];
                $user_type_id = $user['user_type_id'];
                $bank_account_id = $user['bank_account_id'];
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
              print_r($user);
              exit; */

            //$agent_users = AgentUsers::all();
            $agent_users = DB::table('agent_users')
                    ->join('user_work_profile', 'user_work_profile.profile_id', '=', 'agent_users.id')
                    ->join('users', 'user_work_profile.user_id', '=', 'users.id')
                    ->select('agent_users.*', 'users.mobile_number')
                    ->get();

            $states = states::all();
            $cities = Cities::all();
            $brands = VehicleBrands::where('status', '1')->get();
            $years = array("2020", "2021", "2022");
            return view('admin.modules.driver.store', [
                'user' => $user,
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

    public function store(Request $request) {
        $user = Auth::user();
        /* $role_id = $user['role_id'];
          $admin_id = $user['id'];
          $admin_name = $user['first_name'].' '.$user['last_name'];
          $notes = 'Verified by '.$admin_name;

          $role = Roles::find($role_id);
          $user_role = $role['slug'];
          $loginsert = new PortalActivities();
          $loginsert->user_id = $user['id'];
          $loginsert->module_name = 'Edit User';
          $loginsert->request_data = json_encode($_POST);
          $loginsert->response_data = 'NA';
          $loginsert->save();
          $log_id = $loginsert->id; */


        //$id = $request->get('id');

        /* $email = $request->get('email');
          if(!empty($email)){
          $check_email = User::where('email', $email)->where('id', '!=', $id)->first();
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
        $agent_id = $request->agent_id;
        $user = UserWorkProfile::where('profile_id', $agent_id)->first();
        // if (!empty($request->get('id'))) {
        //     $user = User::find($id);
        // } else {
        //     $user = new User();
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/+91' . $mobile_number . '/AUTOGEN/OTP',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'GET',
        // ));
        // $response = curl_exec($curl);
        // curl_close($curl);
        // $otp_result = json_decode($response, true);
        // if ($otp_result['Status'] == 'Success') {
        // $user->is_otp = '2';
        // $user->otp = $otp_result['Details'];
        // }
        // }
        // $user->emailid = $emailid;
        // $user->first_name = $first_name;
        // $user->last_name = $last_name;
        // $user->mobile_number = $mobile_number;
        // $user->gender = $gender;
        // $user->state = $state;
        // $user->city_id = $city_id;
        // $user->created_at = date("Y-m-d H:i:s");
        // $user->updated_at = date("Y-m-d H:i:s");
        // $user->save();

        if (!empty($request->get('id'))) {
            $user_id = $id;
        } else {
            $user_id = $user->user_id;
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

        /* Agent & Travel Agency *///Driver Cum Owner  //Driver        $agent_id = $request->get('agent_id');

        if (!empty($request->get('profile_id'))) {
            $profile_id = $request->get('profile_id');
            $new = Drivers::find($profile_id);
        } else {
            $new = new Drivers();
        }
        $new->first_name = $first_name;
        $new->agent_id = $agent_id;
        $new->mobile_numebr = $mobile_number;
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
            $message = 'User Updated Successfully';
            return redirect(route('admin.driver.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'User Added Successfully';
            return redirect(route('admin.driver.index', ['panel' => Session::get('panel')]))->withMessage($message);
        }

        /* $log_update = PortalActivities::find($log_id);
          $log_update->response_data = $message;
          $log_update->save(); */

        //$message = 'Status change successfully!';
        //return redirect(route('admin.partner.index', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
    }

    public function statecity() {
        $id = $_POST['id'];
        $data = Cities::where('stateCode', $id)->get();
        dd($data);
        $s = '<select class="form-control" name="city_id" id="city_id" required=""><option value="" label="Choose one"></option>';
        foreach ($data as $val) {
            $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
        }
        $s .= '</select>';
        echo $s;
        exit;
    }

    public function models() {
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

    public function vehicleFuelType() {
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

    public function vehicleColour() {
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

    public function show($panel, $id) {

        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->driverRepository->getUserDetails($params);
        /* echo "<pre>";
          print_r($user);
          exit; */

        $last_updated_id = $user->last_updated_id;
        $last_updated_user_details = User::find($last_updated_id);

        $user_work_profiles_for_agent = UserWorkProfile::where('user_id', $id)->where('status', '1')->whereIn('user_type_id', array(3))->orderBy('id', 'DESC')->first();
        /* echo "<pre>";
          print_r($user_work_profiles_for_agent);
          exit; */

        $agent_details = array();
        if (!empty($user_work_profiles_for_agent)) {
            $profile_id = $user_work_profiles_for_agent['profile_id'];

            //$agent_details = AgentUsers::find($profile_id);
            $agent_details = AgentUsers::where('id', $profile_id)->where('status', 1)->first();
        }

        $user_work_profiles_for_drivers = UserWorkProfile::where('user_id', $id)->where('status', '1')->whereIn('user_type_id', array(4))->orderBy('id', 'DESC')->first();

        $driver_details = array();
        if (!empty($user_work_profiles_for_drivers)) {
            $profile_id = $user_work_profiles_for_drivers['profile_id'];

            $driver_details = Drivers::find($profile_id);
        }

        /* echo "<pre>";
          print_r($driver_details);
          exit; */

        $banks = UserBankMapping::select('bank_account.*')->leftJoin('bank_account', function ($join) {
                    $join->on('user_bank_mapping.bank_account_id', '=', 'bank_account.id');
                })->where('user_bank_mapping.user_id', $id)->first();

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
        $countcompany['status'] = '1';
        $vehicles = $this->vehiclesRepository->getByParams($countcompany);
        //$vehicles = Vehicles::where('user_id',$id)->get();
        //$cabs = Cabs::where('user_id',$id)->get();

        $cabs = Cabs::select('cab_post.*', 'vehicles.vehicle_number')->leftJoin('vehicles', function ($join) {
                    $join->on('cab_post.vehicle_id', '=', 'vehicles.id');
                })->where('cab_post.user_id', $id)->get();

        /* if($user->user_type_id == '3'){

          } */
        $drivers = Drivers::where('agent_id', $id)->where('status', '1')->get();

        $states = States::all();

        $plans = SubscriptionPlans::where('is_agent', '=', '1')->get();

        //Referrals
        $referrals = Referrals::select('referrals.*', 'users.first_name', 'users.last_name', 'users.mobile_number', 'user_type.name as user_type_name')
                        ->join('users', function ($join) {
                            $join->on('referrals.user_id', '=', 'users.id');
                        })->join('user_work_profile', function ($join) {
                    $join->on('referrals.user_id', '=', 'user_work_profile.user_id');
                })->join('user_type', function ($join) {
                    $join->on('user_work_profile.user_type_id', '=', 'user_type.id');
                })->where('referrals.reference_by', $user['id'])->get();

        $profile_path = config('custom.upload.user.profile');
        $cities = Cities::all();
        $user_history = UserHistory::where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        return view('admin.modules.driver.show', [
            'user' => $user,
            'user_role' => $this->user_role,
            'cities' => $cities,
            'user_history' => $user_history,
            'states' => $states,
            'plan' => $plan,
            'plan_details' => $plan_details,
            'earnings' => $earnings,
            'customer_trip' => $customer_trip,
            'driver_trip' => $driver_trip,
            'money_spent' => $money_spent,
            'referrals_done' => '0',
            'trip_status' => 'Completed',
            'current_location' => 'Ahm',
            'subscription_status' => 'Active',
            'vehicles' => $vehicles,
            'cabs' => $cabs,
            'drivers' => $drivers,
            'states' => $states,
            'plans' => $plans,
            'referrals' => $referrals,
            'agent_details' => $agent_details,
            'driver_details' => $driver_details,
            'banks' => $banks,
            'last_updated_user_details' => $last_updated_user_details
        ]);
    }

    public function driverEdit(Request $request) {
        dd($request->first_name);
        $driver = array();
        $driver->mobile_numebr = $request->driver_mobile_numebr;
        // $driver->first_name = $request->
        // $driver->last_name = $request->
        $driver->adhar_card_no = $request->adhar_card_no;
        $driver->driving_licence_no = $request->driving_licence_no;
        $driver->driving_licence_expiry_date = $request->driving_licence_expiry_date;
        $driver->street_address = $request->street_address;

        Drivers::where('id', $request->drivers_id)->update($driver);
    }

    public function otp($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->driverRepository->getByParams($params);

        return view('admin.modules.partner.otp', [
            'mobile_number' => $user->mobile_number,
            'id' => $id
        ]);
    }

    public function resendotp($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $user = User::find($id);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/+91' . $user->mobile_number . '/AUTOGEN/OTP',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
                )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        $otp_result = json_decode($response, true);
        if ($otp_result['Status'] == 'Success') {

            $user->is_otp = '2';
            $user->otp = $otp_result['Details'];
            $user->save();
        }

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->userRepository->getByParams($params);

        /* return view('admin.modules.user.otp', [
          'mobile_number' => $user->mobile_number,'id'=>$id
          ]); */

        $message = 'OTP sent successfully.';
        return redirect(route('admin.partner.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    public function sendotp(Request $request) {
        $user = Auth::user();

        $id = $request->get('id', null);
        $user = User::find($id);
        $sessionId = $user->otp;

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
                )
        );

        $response = curl_exec($curl);
        curl_close($curl);

        $otp_result = json_decode($response, true);
        if ($otp_result['Status'] == 'Success' && $otp_result['Details'] == 'OTP Matched') {
            $message = 'OTP verified Successfully';
            $user->is_otp = '1';
            $user->save();
            return redirect(route('admin.partner.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'OTP does not match!';
            return redirect(route('admin.partner.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        }
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request) {
        $id = $request->get('id');
        $user = User::find($id);
        $user->user_status = $request->get('user_status');
        $user->save();

        /* return redirect()->back()->withMessage('message', 'Status change successfully!'); */

        $message = 'Status change successfully!';
        return redirect(route('admin.partner.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    /**
     * Reset attempt
     */
    public function resetAttempt(Request $request) {
        $id = $request->get('user_id');
        $user = User::find($id);
        $user->login_attempt = $request->get('login_attempt');
        $user->save();

        $message = 'Attemp reset successfully!';
        echo "success";
        //return redirect(route('admin.users.show', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
    }

    /**
     * Reset Otp attempt
     */
    public function resetOtpAttempt(Request $request) {
        $id = $request->get('user_id');
        $user = User::find($id);
        $user->otp_attempt = '0';
        $user->save();

        $message = 'OTP attempt reset successfully!';
        echo "success";
        //return redirect(route('admin.users.show', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
    }

    /**
     * USSD Status
     */
    public function changeUssdStatus(Request $request) {
        $id = $request->get('user_id');
        $ussd_enable = $request->get('ussd_enable');
        $user = User::find($id);
        $user->ussd_enable = $ussd_enable;
        $user->save();

        $message = 'USSD status change successfully.';
        echo "success";
    }

    /**
     * Document verification
     */
    public function documentVerification(Request $request) {
        $id = $request->get('id');
        $key = $request->get('key');
        $type = $request->get('type');
        $status = $request->get('status');

        if ($type == 'agent') {
            $update = AgentUsers::find($id);
            $update->$key = $status;
            $update->save();
        } else if ($type == 'driver') {
            $update = Drivers::find($id);
            $update->$key = $status;
            $update->save();
        } else if ($type == 'bank_account') {
            $update = BankAccount::find($id);
            $update->$key = $status;
            $update->save();
        } else if ($type == 'vehicle') {
            $update = Vehicles::find($id);
            $update->$key = $status;
            $update->save();
        } else if ($type == 'vehiclePhotoMapping') {
            $update = VehiclePhotoMapping::find($id);
            $update->$key = $status;
            $update->save();
        }

        echo "success";
    }

    public function toggleStatus($panel, $id) {
        $result = $this->userRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id) {
        $result = $this->userRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id) {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }
        $result = $this->userRepository->delete($id);
        return (int) $result;
    }

    public function showChangePasswordForm() {
        return view('admin.modules.user.change_password');
    }

    public function changePassword(ChangePasswordRequest $request) {
        if (!(\Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with('message', trans('Incorrect current password.'));
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with('message', trans('Password are not match.'));
        }
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->back()->with("message", trans("Password changed successfully !"));
    }

    public function showChangeProfileForm() {
        return view('admin.modules.partner.change_profile');
    }

    public function changeUserProfile(ChangeProfileRequest $request) {
        $id = $request->get('id');

        $array['first_name'] = $request->get('first_name');
        $array['last_name'] = $request->get('last_name');
        $array['email'] = $request->get('email');
        $array['mobile_number'] = $request->get('mobile');

        $save = $this->userRepository->changeProfile($array, $id);
        if ($save) {
            session()->flash('message', 'Details edited successfully');
            return redirect()->back();
        }
    }

    public function uploadprofile(Request $request) {
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
                $user = Auth::user();
                $user->profile_image = $fileName;
                $user->save();

                $profileUpdate = '';
            }

            return [
                'score' => $profileUpdate,
                'image' => auth()->user()->profile_image_formatted,
            ];
        }
    }

    public function save(Request $request) {
        $user = Auth::user();

        $id = $request->get('id', null);
        $user_id = $request->get('user_id', null);

        $mobile_number = $request->get('mobile_number');
        $first_name = $request->get('first_name');
        $father_name = $request->get('father_name');

        $last_name = $request->get('last_name');
        $name = $first_name . " " . $last_name;
        $street_address = $request->get('street_address');
        $license_no = $request->get('license_no');
        $emailid = $request->get('emailid');

        $state_id = $request->get('state_id');
        $city_id = $request->get('city_id');
        $gender = $request->get('gender');
        $expiry_date = $request->get('expiry_date');
        $expiry_date = date("Y-m-d", strtotime($expiry_date));

        $dob = $request->get('dob');
        $dob = date("d-m-Y", strtotime($dob));

        $issue_date = $request->get('issue_date');
        $issue_date = date("Y-m-d", strtotime($issue_date));
        $pin_code = $request->get('pin_code');
        $year_of_experience = $request->get('year_of_experience');

        $usertype = 4;
       
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

        /* Agent & Travel Agency *///Driver Cum Owner  //Driver        $agent_id = $request->get('agent_id');

        if (!empty($id)) {
            $new = Drivers::find($id);
        } else {
            $new = new Drivers();
        }
        $new->first_name = $first_name;
        $new->agent_id = 0;
        $new->dob = $dob;
        $new->mobile_numebr = $mobile_number;
        $new->last_name = $last_name;
        $new->father_name = $father_name;
        $new->gender = $gender;
        $new->adhar_card_no = "";
        $new->driving_licence_no = $license_no;
        $new->driving_licence_expiry_date = $expiry_date;
        $new->issue_date = $issue_date;
        $new->street_address = $street_address;
        $new->state = $state_id;
        $new->city = $city_id;
        $new->pincode = $pin_code;
        $new->year_of_experience = $year_of_experience;
        if (!empty($driving_license_front)) {
            $new->dl_front_url = $driving_license_front;
        }

        if (!empty($driving_license_back)) {
            $new->dl_back_url = $driving_license_back;
        }

        if (!empty($police_verification_url)) {
            $new->police_verification_url = $police_verification_url;
        }
        $new->pan_card_url = "";
        $new->pan_card_number = "";
        $new->created_at = date("Y-m-d H:i:s");
        $new->updated_at = date("Y-m-d H:i:s");
        $new->save();
         $profile_id = $new->id;

        $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $profile_id)->first();

        if (empty($UserWorkProfile)) {
            $insert = new UserWorkProfile();
            $insert->user_id = $user_id;
            $insert->user_type_id = $usertype;
            $insert->profile_id = $profile_id;
            $insert->status = 1;
            $insert->save();
        }
        /*  */
        if (!empty($id)) {
            $message = 'Driver Updated Successfully';
            echo json_encode(array('status' => true, 'message' => $message));
        } else {
            $message = 'Driver Added Successfully';
            echo json_encode(array('status' => true, 'message' => $message));
        }
 
        return redirect()->back();
    }

    public function getDrivingLicenceDetails(Request $request) {

        $curl = curl_init();
        $driving_license_no = $request->license_no;
        $driver_bod = date('d-m-Y', strtotime($request->driver_bod));

//        $driving_license_no = 'KL2019820001295';
//        $driver_bod = '03-05-1963';



        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://driving-license-verification1.p.rapidapi.com/DL/DLDetails',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{"method":"dlvalidate","txn_id":"9ujh7gdhgs","clientid":"PULPIT_MOB","consent":"Y","dlnumber":"' . $driving_license_no . '","dob":"' . $driver_bod . '"}',
            CURLOPT_HTTPHEADER => array(
                'content-type: application/json',
                'X-RapidAPI-Host: driving-license-verification1.p.rapidapi.com',
                'X-RapidAPI-Key: 333ee70dc4msh1fef8639ef8c6d9p144d24jsnc55290059f47'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

//           $response = '{"Succeeded":{"transaction_status":1,"txn_id":"9ujh7gdhgs-2023-04-29 16:25:10.267","statusMessage":"Success","statusCode":"1","request_timestamp":"2023-04-29 16:25:10.266","response_timestamp":"2023-04-29 16:25:11.192","ref_Id":"b2c7b3be-a9e8-4655-89f4-2da8366bfc1e","data":{"result":{"nationality":"IND","dl_no.":"KL20 19820001295","dob":"03-05-1963","name":"PADMAKUMAR V","s_w_d":"VELAYUDHAN G","gender":"Male","blood_group":"A-","permanent_address":"NARAYANAVILASAM BUNGLOW, AMARAVILA P O, CHENKAL (PART), 695122","temporary_address":"NARAYANAVILASAM BUNGLOW, AMARAVILA P O, CHENKAL (PART), 695122","cov":"MCWG, LMV, TRANS, eRIKSH,","issue_date":"22-11-1982","organ_donar":"No","badge_no":"20/400/83","badgeno_issue_date":"1983-05-16","mobile_number":"******8877","image":"/9j/4AAQSkZJRgABAQEAYABgAAD/4RDSRXhpZgAATU0AKgAAAAgABAE7AAIAAAAETkVXAIdpAAQAAAABAAAISpydAAEAAAAIAAAQwuocAAcAAAgMAAAAPgAAAAAc6gAAAAgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAFkAMAAgAAABQAABCYkAQAAgAAABQAABCskpEAAgAAAAMxMwAAkpIAAgAAAAMxMwAA6hwABwAACAwAAAiMAAAAABzqAAAACAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAMjAyMzowNDoxOSAxMTo1NTo1OQAyMDIzOjA0OjE5IDExOjU1OjU5AAAATgBFAFcAAAD/4QsWaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49J++7vycgaWQ9J1c1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCc/Pg0KPHg6eG1wbWV0YSB4bWxuczp4PSJhZG9iZTpuczptZXRhLyI+PHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj48cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0idXVpZDpmYWY1YmRkNS1iYTNkLTExZGEtYWQzMS1kMzNkNzUxODJmMWIiIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIvPjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSJ1dWlkOmZhZjViZGQ1LWJhM2QtMTFkYS1hZDMxLWQzM2Q3NTE4MmYxYiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIj48eG1wOkNyZWF0ZURhdGU+MjAyMy0wNC0xOVQxMTo1NTo1OS4xMjg8L3htcDpDcmVhdGVEYXRlPjwvcmRmOkRlc2NyaXB0aW9uPjxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSJ1dWlkOmZhZjViZGQ1LWJhM2QtMTFkYS1hZDMxLWQzM2Q3NTE4MmYxYiIgeG1sbnM6ZGM9Imh0dHA6Ly9wdXJsLm9yZy9kYy9lbGVtZW50cy8xLjEvIj48ZGM6Y3JlYXRvcj48cmRmOlNlcSB4bWxuczpyZGY9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkvMDIvMjItcmRmLXN5bnRheC1ucyMiPjxyZGY6bGk+TkVXPC9yZGY6bGk+PC9yZGY6U2VxPg0KCQkJPC9kYzpjcmVhdG9yPjwvcmRmOkRlc2NyaXB0aW9uPjwvcmRmOlJERj48L3g6eG1wbWV0YT4NCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCiAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgIAogICAgICAgICAgICAgICAgICAgICAgICAgICAgPD94cGFja2V0IGVuZD0ndyc/Pv/bAEMABwUFBgUEBwYFBggHBwgKEQsKCQkKFQ8QDBEY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","signature":""}}}}';

        $response = json_decode($response);
        if (!empty($response->Failed)) {
            $message = $response->Failed->statusMessage;
            $json = array(
                'status' => "error",
                'message' => $message
            );
            echo json_encode($json);
            die();
        }


        if (!empty($response->Succeeded)) {
            $data = $response->Succeeded->data;
            $name = $response->Succeeded->data->result->name;
            $permanent_address = $response->Succeeded->data->result->permanent_address;
            $father_name = $response->Succeeded->data->result->s_w_d;
            $gender = $response->Succeeded->data->result->gender;
            $issue_date = $response->Succeeded->data->result->issue_date;
            $expiry_date = date("Y-m-d", strtotime("+20 years", strtotime($issue_date))); //2015-05-22 10:35:10
            $issue_date = date("Y-m-d", strtotime($issue_date)); //2015-05-22 10:35:10


            if (!empty($name)) {
                $name = explode(' ', $name);
                $first_name = !empty($name[0]) ? $name[0] : '';
                $last_name = !empty($name[1]) ? $name[1] : '';
            }

            $strings = explode(' ', $permanent_address);

            function extract_numbers($string) {
                return preg_match_all('/(?<!\d)\d{5,6}(?!\d)/', $string, $match) ? $match[0] : [];
            }

            $pincode = '';
            foreach ($strings as $string) {
                $pincode = (extract_numbers($string));

                if (!empty($pincode[0])) {
                    $pincode = $pincode[0];
                    break;
                }
            }

            $getCityStateDetails = DB::table('pincodes')->where('Pincode', $pincode)->first();
            $state = !empty($getCityStateDetails->state) ? $getCityStateDetails->state : '';
            $city = !empty($getCityStateDetails->city) ? $getCityStateDetails->city : '';

            $json = array(
                'status' => "success",
                'first_name' => $first_name,
                'last_name' => $last_name,
                'street_address' => $permanent_address,
                'father_name' => $father_name,
                'gender' => $gender,
                'issue_date' => $issue_date,
                'pincode' => $pincode,
                'state' => $state,
                'city' => $city,
                'expiry_date' => $expiry_date
            );
        }
        echo json_encode($json);
    }

    public function manageDriver(Request $request) {

        $user = Auth::user();
 
        $states = states::all();
        $cities = Cities::all();
        $brands = VehicleBrands::where('status', '1')->get();

        $user_id = $request->user_id;
        $driver_id = $request->driver_id;
        $data = Drivers::find($driver_id);
        
        
        $years = array("2020", "2021", "2022", "2023", "2024", "2025");
        return view('admin.modules.driver.manage', [
            'user' => $user,
            'states' => $states,
            'cities' => $cities,
            'brands' => $brands,
            'data' => $data,
            'user_id' => $user_id,
            'id'=>$driver_id
             
        ]);
    }

}
