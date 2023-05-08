<?php

namespace App\Http\Controllers\Trip;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\Cabs;
use App\Models\Earnings;
use App\Models\VehicleBrandModels;
use App\Models\VehicleBrands;
use Illuminate\Support\Facades\Response;
use App\Models\Trip;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Models\User;
use App\Models\Cities;
use App\Models\States;
use App\Models\TripPostOutstations;
use App\Models\TripPostLocal;
use App\Models\TripPostRental;
use App\Models\OfflineCustomer;
use App\Models\TripBookings;
use App\Models\TripFare;
use App\Repositories\TripRepository;
use App\Repositories\TripOfflineRepository;
use App\Repositories\TripOfflineVisitorRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use phpseclib\Crypt\RC2;
use Illuminate\Support\Facades\Http;


use function PHPUnit\Framework\isNull;

class TripController extends Controller
{
    protected $tripRepository;
    protected $tripOfflineRepository;
    protected $tripOfflineVisitorRepository;


    // trip status => pending: 0, accept: 1, reach_pickup_location: 2, start_trip: 3, end_trip: 4, complete: 5, cancel: 6

    public function __construct(TripRepository $tripRepository,TripOfflineRepository $tripOfflineRepository, TripOfflineVisitorRepository $tripOfflineVisitorRepository) {
        $this->tripRepository = $tripRepository;
        $this->tripOfflineRepository = $tripOfflineRepository;
        $this->tripOfflineVisitorRepository = $tripOfflineVisitorRepository;
 
    }
    public function Trip_data_delete(Request $request, $id)
    {
        dd($id);
    }
    public function trip_delete(Request $request, $manually_trip)
    {
        dd($manually_trip);
    }
    public function index(Request $request, $panel, $param = "")
    {


        $currentDateTime = Carbon::now();
        $newDateTime = Carbon::now()->addMinute(30);


        $manually_trip = Trip::whereNotNull('trip_post_master.trip_owner_name')->get();
        foreach ($manually_trip as $trip) {
            if ($trip->created_at >= $newDateTime) {
                if ($trip->trip_type == "Outstation Trip") {


                    foreach ((array)$trip['id'] as $trip) {
                        $bookings = TripBookings::where('id', $trip)->first();
                        if (!isset($bookings)) {
                            TripPostOutstations::where('trip_post_master_id', $trip)->delete();
                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Local") {

                    foreach ((array)$trip['id'] as $trip) {
                        $bookings = TripBookings::where('id', $trip)->first();
                        if (!isset($bookings)) {
                            TripPostLocal::where('trip_post_master_id', $trip)->delete();
                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Rental") {

                    foreach ((array)$trip['id'] as $trip) {


                        $bookings = TripBookings::where('id', $trip)->first();

                        if (!isset($bookings)) {
                            TripPostRental::where('trip_post_master_id', $trip)->delete();

                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Live") {

                    foreach ((array)$trip['id'] as $trip) {

                        DB::table('trip_post_live')->where([['trip_post_master_id', $trip], ['status', 0]])->delete();
                        $trip = Trip::where('id', $trip)->delete();
                    }
                }
            }
        }

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = Roles::find($role_id);
        $user_role = $role['slug'];
        $data_trip = [];
        $data_trip['today_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::today()], ['trip_bookings.trip_status', 2], ['trip_type', '!=', 'Live']])
            ->count();
        $data_trip['yesterday_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::yesterday()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
            ->count();
        $data_trip['last_week_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subDays(7)], ['trip_status', 2], ['trip_type', '!=', 'Live']])
            ->count();
        $data_trip['last_month_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subMonth()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
            ->count();
        $data_trip['current_month_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->month()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
            ->count();
        $data_trip['all_trip_count'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where('trip_status', 2)->where('trip_type', '!=', 'Live')
            ->count();
        $data_trip['not_trip'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where('trip_post_master.status', '=', '1')->where('trip_type', '!=', 'Live')
            ->count();



        //post maunally trip
        $data_trip['post_maunally_trip_today'] = Trip::whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();
        $data_trip['post_maunally_trip_yesterday'] = Trip::whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();
        $data_trip['post_maunally_trip_last_week'] = Trip::whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();
        $data_trip['post_maunally_trip_last_month'] = Trip::whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();
        $data_trip['post_maunally_trip_current_month'] = Trip::whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();
        $data_trip['post_maunally_trip_all'] = Trip::whereNotNull('trip_owner_name')->where('trip_type', '!=', 'Live')->count();

        //not allocated
        $data_trip['not_allocated_trip_today'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::today())->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
        $data_trip['not_allocated_trip_yesterday'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::yesterday())->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
        $data_trip['not_allocated_trip_last_week'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::now()->subDays(7))->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
        $data_trip['not_allocated_trip_last_month'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->subMonth(1))->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
        $data_trip['not_allocated_trip_current_month'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->month)->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
        $data_trip['not_allocated_trip_all'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();


        $data_trip['agent_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->where('trip_type', '!=', 'Live')->count();
        $data_trip['travel_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->where('trip_type', '!=', 'Live')->count();
        $data_trip['rider_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->where('trip_type', '!=', 'Live')->count();
        $data_trip['dco_trip']  = Trip::where('trip_type', 'Live')->count();

        $data_trip['trip_for'] = 0;
        // dd($data_trip);
        // dd($data_trip);
        if (in_array("6", $role_id_arr) || $user_role == 'administrator') {
            $users = User::where('type', '=', 'user')->get();

            return view('admin.modules.trip.index', compact('users', 'param', 'data_trip'));
        } else {
            abort(403);
        }
    }
    public function customer_trip(Request $request, $panel, $param = "")
    {


        $currentDateTime = Carbon::now();
        $newDateTime = Carbon::now()->addMinute(30);


        $manually_trip = Trip::whereNotNull('trip_post_master.trip_owner_name')->get();
        foreach ($manually_trip as $trip) {
            if ($trip->created_at >= $newDateTime) {
                if ($trip->trip_type == "Outstation Trip") {


                    foreach ((array)$trip['id'] as $trip) {
                        $bookings = TripBookings::where('id', $trip)->first();
                        if (!isset($bookings)) {
                            TripPostOutstations::where('trip_post_master_id', $trip)->delete();
                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Local") {

                    foreach ((array)$trip['id'] as $trip) {
                        $bookings = TripBookings::where('id', $trip)->first();
                        if (!isset($bookings)) {
                            TripPostLocal::where('trip_post_master_id', $trip)->delete();
                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Rental") {

                    foreach ((array)$trip['id'] as $trip) {


                        $bookings = TripBookings::where('id', $trip)->first();

                        if (!isset($bookings)) {
                            TripPostRental::where('trip_post_master_id', $trip)->delete();

                            $trip = Trip::where('id', $trip)->delete();
                        }
                    }
                } else if ($trip->trip_type == "Live") {

                    foreach ((array)$trip['id'] as $trip) {

                        DB::table('trip_post_live')->where([['trip_post_master_id', $trip], ['status', 0]])->delete();
                        $trip = Trip::where('id', $trip)->delete();
                    }
                }
            }
        }

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = Roles::find($role_id);
        $user_role = $role['slug'];
        $data_trip = [];
        $data_trip['today_trip'] = Trip::where([['created_at', Carbon::today()], ['status', 5]])
            ->where('trip_type', 'Live')->count();
        $data_trip['yesterday_trip'] = Trip::where([['created_at', Carbon::yesterday()], ['status', 5]])
            ->where('trip_type', 'Live')->count();
        $data_trip['last_week_trip'] = Trip::where([['created_at', Carbon::now()->subDays(7)], ['status', 5]])
            ->where('trip_type', 'Live')->count();
        $data_trip['last_month_trip'] = Trip::where([['created_at', Carbon::now()->subMonth()], ['status', 5]])
            ->where('trip_type', 'Live')->count();
        $data_trip['current_month_trip'] = Trip::where([['created_at', Carbon::now()->month()], ['status', 5]])
            ->where('trip_type', 'Live')->count();
        $data_trip['all_trip_count'] = Trip::where('status', 5)
            ->where('trip_type', 'Live')->count();
        $data_trip['not_trip'] = Trip::where('trip_post_master.status', '=', '1')
            ->where('trip_type', 'Live')->count();


        //post maunally trip
        $data_trip['post_maunally_trip_today'] = Trip::whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->count();
        $data_trip['post_maunally_trip_yesterday'] = Trip::whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->count();
        $data_trip['post_maunally_trip_last_week'] = Trip::whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->count();
        $data_trip['post_maunally_trip_last_month'] = Trip::whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->count();
        $data_trip['post_maunally_trip_current_month'] = Trip::whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->count();
        $data_trip['post_maunally_trip_all'] = Trip::whereNotNull('trip_owner_name')->count();

        //not allocated
        $data_trip['not_allocated_trip_today'] = Trip::whereDate('created_at', Carbon::today())->where([['trip_type', 'Live'], ['status', 0]])->count();
        $data_trip['not_allocated_trip_yesterday'] = Trip::whereDate('created_at', Carbon::yesterday())->where([['trip_type', 'Live'], ['status', 0]])->count();
        $data_trip['not_allocated_trip_last_week'] = Trip::whereDate('created_at', Carbon::now()->subDays(7))->where([['trip_type', 'Live'], ['status', 0]])->count();
        $data_trip['not_allocated_trip_last_month'] = Trip::whereMonth('created_at', Carbon::now()->subMonth(1))->where([['trip_type', 'Live'], ['status', 0]])->count();
        $data_trip['not_allocated_trip_current_month'] = Trip::whereMonth('created_at', Carbon::now()->month)->where([['trip_type', 'Live'], ['status', 0]])->count();
        $data_trip['not_allocated_trip_all'] = Trip::where([['trip_type', 'Live'], ['status', 0]])->count();

        // $abc = Trip::where('trip_post_master.trip_type','Live')->join('trip_bookings','trip_post_master.id','trip_bookings.trip_id')->count();
        // $abc = DB::table('trip_post_master')
        // ->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')
        // ->where('trip_post_master.trip_type','Live')
        // ->select('trip_post_master.id as id', 'trip_post_master.id as id', DB::raw("count(trip_bookings.trip_id) as count"))
        // ->groupBy('trip_post_master.id')
        // ->count();

        // dd($abc);
        $data_trip['agent_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->where('trip_type', 'Live')->count();
        $data_trip['travel_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->where('trip_type', 'Live')->count();
        $data_trip['rider_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->where('trip_type', 'Live')->count();
        $data_trip['dco_trip']  = Trip::where('trip_type', 'Live')->count();

        $data_trip['trip_for'] = 1;

        if (in_array("6", $role_id_arr) || $user_role == 'administrator') {
            $users = User::where('type', '=', 'user')->get();

            return view('admin.modules.trip.index', compact('users', 'param', 'data_trip'));
        } else {
            abort(403);
        }
    }

    public function tripCreate(Request $request, $param = "")
    {

        $trip_create = new Trip();
        $trip_create->trip_type = $request->trip_type;
        $trip_create->vehicle_type_id  = $request->vehicle_type;
        $trip_create->pickup_date  = $request->pickup_date;
        $trip_create->pickup_time  = $request->pickup_time;
        $trip_create->user_id  = $request->name;
        $trip_create->pickup_loc_lat  = $request->pickup_location;
        $trip_create->pickup_loc_lng  = $request->drop_location;
        $trip_create->save();

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("6", $role_id_arr) || $user_role == 'administrator') {
            $users = User::where('type', '=', 'user')->get();
            return view('admin.modules.trip.index', compact('users', 'param'));
        } else {
            abort(403);
        }
    }
    public function index_json($panel, Request $request, $fields = "")
    {

        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->tripRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['fields'] = $fields;
        $users = $this->tripRepository->getPanelUsers($request, $params);
        return $users;
    }
    public function index_json_customer($panel, Request $request, $fields = "")
    {

        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->tripRepository->getByParamsCustomers($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['fields'] = $fields;
        $users = $this->tripRepository->getPanelCustomers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("6", $role_id_arr) || $user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->tripRepository->getByParams($params);
            }

            $vehicleBrands = VehicleBrands::where('status', '=', '1')->get();

            return view('admin.modules.trip.store', [
                'data' => $data,
                'id' => $id,
                'user_role' => $user_role,
                'admin' => $admin,
                'vehicleBrands' => $vehicleBrands
            ]);
        } else {
            abort(403);
        }
    }
    public function km_location(Request $request)
    {
        $lat1 = $request->latitude;
        $long1 = $request->longitude;
        $lat2 = $request->latitude_drop;
        $long2 = $request->longitude_drop;

        $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=driving&language=pl-PL&key=" . env('GOOGLE_MAPS_API_KEY');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        $response_a = json_decode($response, true);
        if (!(empty($response_a['rows'][0]['elements'][0]['distance']['text']))) {
            $dist = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return Response::json(array('success' => true, 'dist' => $dist));
        } else {
            return Response::json(array('success' => false));
        }
    }
    public function trip_filter(Request $request, $filter, $trip_for = null)
    {
        $data_trip = [];
        $trip = [];
        $trip_search_from = 1;
        if ($trip_for == 0) {
            //partner
            //complete status (complated trips)
            $data_trip['today_trip'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::today()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();
            $data_trip['yesterday_trip'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::yesterday()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();
            $data_trip['last_week_trip'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subDays(7)], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();
            $data_trip['last_month_trip'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subMonth()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();
            $data_trip['current_month_trip'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->month()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();
            $data_trip['all_trip_count'] = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['trip_status', 2], ['trip_type', '!=', 'Live']])
                ->count();


            //post maunally trip
            $data_trip['post_maunally_trip_today'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_yesterday'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_last_week'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_last_month'] = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_current_month'] = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_all'] = Trip::whereRaw('1=1')->whereNotNull('trip_owner_name')->count();


            //not allocated
            $data_trip['not_allocated_trip_today'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::today())->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
            $data_trip['not_allocated_trip_yesterday'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::yesterday())->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
            $data_trip['not_allocated_trip_last_week'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::now()->subDays(7))->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
            $data_trip['not_allocated_trip_last_month'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->subMonth(1))->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
            $data_trip['not_allocated_trip_current_month'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->month)->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();
            $data_trip['not_allocated_trip_all'] = Trip::join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['trip_type', '!=', 'Live'], ['trip_status', 0]])->count();


            // dd($data_trip);
            //trip type
            $data_trip['agent_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->count();
            $data_trip['travel_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->count();
            $data_trip['rider_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->count();
            $data_trip['dco_trip']  = Trip::where('trip_type', 'Live')->count();

            if ($filter == "today-completed") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::today()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "yesterday-completed") {
                $trip_filter =  Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::yesterday()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "last-week-completed") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subDays(7)], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "current-month-completed") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->month()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "last-month-completed") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['created_at', Carbon::now()->subMonth()], ['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "all-completed") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['trip_status', 2], ['trip_type', '!=', 'Live']])
                    ->get();
            } elseif ($filter == "today-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "yesterday-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "last-week-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->get();
            } elseif ($filter === "current-month-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "last-month-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "all-maunally") {

                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "today-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::today())->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "yesterday-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::yesterday())->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "last-week-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereDate('created_at', Carbon::now()->subDays(7))->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "current-month-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->month)->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "last-month-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->whereMonth('created_at', Carbon::now()->subMonth(1))->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "all-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->join('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id')->where([['trip_status', 0], ['trip_type', '!=', 'Live']])->get();
            } elseif ($filter == "dco-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 5)->get();
            } elseif ($filter == "rider-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->get();
            } elseif ($filter == "travel-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->get();
            } elseif ($filter == "agent-trip") {

                $trip_filter =  User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->get();
            } elseif ($filter == "dropdown-filter") {
                // dd($request->all());
                $trip_select_dropdown = Trip::query();

                if ($request->select_city != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('pickup_location', 'like', "%$request->select_city%");
                } else {
                    if (($request->select_state != 0)) {
                        $trip_select_dropdown = $trip_select_dropdown->where('pickup_location', 'like', "%$request->select_state%");
                    }
                }

                if ($request->cab_type_wise != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('vehicle_type_id', $request->cab_type_wise);
                }
                if ($request->trip_type_wise != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('trip_type', 'like', "%$request->trip_type_wise%");
                }
                $trip_search_from = 0;
                $trip_filter = $trip_select_dropdown->orderBy('created_at', 'desc')->get();
                $trip['city'] = $request->select_city;
                $trip['state'] = $request->select_state;
                $trip['cab_type_wise'] = $request->cab_type_wise;
                $trip['trip_type_wise'] = $request->trip_type_wise;
                $trip['trip_from'] = $request->trip_from;
                $state = States::where('name', 'like', "%$request->select_state%")->first();
                $trip['cities'] = Cities::where('stateCode', $state->isoCode)->get();
            }
        } else {
            //complete status (complated trips)
            $data_trip['today_trip'] = Trip::whereRaw('1=1')->where([['created_at', Carbon::today()], ['status', 5], ['trip_type', '=', 'Live']])
                ->count();
            $data_trip['yesterday_trip'] = Trip::whereRaw('1=1')->where([['created_at', Carbon::yesterday()], ['status', 5], ['trip_type', '=', 'Live']])
                ->count();
            $data_trip['last_week_trip'] = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->subDays(7)], ['status', 5], ['trip_type', '=', 'Live']])
                ->count();
            $data_trip['last_month_trip'] = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->subMonth()], ['status', 5], ['trip_type', '=', 'Live']])
                ->count();
            $data_trip['current_month_trip'] = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->month()], ['status', 5], ['trip_type', '=', 'Live']])
                ->count();
            $data_trip['all_trip_count'] = Trip::whereRaw('1=1')->where([['status', 5], ['trip_type', '=', 'Live']])
                ->count();


            //post maunally trip
            $data_trip['post_maunally_trip_today'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_yesterday'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_last_week'] = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_last_month'] = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_current_month'] = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->count();
            $data_trip['post_maunally_trip_all'] = Trip::whereRaw('1=1')->whereNotNull('trip_owner_name')->count();


            //not allocated
            $data_trip['not_allocated_trip_today'] = Trip::whereDate('created_at', Carbon::today())->where([['trip_type', 'Live'], ['status', 0]])->count();
            $data_trip['not_allocated_trip_yesterday'] = Trip::whereDate('created_at', Carbon::yesterday())->where([['trip_type', 'Live'], ['status', 0]])->count();
            $data_trip['not_allocated_trip_last_week'] = Trip::whereDate('created_at', Carbon::now()->subDays(7))->where([['trip_type', 'Live'], ['status', 0]])->count();
            $data_trip['not_allocated_trip_last_month'] = Trip::whereMonth('created_at', Carbon::now()->subMonth(1))->where([['trip_type', 'Live'], ['status', 0]])->count();
            $data_trip['not_allocated_trip_current_month'] = Trip::whereMonth('created_at', Carbon::now()->month)->where([['trip_type', 'Live'], ['status', 0]])->count();
            $data_trip['not_allocated_trip_all'] = Trip::where([['trip_type',  'Live'], ['status', 0]])->count();


            // dd($data_trip);
            //trip type
            $data_trip['agent_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->count();
            $data_trip['travel_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->count();
            $data_trip['rider_trip']  = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->count();
            $data_trip['dco_trip']  = Trip::where('trip_type', 'Live')->count();

            if ($filter == "today-completed") {
                $trip_filter = Trip::whereRaw('1=1')->where([['created_at', Carbon::today()], ['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "yesterday-completed") {
                $trip_filter =  Trip::whereRaw('1=1')->where([['created_at', Carbon::yesterday()], ['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "last-week-completed") {
                $trip_filter = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->subDays(7)], ['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "current-month-completed") {
                $trip_filter = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->month()], ['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "last-month-completed") {
                $trip_filter = Trip::whereRaw('1=1')->where([['created_at', Carbon::now()->subMonth()], ['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "all-completed") {
                $trip_filter = Trip::whereRaw('1=1')->where([['status', 5], ['trip_type', '=', 'Live']])
                    ->get();
            } elseif ($filter == "today-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::today())->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "yesterday-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::yesterday())->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "last-week-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::now()->subDays(7))->whereNotNull('trip_owner_name')->get();
            } elseif ($filter === "current-month-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->month)->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "last-month-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->subMonth(1))->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "all-maunally") {
                $trip_filter = Trip::whereRaw('1=1')->whereNotNull('trip_owner_name')->get();
            } elseif ($filter == "today-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::today())->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "yesterday-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::yesterday())->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "last-week-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->whereDate('created_at', Carbon::now()->subDays(7))->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "current-month-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->month)->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "last-month-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->whereMonth('created_at', Carbon::now()->subMonth(1))->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "all-allocated") {
                $trip_filter = Trip::whereRaw('1=1')->where([['status', 0], ['trip_type', '=', 'Live']])->get();
            } elseif ($filter == "dco-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 5)->get();
            } elseif ($filter == "rider-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 4)->get();
            } elseif ($filter == "travel-trip") {

                $trip_filter = User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 3)->get();
            } elseif ($filter == "agent-trip") {

                $trip_filter =  User::join('trip_post_master', 'users.id', '=', 'trip_post_master.user_id')->where('users.user_type_id', 2)->get();
            } elseif ($filter == "dropdown-filter") {
                // dd($request->all());
                $trip_select_dropdown = Trip::query();

                if ($request->select_city != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('pickup_location', 'like', "%$request->select_city%");
                } else {
                    if (($request->select_state != 0)) {
                        $trip_select_dropdown = $trip_select_dropdown->where('pickup_location', 'like', "%$request->select_state%");
                    }
                }

                if ($request->cab_type_wise != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('vehicle_type_id', $request->cab_type_wise);
                }
                if ($request->trip_type_wise != 0) {
                    $trip_select_dropdown = $trip_select_dropdown->where('trip_type', 'like', "%$request->trip_type_wise%");
                }
                $trip_search_from = 0;
                $trip_filter = $trip_select_dropdown->orderBy('created_at', 'desc')->get();
                $trip['city'] = $request->select_city;
                $trip['state'] = $request->select_state;
                $trip['cab_type_wise'] = $request->cab_type_wise;
                $trip['trip_type_wise'] = $request->trip_type_wise;
                $trip['trip_from'] = $request->trip_from;
                $state = States::where('name', 'like', "%$request->select_state%")->first();
                $trip['cities'] = Cities::where('stateCode', $state->isoCode)->get();
            }
        }
        $data_trip['trip_for'] = $trip_for;
        return view('admin.modules.trip.index', ['trip_filter' => $trip_filter, 'data_trip' => $data_trip, 'trip_search_from' => $trip_search_from, 'trip' => $trip]);
    }
    public function stateCode(Request $request)
    {
        $state = States::where('name', 'like', "%$request->state%")->first();
        if (isset($state->isoCode)) {
            $city = Cities::where('stateCode', $state->isoCode)->get();
        } else {
            $city = '';
        }

        return Response::json(array('success' => true, 'city_name' => $city));
    }
    public function store(Request $request)
    {
        $user_id = auth()->user();
        $trip_type = $request->trip_type;
        if ($request->trip_type == "Local") {
            $validator = Validator::make($request->all(), [
                // 'ride_amount' => ['required'],
                // 'driver_earning' => ['required'],
                'autocomplete_drop' => ['required'],
                'pickup_date_time' => 'required|after_or_equal:today',
                'latitude' => ['required'],
                'latitude_drop' => ['required'],
            ], [
                'latitude.required' => 'Please Enter Valid Pickup Location.',
                'latitude_drop.required' => 'Please Enter Valid Drop Location.',
            ]);
        } elseif ($request->trip_type == "Rental") {
            $validator = Validator::make($request->all(), [
                // 'ride_amount' => ['required'],
                // 'driver_earning' => ['required'],
                'plan_hours' => ['required'],
                // 'amount' => ['required'],
                // 'extra_km_fare' => ['required'],
                // 'extra_time_fare' => ['required'],
                'pickup_date_time' => 'required|after_or_equal:today',
                'latitude' => ['required'],
            ], [
                'latitude.required' => 'Please Enter Valid Pickup Location.',
            ]);
        } elseif ($request->trip_type == "Outstation Trip") {
            if ($request->outstation_trip == "Round Trip") {
                if ($request->outstation_driver_allowance == 24 || $request->outstation_driver_allowance == 12) {
                    $validator = Validator::make($request->all(), [
                        // 'ride_amount' => ['required'],
                        // 'driver_earning' => ['required'],
                        'autocomplete_drop' => ['required'],
                        // 'outstation_extra_per_km' => ['required'],
                        // 'km_limit_outstation' => ['required'],
                        'return_date_time' => ['required'],
                        'pickup_date_time' => 'required|after_or_equal:today',
                        'return_date_time' => 'required|after_or_equal:today',
                        'latitude' => ['required'],
                        'latitude_drop' => ['required'],
                    ], [
                        'latitude.required' => 'Please Enter Valid Pickup Location.',
                        'latitude_drop.required' => 'Please Enter Valid Drop Location.',
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        // 'ride_amount' => ['required'],
                        // 'driver_earning' => ['required'],
                        'autocomplete_drop' => ['required'],
                        // 'outstation_extra_per_km' => ['required'],
                        // 'km_limit_outstation' => ['required'],
                        'pickup_date_time' => 'required|after_or_equal:today',
                        'return_date_time' => ['required'],
                        'latitude' => ['required'],
                        'latitude_drop' => ['required'],
                    ], [
                        'latitude.required' => 'Please Enter Valid Pickup Location.',
                        'latitude_drop.required' => 'Please Enter Valid Drop Location.',
                    ]);
                }
            } else {
                if ($request->outstation_driver_allowance == 24 || $request->outstation_driver_allowance == 12) {
                    $validator = Validator::make($request->all(), [
                        // 'ride_amount' => ['required'],
                        // 'driver_earning' => ['required'],
                        'autocomplete_drop' => ['required'],
                        // 'driver_allowance' => ['required'],
                        // 'outstation_extra_per_km' => ['required'],
                        'pickup_date_time' => 'required|after_or_equal:today',
                        // 'km_limit_outstation' => ['required'],
                        'latitude' => ['required'],
                        'latitude_drop' => ['required'],
                    ], [
                        'latitude.required' => 'Please Enter Valid Pickup Location.',
                        'latitude_drop.required' => 'Please Enter Valid Drop Location.',
                    ]);
                } else {
                    $validator = Validator::make($request->all(), [
                        // 'ride_amount' => ['required'],
                        // 'driver_earning' => ['required'],
                        'autocomplete_drop' => ['required'],
                        // 'driver_allowance' => ['required'],
                        // 'outstation_extra_per_km_amount' => ['required'],
                        'pickup_date_time' => 'required|after_or_equal:today',
                        // 'km_limit_outstation' => ['required'],
                        'latitude' => ['required'],
                        'latitude_drop' => ['required'],
                    ], [
                        'latitude.required' => 'Please Enter Valid Pickup Location.',
                        'latitude_drop.required' => 'Please Enter Valid Drop Location.',
                    ]);
                }
            }
        }

        if ($validator->fails()) {
            // For example:
            return redirect('/super-admin/trip/create')
                ->withErrors($validator)
                ->withInput();

            // Also handy: get the array with the errors
            $validator->errors();
        }
        $isoCode = States::where('name', $request->stateCodeAddress)->first();
        $drop_isoCode = States::where('name', $request->stateCodeDropAddress)->first();
        $pickupLocation = $request->autocomplete_pickup;
        $splitPickupLocation = explode(", ", $pickupLocation);
        $countPickupLocation = count($splitPickupLocation);
        foreach ($splitPickupLocation as $key => $value) {
            $key++;
            if ($key == $countPickupLocation - 1) {
                $stateKey = $key;
                $isoCode = States::where('name', $value)->first();
                if ($isoCode) {
                    $splitPickupLocation[$stateKey - 1] = $isoCode->isoCode;
                } else {
                    $splitPickupLocation[$stateKey - 1] = $value;
                }
            } elseif ($key == $countPickupLocation) {
                $stateKey = $key;
                // $splitPickupLocation[$stateKey-1] = "IND";
                unset($splitPickupLocation[$stateKey - 1]);
            }
        }
        $pickupLocation = implode(", ", $splitPickupLocation);
        $trip_create = new Trip();
        if ($trip_type == "Local") {
            $trip_create->trip_type = "InCity";
        } else if ($trip_type == "Rental") {
            $trip_create->trip_type = "Hourly";
        } else {
            $trip_create->trip_type = $trip_type;
        }

        if ($request->vehicle_type == 4) {
            $trip_create->vehicle_type_id = 3;
            $trip_create->vehical_manual_type  = $request->other;
        } else {
            $trip_create->vehicle_type_id  = $request->vehicle_type;
        }

        $trip_create->pickup_date  = Str::substr($request->pickup_date_time, 0, 10); //
        $trip_create->pickup_time  = Str::substr($request->pickup_date_time, 11); //
        $trip_create->pickup_location = $pickupLocation; //
        $trip_create->trip_owner_name = $request->trip_owner_name;
        $trip_create->trip_owner_mobile_no = $request->mobile_no;
        $trip_create->user_id = $user_id['id'];
        $trip_create->pickup_loc_lat  = $request->latitude;
        $trip_create->pickup_loc_lng  = $request->longitude;
        $trip_create->fare = $request->get('ride_amount', 0);
        $trip_create->commission_price = $request->get('driver_earning', 0);
        $trip_create->estimate_fare = $request->get('rental_amount', 0);
        $trip_create->pickup_state_code = $isoCode->isoCode;
        $trip_create->save();
       
        $trip_id = $trip_create->id;
        // $earnings = new Earnings();
        // $earnings->user_id = $user_id['id'];
        // $earnings->trip_id = $trip_id;
        // $earnings->fare = $request->driver_earning;
        // $earnings->datetime = Carbon::now();
        // // $earnings->comission  
        // // $earnings->booking_id  
        // $earnings->save();


        if ($trip_type == "Local") {
            $dropLocation = $request->autocomplete_drop;
            $splitDropLocation = explode(", ", $dropLocation);
            $countDropLocation = count($splitDropLocation);
            foreach ($splitDropLocation as $key => $value) {
                $key++;
                if ($key == $countDropLocation - 1) {
                    $stateKey = $key;
                    $isoCode = States::where('name', $value)->first();
                    if ($isoCode) {
                        $splitDropLocation[$stateKey - 1] = $isoCode->isoCode;
                    } else {
                        $splitDropLocation[$stateKey - 1] = $value;
                    }
                } elseif ($key == $countDropLocation) {
                    $stateKey = $key;
                    // $splitDropLocation[$stateKey-1] = "IND";
                    unset($splitDropLocation[$stateKey - 1]);
                }
            }
            $dropLocation = implode(", ", $splitDropLocation);
            $trip_post_local = new TripPostLocal();
            $trip_post_local->trip_post_master_id  = $trip_id;
            $trip_post_local->drop_location = $dropLocation;
            $trip_post_local->drop_loc_lat = $request->latitude_drop;
            $trip_post_local->drop_loc_lng = $request->longitude_drop;
            if ($drop_isoCode) {
                $trip_post_local->drop_state_code = $drop_isoCode->isoCode;
            }
            $trip_post_local->save();
        } elseif ($trip_type == "Rental") {
            $trip_post_rental = new TripPostRental();
            $trip_post_rental->trip_post_master_id   = $trip_id;
            if ($request->plan_hours == "other") {
                $trip_post_rental->time_duration = 'other';
                $trip_post_rental->km_limit = $request->get('select_km', 0);
            } else {
                $trip_post_rental->time_duration = $request->get('plan_hours', 0);
                $select_km = 0;
                if ($request->plan_hours == 4) {
                    $select_km = 40;
                } elseif ($request->plan_hours == 6) {
                    $select_km = 60;
                } elseif ($request->plan_hours == 8) {
                    $select_km = 80;
                } elseif ($request->plan_hours == 10) {
                    $select_km = 100;
                } elseif ($request->plan_hours == 12) {
                    $select_km = 120;
                }
                $trip_post_rental->km_limit = $request->get('select_km', 0);
            }

            if (isset($request->extra_km_fare)) {
                $trip_post_rental->has_addons = 1;
            } else {
                $trip_post_rental->has_addons = 0;
            }
            $trip_post_rental->extra_km_fare = $request->get('extra_km_fare', 0);
            $trip_post_rental->extra_time_fare = $request->get('extra_time_fare', 0);
            // $strip_post_rental->drop_state_code = $drop_isoCode->isoCode;

            $trip_post_rental->save();
        } elseif ($trip_type == "Outstation Trip") {
            $dropLocation = $request->autocomplete_drop;
            $splitDropLocation = explode(", ", $dropLocation);
            $countDropLocation = count($splitDropLocation);
            foreach ($splitDropLocation as $key => $value) {
                $key++;
                if ($key == $countDropLocation - 1) {
                    $stateKey = $key;
                    $isoCode = States::where('name', $value)->first();
                    if ($isoCode) {
                        $splitDropLocation[$stateKey - 1] = $isoCode->isoCode;
                    } else {
                        $splitDropLocation[$stateKey - 1] = $value;
                    }
                } elseif ($key == $countDropLocation) {
                    $stateKey = $key;
                    // $splitDropLocation[$stateKey-1] = "IND";
                    unset($splitDropLocation[$stateKey - 1]);
                }
            }
            $dropLocation = implode(", ", $splitDropLocation);
            $trip_outstation =  new TripPostOutstations();
            $trip_outstation->trip_post_master_id  = $trip_id;
            $trip_outstation->drop_location = $dropLocation;
            $trip_outstation->drop_loc_lat  = $request->latitude_drop;
            $trip_outstation->drop_loc_lng  = $request->longitude_drop;
            $trip_outstation->extra_km_limit = $request->get('min_km_limit', 0);
            $trip_outstation->commission_per_km = $request->get('outstation_extra_per_km', 0);
            if ($drop_isoCode) {
                $trip_outstation->drop_state_code = $drop_isoCode->isoCode;
            }
            if (isset($request->outstation_driver_allowance)) {
                $trip_outstation->driver_allowance_included = 1;
                if ($request->outstation_driver_allowance == 24) {
                    $trip_outstation->full_allowance = $request->get('outstation_extra_per_km_amount', 0);
                } elseif ($request->outstation_driver_allowance == 12) {
                    $trip_outstation->day_allowance = $request->get('outstation_extra_per_km_amount', 0);
                } else {
                    $trip_outstation->extra_km_limit = $request->get('extra_per_km', 0);
                }
            } else {
                $trip_outstation->driver_allowance_included = 0;
            }
            if ($request->trip_type == "Outstation Trip" && $request->outstation_trip == "Round Trip") {
                $trip_outstation->is_round_trip = 1;
                $trip_outstation->return_date = Str::substr($request->return_date_time, 0, 10);
                $trip_outstation->return_time = Str::substr($request->return_date_time, 11);
            } else {
                $trip_outstation->is_round_trip = 0;
                if (!isNull($request->toll_tax_yes)) {
                    $trip_outstation->is_tax_included = 1;
                } else {
                    $trip_outstation->is_tax_included = 0;
                }
            }

            $trip_outstation->save();
        }

        $fromCity = !empty($splitPickupLocation[0]) ? $splitPickupLocation[0]:'';
        $toCity = !empty($splitDropLocation[0]) ? $splitDropLocation[0]: '';
   

        $notification_location =  $fromCity;
        $limit = 36;
        if(!empty($toCity)){
             
            $notification_location .=  " to ".$toCity; 
        }
        $dropLocation=  !empty($dropLocation) ? $dropLocation :  '';
        if(!empty($dropLocation)){
            $dropLocation=  (strlen($dropLocation) > $limit) ? substr($dropLocation,0,$limit) : $dropLocation;
            $dropLocation=  "\nDrop Location: ". $dropLocation;
        }
    
        
        $pickupLocation=  !empty($pickupLocation) ? (strlen($pickupLocation) > $limit) ? substr($pickupLocation,0,$limit) : $pickupLocation : '';
        

        // $usersToken = User::where('role_id', '1')->whereNull('user_interest_status')->orWhere('user_interest_status', '0')->get();
        // $usersToken = User::where('id','11179')->get();
        $notificationText =  "Pickup Location: " . $pickupLocation . $dropLocation . ".\nPickup Date: " . date('d/m/Y',strtotime(Str::substr($request->pickup_date_time, 0, 10))).".";
        // $r = $this->notificationsRepository->sendPuchNotification($device_type = "Android", $tokens, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "Pulpit");

        $fields = "notificationId";
        $desc = $notificationText;
        $type = 'NEW_TRIP';
        if (!empty($pushMessageText)) {
            $type = $pushMessageText;
        }
        if ($trip_type == "Outstation Trip") {
            $trip_type = "Outstation";
        }
        $title = " 🚕 ".$trip_type." Trip - ".$notification_location." 🚕 ";
        $message = array("message" => $desc, 'title' => $title, 
        'click_action' => "FLUTTER_NOTIFICATION_CLICK", 'status' => 'done');
        // $tokens = [];
        // foreach ($usersToken as $key => $user) {
        //     if (!is_null($user->device_token)) {
        //         array_push($tokens, $user->device_token);
        //     }
        // }
        // $token = json_encode($tokens, true);
        $data = [
            "template" => [
                "title" => $title,
                "body" => $desc,
            ],
            "data" => [
                "body" => mb_convert_encoding($notificationText, 'HTML-ENTITIES', 'UTF-8'),
                "type" => $type,
                "sound" => true,
                "title" => $title,
                "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                "priority" => "high"
            ]
        ];
        
        /*
        ,
            "latitude" => floatval($request->latitude),
            "longitude" => floatval($request->longitude)
        */ 
        $response = Http::post('https://customer.api.pulpitmobility.com/customer/notification_php', $data);
        $jsonData = $response->json();
      /*   echo "<pre/>";
        print_r($data);
        exit; */
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("6", $role_id_arr) || $user_role == 'administrator') {
            $users = User::where('type', '=', 'user')->get();
            $param = "Trip Post Data Store Successfully";
            return redirect('super-admin/completetrip');
        } else {
            abort(403);
        }
    }
    public function show($panel, $id)
    {

        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $user = $this->tripRepository->getByParams($params);
        if ($user) {
            $customer_id = $user->customer_id;
        } else {
            $customer_id = 0;
        }

        $TripPostOutstations = TripPostOutstations::where('trip_post_master_id', $id)->first();
        $TripPostLocal = TripPostLocal::where('trip_post_master_id', $id)->first();
        $TripPostRental = TripPostRental::where('trip_post_master_id', $id)->first();
        $customers = OfflineCustomer::where('id', $customer_id)->first();

        $profile_path = config('custom.upload.user.profile');

 

        return view('admin.modules.trip.show', [
            'user' => $user, 'user_role' => $this->user_role, 'trip_post_outstation' => $TripPostOutstations, 'trip_post_local' => $TripPostLocal, 'trip_post_rental' => $TripPostRental, 'customers' => $customers
        ]);
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

    public function toggleStatus($panel, $id)
    {
        $result = $this->tripRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->tripRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->tripRepository->updateStatus($id);
        return redirect('/super-admin/completetrip');
        // return (int) $result;
    }

    public function customer_trip_offline(Request $request, $panel, $param = "") {

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.trip.trip_booking_offline');
        } else {
            abort(403);
        }
    }

    public function customer_trip_offline_json(Request $request) {
        $user = Auth::user();
        $trip_type = $request->trip_type;
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['trip_type'] = $trip_type;
            $total = $this->tripOfflineRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['trip_type'] = $trip_type;
        $users = $this->tripOfflineRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function customer_trip_offline_details(Request $request) {

        $id = $request->id;

        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $params['trip_type'] = 1;

        $results = DB::table('trip_bookings_offline')
                ->join('website_local_trip_fare', 'website_local_trip_fare.id', '=', 'trip_bookings_offline.vehicle_id')
                ->join('vehicle_types', 'vehicle_types.id', '=', 'website_local_trip_fare.vehicle_type_id')
                ->select('trip_bookings_offline.*', 'vehicle_types.name as vehicle_name')
                ->where('trip_bookings_offline.id', '=', $id)
                ->first();

        return view('admin.modules.trip.trip_booking_offline_show', ['user' => $user, 'trip_booking' => $results, 'user_role' => $this->user_role]);
    }

    public function customer_trip_offline_update_status(Request $request) {
        $id = $request->id;
        $status = $request->status;
        $reason = $request->reason;

        $tripBookingOffline = \App\Models\TripBookingsOffline::find($id);
        $tripBookingOffline->reason = $reason;
        $tripBookingOffline->status = $status;
        $tripBookingOffline->save();
        echo json_encode(array('status' => true, 'message' => 'Status Updated Successfully!'));
        die();
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // Website Offline Trip Visitor
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    public function customer_trip_offline_visitor(Request $request, $panel, $param = "") {

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.trip.trip_booking_offline_visitor');
        } else {
            abort(403);
        }
    }

    public function customer_trip_offline_visitor_json(Request $request) {
        $user = Auth::user();
        $trip_type = $request->trip_type;
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $countcompany['trip_type'] = $trip_type;
            $total = $this->tripOfflineVisitorRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $params['trip_type'] = $trip_type;
        $users = $this->tripOfflineVisitorRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function customer_trip_offline_visitor_details(Request $request) {

        $id = $request->id;

        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $params['trip_type'] = 1;

        $results = DB::table('trip_bookings_offline_visitor')
                ->join('website_local_trip_fare', 'website_local_trip_fare.id', '=', 'trip_bookings_offline_visitor.vehicle_id')
                ->join('vehicle_types', 'vehicle_types.id', '=', 'website_local_trip_fare.vehicle_type_id')
                ->select('trip_bookings_offline_visitor.*', 'vehicle_types.name as vehicle_name')
                ->where('trip_bookings_offline_visitor.id', '=', $id)
                ->first();

        return view('admin.modules.trip.trip_booking_offline_visitor_show', ['user' => $user, 'trip_booking' => $results, 'user_role' => $this->user_role]);
    }

    public function customer_trip_offline_visitor_update_status(Request $request) {
        $id = $request->id;
        $status = $request->status;
        $reason = $request->reason;

        $tripBookingOffline = \App\Models\TripBookingsOfflineVisitor::find($id);
        $tripBookingOffline->reason = $reason;
        $tripBookingOffline->status = $status;
        $tripBookingOffline->save();
        echo json_encode(array('status' => true, 'message' => 'Status Updated Successfully!'));
        die();
    }
    
    public function checkFraud(Request $request){
        $mobile  = $request->mobile;
        
        $checkExists = DB::table('fraud_lists')->where('phone_number',$mobile)->count();
        $status = false; 
        if(!empty($checkExists)){
            $status = true;
        }
        echo json_encode(array('status'=>$status));
        die();
    }
    public function fraud_store(Request $request){
        $mobile  = $request->fraud_mobile_number;
        
        $checkExists = DB::table('fraud_lists')->where('phone_number',$mobile)->count();
        if(empty($checkExists)){
            DB::table('fraud_lists')->insert(array('phone_number'=>$mobile));
        } 
        Session::flash('message', "Fraud number is listed successfully!");

        return redirect()->back();
    }
}