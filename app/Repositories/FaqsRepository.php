<?php

namespace App\Repositories;

use App\Models\Faqs;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use DB;

class FaqsRepository extends BaseRepository
{
    protected $faqs;

    public function __construct(Faqs $faqs)
    {
        parent::__construct($faqs);
        $this->faqs = $faqs;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $query = $this->faqs::whereRaw('1=1');
        $query->select('faqs.id', 'faqs.question', 'faqs.answer', 'faqs.datetime');
        //$query->leftJoin('vehicles', 'cab_post.vehicle_id', '=', 'vehicles.id');

        // conditions
        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('faqs.id', $params['id']);
        }

        if (isset($params['question'])) {
            $query->where('faqs.question', $params['question']);
        }

        if (isset($params['answer'])) {
            $query->where('answer', $params['answer']);
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

        $query = $this->faqs::whereRaw('1=1');
        $query->select('faqs.id', 'faqs.question', 'faqs.answer', 'faqs.datetime');

        //$query->where('status', '!=', '2');
        if (isset($params['id'])) {
            $query->where('id', $params['id']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("question like " . "'" . $search . "' OR answer like " . "'" . $search . "'");
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
        DB::table('faq_media')->where('faq_id', $id)->delete();
        $user = $this->faqs::where('id', $id)->delete();
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