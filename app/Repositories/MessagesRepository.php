<?php

namespace App\Repositories;

use App\Models\Messages;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MessagesRepository extends BaseRepository {

    protected $messages;

    public function __construct(Messages $messages) {
        parent::__construct($messages);
        $this->messages = $messages;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params) {
        $query = $this->messages::whereRaw('1=1');
        $query->select('messages.*');
//        $query->leftJoin('vehicle_types', 'messages.vehicle_type_id', '=', 'vehicle_types.id');

        if (isset($params['id'])) {
            $query->where('messages.id', $params['id']);
        }

        if (isset($params['message'])) {
            $query->where('messages.message', $params['message']);
        }
        if (isset($params['city1'])) {
            $query->where('messages.city1', $params['city1']);
        }
        if (isset($params['city2'])) {
            $query->where('messages.city2', $params['city2']);
        }
        if (isset($params['group_name'])) {
            $query->where('messages.group_name', $params['group_name']);
        }
        if (isset($params['phone'])) {
            $query->where('messages.phone', $params['phone']);
        }
        if (isset($params['price'])) {
            $query->where('messages.price', $params['price']);
        }

        if (isset($params['user_id'])) {
            $query->where('messages.group_ID', $params['group_ID']);
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

    public function getPanelUsers($request, $params) {
        if (request('per_page') == 'all') {
            $usersCount = [];
            $usersCount['count'] = true;
            $perPage = $this->getByParams($usersCount);
        } else {
            $perPage = request('per_page', config('custom.db.per_page'));
        }
        $orderBy = request('order_by', 'cab_post.id');
        $order = request('order', 'desc');

        $query = $this->messages::whereRaw('1=1');
        $query->select('messages.*', DB::raw('DATE_FORMAT(messages.created_at, "%d-%b-%Y") as created_at'));
//        $query->leftJoin('vehicle_types', 'messages.vehicle_type_id', '=', 'vehicle_types.id');
        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('messages.id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("messages.message like " . "'" . $search . "' OR "
                        . "messages.group_name like " . "'" . $search . "' OR "
                        . "messages.group_ID like " . "'" . $search . "' OR "
                        . "messages.phone like " . "'" . $search . "' OR "
                        . "messages.city1 like " . "'" . $search . "' OR "
                        . "messages.city2 like " . "'" . $search . "' OR "
                        . "messages.price like " . "'" . $search . "'");
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

}
