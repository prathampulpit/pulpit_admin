<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Models\ApiLogs;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use DB;

class DemoController extends BaseController
{
    protected $userRepository;

    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * Demo api 
     *
     * @return \Illuminate\Http\Response
     */
    public function demo(Request $request)
    {
        $input = $request->all();
        $deviceToken = $input['device_token'];
        $deviceType = $input['device_type'];

        $notificationText = $input['notification_text'];
        //$deviceType = 'Android';
        //$deviceToken = 'd2dbeQJQjoY:APA91bGLliV4cU_1c0IR6EVEstf4yvT138rWEyebhNEPoXfaBMpoNuDJHaAp5fjJNKAH-M57BI5SstOzXWHXTyslSdNeee8mtL8qLUhe7Og6SKAqckPH_MvpDU4rhcfdw-m7N88-PL1y';
        //$deviceToken = json_encode($deviceToken);
        $notification = $this->sendPuchNotification($deviceType, $deviceToken, $notificationText, $totalNotifications = '0', $pushMessageText = "");
        print_r($notification);
        exit;
        $created_at = date('Y-m-d', strtotime("-4 days"));
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://3.227.121.40/v1/config/agent-locations",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK NjI2YjU3Y2ItZGJkMC00ODcxLTkzOWItYzYzNjIwMTQ2NTM0",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }
        /* $apilog = ApiLogs::select()->where(DB::raw("(STR_TO_DATE(created_at,'%Y-%m-%d'))"), "<=", $created_at)->delete(); */
    }
}