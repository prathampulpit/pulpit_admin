<?php

namespace App\Repositories;

use App\Models\PolygonRecords;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PolygonRecordsRepository extends BaseRepository
{
    protected $polygonRecords;

    public function __construct(PolygonRecords $polygonRecords)
    {
        parent::__construct($polygonRecords);
        $this->polygonRecords = $polygonRecords;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->polygonRecords::whereRaw('1=1');
        $users->select('polygon_records.id', 'polygon_records.area_name', 'polygon_records.coordinates', 'polygon_records.service', 'polygon_records.status', 'polygon_records.city_id', 'polygon_records.circle_radius', 'city.name');
        $users->leftJoin('city', 'polygon_records.city_id', '=', 'city.id');

        // conditions
        $users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('polygon_records.id', $params['id']);
        }

        if (isset($params['name'])) {
            $users->where('polygon_records.name', $params['name']);
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

        $query = $this->polygonRecords::whereRaw('1=1');
        $query->select('polygon_records.id', 'polygon_records.area_name', 'polygon_records.coordinates', 'polygon_records.circle_radius', 'polygon_records.service', 'polygon_records.status', 'polygon_records.city_id', 'city.name');
        $query->leftJoin('city', 'polygon_records.city_id', '=', 'city.id');
        $query->where('status', '!=', '2');

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("coordinates like " . "'" . $search . "' OR service like " . "'" . $search . "' OR area_name like " . "'" . $search . "'");
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
        $user = $this->polygonRecords::where('id', $id)->first();
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

    public function toggleStatus($id)
    {
        $user = $this->polygonRecords::where('id', $id)->first();
        $newStatus = '1';
        if ($user->status == '1') {
            $newStatus = '0';
        }

        $user->status = $newStatus;
        $user->save();
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