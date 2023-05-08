<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\ReferFriends;
use Illuminate\Support\Facades\DB;

class TravelRepository extends BaseRepository
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    /**
     * Get User table details only
     */
    public function getUserDetails($params)
    {
        $users = $this->user::whereRaw('1=1');
        $users->select(
            DB::raw("CONVERT(users.id, CHAR) as user_id"),
            'users.id',
            'users.user_type_id',
            'users.reference_code',
            'users.mobile_number',
            'users.emailid',
            'users.user_interest_status',
            'users.user_status',
            'users.first_name',
            'users.last_name',
            'users.created_at',
            'users.user_interest_status',
            'states.name as state_name',
            'city.name as city_name',
            'users.profile_pic',
            'users.profile_pic_status',
            'users.last_updated_id',
            'users.is_approved'
        );
        $users->leftJoin('states', 'users.state', '=', 'states.id');
        $users->leftJoin('city', 'users.city_id', '=', 'city.id');
        $users->where('type', '=', 'user');
        $users->where('users.status', '=', '1');


        // conditions
        //$users->where('type', '=', 'user');
        if (isset($params['user_id'])) {
            $users->where('users.id', $params['user_id']);
        }

        if (isset($params['mobile_number'])) {
            $users->where('users.mobile_number', $params['mobile_number']);
        }

        if (isset($params['user_status'])) {
            $users->where('users.user_status', $params['user_status']);
        }

        if (isset($params['type'])) {
            $users->where('users.type', $params['type']);
        }
        if (isset($params['state_id']) & !empty($params['state_id'])) {
            $users->where('users.state', '=', $params['state_id']);
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            //$users->groupBy('users.id');
            //echo $records = $users->toSql(); exit;
            $records = $users->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $users->orderBy($params['order_by'], $params['order']);
            $users->groupBy('users.id');
        }

        if (isset($params['count'])) {
            $users->groupBy('users.id');
            $records = $users->get()->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $users->paginate($params['limit']);
        } else {
            $records = $users->get();
        }

        return $records;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->user::whereRaw('1=1');
        $users->select(
            DB::raw("CONVERT(users.id, CHAR) as user_id"),
            'users.id',
            'agent_users.id as agent_id',
            'drivers.id as driver_id',
            'users.mobile_number',
            'users.emailid',
            'users.user_interest_status',
            'users.user_status',
            'users.first_name',
            'users.last_name',
            'users.created_at',
            'agent_users.travel_name',
            'agent_users.owner_name',
            'users.user_interest_status',
            'agent_users.office_no',
            'agent_users.total_business_year',
            'agent_users.logo',
            'agent_users.pan_card',
            'agent_users.adhar_card',
            'users.bank_account_id',
            'user_type.name as user_type_name',
            'drivers.mobile_numebr as driver_mobile_numebr',
            'drivers.first_name as driver_first_name',
            'drivers.last_name as driver_last_name',
            'drivers.adhar_card_no as driver_adhar_card_no',
            'drivers.driving_licence_no as driving_licence_no',
            'drivers.driving_licence_expiry_date',
            'drivers.street_address',
            'drivers.pincode',
            'drivers.pincode',
            'agent_users.pan_card_url',
            'agent_users.adhar_card_url',
            'agent_users.adhar_card_back_url',
            'agent_users.registration_document_url',
            'drivers.adhar_card_url as d_adhar_card_url',
            'drivers.pan_card_url as d_pan_card_url',
            'drivers.dl_front_url',
            'drivers.dl_back_url',
            'drivers.police_verification_url',
            'agent_users.logo as agent_logo',
            'bank_account.document_url as bank_document_url',
            'user_work_profile.user_type_id',
            'user_work_profile.profile_id',
            'agent_users.logo_status as agent_logo_status',
            'agent_users.pan_card_url_status as pan_card_url_status',
            'agent_users.adhar_card_url_status as adhar_card_url_status',
            'agent_users.adhar_card_back_url_status as adhar_card_back_url_status',
            'agent_users.registration_document_url_status as registration_document_url_status',
            'bank_account.bank_document_url_status',
            'drivers.dl_front_url_status',
            'drivers.dl_back_url_status',
            'drivers.police_verification_url_status',
            'drivers.d_pan_card_url_status',
            'drivers.d_adhar_card_url_status',
            'users.state',
            'users.city_id',
            'users.otp',
            'users.is_otp',
            'user_purchased_plans.end_datetime',
            'subscription_plans.name as subscription_name',
            'bank_account.account_number',
            'bank_account.bank_name',
            'bank_account.branch_name',
            'users.reference_code',
            'bank_account.ifsc_code',
            'states.name as state_name',
            'city.name as city_name'
        );
        //$users->leftJoin('user_work_profile', 'user_work_profile.user_id', '=', 'users.id');
        $users->leftJoin("user_work_profile", function ($join) {
            $join->on("users.id", "=", "user_work_profile.user_id")->where("user_work_profile.status", 1);
        });
        $users->leftJoin('user_type', 'user_type.id', '=', 'user_work_profile.user_type_id');
        //$users->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        $users->leftJoin('user_bank_mapping', 'users.id', '=', 'user_bank_mapping.user_id');
        $users->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
        $users->leftJoin("agent_users", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
        });
        //$users->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
        // $users->leftJoin("drivers",function($join){
        //     $join->on("user_work_profile.profile_id","=","drivers.id")->where("user_type.id","=","4");
        // });
        $users->leftJoin("drivers", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "drivers.id")->whereIn("user_type.id", [4, 5]);
        });
        $users->leftJoin('user_purchased_plans', 'users.id', '=', 'user_purchased_plans.user_id');
        $users->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        $users->leftJoin('states', 'users.state', '=', 'states.id');
        $users->leftJoin('city', 'users.city_id', '=', 'city.id');

        $users->where('type', '=', 'user');
        $users->where('user_type.id', '=', '3');
        $users->where('agent_users.status', '=', '1');
        $users->where('users.status', '=', '1');
        if (isset($params['expired_users'])) {
            $today = date("Y-m-d");
            //$users->leftJoin('user_purchased_plans', 'users.id', '=', 'user_purchased_plans.user_id');
            $users->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') < '" . $today . "' AND DATE_FORMAT(user_purchased_plans.start_datetime, '%Y-%m-%d') > '" . $today . "' ");
            $users->groupBy('users.id');
        }

        // conditions
        //$users->where('type', '=', 'user');
        if (isset($params['user_id'])) {
            $users->where('users.id', $params['user_id']);
        }

        if (isset($params['mobile_number'])) {
            $users->where('users.mobile_number', $params['mobile_number']);
        }

        if (isset($params['user_status'])) {
            $users->where('users.user_status', $params['user_status']);
        }

        if (isset($params['type'])) {
            $users->where('users.type', $params['type']);
        }
        if (isset($params['state_id']) & !empty($params['state_id'])) {
            $users->where('users.state', '=', $params['state_id']);
        }
        if (isset($params['vehicle_type_id']) & !empty($params['vehicle_type_id'])) {
            $users->where('subscription_plans.vehicle_type_id', '=', $params['vehicle_type_id']);
        }
        if (isset($params['plan_id']) & !empty($params['plan_id'])) {
            $users->where('user_purchased_plans.subscription_plan_id', '=', $params['plan_id']);
        }

        if (isset($params['today_joined'])) {
            $today = date("Y-m-d");
            $users->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $today . "' ");
        }

        if (isset($params['yesterday_joined'])) {
            $users->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $params['yesterday_joined'] . "' ");
        }

        if (isset($params['week'])) {
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday midnight", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);

            $users->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_week . "' AND '" . $end_week . "' ");
        }

        if (isset($params['this_month_joined'])) {
            $today = date("Y-m-d");
            $users->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m') = '" . $params['this_month_joined'] . "' ");
        }

        if (isset($params['last_month_joined'])) {
            $today = date("Y-m-d");
            $users->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m') = '" . $params['last_month_joined'] . "' ");
        }

        if (isset($params['paid_users'])) {
            //$users->where('subscription_plans.name', '!=', '');
            $users->whereRaw("users.id IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }

        if (isset($params['unpaid_users'])) {
            //$users->where('subscription_plans.name', '=', '');
            $users->whereRaw("users.id NOT IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            //$users->groupBy('users.id');
            //echo $records = $users->toSql(); exit;
            $records = $users->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $users->orderBy($params['order_by'], $params['order']);
            $users->groupBy('users.id');
        }

        if (isset($params['count'])) {
            $users->groupBy('users.id');
            $records = $users->get()->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $users->paginate($params['limit']);
        } else {
            $records = $users->get();
        }

        return $records;
    }

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

        $query = $this->user::whereRaw('1=1');
        $query->select(DB::raw("CONVERT(users.id, CHAR) as user_id"), 'users.id', 'users.mobile_number', 'users.user_interest_status', 'users.emailid', 'users.user_status', 'users.first_name', 'users.last_name', 'users.created_at', 'agent_users.travel_name', 'agent_users.owner_name', 'agent_users.office_no', 'agent_users.total_business_year', 'agent_users.logo', 'agent_users.pan_card', 'agent_users.adhar_card', 'users.bank_account_id', 'user_type.name as user_type_name', 'drivers.mobile_numebr as driver_mobile_numebr', 'drivers.first_name as driver_first_name', 'drivers.last_name as driver_last_name', 'drivers.adhar_card_no as driver_adhar_card_no', 'drivers.driving_licence_no as driving_licence_no', 'drivers.driving_licence_expiry_date', 'drivers.street_address', 'drivers.pincode', 'users.otp', 'users.is_otp', 'users.profile_pic', 'users.is_approved');
        $query->leftJoin('user_work_profile', 'users.id', '=', 'user_work_profile.user_id');
        $query->leftJoin('user_type', 'user_work_profile.user_type_id', '=', 'user_type.id');
        $query->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        $query->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
        $query->leftJoin('user_purchased_plans', 'users.id', '=', 'user_purchased_plans.user_id');

        $query->where('type', '=', 'user');
        $query->where('user_type.id', '=', '3');
        $query->where('agent_users.status', '=', '1');
        $query->where('users.status', '=', '1');
        if (isset($params['user_status'])) {
            $query->where('users.user_status', $params['user_status']);
        }
        if ($request->get('state_id') != 'all') {
            $query->where('users.state', $request->get('state_id'));
        }
        if ($request->get('city_id') != 'all') {
            $query->where('users.city_id', $request->get('city_id'));
        }

        if ($request->get('plan_id')) {
            $query->where('user_purchased_plans.subscription_plan_id', $request->get('plan_id'));
        }

        if ($request->get('start_date')) {
            $start_date = date("Y-m-d", strtotime($request->get('start_date')));
            $query->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $start_date . "' ");
        } else if ($request->get('start_date') && isset($params['user_type'])) {
            $start_date = date("Y-m-d", strtotime($request->get('start_date')));
            $user_type = $params['user_type'];
            $query->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $start_date . "' OR user_type.id = '" . $user_type . "' ");
        } else if ($request->get('user_type') != 'all') {
            $query->where('user_type.id', $request->get('user_type'));
        }

        $query->whereRaw('(user_work_profile.status = 1 OR user_work_profile.status IS NULL)');

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            //$query->where(function ($query) use ($search) {
            /* $query->whereRaw("users.first_name like " . "'" . $search . "'");
                $query->Orwhere('users.last_name', 'like', $search);
                $query->Orwhere('users.emailid', 'like', $search);
                $query->Orwhere('users.mobile_number', 'like', $search); */
            $query->whereRaw("(users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "' OR users.emailid like '" . $search . "' OR users.mobile_number like '" . $search . "')");
            //});
        }

        $query->orderBy($orderBy, $order);
        $query->groupBy('users.id');
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            //$records = $users->get();
            $records = $query->paginate($perPage);
        }

        //$records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }

    public function getPanelPartnerUsers($request, $params)
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

        $query = $this->user::whereRaw('1=1');
        $query->select(DB::raw("CONVERT(users.id, CHAR) as user_id"), 'users.id', 'users.mobile_number', 'users.emailid', 'users.user_interest_status', 'users.user_status', 'users.first_name', 'users.last_name', 'users.created_at', 'agent_users.travel_name', 'agent_users.owner_name', 'agent_users.office_no', 'agent_users.total_business_year', 'agent_users.logo', 'agent_users.pan_card', 'agent_users.adhar_card', 'users.bank_account_id', 'user_type.name as user_type_name', 'drivers.mobile_numebr as driver_mobile_numebr', 'drivers.first_name as driver_first_name', 'drivers.last_name as driver_last_name', 'drivers.adhar_card_no as driver_adhar_card_no', 'drivers.driving_licence_no as driving_licence_no', 'drivers.driving_licence_expiry_date', 'drivers.street_address', 'drivers.pincode', 'users.otp', 'users.is_otp', 'agent_users.logo_status as agent_logo_status', 'agent_users.pan_card_url_status as pan_card_url_status', 'agent_users.adhar_card_url_status as adhar_card_url_status', 'agent_users.registration_document_url_status as registration_document_url_status', 'bank_account.bank_document_url_status', 'drivers.dl_front_url_status', 'drivers.dl_back_url_status',  'drivers.d_pan_card_url_status', 'drivers.police_verification_url_status', 'drivers.d_adhar_card_url_status', 'users.profile_pic', 'user_purchased_plans.end_datetime', 'subscription_plans.name as subscription_name', 'users.availability_status', DB::raw("(SELECT count(id) FROM vehicles WHERE vehicles.user_id = users.id AND vehicles.status = 1) as total_cabs"), DB::raw("(SELECT count(agent_users.id) FROM agent_users LEFT JOIN user_work_profile ON agent_users.id = user_work_profile.profile_id WHERE user_work_profile.user_id = users.id AND user_work_profile.user_type_id = '4') as total_drivers"), 'drivers.all_document_verify', 'users.is_approved');

        //$query->leftJoin('user_work_profile', 'users.id', '=', 'user_work_profile.user_id');
        $query->leftJoin("user_work_profile", function ($join) {
            $join->on("users.id", "=", "user_work_profile.user_id")->where("user_work_profile.status", 1);
        });
        $query->leftJoin('user_type', 'user_work_profile.user_type_id', '=', 'user_type.id');
        $query->leftJoin('user_bank_mapping', 'users.id', '=', 'user_bank_mapping.user_id');
        $query->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
        //$query->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        $query->leftJoin("agent_users", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
        });
        //$query->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
        $query->leftJoin("drivers", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "drivers.id")->whereIn("user_type.id", [3]);
        });
        $query->leftJoin('user_purchased_plans', 'users.id', '=', 'user_purchased_plans.user_id');
        $query->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        $query->leftJoin('vehicle_types', 'subscription_plans.vehicle_type_id', '=', 'vehicle_types.id');

        $query->where('type', '=', 'user');
        $query->where('user_type.id', '=', '3');
        $query->where('agent_users.status', '=', '1');
        $query->where('users.status', '=', '1');

        $query->whereRaw('(user_work_profile.status = 1 OR user_work_profile.status IS NULL)');

        if (isset($params['user_status'])) {
            $query->where('users.user_status', $params['user_status']);
        }
        if ($request->get('state_id') != 'all') {
            $query->where('users.state', $request->get('state_id'));
        }
        if ($request->get('city_id')) {
            $query->where('users.city_id', $request->get('city_id'));
        }
        if ($request->get('plan_id')) {
            $query->where('user_purchased_plans.subscription_plan_id', $request->get('plan_id'));
        }
        if ($request->get('vehicle_type_id')) {
            $query->where('subscription_plans.vehicle_type_id', $request->get('vehicle_type_id'));
        }

        /* Filter start here */
        if (isset($params['filter']) && $params['filter'] == 'day') {
            $start_date = date("Y-m-d H:i:s");
            $start_date = date('Y-m-d', strtotime('+5 hour +30 minutes', strtotime($start_date)));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'yesterday') {
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $yesterday . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'week') {
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday midnight", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);

            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_week . "' AND '" . $end_week . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'inactive') {
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $query->where('users.status', '=', '0');
        }

        if (isset($params['filter']) && $params['filter'] == 'this_month') {
            $this_month = date('Y-m');
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $this_month . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'last_month') {
            $last_month = date('Y-m', strtotime(date('Y-m') . " -1 month"));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $last_month . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'fiften_date') {
            $fiften_date = date('Y-m-d', strtotime("-15 days"));
            $start_date = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $fiften_date . "' AND '" . $start_date . "'");
        }

        if (isset($params['filter']) && $params['filter'] == 'expired_users') {
            $today = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(users.created_at,'+00:00','+05:30'), '%Y-%m-%d') < '" . $today . "' AND DATE_FORMAT(user_purchased_plans.start_datetime, '%Y-%m-%d') > '" . $today . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'paid') {
            //$query->where('subscription_plans.name', '!=', '');
            $query->whereRaw("users.id IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }

        if (isset($params['filter']) && $params['filter'] == 'unpaid') {
            $query->whereRaw("users.id NOT IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }
        /* End */

        if ($request->get('start_date')) {
            $start_date = date("Y-m-d H:i:s", strtotime($request->get('start_date')));
            $start_date = date('Y-m-d', strtotime('+5 hour +30 minutes', strtotime($start_date)));
            $query->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $start_date . "' ");
        } else if ($request->get('start_date') && isset($params['user_type'])) {
            $start_date = date("Y-m-d H:i:s", strtotime($request->get('start_date')));
            $start_date = date('Y-m-d', strtotime('+5 hour +30 minutes', strtotime($start_date)));
            $user_type = $params['user_type'];
            $query->whereRaw("DATE_FORMAT(users.created_at, '%Y-%m-%d') = '" . $start_date . "' OR user_type.id = '" . $user_type . "' ");
        } else if ($request->get('user_type') != 'all') {
            $query->where('user_type.id', $request->get('user_type'));
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            //$query->where(function ($query) use ($search) {
            $query->whereRaw("(users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "' OR users.emailid like " . "'" . $search . "' OR users.mobile_number like " . "'" . $search . "') ");
            //});
        }

        $query->orderBy($orderBy, $order);
        $query->groupBy('users.id');
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            //$records = $users->get();
            $records = $query->paginate($perPage);
        }

        //$records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }

    public function getAgentDetails($params)
    {
        $users = $this->user::whereRaw('1=1');
        $users->select(DB::raw("CONVERT(users.id, CHAR) as user_id"), 'users.id', 'users.mobile_number', 'users.emailid', 'users.user_interest_status', 'users.user_status', 'users.first_name', 'users.last_name', 'users.created_at', 'agent_users.travel_name', 'agent_users.owner_name', 'agent_users.office_no', 'agent_users.total_business_year', 'agent_users.logo', 'agent_users.pan_card', 'agent_users.adhar_card', 'users.bank_account_id', 'user_type.name as user_type_name', 'drivers.mobile_numebr as driver_mobile_numebr', 'drivers.first_name as driver_first_name', 'drivers.last_name as driver_last_name', 'drivers.adhar_card_no as driver_adhar_card_no', 'drivers.driving_licence_no as driving_licence_no', 'drivers.driving_licence_expiry_date', 'drivers.street_address', 'drivers.pincode', 'drivers.pincode', 'agent_users.pan_card_url', 'agent_users.adhar_card_url', 'drivers.adhar_card_url as d_adhar_card_url', 'drivers.pan_card_url as d_pan_card_url', 'drivers.dl_front_url', 'drivers.dl_back_url', 'drivers.police_verification_url', 'agent_users.logo as agent_logo', 'bank_account.document_url as bank_document_url', 'user_work_profile.user_type_id', 'user_work_profile.profile_id');
        $users->leftJoin('user_work_profile', 'user_work_profile.user_id', '=', 'users.id');
        $users->leftJoin('user_type', 'user_type.id', '=', 'user_work_profile.user_type_id');
        //$users->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        $users->leftJoin('user_bank_mapping', 'users.id', '=', 'user_bank_mapping.user_id');
        $users->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
        $users->leftJoin("agent_users", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
        });
        //$users->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
        $users->leftJoin("drivers", function ($join) {
            $join->on("user_work_profile.profile_id", "=", "drivers.id")->where("user_type.id", "=", "4");
        });

        // conditions
        //$users->where('type', '=', 'user');
        if (isset($params['user_id'])) {
            $users->where('users.id', $params['user_id']);
        }

        if (isset($params['mobile_number'])) {
            $users->where('users.mobile_number', $params['mobile_number']);
        }

        if (isset($params['user_status'])) {
            $users->where('users.user_status', $params['user_status']);
        }

        if (isset($params['type'])) {
            $users->where('users.type', $params['type']);
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            //$users->groupBy('users.id');
            //echo $records = $users->toSql(); exit;
            $records = $users->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $users->orderBy($params['order_by'], $params['order']);
            $users->groupBy('users.id');
        }

        if (isset($params['count'])) {
            $records = $users->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $users->paginate($params['limit']);
        } else {
            $records = $users->get();
        }

        return $records;
    }

    /**
     * Method to get a user instance with all details
     * @var $id
     * @return User
     */
    public function getOneById($id)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('users.id', $id);
        $users->select('users.email', 'users.status', 'users.id', 'users.id as uid', 'user_profiles.id as pid', 'user_profiles.user_id', 'user_profiles.country_id', 'user_profiles.language_id', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.dob', 'user_profiles.gender', 'user_profiles.phone', 'user_profiles.description', 'user_profiles.city', 'user_profiles.state', 'user_profiles.address', 'user_profiles.zipcode', 'user_profiles.profile_picture', 'user_profiles.profile_picture_status', 'user_profiles.twitter_link', 'user_profiles.custom_profile_link');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }

    /**
     * Method to get a user instance with all details
     * @var $email
     * @return User
     */
    public function getOneByEmail($email)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('users.email', $email);
        $users->select('users.id');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }

    public function getAllByEmail($email)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('users.email', $email);
        $users->select('users.*');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }

    public function toggleStatus($id)
    {
        $user = $this->user::where('id', $id)->first();
        $newStatus = 'Yes';
        if ($user->is_pep_scan == 'Yes') {
            $newStatus = 'No';
            $user->user_status = '1';
        }

        $user->is_pep_scan = $newStatus;
        $user->save();
    }

    public function toggleReferalStatus($id)
    {
        $user = $this->user::where('id', $id)->first();
        if ($user->referal_register_type == '3') {
            $user->referal_register_type = '0';
            $user->save();

            $referFriends = new ReferFriends();
            $referFriends->user_id = 0;
            $referFriends->mobile_number = $user->country_code . $user->mobile_number;
            $referFriends->created_at = date("Y-m-d H:i:s");
            $referFriends->updated_at = date("Y-m-d H:i:s");
            $referFriends->save();
        }
    }
}