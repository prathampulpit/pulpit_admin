<?php

namespace App\Repositories;

use App\Models\CmsPages;
use Illuminate\Support\Facades\DB;

class CmsPagesRepository extends BaseRepository
{
    protected $cmsPages;

    public function __construct(CmsPages $cmsPages)
    {
        parent::__construct($cmsPages);
        $this->cmsPages = $cmsPages;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->cmsPages::whereRaw('1=1');
        //$query->select('cms_pages.id');
        // conditions
        if (isset($params['id'])) {
            $query->where('id', $params['id']);
        }
        if (isset($params['slug'])) {
            $query->where('slug', $params['slug']);
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
            $records = $query->paginate($params['limit']);
        } else {
            $records = $query->get();
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

        $query = $this->cmsPages::whereRaw('1=1');
        $query->select("*");

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("page_name like " . "'" . $search . "'");
            });
        }

        $query->orderBy($orderBy, $order);
        $records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }
}