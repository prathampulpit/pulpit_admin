<?php

namespace App\Repositories;

use App\Models\UserPurchasedPlans;
use Illuminate\Support\Facades\DB;

class TransactionsRepository extends BaseRepository
{
    protected $userPurchasedPlans;

    public function __construct(UserPurchasedPlans $userPurchasedPlans)
    {
        parent::__construct($userPurchasedPlans);
        $this->userPurchasedPlans = $userPurchasedPlans;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->userPurchasedPlans::whereRaw('1=1');
        $users->select('user_purchased_plans.id', 'user_purchased_plans.user_id', 'user_purchased_plans.subscription_plan_id', 'user_purchased_plans.start_datetime', 'user_purchased_plans.end_datetime', 'users.first_name', 'users.last_name', 'subscription_plans.price', 'subscription_plans.name');
        $users->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        $users->leftJoin('users', 'user_purchased_plans.user_id', '=', 'users.id');

        // conditions
        if (isset($params['id'])) {
            $users->where('user_purchased_plans.id', $params['id']);
        }

        if (isset($params['status'])) {
            $users->where('user_purchased_plans.status', $params['status']);
        }

        if (isset($params['user_id'])) {
            $users->where('user_purchased_plans.user_id', $params['user_id']);
        }

        if (isset($params['not_status'])) {
            $users->where('user_purchased_plans.status', '!=', $params['not_status']);
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $users->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $users->orderBy($params['order_by'], $params['order']);
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

    //DB::raw("CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30') as end_datetime")
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

        $query = $this->userPurchasedPlans::whereRaw('1=1');
        $query->select('user_purchased_plans.id', 'user_purchased_plans.user_id', 'user_purchased_plans.subscription_plan_id', DB::raw("CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30') as start_datetime"), DB::raw("CONVERT_TZ(user_purchased_plans.end_datetime,'+00:00','+05:30') as end_datetime"), 'users.first_name', 'users.last_name', 'subscription_plans.price', 'subscription_plans.name', 'user_purchased_plans.payment_txn_id');
        $query->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        $query->leftJoin('users', 'user_purchased_plans.user_id', '=', 'users.id');

        $query->where('user_purchased_plans.status', '=', '1');
        //$query->where('user_purchased_plans.payment_txn_id', '!=', '');
        if (isset($params['name'])) {
            $query->where('user_purchased_plans.name', $params['name']);
        }

        if (isset($params['trans_types'])) {
            $query->where('user_purchased_plans.trans_type', $params['trans_types']);
        }

        if ($request->get('start_date')) {
            $start_date = date("Y-m-d", strtotime($request->get('start_date')));

            $query->whereRaw("DATE_FORMAT(CONVERT_TZ(user_purchased_plans.start_datetime,'+00:00','+05:30'), '%Y-%m-%d') = '" . $start_date . "' ");
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("subscription_plans.price like " . "'" . $search . "' OR user_purchased_plans.payment_txn_id like " . "'" . $search . "' OR user_purchased_plans.payment_order_id like " . "'" . $search . "' OR users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "'");
            });
        }

        $query->orderBy($orderBy, $order);

        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->paginate($perPage);
        }

        return $records;
    }

    public function transactionData($params)
    {
        $users = $this->userPurchasedPlans::whereRaw('1=1');
        $users->select('user_purchased_plans.id', 'user_purchased_plans.user_id', 'user_purchased_plans.subscription_plan_id', 'user_purchased_plans.start_datetime', 'user_purchased_plans.end_datetime', 'users.first_name', 'users.last_name', 'subscription_plans.price', 'subscription_plans.name');
        $users->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');
        $users->leftJoin('users', 'user_purchased_plans.user_id', '=', 'users.id');

        // conditions
        if (isset($params['id'])) {
            $users->where('user_purchased_plans.id', $params['id']);
        }

        if (isset($params['status'])) {
            $users->where('user_purchased_plans.status', $params['status']);
        }

        if (isset($params['user_id'])) {
            $users->where('user_purchased_plans.user_id', $params['user_id']);
        }

        if (isset($params['not_status'])) {
            $users->where('user_purchased_plans.status', '!=', $params['not_status']);
        }

        if (isset($params['date'])) {
            $users->whereRaw("DATE_FORMAT(user_purchased_plans.start_datetime, '%Y-%m') = '" . $params['date'] . "' ");
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $users->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $users->orderBy($params['order_by'], $params['order']);
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

    public function updateStatus($id)
    {
        $user = $this->userPurchasedPlans::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}