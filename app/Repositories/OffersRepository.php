<?php

namespace App\Repositories;

use App\Models\Offers;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;

class OffersRepository extends BaseRepository
{
    protected $offers;

    public function __construct(Offers $offers)
    {
        parent::__construct($offers);
        $this->offers = $offers;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->offers::whereRaw('1=1');
        $users->select('id', 'offer_url', 'offer_type', 'offer_for','status');

        // conditions
        $users->where('status', '!=', '2');
        if (isset($params['id'])) {
            $users->where('id', $params['id']);
        }

        if (isset($params['offer_url'])) {
            $users->where('offer_url', $params['offer_url']);
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

        $query = $this->offers::whereRaw('1=1');
        $query->select('id', 'offer_url', 'offer_type', 'offer_for','status');

        $query->where('status', '!=', '2');
        if (isset($params['offer_url'])) {
            $query->where('offer_url', $params['offer_url']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("offer_url like " . "'" . $search . "'");
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
        $user = $this->offers::where('id', $id)->first();
        $user->status = '2';
        $user->save();
    }

    public function toggleStatus($id)
    {
        $user = $this->offers::where('id', $id)->first();
        $newStatus = '1';
        if ($user->status == '1') {
            $newStatus = '0';
        }

        $user->status = $newStatus;
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