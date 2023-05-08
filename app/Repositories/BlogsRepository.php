<?php

namespace App\Repositories;

use App\Models\Blogs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class BlogsRepository extends BaseRepository
{
    protected $blogs;
 
    public function __construct(Blogs $blogs)
    {
        parent::__construct($blogs);
        $this->blogs = $blogs;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->blogs::whereRaw('1=1');
        $query->select('blogs.*');
//        $query->leftJoin('vehicle_types', 'blogs.vehicle_type_id', '=', 'vehicle_types.id');
       
        if (isset($params['id'])) {
            $query->where('blogs.id', $params['id']);
        }

        if (isset($params['title'])) {
            $query->where('blogs.title', $params['title']);
        }
       if (isset($params['title'])) {
            $query->where('blogs.description', $params['description']);
        }

        if (isset($params['user_id'])) {
            $query->where('blogs.user_id', $params['user_id']);
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

        $query = $this->blogs::whereRaw('1=1');
        $query->select('blogs.*');
//        $query->leftJoin('vehicle_types', 'blogs.vehicle_type_id', '=', 'vehicle_types.id');

        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('blogs.id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("blogs.description like " . "'" . $search . "' OR blogs.title like " . "'" . $search . "' OR blogs.meta_description like " . "'" . $search . "' OR blogs.meta_authors like " . "'" . $search . "'");
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