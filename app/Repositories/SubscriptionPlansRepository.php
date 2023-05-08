<?php

namespace App\Repositories;

use App\Models\SubscriptionPlans;
use Illuminate\Support\Facades\DB;

class SubscriptionPlansRepository extends BaseRepository
{
    protected $subscriptionPlans;

    public function __construct(SubscriptionPlans $subscriptionPlans)
    {
        parent::__construct($subscriptionPlans);
        $this->subscriptionPlans = $subscriptionPlans;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->subscriptionPlans::whereRaw('1=1');
        $users->select('subscription_plans.id', 'subscription_plans.vehicle_type_id', 'subscription_plans.name', 'subscription_plans.plan_validity', 'vehicle_types.name as vehicle_types_name', 'subscription_plans.price', 'subscription_plans.order');
        $users->leftJoin('vehicle_types', 'subscription_plans.vehicle_type_id', '=', 'vehicle_types.id');

        if (isset($params['id'])) {
            $users->where('subscription_plans.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('subscription_plans.name', $params['name']);
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

        $query = $this->subscriptionPlans::whereRaw('1=1');
        $query->select('subscription_plans.id', 'subscription_plans.vehicle_type_id', 'subscription_plans.name', 'subscription_plans.plan_validity', 'vehicle_types.name as vehicle_types_name', 'subscription_plans.price', 'subscription_plans.order', 'subscription_plans.is_agent');
        $query->leftJoin('vehicle_types', 'subscription_plans.vehicle_type_id', '=', 'vehicle_types.id');

        $query->whereRaw('subscription_plans.status != 2');
        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("subscription_plans.name like " . "'" . $search . "' OR subscription_plans.price like " . "'" . $search . "' OR vehicle_types.name like " . "'" . $search . "'");
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

    public function updateStatus($id)
    {
        $user = $this->subscriptionPlans::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}