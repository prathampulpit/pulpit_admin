<?php

namespace App\Http\Controllers\Blogs;

use App\Repositories\BlogsRepository;
use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\TripFare;
use App\Models\Roles;
use App\Models\User;
use App\Models\Cities;
use App\Models\VehicleTypes;
use App\Models\WebsiteLocalTripFare;
use App\Models\PortalActivities;
use App\Models\PolygonRecords;
use App\Repositories\TripFareRepository;
use App\Repositories\WebsiteLocalTripFareRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class BlogsController extends Controller {

    protected $BlogsRepository;

    public function __construct(BlogsRepository $BlogsRepository) {
        $this->BlogsRepository = $BlogsRepository;
    }

    public function index($slug = null, $lang = null) {
        return view('admin.modules.blogs.index');
    }

    public function index_json(Request $request) {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->BlogsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->BlogsRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null) {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        $params = [];

        $data = null;
        if ($id) {
            $params = [];
            $params['id'] = $id;
            $params['response_type'] = "single";
            $data = $this->BlogsRepository->getByParams($params);
        }


        return view('admin.modules.blogs.store', [
            'data' => $data,
            'id' => $id,
        ]);
    }

    public function store(Request $request) {

        $title =  $request->get('title');
        $slug = str_replace("?", "", $title);
        $slug = str_replace(" ", "-", $slug);
     
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['title'] = $request->get('title');
        $user['slug'] =  $slug;
        $user['meta_description'] = $request->get('meta_description');
        $user['meta_author'] = $request->get('meta_author');
        $user['meta_keywords'] = $request->get('meta_keywords');
        $user['description'] = $request->get('description');
        $user['short_description'] = $request->get('short_description');
        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $user['updated_at'] = date("Y-m-d H:i:s");

        //  try {
        if ($request->file('image_upload')) { 
            $imageName = time() . '.' . $request->image_upload->getClientOriginalExtension();
            $request->image_upload->move(public_path('/uploads/blogs'), $imageName);
            $directory = "/uploads/blogs";
            $user['images'] = $directory."/".$imageName;
        }
        /* } catch (\Exception $e) {

          } */

        $this->BlogsRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'Trip Fare Updated Successfully';
        } else {
            $message = 'Trip Fare Added Successfully';
        }

        return redirect(route('admin.blogs.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id) {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $data = $this->BlogsRepository->getByParams($params);

        return view('admin.modules.blogs.show', [
            'data' => $data
        ]);
    }

    public function destroy($panel, $id) {
        $result = \App\Models\Blogs::where('id', $id)->delete();
        return (int) $result;
    }

}
