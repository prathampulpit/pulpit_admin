<?php

namespace App\Repositories;

use App\Models\Cabs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;

class CabsRepository extends BaseRepository
{
    protected $cabs;

    public function __construct(Cabs $cabs)
    {
        parent::__construct($cabs);
        $this->cabs = $cabs;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->cabs::whereRaw('1=1');
        $query->select('cab_post.id', 'vehicles.vehicle_number', 'users.first_name', 'users.last_name', 'cab_post.user_id', 'cab_post_type', 'start_location', 'end_location', 'vehicle_id', 'start_time', 'start_date', 'available_for', 'cab_post_mid_cities.address', 'vehicles.registration_year', 'vehicle_brands.name as brand_name', 'vehicle_brand_models.name as model_name', 'vehicle_types.name as vehicle_type_name', 'vehicle_fuel_type.name as vehicle_fuel_type_name', 'vehicles.owner_name', 'users.mobile_number');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

        $query->leftJoin('vehicle_brands', 'vehicles.brand_id', '=', 'vehicle_brands.id');
        $query->leftJoin('vehicle_brand_models', 'vehicles.model_id', '=', 'vehicle_brand_models.id');
        $query->leftJoin('vehicle_types', 'vehicles.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('vehicle_fuel_type', 'vehicles.fuel_type_id', '=', 'vehicle_fuel_type.id');

        $query->leftJoin('users', 'cab_post.user_id', '=', 'users.id');
        $query->leftJoin('cab_post_mid_cities', 'cab_post.id', '=', 'cab_post_mid_cities.cab_post_id');

        // conditions
        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('cab_post.id', $params['id']);
        }

        if (isset($params['user_id'])) {
            $query->where('cab_post.user_id', $params['user_id']);
        }

        if (isset($params['cab_post_type'])) {
            $query->where('cab_post_type', $params['cab_post_type']);
        }

        if (isset($params['vehicle_id'])) {
            $query->where('vehicle_id', $params['vehicle_id']);
        }

        if (isset($params['response_type']) && $params['response_type'] == "single") {
            $records = $query->first();
            return $records;
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $query->orderBy($params['order_by'], $params['order']);
        }

        if (isset($params['count'])) {
            $records = $query->count();
            return $records;
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->get();
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
        $orderBy = request('order_by', 'cab_post.id');
        $order = request('order', 'desc');

        $query = $this->cabs::whereRaw('1=1');
        $query->select('cab_post.id', 'vehicles.vehicle_number', 'users.first_name', 'users.last_name', 'cab_post.user_id', 'cab_post_type', 'start_location', 'end_location', 'vehicle_id', 'start_time', 'start_date', 'available_for');
        $query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');
        $query->leftJoin('users', 'cab_post.user_id', '=', 'users.id');

        //$query->where('status', '!=', '2');
        if (isset($params['user_id'])) {
            $query->where('cab_post.user_id', $params['user_id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("users.last_name like " . "'" . $search . "' OR users.first_name like " . "'" . $search . "' OR cab_post.cab_post_type like " . "'" . $search . "' OR cab_post.start_location like " . "'" . $search . "' OR cab_post.end_location like " . "'" . $search . "' OR cab_post.start_time like " . "'" . $search . "' OR cab_post.start_date like " . "'" . $search . "'");
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
        $user = $this->cabs::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }

    public function upload($file, $uploadPath)
    {
        $name = $this->getName($file);
        $path = $uploadPath . '/' . $name;

        $disk = $this->getDisk();
        Storage::disk($disk)->put($path, file_get_contents($file));

        return $name;
    }

    private function getName($file)
    {
        return Str::slug(preg_replace('/\s+/', '_', time())) . '-' . time() . '.' . $file->getClientOriginalExtension();
    }

    private function getDisk()
    {
        return  config('custom.upload.disk', 'local');
    }
}