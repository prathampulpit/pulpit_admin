<?php

namespace App\Repositories;

use App\Models\VehicleBrands;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class VehicleBrandsRepository extends BaseRepository
{
    protected $vehicleBrands;

    public function __construct(VehicleBrands $vehicleBrands)
    {
        parent::__construct($vehicleBrands);
        $this->vehicleBrands = $vehicleBrands;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->vehicleBrands::whereRaw('1=1');
        $users->select('id', 'name', 'logo');

        // conditions
        $users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('id', $params['id']);
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

        $query = $this->vehicleBrands::whereRaw('1=1');
        $query->select('id', 'name', 'logo');

        $query->where('status', '!=', '2');
        if (isset($params['name'])) {
            $query->where('name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("name like " . "'" . $search . "'");
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
        $user = $this->vehicleBrands::where('id', $id)->first();
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