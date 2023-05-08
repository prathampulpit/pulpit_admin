<?php

namespace App\Repositories;

use App\Models\VehicleFuelType;
use Illuminate\Support\Facades\DB;

class VehicleFuelTypeRepository extends BaseRepository
{
    protected $vehicleFuelType;

    public function __construct(VehicleFuelType $vehicleFuelType)
    {
        parent::__construct($vehicleFuelType);
        $this->vehicleFuelType = $vehicleFuelType;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->vehicleFuelType::whereRaw('1=1');
        $users->select('vehicle_fuel_type.id', 'vehicle_fuel_type.model_id', 'vehicle_fuel_type.name', 'vehicle_fuel_type.status');
        //$users->leftJoin('vehicle_brand_models', 'vehicle_fuel_type.model_id', '=', 'vehicle_brand_models.id');

        // conditions
        $users->where('vehicle_fuel_type.status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('vehicle_fuel_type.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('vehicle_fuel_type.name', $params['name']);
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

        $query = $this->vehicleFuelType::whereRaw('1=1');
        $query->select('vehicle_fuel_type.id', 'vehicle_fuel_type.model_id', 'vehicle_fuel_type.name', 'vehicle_fuel_type.status');
        //$query->leftJoin('vehicle_brand_models', 'vehicle_fuel_type.model_id', '=', 'vehicle_brand_models.id');

        $query->where('vehicle_fuel_type.status', '!=', '2');
        if (isset($params['name'])) {
            $query->where('vehicle_fuel_type.name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("vehicle_fuel_type.name like " . "'" . $search . "'");
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
        $user = $this->vehicleFuelType::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}