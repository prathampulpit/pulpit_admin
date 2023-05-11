<?php

namespace App\Http\Controllers\DeleteUser;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\AgentUsers;
use App\Models\User;
use App\Models\LinkCards;
use App\Models\Cities;
use App\Models\Drivers;
use App\Models\Roles;
use App\Models\UserWorkProfile;
//use App\Repositories\Upload\UploadRepository;
use App\Repositories\AdminRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Password;
use Session;
use DB;

class DeleteUserController extends Controller
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
        $users = User::where([['status', 1],['type','user'],['is_deleted',0]])->get();

        return view('admin.modules.deleteuser.store', [
            'data' => $user,
            'users' => $users,
            'id' => $id
        ]);
    }

    public function store(Request $request)
    {
        $mobile_number = $request->get('mobile_number');
        if (!empty($mobile_number)) {
            //$r = DB::statement('call delete_agent_procedure()');
            $r = DB::select("CALL delete_agent_procedure('" . $mobile_number . "')");
            //dd($r); print_r($r); exit;
        }

        $message = 'User Deleted Successfully';
        return redirect(route('admin.deleteuser.create', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $params['user'] = "user";
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

    public function destroy($panel, Request $request)
    {
        $id = $request->id;
        $user = Auth::user();
        $user_id = $user['id'];
  
        if ($user->type != 'super_admin') {
            abort(403);
        }
        $data = UserWorkProfile::where('user_id',$id)->first();
        UserWorkProfile::where('user_id',$id)->update([
            'status' => 0
        ]);
        if(isset($data)){
           if($data->user_type_id == 2){
                AgentUsers::where('id',$data->profile_id)->update([
                    'is_deleted' => 1
                ]);
            }elseif($data->user_type_id == 3){
                AgentUsers::where('id',$data->profile_id)->update([
                    'is_deleted' => 1
                ]);
            }elseif($data->user_type_id == 4){
                Drivers::where('id',$data->profile_id)->update([
                    'is_deleted' => 1
                ]);
            } 
        }
        
        $result = User::where('id',$id)->update([
            'user_status' => 0,
            'reference_code' => null,
            'profile_pic' => null,
            'profile_pic_status' => 1,
            'device_type' => null,
            'device_token' => null,
            'profile_completion_step' => 0,
            'status' => 0,
            'user_type_id' => null,
            'last_updated_id' => $user_id,
            'is_approved' => 0,
            'is_deleted' => 1,
        ]);
        if($result){
            $message = 'User Deleted Successfully';
            return redirect(route('admin.deleteuser.create', ['panel' => Session::get('panel')]))->withMessage($message);
        }else{
            $message = 'User Not Deleted';
            return redirect(route('admin.deleteuser.create', ['panel' => Session::get('panel')]))->withMessage($message);
        }
        
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