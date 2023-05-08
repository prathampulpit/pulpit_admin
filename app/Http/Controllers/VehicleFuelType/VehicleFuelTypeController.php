<?php

namespace App\Http\Controllers\VehicleFuelType;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use App\Models\VehicleFuelType;
use App\Models\Roles;
use App\Models\PortalActivities;
use App\Repositories\VehicleFuelTypeRepository;
use App\Repositories\VehicleBrandModelsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Password;
use Session;
use URL;

class VehicleFuelTypeController extends Controller
{
    protected $vehicleFuelTypeRepository;
    protected $vehicleBrandModelsRepository;

    public function __construct(
        VehicleFuelTypeRepository $vehicleFuelTypeRepository,
        VehicleBrandModelsRepository $vehicleBrandModelsRepository
    ) {
        $this->vehicleFuelTypeRepository = $vehicleFuelTypeRepository;
        $this->vehicleBrandModelsRepository = $vehicleBrandModelsRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.vehicleFuelType.index');
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
            $total = $this->vehicleFuelTypeRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->vehicleFuelTypeRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->vehicleFuelTypeRepository->getByParams($params);
            }

            $params1['not_status'] = '2';
            $vehicleBrands = $this->vehicleBrandModelsRepository->getByParams($params1);

            return view('admin.modules.vehicleFuelType.store', [
                'data' => $data,
                'id' => $id,
                'user_role' => $user_role,
                'vehicleBrands' => $vehicleBrands
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

        /* $user = array();
        $user['id'] = $request->get('id', null);
        $user['name'] = $request->get('name');
        //$user['model_id'] = $request->get('model_id');
        $user['updated_at'] = date("Y-m-d H:i:s");
        if (empty($request->get('id'))) {
            $user['created_at'] = date("Y-m-d H:i:s");
        }
        $this->vehicleFuelTypeRepository->save($user); */

        if (empty($request->get('id'))) {
            $record = new VehicleFuelType();
            $record->name = $request->get('name');
            $record->created_at = date("Y-m-d H:i:s");
            $record->updated_at = date("Y-m-d H:i:s");
            $record->save();
        } else {
            $id = $request->get('id');
            $record = VehicleFuelType::find($id);
            $record->name = $request->get('name');
            $record->updated_at = date("Y-m-d H:i:s");
            $record->save();
        }

        if (!empty($request->get('id'))) {
            $message = 'User Type Updated Successfully';
        } else {
            $message = 'User Type Added Successfully';
        }

        $log_update = new PortalActivities();
        $log_update->response_data = $message;
        $log_update->save();

        return redirect(route('admin.vehicleFuelType.index', ['panel' => Session::get('panel')]))->withMessage($message);
    }

    public function show($panel, $id)
    {
        $user = Auth::user();
        $role_id = $user['role_id'];

        $role = Roles::find($role_id);
        $this->user_role = $role['slug'];

        $params = [];
        $params['user_id'] = $id;
        $params['response_type'] = "single";
        $user = $this->vehicleFuelTypeRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.user.show', [
            'user' => $user, 'user_role' => $this->user_role
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
        $result = $this->vehicleFuelTypeRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->vehicleFuelTypeRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->vehicleFuelTypeRepository->updateStatus($id);
        return (int) $result;
    }
}
