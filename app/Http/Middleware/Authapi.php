<?php

namespace App\Http\Middleware;

use Closure;
use \App\Models\User;
use \App\Devices;
use \App\Versions;
use App;
use DB;

class Authapi
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $action = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $action);
        $action = $method;

        $input = $request->all();

        if (isset($input['language_code']) && !empty($input['language_code'])) {
            $language_code = $input['language_code'];
        } else {
            $language_code = 'en';
        }
        App::setLocale($language_code);

        if (isset($input['device_type']) && !empty($input['device_type'])) {
            $device_type = $input['device_type'];
        } else {
            $device_type = 'iOS';
        }

        $versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"), DB::raw("CONVERT(version, CHAR) as version"), DB::raw("CONVERT(min_version, CHAR) as min_version"), 'store_url', 'force_update', 'device', 'msg', 'created_at', 'updated_at')->where('device', '=', $device_type)->first();

        if ($action == 'register') {
            if (isset($input['mobile_number'])) {
                $mobile_number = $input['mobile_number'];

                $user = User::select('id', 'is_profile_complete')->where('mobile_number', $mobile_number)->first();
                if (!empty($user)) {
                    $user_id = $user['id'];
                    $is_profile_complete = $user['is_profile_complete'];

                    $device_type = $input['device_type'];
                    if ($device_type == 'Android') {
                        $device_type = 1;
                    } else {
                        $device_type = 2;
                    }
                    $device_token = $input['device_token'];
                    $int_udid = $input['int_udid'];

                    /* $users = Devices::select('id')->where( array('user_id'=>$user_id, 'int_udid'=>$int_udid))->first(); */
                    $devices = Devices::select('id', 'int_udid')->where(array('user_id' => $user_id))->first();

                    if (empty($devices)) {
                        $device = new Devices();
                        $device->user_id = $user_id;
                        $device->device_token = $device_token;
                        $device->int_udid = $int_udid;
                        $device->device_type = $device_type;
                        $device->created_at = date("Y-m-d H:i:s");
                        $device->updated_at = date("Y-m-d H:i:s");
                        $device->save();
                    } else {
                        $id = $devices->id;
                        $is_login = $devices->is_login;
                        $login_int_udid = $devices->int_udid;
                        if ($is_profile_complete == '1') {
                            if ($login_int_udid !=  $int_udid) {

                                /* Send Otp */
                                /* $language_code = 'en';
                                if(isset($_POST['language_code']) && !empty($_POST['language_code'])){
                                    $language_code = $_POST['language_code'];
                                }

                                if(isset($_POST['country_code'])){
                                    $country_code = $_POST['country_code'];
                                    if($country_code == 'TZS'){
                                        $country_code = "255";
                                    }
                                }else{
                                    $country_code = '255';
                                }

                                $external_id = rand(1000,9999).time().rand(1000,9999);
                                $payment_reference = rand(1000,9999).time().rand(1000,9999);
                                $param['externalId'] = $external_id;
                                $param['msisdn'] = $country_code.$mobile_number;
                                $param['currency'] = "TZS";
                                $param['paymentReference'] = $payment_reference;
                                $json_request = json_encode($param);

                                $api_url = 'service/onboarding/otp-generate';
                                $endpoint_base_url = env("SELCOM_URL").$api_url;

                                $clientcert = "/var/www/ara-backend-php/arasslkey/client.crt";
                                $keyfile = "/var/www/ara-backend-php/arasslkey/client.key";

                                $curl = curl_init();        
                                    curl_setopt_array($curl, array(
                                    CURLOPT_URL => $endpoint_base_url,
                                    CURLOPT_RETURNTRANSFER => true,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_TIMEOUT => 300,
                                    CURLOPT_POST => 0,
                                    CURLOPT_SSLCERT => $clientcert,
                                    CURLOPT_SSLKEYTYPE => 'PEM',
                                    CURLOPT_SSLKEY => $keyfile,
                                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                    CURLOPT_CUSTOMREQUEST => 'POST',
                                    CURLOPT_POSTFIELDS => $json_request,
                                    CURLOPT_HTTPHEADER => array(
                                        "Authorization: DIGIBANK ".env("AUTH_KEY"),
                                        "x-customer-lang: ".$language_code,
                                        "Content-Type: application/json"
                                    ),
                                ));

                                $response = curl_exec($curl);
                                $err = curl_error($curl);
                                curl_close($curl);
                                
                                DB::table('selcom_api_logs')->insert([
                                    'user_id' => '0',
                                    'request_data' => $json_request,
                                    'response_data' => $response,
                                    'api_name' => $endpoint_base_url,
                                    'created_at' => date("Y-m-d H:i:s"),
                                    'updated_at' => date("Y-m-d H:i:s"),
                                ]);

                                if ($err) {
                                    $response2['success'] = false;
                                    $response2['errorcode'] = "0";
                                    $response2['message'] = trans('message.selcom_api_error');
                                    $response2['total_records'] = "0";
                                    $response2['current_page'] = "0";                                
                                    $response2['data'] = array();
                                    $response2['app_versions'] = $versions;
                                    echo json_encode($response2);
                                    exit;
                                } else {
                                    $json_arr = json_decode($response,true);
                                    if(!empty($json_arr)){
                                        $resultcode = $json_arr['resultcode'];
                                        $result = $json_arr['result'];
                                        if($resultcode !='200' && $result != 'SUCCESS'){
                                            $response0['success'] = false;
                                            $response0['errorcode'] = "0";
                                            $response0['message'] = $json_arr['message'];
                                            $response0['total_records'] = "0";
                                            $response0['current_page'] = "0";                                
                                            $response0['data'] = array();
                                            $response0['app_versions'] = $versions;
                                            echo json_encode($response0);
                                            exit;
                                        }
                                    }else{
                                        $response1['success'] = false;
                                        $response1['errorcode'] = "0";
                                        $response1['message'] = trans('message.selcom_api_error');
                                        $response1['total_records'] = "0";
                                        $response1['current_page'] = "0";                                
                                        $response1['data'] = array();
                                        $response1['app_versions'] = $versions;
                                        echo json_encode($response1);
                                        exit;
                                    }
                                } */
                                /* End */

                                $response3['success'] = false;
                                $response3['errorcode'] = "5";
                                $response3['message'] = trans('message.device_not_match');
                                $response3['total_records'] = "0";
                                $response3['current_page'] = "0";
                                $response3['data'] = array();
                                $response3['app_versions'] = $versions;
                                echo json_encode($response3);
                                exit;
                            }
                        }
                        $device = Devices::find($id);
                        $device->device_token = $device_token;
                        $device->int_udid = $int_udid;
                        $device->device_type = $device_type;
                        $device->updated_at = date("Y-m-d H:i:s");
                        $device->save();
                    }
                }
            }
        } else {
            if (isset($input['user_id'])) {
                $user_id = $input['user_id'];
                $user = User::select('id', 'is_profile_complete')->where('id', $user_id)->first();
                if (!empty($user)) {
                    $user_id = $user['id'];
                    $is_profile_complete = $user['is_profile_complete'];

                    $device_type = $input['device_type'];
                    if ($device_type == 'Android') {
                        $device_type = 1;
                    } else {
                        $device_type = 2;
                    }
                    $device_token = $input['device_token'];
                    $int_udid = $input['int_udid'];

                    $devices = Devices::select('id', 'int_udid')->where(array('user_id' => $user_id))->first();
                    if (empty($devices)) {
                        $device = new Devices();
                        $device->user_id = $user_id;
                        $device->device_token = $device_token;
                        $device->int_udid = $int_udid;
                        $device->device_type = $device_type;
                        $device->created_at = date("Y-m-d H:i:s");
                        $device->updated_at = date("Y-m-d H:i:s");
                        $device->save();
                    } else {
                        $id = $devices->id;
                        $is_login = $devices->is_login;
                        $login_int_udid = $devices->int_udid;
                        if ($is_profile_complete == '1') {
                            if ($login_int_udid !=  $int_udid) {
                                $response['success'] = false;
                                $response['errorcode'] = "5";
                                $response['message'] = trans('message.device_not_match');
                                $response['total_records'] = "0";
                                $response['current_page'] = "0";
                                $response['data'] = array();
                                $response['app_versions'] = $versions;
                                echo json_encode($response);
                                exit;
                            }
                        }
                        $device = Devices::find($id);
                        $device->device_token = $device_token;
                        $device->int_udid = $int_udid;
                        $device->device_type = $device_type;
                        $device->updated_at = date("Y-m-d H:i:s");
                        $device->save();
                    }
                }
            }
        }
        return $next($request);
    }
}
