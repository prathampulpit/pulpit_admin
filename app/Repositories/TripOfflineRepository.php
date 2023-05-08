<?php

namespace App\Repositories;

use App\Models\SubscriptionUsedCoupon;
use App\Models\TripBookingsOffline;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TripOfflineRepository extends BaseRepository {

    protected $TripBookingsOffline;

    public function __construct(TripBookingsOffline $TripBookingsOffline) {
        parent::__construct($TripBookingsOffline);
        $this->TripBookingsOffline = $TripBookingsOffline;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params) {
        $users = $this->TripBookingsOffline::whereRaw('1=1');
        $users->select('trip_bookings_offline.*');
        // $users->leftJoin('users', 'subscription_used_coupon.user_id', '=', 'users.id');
        // $users->leftJoin('subscription_coupons', 'subscription_used_coupon.subscription_coupon_id', '=', 'subscription_coupons.id'); 
        // conditions
        //$users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('id', $params['id']);
        }

        if (isset($params['first_name'])) {
            $users->where('trip_bookings_offline.first_name', $params['first_name']);
        }
//        if (isset($params['response_type']) && $params['response_type'] == "single") {
//            $records = $users->first();
//            return $records;
//        }
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

    public function getPanelUsers($request, $params) {
         
        $trip_type = $params['trip_type'];
        if (request('per_page') == 'all') {
            $usersCount = [];
            $usersCount['count'] = true;
            $perPage = $this->getByParams($usersCount);
        } else {
            $perPage = request('per_page', config('custom.db.per_page'));
        }
        $orderBy = request('order_by', 'id');
        $order = request('order', 'desc');

        $query = $this->TripBookingsOffline::whereRaw('1=1');
        $query->select('trip_bookings_offline.*');
        if (isset($params['first_name'])) {
            $query->where('trip_bookings_offline.first_name', $params['name']);
        }
        $query->where('trip_bookings_offline.trip_type', $trip_type);

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
//                $query->whereRaw("trip_bookings_offline.first_name like " . "'" . $search . "' OR trip_bookings_offline.last_name like " . "'" . $search . "' OR subscription_coupons.coupon_code like " . "'" . $search . "' OR subscription_coupons.no_of_days = " . "'" . $search . "'");
                $query->whereRaw("trip_bookings_offline.first_name like " . "'" . $search . "' OR trip_bookings_offline.last_name like " . "'" . $search . "' OR trip_bookings_offline.pickup_address like " . "'" . $search . "' OR trip_bookings_offline.pickup_latitude = " . "'" . $search . "'");
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

    public function updateStatus($id) {
        $user = $this->TripBookingsOffline::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }

    public function upload($file, $uploadPath) {
        $name = $this->getName($file);
        $path = $uploadPath . '/' . $name;

        $disk = $this->getDisk();
        Storage::disk($disk)->put($path, file_get_contents($file));

        return $name;
    }

    private function getName($file) {
        return Str::slug(preg_replace('/\s+/', '_', time())) . '-' . time() . '.' . $file->getClientOriginalExtension();
    }

    private function getDisk() {
        return config('custom.upload.disk', 'local');
    }

}
