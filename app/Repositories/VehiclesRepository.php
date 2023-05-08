<?php

namespace App\Repositories;

use App\Models\Vehicles;
use Illuminate\Support\Facades\DB;

class VehiclesRepository extends BaseRepository
{
    protected $vehicles;

    public function __construct(Vehicles $vehicles)
    {
        parent::__construct($vehicles);
        $this->vehicles = $vehicles;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->vehicles::whereRaw('1=1');
        $users->select('vehicles.id', 'users.first_name', 'users.last_name','users.mobile_number', 'vehicles.brand_id', 'vehicles.vehicle_number', 'vehicles.status', 'vehicle_brands.name as brand_name', 'users.first_name as name', 'vehicle_brand_models.name as model_name', 'users.mobile_number', 'vehicle_types.name as vehicle_type_name', 'vehicle_fuel_type.name as vehicle_fuel_type_name', 'vehicles.rc_front_url', 'vehicles.rc_back_url', 'vehicles.insurance_doc_url', 'vehicles.permit_doc_url', 'vehicles.fitness_doc_url', 'vehicles.puc_doc_url', 'vehicles.agreement_doc_url', 'vehicles.registration_year', 'vehicles.rc_front_url_status', 'vehicles.rc_back_url_status', 'vehicles.insurance_doc_url_status', 'vehicles.permit_doc_url_status', 'vehicles.fitness_doc_url_status', 'vehicles.puc_doc_url_status', 'vehicles.agreement_doc_url_status', 'vehicles.insurance_exp_date', 'vehicles.permit_exp_date', 'vehicles.fitness_exp_date', 'vehicles.puc_exp_date', 'vehicles.user_id');
        $users->leftJoin('vehicle_brands', 'vehicles.brand_id', '=', 'vehicle_brands.id');
        $users->leftJoin('vehicle_brand_models', 'vehicles.model_id', '=', 'vehicle_brand_models.id');
        $users->leftJoin('users', 'vehicles.user_id', '=', 'users.id');
        $users->leftJoin('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id');
        $users->leftJoin('vehicle_fuel_type', 'vehicles.fuel_type_id', '=', 'vehicle_fuel_type.id');
        // conditions
        $users->where('vehicles.status', '=', '1');
        if (isset($params['id'])) {
            $users->where('vehicles.id', $params['id']);
        }
        if (isset($params['status'])) {
            $users->where('vehicles.status', $params['status']);
        }

        if (isset($params['user_id'])) {
            $users->where('vehicles.user_id', $params['user_id']);
        }

        if (isset($params['state_id']) & !empty($params['state_id'])) {
            $users->whereRaw("vehicles.state like " . "'" . $params['state_id'] . "'");
        }

        if (isset($params['city_id']) & !empty($params['city_id'])) {
            $users->whereRaw("vehicles.city like " . "'" . $params['city_id'] . "'");
        }

        if (isset($params['vehicle_type']) & !empty($params['vehicle_type'])) {
            $users->where("vehicles.vehicle_type_id", "=" , $params['vehicle_type']);
        }

        if (isset($params['name'])) {
            $users->where('vehicles.name', $params['name']);
        }

        if (isset($params['not_status'])) {
            $users->where('vehicles.status', '!=', $params['not_status']);
        }

        if (!empty($params['all_document_verify'])) {
            if ($params['all_document_verify'] == 'pending') {
                $users->where('vehicles.all_document_verify', '0');
            } elseif ($params['all_document_verify'] == 'complete') {
                $users->where('vehicles.all_document_verify', '1');
            }
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
        $query = $this->vehicles::whereRaw('1=1');
        $query->select('vehicles.id', 'users.first_name', 'users.last_name', 'vehicles.brand_id','vehicles.registration_year', 'vehicles.vehicle_number', 'vehicles.status', 'vehicle_brands.name as brand_name', 'users.first_name as name', 'vehicle_brand_models.name as model_name', 'users.mobile_number', 'vehicle_types.name as vehicle_type_name', 'vehicle_fuel_type.name as vehicle_fuel_type_name');
        $query->leftJoin('vehicle_brands', 'vehicles.brand_id', '=', 'vehicle_brands.id');
        $query->leftJoin('vehicle_brand_models', 'vehicles.model_id', '=', 'vehicle_brand_models.id');
        $query->leftJoin('users', 'vehicles.user_id', '=', 'users.id');
        $query->leftJoin('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('vehicle_fuel_type', 'vehicles.fuel_type_id', '=', 'vehicle_fuel_type.id');

        $query->where('vehicles.status', '=', '1');
        if (isset($params['name'])) {
            $query->where('vehicles.name', $params['name']);
        }

        if (!empty($params['all_document_verify'])) {
            if ($params['all_document_verify'] == 'pending') {
                $query->where('vehicles.all_document_verify', '0');
            } elseif ($params['all_document_verify'] == 'complete') {
                $query->where('vehicles.all_document_verify', '1');
            }
        }
        if (isset($request->state_id) && !empty($request->state_id) && $request->state_id != 'all') {
            $query->whereRaw("vehicles.state like " . "'" . $request->state_id . "'");
        }

        if (isset($request->city_id) && !empty($request->city_id) && $request->city_id != 'all') {
            $query->whereRaw("vehicles.city like " . "'" . $request->city_id . "'");
        }
        if (isset($request->vehicle_type) & !empty($request->vehicle_type)  && $request->vehicle_type != 'all') {
            $query->where("vehicles.vehicle_type_id", "=" , $request->vehicle_type);
        }
        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("vehicles.vehicle_number like " . "'" . $search . "' OR vehicle_brand_models.name like " . "'" . $search . "' OR vehicles.registration_year like " . "'" . $search . "' OR vehicle_brands.name like " . "'" . $search . "' OR users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "'");
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
        $user = $this->vehicles::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }
}