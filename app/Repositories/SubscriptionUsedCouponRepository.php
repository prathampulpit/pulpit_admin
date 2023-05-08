<?php

namespace App\Repositories;

use App\Models\SubscriptionUsedCoupon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubscriptionUsedCouponRepository extends BaseRepository
{
    protected $subscriptionUsedCoupon;

    public function __construct(SubscriptionUsedCoupon $subscriptionUsedCoupon)
    {
        parent::__construct($subscriptionUsedCoupon);
        $this->subscriptionUsedCoupon = $subscriptionUsedCoupon;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->subscriptionUsedCoupon::whereRaw('1=1');
        $users->select('subscription_used_coupon.id', 'subscription_coupons.coupon_code', 'subscription_coupons.no_of_days');
        $users->leftJoin('users', 'subscription_used_coupon.user_id', '=', 'users.id');
        $users->leftJoin('subscription_coupons', 'subscription_used_coupon.subscription_coupon_id', '=', 'subscription_coupons.id');

        // conditions
        //$users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('id', $params['id']);
        }

        if (isset($params['coupon_code'])) {
            $users->where('subscription_coupons.coupon_code', $params['coupon_code']);
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

        $query = $this->subscriptionUsedCoupon::whereRaw('1=1');
        $query->select('subscription_used_coupon.id', 'users.first_name', 'users.last_name', 'subscription_coupons.coupon_code', 'subscription_coupons.no_of_days', 'subscription_plans.name as subscription_plan_name');
        $query->leftJoin('users', 'subscription_used_coupon.user_id', '=', 'users.id');
        $query->leftJoin('subscription_coupons', 'subscription_used_coupon.subscription_coupon_id', '=', 'subscription_coupons.id');
        $query->leftJoin('user_purchased_plans', 'subscription_used_coupon.user_purchase_plan_id', '=', 'user_purchased_plans.id');
        $query->leftJoin('subscription_plans', 'user_purchased_plans.subscription_plan_id', '=', 'subscription_plans.id');

        //$query->where('status', '!=', '2');
        if (isset($params['coupon_code'])) {
            $query->where('subscription_coupons.coupon_code', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("users.first_name like " . "'" . $search . "' OR users.last_name like " . "'" . $search . "' OR subscription_coupons.coupon_code like " . "'" . $search . "' OR subscription_coupons.no_of_days = " . "'" . $search . "'");
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
        $user = $this->subscriptionUsedCoupon::where('id', $id)->first();
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