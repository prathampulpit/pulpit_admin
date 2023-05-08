<?php

namespace App\Repositories;

use App\Models\Parivarvahan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ParivarvahanRepository extends BaseRepository
{
    protected $parivarvahan;

    public function __construct(Parivarvahan $parivarvahan)
    {
        parent::__construct($parivarvahan);
        $this->parivarvahan = $parivarvahan;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->parivarvahan::whereRaw('1=1');
        $users->select('parivarvahan.id', 'parivarvahan.vehicle_id', 'parivarvahan.from_state', 'parivarvahan.to_state', 'parivarvahan.start_date', 'parivarvahan.end_date', 'parivarvahan.mobile_number', 'parivarvahan.tax_amount', 'parivarvahan.status', 'parivarvahan.doc_url', 'parivarvahan.user_id', 'users.first_name', 'users.last_name', 'vehicles.vehicle_number');
        $users->leftJoin('users', 'parivarvahan.user_id', '=', 'users.id');
        $users->leftJoin('vehicles', 'parivarvahan.vehicle_id', '=', 'vehicles.id');

        // conditions
        $users->where('parivarvahan.status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('parivarvahan.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('name', $params['name']);
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

        $query = $this->parivarvahan::whereRaw('1=1');
        $query->select('parivarvahan.id', 'parivarvahan.vehicle_id', 'parivarvahan.from_state', 'parivarvahan.to_state', 'parivarvahan.start_date', 'parivarvahan.end_date', 'parivarvahan.mobile_number', 'parivarvahan.tax_amount', 'parivarvahan.status', 'parivarvahan.doc_url', 'parivarvahan.user_id', 'users.first_name', 'users.last_name', 'vehicles.vehicle_number', 'parivarvahan.file_type', 'users.mobile_number as user_mobile_number');
        $query->leftJoin('users', 'parivarvahan.user_id', '=', 'users.id');
        $query->leftJoin('vehicles', 'parivarvahan.vehicle_id', '=', 'vehicles.id');

        //$query->where('status', '!=', '2');
        if (isset($params['name'])) {
            $query->where('name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "' OR parivarvahan.from_state like " . "'" . $search . "' OR parivarvahan.to_state like " . "'" . $search . "' OR parivarvahan.mobile_number like " . "'" . $search . "' OR vehicles.vehicle_number like " . "'" . $search . "'");
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
        $user = $this->parivarvahan::where('id', $id)->first();
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