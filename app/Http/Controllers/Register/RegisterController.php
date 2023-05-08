<?php

namespace App\Http\Controllers\Register;

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
use App\Models\VehicleTypes;
use App\Models\VehiclePhotoMapping;
use App\Models\VehicleDrivingMapping;
use App\Models\UserBankMapping;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\VehiclesRepository;
use App\Repositories\RegisterRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;
// use DB;
use App\Models\UserHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;


class RegisterController extends Controller
{
    protected $registerRepository;

    public function __construct(
        VehiclesRepository $vehiclesRepository,
        RegisterRepository $registerRepository
    ) {
        $this->vehiclesRepository = $vehiclesRepository;
        $this->registerRepository = $registerRepository;
    }

    public function index($panel, Request $request, $sub = null)
    {
        $user = Auth::user();
        $id = $user['id'];

        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("4", $role_id_arr) || $user_role == 'administrator') {
            $profile_path = config('custom.upload.register.profile');
            $file_path = $profile_path;

            $user_type = userType::where('status', '!=', '2')->get();

            $today = date("Y-m-d");
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $week = date('Y-m-d', strtotime("-7 days"));
            $this_month = date('Y-m');
            $last_month = date('Y-m', strtotime(date('Y-m') . " -1 month"));
            $fiften_date = date('Y-m-d', strtotime("-15 days"));

            //$today_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$today."'")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['today_joined'] = $today;
            //$countcompany['type_id'] = '6';
            $today_joined = $this->registerRepository->getByParams($countcompany);

            //$yesterday_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') = '".$yesterday."'")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['yesterday_joined'] = $yesterday;
            $yesterday_joined = $this->registerRepository->getByParams($countcompany);

            //$week_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$week."' ")->count();

            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['week'] = $week;
            $countcompany['filter'] = 'week';
            $week_joined = $this->registerRepository->getCount($countcompany);

            //$this_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$this_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['this_month_joined'] = $this_month;
            $countcompany['filter'] = 'this_month';
            $this_month_joined = $this->registerRepository->getCount($countcompany);

            //$last_month_joined = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = '".$last_month."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['last_month_joined'] = $last_month;
            $countcompany['filter'] = 'last_month';
            $last_month_joined = $this->registerRepository->getCount($countcompany);

            //$all_joined = User::count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $all_joined = $this->registerRepository->getCount($countcompany);

            //$active_users = User::whereRaw("user_status = 1")->whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$today."' AND '".$fiften_date."' ")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['user_status'] = '1';
            $countcompany['fiften_date'] = $fiften_date;
            $active_users = $this->registerRepository->getByParams($countcompany);

            //$inactive_users = User::whereRaw("user_status = 0")->count();
            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['user_status'] = '0';
            $inactive_users = $this->registerRepository->getByParams($countcompany);

            //$paid_users = UserPurchasedPlans::groupBy('user_id')->count();
            /* $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['paid_users'] = '1';
            $paid_users = $this->registerRepository->getByParams($countcompany); */

            $countcompany = array();
            //$countcompany['count'] = true;
            $countcompany['redirect_from'] = 'subscribe';
            $subscribed = $this->registerRepository->getCount($countcompany);
            $paid_users = count($subscribed);

            $unpaid_users = $all_joined - $paid_users;

            /* $expired_users = UserPurchasedPlans::leftJoin('users', function($join) {
            $join->on('user_purchased_plans.user_id', '=', 'users.id');
            })->groupBy('user_purchased_plans.user_id')->whereRaw("DATE_FORMAT(start_datetime, '%Y-%m-%d') < '".$today."' AND DATE_FORMAT(start_datetime, '%Y-%m-%d') > '".$today."' ")->count(); */

            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['expired_users'] = '1';
            $expired_users = $this->registerRepository->getByParams($countcompany);

            $countcompany = array();
            $countcompany['count'] = true;
            //$countcompany['type_id'] = '6';
            $countcompany['pending'] = '0';
            $pending = $this->registerRepository->getByParams($countcompany);

            // $uninstall_users = '0';

            /* Graph Data */
            $day_name_arr = '';
            $graph_trans_data_arr = '';
            for ($i = 6; $i >= 0; $i--) {
                $day_name_arr .= "'" . date('M', strtotime("-$i month")) . "',";
                $date = date('Y-m', strtotime("-$i month"));

                $countcompany = array();
                $countcompany['count'] = true;
                $countcompany['type_id'] = '5';
                $countcompany['this_month_joined'] = $date;
                $expired_users = $this->registerRepository->getByParams($countcompany);
                $graph_trans_data_arr .= $expired_users . ",";
            }
            $day_name_arr .= '';
            $day_name_arr1 = rtrim($day_name_arr, ",");
            $day_name = $day_name_arr1 . "";

            $graph_trans_data_arr .= '';
            $graph_trans_data_arr1 = rtrim($graph_trans_data_arr, ",");
            $graph_trans_data = $graph_trans_data_arr1;

            return view('admin.modules.register.index', compact('file_path', 'user_type', 'today_joined', 'yesterday_joined', 'week_joined', 'this_month_joined', 'last_month_joined', 'all_joined', 'active_users', 'inactive_users', 'paid_users', 'unpaid_users', 'expired_users', 'pending', 'day_name', 'graph_trans_data', 'sub'));
        } else {
            abort(403);
        }
    }

    public function index_json($panel, Request $request, $sub = null)
    {
        $user = Auth::user();
        //$in = $this->registerRepository->hieararchy($user, false);
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->registerRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['redirect_from'] = $sub;
        $params['filter'] = $sub;
        //$params['register_step'] = '6';
        if (!empty($sub)) {
            $params['all_document_verify'] = $sub;
        }
        $users = $this->registerRepository->getPanelUsers($request, $params);
        
        return $users;
    }

    public function referal_request()
    {
        $profile_path = config('custom.upload.register.profile');
        $file_path = env('APP_URL') . '/storage/' . $profile_path . "/";
        return view('admin.modules.register.referal_request', compact('file_path'));
    }

    public function referal_request_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['referal_register_type'] = '3';
            $total = $this->registerRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['referal_register_type'] = '3';
        $users = $this->registerRepository->getPanelUsers($request, $params);
        return $users;
    }
    public function createUpdate(Request $request)
    {
        try {
            // dd($request->all());
            $stateName = States::where('id', $request->state_name)->first();
            $stateId = $stateName->id;
            $cityId = '';
            if (!empty($request->city_name)) {
                $cityName = Cities::where('id', $request->city_name)->first();
                $cityId = $cityName->id;
            }
            $user_data = json_decode($request->user_data);
            // dd($request->all());
            $user_name = $user_data->first_name . " " . $user_data->last_name;
            $user_id = auth()->user();

            $name = explode(" ", $request->rider_name);


            $user = array();

            if ($user_name != $request->rider_name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "User name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $user['first_name'] = $name[0];
            $user['last_name'] = $name[1];
            if (!empty($stateName)) {
                if ($user_data->state_name != $stateName->name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "State name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $user['state'] = $request->state_name;

            if (isset($cityId)) {
                if ($user_data->city_name != $request->city_name) {
                    $message = new UserHistory();
                    $message->edit_user_id = $user_id->id;
                    $message->message = "City name updated by " . $user_id->first_name . " " . $user_id->last_name;
                    $message->user_id = $request->user_id;
                    $message->save();
                }
            }
            $user['city_id'] = $request->city_name;


            if ($request->new_user_interest_status != $request->user_interest_status) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Interest Status updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }

            $user['user_interest_status'] = $request->user_interest_status;
            User::where('id', $request->user_id)->update($user);

            $user_work_profile = UserWorkProfile::where('user_id', $request->user_id)->first();
            // $user_work_profile = DB::table('user_work_profile')->where('user_id', $request->user_id)->first();
            // dd($request->user_id);
            if (!empty($user_work_profile->user_type_id)) {


                if ($user_work_profile->user_type_id == 2) {
                    $agent_user = array();
                    $agent_user['owner_name'] = $request->rider_name;
                    $agent_user['city_id'] = $request->city_name;
                    AgentUsers::where('id', $user_work_profile->profile_id)->update($agent_user);
                } elseif ($user_work_profile->user_type_id == 3) {
                    $agent_user = array();
                    $agent_user['travel_name'] = $request->rider_name;
                    $agent_user['city_id'] = $request->city_name;
                    AgentUsers::where('id', $user_work_profile->profile_id)->update($agent_user);
                }
                // $user_work_profile->user_type_id == 4 || $user_work_profile->user_type_id == 5
                elseif ($user_work_profile->user_type_id == 4 || $user_work_profile->user_type_id == 5) {

                    $driver = array();
                    $driver['first_name'] = $name[0];
                    $driver['last_name'] = $name[0];
                    if (!empty($request->city_name)) {
                        $driver['city'] = $cityName->name;
                    }
                    Drivers::where('id', $user_work_profile->profile_id)->update($driver);
                }
            }
            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => 404]);
        }
    }
    public function history_comment(Request $request)
    {
        $auth = auth()->user();
        $history = new UserHistory();
        $history->edit_user_id = $auth->id;
        $history->message = '"' . $request->comment_text . '" ' . ' Added By ' . $auth->first_name . ' ' . $auth->last_name;
        $history->user_id = $request->user_id;
        $history->save();
        return redirect()->back();
    }
    public function updateAgent(Request $request)
    {
        try {
            // dd($request->all());
            $user_id = auth()->user();

            $agent_data = json_decode($request->agent_data);
            $agent_user = array();

            if ($agent_data->travel_name != $request->travel_name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Travel name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['travel_name'] = $request->travel_name;

            if ($agent_data->owner_name != $request->owner_name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Owner name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['owner_name'] = $request->owner_name;

            if ($agent_data->office_no != $request->agent_office_no) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Office no updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['office_no'] = $request->agent_office_no;

            if ($agent_data->total_business_year != $request->total_business_year) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Total business year updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['total_business_year'] = $request->total_business_year;

            if ($agent_data->pan_card != $request->pan_card) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Pan card updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['pan_card'] = $request->pan_card;

            if ($agent_data->adhar_card != $request->adhar_card) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Adhar card updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $agent_user['adhar_card'] = $request->adhar_card;
            AgentUsers::where('id', $request->agent_id)->update($agent_user);

            $user_work_profile = UserWorkProfile::where('profile_id', $request->agent_id)->first();
            $user_bank_map = UserBankMapping::where('user_id', $user_work_profile->user_id)->first();
            $bank_account1 = BankAccount::where('id', $user_bank_map->bank_account_id)->first();

            $bank_account = array();

            $bank_data = json_decode($request->bank_data);

            if ($bank_data->ifsc_code != $request->ifsc_code) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "IFSC code updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $bank_account['ifsc_code'] = $request->ifsc_code;

            if ($bank_data->bank_name != $request->bank_name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Bank name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $bank_account['bank_name'] = $request->bank_name;

            if ($bank_data->branch_name != $request->branch_name) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Branch name updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $bank_account['branch_name'] = $request->branch_name;

            if ($bank_data->account_number != $request->account_number) {
                $message = new UserHistory();
                $message->edit_user_id = $user_id->id;
                $message->message = "Account number updated by " . $user_id->first_name . " " . $user_id->last_name;
                $message->user_id = $request->user_id;
                $message->save();
            }
            $bank_account['account_number'] = $request->account_number;

            BankAccount::where('id', $user_bank_map->bank_account_id)->update($bank_account);

            return response()->json(['success' => true]);
        } catch (\Throwable $th) {
            return response()->json(['success' => 404]);
        }
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

            $action_type = "add";
            $user = null;
            if ($id) {
                $params = [];
                $params['user_id'] = $id;
                $params['response_type'] = "single";
                $user = $this->registerRepository->getByParams($params);
                //echo "<pre>"; print_r($user); exit; 

                //$user = $this->registerRepository->getByParams($params);

                $profile_id = $user['profile_id'];
                $user_type_id = $user['user_type_id'];
                $bank_account_id = $user['bank_account_id'];
                $action_type = "edit";
                $driver_id = $user['t_driver_id'];
            }

            /* echo "<pre>";
            print_r($agent_users);
            exit; */

            $driver = array();
            if ($user_type_id == 2 || $user_type_id == 3) {
                $data = AgentUsers::find($profile_id);

                $bank = array();
                if (!empty($bank_account_id)) {
                    $bank = BankAccount::find($bank_account_id);
                }
                $vehicle_photo_mapping = array();
                if ($user_type_id == 3) {
                    $vehicle_id = $user['vehicle_id'];
                    $vehicle_photo_mapping = VehiclePhotoMapping::where('vehicle_id', '=', $vehicle_id)->get();
                    if ($vehicle_photo_mapping->isEmpty()) {
                        $vehicle_photo_mapping = array();
                    }
                }

                if ($user_type_id == 3) {
                    $driver = Drivers::find($driver_id);
                }
            } else if ($user_type_id == '5') { //Driver cum owner
                $data = Drivers::find($profile_id);
                $bank = BankAccount::find($bank_account_id);
                $vehicle_photo_mapping = array();
            } else if ($user_type_id == '4') { //Driver
                $data = Drivers::find($profile_id);
                $bank = array();
                $vehicle_photo_mapping = array();
            } else {
                $data = array();
                $bank = array();
                $vehicle_photo_mapping = array();
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
            $models = VehicleBrandModels::where('status', '1')->get();
            $fuel_types = VehicleFuelType::where('status', '1')->get();
            $vehicle_colours = VehicleColour::where('status', '1')->get();
            $vehicle_types = VehicleTypes::where('status', '1')->get();

            $years = array("2010", "2011", "2012", "2013", "2014", "2015", "2016", "2017", "2018", "2019", "2020", "2021", "2022", "2023");
            return view('admin.modules.register.store', [
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
                'models' => $models,
                'driver' => $driver,
                'vehicle_types' => $vehicle_types,
                'vehicle_colours' => $vehicle_colours,
                'fuel_types' => $fuel_types,
                'profile_id' => $profile_id,
                'user_type_id' => $user_type_id,
                'bank_account_id' => $bank_account_id,
                'agent_users' => $agent_users,
                'action_type' => $action_type,
                'vehicle_photo_mapping' => $vehicle_photo_mapping
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

            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://2factor.in/API/V1/4d181bfe-6fa7-11e7-94da-0200cd936042/SMS/+91' . $mobile_number . '/AUTOGEN/OTP',
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
            $newrecord->status = 1;
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
            $message = 'User Updated Successfully';
            return redirect(route('admin.register.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'User Added Successfully';
            return redirect(route('admin.register.otp', ['panel' => Session::get('panel'), 'id' => $user_id]))->withMessage($message);
        }

        /* $log_update = PortalActivities::find($log_id);
        $log_update->response_data = $message;
        $log_update->save(); */

        //$message = 'Status change successfully!';
        //return redirect(route('admin.users.index', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);

    }

    public function storefirst(Request $request)
    {

        $user = Auth::user();
        $last_updated_id = $user['id'];

        $id = $request->get('id', null);
        $usertype = $request->get('usertype');
        $first_name = $request->get('first_name');
        $last_name = $request->get('last_name');
        $name = $first_name . " " . $last_name;
        $state = $request->get('state');
        $city_id = $request->get('city_id');
        $emailid = $request->get('email');
        $mobile_number = $request->get('mobile_number');
        $gender = $request->get('gender');
        $travel_name = $request->get('travel_name');
        $agent_name = $request->get('agent_name');
        $owner_name = $request->get('owner_name');
        $total_year_of_business = $request->get('total_business_year');
        $adhar_no = $request->get('adhar_card');
        $license_no = $request->get('license_no');
        $expiry_date = $request->get('expiry_date');
        $expiry_date = date("Y-m-d", strtotime($expiry_date));
        $street_address = $request->get('street_address');
        $pin_code = $request->get('pin_code');
        $pan_card_no = $request->get('pan_card_no');
        $office_no = $request->get('office_no');
        $adhar_card_no = $request->get('adhar_card');
        $account_holder_name = $request->get('account_holder_name');
        $ifsc_code = $request->get('ifsc_code');
        $bank_name = $request->get('bank_name');
        $branch_name = $request->get('branch_name');
        $account_number = $request->get('account_number');
        $confirm_account_number = $request->get('confirm_account_number');
        $registration_document = $request->get('registration_document');
        $action_type = $request->get('action_type');

        if (!empty($request->get('id')) && $action_type == 'edit') {
            $user = User::find($id);
        } else {
            $check_mobile_number = User::where('mobile_number', $mobile_number)->where('status', 1)->first();
            if (!empty($check_mobile_number)) {
                echo "mobile_number_exits";
                exit;
            }
            //

            $user = new User();
        }

        if (!isset($usertype) && empty($usertype)) {
            $usertype = '';
        }
        $user->emailid = $emailid;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->mobile_number = $mobile_number;
        $user->gender = $gender;
        $user->state = $state;
        $user->city_id = $city_id;
        $user->user_type_id = $usertype;
        if (empty($request->get('id')) && $action_type == 'add') {
            $user->created_at = date("Y-m-d H:i:s");
        }
        $user->updated_at = date("Y-m-d H:i:s");
        $user->save();

        if (!empty($request->get('id'))) {
            $user_id = $id;
        } else {
            $user_id = $user->id;
        }

        $lastUpdate = User::find($user_id);
        $lastUpdate->last_updated_id = $last_updated_id;
        $lastUpdate->save();

        if (!empty($request->file('logo'))) {

            $file = $request->file('logo');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $agent_logo = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('logo');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $document_file_name = rand('11111','99999').time() . '.'.$image_ext;
            //$document_file_name = rand('111','999').time().$file->getClientOriginalName();
            $filePath = "/".$document_file_name;
            Storage::disk('s3')->put($filePath, $image);
            $agent_logo = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $agent_logo = "";
            } else {
                $agent_logo = $request->get('edit_logo');
            }
        }

        if (!empty($request->file('driving_license_front'))) {

            $file = $request->file('driving_license_front');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $driving_license_front = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if ($usertype == '4') {
                if (!empty($request->get('profile_id'))) {
                    $profile_id = $request->get('profile_id');

                    $driverProfUpdate = Drivers::find($profile_id);
                    $driverProfUpdate->dl_front_url_status = '2';
                    $driverProfUpdate->save();
                }
            }
        } else {
            if ($action_type == 'add') {
                $driving_license_front = "";
            } else {
                $driving_license_front = $request->get('edit_driving_license_front');
            }
        }

        if (!empty($request->file('driving_license_back'))) {

            $file = $request->file('driving_license_back');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $driving_license_back = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if ($usertype == '4') {
                if (!empty($request->get('profile_id'))) {
                    $profile_id = $request->get('profile_id');

                    $driverProfUpdate = Drivers::find($profile_id);
                    $driverProfUpdate->dl_back_url_status = '2';
                    $driverProfUpdate->save();
                }
            }
        } else {
            if ($action_type == 'add') {
                $driving_license_back = "";
            } else {
                $driving_license_back = $request->get('edit_driving_license_back');
            }
        }

        if (!empty($request->file('police_verification_url'))) {

            $file = $request->file('police_verification_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $police_verification_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('police_verification');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $police_verification = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$police_verification;
            Storage::disk('s3')->put($filePath, $image);
            $police_verification_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $police_verification_url = "";
            } else {
                $police_verification_url = $request->get('edit_police_verification');
            }
        }

        if (!empty($request->file('pan_card'))) {

            $file = $request->file('pan_card');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $pan_card_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if ($usertype == '2' || $usertype == '3') {
                if (!empty($request->get('profile_id')) && $action_type == 'edit') {
                    $profile_id_ag = $request->get('profile_id');
                    $AgProfile = AgentUsers::find($profile_id_ag);
                    if (!empty($AgProfile)) {
                        $AgProfile->pan_card_url_status = '2';
                        $AgProfile->save();
                    }
                }
            }
        } else {
            if ($action_type == 'add') {
                $pan_card_url = "";
            } else {
                $pan_card_url = $request->get('edit_pan_card');
            }
        }

        if (!empty($request->file('addar_card_front'))) {

            $file = $request->file('addar_card_front');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $addar_card_front = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if ($usertype == '2' || $usertype == '3') {
                if (!empty($request->get('profile_id')) && $action_type == 'edit') {
                    $profile_id_ag = $request->get('profile_id');
                    $AgProfile = AgentUsers::find($profile_id_ag);
                    if (!empty($AgProfile)) {
                        $AgProfile->adhar_card_url_status = '2';
                        $AgProfile->save();
                    }
                }
            }
        } else {
            if ($action_type == 'add') {
                $addar_card_front = "";
            } else {
                $addar_card_front = $request->get('edit_addar_card_front');
            }
        }

        if (!empty($request->file('addar_card_back'))) {

            $file = $request->file('addar_card_back');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $addar_card_back = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if ($usertype == '2' || $usertype == '3') {
                if (!empty($request->get('profile_id')) && $action_type == 'edit') {
                    $profile_id_ag = $request->get('profile_id');
                    $AgProfile = AgentUsers::find($profile_id_ag);
                    if (!empty($AgProfile)) {
                        $AgProfile->adhar_card_back_url_status = '2';
                        $AgProfile->save();
                    }
                }
            }
        } else {
            if ($action_type == 'add') {
                $addar_card_back = "";
            } else {
                $addar_card_back = $request->get('edit_addar_card_back');
            }
        }

        if (!empty($request->file('cheque_book'))) {

            $file = $request->file('cheque_book');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $cheque_book_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            $bank_document_url_status = 2;
        } else {
            if ($action_type == 'add') {
                $cheque_book_url = "";
            } else {
                $cheque_book_url = $request->get('edit_cheque_book');
            }
        }

        //if ($request->get('registration_document_url') != "") {

        if (!empty($request->file('registration_document_url'))) {
            $file = $request->file('registration_document_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $registration_document_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $registration_document_url = "";
            } else {
                $registration_document_url = $request->get('edit_registration_document_url');
            }
        }

        /* https://pulpit-media-storage.s3-ap-south-1.amazonaws.com/image_cropper_1633957680100.jpg */

        /* Agent & Travel Agency*/
        if ($usertype == '2' || $usertype == '3') {
            if (!empty($request->get('profile_id')) && $action_type == 'edit') {
                $profile_id = $request->get('profile_id');
                $newrecord = AgentUsers::find($profile_id);
                if (empty($newrecord)) {
                    $newrecord = new AgentUsers();
                    $action_type = 'add';
                } else {
                    $profile_id = $newrecord['id'];
                }
            } else {
                $newrecord = new AgentUsers();
            }
            $newrecord->user_type_id = $usertype;
            $newrecord->travel_name = $agent_name;
            $newrecord->owner_name = $owner_name;
            $newrecord->office_no = $office_no;
            $newrecord->total_business_year = $total_year_of_business;
            $newrecord->city_id = $city_id;
            $newrecord->logo = $agent_logo;
            $newrecord->status = 1;
            $newrecord->pan_card = $pan_card_no;
            $newrecord->adhar_card = $adhar_card_no;
            if (!empty($pan_card_url)) {
                $newrecord->pan_card_url = $pan_card_url;
            }

            if (!empty($addar_card_front)) {
                $newrecord->adhar_card_url = $addar_card_front;
            }
            if (!empty($addar_card_back)) {
                $newrecord->adhar_card_back_url = $addar_card_back;
            }
            $newrecord->registration_document_url = $registration_document_url;
            $newrecord->registration_document = $registration_document;
            $newrecord->created_at = date("Y-m-d H:i:s");
            $newrecord->updated_at = date("Y-m-d H:i:s");
            $newrecord->save();

            $profile_id = $newrecord->id;

            if ($action_type == 'edit') {
                $bank_account_id = $request->get('bank_account_id');
                if (!empty($bank_account_id)) {
                    $bank_account = BankAccount::find($bank_account_id);

                    if (isset($bank_document_url_status)) {
                        $bank_document_url_status = $bank_document_url_status;
                    } else {
                        $bank_document_url_status = $bank_account->bank_document_url_status;
                    }
                } else {
                    $bankmapping = UserBankMapping::where('user_id', $user_id)->first();
                    if (!empty($bankmapping)) {
                        $bank_account_id = $bankmapping['bank_account_id'];
                        $bank_account = BankAccount::find($bank_account_id);
                        $action_type = 'edit';
                    } else {
                        $bank_account = new BankAccount();
                        $action_type = 'add';
                    }
                }
            } else {
                $bank_account = new BankAccount();
                $action_type = 'add';
            }
            $bank_account->account_holder_name = $account_holder_name;
            $bank_account->ifsc_code = $ifsc_code;
            $bank_account->bank_name = $bank_name;
            $bank_account->branch_name = $branch_name;
            $bank_account->account_number = $account_number;
            if (!empty($cheque_book_url)) {
                $bank_account->document_url = $cheque_book_url;
            }

            if (isset($bank_document_url_status) && $action_type == 'edit') {
                $bank_account->bank_document_url_status = $bank_document_url_status;
            }

            $bank_account->save();

            if ($action_type == 'add') {
                $bank_mapping = new UserBankMapping();
                $bank_mapping->user_id = $user_id;
                $bank_mapping->bank_account_id = $bank_account->id;
                $bank_mapping->created_at = date("Y-m-d H:i:s");
                $bank_mapping->updated_at = date("Y-m-d H:i:s");
                $bank_mapping->save();

                $user_step = User::find($user_id);
                $user_step->profile_completion_step = '3';
                $user_step->save();
            }
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
            //$profile_id = $user_id;
        } else if ($usertype == '') {
        } else {
            $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $profile_id)->first();

            if (empty($UserWorkProfile)) {
                $insert = new UserWorkProfile();
                $insert->user_id = $user_id;
                $insert->user_type_id = $usertype;
                $insert->profile_id = $profile_id;
                $insert->save();
            }
        }

        if (!empty($account_holder_name)) {
            $message = 'User Added Successfully';
            Session::flash('message', $message);
            //return redirect(route('admin.register.index', ['panel' => Session::get('panel')]))->withMessage($message);
        }
        /* else {
        $message = 'User Added Successfully';
        return redirect(route('admin.register.otp', ['panel' => Session::get('panel'),'id'=>$user_id]))->withMessage($message);
        } */

        echo $user_id . "-" . $profile_id;
    }

    public function storesecond(Request $request)
    {

        $user = Auth::user();

        // dd($request->all());
        $id = $request->get('id', null);
        $usertype = '3';
        $user_id = $request->get('user_id');
        $agent_id = $request->get('hid_agent_id');

        $vehicle_number_first_four_char = $request->get('vehicle_number_first_four_char');
        $vehicle_number_two_char = $request->get('vehicle_number_two_char');
        $vehicle_number_last_four_char = $request->get('vehicle_number_last_four_char');

        $vehicle_number = strtoupper($vehicle_number_first_four_char) . "-" . strtoupper($vehicle_number_two_char) . "-" . $vehicle_number_last_four_char; //$request->get('vehicle_number');

        $state = $request->get('state');
        $city = $request->get('city');
        $owner_name = $request->get('owner_name_rc');
        $brand_id = $request->get('brand_id');
        $model_id = $request->get('model_id');
        $registration_year = $request->get('registration_year');
        $fuel_type_id = $request->get('fuel_type_id');
        $colour_id = $request->get('colour_id');
        $vehicle_type_id = $request->get('vehicle_type_id');

        $driver_mobile_numebr = $request->get('driver_mobile_numebr');
        $driver_first_name = $request->get('driver_first_name');
        $driver_last_name = $request->get('driver_last_name');
        $year_of_experience = $request->get('year_of_experience');
        $adhar_card_no = $request->get('adhar_card_no');
        $driving_licence_no = $request->get('driving_licence_no');
        $pan_card_number = $request->get('pan_card_number');
        $street_address = $request->get('street_address');
        $city_name = $request->get('city_name');
        $pincode = $request->get('pincode');
        $driving_licence_expiry_date = $request->get('driving_licence_expiry_date');

        $insurance_exp_date = $request->get('insurance_exp_date1');
        $permit_exp_date = $request->get('permit_exp_date1');
        $fitness_exp_date = $request->get('fitness_exp_date1');
        $puc_exp_date = $request->get('puc_exp_date1');

        $action_type = $request->get('action_type');

        if (!empty($request->file('rc_front_url'))) {

            $file = $request->file('rc_front_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $rc_front_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if (!empty($request->get('vehicle_id'))) {
                $vupdate = Vehicles::find($request->get('vehicle_id'));
                $vupdate->rc_front_url_status = '2';
                $vupdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $rc_front_url = "";
            } else {
                $rc_front_url = $request->get('edit_rc_front_url');
            }
        }

        if (!empty($request->file('rc_back_url'))) {

            $file = $request->file('rc_back_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $rc_back_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if (!empty($request->get('vehicle_id'))) {
                $vupdate = Vehicles::find($request->get('vehicle_id'));
                $vupdate->rc_back_url_status = '2';
                $vupdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $rc_back_url = "";
            } else {
                $rc_back_url = $request->get('edit_rc_back_url');
            }
        }

        if (!empty($request->file('dl_front_url'))) {

            $file = $request->file('dl_front_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dl_front_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $dl_front_url = "";
            } else {
                $dl_front_url = $request->get('edit_dl_front_url');
            }
        }

        if (!empty($request->file('dl_back_url'))) {

            $file = $request->file('dl_back_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dl_back_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $dl_back_url = "";
            } else {
                $dl_back_url = $request->get('edit_dl_back_url');
            }
        }

        if (!empty($request->file('police_verification_url'))) {

            $file = $request->file('police_verification_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $police_verification_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('police_verification_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $police_verification_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$police_verification_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $police_verification_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $police_verification_url = "";
            } else {
                $police_verification_url = $request->get('edit_police_verification_url');
            }
        }

        if (!empty($request->file('d_pan_card_url'))) {
            /* $file = $request->get('d_pan_card_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $d_pan_card_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$d_pan_card_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $d_pan_card_url = env('S3_BUCKET_URL').$filePath; */

            $file = $request->file('d_pan_card_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $d_pan_card_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($action_type == 'add') {
                $d_pan_card_url = "";
            } else {
                $d_pan_card_url = $request->get('edit_d_pan_card_url');
            }
        }

        if (!empty($request->file('d_adhar_card_url'))) {
            /* $file = $request->get('d_adhar_card_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $d_adhar_card_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$d_adhar_card_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $d_adhar_card_url = env('S3_BUCKET_URL').$filePath; */

            $file = $request->file('d_adhar_card_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $d_adhar_card_url = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($action_type == 'add') {
                $d_adhar_card_url = "";
            } else {
                $d_adhar_card_url = $request->get('edit_d_adhar_card_url');
            }
        }

        if (!empty($request->file('adhar_card_back_url'))) {

            $file = $request->file('adhar_card_back_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $adhar_card_back_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('adhar_card_back_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $adhar_card_back_url_name = 'adharcard-backurl-'.rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$adhar_card_back_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $adhar_card_back_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $adhar_card_back_url = "";
            } else {
                $adhar_card_back_url = $request->get('edit_adhar_card_back_url');
            }
        }

        if (!empty($request->file('vehicle_front'))) {

            $file = $request->file('vehicle_front');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle_front = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if (!empty($request->get('vehicle_id'))) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $request->get('vehicle_id'))->where('vehicle_photos_view_master_id', 1)->first();
                if (!empty($front_img)) {
                    $front_img->image_url_status = '2';
                    $front_img->save();
                }
            }
        } else {
            if ($action_type == 'add') {
                $vehicle_front = "";
            } else {
                $vehicle_front = $request->get('edit_vehicle_front');
            }
        }

        if (!empty($request->file('vehicle_back'))) {

            $file = $request->file('vehicle_back');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle_back = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('vehicle_back');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $vehicle_back_name = 'vehicle-back-'.rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$vehicle_back_name;
            Storage::disk('s3')->put($filePath, $image);
            $vehicle_back = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $vehicle_back = "";
            } else {
                $vehicle_back = $request->get('edit_vehicle_back');
            }
        }

        if (!empty($request->file('vehicle_left'))) {

            $file = $request->file('vehicle_left');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle_left = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('vehicle_left');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $vehicle_left_name = 'vehicle-left-'.rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$vehicle_left_name;
            Storage::disk('s3')->put($filePath, $image);
            $vehicle_left = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $vehicle_left = "";
            } else {
                $vehicle_left = $request->get('edit_vehicle_left');
            }
        }

        if (!empty($request->file('vehicle_right'))) {

            $file = $request->file('vehicle_right');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle_right = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('vehicle_right');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $vehicle_right_name = 'vehicle-right-'.rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$vehicle_right_name;
            Storage::disk('s3')->put($filePath, $image);
            $vehicle_right = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $vehicle_right = "";
            } else {
                $vehicle_right = $request->get('edit_vehicle_right');
            }
        }

        if (!empty($request->file('vehicle_interior'))) {
            /* $file = $request->get('vehicle_interior');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $vehicle_interior_name = 'vehicle-interior-'.rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$vehicle_interior_name;
            Storage::disk('s3')->put($filePath, $image);
            $vehicle_interior = env('S3_BUCKET_URL').$filePath; */

            $file = $request->file('vehicle_interior');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $vehicle_interior = env('S3_BUCKET_URL') . $filePath;
        } else {
            if ($action_type == 'add') {
                $vehicle_interior = "";
            } else {
                $vehicle_interior = $request->get('edit_vehicle_interior');
            }
        }

        if (!empty($request->file('insurance_doc_url1'))) {

            $file = $request->file('insurance_doc_url1');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $insurance_doc_url1 = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            if (!empty($request->get('vehicle_id'))) {
                $vupdate = Vehicles::find($request->get('vehicle_id'));
                $vupdate->insurance_doc_url_status = '2';
                $vupdate->save();
            }
        } else {
            if ($action_type == 'add') {
                $insurance_doc_url1 = "";
            } else {
                $insurance_doc_url1 = $request->get('edit_insurance_doc_url1');
            }
        }

        if (!empty($request->file('permit_doc_url1'))) {

            $file = $request->file('permit_doc_url1');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $permit_doc_url1 = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('permit_doc_url1');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $permit_doc_url1_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$permit_doc_url1_name;
            Storage::disk('s3')->put($filePath, $image);
            $permit_doc_url1 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $permit_doc_url1 = "";
            } else {
                $permit_doc_url1 = $request->get('edit_permit_doc_url1');
            }
        }

        if (!empty($request->file('fitness_doc_url1'))) {

            $file = $request->file('fitness_doc_url1');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fitness_doc_url1 = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('fitness_doc_url1');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $fitness_doc_url1_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$fitness_doc_url1_name;
            Storage::disk('s3')->put($filePath, $image);
            $fitness_doc_url1 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $fitness_doc_url1 = "";
            } else {
                $fitness_doc_url1 = $request->get('edit_fitness_doc_url1');
            }
        }

        if (!empty($request->file('puc_doc_url1'))) {

            $file = $request->file('puc_doc_url1');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $puc_doc_url1 = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('puc_doc_url1');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $puc_doc_url1_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$puc_doc_url1_name;
            Storage::disk('s3')->put($filePath, $image);
            $puc_doc_url1 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $puc_doc_url1 = "";
            } else {
                $puc_doc_url1 = $request->get('edit_puc_doc_url1');
            }
        }

        if (!empty($request->file('agreement_doc_url'))) {

            $file = $request->file('agreement_doc_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $agreement_doc_url1 = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('agreement_doc_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $agreement_doc_url1_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$agreement_doc_url1_name;
            Storage::disk('s3')->put($filePath, $image);
            $agreement_doc_url1 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $agreement_doc_url1 = "";
            } else {
                $agreement_doc_url1 = $request->get('edit_agreement_doc_url');
            }
        }

        if ($action_type == 'add') {
            $new = new Vehicles();
        } else {
            $vehicle_id = $request->get('vehicle_id');
            if (empty($vehicle_id)) {

                $veh = Vehicles::where('user_id', '=', $user_id)->first();
                if (!empty($veh)) {
                    $vehicle_id = $veh->id;
                    $new = Vehicles::find($vehicle_id);
                } else {
                    $new = new Vehicles();
                }
            } else {
                $new = Vehicles::find($vehicle_id);
            }
        }

        $profile_completion_step = '0';
        if (!empty($vehicle_type_id)) {
            $profile_completion_step = '1';
        }

        if (!empty($insurance_exp_date)) {
            $profile_completion_step = '2';
        }

        $new->user_id = $user_id;
        $new->vehicle_number = $vehicle_number;
        $new->city = $city;
        $new->state = $state;
        $new->vehicle_type_id = $vehicle_type_id;
        $new->brand_id = $brand_id;
        $new->model_id = $model_id;
        $new->registration_year = $registration_year;
        $new->fuel_type_id = $fuel_type_id;
        $new->colour_id = $colour_id;
        $new->owner_name = $owner_name;
        $new->rc_front_url = $rc_front_url;
        $new->rc_back_url = $rc_back_url;
        $new->insurance_doc_url = $insurance_doc_url1;
        $new->insurance_exp_date = $insurance_exp_date;
        $new->permit_doc_url = $permit_doc_url1;
        $new->permit_exp_date = $permit_exp_date;
        $new->fitness_doc_url = $fitness_doc_url1;
        $new->fitness_exp_date = $fitness_exp_date;
        $new->status = 1;
        $new->puc_doc_url = $puc_doc_url1;
        $new->puc_exp_date = $puc_exp_date;
        $new->agreement_doc_url = $agreement_doc_url1;
        $new->completion_steps = $profile_completion_step;
        $new->created_at = date("Y-m-d H:i:s");
        $new->updated_at = date("Y-m-d H:i:s");
        $new->save();

        if ($action_type == 'add') {
            //$vehicle_id = $new->id;
            if (!empty($vehicle_front)) {
                $front_img = new VehiclePhotoMapping();
                $front_img->vehicle_id = $new->id;
                $front_img->vehicle_photos_view_master_id = '1';
                $front_img->image_url = $vehicle_front;
                $front_img->created_at = date("Y-m-d H:i:s");
                $front_img->updated_at = date("Y-m-d H:i:s");
                $front_img->save();
            }
        } else {
            if (!empty($vehicle_id)) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', 1)->first();
                if (!empty($front_img)) {
                    $front_img->image_url = $vehicle_front;
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                } else {
                    if (!empty($vehicle_front)) {
                        $front_img = new VehiclePhotoMapping();
                        $front_img->vehicle_id = $vehicle_id;
                        $front_img->vehicle_photos_view_master_id = '1';
                        $front_img->image_url = $vehicle_front;
                        $front_img->created_at = date("Y-m-d H:i:s");
                        $front_img->updated_at = date("Y-m-d H:i:s");
                        $front_img->save();
                    }
                }
            } else {
                if (!empty($vehicle_front)) {
                    $front_img = new VehiclePhotoMapping();
                    $front_img->vehicle_id = $new->id;
                    $front_img->vehicle_photos_view_master_id = '1';
                    $front_img->image_url = $vehicle_front;
                    $front_img->created_at = date("Y-m-d H:i:s");
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                }
            }
        }

        if ($action_type == 'add') {
            if (!empty($vehicle_back)) {
                $front_img = new VehiclePhotoMapping();
                $front_img->vehicle_id = $new->id;
                $front_img->vehicle_photos_view_master_id = '2';
                $front_img->image_url = $vehicle_back;
                $front_img->created_at = date("Y-m-d H:i:s");
                $front_img->updated_at = date("Y-m-d H:i:s");
                $front_img->save();
            }
        } else {
            if (!empty($vehicle_id)) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', 2)->first();
                if (!empty($front_img)) {
                    $front_img->image_url = $vehicle_back;
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                } else {
                    if (!empty($vehicle_back)) {
                        $front_img = new VehiclePhotoMapping();
                        $front_img->vehicle_id = $vehicle_id;
                        $front_img->vehicle_photos_view_master_id = '2';
                        $front_img->image_url = $vehicle_back;
                        $front_img->created_at = date("Y-m-d H:i:s");
                        $front_img->updated_at = date("Y-m-d H:i:s");
                        $front_img->save();
                    }
                }
            } else {
                if (!empty($vehicle_back)) {
                    $front_img = new VehiclePhotoMapping();
                    $front_img->vehicle_id = $new->id;
                    $front_img->vehicle_photos_view_master_id = '2';
                    $front_img->image_url = $vehicle_back;
                    $front_img->created_at = date("Y-m-d H:i:s");
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                }
            }
        }

        if ($action_type == 'add') {
            if (!empty($vehicle_left)) {
                $front_img = new VehiclePhotoMapping();
                $front_img->vehicle_id = $new->id;
                $front_img->vehicle_photos_view_master_id = '4';
                $front_img->image_url = $vehicle_left;
                $front_img->created_at = date("Y-m-d H:i:s");
                $front_img->updated_at = date("Y-m-d H:i:s");
                $front_img->save();
            }
        } else {
            if (!empty($vehicle_id)) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', 4)->first();
                if (!empty($front_img)) {
                    $front_img->image_url = $vehicle_left;
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                } else {
                    if (!empty($vehicle_left)) {
                        $front_img = new VehiclePhotoMapping();
                        $front_img->vehicle_id = $vehicle_id;
                        $front_img->vehicle_photos_view_master_id = '4';
                        $front_img->image_url = $vehicle_left;
                        $front_img->created_at = date("Y-m-d H:i:s");
                        $front_img->updated_at = date("Y-m-d H:i:s");
                        $front_img->save();
                    }
                }
            } else {
                if (!empty($vehicle_left)) {
                    $front_img = new VehiclePhotoMapping();
                    $front_img->vehicle_id = $new->id;
                    $front_img->vehicle_photos_view_master_id = '4';
                    $front_img->image_url = $vehicle_left;
                    $front_img->created_at = date("Y-m-d H:i:s");
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                }
            }
        }

        if ($action_type == 'add') {
            if (!empty($vehicle_right)) {
                $front_img = new VehiclePhotoMapping();
                $front_img->vehicle_id = $new->id;
                $front_img->vehicle_photos_view_master_id = '5';
                $front_img->image_url = $vehicle_right;
                $front_img->created_at = date("Y-m-d H:i:s");
                $front_img->updated_at = date("Y-m-d H:i:s");
                $front_img->save();
            }
        } else {
            if (!empty($vehicle_id)) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', 5)->first();
                if (!empty($front_img)) {
                    $front_img->image_url = $vehicle_right;
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                } else {
                    if (!empty($vehicle_right)) {
                        $front_img = new VehiclePhotoMapping();
                        $front_img->vehicle_id = $vehicle_id;
                        $front_img->vehicle_photos_view_master_id = '5';
                        $front_img->image_url = $vehicle_right;
                        $front_img->created_at = date("Y-m-d H:i:s");
                        $front_img->updated_at = date("Y-m-d H:i:s");
                        $front_img->save();
                    }
                }
            } else {
                if (!empty($vehicle_right)) {
                    $front_img = new VehiclePhotoMapping();
                    $front_img->vehicle_id = $new->id;
                    $front_img->vehicle_photos_view_master_id = '5';
                    $front_img->image_url = $vehicle_right;
                    $front_img->created_at = date("Y-m-d H:i:s");
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                }
            }
        }

        if ($action_type == 'add') {
            if (!empty($vehicle_interior)) {
                $front_img = new VehiclePhotoMapping();
                $front_img->vehicle_id = $new->id;
                $front_img->vehicle_photos_view_master_id = '6';
                $front_img->image_url = $vehicle_interior;
                $front_img->created_at = date("Y-m-d H:i:s");
                $front_img->updated_at = date("Y-m-d H:i:s");
                $front_img->save();
            }
        } else {
            if (!empty($vehicle_id)) {
                $front_img = VehiclePhotoMapping::where('vehicle_id', $vehicle_id)->where('vehicle_photos_view_master_id', 6)->first();
                if (!empty($front_img)) {
                    $front_img->image_url = $vehicle_interior;
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                } else {
                    if (!empty($vehicle_interior)) {
                        $front_img = new VehiclePhotoMapping();
                        $front_img->vehicle_id = $new->id;
                        $front_img->vehicle_photos_view_master_id = '6';
                        $front_img->image_url = $vehicle_interior;
                        $front_img->created_at = date("Y-m-d H:i:s");
                        $front_img->updated_at = date("Y-m-d H:i:s");
                        $front_img->save();
                    }
                }
            } else {
                if (!empty($vehicle_interior)) {
                    $front_img = new VehiclePhotoMapping();
                    $front_img->vehicle_id = $new->id;
                    $front_img->vehicle_photos_view_master_id = '6';
                    $front_img->image_url = $vehicle_interior;
                    $front_img->created_at = date("Y-m-d H:i:s");
                    $front_img->updated_at = date("Y-m-d H:i:s");
                    $front_img->save();
                }
            }
        }

        /* Driver Data */
        if ($action_type == 'add') {
            $newrecord = new Drivers();
            $action_type = 'add';
            $usertype = '4';
        } else {
            $driver_id = $request->get('driver_id');
            if (!empty($driver_id)) {
                $newrecord = Drivers::find($driver_id);
                $action_type = 'edit';
                $usertype = '4';
            } else {
                $vehicle_id = $request->get('vehicle_id');
                if (empty($vehicle_id)) {
                    $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('status', '1')->first();
                    if (!empty($v)) {
                        $driver_id = $v->driver_id;
                    }
                }

                if (!empty($driver_id)) {
                    $newrecord = Drivers::find($driver_id);
                    $action_type = 'edit';
                    $usertype = '4';
                } else {
                    $newrecord = new Drivers();
                    $action_type = 'add';
                    $usertype = '4';
                }
            }
        }
        $newrecord->mobile_numebr = $driver_mobile_numebr;
        $newrecord->first_name = $driver_first_name;
        $newrecord->last_name = $driver_last_name;
        $newrecord->adhar_card_no = $adhar_card_no;
        $newrecord->driving_licence_no = $driving_licence_no;
        $newrecord->pan_card_number = $pan_card_number;
        $newrecord->driving_licence_expiry_date = $driving_licence_expiry_date;
        $newrecord->street_address = $street_address;
        $newrecord->city = $city_name;
        $newrecord->pincode = $pincode;
        $newrecord->dl_front_url = $dl_front_url;
        $newrecord->dl_back_url = $dl_back_url;
        $newrecord->police_verification_url = $police_verification_url;
        $newrecord->pan_card_url = $d_pan_card_url;
        $newrecord->adhar_card_url = $d_adhar_card_url;
        $newrecord->adhar_card_back_url = $adhar_card_back_url;
        $newrecord->year_of_experience = $year_of_experience;
        $newrecord->agent_id = $agent_id;
        $newrecord->created_at = date("Y-m-d H:i:s");
        $newrecord->updated_at = date("Y-m-d H:i:s");

        $newrecord->save();
        $profile_id = '';
        if ($action_type == 'add') {
            $driver_id = $newrecord->id;
            $vehicle_id = $new->id;
            $profile_id = $newrecord->id;

            $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('status', '1')->where('driver_id', '=', $driver_id)->first();
            if (empty($v)) {
                $save = new VehicleDrivingMapping();
                $save->vehicle_id = $new->id;
                $save->driver_id = $newrecord->id;
                $save->status = 1;
                $save->save();
            }
        } else {

            $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('status', '1')->where('driver_id', '=', $driver_id)->first();
            if (empty($v)) {
                $save = new VehicleDrivingMapping();
                $save->vehicle_id = $new->id;
                $save->driver_id = $newrecord->id;
                $save->status = 1;
                $save->save();
            } else {
                //$profile_id = $v->vehicle_id;
                $profile_id = $driver_id;
                /* $save = new VehicleDrivingMapping(); */
                /* $v->vehicle_id = $profile_id;
                $v->driver_id = $driver_id;
                $v->status = 1;
                $v->save(); */
            }

            /* if (empty($driver_id)) {
            $driver_id = $newrecord->id;
            $vehicle_id = $new->id;
            $profile_id = $driver_id;
            $save = new VehicleDrivingMapping();
            $save->vehicle_id = $new->id;
            $save->driver_id = $newrecord->id;
            $save->status = 1;
            $save->save();
            }else{
            $profile_id = $driver_id;
            $save = new VehicleDrivingMapping();
            $save->vehicle_id = $vehicle_id;
            $save->driver_id = $driver_id;
            $save->status = 1;
            $save->save();
            } */
        }

        /* if($usertype == '6'){
        $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id',$usertype)->where('profile_id',$user_id)->first();
        $profile_id = $user_id;
        }else{
        $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id',$usertype)->where('profile_id',$profile_id)->first();
        }
        if(empty($UserWorkProfile)){
        $insert = new UserWorkProfile();
        $insert->user_id = $user_id;
        $insert->user_type_id = $usertype;
        $insert->profile_id = $profile_id;
        $insert->save();
        } */
        $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $profile_id)->where('status', '1')->first();
        if (empty($UserWorkProfile)) {
            $insert = new UserWorkProfile();
            $insert->user_id = $user_id;
            $insert->user_type_id = $usertype;
            $insert->profile_id = $profile_id;
            $insert->save();
        } else {
            $UserWorkProfile->user_id = $user_id;
            $UserWorkProfile->user_type_id = $usertype;
            $UserWorkProfile->profile_id = $profile_id;
            $UserWorkProfile->save();
        }

        if (!empty($driver_mobile_numebr)) {
            $message = 'User Added Successfully';
            Session::flash('message', $message);
            //return redirect(route('admin.register.index', ['panel' => Session::get('panel')]))->withMessage($message);
        }
        /* else {
        $message = 'User Added Successfully';
        return redirect(route('admin.register.otp', ['panel' => Session::get('panel'),'id'=>$user_id]))->withMessage($message);
        } */

        echo $user_id . "-" . $vehicle_id . "-" . $driver_id;
    }

    public function storethird(Request $request)
    {

        $users = Auth::user();

        $id = $request->get('id', null);
        $usertype = $request->get('usertype');
        $first_name = $request->get('hid_first_name');
        $last_name = $request->get('hid_last_name');
        $name = $first_name . " " . $last_name;
        $state = $request->get('hid_state');
        $city_id = $request->get('hid_city_id');
        $emailid = $request->get('hid_email');
        $mobile_number = $request->get('hid_mobile_number');
        $gender = $request->get('hid_gender');

        $account_holder_name = $request->get('account_holder_name2');
        $ifsc_code = $request->get('ifsc_code2');
        $bank_name = $request->get('bank_name2');
        $branch_name = $request->get('branch_name2');
        $account_number = $request->get('account_number2');
        //$confirm_account_number = $request->get('confirm_account_number');

        $action_type = $request->get('action_type');

        $usertype = 5;
        if ($action_type == 'add') {
            $user = new User();
        } else {
            $user_id2 = $request->get('id');
            $user = User::find($user_id2);
        }
        $user->emailid = $emailid;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->mobile_number = $mobile_number;
        $user->gender = $gender;
        $user->state = $state;
        $user->city_id = $city_id;
        $user->user_type_id = $usertype;
        $user->created_at = date("Y-m-d H:i:s");
        $user->updated_at = date("Y-m-d H:i:s");
        $user->save();

        if (!empty($request->get('id'))) {
            $user_id = $id;
        } else {
            $user_id = $user->id;
        }

        //$usertype = '5';
        $agent_id = $request->get('hid_agent_id');
        $vehicle_number = $request->get('vehicle_number');
        $state = $request->get('state1');
        $city = $request->get('city1');
        $owner_name = $request->get('owner_name_rc2');
        $brand_id = $request->get('brand_id2');
        $model_id = $request->get('model_id2');
        $registration_year = $request->get('registration_year2');
        $fuel_type_id = $request->get('fuel_type_id2');
        $colour_id = $request->get('colour_id2');
        $vehicle_type_id = $request->get('vehicle_type_id2');

        $driver_mobile_numebr = $request->get('driver_mobile_numebr2');
        $driver_first_name = $request->get('driver_first_name2');
        $driver_last_name = $request->get('driver_last_name2');
        $year_of_experience = $request->get('year_of_experience2');
        $adhar_card_no = $request->get('dc_adhar_card_no');
        $driving_licence_no = $request->get('driving_licence_no2');
        $pan_card_number = $request->get('dc_pan_card_number');
        $street_address = $request->get('street_address2');
        $city_name = $request->get('city_name2');
        $driver_city_name = $request->get('city_name');
        $pincode = $request->get('pincode2');
        $driving_licence_expiry_date = $request->get('driving_licence_expiry_date2');

        $insurance_exp_date = $request->get('insurance_exp_date');
        $permit_exp_date = $request->get('permit_exp_date');
        $fitness_exp_date = $request->get('fitness_exp_date');
        $puc_exp_date = $request->get('puc_exp_date');

        if (!empty($request->file('dl_front_url2'))) {

            $file = $request->file('dl_front_url2');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dl_front_url2 = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('dl_front_url2');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $dl_front_url2_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$dl_front_url2_name;
            Storage::disk('s3')->put($filePath, $image);
            $dl_front_url2 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $dl_front_url2 = "";
            } else {
                $dl_front_url2 = $request->get('edit_dl_front_url2');
            }
        }

        if (!empty($request->file('dl_back_url2'))) {

            $file = $request->file('dl_back_url2');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dl_back_url2 = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('dl_back_url2');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $dl_back_url2_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$dl_back_url2_name;
            Storage::disk('s3')->put($filePath, $image);
            $dl_back_url2 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $dl_back_url2 = "";
            } else {
                $dl_back_url2 = $request->get('edit_dl_back_url2');
            }
        }

        if (!empty($request->file('rc_front_url2'))) {

            $file = $request->file('rc_front_url2');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $rc_front_url2 = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('rc_front_url2');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $rc_front_url2_name = rand('11111','99999').time() . '.'.$image_ext;
            //$document_file_name = rand('111','999').time().$file->getClientOriginalName();
            $filePath = "/".$rc_front_url2_name;
            Storage::disk('s3')->put($filePath, $image);
            $rc_front_url2 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $rc_front_url2 = "";
            } else {
                $rc_front_url2 = $request->get('edit_rc_front_url2');
            }
        }

        if (!empty($request->file('rc_back_url2'))) {

            $file = $request->file('rc_back_url2');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $rc_back_url2 = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('rc_back_url2');
            //$driving_license_front_file_name = rand('111','999').time().$file->getClientOriginalName();
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $rc_back_url2_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$rc_back_url2_name;
            Storage::disk('s3')->put($filePath, $image);
            $rc_back_url2 = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $rc_back_url2 = "";
            } else {
                $rc_back_url2 = $request->get('edit_rc_back_url2');
            }
        }

        if (!empty($request->file('insurance_doc_url'))) {

            $file = $request->file('insurance_doc_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $insurance_doc_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('insurance_doc_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $insurance_doc_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$insurance_doc_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $insurance_doc_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $insurance_doc_url = "";
            } else {
                $insurance_doc_url = $request->get('edit_insurance_doc_url');
            }
        }

        if (!empty($request->file('permit_doc_url'))) {

            $file = $request->file('permit_doc_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $permit_doc_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('permit_doc_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $permit_doc_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$permit_doc_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $permit_doc_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $permit_doc_url = "";
            } else {
                $permit_doc_url = $request->get('edit_permit_doc_url');
            }
        }

        if (!empty($request->file('fitness_doc_url'))) {

            $file = $request->file('fitness_doc_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fitness_doc_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('fitness_doc_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $fitness_doc_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$fitness_doc_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $fitness_doc_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $fitness_doc_url = "";
            } else {
                $fitness_doc_url = $request->get('edit_fitness_doc_url');
            }
        }

        if (!empty($request->file('puc_doc_url'))) {

            $file = $request->file('puc_doc_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $puc_doc_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('puc_doc_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $puc_doc_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$puc_doc_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $puc_doc_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $puc_doc_url = "";
            } else {
                $puc_doc_url = $request->get('edit_puc_doc_url');
            }
        }

        if (!empty($request->file('agreement_doc_url1'))) {

            $file = $request->file('agreement_doc_url1');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $agreement_doc_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('agreement_doc_url1');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $agreement_doc_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$agreement_doc_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $agreement_doc_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $agreement_doc_url = "";
            } else {
                $agreement_doc_url = $request->get('edit_agreement_doc_url1');
            }
        }

        if (!empty($request->file('dc_pan_card_url'))) {

            $file = $request->file('dc_pan_card_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dc_pan_card_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('dc_pan_card_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $dc_pan_card_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$dc_pan_card_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $dc_pan_card_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $dc_pan_card_url = "";
            } else {
                $dc_pan_card_url = $request->get('edit_dc_pan_card_url');
            }
        }

        if (!empty($request->file('dc_adhar_card_url'))) {

            $file = $request->file('dc_adhar_card_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dc_adhar_card_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('dc_adhar_card_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $dc_adhar_card_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$dc_adhar_card_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $dc_adhar_card_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $dc_adhar_card_url = "";
            } else {
                $dc_adhar_card_url = $request->get('edit_dc_adhar_card_url');
            }
        }

        if (!empty($request->file('dc_adhar_card_back_url'))) {

            $file = $request->file('dc_adhar_card_back_url');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $dc_adhar_card_back_url = env('S3_BUCKET_URL') . $filePath;

            /* $file = $request->get('dc_adhar_card_back_url');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $dc_adhar_card_back_url_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$dc_adhar_card_back_url_name;
            Storage::disk('s3')->put($filePath, $image);
            $dc_adhar_card_back_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $dc_adhar_card_back_url = "";
            } else {
                $dc_adhar_card_back_url = $request->get('edit_dc_adhar_card_back_url');
            }
        }

        if (!empty($request->file('cheque_book2'))) {

            $file = $request->file('cheque_book2');

            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = "/" . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $cheque_book_url = env('S3_BUCKET_URL') . $filePath;

            if (!empty($request->get('id')) && $action_type == 'edit') {
                $userProUpdate = User::find($request->get('id'));
                $userProUpdate->is_approved = 2;
                $userProUpdate->save();
            }

            /* $file = $request->get('cheque_book2');
            $image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$file));
            $image = base64_decode(preg_replace('#^data:application/\w+;base64,#i', '',$image));
            $pos  = strpos($file, ';');
            $type = explode(':', substr($file, 0, $pos))[1];
            $image_ext = explode('/', $type)[1];
            $cheque_book2_name = rand('11111','99999').time() . '.'.$image_ext;
            $filePath = "/".$cheque_book2_name;
            Storage::disk('s3')->put($filePath, $image);
            $cheque_book_url = env('S3_BUCKET_URL').$filePath; */
        } else {
            if ($action_type == 'add') {
                $cheque_book_url = "";
            } else {
                $cheque_book_url = $request->get('edit_cheque_book2');
            }
        }

        /* if ($action_type == 'add') {
        $new = new Vehicles();
        }else{
        $vehicle_id = $request->get('vehicle_id');
        if (!empty($vehicle_id)) {
        $new = Vehicles::find($vehicle_id);
        }else{
        $new = new Vehicles();
        }
        } */

        if ($action_type == 'add') {
            $new = new Vehicles();
        } else {
            $vehicle_id = $request->get('vehicle_id');
            if (empty($vehicle_id)) {

                $veh = Vehicles::where('user_id', '=', $user_id)->first();
                if (!empty($veh)) {
                    $vehicle_id = $veh->id;
                    $new = Vehicles::find($vehicle_id);
                } else {
                    $new = new Vehicles();
                }
            } else {
                $new = Vehicles::find($vehicle_id);
            }
        }

        $new->user_id = $user_id;
        $new->vehicle_number = $vehicle_number;
        $new->city = $city;
        $new->state = $state;
        $new->vehicle_type_id = $vehicle_type_id;
        $new->brand_id = $brand_id;
        $new->model_id = $model_id;
        $new->registration_year = $registration_year;
        $new->fuel_type_id = $fuel_type_id;
        $new->colour_id = $colour_id;
        $new->owner_name = $owner_name;
        $new->rc_front_url = $rc_front_url2;
        $new->rc_back_url = $rc_back_url2;
        $new->insurance_doc_url = $insurance_doc_url;
        $new->insurance_exp_date = $insurance_exp_date;
        $new->permit_doc_url = $permit_doc_url;
        $new->permit_exp_date = $permit_exp_date;
        $new->fitness_doc_url = $fitness_doc_url;
        $new->fitness_exp_date = $fitness_exp_date;
        $new->status = 1;
        $new->puc_doc_url = $puc_doc_url;
        $new->puc_exp_date = $puc_exp_date;
        $new->agreement_doc_url = $agreement_doc_url;
        $new->created_at = date("Y-m-d H:i:s");
        $new->updated_at = date("Y-m-d H:i:s");
        $new->save();

        if ($action_type == 'add') {
            $vehicle_id = $new->id;
        } else {
            if (!empty($vehicle_id)) {
                $vehicle_id = $vehicle_id;
            } else {
                $vehicle_id = $new->id;
            }
        }

        /* Driver Data */
        /* if ($action_type == 'add') {
        $newrecord = new Drivers();
        }else{
        $driver_id = $request->get('driver_id');
        if (!empty($driver_id)) {
        $newrecord = Drivers::find($driver_id);
        }else{
        $newrecord = new Drivers();
        }
        } */

        if ($action_type == 'add') {
            $newrecord = new Drivers();
            $action_type = 'add';
            $usertype = '4';
        } else {
            $driver_id = $request->get('driver_id');
            if (!empty($driver_id)) {
                $newrecord = Drivers::find($driver_id);
                $action_type = 'edit';
                $usertype = '4';
            } else {
                $vehicle_id = $request->get('vehicle_id');
                if (empty($vehicle_id)) {
                    $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('status', '1')->first();
                    if (!empty($v)) {
                        $driver_id = $v->driver_id;
                    }
                }

                if (!empty($driver_id)) {
                    $newrecord = Drivers::find($driver_id);
                    $action_type = 'edit';
                    $usertype = '4';
                } else {
                    $newrecord = new Drivers();
                    $action_type = 'add';
                    $usertype = '4';
                }
            }
        }

        $newrecord->mobile_numebr = $driver_mobile_numebr;
        $newrecord->first_name = $driver_first_name;
        $newrecord->last_name = $driver_last_name;
        $newrecord->adhar_card_no = $adhar_card_no;
        $newrecord->driving_licence_no = $driving_licence_no;
        $newrecord->pan_card_number = $pan_card_number;
        $newrecord->driving_licence_expiry_date = $driving_licence_expiry_date;
        $newrecord->street_address = $street_address;
        $newrecord->city = $city_name;
        $newrecord->pincode = $pincode;
        $newrecord->dl_front_url = $dl_front_url2;
        $newrecord->dl_back_url = $dl_back_url2;
        //$newrecord->police_verification_url = $police_verification_url;
        $newrecord->pan_card_url = $dc_pan_card_url;
        $newrecord->adhar_card_url = $dc_adhar_card_url;
        $newrecord->adhar_card_back_url = $dc_adhar_card_back_url;
        $newrecord->year_of_experience = $year_of_experience;
        $newrecord->agent_id = 0;
        $newrecord->created_at = date("Y-m-d H:i:s");
        $newrecord->updated_at = date("Y-m-d H:i:s");
        $newrecord->save();


        /* if ($action_type == 'add' || $action_type == 'edit') {
        if (empty($driver_id)) {
        $profile_id = $newrecord->id;
        $save = new VehicleDrivingMapping();
        $save->vehicle_id = $new->id;
        $save->driver_id = $newrecord->id;
        $save->status = 1;
        $save->save();
        }else{
        $profile_id = $driver_id;
        
        $driver_id = $driver_id;
        $save = VehicleDrivingMapping::Where('vehicle_id',$vehicle_id)->where('driver_id',$profile_id)->first();
        $save->vehicle_id = $new->id;
        $save->driver_id = $profile_id;
        $save->status = 1;
        $save->save();
        }
        } */

        if ($action_type == 'add') {
            $driver_id = $newrecord->id;
            $vehicle_id = $new->id;
            $profile_id = $newrecord->id;

            $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('driver_id', '=', $driver_id)->where('status', '1')->first();
            if (empty($v)) {
                $save = new VehicleDrivingMapping();
                $save->vehicle_id = $new->id;
                $save->driver_id = $newrecord->id;
                $save->status = 1;
                $save->save();
            }
        } else {

            $v = VehicleDrivingMapping::where('vehicle_id', '=', $vehicle_id)->where('driver_id', '=', $driver_id)->where('status', '1')->first();
            if (empty($v)) {
                $save = new VehicleDrivingMapping();
                $save->vehicle_id = $new->id;
                $save->driver_id = $newrecord->id;
                $save->status = 1;
                $save->save();
            } else {
                $profile_id = $v->vehicle_id;
                /* $save = new VehicleDrivingMapping();
                $save->vehicle_id = $profile_id;
                $save->driver_id = $driver_id;
                $save->status = 1;
                $save->save(); */
            }
        }

        /* if ($action_type == 'add') {
        $bank_account = new BankAccount();
        $action_type = 'add'; 
        }else{
        $bank_account_id = $request->get('bank_account_id');
        if (!empty($bank_account_id)) {
        $action_type = 'edit';
        $bank_account = BankAccount::find($bank_account_id);
        }else{
        $bank_account = new BankAccount();
        $action_type = 'add';
        }
        } */

        if ($action_type == 'edit') {
            $bank_account_id = $request->get('bank_account_id');
            if (!empty($bank_account_id)) {
                $bank_account = BankAccount::find($bank_account_id);
            } else {
                $bankmapping = UserBankMapping::where('user_id', $user_id)->first();
                if (!empty($bankmapping)) {
                    $bank_account_id = $bankmapping['bank_account_id'];
                    $bank_account = BankAccount::find($bank_account_id);
                    $action_type = 'edit';
                } else {
                    $bank_account = new BankAccount();
                    $action_type = 'add';
                }
            }
        } else {
            $bank_account = new BankAccount();
            $action_type = 'add';
        }

        $bank_account->account_holder_name = $account_holder_name;
        $bank_account->ifsc_code = $ifsc_code;
        $bank_account->bank_name = $bank_name;
        $bank_account->branch_name = $branch_name;
        $bank_account->account_number = $account_number;
        if (!empty($cheque_book_url)) {
            $bank_account->document_url = $cheque_book_url;
        }
        $bank_account->save();

        if ($action_type == 'add') {
            $bank_mapping = new UserBankMapping();
            $bank_mapping->user_id = $user_id;
            $bank_mapping->bank_account_id = $bank_account->id;
            $bank_mapping->created_at = date("Y-m-d H:i:s");
            $bank_mapping->updated_at = date("Y-m-d H:i:s");
            $bank_mapping->save();
        }

        if ($action_type == 'add' || $action_type == 'edit') {
            if ($usertype == '6') {
                $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $user_id)->where('status', '1')->first();
                $profile_id = $user_id;
            } else {
                $UserWorkProfile = UserWorkProfile::where('user_id', $user_id)->where('user_type_id', $usertype)->where('profile_id', $driver_id)->where('status', '1')->first();
            }

            if (empty($UserWorkProfile)) {
                $insert = new UserWorkProfile();
                $insert->user_id = $user_id;
                $insert->user_type_id = $usertype;
                $insert->profile_id = $profile_id;
                $insert->save();
            }
        }

        if (!empty($driver_mobile_numebr)) {
            $message = 'User Added Successfully';
            Session::flash('message', $message);
            //return redirect(route('admin.register.index', ['panel' => Session::get('panel')]))->withMessage($message);
        }
        /* else {
        $message = 'User Added Successfully';
        return redirect(route('admin.register.otp', ['panel' => Session::get('panel'),'id'=>$user_id]))->withMessage($message);
        } */

        echo $user_id . "-" . $vehicle_id . "-" . $driver_id;
    }

    public function statecity()
    {
        $id = $_POST['id'];
        if ($id != "all") {
            $states = States::where('id', $id)->first();
            $isoCode = $states['isoCode'];

            $data = Cities::where('stateCode', $isoCode)->get();
        } else {

            $data = Cities::all();
        }
        if (isset($_POST['pagetype']) && $_POST['pagetype'] == 'index') {
            $s = '<option value="all" label="Select City"></option>';
            foreach ($data as $val) {
                $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
            }
            //$s .= '';
        } else if (isset($_POST['pagetype']) && $_POST['pagetype'] == 'indextop') {
            $s = '<select class="form-control" name="city_id1" id="city_id" required="" style="margin-left: -28px;"><option value="" label="Select City"></option>';
            foreach ($data as $val) {
                $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
            }
            $s .= '</select>';
        } else {
            $s = '<select class="form-control" name="city_id" id="city_id" required=""><option value="" label="Choose one"></option>';
            foreach ($data as $val) {
                $s .= '<option value="' . $val['id'] . '">' . $val["name"] . '</option>';
            }
            $s .= '</select>';
        }
        echo $s;
        exit;
    }

    public function models()
    {
        $id = $_POST['id'];
        $vehicle_type_id = $_POST['vehicle_type_id'];
        $data = VehicleBrandModels::where('brand_id', $id)->where('vehicle_type_id', $vehicle_type_id)->where('status', '1')->get();
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
        //$data = VehicleFuelType::where('model_id',$id)->where('status','1')->get();
        $data = VehicleFuelType::where('status', '1')->get();
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
        //$data = VehicleColour::where('model_id',$id)->where('status','1')->get();
        $data = VehicleColour::where('status', '1')->get();
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
        $user = $this->registerRepository->getByParams($params);

        $last_updated_id = $user->last_updated_id;
        $last_updated_user_details = User::find($last_updated_id);

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
        $drivers = Drivers::where('agent_id', $id)->where('status', '1')->get();

        $brand_id = $user->brand_id;
        $brands = VehicleBrands::where('id', '=', $brand_id)->first();

        $model_id = $user->model_id;
        $models = VehicleBrandModels::where('id', '=', $model_id)->first();

        $fuel_type_id = $user->fuel_type_id;
        $fuelType = VehicleFuelType::where('id', '=', $fuel_type_id)->first();

        $cities = Cities::all();
        $states = states::all();
        $profile_path = config('custom.upload.user.profile');
        $user_history = UserHistory::where('user_id', $id)->orderBy('created_at', 'DESC')->get();
        return view('admin.modules.register.show', [
            'user' => $user,
            'cities' => $cities,
            'states' => $states,
            'user_history' => $user_history,
            'user_role' => $this->user_role,
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
            'brands' => $brands,
            'models' => $models,
            'fuelType' => $fuelType,
            'last_updated_user_details' => $last_updated_user_details
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
        $user = $this->registerRepository->getByParams($params);

        return view('admin.modules.user.otp', [
            'mobile_number' => $user->mobile_number,
            'id' => $id
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

        curl_setopt_array(
            $curl,
            array(
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
        $user = $this->registerRepository->getByParams($params);

        /* return view('admin.modules.user.otp', [
        'mobile_number' => $user->mobile_number,'id'=>$id
        ]); */

        $message = 'OTP sent successfully.';
        return redirect(route('admin.users.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }
    /**
     * Summary of sendotp
     */
    public function sendotp(Request $request)
    {
        $user = Auth::user();

        $id = $request->get('id', null);
        $user = User::find($id);
        $sessionId = $user->otp;

        $otp = $request->get('otp');

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
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
            return redirect(route('admin.users.index', ['panel' => Session::get('panel')]))->withMessage($message);
        } else {
            $message = 'OTP does not match!';
            return redirect(route('admin.users.otp', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
        }
    }

    /**
     * Summary of downloadexport 
     */
    public function downloadexport(Request $request)
    {
        $start_date = $request->session()->get('start_date');
        $from_date = "";
        $end_date = "";
        if (!empty($start_date)) {
            $from_date = date("Y-m-d", strtotime($start_date[0]));
            $from_date = date('Y-m-d', strtotime($from_date . ' +1 day'));

            $end_date = date("Y-m-d", strtotime($start_date[1]));
            $end_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        // Excel file name for download
        $fileName = "user-data_" . time() . ".xls";
//        $fileName = "user-data_" . time() . ".csv";

        // Column names
        $fields = array(
            'First Name',
            'Last Name',
            'Email',
            'Mobile Number',
            'User Type',
            'APP Version',
            'State',
            'City',
            'Vehicle Number',
            'Vehicle Type',
            'Profile Id',
            'Agent Profile Approval',
            'Driver Profile Approva'
        );

        // Display column names as first row
        $excelData = implode("\t", array_values($fields)) . "\n";
        if (!empty($from_date) && !empty($end_date)) {
            $users = DB::select("SELECT u.first_name, u.last_name,u.emailid,u.mobile_number,
                    ut.name as UserType,u.app_version as app_version, s.name as State, c.name as City, v.vehicle_number as VehicleNumber,vt.name as VehicleType,
                    uwp.profile_id,au.all_document_verify as AgentProfileApproval,d.all_document_verify as DriverProfileApprova
                    FROM users as u LEFT JOIN
                    user_work_profile as uwp on uwp.user_id = u.id AND uwp.status = 1 LEFT JOIN
                    user_type as ut on u.user_type_id = ut.id LEFT JOIN
                    states as s on u.state = s.id LEFT JOIN
                    city as c on u.city_id = c.id LEFT JOIN
                    vehicles as v on v.user_id = u.id AND v.status = 1 LEFT JOIN
                    vehicle_types as vt on v.vehicle_type_id = vt.id LEFT JOIN
                    agent_users as au on au.id = uwp.profile_id AND au.status = 1 AND ut.id IN ('2','3') LEFT JOIN
                    vehicle_driving_mapping as vdm on v.id = vdm.vehicle_id AND ut.id = '3' LEFT JOIN
                    user_purchased_plans as upp on u.id = upp.user_id AND upp.status = 1 LEFT JOIN
                    subscription_plans as sp on upp.subscription_plan_id = sp.id LEFT JOIN
                    user_bank_mapping as ubm on u.id = ubm.user_id LEFT JOIN
                    bank_account as ba on ubm.bank_account_id = ba.id LEFT JOIN
                    drivers as d on d.id = uwp.profile_id AND d.status = 1 AND ut.id IN ('4','5') WHERE (ut.id != 4 OR ut.id IS NULL) AND
                    u.type = 'user' AND (uwp.status = 1 OR uwp.status IS NULL) AND u.status = 1 AND D
                    ATE_FORMAT(CONVERT_TZ(u.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $from_date . "' AND '" . $end_date . "' group by
                    u.id");
        } else {
            $users = DB::select("SELECT u.first_name, u.last_name,u.emailid,u.mobile_number,
                    ut.name as UserType,u.app_version as app_version, s.name as State, c.name as City, v.vehicle_number as VehicleNumber,vt.name as VehicleType,
                    uwp.profile_id,au.all_document_verify as AgentProfileApproval,d.all_document_verify as DriverProfileApprova
                    FROM users as u LEFT JOIN
                    user_work_profile as uwp on uwp.user_id = u.id AND uwp.status = 1 LEFT JOIN
                    user_type as ut on u.user_type_id = ut.id LEFT JOIN
                    states as s on u.state = s.id LEFT JOIN
                    city as c on u.city_id = c.id LEFT JOIN
                    vehicles as v on v.user_id = u.id AND v.status = 1 LEFT JOIN
                    vehicle_types as vt on v.vehicle_type_id = vt.id LEFT JOIN
                    agent_users as au on au.id = uwp.profile_id AND au.status = 1 AND ut.id IN ('2','3') LEFT JOIN
                    vehicle_driving_mapping as vdm on v.id = vdm.vehicle_id AND ut.id = '3' LEFT JOIN
                    user_purchased_plans as upp on u.id = upp.user_id AND upp.status = 1 LEFT JOIN  
                    subscription_plans as sp on upp.subscription_plan_id = sp.id LEFT JOIN
                    user_bank_mapping as ubm on u.id = ubm.user_id LEFT JOIN
                    bank_account as ba on ubm.bank_account_id = ba.id LEFT JOIN 
                    drivers as d on d.id = uwp.profile_id AND d.status = 1 AND ut.id IN ('4','5') 
                    WHERE (ut.id != 4 OR ut.id IS NULL) AND u.status = 1 AND u.type = 'user' AND (uwp.status = 1 OR uwp.status IS NULL) group by u.id");
        }
        if (isset($users)) {
            foreach ($users as $val) {
                $lineData = array($val->first_name, $val->last_name, $val->emailid, $val->mobile_number, $val->UserType, $val->app_version, $val->State, $val->City, $val->VehicleNumber, $val->VehicleType, $val->profile_id, $val->AgentProfileApproval, $val->DriverProfileApprova);
                $excelData .= implode("\t", array_values($lineData)) . "\n";
            }
        } else {
            $excelData .= 'No records found...' . "\n";
        }

        // Headers for download 
        header("Content-Type: application/vnd.ms-excel");
//        header("Content-Type: application/vnd.ms-csv");
        header("Content-Disposition: attachment; filename=\"$fileName\"");

        // Render excel data 
        echo $excelData;

        exit;
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
        return redirect(route('admin.users.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
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
        $result = $this->registerRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->registerRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        if (!Auth::user()->hasPermission('user-delete')) {
            abort(403);
        }
        $result = $this->registerRepository->delete($id);
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
        return view('admin.modules.user.change_profile');
    }
    public function changeUserProfile(ChangeProfileRequest $request)
    {
        $id = $request->get('id');

        $array['first_name'] = $request->get('first_name');
        $array['last_name'] = $request->get('last_name');
        $array['email'] = $request->get('email');
        $array['mobile_number'] = $request->get('mobile');

        $save = $this->registerRepository->changeProfile($array, $id);
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
}