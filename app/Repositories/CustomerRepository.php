<?php

namespace App\Repositories;

use App\Models\Customer;
// use App\Models\ReferFriends;
use Illuminate\Support\Facades\DB;
//use Your Model

/**
 * Class CustomerRepository.
 */
class CustomerRepository extends BaseRepository
{

    protected $customer;

    public function __construct(Customer $customer)
    {
        parent::__construct($customer);
        $this->customer = $customer;
    }

    /**
     * @return string
     *  Return the model
     */
    
    /**
     * Method to get customers with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     * CONVERT_TZ(customers.created_at,'+00:00','+05:30')
     * DB::raw("CONVERT_TZ(customers.created_at,'+00:00','+05:30') as created_at")
     */
    public function getByParams($params)
    {
        $customers = $this->customer::select(DB::raw("CONVERT(customers.id, CHAR) as customer_id"), 'customers.id',
        'customers.mobile_number','customers.email', 'customers.status', 'customers.latitude', 'customers.longitude',
        'customers.first_name', 'customers.last_name', 'customers.address', 'customers.profile_pic',
        DB::raw("CONVERT_TZ(customers.created_at,'+00:00','+05:30') as created_at"), 'customers.state_id','customers.reference_code', 'customers.city_id');
        // $customers->leftJoin('user_work_profile', 'user_work_profile.customer_id', '=', 'customers.id');
        // $customers->leftJoin('user_type', 'user_type.id', '=', 'user_work_profile.user_type_id');
        // //$customers->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        // $customers->leftJoin('user_bank_mapping', 'customers.id', '=', 'user_bank_mapping.customer_id');
        // $customers->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
        // $customers->leftJoin("agent_users", function ($join) {
        //     $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
        // });
        // //$customers->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
        // // $customers->leftJoin("drivers",function($join){
        // //     $join->on("user_work_profile.profile_id","=","drivers.id")->where("user_type.id","=","4");
        // // });
        // $customers->leftJoin("drivers", function ($join) {
        //     $join->on("user_work_profile.profile_id", "=", "drivers.id")->whereIn("user_type.id", [4, 5]);
        // });
        // $customers->leftJoin('vehicles', 'customers.id', '=', 'vehicles.customer_id');
        // $customers->leftJoin('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id');
        // $customers->leftJoin('user_purchased_plans', 'customers.id', '=', 'user_purchased_plans.customer_id');
        // $customers->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        // if (isset($params['paid_users'])) {
        //     $customers->where('subscription_plans.name', '!=', '');
        //     $customers->groupBy('user_purchased_plans.id');
        // }

        // if (isset($params['expired_users'])) {
        //     $today = date("Y-m-d");
        //     //$customers->leftJoin('user_purchased_plans', 'customers.id', '=', 'user_purchased_plans.customer_id');
        //     $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30'), '%Y-%m-%d') < '" . $today . "' AND DATE_FORMAT(CONVERT_TZ(user_purchased_plans.end_datetime,'+00:00','+05:30'), '%Y-%m-%d') > '" . $today . "' ");
        //     $customers->groupBy('customers.id');
        // }

        // // conditions
        // //$customers->where('type', '=', 'customer');
        if (isset($params['customer_id'])) {
            $customers->where('customers.id', $params['customer_id']);
        }

        // if (isset($params['mobile_number'])) {
        //     $customers->where('customers.mobile_number', $params['mobile_number']);
        // }

        if (isset($params['status'])) {
            $customers->where('customers.status', $params['status']);
        }

       if (isset($params['state_id']) & !empty($params['state_id'])) {
            $customers->where('customers.state_id', '=', $params['state_id']);
        }

        // if (isset($params['type'])) {
        //     $customers->where('customers.type', $params['type']);
        // }

        if (isset($params['type'])) {
            $customers->where('type', '=', $params['type']);
        }

        if (isset($params['city_id']) & !empty($params['city_id'])) {
            $customers->where('customers.city_id', '=', $params['city_id']);
        }
        // if (isset($params['vehicle_type_id']) & !empty($params['vehicle_type_id'])) {
        //     $customers->where('vehicles.vehicle_type_id', '=', $params['vehicle_type_id']);
        // }
        // if (isset($params['plan_id']) & !empty($params['plan_id'])) {
        //     $customers->where('user_purchased_plans.subscription_plan_id', '=', $params['plan_id']);
        // }

        if (isset($params['today_joined'])) {
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $params['today_joined'] . "' ");
        }

        if (isset($params['yesterday_joined'])) {
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $params['yesterday_joined'] . "' ");
        }

        if (isset($params['week_joined'])) {
            $today = date("Y-m-d");
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday midnight", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_week . "' AND '" . $end_week . "' ");
        }

        if (isset($params['week'])) {
            $today = date("Y-m-d");
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $params['week'] . "' AND '" . $today . "' ");
        }

        if (isset($params['this_month_joined'])) {
            $today = date("Y-m-d");
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $params['this_month_joined'] . "' ");
        }

        if (isset($params['last_month_joined'])) {
            $today = date("Y-m-d");
            $customers->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $params['last_month_joined'] . "' ");
        }
        if (isset($params['response_type']) && $params['response_type'] == "single") {
            //$customers->groupBy('customers.id');
            //echo $records = $customers->toSql(); exit;
            $records = $customers->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $customers->orderBy($params['order_by'], $params['order']);
            $customers->groupBy('customers.id');
        }

        if (isset($params['count'])) {
            $records = $customers->get()->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $customers->paginate($params['limit']);
        } else {
            $records = $customers->get();
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

        $query = $this->customer::select(DB::raw("CONVERT(customers.id, CHAR) as customer_id"), 'customers.id','customers.mobile_number', 
        'customers.email', 'customers.status', 'customers.first_name','customers.address', 'customers.latitude', 'customers.longitude', 'customers.last_name', 'customers.profile_pic',
         DB::raw("CONVERT_TZ(customers.created_at,'+00:00','+05:30') as created_at"), 'customers.state_id','customers.reference_code', 'customers.city_id');
        // $query->leftJoin('user_work_profile', 'customers.id', '=', 'user_work_profile.customer_id');
        // $query->leftJoin('user_type', 'user_work_profile.user_type_id', '=', 'user_type.id');
        // $query->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
        // $query->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');

        $query->where('type', '=', 'customer');
        // $query->where('user_type.id', '=', '6');
        if (isset($params['status'])) {
            $query->where('customers.status', $params['status']);
        }
        /* if (isset($params['user_type'])) {
            $query->where('user_type.id', $params['user_type']);
        } */


        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            //$query->where(function ($query) use ($search) {
            $query->whereRaw("customers.first_name like " . "'" . $search . "'");
            $query->Orwhere('customers.last_name', 'like', $search);
            $query->Orwhere('customers.email', 'like', $search);
            $query->Orwhere('customers.mobile_number', 'like', $search);
            //});
        }

        // state & city filter
        if ($request->get('state_id') != 'all') {
            $query->where('customers.state_id', $request->get('state_id'));
        }
        if ($request->get('city_id') != 'all') {
            $query->where('customers.city_id', $request->get('city_id'));
        }
        
        /* Filter start here */
        if (isset($params['filter']) && $params['filter'] == 'day') {

            //$start_date = date("Y-m-d", strtotime($request->get('start_date')));

            $start_date = date("Y-m-d H:i:s");
            $start_date = date('Y-m-d', strtotime('+5 hour +30 minutes', strtotime($start_date)));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'yesterday') {
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $yesterday . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'week') {
            $previous_week = strtotime("-1 week +1 day");
            $start_week = strtotime("last monday midnight", $previous_week);
            $end_week = strtotime("next sunday", $start_week);
            $start_week = date("Y-m-d", $start_week);
            $end_week = date("Y-m-d", $end_week);
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $start_week . "' AND '" . $end_week . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'inactive') {
            $yesterday = date('Y-m-d', strtotime("-1 days"));
            $query->where('customers.status', '=', '0');
        }

        if (isset($params['filter']) && $params['filter'] == 'this_month') {
            $this_month = date('Y-m');
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $this_month . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'last_month') {
            $last_month = date('Y-m', strtotime(date('Y-m') . " -1 month"));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m') = '" . $last_month . "' ");
        }

        if (isset($params['filter']) && $params['filter'] == 'fiften_days') {
            $fiften_date = date('Y-m-d', strtotime("-15 days"));
            $start_date = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') BETWEEN '" . $fiften_date . "' AND '" . $start_date . "'");
        }

        if (isset($params['filter']) && $params['filter'] == 'expired_users') {
            $today = date("Y-m-d");
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') < '" . $today . "' AND DATE_FORMAT(user_purchased_plans.start_datetime, '%Y-%m-%d') > '" . $today . "' ");
        }

        /* if (isset($params['filter']) && $params['filter'] == 'paid') {
            $query->where('subscription_plans.name', '!=', '');
        }

        if (isset($params['filter']) && $params['filter'] == 'unpaid') {
            $query->where('subscription_plans.name', '=', '');
        } */
        if (isset($params['filter']) && $params['filter'] == 'paid') {
            //$query->where('subscription_plans.name', '!=', '');
            $query->whereRaw("customers.id IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }

        if (isset($params['filter']) && $params['filter'] == 'unpaid') {
            $query->whereRaw("customers.id NOT IN (SELECT user_id FROM user_purchased_plans WHERE user_purchased_plans.status = 1)");
        }
        /* End */
        if ($request->get('start_date')) {
            $start_date = date("Y-m-d", strtotime($request->get('start_date')));
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' ");
        } else if ($request->get('start_date') && isset($params['user_type'])) {
            $start_date = date("Y-m-d", strtotime($request->get('start_date')));
            $user_type = $params['user_type'];
            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' OR user_type.id = '" . $user_type . "' ");
        } else if ($request->get('user_type') != 'all') {
            $query->where('user_type.id', $request->get('user_type'));
        }

        $query->orderBy($orderBy, $order);
        $query->groupBy('customers.id');
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            //$records = $customers->get();
            $records = $query->paginate($perPage);
        }
        //$records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }

    // public function getPanelPartnerUsers($request, $params)
    // {

    //     if (request('per_page') == 'all') {
    //         $usersCount = [];
    //         $usersCount['count'] = true;
    //         $perPage = $this->getByParams($usersCount);
    //     } else {
    //         $perPage = request('per_page', config('custom.db.per_page'));
    //     }
    //     $orderBy = request('order_by', 'id');
    //     $order = request('order', 'desc');

    //     $query = $this->customer::select(DB::raw("CONVERT(customers.id, CHAR) as customer_id"), 'customers.id', 'customers.mobile_number', 'customers.email', 'customers.status', 'customers.first_name', 'customers.last_name', DB::raw("CONVERT_TZ(customers.created_at,'+00:00','+05:30') as created_at"), 'agent_users.travel_name', 'agent_users.owner_name', 'agent_users.office_no', 'agent_users.total_business_year', 'agent_users.logo', 'agent_users.pan_card', 'agent_users.adhar_card', 'user_type.name as user_type_name', 'drivers.mobile_numebr as driver_mobile_numebr', 'drivers.first_name as driver_first_name', 'drivers.last_name as driver_last_name', 'drivers.adhar_card_no as driver_adhar_card_no', 'drivers.driving_licence_no as driving_licence_no', 'drivers.driving_licence_expiry_date', 'drivers.street_address', 'drivers.pincode', 'agent_users.logo_status as agent_logo_status', 'agent_users.pan_card_url_status as pan_card_url_status', 'agent_users.adhar_card_url_status as adhar_card_url_status', 'agent_users.registration_document_url_status as registration_document_url_status', 'bank_account.bank_document_url_status', 'bank_account.id as bank_account_id', 'drivers.dl_front_url_status', 'drivers.dl_back_url_status', 'drivers.police_verification_url_status', 'drivers.d_pan_card_url_status', 'drivers.police_verification_url_status', 'drivers.d_adhar_card_url_status', 'customers.profile_pic');
    //     $query->leftJoin('user_work_profile', 'customers.id', '=', 'user_work_profile.customer_id');
    //     $query->leftJoin('user_type', 'user_work_profile.user_type_id', '=', 'user_type.id');
    //     $query->leftJoin('user_bank_mapping', 'customers.id', '=', 'user_bank_mapping.customer_id');
    //     $query->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
    //     //$query->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
    //     $query->leftJoin("agent_users", function ($join) {
    //         $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
    //     });
    //     //$query->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
    //     $query->leftJoin("drivers", function ($join) {
    //         $join->on("user_work_profile.profile_id", "=", "drivers.id")->whereIn("user_type.id", [4, 5]);
    //     });

    //     $query->where('type', '=', 'customer');
    //     $query->where('user_type.id', '!=', '6');
    //     if (isset($params['status'])) {
    //         $query->where('customers.status', $params['status']);
    //     }
    //     /* if (isset($params['user_type'])) {
    //         $query->where('user_type.id', $params['user_type']);
    //     } */

    //     if ($request->get('start_date')) {
    //         $start_date = date("Y-m-d", strtotime($request->get('start_date')));
    //         $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' ");
    //     } else if ($request->get('start_date') && isset($params['user_type'])) {
    //         $start_date = date("Y-m-d", strtotime($request->get('start_date')));
    //         $user_type = $params['user_type'];
    //         $query->whereRaw("DATE_FORMAT(CONVERT_TZ(customers.created_at,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' OR user_type.id = '" . $user_type . "' ");
    //     } else if ($request->get('user_type') != 'all') {
    //         $query->where('user_type.id', $request->get('user_type'));
    //     }

    //     // search
    //     if ($request->get('search')) {
    //         $search = '%' . $request->get('search') . '%';
    //         //$query->where(function ($query) use ($search) {
    //         $query->whereRaw("customers.first_name like " . "'" . $search . "'");
    //         $query->Orwhere('customers.last_name', 'like', $search);
    //         $query->Orwhere('customers.email', 'like', $search);
    //         $query->Orwhere('customers.mobile_number', 'like', $search);
    //         //});
    //     }

    //     $query->orderBy($orderBy, $order);
    //     $query->groupBy('customers.id');
    //     if (isset($params['limit'])) {
    //         $records = $query->paginate($params['limit']);
    //     } else {
    //         //$records = $customers->get();
    //         $records = $query->paginate($perPage);
    //     }

    //     //$records = $query->paginate($perPage);
    //     //echo $records = $query->toSql(); exit;
    //     return $records;
    // }

    // public function getAgentDetails($params)
    // {
    //     $customers = $this->customer::select(DB::raw("CONVERT(customers.id, CHAR) as customer_id"), 'customers.id', 'customers.mobile_number', 'customers.email', 'customers.status', 'customers.first_name', 'customers.last_name', 'customers.created_at', 'agent_users.travel_name', 'agent_users.owner_name', 'agent_users.office_no', 'agent_users.total_business_year', 'agent_users.logo', 'agent_users.pan_card', 'agent_users.adhar_card', 'user_type.name as user_type_name', 'drivers.mobile_numebr as driver_mobile_numebr', 'drivers.first_name as driver_first_name', 'drivers.last_name as driver_last_name', 'drivers.adhar_card_no as driver_adhar_card_no', 'drivers.driving_licence_no as driving_licence_no', 'drivers.driving_licence_expiry_date', 'drivers.street_address', 'drivers.pincode', 'drivers.pincode', 'agent_users.pan_card_url', 'agent_users.adhar_card_url', 'drivers.adhar_card_url as d_adhar_card_url', 'drivers.pan_card_url as d_pan_card_url', 'drivers.dl_front_url', 'drivers.dl_back_url', 'drivers.police_verification_url', 'agent_users.logo as agent_logo', 'bank_account.document_url as bank_document_url', 'user_work_profile.user_type_id', 'user_work_profile.profile_id');
    //     $customers->leftJoin('user_work_profile', 'user_work_profile.customer_id', '=', 'customers.id');
    //     $customers->leftJoin('user_type', 'user_type.id', '=', 'user_work_profile.user_type_id');
    //     //$customers->leftJoin('agent_users', 'user_type.id', '=', 'agent_users.user_type_id');
    //     $customers->leftJoin('user_bank_mapping', 'customers.id', '=', 'user_bank_mapping.customer_id');
    //     $customers->leftJoin('bank_account', 'user_bank_mapping.bank_account_id', '=', 'bank_account.id');
    //     $customers->leftJoin("agent_users", function ($join) {
    //         $join->on("user_work_profile.profile_id", "=", "agent_users.id")->where("user_type.id", "=", "3");
    //     });
    //     //$customers->leftJoin('drivers', 'user_work_profile.profile_id', '=', 'drivers.id');
    //     $customers->leftJoin("drivers", function ($join) {
    //         $join->on("user_work_profile.profile_id", "=", "drivers.id")->where("user_type.id", "=", "4");
    //     });

    //     // conditions
    //     //$customers->where('type', '=', 'customer');
    //     if (isset($params['customer_id'])) {
    //         $customers->where('customers.id', $params['customer_id']);
    //     }

    //     if (isset($params['mobile_number'])) {
    //         $customers->where('customers.mobile_number', $params['mobile_number']);
    //     }

    //     if (isset($params['status'])) {
    //         $customers->where('customers.status', $params['status']);
    //     }

    //     if (isset($params['type'])) {
    //         $customers->where('customers.type', $params['type']);
    //     }

    //     if (isset($params['response_type']) && $params['response_type'] == "single") {
    //         //$customers->groupBy('customers.id');
    //         //echo $records = $customers->toSql(); exit;
    //         $records = $customers->first();
    //         return $records;
    //     }

    //     // order by
    //     if (isset($params['order_by']) && isset($params['order'])) {
    //         $customers->orderBy($params['order_by'], $params['order']);
    //         $customers->groupBy('customers.id');
    //     }

    //     if (isset($params['count'])) {
    //         $records = $customers->count();
    //         return $records;
    //     }

    //     // paginate
    //     if (isset($params['limit'])) {
    //         $records = $customers->paginate($params['limit']);
    //     } else {
    //         $records = $customers->get();
    //     }

    //     return $records;
    // }

    /**
     * Method to get a customer instance with all details
     * @var $id
     * @return Customer
     */
    // public function getOneById($id)
    // {
    //     $customers = $this->customer::where('id', $id);
    //     $customers->select('customers.email', 'customers.status', 'customers.id','user_profiles.id as pid', 'user_profiles.customer_id', 'user_profiles.country_id', 'user_profiles.language_id', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.dob', 'user_profiles.gender', 'user_profiles.phone', 'user_profiles.description', 'user_profiles.city', 'user_profiles.state_id', 'user_profiles.address', 'user_profiles.zipcode', 'user_profiles.profile_picture', 'user_profiles.profile_picture_status', 'user_profiles.twitter_link', 'user_profiles.custom_profile_link');
    //     // $customers->leftJoin('user_profiles', 'customers.id', '=', 'user_profiles.customer_id');
    //     return $customers->first();
    // }

    /**
     * Method to get a customer instance with all details
     * @var $email
     * @return Customer
     */
    // public function getOneByEmail($email)
    // {
    //     $customers = $this->customer::where('customers.email', $email);
    //     $customers->select('customers.id');
    //     $customers->leftJoin('user_profiles', 'customers.id', '=', 'user_profiles.customer_id');
    //     return $customers->first();
    // }

    // public function getAllByEmail($email)
    // {
    //     $customers = $this->customer::where('customers.email', $email);
    //     $customers->select('customers.*');
    //     $customers->leftJoin('user_profiles', 'customers.id', '=', 'user_profiles.customer_id');
    //     return $customers->first();
    // }

    public function toggleStatus($id)
    {
        $customer = $this->customer::where('id', $id)->first();
        $newStatus = 'Yes';
        if ($customer->is_pep_scan == 'Yes') {
            $newStatus = 'No';
            $customer->status = '1';
        }

        $customer->is_pep_scan = $newStatus;
        $customer->save();
    }

    public function toggleReferalStatus($id)
    {
        $customer = $this->customer::where('id', $id)->first();
        if ($customer->referal_register_type == '3') {
            $customer->referal_register_type = '0';
            $customer->save();

            // $referFriends = new ReferFriends();
            // $referFriends->customer_id = 0;
            // $referFriends->mobile_number = $customer->country_code . $customer->mobile_number;
            // $referFriends->created_at = date("Y-m-d H:i:s");
            // $referFriends->updated_at = date("Y-m-d H:i:s");
            // $referFriends->save();
        }
    }
}
