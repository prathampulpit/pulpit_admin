<?php

namespace App\Repositories;

use App\Models\OfflineCustomer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class offlineCustomerRepository extends BaseRepository
{
    protected $offlineCustomer;

    public function __construct(offlineCustomer $offlineCustomer)
    {
        parent::__construct($offlineCustomer);
        $this->offlineCustomer = $offlineCustomer;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->offlineCustomer::whereRaw('1=1');
        $users->select('id', 'name', 'address_line1', 'address_line2', 'mobile_number');

        // conditions
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

        $query = $this->offlineCustomer::whereRaw('1=1');
        $query->select('id', 'name', 'address_line1', 'address_line2', 'mobile_number');

        if (isset($params['name'])) {
            $query->where('name', $params['name']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("name like " . "'" . $search . "' OR address_line1 like " . "'" . $search . "' OR address_line2 like " . "'" . $search . "' OR mobile_number like " . "'" . $search . "'");
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
        $user = $this->offlineCustomer::where('id', $id)->first();
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