<?php

namespace App\Http\Controllers\Parivarvahan;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Parivarvahan;
use App\Models\Roles;
use App\Models\User;
use App\Models\PortalActivities;
use App\Repositories\ParivarvahanRepository;
use App\Repositories\NotificationsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class ParivarvahanController extends Controller
{
    protected $parivarvahanRepository;
    protected $notificationsRepository;

    public function __construct(
        ParivarvahanRepository $parivarvahanRepository,
        NotificationsRepository $notificationsRepository
    ) {
        $this->parivarvahanRepository = $parivarvahanRepository;
        $this->notificationsRepository = $notificationsRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if (in_array("5", $role_id_arr) || $user_role == 'administrator') {
            return view('admin.modules.parivarvahan.index');
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
            $total = $this->parivarvahanRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->parivarvahanRepository->getPanelUsers($request, $params);
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
                $data = $this->parivarvahanRepository->getByParams($params);
            }

            return view('admin.modules.parivarvahan.store', [
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

        $tax_amount = $request->get('tax_amount');
        $user = array();
        $user['id'] = $request->get('id', null);
        if (!empty($tax_amount)) {
            $user['tax_amount'] = $request->get('tax_amount');
            $user['status'] = '1';
        }

        $user['updated_at'] = date("Y-m-d H:i:s");
        if ($request->has('document_upload')) {
            $file = $request->file('document_upload');
            $document_file_name = rand('11111111', '999999999') . time() . $file->getClientOriginalName();
            $filePath = '' . $document_file_name;
            Storage::disk('s3')->put($filePath, file_get_contents($file));
            $fullpath = env('S3_BUCKET_URL') . $filePath;
            $user['doc_url'] = $document_file_name;
            $user['status'] = '3';

            $temp = explode('.', $document_file_name);
            $extension = end($temp);
            if ($extension == 'pdf' || $extension == 'PDF') {
                $user['file_type'] = '2';
            } else {
                $user['file_type'] = '1';
            }
        }
        $this->parivarvahanRepository->save($user);

        if (!empty($request->get('id'))) {
            if (!empty($tax_amount)) {

                //BORDER_TAX

                $params['id'] = $request->get('id');
                $params['response_type'] = "single";
                $getRecord = $this->parivarvahanRepository->getByParams($params);
                //echo "<pre>"; print_r($getRecord); exit;

                $user = User::find($getRecord['user_id']);
                if (!empty($user)) {
                    $device_token = $user->device_token;
                    $device_type = $user->device_type;

                    $notificationText = "Your border tax service is accepted. You can now pay tax amount via our application.";
                    if (!empty($device_token)) {
                        $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', "BORDER_TAX", $user['title'], "");
                    }
                }

                $message = 'Tax Amount updated successfully';
            } else {
                $message = 'Document updated successfully';
            }
        } else {
            $message = 'Tax Amount Added Successfully';
        }

        return redirect(route('admin.parivarvahan.index', ['panel' => Session::get('panel')]))->withMessage($message);
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
        $user = $this->parivarvahanRepository->getByParams($params);

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
        $result = $this->parivarvahanRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->parivarvahanRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->parivarvahanRepository->updateStatus($id);
        return (int) $result;
    }
}
