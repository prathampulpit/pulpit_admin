<?php

namespace App\Http\Controllers\Agent;

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
use App\Repositories\AgentRepository;
use App\Repositories\VehiclesRepository;
use App\Repositories\TripRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
//use App\Helper\Helper;
use Storage;
use Password;
use Session;
use URL;
use DB;
use App\Models\UserHistory;
use App\States as AppStates;
use NunoMaduro\Collision\Adapters\Phpunit\State;

class AgentController extends Controller
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository,
        VehiclesRepository $vehiclesRepository,
        AgentRepository $agentRepository,
        TripRepository $tripRepository
    ) {
        $this->userRepository = $userRepository;
        $this->vehiclesRepository = $vehiclesRepository;
        $this->agentRepository = $agentRepository;
        $this->tripRepository = $tripRepository;
    }
    public function createUpdate(Request $request)
    {

        try {
            $user_id = auth()->user();
            $user_data = json_decode($request->user_data);
            // dd($request->all());
            $stateNameData = States::where('name', $user_data->state_name)->first();

            $user_name = $user_data->first_name . " " . $user_data->last_name;
            $stateName = States::where('id', $request->state_name)->first(); //8

            $stateId = $stateName->id;




            $user = array();
            $name = explode(" ", $request->name);

            if ($user_name != $request->name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "User name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $user['first_name'] = $name[0];
            $user['last_name'] = $name[1];

            if (!empty($request->city_name)) {
                if ($request->city_name != $user_data->city_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "City name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            if (!empty($request->city_name)) {
                $user['city_id'] = $request->city_name;
            }
            // dd($stateId ."=". $stateNameData->id);
            if ($stateId != $stateNameData->id) {
                // dd(7);
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "State name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $user['state']  = $request->state_name;

            if ($request->new_user_interest_status != $request->user_interest_status) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Interest Status updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
       
            $user['user_interest_status'] = $request->user_interest_status;

            User::where('id', $request->user_id)->update($user);
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => 404]);
        }
    }
    public function updateAgent(Request $request)
    {

        try {
            $user_id = auth()->user();
            $agentDetail = json_decode($request->agentDetail);
            $bankDetail = json_decode($request->bankDetail);
            // dd($bankDetail);
            // dd($agentDetail);
            // dd($request->all());
            $userBankMapping = UserBankMapping::where('user_id', $request->user_id)->first();

            $bank_account = array();
            // dd($request->bank_name);
            if (!empty($request->bank_name)) {
                if ($bankDetail->bank_name != $request->bank_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Bank name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $bank_account['bank_name'] = $request->bank_name;

            if (!empty($request->branch_name)) {
                if ($bankDetail->branch_name != $request->branch_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Branch name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $bank_account['branch_name'] = $request->branch_name;

            if (!empty($request->ifsc_code)) {
                if ($bankDetail->ifsc_code != $request->ifsc_code) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "IFSC Code updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $bank_account['ifsc_code'] = $request->ifsc_code;
           
            if (!empty($request->account_number)) {
                if ($bankDetail->account_number != $request->account_number) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Account Number updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $bank_account['account_number'] = $request->account_number;

            BankAccount::where('id', $userBankMapping->bank_account_id)->update($bank_account);

            $agent = array();

            if (!empty($request->travel_name)) {
                if ($agentDetail->travel_name != $request->travel_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Travel name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }

            $agent['travel_name'] = $request->travel_name;

            if (!empty($request->owner_name)) {
                if ($agentDetail->owner_name != $request->owner_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Owner name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $agent['owner_name'] = $request->owner_name;

            if (!empty($request->office_no)) {
                if ($agentDetail->office_no != $request->office_no) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Office Number updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $agent['office_no'] = $request->office_no;

            if (!empty($request->total_business_year)) {
                if ($agentDetail->total_business_year != $request->total_business_year) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Total Business Year updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $agent['total_business_year'] = $request->total_business_year;

            if (!empty($request->pan_card)) {
                if ($agentDetail->pan_card != $request->pan_card) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Pan Card updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $agent['pan_card'] = $request->pan_card;

            if (!empty($request->adhar_card)) {
                if ($agentDetail->adhar_card != $request->adhar_card) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "Adhar Card updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $agent['adhar_card'] = $request->adhar_card;
            AgentUsers::where('id', $request->agent_id)->update($agent);

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => 404]);
        }
    }
    public function index($panel, Request $request, $filter = null)
    {
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
            $vehicle_type_id = $request->vehicle_type_id;
            $plan_id = $request->plan_id;

            $user_type = userType::where('status', '!=', 2)->where('id', '!=', 6)->get();
            $states = States::all();
            $cities = Cities::all();
            $plans = SubscriptionPlans::where('is_agent', '=', '1')->get();

            $today = date("Y-m-d");
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $week = date('Y-m-d', strtotime("-7 days"));
            $this_month = date('Y-m');
            $last_month = date('Y-m', strtotime(date('Y-m') . " -1 month"));
            $fiften_date = date('Y-m-d', strtotime("-15 days"));

            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['today_joined'] = $today;
            $today_joined = $this->agentRepository->getByParams($countcompany);
            //$today_joined = count($today_joined);

            //$yesterday_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$yesterday."'")->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['yesterday_joined'] = $yesterday;
            $yesterday_joined = $this->agentRepository->getByParams($countcompany);

            //$week_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$week."' ")->count();

            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['week'] = $week;
            $week_joined = $this->agentRepository->getByParams($countcompany);

            //$this_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$this_month."' ")->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['this_month_joined'] = $this_month;
            $this_month_joined = $this->agentRepository->getByParams($countcompany);

            //$last_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$last_month."' ")->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['last_month_joined'] = $last_month;
            $last_month_joined = $this->agentRepository->getByParams($countcompany);


            //$active_users = User::whereRaw("user_status = 1")->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$fiften_date."' ")->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['user_status'] = '1';
            $countcompany['fiften_date'] = $fiften_date;
            $countcompany['state_id'] = $request->state_id;
            $countcompany['plan_id'] = $request->plan_id;
            $active_users = $this->agentRepository->getByParams($countcompany);

            //$inactive_users = User::whereRaw("user_status = 0")->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['user_status'] = '0';
            $countcompany['state_id'] = $request->state_id;
            $countcompany['plan_id'] = $request->plan_id;
            $inactive_users = $this->agentRepository->getByParams($countcompany);

            //$all_joined = User::count();
            /* $countcompany1 = [];
            $countcompany1['count'] = true;
            $all_joined = $this->agentRepository->getByParams($countcompany1); */

            $countcompany = [];
            $countcompany['count'] = true;
            $all_joined = $this->agentRepository->getByParams($countcompany);

            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['state_id'] = $request->state_id;
            $countcompany['plan_id'] = $request->plan_id;
            $all_joined_for_paid = $this->agentRepository->getByParams($countcompany);

            //$paid_users = UserPurchasedPlans::groupBy('user_id')->count();
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['paid_users'] = '1';
            $countcompany['state_id'] = $request->state_id;
            $countcompany['plan_id'] = $request->plan_id;
            $paid_users = $this->agentRepository->getByParams($countcompany);
            $unpaid_users = $all_joined_for_paid - $paid_users;

            /* $expired_users = UserPurchasedPlans::leftJoin('users', function($join) {
                $join->on('user_purchased_plans.user_id', '=', 'users.id');
            })->groupBy('user_purchased_plans.user_id')->whereRaw("DATE_FORMAT(start_datetime, '%Y-%m-%d') < '".$today."' AND DATE_FORMAT(start_datetime, '%Y-%m-%d') > '".$today."' ")->count(); */

            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['expired_users'] = '1';
            $countcompany['state_id'] = $request->state_id;
            $countcompany['plan_id'] = $request->plan_id;
            $expired_users = $this->agentRepository->getByParams($countcompany);

            $uninstall_users = '0';

            /* Graph Data */
            $day_name_arr = '';
            $graph_trans_data_arr = '';
            for ($i = 6; $i >= 0; $i--) {
                $day_name_arr .= "'" . date('d-M', strtotime("-$i month")) . "',";
                $date = date('Y-m', strtotime("-$i month"));

                $countcompany = [];
                $countcompany['count'] = true;
                $countcompany['this_month_joined'] = $date;
                $countcompany['state_id'] = $request->state_id;
                $countcompany['plan_id'] = $request->plan_id;
                $expired_users = $this->agentRepository->getByParams($countcompany);
                $graph_trans_data_arr .= $expired_users . ",";
            }
            $day_name_arr .= '';
            $day_name_arr1 = rtrim($day_name_arr, ",");
            $day_name = $day_name_arr1 . "";

            $graph_trans_data_arr .= '';
            $graph_trans_data_arr1 = rtrim($graph_trans_data_arr, ",");
            $graph_trans_data = $graph_trans_data_arr1;

            return view('admin.modules.agent.index', compact('file_path', 'user_type', 'today_joined', 'yesterday_joined', 'week_joined', 'this_month_joined', 'last_month_joined', 'all_joined', 'active_users', 'inactive_users', 'paid_users', 'unpaid_users', 'expired_users', 'uninstall_users', 'day_name', 'graph_trans_data', 'states', 'plans', 'state_id', 'vehicle_type_id', 'plan_id', 'cities', 'filter'));
        } else {
            abort(403);
        }
    }

    public function index_json($panel, Request $request, $filter = null)
    {
        $user = Auth::user();
        //$in = $this->agentRepository->hieararchy($user, false);
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->agentRepository->getByParams($countcompany);
        } else {
            // dd(9);
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['filter'] = $filter;
        $users = $this->agentRepository->getPanelPartnerUsers($request, $params);
        return $users;
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

            $user = null;
            if ($id) {
                $params = [];
                $params['user_id'] = $id;
                $params['response_type'] = "single";
                $user = $this->agentRepository->getByParams($params);

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
            return view('admin.modules.partner.store', [
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

    public function store(Request $request)
    {
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

        if (!empty($request->get('id'))) {
            $user = User::find($id);
        } else {
            $user = new User();

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
                $user->is_otp = '2';
                $user->otp = $otp_result['Details'];
            }
        }
        $user->emailid = $emailid;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->mobile_number = $mobile_number;
        $user->gender = $gender;
        $user->state = $state;
        $user->city_id = $city_id;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->save();

        if (!empty($request->get('id'))) {
            $user_id = $id;
        } else {
            $user_id = $user->id;
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
            $message = 'User Updated Successfully';
            return redirect(route('admin.partner.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'User Added Successfully';
            return redirect(route('admin.partner.otp', ['panel' => Session::get('panel'), 'id' => $user_id]))->withMessage($message);
        }

        /* $log_update = PortalActivities::find($log_id);
        $log_update->response_data = $message;
        $log_update->save(); */

        //$message = 'Status change successfully!';
        //return redirect(route('admin.partner.index', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);

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
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        //$user = $this->agentRepository->getByParams($params);
        $user = $this->agentRepository->getUserDetails($params);

        $last_updated_id = $user->last_updated_id;


        $last_updated_user_details = User::find($last_updated_id);

        $user_work_profiles_for_agent = UserWorkProfile::where('user_id', $id)->where('status', '1')->whereIn('user_type_id', array(2))->first();

        $agent_details = array();
        if (!empty($user_work_profiles_for_agent)) {
            $profile_id = $user_work_profiles_for_agent['profile_id'];

            //$agent_details = AgentUsers::find($profile_id);
            $agent_details = AgentUsers::where('id', $profile_id)->where('status', 1)->first();
        }

        $banks = UserBankMapping::select('bank_account.*')->leftJoin('bank_account', function ($join) {
            $join->on('user_bank_mapping.bank_account_id', '=', 'bank_account.id');
        })->where('user_bank_mapping.user_id', $id)->first();

        //echo "<pre>"; print_r($user); exit;
        $plan = UserPurchasedPlans::where('user_id', $id)->where('status', 1)->orderBy('id', 'DESC')->first();

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

        $cabs = Cabs::select('cab_post.*', 'vehicles.vehicle_number')->leftJoin('vehicles', function ($join) {
            $join->on('cab_post.vehicle_id', '=', 'vehicles.id');
        })->where('cab_post.user_id', $id)->get();

        /* if($user->user_type_id == '3'){
            
        } */
        $drivers = Drivers::where('agent_id', $id)->get();

        $params = [];
        $params['user_id'] = $id;
        $trip = $this->tripRepository->getByParams($params);

        //Referrals
        $referrals = Referrals::select('referrals.*', 'users.first_name', 'users.last_name', 'users.mobile_number', 'user_type.name as user_type_name')
            ->join('users', function ($join) {
                $join->on('referrals.user_id', '=', 'users.id');
            })->join('user_work_profile', function ($join) {
                $join->on('referrals.user_id', '=', 'user_work_profile.user_id');
            })->join('user_type', function ($join) {
                $join->on('user_work_profile.user_type_id', '=', 'user_type.id');
            })->where('referrals.reference_by', $id)->get();

        $profile_path = config('custom.upload.user.profile');

        $cities = Cities::all();
        $user_history = UserHistory::where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        $states = States::all();
        return view('admin.modules.agent.show', [
            'cities' => $cities, 'user_history' => $user_history, 'states' => $states, 'user' => $user, 'user_role' => $this->user_role, 'plan' => $plan, 'plan_details' => $plan_details, 'earnings' => $earnings, 'customer_trip' => $customer_trip, 'driver_trip' => $driver_trip, 'money_spent' => $money_spent, 'referrals_done' => '0', 'trip_status' => 'Completed', 'current_location' => 'Ahm', 'subscription_status' => 'Active', 'vehicles' => $vehicles, 'cabs' => $cabs, 'drivers' => $drivers, 'referrals' => $referrals, 'trip' => $trip, 'agent_details' => $agent_details, 'banks' => $banks, 'last_updated_user_details' => $last_updated_user_details
        ]);
    }

    public function otp($panel, $id)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->agentRepository->getByParams($params);

        return view('admin.modules.partner.otp', [
            'mobile_number' => $user->mobile_number, 'id' => $id
        ]);
    }

    public function resendotp($panel, $id)
    {
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
        ));

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


    public function sendotp(Request $request)
    {
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
        ));

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
    public function changeStatus(Request $request)
    {
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
    public function resetAttempt(Request $request)
    {
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
    public function resetOtpAttempt(Request $request)
    {
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
    public function changeUssdStatus(Request $request)
    {
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
    public function documentVerification(Request $request)
    {
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

    public function toggleStatus($panel, $id)
    {
        $result = $this->userRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->userRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }
        $result = $this->userRepository->delete($id);
        return (int) $result;
    }

    public function showChangePasswordForm()
    {
        return view('admin.modules.user.change_password');
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
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->back()->with("message", trans("Password changed successfully !"));
    }

    public function showChangeProfileForm()
    {
        return view('admin.modules.partner.change_profile');
    }
    public function changeUserProfile(ChangeProfileRequest $request)
    {
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
    public function resetProfile(Request $request)
    {

        $user_id = $request->user_reset_id;
        $userWorksData = UserWorkProfile::where('user_id', $user_id)->get();
        User::where('users.id', $user_id)->update([
            'user_type_id' => NULL,
            'profile_pic' => NULL,
            'profile_pic_status' => 2,
            'profile_completion_step'=>0,
            'is_approved' => 0
        ]);
        // UserWorkProfile::where('user_id', $user_id)->update(['status' => 0]);
        UserPurchasedPlans::where('user_id', $user_id)->delete();
        UserBankMapping::where('user_id', $user_id)->join('bank_account', 'user_bank_mapping.bank_account_id', 'bank_account.id')->delete();
        DB::table('travelled_location')->where('user_id', $user_id)->update(['status' => 0]);
        DB::table('user_bank_mapping')->join('bank_account','user_bank_mapping.bank_account_id','bank_account.id')->where('user_bank_mapping.user_id', $user_id)->update([
            'bank_account.account_holder_name' => NULL,
            'bank_account.ifsc_code' => NULL,
            'bank_account.bank_name' => NULL,
            'bank_account.branch_name' => NULL,
            'bank_account.account_number' => NULL,
            'bank_account.document_url' => NULL,
            'bank_account.bank_document_url_status' => 0,
        ]);
        if (!empty($userWorksData)) {
            foreach ($userWorksData as $key => $userWorkData) {
                if ($userWorkData->user_type_id == 2) {
                    AgentUsers::join('user_work_profile', 'agent_users.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'agent_users.status' => 0,
                        'logo' => NULL,
                        'logo_status' => 0,
                        'pan_card' => NULL,
                        'adhar_card' => NULL,
                        'pan_card_url' => NULL,
                        'pan_card_url_status' => 0,
                        'adhar_card_url' => NULL,
                        'adhar_card_url_status' => 0,
                        'registration_document_url' => NULL,
                        'registration_document_url_status' => 0,
                        'registration_document' => NULL,
                        'adhar_card_back_url' => NULL,
                        'adhar_card_back_url_status' => 0,
                        'all_document_verify' => 0,
                    ]);
                    Drivers::join('user_work_profile', 'drivers.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'drivers.status' => 0,
                        'drivers.mobile_numebr' => NULL,
                        'dl_front_url' => NULL,
                        'dl_back_url' => NULL,
                        'police_verification_url' => NULL
                    ]);
                    Vehicles::where('user_id', $user_id)->update([
                        'status' => 0,
                        'completion_steps' => 0,
                        'rc_front_url' => NULL,
                        'rc_back_url' => NULL,
                        'insurance_doc_url' => NULL,
                        'permit_doc_url' => NULL,
                        'fitness_doc_url' => NULL,
                        'puc_doc_url' => NULL,
                        'agreement_doc_url' => NULL,
                        'vehicle_number' => NULL,
                        'all_document_verify' => 0,
                        'city' => NULL,
                        'state' => NULL,
                        'registration_year' => NULL,
                        'owner_name' => NULL,
                        'rc_front_url_status' => 0,
                        'rc_back_url_status' => 0,
                        'insurance_doc_url_status' => 0,
                        'permit_doc_url_status' => 0,
                        'fitness_doc_url_status' => 0,
                        'puc_doc_url_status' => 0,
                        'agreement_doc_url_status' => 0,
                        'insurance_exp_date' => NULL,
                        'permit_exp_date' => NULL
                    ]);
                } elseif ($userWorkData->user_type_id == 3) {
                    AgentUsers::join('user_work_profile', 'agent_users.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'agent_users.status' => 0,
                        'logo' => NULL,
                        'logo_status' => 0,
                        'pan_card' => NULL,
                        'adhar_card' => NULL,
                        'pan_card_url' => NULL,
                        'pan_card_url_status' => 0,
                        'adhar_card_url' => NULL,
                        'adhar_card_url_status' => 0,
                        'registration_document_url' => NULL,
                        'registration_document_url_status' => 0,
                        'registration_document' => NULL,
                        'adhar_card_back_url' => NULL,
                        'adhar_card_back_url_status' => 0,
                        'all_document_verify' => 0,
                    ]);
                    Drivers::join('user_work_profile', 'drivers.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'drivers.status' => 0,
                        'drivers.mobile_numebr' => NULL,
                        'dl_front_url' => NULL,
                        'dl_back_url' => NULL,
                        'police_verification_url' => NULL
                    ]);
                     Vehicles::where('user_id', $user_id)->update([
                        'status' => 0,
                        'completion_steps' => 0,
                        'rc_front_url' => NULL,
                        'rc_back_url' => NULL,
                        'insurance_doc_url' => NULL,
                        'permit_doc_url' => NULL,
                        'fitness_doc_url' => NULL,
                        'puc_doc_url' => NULL,
                        'agreement_doc_url' => NULL,
                        'vehicle_number' => NULL,
                        'all_document_verify' => 0,
                        'city' => NULL,
                        'state' => NULL,
                        'registration_year' => NULL,
                        'owner_name' => NULL,
                        'rc_front_url_status' => 0,
                        'rc_back_url_status' => 0,
                        'insurance_doc_url_status' => 0,
                        'permit_doc_url_status' => 0,
                        'fitness_doc_url_status' => 0,
                        'puc_doc_url_status' => 0,
                        'agreement_doc_url_status' => 0,
                        'insurance_exp_date' => NULL,
                        'permit_exp_date' => NULL
                    ]);
                } elseif ($userWorkData->user_type_id == 4) {
                    Drivers::join('user_work_profile', 'drivers.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'drivers.status' => 0,
                        
                        'dl_front_url' => NULL,
                        'dl_back_url' => NULL,
                        'police_verification_url' => NULL
                    ]);

                    Vehicles::where('user_id', $user_id)->update([
                        'status' => 0,
                        'completion_steps' => 0,
                        'rc_front_url' => NULL,
                        'rc_back_url' => NULL,
                        'insurance_doc_url' => NULL,
                        'permit_doc_url' => NULL,
                        'fitness_doc_url' => NULL,
                        'puc_doc_url' => NULL,
                        'agreement_doc_url' => NULL,
                        'vehicle_number' => NULL,
                        'all_document_verify' => 0,
                        'city' => NULL,
                        'state' => NULL,
                        'registration_year' => NULL,
                        'owner_name' => NULL,
                        'rc_front_url_status' => 0,
                        'rc_back_url_status' => 0,
                        'insurance_doc_url_status' => 0,
                        'permit_doc_url_status' => 0,
                        'fitness_doc_url_status' => 0,
                        'puc_doc_url_status' => 0,
                        'agreement_doc_url_status' => 0,
                        'insurance_exp_date' => NULL,
                        'permit_exp_date' => NULL
                    ]);
                } elseif ($userWorkData->user_type_id == 5) {
                    Drivers::join('user_work_profile', 'drivers.id', '=', 'user_work_profile.profile_id')->where('user_work_profile.user_id', $user_id)->update([
                        'drivers.status' => 0,
                        'drivers.mobile_numebr' => NULL,
                        'dl_front_url' => NULL,
                        'dl_back_url' => NULL,
                        'police_verification_url' => NULL
                    ]);

                    Vehicles::where('user_id', $user_id)->update([
                        'status' => 0,
                        'completion_steps' => 0,
                        'rc_front_url' => NULL,
                        'rc_back_url' => NULL,
                        'insurance_doc_url' => NULL,
                        'permit_doc_url' => NULL,
                        'fitness_doc_url' => NULL,
                        'puc_doc_url' => NULL,
                        'agreement_doc_url' => NULL,
                        'vehicle_number' => NULL,
                        'all_document_verify' => 0,
                        'city' => NULL,
                        'state' => NULL,
                        'registration_year' => NULL,
                        'owner_name' => NULL,
                        'rc_front_url_status' => 0,
                        'rc_back_url_status' => 0,
                        'insurance_doc_url_status' => 0,
                        'permit_doc_url_status' => 0,
                        'fitness_doc_url_status' => 0,
                        'puc_doc_url_status' => 0,
                        'agreement_doc_url_status' => 0,
                        'insurance_exp_date' => NULL,
                        'permit_exp_date' => NULL   
                    ]);
                }
            }
        }
        $admin_id = auth()->user();
        $message = new UserHistory();
        $message->edit_user_id = $admin_id->id;
        $message->message = "Reset Profile by " . $admin_id->first_name . " " . $admin_id->last_name;
        $message->user_id = $user_id;
        $message->save();

        return redirect()->back()->with("message", "Profile Reset Successfully");
    }
}
