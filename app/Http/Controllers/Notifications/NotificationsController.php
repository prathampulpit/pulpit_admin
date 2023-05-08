<?php

namespace App\Http\Controllers\Notifications;

use App\Events\User\ProfileUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\ChangeProfileRequest;
use App\Http\Requests\User\StoreUser;
use Aws\Exception\AwsException;
use App\Models\Notifications;
use App\Models\Roles;
use App\Models\User;
use App\Models\PortalActivities;
use App\Repositories\NotificationsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Storage;
use Password;
use Session;
use URL;

class NotificationsController extends Controller
{
    protected $notificationsRepository;

    public function __construct(
        NotificationsRepository $notificationsRepository
    ) {
        $this->notificationsRepository = $notificationsRepository;
    }

    public function index()
    {
        $user = Auth::user();
        $role_id = $user['role_id'];
        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            return view('admin.modules.notifications.index');
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
            $total = $this->notificationsRepository->getByParams($countcompany);
        } else {
            $total = request('per_page', config('custom.db.per_page', 100));
        }
        $params['per_page'] = $total;
        $users = $this->notificationsRepository->getPanelUsers($request, $params);
        return $users;
    }

    public function createEdit($panel, $id = null)
    {
        $admin = Auth::user();
        $role_id = $admin['role_id'];

        $role_id_arr = explode(",", $role_id);

        $role = \App\Models\Roles::find($role_id);
        $user_role = $role['slug'];

        if ($user_role == 'administrator') {
            $params = [];

            $data = null;
            if ($id) {
                $params = [];
                $params['id'] = $id;
                $params['response_type'] = "single";
                $data = $this->notificationsRepository->getByParams($params);
            }

            $users = User::all();

            return view('admin.modules.notifications.store', [
                'data' => $data,
                'id' => $id,
                'users' => $users,
                'admin' => $admin
            ]);
        } else {
            abort(403);
        }
    }

    public function store(Request $request)
    {
        $user = array();
        $user['id'] = $request->get('id', null);
        $user['user_id'] = $request->get('user_id');
        $user['title'] = $request->get('title');
        $user['description'] = $request->get('description');
        $user['type'] = 'admin';
        $image1 = $request->file('image_name');
        if (!empty($image1)) {
            $image1_name = rand('111', '999') . time() . $image1->getClientOriginalName();
            $filePath1 = "/" . $image1_name;
            Storage::disk('s3')->put($filePath1, file_get_contents($image1));
            $image_name = env('S3_BUCKET_URL') . $filePath1;
            $user['image_name'] = $image_name;
        } else {
            $image_name = "";
        }

        if (empty($request->get('id'))) {
            $user['datetime'] = date("Y-m-d H:i:s");
        }
        
        if (!empty($request->get('id'))) {
            $message = 'Notification Updated Successfully';
        } else {
            if ($user['user_id'] != 0) {
                if ($request->audience == 2) {
                    $users = User::where([['id', $user['user_id']], ['is_approved', $request->audience]])->orWhere([['id', $user['user_id']], ['is_approved', 0]])->get();
                    foreach ($users as $v) {
                        $device_token = $v->device_token;
                        $device_type = $v->device_type;

                        $notificationText = $request->get('description');
                        $user['user_id'] = $v->id;
                        $this->notificationsRepository->save($user);
                        if (!empty($device_token)) {
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $user['title'], $image_name);
                        }
                    }
                }else{
                    $userData = User::where('id', $user['user_id'])->first();
                    if ($userData) {
                        $device_token = $userData->device_token;
                        $device_type = $userData->device_type;

                        $this->notificationsRepository->save($user);
                        $user['user_id'] = $userData->id;
                        $notificationText = $request->get('description');
                        if (!empty($device_token)) {
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $user['title'], $image_name);
                        }
                    }
                }

            } else {
                if ($request->audience == 2) {
                    $users = User::where([['role_id', 1], ['id', '!=', '1'], ['is_approved', $request->audience]])->get();
                    foreach ($users as $v) {
                        $device_token = $v->device_token;
                        $device_type = $v->device_type;
                        $notificationText = $request->get('description');
                        $user['user_id'] = $v->id;
                        $this->notificationsRepository->save($user);
                        if (!empty($device_token)) {
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $user['title'], $image_name);
                        }
                    }
                } else {
                    $users = User::where([['role_id', 1], ['id', '!=', '1']])->get();
                    foreach ($users as $v) {
                        $device_token = $v->device_token;
                        $device_type = $v->device_type;

                        $notificationText = $request->get('description');
                        $user['user_id'] = $v->id;
                        $this->notificationsRepository->save($user);
                        if (!empty($device_token)) {
                            $r = $this->notificationsRepository->sendPuchNotification($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $user['title'], $image_name);
                        }
                    }
                }
            }
            $message = 'Notification Added Successfully';
        }

        return redirect(route('admin.notifications.index', ['panel' => Session::get('panel')]))->withMessage($message);
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
        $notifications = $this->notificationsRepository->getByParams($params);

        $profile_path = config('custom.upload.user.profile');

        return view('admin.modules.notifications.show', [
            'notifications' => $notifications, 'user_role' => $this->user_role
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
        return redirect(route('admin.notifications.show', ['panel' => Session::get('panel'), 'id' => $id]))->withMessage($message);
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
        $result = $this->notificationsRepository->toggleStatus($id);
        return (int) $result;
    }

    public function toggleReferalStatus($panel, $id)
    {
        $result = $this->notificationsRepository->toggleReferalStatus($id);
        return (int) $result;
    }

    public function destroy($panel, $id)
    {
        $result = $this->notificationsRepository->updateStatus($id);
        return (int) $result;
    }
}