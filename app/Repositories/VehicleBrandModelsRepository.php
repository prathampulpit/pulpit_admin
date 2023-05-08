<?php

namespace App\Repositories;

use App\Models\VehicleBrandModels;
use App\Models\ReferFriends;
use Illuminate\Support\Facades\DB;

class VehicleBrandModelsRepository extends BaseRepository
{
    protected $vehicleBrandModels;

    public function __construct(VehicleBrandModels $vehicleBrandModels)
    {
        parent::__construct($vehicleBrandModels);
        $this->vehicleBrandModels = $vehicleBrandModels;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->vehicleBrandModels::whereRaw('1=1');
        $users->select('vehicle_brand_models.id', 'vehicle_brand_models.brand_id', 'vehicle_brand_models.name', 'vehicle_brand_models.status', 'vehicle_brands.name as brand_name', 'vehicle_brand_models.vehicle_type_id');
        $users->leftJoin('vehicle_brands', 'vehicle_brand_models.brand_id', '=', 'vehicle_brands.id');

        // conditions
        $users->where('vehicle_brand_models.status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('vehicle_brand_models.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('vehicle_brand_models.name', $params['name']);
        }

        if (isset($params['not_status'])) {
            $users->where('vehicle_brand_models.status', '!=', $params['not_status']);
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

        $query = $this->vehicleBrandModels::whereRaw('1=1');
        $query->select('vehicle_brand_models.id', 'vehicle_brand_models.name', 'vehicle_brand_models.status', 'vehicle_brands.name as brand_name', 'vehicle_brand_models.vehicle_type_id', 'vehicle_types.name as vehicle_type_name');
        $query->leftJoin('vehicle_brands', 'vehicle_brand_models.brand_id', '=', 'vehicle_brands.id');
        $query->leftJoin('vehicle_types', 'vehicle_brand_models.vehicle_type_id', '=', 'vehicle_types.id');

        $query->where('vehicle_brand_models.status', '!=', '2');
        if (isset($params['name'])) {
            $query->where('vehicle_brand_models.name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("vehicle_brand_models.name like " . "'" . $search . "' OR vehicle_types.name like " . "'" . $search . "' OR vehicle_brands.name like " . "'" . $search . "'");
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
        $user = $this->vehicleBrandModels::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}