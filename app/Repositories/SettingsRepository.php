<?php

namespace App\Repositories;

use App\Models\Settings;
use Illuminate\Support\Facades\DB;

class SettingsRepository extends BaseRepository
{
    protected $settings;

    public function __construct(Settings $settings)
    {
        parent::__construct($settings);
        $this->settings = $settings;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $settings = $this->settings::whereRaw('1=1');

        // conditions
        if (isset($params['id'])) {
            $settings->where('id', $params['id']);
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $settings->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $settings->orderBy($params['order_by'], $params['order']);
        }

        if (isset($params['count'])) {
            $records = $settings->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $settings->paginate($params['limit']);
        } else {
            $records = $settings->get();
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

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("name like " . "'" . $search . "'");
                $query->Orwhere('email', 'like', $search);
                $query->Orwhere('mobile_number', 'like', $search);
            });
        }

        $query->orderBy($orderBy, $order);
        $records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }
}