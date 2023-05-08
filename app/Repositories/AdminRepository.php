<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminRepository extends BaseRepository
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    /**
     * Method to get users with pagination. Additional conditions can be added to filter
     * @var $params
     * @return Collection
     */
    public function getByParams($params)
    {
        $users = $this->user::whereRaw('1=1');
        $users->select(DB::raw("CONVERT(users.id, CHAR) as user_id"), 'users.id', 'users.first_name', 'users.last_name', 'users.mobile_number', 'users.emailid', 'users.role_id');
        $users->where('is_deleted',0);
        // conditions
        $users->where('type', 'super_admin');
        if (isset($params['user_id'])) {
            $users->where('id', $params['user_id']);
        }

        if (isset($params['mobile_number'])) {
            $users->where('mobile_number', $params['mobile_number']);
        }

        if (isset($params['user'])) {
            $users->where('user.type', $params['user']);
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

        $query = $this->user::whereRaw('1=1');
        $query->select(DB::raw("CONVERT(users.id, CHAR) as user_id"), 'users.id', 'users.first_name', 'users.last_name', 'users.mobile_number', 'users.emailid', 'users.role_id');
 //       $query->where('type', '=', 'super_admin');
        $query->where('id', '!=', '1');
        $query->where('is_deleted', 0);


        if (isset($params['user_status'])) {
            $query->where('user_status', $params['user_status']);
        }

        // search
        if ($request->get('search')) {
            $search = '%' . $request->get('search') . '%';
            $query->where(function ($query) use ($search) {
                $query->whereRaw("first_name like " . "'" . $search . "' OR last_name like " . "'" . $search . "'");
                $query->Orwhere('emailid', 'like', $search);
                $query->Orwhere('mobile_number', 'like', $search);
            });
        }

        $query->orderBy($orderBy, $order);
        $records = $query->paginate($perPage);
        //echo $records = $query->toSql(); exit;
        return $records;
    }

    /**
     * Method to get a user instance with all details
     * @var $id
     * @return User
     */
    public function getOneById($id)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('users.id', $id);
        $users->where('is_deleted', 0);
        $users->select('users.email', 'users.status', 'users.id', 'users.id as uid', 'user_profiles.id as pid', 'user_profiles.user_id', 'user_profiles.country_id', 'user_profiles.language_id', 'user_profiles.first_name', 'user_profiles.last_name', 'user_profiles.dob', 'user_profiles.gender', 'user_profiles.phone', 'user_profiles.description', 'user_profiles.city', 'user_profiles.state', 'user_profiles.address', 'user_profiles.zipcode', 'user_profiles.profile_picture', 'user_profiles.profile_picture_status', 'user_profiles.twitter_link', 'user_profiles.custom_profile_link');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }

    /**
     * Method to get a user instance with all details
     * @var $email
     * @return User
     */
    public function getOneByEmail($email)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('is_deleted', 0);
        $users->where('users.email', $email);
        $users->select('users.id');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }

    public function getAllByEmail($email)
    {
        $users = $this->user::whereRaw('1=1');
        $users->where('is_deleted', 0);
        $users->where('users.email', $email);
        $users->select('users.*');
        $users->leftJoin('user_profiles', 'users.id', '=', 'user_profiles.user_id');
        return $users->first();
    }


}