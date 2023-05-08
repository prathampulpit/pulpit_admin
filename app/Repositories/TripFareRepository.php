<?php

namespace App\Repositories;

use App\Models\TripFare;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TripFareRepository extends BaseRepository
{
    protected $tripFare;

    public function __construct(TripFare $tripFare)
    {
        parent::__construct($tripFare);
        $this->tripFare = $tripFare;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->tripFare::whereRaw('1=1');
        $query->select('trip_fare.id', 'trip_fare.gst','city.name', 'vehicle_types.name as vehicle_type', 'trip_fare.base_fare', 'trip_fare.minimum_fare', 'trip_fare.base_distance', 'trip_fare.base_distance_fare', 'trip_fare.base_time', 'trip_fare.base_time_fare', 'trip_fare.break_one_distance', 'trip_fare.break_one_distance_fare', 'trip_fare.break_one_time', 'trip_fare.break_one_time_fare', 'trip_fare.break_two_distance', 'trip_fare.break_two_distance_fare', 'trip_fare.break_two_time', 'trip_fare.break_two_time_fare', 'trip_fare.waiting_time', 'trip_fare.waiting_time_fare', 'trip_fare.price_surge', 'trip_fare.city_id', 'trip_fare.vehicle_type_id', 'polygon_records.area_name', 'trip_fare.polygon_record_id');
        $query->leftJoin('city', 'trip_fare.city_id', '=', 'city.id');
        $query->leftJoin('vehicle_types', 'trip_fare.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('polygon_records', 'trip_fare.polygon_record_id', '=', 'polygon_records.id');

        // conditions
        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('trip_fare.id', $params['id']);
        }

        if (isset($params['title'])) {
            $query->where('trip_fare.title', $params['title']);
        }

        if (isset($params['user_id'])) {
            $query->where('trip_fare.user_id', $params['user_id']);
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
        $orderBy = request('order_by', 'cab_post.id');
        $order = request('order', 'desc');

        $query = $this->tripFare::whereRaw('1=1');
        $query->select('trip_fare.id','trip_fare.gst', 'city.name', 'vehicle_types.name as vehicle_type', 'trip_fare.base_fare', 'trip_fare.minimum_fare', 'trip_fare.base_distance', 'trip_fare.base_distance_fare', 'trip_fare.base_time', 'trip_fare.base_time_fare', 'trip_fare.break_one_distance', 'trip_fare.break_one_distance_fare', 'trip_fare.break_one_time', 'trip_fare.break_one_time_fare', 'trip_fare.break_two_distance', 'trip_fare.break_two_distance_fare', 'trip_fare.break_two_time', 'trip_fare.break_two_time_fare', 'trip_fare.waiting_time', 'trip_fare.waiting_time_fare', 'trip_fare.price_surge', 'trip_fare.city_id', 'trip_fare.vehicle_type_id', 'polygon_records.area_name');
        $query->leftJoin('city', 'trip_fare.city_id', '=', 'city.id');
        $query->leftJoin('vehicle_types', 'trip_fare.vehicle_type_id', '=', 'vehicle_types.id');
        $query->leftJoin('polygon_records', 'trip_fare.polygon_record_id', '=', 'polygon_records.id');

        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('trip_fare.id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("trip_fare.base_fare like " . "'" . $search . "' OR trip_fare.minimum_fare like " . "'" . $search . "' OR polygon_records.area_name like " . "'" . $search . "'");
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