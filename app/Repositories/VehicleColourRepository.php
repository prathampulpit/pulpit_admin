<?php

namespace App\Repositories;

use App\Models\VehicleColour;
use Illuminate\Support\Facades\DB;

class VehicleColourRepository extends BaseRepository
{
    protected $vehicleColour;

    public function __construct(VehicleColour $vehicleColour)
    {
        parent::__construct($vehicleColour);
        $this->vehicleColour = $vehicleColour;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->vehicleColour::whereRaw('1=1');
        $users->select('vehicle_colour.id', 'vehicle_colour.model_id', 'vehicle_colour.name', 'vehicle_colour.colour_code', 'vehicle_colour.status');
        //$users->leftJoin('vehicle_brand_models', 'vehicle_colour.model_id', '=', 'vehicle_brand_models.id');

        // conditions
        $users->where('vehicle_colour.status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('vehicle_colour.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('vehicle_colour.name', $params['name']);
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

        $query = $this->vehicleColour::whereRaw('1=1');
        $query->select('vehicle_colour.id', 'vehicle_colour.name', 'vehicle_colour.colour_code', 'vehicle_colour.status');
        //$query->leftJoin('vehicle_brand_models', 'vehicle_colour.model_id', '=', 'vehicle_brand_models.id');

        $query->where('vehicle_colour.status', '!=', '2');
        if (isset($params['name'])) {
            $query->where('vehicle_colour.name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("vehicle_colour.name like " . "'" . $search . "'");
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
        $user = $this->vehicleColour::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}