<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController;
use GuzzleHttp\Client;
use Laravel\Passport\Passport;
use Carbon\Carbon;
use App\Models\User;
use App\Versions;
use Closure;
use App;
use DB;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    protected function redirectTo($request)
    {
        /* if ($request->expectsJson()) {            
        }else{
            $input = $request->all();
            $user_id = $input['user_id'];
            $user = User::find($user_id);
            $user->tokens->each(function ($token,$key) use ($user_id) {
                if($token['user_id'] == $user_id){
                    $token->delete();                       
                }
            });
            // echo $user->createToken('ara')->accessToken;
            $response = [
                'success' => false,
                'errorcode' => '-13',
                'message' => trans('message.access_token_expired'),
                
                'total_records' => "0",
                'current_page' => "0",
            ];
            //$response['data'] = array();
            echo json_encode($response);
            exit;            
        } */
        if (!$request->expectsJson()) {
            $panel = $request->segment(1);

            if ($panel == 'api') {
                $input = $request->all();

                if (isset($input['device_type']) && !empty($input['device_type'])) {
                    $device_type = $input['device_type'];
                } else {
                    $device_type = 'iOS';
                }
                $versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"), DB::raw("CONVERT(version, CHAR) as version"), DB::raw("CONVERT(min_version, CHAR) as min_version"), 'store_url', 'force_update', 'device', 'msg', 'created_at', 'updated_at')->where('device', '=', $device_type)->first();

                if (!isset($input['user_id'])) {
                    $response = [
                        'success' => false,
                        'errorcode' => '-11',
                        'message' => trans('message.parameters_missing'),
                        'total_records' => "0",
                        'current_page' => "0",
                    ];

                    $response['data'] = array();
                    $response['app_versions'] = $versions;
                    echo json_encode($response);
                    exit;
                }

                $user_id = $input['user_id'];
                $user = User::find($user_id);
                if (empty($user)) {
                    $response = [
                        'success' => false,
                        'errorcode' => '0',
                        'message' => trans('message.wrong_user'),
                        'total_records' => "0",
                        'current_page' => "0",
                    ];

                    $response['data'] = array();
                    $response['app_versions'] = $versions;
                    echo json_encode($response);
                    exit;
                }

                $user->tokens->each(function ($token, $key) use ($user_id) {
                    if ($token['user_id'] == $user_id) {
                        $token->delete();
                    }
                });

                // echo $user->createToken('ara')->accessToken;
                $response = [
                    'success' => false,
                    'errorcode' => '-13',
                    'message' => trans('message.access_token_expired'),
                    'total_records' => "0",
                    'current_page' => "0",
                ];

                $response['data'] = array();
                $response['app_versions'] = $versions;
                echo json_encode($response);
                exit;
            } else {
                return route('login', ['panel' => $panel]);
            }
        }
    }
}
