<?php

namespace App\Repositories;

use App\Models\Trip;
use Illuminate\Support\Facades\DB;

class TripRepository extends BaseRepository
{
    protected $trip;

    public function __construct(Trip $trip)
    {
        parent::__construct($trip);
        $this->trip = $trip;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    //DB::raw("CONVERT_TZ(trip_bookings.trip_end_datetime,'+00:00','+05:30') as trip_end_datetime")
    //DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime")
    //DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime")
    public function getByParams($params)
    {
        $query = $this->trip::whereRaw('1=1');
        $query->select('trip_post_master.*', 'trip_post_outstation.*', 'trip_post_outstation.extra_time_fare', 'trip_post_rental.time_duration', 
        'trip_post_rental.km_limit', 'trip_post_rental.has_addons', 'trip_post_rental.extra_km_fare', 'trip_post_rental.extra_time_fare', 
        'trip_post_local.drop_location', 'vehicle_types.name as vehicle_type', 'users.first_name','users.user_interest_status', 'users.last_name', 'users.mobile_number', 
        'trip_bookings.booking_datetime', DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime"), 
        DB::raw("CONVERT_TZ(trip_bookings.trip_end_datetime,'+00:00','+05:30') as trip_end_datetime"), 'vehicles.vehicle_number', 
        'trip_bookings.trip_status', 'trip_bookings.trip_id as tb_trip_id', 'bidding.trip_id as b_trip_id', 
        DB::raw("CONCAT(trip_post_master.pickup_date,'', trip_post_master.pickup_time) as pickup_date_time"), 'trip_bookings.strat_trip_kms_reading', 
        'trip_bookings.end_trip_kms_reading', 'trip_bookings.fare as booking_fare', 'trip_bookings.commission_price as booking_commission_price', 
        'trip_bookings.fare_per_km as booking_fare_per_km', 'trip_bookings.commission_per_km as booking_commission_per_km', 'trip_bookings.payment_type',
        'trip_bookings.advance_paid_amount', 'trip_bookings.payment_status', 'trip_bookings.start_trip_odometer_image', 
        'trip_bookings.end_trip_odometer_image', 'trip_bookings.driver_allowance', 'trip_bookings.note', 'cab_post.cab_post_type', 
        'cab_post.start_location', 'cab_post.end_location', 'cab_post.start_date', 'cab_post.start_time', 'cab_post.available_for', 
        'users.emailid', 'users.gender','trip_bookings.customer_id', 'vehicles.city', 'vehicles.state', 'vehicles.owner_name', 
        'trip_post_outstation.drop_location as drop_location_for_outstation');
        $query->leftJoin('users', 'trip_post_master.user_id', '=', 'users.id');
        $query->leftJoin('trip_post_rental', 'trip_post_master.id', '=', 'trip_post_rental.trip_post_master_id');
        $query->leftJoin('trip_post_outstation', 'trip_post_master.id', '=', 'trip_post_outstation.trip_post_master_id');
        $query->leftJoin('trip_post_local', 'trip_post_master.id', '=', 'trip_post_local.trip_post_master_id');
        $query->leftJoin('vehicle_types', 'trip_post_master.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id');
        $query->leftJoin('bidding', 'trip_post_master.id', '=', 'bidding.trip_id');
        $query->leftJoin('cab_post', 'trip_bookings.cab_id', '=', 'cab_post.id');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

        // conditions
//        $query->where('trip_post_master.is_deleted', '!=', '1');
//        $query->where('trip_post_master.trip_type', '!=', 'Live');
        if (isset($params['id'])) {
            $query->where('trip_post_master.id', $params['id']);
        }

        if (isset($params['user_id'])) {
            $query->where('trip_post_master.user_id', $params['user_id']);
        }

        if (isset($params['trip_type'])) {
            $query->where('trip_post_master.trip_type', $params['trip_type']);
        }

        if (isset($params['not_status'])) {
            $query->where('trip_post_master.status', '!=', $params['not_status']);
        }

        if (isset($params['status'])) {
            $query->where('trip_post_master.status', '=', $params['status']);
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $today = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(trip_bookings.booking_datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $params['start_date'] . "' AND '" . $params['end_date'] . "' ");
        }

        if (isset($params['fields']) && $params['fields'] == 'complete') {
            $query->where('trip_bookings.trip_status', '2');
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $query->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $query->orderBy($params['order_by'], $params['order']);
        }

        if (isset($params['count'])) {
            $records = $query->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->get();
        }
        
        return $records;
    }
    public function getByParamsCustomers($params)
    {
        $query = $this->trip::whereRaw('1=1');
        $query->select('trip_post_master.*', 'trip_post_outstation.*', 'trip_post_outstation.extra_time_fare', 'trip_post_rental.time_duration', 
        'trip_post_rental.km_limit', 'trip_post_rental.has_addons', 'trip_post_rental.extra_km_fare', 'trip_post_rental.extra_time_fare', 
        'trip_post_local.drop_location', 'vehicle_types.name as vehicle_type', 'users.first_name','users.user_interest_status', 'users.last_name', 'users.mobile_number', 
        'trip_bookings.booking_datetime', DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime"), 
        DB::raw("CONVERT_TZ(trip_bookings.trip_end_datetime,'+00:00','+05:30') as trip_end_datetime"), 'vehicles.vehicle_number', 
        'trip_bookings.trip_status', 'trip_bookings.trip_id as tb_trip_id', 'bidding.trip_id as b_trip_id', 
        DB::raw("CONCAT(trip_post_master.pickup_date,'', trip_post_master.pickup_time) as pickup_date_time"), 'trip_bookings.strat_trip_kms_reading', 
        'trip_bookings.end_trip_kms_reading', 'trip_bookings.fare as booking_fare', 'trip_bookings.commission_price as booking_commission_price', 
        'trip_bookings.fare_per_km as booking_fare_per_km', 'trip_bookings.commission_per_km as booking_commission_per_km', 'trip_bookings.payment_type',
        'trip_bookings.advance_paid_amount', 'trip_bookings.payment_status', 'trip_bookings.start_trip_odometer_image', 
        'trip_bookings.end_trip_odometer_image', 'trip_bookings.driver_allowance', 'trip_bookings.note', 'cab_post.cab_post_type', 
        'cab_post.start_location', 'cab_post.end_location', 'cab_post.start_date', 'cab_post.start_time', 'cab_post.available_for', 
        'users.emailid', 'users.gender','trip_bookings.customer_id', 'vehicles.city', 'vehicles.state', 'vehicles.owner_name', 
        'trip_post_outstation.drop_location as drop_location_for_outstation');
        $query->leftJoin('users', 'trip_post_master.user_id', '=', 'users.id');
        $query->leftJoin('trip_post_rental', 'trip_post_master.id', '=', 'trip_post_rental.trip_post_master_id');
        $query->leftJoin('trip_post_outstation', 'trip_post_master.id', '=', 'trip_post_outstation.trip_post_master_id');
        $query->leftJoin('trip_post_local', 'trip_post_master.id', '=', 'trip_post_local.trip_post_master_id');
        $query->leftJoin('vehicle_types', 'trip_post_master.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id');
        $query->leftJoin('bidding', 'trip_post_master.id', '=', 'bidding.trip_id');
        $query->leftJoin('cab_post', 'trip_bookings.cab_id', '=', 'cab_post.id');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

        // conditions
        $query->where('trip_post_master.is_deleted', '!=', '1');
        $query->where('trip_post_master.trip_type', '=', 'Live');
        if (isset($params['id'])) {
            $query->where('trip_post_master.id', $params['id']);
        }

        if (isset($params['user_id'])) {
            $query->where('trip_post_master.user_id', $params['user_id']);
        }

        if (isset($params['trip_type'])) {
            $query->where('trip_post_master.trip_type', $params['trip_type']);
        }

        if (isset($params['not_status'])) {
            $query->where('trip_post_master.status', '!=', $params['not_status']);
        }

        if (isset($params['status'])) {
            $query->where('trip_post_master.status', '=', $params['status']);
        }

        if (isset($params['start_date']) && isset($params['end_date'])) {
            $today = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(trip_bookings.booking_datetime,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $params['start_date'] . "' AND '" . $params['end_date'] . "' ");
        }

        if (isset($params['fields']) && $params['fields'] == 'complete') {
            $query->where('trip_bookings.trip_status', '2');
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $query->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $query->orderBy($params['order_by'], $params['order']);
        }

        if (isset($params['count'])) {
            $records = $query->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->get();
        }

        return $records;
    }

    //DB::raw("CONVERT_TZ(trip_bookings.trip_end_datetime,'+00:00','+05:30') as trip_end_datetime")
    //DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime")
    //DB::raw("CONVERT_TZ(trip_bookings.trip_start_datetime,'+00:00','+05:30') as trip_start_datetime")
    //DB::raw("CONVERT_TZ(CONCAT(trip_post_master.pickup_date,'', trip_post_master.pickup_time),'+00:00','+05:30') as pickup_date_time")
    public function getPanelUsers($request, $params)
    {
        if (request('per_page') == 'all') {
            $usersCount = [];
            $usersCount['count'] = true;
            $perPage = $this->getByParams($usersCount);
        } else {
            $perPage = request('per_page', config('custom.db.per_page'));
        }
        $orderBy = request('order_by', 'id');
        $order = request('order', 'desc');

        $query = $this->trip::whereRaw('1=1');
        $query->select(DB::raw("LEFT(trip_post_master.pickup_location , 30) as mini_pickup_location"), 'trip_post_master.*', 'trip_post_rental.time_duration', 'trip_post_rental.km_limit', 'trip_post_rental.has_addons', 'trip_post_rental.extra_km_fare', 'trip_post_rental.extra_time_fare', 'trip_post_local.drop_location', 'vehicles.vehicle_number', 'vehicle_types.name as vehicle_type', 'users.first_name','users.user_interest_status', 'users.last_name', DB::raw("CONCAT(trip_post_master.pickup_date,'', trip_post_master.pickup_time) as pickup_date_time"), 'trip_bookings.trip_status', 'trip_bookings.trip_id as tb_trip_id', 'bidding.trip_id as b_trip_id', 'trip_post_outstation.drop_location as drop_location_for_outstation', DB::raw("LEFT(trip_post_outstation.drop_location , 30) as mini_drop_location_for_outstation"), DB::raw("LEFT(trip_post_local.drop_location, 30) as mini_drop_location"), DB::raw("MAX(bidding.is_accepted) as max_is_accepted"));
        $query->leftJoin('users', 'trip_post_master.user_id', '=', 'users.id');
        $query->leftJoin('trip_post_rental', 'trip_post_master.id', '=', 'trip_post_rental.trip_post_master_id');
        $query->leftJoin('trip_post_outstation', 'trip_post_master.id', '=', 'trip_post_outstation.trip_post_master_id');
        $query->leftJoin('trip_post_local', 'trip_post_master.id', '=', 'trip_post_local.trip_post_master_id');
        $query->leftJoin('vehicle_types', 'trip_post_master.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id');
        $query->leftJoin('bidding', 'trip_post_master.id', '=', 'bidding.trip_id');
        /* $query->leftJoin("bidding",function($join){
            $join->on("trip_post_master.id","=","bidding.trip_id")->orderBy("bidding.is_accepted","DESC");
        }); */

        $query->leftJoin('cab_post', 'trip_bookings.cab_id', '=', 'cab_post.id');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

        $query->where('trip_post_master.is_deleted', '!=', '1');
        $query->where('trip_post_master.trip_type', '!=', 'Live');

        if ($request->get('trip_type') != 'all') {
            $query->where('trip_post_master.trip_type', $request->get('trip_type'));
        }

        if ($params['fields'] == 'complete') {
            $query->where('trip_bookings.trip_status', '2');
        }
        /* if ($request->get('user_id') != 'all') {
            $query->where('trip_post_master.user_id', $request->get('user_id'));
        } */

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("trip_post_master.trip_type like " . "'" . $search . "' OR trip_post_master.pickup_location like " . "'" . $search . "' OR trip_post_master.pickup_date like " . "'" . $search . "' OR users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "'  OR vehicle_types.name like " . "'" . $search . "'");
            });
        }

        $query->orderBy($orderBy, $order);
        $query->groupBy('trip_post_master.id');

        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            //echo $query->toSql(); exit;
            $records = $query->paginate($perPage);
        }

        /* echo "<pre>";
        print_r($records);
        exit; */

        return $records;
    }
    public function getPanelCustomers($request, $params)
    {
         
        if (request('per_page') == 'all') {
            $usersCount = [];
            $usersCount['count'] = true;
            $perPage = $this->getByParams($usersCount);
        } else {
            $perPage = request('per_page', config('custom.db.per_page'));
        }
        $orderBy = request('order_by', 'id');
        $order = request('order', 'desc');

        $query = $this->trip::whereRaw('1=1');
        $query->select(DB::raw("LEFT(trip_post_master.pickup_location , 30) as mini_pickup_location"), 'trip_post_master.*', 'trip_post_rental.time_duration', 'trip_post_rental.km_limit', 'trip_post_rental.has_addons', 'trip_post_rental.extra_km_fare', 'trip_post_rental.extra_time_fare', 'trip_post_local.drop_location', 'vehicles.vehicle_number', 'vehicle_types.name as vehicle_type', 'users.first_name','users.user_interest_status', 'users.last_name', DB::raw("CONCAT(trip_post_master.pickup_date,'', trip_post_master.pickup_time) as pickup_date_time"), 'trip_bookings.trip_status', 'trip_bookings.trip_id as tb_trip_id', 'bidding.trip_id as b_trip_id', 'trip_post_outstation.drop_location as drop_location_for_outstation', DB::raw("LEFT(trip_post_outstation.drop_location , 30) as mini_drop_location_for_outstation"), DB::raw("LEFT(trip_post_local.drop_location, 30) as mini_drop_location"), DB::raw("MAX(bidding.is_accepted) as max_is_accepted"));
        $query->leftJoin('users', 'trip_post_master.user_id', '=', 'users.id');
        $query->leftJoin('trip_post_rental', 'trip_post_master.id', '=', 'trip_post_rental.trip_post_master_id');
        $query->leftJoin('trip_post_outstation', 'trip_post_master.id', '=', 'trip_post_outstation.trip_post_master_id');
        $query->leftJoin('trip_post_local', 'trip_post_master.id', '=', 'trip_post_local.trip_post_master_id');
        $query->leftJoin('vehicle_types', 'trip_post_master.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('trip_bookings', 'trip_post_master.id', '=', 'trip_bookings.trip_id');
        $query->leftJoin('bidding', 'trip_post_master.id', '=', 'bidding.trip_id');
        /* $query->leftJoin("bidding",function($join){
            $join->on("trip_post_master.id","=","bidding.trip_id")->orderBy("bidding.is_accepted","DESC");
        }); */

        $query->leftJoin('cab_post', 'trip_bookings.cab_id', '=', 'cab_post.id');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

     //   $query->where('trip_post_master.is_deleted', '!=', '1');
   //     $query->where('trip_post_master.trip_type', '=', 'Live');

        if ($request->get('trip_type') != 'all') {
            $query->where('trip_post_master.trip_type', $request->get('trip_type'));
        }

        if ($params['fields'] == 'complete') {
            $query->where('trip_bookings.trip_status', '2');
        }
        /* if ($request->get('user_id') != 'all') {
            $query->where('trip_post_master.user_id', $request->get('user_id'));
        } */

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("trip_post_master.trip_type like " . "'" . $search . "' OR trip_post_master.pickup_location like " . "'" . $search . "' OR trip_post_master.pickup_date like " . "'" . $search . "' OR users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "'  OR vehicle_types.name like " . "'" . $search . "'");
            });
        }

        $query->orderBy($orderBy, $order);
        $query->groupBy('trip_post_master.id');

        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            //echo $query->toSql(); exit;
            $records = $query->paginate($perPage);
        }

         
        return $records;
    }

    public function updateStatus($id)
    {
        $user = $this->trip::where('id', $id)->first();
        $user->is_deleted = '1';
        $user->save();
    }
}