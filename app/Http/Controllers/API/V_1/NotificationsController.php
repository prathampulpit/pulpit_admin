<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Models\Transactions;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class NotificationsController extends BaseController
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->datetime = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * List of all notification
     * 
     * @return json array
     */
    public function notificationLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $result = Notifications::where('user_id', '=', $this->user_id)->where('status', '=', '1')->orderBy('id', 'DESC')->get();
        $response = array();
        if (!empty($result)) {
            foreach ($result as $val) {
                $d['notification_id'] = (string)$val['id'];
                $d['notification_type'] = $val['notification_type'];
                $d['notification_title'] = $val['notification_title'];
                $d['notification_text'] = $val['notification_text'];
                $response[] = $d;
            }
        }
        return $this->sendResponse('1', $response, trans('message.all_notification'));
    }

    /**
     * Remove selected notification
     * 
     * @return json array
     */
    public function removeNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $notification_id = $input['notification_id'];

        $qry = Notifications::find($notification_id);
        $qry->status = '2';
        $qry->save();
        return $this->sendResponse('1', array(), trans('message.remove_notification'));
    }

    /**
     * Remove all notification
     * 
     * @return json array
     */
    public function removeAllNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $data['status'] = '2';
        DB::table('notifications')->where('user_id', $this->user_id)->update($data);
        return $this->sendResponse('1', array(), trans('message.remove_all_notification'));
    }
}