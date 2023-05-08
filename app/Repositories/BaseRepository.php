<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Log;

abstract class BaseRepository {

    protected $entity;

    public function __construct($entity) {
        $this->entity = $entity;
    }

    public function getByParams($params) {
        $entity = $this->entity::whereRaw('1=1');

        // conditions
        if (isset($params['status'])) {
            $entity->where('status', $params['status']);
        }

        // order by
        if (isset($params['order_by']) && isset($params['order'])) {
            $entity->orderBy($params['order_by'], $params['order']);
        }

        // paginate
        if (isset($params['limit'])) {
            $records = $entity->paginate($params['limit']);
        } else {
            $records = $entity->get();
        }

        return $records;
    }

    public function getOneById($id) {
        $entity = $this->entity::whereRaw('1=1');
        $entity->where('id', $id);
        return $entity->first();
    }

    public function getOneBySlug($slug) {
        $entity = $this->entity::whereRaw('1=1');
        $entity->where('slug', $slug);
        return $entity->first();
    }

    public function save($params) {

        try {
            if (isset($params['id'])) {
                $entity = $this->entity->where('id', $params['id'])->first(); 
                $entity->update($params);
                 return $entity;
            } else {
                $entity = $this->entity->create($params);
               
                return $entity;
            }
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $this->entity::destroy($id);
            return true;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            return false;
        }
    }

    public function changeStatus($entity) {
        try {
            if ($entity->status == 1) {
                $entity->status = 0;
            } else {
                $entity->status = 1;
            }
            $entity->save();
            return true;
        } catch (\Exception $e) {
            Log::info($e->message());
            return false;
        }
    }

    public function featuredStatus($entity) {
        try {

            if ($entity->is_featured == 1) {
                $entity->is_featured = 0;
            } else {
                $entity->is_featured = 1;
            }

            $entity->save();
            return true;
        } catch (\Exception $e) {
            Log::info($e->message());
            return false;
        }
    }

    public function profileStatus($entitys) {

        $params = array();

        if ($entitys->profile_picture_status == 'approved') {
            $params['profile_picture_status'] = 'pending';
        } else {
            $params['profile_picture_status'] = 'approved';
        }

        $entity = $this->entity->where('user_id', $entitys['id'])->first();
        $entity->update($params);

        return true;
    }

}
