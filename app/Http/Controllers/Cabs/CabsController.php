<?php

namespace App\Http\Controllers\Cabs;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Cabs;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Repositories\CabsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class CabsController extends Controller
{
    protected $cabsRepository;

    public function __construct(
        CabsRepository $cabsRepository
    ) {
        $this->cabsRepository = $cabsRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("7", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.cabs.index');
        } else {
            abort(403);
        }
    }

    public function index_json(Request $request)
    {
        $user = Auth::user();
        if (request('per_page') == 'all') {
            $countcompany = [];
            $countcompany['count'] = true;
            $total = $this->cabsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->cabsRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("7", $role_id_arr) || $user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->cabsRepository->getByParams($params);
            }

            return view('admin.modules.cabs.store', [
                'data' => $data,
                'id' => $id,
                'user_role' => $user_role,
                'admin' => $admin
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $admin_id = $user['id'];

        $user = array();
        $user['id'] = $request->get('id', null);
        $user['name'] = $request->get('name');
        $user['updated_at'] = date("Y-m-d H:i:s");
        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        if ($request->has('logo')) {

            /* $file = $request->file('logo');
            $uploadPath = config('custom.upload.vehicle');
            $path = $this->cabsRepository->upload($file, $uploadPath);
            $user['logo'] = $path; */

            $file = $request->file('logo');
            $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = 'images/' . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fullpath = env('S3_BUCKET_URL') . $filePath;
            $user['logo'] = $document_file_name;
        }
        $this->cabsRepository->save($user);

        if (!empty($request->get('id'))) {
            $message = 'User Type Updated Successfully';
        } else {
            $message = 'User Type Added Successfully';
        }

        $log_update = new PortalActivities();
        $log_update->response_data = $message;
        $log_update->save();

        return redirect(route('admin.cabs.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['id'] = $id;
        $params['response_type'] = "single";
        $cabs = $this->cabsRepository->getByParams($params);
         
        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.cabs.show', [
            'cabs' => $cabs, 'user_role' => $this->user_role
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

        $message = 'Status change successfully!';
        return redirect(route('admin.users.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
    }

    /**
     * Reset attempt
     */
    public function resetAttempt(Request $request)
    {
        $id = $request->get('user_id');
        $user = User::find($id);
        $user->login_attempt = $request->get('login_attempt');
        $user->save();

        $message = 'Attemp reset successfully!';
        echo "success";
    }

    /**
     * USSD Status
     */
    public function changeUssdStatus(Request $request)
    {
        $id = $request->get('user_id');
        $ussd_enable = $request->get('ussd_enable');
        $user = User::find($id);
        $user->ussd_enable = $ussd_enable;
        $user->save();

        $message = 'USSD status change successfully.';
        echo "success";
    }

    public function toggleStatus($panel, $id)
    {
        $result = $this->cabsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->cabsRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->cabsRepository->updateStatus($id);
        return (int) $result;
    }
}
