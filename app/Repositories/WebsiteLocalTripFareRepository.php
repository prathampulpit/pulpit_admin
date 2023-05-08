<?php

namespace App\Repositories;

use App\Models\WebsiteLocalTripFare;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WebsiteLocalTripFareRepository extends BaseRepository
{
    protected $websiteLocalTripFare;
 
    public function __construct(WebsiteLocalTripFare $websiteLocalTripFare)
    {
        parent::__construct($websiteLocalTripFare);
        $this->websiteLocalTripFare = $websiteLocalTripFare;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    { 
        $query = $this->websiteLocalTripFare::whereRaw('1=1');
        $query->select('website_local_trip_fare.*', 'vehicle_types.name as vehicle_type');
        $query->leftJoin('vehicle_types', 'website_local_trip_fare.vehicle_type_id', '=', 'vehicle_types.id');

        if (isset($params['id'])) {
            $query->where('website_local_trip_fare.id', $params['id']);
        }

        if (isset($params['title'])) {
            $query->where('website_local_trip_fare.title', $params['title']);
        }

        if (isset($params['user_id'])) {
            $query->where('website_local_trip_fare.user_id', $params['user_id']);
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

        $query = $this->websiteLocalTripFare::whereRaw('1=1');
        $query->select('website_local_trip_fare.*','vehicle_types.name as vehicle_type');
        $query->leftJoin('vehicle_types', 'website_local_trip_fare.vehicle_type_id', '=', 'vehicle_types.id');

        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('website_local_trip_fare.id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("website_local_trip_fare.base_fare like " . "'" . $search . "' OR website_local_trip_fare.minimum_fare like " . "'" . $search . "' OR polygon_records.area_name like " . "'" . $search . "'");
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