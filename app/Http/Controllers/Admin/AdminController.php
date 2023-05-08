<?php

namespace App\Http\Controllers\Admin;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\User;
use App\Models\LinkCards;
use App\Models\Cities;
use App\Models\Roles;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Password;
use Session;

class AdminController extends Controller
{
    protected $adminRepository;

    public function __construct(
        AdminRepository $adminRepository
    ) {
        $this->adminRepository = $adminRepository;
    }

    public function index()
    {
         
        $user = Auth::user();
        $id = $user['id'];

        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];
        if ($user_role != 'administrator') {
            abort(403);
        }

        $profile_path = config('custom.upload.user.profile');
        $file_path = env('APP_URL') . '/storage/' . $profile_path . "/";
        return view('admin.modules.admin.index', compact('file_path'));
    }

    public function index_json(Request $request)
    { 
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->adminRepository->getByParams($countcompany);
            
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->adminRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {

        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];
        if ($user_role != 'administrator') {
            abort(403);
        }

        $params = [];

        $user = null;
        if ($id) {
            $params = [];
            $params['user_id'] = $id;
            $params['response_type'] = "single";
            $user = $this->adminRepository->getByParams($params);
        }
        //$cities = Cities::where('status','1')->orderBy('population', 'desc')->get();

        $roles = Roles::where('id', '!=', '1')->get();

        return view('admin.modules.admin.store', [
            'data' => $user,
            'id' => $id,
            //'cities' => $cities,
            'roles' => $roles
        ]);
    }

    public function store(Request $request)
    {
        
       
        if (!empty($request->get('id'))) {
            $id = $request->get('id');
        } else {
            $id = '';
        }

        $email = $request->get('email');
        if (!empty($email)) {
            if (!empty($request->get('id'))) {
                $check_email = User::where('emailid', $email)->where('type', '=', 'super_admin')->where('id', '!=', $id)->first();
                if (!empty($check_email)) {
                    return redirect()->back()->with('message', trans('Email already exits!.'));
                }
            } else {
                $check_email = User::where('emailid', $email)->where('type', '=', 'super_admin')->first();
                if (!empty($check_email)) {
                    return redirect()->back()->with('message', trans('Email already exits!.'));
                }
            }
        }
        // dd($request->all());
        if (!empty($request->get('id'))) {
            $user = User::find($id);
        } else {
            $user = new User();
        }

        $role_id_arr = $request->get('role_id');
        $role_id_all = $request->get('role_all');
        $all_role = Roles::where('id','!=',1)->get();
        
        $user->first_name = $request->get('first_name');
        $user->last_name = $request->get('last_name');
        $user->mobile_number = $request->get('mobile_number');
        if (isset($role_id_arr)) {
            
            if($role_id_all == "all")
            {   
                foreach($all_role as $role)
                {
                    $user->role_id = $role->id;
                }
               
            }else{
                $role_id = implode(",", $role_id_arr);
                $user->role_id = $role_id;
            }
            
        }
        if (!empty($email)) {
            $user->emailid = $request->get('email');
        }
        if (!empty($request->get('password'))) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->type = "super_admin";
        $user->city_id = '31';
        $user->save();

        if (!empty($request->get('id'))) {
            $message = 'Admin Updated Successfully';
        } else {
            $message = 'Admin Added Successfully';
        }
        
 

        //return redirect(route('admin.admin.show', ['panel' => Session::get('panel'),'id'=>$id]))->withMessage($message);
        return redirect(route('admin.admin.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->adminRepository->getByParams($params);

        $role_id = $user->role_id;
        $role_id_arr = explode(",", $role_id);
        $roles = Roles::where('id', '!=', '1')->whereIn('id', $role_id_arr)->get();

        return view('admin.modules.admin.show', [
            'user' => $user, 'roles' => $roles
        ]);
    }

    /**
     * Change user status
     */
    public function changeStatus(Request $request)
    {
        $id = $request->get('id');
        $user = User::find($id);
        $user->user_status = $request->get('user_status');
        $user->save();

        /* return redirect()->back()->withMessage('message', 'Status change successfully!'); */

        $message = 'Status change successfully!';
        return redirect(route('admin.admin.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    public function destroy($panel, $id)
    {
        $user = Auth::user();
        $user_id = $user['id'];

        if ($user_id == '1') {
            abort(403);
        }
        $result = User::where('id', $id)->update(['is_deleted' => 1]);
        
        return (int) $result;
    }

    public function showChangePasswordForm()
    {
        return view('admin.modules.user.change_password');
    }
    public function changePassword(ChangePasswordRequest $request)
    {
        if (!(\Hash::check($request->get('current_password'), Auth::user()->password))) {
            // The passwords matches
            return redirect()->back()->with('message', trans('Incorrect current password.'));
        }

        if (strcmp($request->get('current_password'), $request->get('password')) == 0) {
            //Current password and new password are same
            return redirect()->back()->with('message', trans('Password are not match.'));
        }
        //Change Password
        $user = Auth::user();
        $user->password = bcrypt($request->get('password'));
        $user->password_changed_at = Carbon::now();
        $user->save();

        return redirect()->back()->with("message", trans("Password changed successfully !"));
    }

    public function showChangeProfileForm()
    {
        return view('admin.modules.user.change_profile');
    }
    public function changeUserProfile(ChangeProfileRequest $request)
    {
        $id = $request->get('id');

        $array['first_name'] = $request->get('first_name');
        $array['last_name'] = $request->get('last_name');
        /* $array['email'] = $request->get('email');
        $array['mobile_number'] = $request->get('mobile_number'); */

        $save = $this->userRepository->changeProfile($array, $id);
        if ($save) {
            session()->flash('message', 'Details edited successfully');
            return redirect()->back();
        }
    }

    public function uploadprofile(Request $request)
    {
        $profileUpdate = null;
        $uploadPath = config('custom.upload.user.profile');
        $base64_image = request('image');
        if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
            $data = substr($base64_image, strpos($base64_image, ',') + 1);

            $file = base64_decode($data);
            $path = $uploadPath . "/" . auth()->user()->id;

            if (!File::isDirectory($path)) {
                File::makeDirectory($path, 0777, true, true);
            }
            $fileName = $this->uploadRepository->uploadProfile($file, $path);

            if (auth()->user()->profile_image == "") {
                $profileUpdate = event(new ProfileUpdated('profilepic', 1.5));
            } else {
                $user = Auth::user();
                $user->profile_image = $fileName;
                $user->save();

                $profileUpdate = '';
            }

            return [
                'score' => $profileUpdate,
                'image' => auth()->user()->profile_image_formatted,
            ];
        }
    }
}