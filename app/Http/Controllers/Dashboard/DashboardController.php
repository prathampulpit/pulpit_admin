<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Roles;
use App\Models\UserPurchasedPlans;
use App\Models\SubscriptionPlans;
use App\Models\Earnings;
use App\Models\TripBookings;
use App\Models\Vehicles;
use App\Models\Drivers;
use App\Models\VehicleTypes;
use App\Repositories\UserRepository;
use App\Repositories\TransactionsRepository;
use App\Repositories\RegisterRepository;
use App\Repositories\DriverRepository;
use App\Repositories\TripRepository;
use Illuminate\Http\Request;
// use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use Carbon\Carbon;

class DashboardController extends Controller
{

    protected $userRepository;
    protected $transactionsRepository;
    protected $registerRepository;
    protected $driverRepository;
    protected $tripRepository;

    public function __construct(UserRepository $userRepository, TransactionsRepository $transactionsRepository, RegisterRepository $registerRepository, DriverRepository $driverRepository, TripRepository $tripRepository)
    {
        $this->userRepository = $userRepository;
        $this->transactionsRepository = $transactionsRepository;
        $this->registerRepository = $registerRepository;
        $this->driverRepository = $driverRepository;
        $this->tripRepository = $tripRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function index($panel,Request $request, $filter = "")
    {
        // if($request){
        //     dd($request->all()); 
        // }
        if ($filter == 'day') {
            $start_date = date("Y-m-d");
            $end_date = date("Y-m-d");
        } else if ($filter == 'week') {
            $start_date = date('Y-m-d', strtotime('-7 days'));
            $end_date = date("Y-m-d");
        } else if ($filter == 'month') {
            $start_date = date('Y-m-d', strtotime('-30 days'));
            $end_date = date("Y-m-d");
        } else {
            $start_date = "";
            $end_date = "";
        }
        /**
         * Total users
         */
        $params['type'] = "user";
        $users = $this->userRepository->getByParams($params);
        $total_users = count($users);

        /* $number_of_users_subscribed = UserPurchasedPlans::groupBy('user_id')->get();
        $number_of_users_subscribed = count($number_of_users_subscribed); */


        //CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30')
        if ($filter != '') {
            $total_revenue = UserPurchasedPlans::leftJoin('subscription_plans', function ($join) {
                $join->on('user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
            })
                ->whereRaw("DATE_FORMAT(CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND user_purchased_plans.status = 1")
                ->sum('subscription_plans.price');
        } else {
            $total_revenue = UserPurchasedPlans::leftJoin('subscription_plans', function ($join) {
                $join->on('user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
            })->where('user_purchased_plans.status', 1)->sum('subscription_plans.price');
        }

        if ($filter != '') {
            $agents_total_earning = Earnings::whereRaw("DATE_FORMAT(CONVERT_TZ(datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' ")->sum('comission');
            $driver_total_earning = Earnings::whereRaw("DATE_FORMAT(CONVERT_TZ(datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' ")->sum('fare');
            //$total_trips_completed = TripBookings::whereRaw("DATE_FORMAT(booking_datetime, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' ")->count();
            $Customers_total_spent = Earnings::whereRaw("DATE_FORMAT(CONVERT_TZ(datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' ")->sum('fare');
            //$pending_approvals_user = User::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' ")->where('user_status', '!=', '1')->count();
            $pending_approvals_vehicle = Vehicles::whereRaw("DATE_FORMAT(CONVERT_TZ(created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' ")->where('all_document_verify', '=', '0')->count();
            //$pending_approvals_driver = Drivers::whereRaw("DATE_FORMAT(created_at, '%Y-%m-%d') BETWEEN '".$start_date."' AND '".$end_date."' ")->where('all_document_verify', '=', '0')->count();
            $pending_approvals_payment = TripBookings::whereRaw("DATE_FORMAT(CONVERT_TZ(booking_datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_date . "' AND '" . $end_date . "' ")->sum('fare');

            $params = array();
            $params['all_document_verify'] = '0';
            $params['count'] = '1';
            $params['start_date'] = $start_date;
            $params['end_date'] = $end_date;
            $pending_approvals_driver = $this->driverRepository->getByParams($params);

            $params = array();
            $params['all_document_verify'] = 'pending';
            $params['count'] = '1';
            $params['start_date'] = $start_date;
            $params['end_date'] = $end_date;
            $pending_approvals_user = $this->registerRepository->getByParams($params);

            $params = array();
            $params['fields'] = 'complete';
            $params['count'] = '1';
            $params['start_date'] = $start_date;
            $params['end_date'] = $end_date;
            $total_trips_completed = $this->tripRepository->getByParams($params);

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['all_document_verify'] = 'complete';
            $countcompany['start_date'] = $start_date;
            $countcompany['end_date'] = $end_date;
            $approved_users = $this->registerRepository->getByParams($countcompany);

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['all_document_verify'] = 'pending';
            $countcompany['start_date'] = $start_date;
            $countcompany['end_date'] = $end_date;
            $pending_approved_users = $this->registerRepository->getByParams($countcompany);

            $countcompany = array();
            //$countcompany['count'] = true;
            $countcompany['redirect_from'] = 'subscribe';
            $countcompany['start_date'] = $start_date;
            $countcompany['end_date'] = $end_date;
            $subscribed = $this->registerRepository->getCount($countcompany);
            $number_of_users_subscribed = count($subscribed);
        } else {
            $agents_total_earning = Earnings::sum('comission');
            $driver_total_earning = Earnings::sum('fare');
            //$total_trips_completed = TripBookings::count();
            $Customers_total_spent = Earnings::sum('fare');
            //$pending_approvals_user = User::where('user_status', '!=', '1')->count();
            $pending_approvals_vehicle = Vehicles::where('all_document_verify', '=', '0')->count();
            //$pending_approvals_driver = Drivers::where('all_document_verify', '=', '0')->count();
            $pending_approvals_payment = TripBookings::sum('fare');

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['all_document_verify'] = 'pending';
            $pending_approved_users = $this->registerRepository->getCount($countcompany);

            $countcompany = array();
            $countcompany['count'] = true;
            $countcompany['all_document_verify'] = 'complete';
            $approved_users = $this->registerRepository->getCount($countcompany);

            $params = array();
            $params['all_document_verify'] = '0';
            $params['count'] = '1';
            $pending_approvals_driver = $this->driverRepository->getByParams($params);

            $params = array();
            $params['all_document_verify'] = 'pending';
            $params['count'] = '1';
            $pending_approvals_user = $this->registerRepository->getCount($params);

            $params = array();
            $params['fields'] = 'complete';
            $params['count'] = '1';
            //$params['start_date'] = $start_date;
            //$params['end_date'] = $end_date;
            $total_trips_completed = $this->tripRepository->getByParams($params);

            $countcompany = array();
            //$countcompany['count'] = true;
            $countcompany['redirect_from'] = 'subscribe';
            $subscribed = $this->registerRepository->getCount($countcompany);
            $number_of_users_subscribed = count($subscribed);
        }



        $params = array();
        $params['limit'] = "5";
        $params['status'] = "1";
        $params['order_by'] = 'id';
        $params['order'] = 'DESC';
        $trans = $this->transactionsRepository->getByParams($params);
        /* echo "<pre>";
        print_r($trans);
        exit; */

        //$day_name_arr = array();
        //$graph_trans_data_arr = array();

        $day_name_arr = '';
        $graph_trans_data_arr = '';
        for ($i = 0; $i <= 12; $i++) {
            $day_name_arr .= "'" . date('M', strtotime("-$i month")) . "',";
            $date = date('Y-m', strtotime("-$i month"));

            /* $p['date'] = $date;
            $p['count'] = '';
            $trans_data = $this->transactionsRepository->transactionData($p); */

            $countcompany = array();
            $countcompany['this_month_joined'] = $date;
            $countcompany['count'] = true;
            $countcompany['paid_users'] = '1';
            $trans_data = $this->registerRepository->getByParams($countcompany);

            $graph_trans_data_arr .= $trans_data . ",";
        }
        $day_name_arr .= '';
        $day_name_arr1 = rtrim($day_name_arr, ",");
        $day_name = $day_name_arr1 . "";

        $graph_trans_data_arr .= '';
        $graph_trans_data_arr1 = rtrim($graph_trans_data_arr, ",");
        $graph_trans_data = $graph_trans_data_arr1;

        $prm['count'] = '';
        $total_subscription_user = $this->transactionsRepository->transactionData($prm);

        $profile_path = config('custom.upload.user.profile');
        $vehicleType = $request->vehicle_type_id;
        $driverType = $request->driver_type_id;
        $vehicle_types = VehicleTypes::all();
        if(isset($driverType)){
            if($driverType == 1){
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['online'] = 1;
                $drivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['online'] = 1;
                $params['onlineDrivers'] = 1;
                $onlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['offline'] = 0;
                $params['offlineDrivers'] = 0;
                $offlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['onGoing'] = 1;
                $params['onGoingDrivers'] = 1;
                $onGoingDrivers = $this->driverRepository->getDriverDetails($params);
            }elseif($driverType == 2){
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['offline'] = 0;
                $drivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['online'] = 1;
                $params['onlineDrivers'] = 1;
                $onlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['offline'] = 0;
                $params['offlineDrivers'] = 0;
                $offlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['onGoing'] = 1;
                $params['onGoingDrivers'] = 1;
                $onGoingDrivers = $this->driverRepository->getDriverDetails($params);
            }elseif($driverType == 3){
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['online'] = 1;
                $params['onGoing'] = 1;
                $drivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['online'] = 1;
                $params['onlineDrivers'] = 1;
                $onlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['offline'] = 0;
                $params['offlineDrivers'] = 0;
                $offlineDrivers = $this->driverRepository->getDriverDetails($params);
                $params = [];
                $params['vehicleType'] = $vehicleType;
                $params['onGoing'] = 1;
                $params['onGoingDrivers'] = 1;
                $onGoingDrivers = $this->driverRepository->getDriverDetails($params);
            }
        }else{
            $params = [];
            $params['vehicleType'] = $vehicleType;
            $drivers = $this->driverRepository->getDriverDetails($params);
            $params = [];
            $params['vehicleType'] = $vehicleType;
            $params['online'] = 1;
            $params['onlineDrivers'] = 1;
            $onlineDrivers = $this->driverRepository->getDriverDetails($params);
            $params = [];
            $params['vehicleType'] = $vehicleType;
            $params['offline'] = 0;
            $params['offlineDrivers'] = 0;
            $offlineDrivers = $this->driverRepository->getDriverDetails($params);
            $params = [];
            $params['vehicleType'] = $vehicleType;
            $params['onGoing'] = 1;
            $params['onGoingDrivers'] = 1;
            $onGoingDrivers = $this->driverRepository->getDriverDetails($params);
        }
        
        foreach($drivers as $driver){
            if($driver->vehicle_type_id  == 1){
                $driver['icon'] = "https://pulpit-media.s3.ap-south-1.amazonaws.com/2961669694574HatchBack_new.png";
            }elseif($driver->vehicle_type_id == 3){
                $driver['icon'] = "https://pulpit-media.s3.ap-south-1.amazonaws.com/8761669694554SUV_new.png";
            }else{
                $driver['icon'] = "https://pulpit-media.s3.ap-south-1.amazonaws.com/3681669694523sedan_new.png";
            }

        // Danger Code Every Time Server Crash To This Code Uncomment Soo PLease Tack Care Take Risk This Code Uncomment Time
            // if(isset($driver->last_online_at) && isset($driver->last_offline_at)){
            //     $today = Carbon::now()->setTimezone('Asia/Kolkata');
            //     $todayTime = $today;
            //     if($driver->is_online == 1){
            //         $loginTime = Carbon::parse($driver->last_online_at)->setTimezone('Asia/Kolkata');
            //         $online = "Online";
            //     }else{
            //         $loginTime = Carbon::parse($driver->last_offline_at)->setTimezone('Asia/Kolkata');
            //         $online = "Offline";
            //     }
            //     $diff_in_days = $loginTime->diffInDays($todayTime);
            //     $loginTime = $loginTime->addDays($diff_in_days)->setTimezone('Asia/Kolkata');
            //     $diff_in_hours = $loginTime->diffInHours($todayTime);
            //     $loginTime = $loginTime->addHour($diff_in_hours)->setTimezone('Asia/Kolkata');
                
            //     $diff_in_minutes = $loginTime->diffInMinutes($todayTime);
                
            //     $driver['times'] = $diff_in_days .'D ' . $diff_in_hours . 'h ' . $diff_in_minutes . 'm ' . ' ' . $online ;
            // }else{
            //     $driver['times'] = 'New User';
            // } 
        }
        /* return view('admin.modules.dashboard.index', compact('total_users', 'total_revenue', 'profile_path','number_of_users_subscribed','total_trips_completed','agents_total_earning','Customers_total_spent','driver_total_earning','pending_approvals_user','pending_approvals_driver','pending_approvals_vehicle','pending_approvals_payment','trans','filter','day_name','graph_trans_data')); */

        return view('admin.modules.dashboard.index', [
            'total_users' => $total_users,
            'vehicleType'=> $vehicleType, 
            'vehicle_types' => $vehicle_types,
            'allDrivers' => $drivers, 
            'driverType' => $driverType, 
            'onlineDrivers' => $onlineDrivers, 
            'offlineDrivers' => $offlineDrivers, 
            'onGoingDrivers' => $onGoingDrivers, 
            'total_revenue' => $total_revenue, 
            'profile_path' => $profile_path, 
            'approved_users' => $approved_users, 
            'number_of_users_subscribed' => $number_of_users_subscribed, 
            'total_trips_completed' => $total_trips_completed, 
            'agents_total_earning' => $agents_total_earning, 
            'Customers_total_spent' => $Customers_total_spent, 
            'driver_total_earning' => $driver_total_earning, 
            'pending_approvals_user' => $pending_approvals_user, 
            'pending_approvals_driver' => $pending_approvals_driver, 
            'pending_approvals_vehicle' => $pending_approvals_vehicle, 
            'pending_approvals_payment' => $pending_approvals_payment, 
            'trans' => $trans, 
            'filter' => $filter, 
            'day_name' => $day_name, 
            'graph_trans_data' => $graph_trans_data, 
            'total_subscription_user' => $total_subscription_user, 
            'pending_approved_users' => $pending_approved_users
        ]);
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->userRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params = [];
        $params['per_page'] = $total;
        $params['user_status'] = "3";
        $params['limit'] = '20';
        $users = $this->userRepository->getPanelUsers($request, $params);
        return $users;
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
        $user = $this->userRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.dashboard.show', [
            'user' => $user, 'file_path' => $file_path, 'user_role' => $this->user_role
        ]);
    }
}