<?php

namespace App\Repositories;

use App\Models\ReferralMasters;
use Illuminate\Support\Facades\DB;

class ReferralMastersRepository extends BaseRepository
{
    protected $referralMasters;

    public function __construct(ReferralMasters $referralMasters)
    {
        parent::__construct($referralMasters);
        $this->referralMasters = $referralMasters;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->referralMasters::whereRaw('1=1');
        $users->select('id', 'type', 'referral_bonus', 'max_referral_bonus', 'status');

        // conditions
        $users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('id', $params['id']);
        }

        if (isset($params['type'])) {
            $users->where('type', $params['type']);
        }

        if (isset($params['referral_bonus'])) {
            $users->where('referral_bonus', $params['referral_bonus']);
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

        $query = $this->referralMasters::whereRaw('1=1');
        $query->select('id', 'type', 'referral_bonus', 'max_referral_bonus', 'status');

        $query->where('status', '!=', '2');
        if (isset($params['type'])) {
            $query->where('type', $params['type']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("type like " . "'" . $search . "' OR referral_bonus like " . "'" . $search . "'");
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
        $user = $this->referralMasters::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}