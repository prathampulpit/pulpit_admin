<?php

namespace App\Repositories;

use App\Models\ClaimedRewards;
use DB;

class ClaimedRewardsRepository extends BaseRepository
{
    protected $claimedRewards;

    public function __construct(ClaimedRewards $claimedRewards)
    {
        parent::__construct($claimedRewards);
        $this->claimedRewards = $claimedRewards;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->claimedRewards::whereRaw('1=1');
        $users->select('claimed_rewards.id', 'claimed_rewards.user_id', 'claimed_rewards.datetime', 'claimed_rewards.status', 'users.first_name', 'users.last_name');
        $users->leftJoin('users', 'claimed_rewards.user_id', '=', 'users.id');

        if (isset($params['id'])) {
            $users->where('claimed_rewards.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('users.first_name', $params['name']);
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

        $query = $this->claimedRewards::whereRaw('1=1');
        $query->select('claimed_rewards.id', 'claimed_rewards.user_id', 'claimed_rewards.datetime', 'claimed_rewards.status', 'users.first_name', 'users.last_name');
        $query->leftJoin('users', 'claimed_rewards.user_id', '=', 'users.id');

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "'");
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
        $user = $this->claimedRewards::where('id', $id)->first();
        $user->status = '1';
        $user->save();
    }
}