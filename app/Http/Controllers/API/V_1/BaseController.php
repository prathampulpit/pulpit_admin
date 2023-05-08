<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Controller as Controller;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use Aws\Exception\AwsException;
use Aws\Sts\StsClient;
use Aws\S3\S3Client;
use App\Models\Devices;
use App\Models\User;
use App\Models\UserAccounts;
use App\Models\AccountBalances;
use App\Models\LinkCards;
use App\Models\Currencies;
use App\Models\Cities;
use App\Models\ApiLogs;
use App\Models\ForexRates;
use App\Models\Versions;
use DateTime;
use DB;
use Illuminate\Support\Facades\Auth;

use function GuzzleHttp\json_encode;

class BaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('authapi');
        if (isset($_POST['language_code'])) {
            $this->language_code = $_POST['language_code'];
        } else {
            $this->language_code = "en";
        }
    }

    /**
     * Update device details
     * 
     * @return true false
     * */
    public function update_devices()
    {
    }

    /**
     * Update access token as per required
     *  
     * @return array
     * */
    public function access_token_handler($input)
    {

        $action = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $action);
        $action = $method;

        $headers = apache_request_headers();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if ($action == 'register' || $action == 'documentUpload' || $action == 'detachAccount' || $action == 'updateDeviceDetails' || $action == 'updateLoginPin' || $action == 'billPaymentProductLists' || $action == 'categoryWalletBanksAppConfig' || $action == 'otherCountryAppConfig' || $action == 'cityAppConfig' || $action == 'countryAppConfig' || $action == 'uploadOcrCardDocument' || $action == 'uploadWorkPermitDocuments' || $action == 'nationalityIdentificationConfig' || $action == 'supportTopicsConfig' || $action == 'checkConfigApiVersion' || $action == 'getBankListFromSelcom' || $action == 'checkOtp' || $action == 'editClient' || $action == 'createVcnForWeb' || $action == 'demo' || $action == 'checkUserContactList' || $action == 'disputeTransactionTopicsConfig' || $action == 'sendReferRequest' || $action == 'acceptRejectRequest' || $action == 'checkAllUserContactList' || $action == 'birtAccessRequest') {
            $access_token = '';
            if ($action == 'updateDeviceDetails') {
                $devices = Devices::select('id', 'int_udid', 'updated_at', 'user_id')->where(array('int_udid' => $input['int_udid'], 'device_token' => $input['device_token']))->first();
                if (!empty($devices)) {
                    $user_id = $devices['user_id'];
                    $user = User::find($user_id);
                    $access_token_arr = $user->tokens->where('user_id', $user_id)->first();
                    if (!empty($access_token_arr)) {
                        $user->tokens->each(function ($token, $key) use ($user_id, $user) {
                            if ($token['user_id'] == $user_id) {
                                $token->delete();
                            }
                        });
                        $access_token = $user->createToken('ara')->accessToken;
                    } else {
                        $access_token = $user->createToken('ara')->accessToken;
                    }
                }
            }
        } else {

            if (isset($input['device_type']) && !empty($input['device_type'])) {
                $device_type = $input['device_type'];
            } else {
                $device_type = 'iOS';
            }
            $versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"), DB::raw("CONVERT(version, CHAR) as version"), DB::raw("CONVERT(min_version, CHAR) as min_version"), 'store_url', 'force_update', 'device', 'msg', 'created_at', 'updated_at')->where('device', '=', $device_type)->first();

            if ($action != 'login') {
                /* if(!isset($headers['authorization'])){
                    $response = [
                        'success' => false,
                        'errorcode' => '-11',
                        'message' => trans('message.auth_parameters_missing'),
                        'total_records' => "0",
                        'current_page' => "0",
                    ];
            
                    $response['data'] = array();
                    $response['app_versions'] = $versions;
                    echo json_encode($response);
                    exit;
                } */
            }

            if (isset($input) && !empty($input['user_id'])) {
                $user_id = $input['user_id'];

                $loginuser = User::find($user_id);
                if (empty($loginuser)) {
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

                if ($action == 'login' || $action == 'verifyDocument') {
                } else {
                    $login_details = Auth::user();
                    $login_user_id = $login_details['id'];
                    if ($login_user_id != $user_id) {
                        if (isset($input['device_type']) && !empty($input['device_type'])) {
                            $device_type = $input['device_type'];
                        } else {
                            $device_type = 'iOS';
                        }
                        //$versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"),DB::raw("CONVERT(version, CHAR) as version"),DB::raw("CONVERT(min_version, CHAR) as min_version"),'store_url','force_update','device','msg','created_at','updated_at')->where('device','=', $device_type)->first();
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
                    }
                }

                $devices = Devices::select('id', 'int_udid', 'updated_at')->where(array('user_id' => $user_id))->first();
                if (!empty($devices)) {

                    DB::table('oauth_access_tokens')->where('expires_at', '<', date("Y-m-d"))->delete();

                    $updated_at = $devices['updated_at'];
                    $user = User::find($user_id);
                    $access_token_arr = $user->tokens->where('user_id', $user_id)->first();
                    if (!empty($access_token_arr)) {
                        $user->tokens->each(function ($token, $key) use ($user_id, $user) {
                            if ($token['user_id'] == $user_id) {
                                $token->delete();
                            }
                        });
                        $access_token = $user->createToken('ara')->accessToken;

                        /* $expires_at = $access_token_arr['expires_at'];
                        
                        $datetime1 = new DateTime($updated_at);
                        $datetime2 = new DateTime($expires_at);
                        $interval = $datetime1->diff($datetime2);
                        $seconds = $interval->format('%s');
                        if($seconds > 200){
                            $user->tokens->each(function ($token,$key) use ($user_id, $user) {
                                if($token['user_id'] == $user_id){
                                    $token->delete();
                                }
                            });
                            $access_token = $user->createToken('ara')->accessToken;
                        }else{
                            if($action == 'login' || $action == 'verifyDocument' || $action == 'updateRegisterDetails'){
                                
                                $user->tokens->each(function ($token,$key) use ($user_id, $user) {
                                    if($token['user_id'] == $user_id){
                                        $token->delete();
                                    }
                                });

                                $access_token = $user->createToken('ara')->accessToken;
                            }else{
                                if(!empty($headers) && isset($headers)){
                                    $authorization = str_replace("Bearer ","", $headers['authorization']);
                                    $access_token = trim($authorization);
                                }else{
                                    $user->tokens->each(function ($token,$key) use ($user_id, $user) {
                                        if($token['user_id'] == $user_id){
                                            $token->delete();
                                        }
                                    });
                                    $access_token = $user->createToken('ara')->accessToken;
                                }
                            }
                        } */
                    } else {
                        $access_token = $user->createToken('ara')->accessToken;
                    }
                } else {
                    $access_token = "";
                    //$headers = apache_request_headers();
                    if (!empty($headers)) {
                        $authorization = str_replace("Bearer ", "", $headers['authorization']);
                        $access_token = trim($authorization);
                    }
                }
            } else {
                $access_token = "";
                //$headers = apache_request_headers();
                if (!empty($headers)) {
                    $authorization = str_replace("Bearer ", "", $headers['authorization']);
                    $access_token = trim($authorization);
                }
            }
        }

        return $access_token;
    }

    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendResponse($error_code, $result, $message, $code = 200, $total_records = "0", $current_page = "0", $total_page = "0")
    {
        $action = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $action);
        $action = $method;

        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            $user_id = $_POST['user_id'];
        } else {
            $user_id = 0;
        }
        $request_data = json_encode($_POST);

        if (isset($_POST['device_type']) && !empty($_POST['device_type'])) {
            $device_type = $_POST['device_type'];
        } else {
            $device_type = 'iOS';
        }

        $versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"), DB::raw("CONVERT(version, CHAR) as version"), DB::raw("CONVERT(min_version, CHAR) as min_version"), 'store_url', 'force_update', 'device', 'msg', 'created_at', 'updated_at')->where('device', '=', $device_type)->first();

        $access_token = $this->access_token_handler($_POST);
        $response = [
            'success' => true,
            'errorcode' => $error_code,
            'data'    => $result,
            'message' => $message,
            'access_token' => $access_token,
            'total_records' => $total_records,
            'current_page' => $current_page,
            'total_page' => $total_page,
            'app_versions' => $versions,
        ];

        if ($action != 'billPaymentProducts') {
            $headers = apache_request_headers();
            $apilog_header = new ApiLogs();
            $apilog_header->user_id = "0";
            $apilog_header->api_name = $action . "-header";
            $apilog_header->request_data = json_encode($headers);
            $apilog_header->response_data = "NA";
            $apilog_header->created_at = date("Y-m-d H:i:s");
            $apilog_header->updated_at = date("Y-m-d H:i:s");
            $apilog_header->save();

            $apilog = new ApiLogs();
            $apilog->user_id = $user_id;
            $apilog->api_name = $action;
            $apilog->request_data = $request_data;
            $apilog->response_data = json_encode($response);
            $apilog->created_at = date("Y-m-d H:i:s");
            $apilog->updated_at = date("Y-m-d H:i:s");
            $apilog->save();
        }

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendError($error_code, $error, $errorMessages = [], $code = 500, $total_records = "0", $current_page = "0", $total_page = "0")
    {
        $action = \Route::currentRouteAction();
        list($controller, $method) = explode('@', $action);
        $action = $method;

        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            $user_id = $_POST['user_id'];
        } else {
            $user_id = 0;
        }
        $request_data = json_encode($_POST);

        if (isset($_POST['device_type']) && !empty($_POST['device_type'])) {
            $device_type = $_POST['device_type'];
        } else {
            $device_type = 'iOS';
        }

        $versions = Versions::select(DB::raw("CONVERT(id, CHAR) as id"), DB::raw("CONVERT(version, CHAR) as version"), DB::raw("CONVERT(min_version, CHAR) as min_version"), 'store_url', 'force_update', 'device', 'msg', 'created_at', 'updated_at')->where('device', '=', $device_type)->first();

        $access_token = $this->access_token_handler($_POST);
        $response = [
            'success' => false,
            'errorcode' => $error_code,
            'message' => $error,
            'access_token' => $access_token,
            'total_records' => $total_records,
            'current_page' => $current_page,
            'total_page' => $total_page,
            'app_versions' => $versions,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = array();
        }

        $headers = apache_request_headers();
        $apilog_header = new ApiLogs();
        $apilog_header->user_id = "0";
        $apilog_header->api_name = $action . "-header";
        $apilog_header->request_data = json_encode($headers);
        $apilog_header->response_data = "NA";
        $apilog_header->created_at = date("Y-m-d H:i:s");
        $apilog_header->updated_at = date("Y-m-d H:i:s");
        $apilog_header->save();

        $apilog = new ApiLogs();
        $apilog->user_id = $user_id;
        $apilog->api_name = $action;
        $apilog->request_data = $request_data;
        $apilog->response_data = json_encode($response);
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        return response()->json($response, $code);
    }

    /**
     * return error response.
     *
     * @return \Illuminate\Http\Response
     */
    public function sendApiClientResponse($error_code, $result, $message, $code = 200, $total_records = "0", $current_page = "0")
    {
        $response = [
            'success' => true,
            'errorcode' => $error_code,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    public function sendApiClientError($error_code, $error, $errorMessages = [], $code = 500, $total_records = "0", $current_page = "0")
    {
        $response = [
            'success' => false,
            'errorcode' => $error_code,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = array();
        }
        return response()->json($response, $code);
    }

    /** 
     * File upload method 
     * @return file name
     */
    public function upload($file, $uploadPath)
    {
        $name = $this->getName($file);
        $path = $uploadPath . '/' . $name;
        $disk = $this->getDisk();
        Storage::disk($disk)->put($path, file_get_contents($file));
        return $name;
    }

    /** 
     * Generate file name method
     * @return file name
     */
    private function getName($file)
    {
        return Str::slug($file->getClientOriginalName()) . '-' . time() . '.' . $file->getClientOriginalExtension();
    }

    private function getDisk()
    {
        return config('custom.upload.disk', 'local');
    }

    /**
     * Record add OR edit here
     * @return true false & id value 
     */
    public function recordAddEdit($table, $data, $key = '', $val = '')
    {
        if ($key == '' && $val == '') {
            $result = DB::table($table)->insert($data);
        } else {
            $result = DB::table($table)->where($key, $val)->update($data);
        }
        return $result;
    }

    /**
     * Get all details using key & val
     * @return array list
     */
    public function getRecords($table, $selects, $key, $val)
    {
        $users = DB::table($table)->select(DB::raw($selects))->where($key, '=', $val)->get();
        return $users;
    }

    /**
     * Get one details using key & val
     * @return array list
     */
    public function getOneRecords($table, $selects, $key, $val)
    {
        $users = DB::table($table)->select(DB::raw($selects))->where($key, '=', $val)->first();
        return $users;
    }

    /**
     * Store selcom api request and response
     */
    public function selcomApiRequestResponse($user_id, $api_name, $request_data, $response_data)
    {
        $data['user_id'] = $user_id;
        $data['request_data'] = $request_data;
        $data['response_data'] = $response_data;
        $data['api_name'] = $api_name;
        $data['created_at'] = date("Y-m-d H:i:s");
        $data['updated_at'] = date("Y-m-d H:i:s");
        $this->recordAddEdit('selcom_api_logs', $data);
        return true;
    }

    /**
     * Selcom third party api call here
     * @return array list 
     */
    public function selcomApi($api_url, $param, $user_id = "", $method = "POST")
    {

        $language_code = 'en';
        if (isset($_POST['language_code']) && !empty($_POST['language_code'])) {
            $language_code = $_POST['language_code'];
        }

        $endpoint_base_url = env("SELCOM_URL") . $api_url;

        $clientcert = "/var/www/ara-backend-php/arasslkey/client.crt";
        $keyfile = "/var/www/ara-backend-php/arasslkey/client.key";

        try {
            if ($method == 'POST') {
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
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $param,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            } else if ($method == 'PUT') {
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
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $param,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            } else {

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $endpoint_base_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_POST => 0,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_SSLCERT => $clientcert,
                    CURLOPT_SSLKEYTYPE => 'PEM',
                    CURLOPT_SSLKEY => $keyfile,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            }

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
            } else {
                $json_arr = json_decode($response, true);
                if (!empty($json_arr)) {
                    return $json_arr;
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
            }
        } catch (Exception $e) {
            $err = json_encode($e->getMessage());
            return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
        }
    }

    public function selcomApiTest($api_url, $param, $user_id = "", $method = "POST")
    {

        $language_code = 'en';
        if (isset($_POST['language_code']) && !empty($_POST['language_code'])) {
            $language_code = $_POST['language_code'];
        }

        echo $endpoint_base_url = env("SELCOM_URL") . $api_url;
        echo "\n";
        $clientcert = "/var/www/ara-backend-php/arasslkey/client.crt";
        $keyfile = "/var/www/ara-backend-php/arasslkey/client.key";

        try {
            if ($method == 'POST') {
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
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $param,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            } else if ($method == 'PUT') {
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
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_POSTFIELDS => $param,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            } else {

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => $endpoint_base_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_POST => 0,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 300,
                    CURLOPT_SSLCERT => $clientcert,
                    CURLOPT_SSLKEYTYPE => 'PEM',
                    CURLOPT_SSLKEY => $keyfile,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: DIGIBANK " . env("AUTH_KEY"),
                        "x-customer-lang: " . $language_code,
                        "Content-Type: application/json"
                    ),
                ));
            }

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);

            if ($err) {
                print_r($err);
                exit;
                return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
            } else {
                print_r($response);
                exit;
                $json_arr = json_decode($response, true);
                if (!empty($json_arr)) {
                    return $json_arr;
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
            }
        } catch (Exception $e) {
            $err = json_encode($e->getMessage());
            print_r($err);
            exit;
            return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
        }
    }

    /**
     * Selcom third party api call here
     * @return array list 
     */
    public function selcomOnboardingApis($user_id, $param)
    {
        $user = User::find($user_id);
        $namearr = explode(" ", $user->name);
        $first_name = $user->first_name; //$namearr[0];
        //$first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $user->last_name; //$namearr[1];
        //$last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);
        $language = $param['language_code'];
        $login_pin = $param['login_pin'];
        $dob = $user->dob;
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $email = $param['email'];
        $gender = $user->gender;
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $client_id = trim($user->client_id);
        $document_id = $user->document_id;
        $document_name = $user->document_file_name;
        $document_number = $user->document_number;
        $nationality_id = $user->nationality_id;
        $resident_permit = $user->resident_permit;
        $work_permit = $user->work_permit;
        $user_type = $user->user_type;
        $profile_picture = $user->profile_picture;
        $selfie_picture = $user->selfie_picture;
        $address = $param['address'];
        $city_id = $param['city_id'];
        if (!empty($city_id)) {
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        } else {
            $city_name = "";
        }
        $latitude = $param['latitude'];
        $longitude = $param['longitude'];
        $referral_code = $param['referral_code'];

        $req['externalId'] = rand(1000, 9999) . substr(time(), -7);
        $req['fullname'] = $first_name . " " . $last_name;
        $json_request = json_encode($req);
        $check_name = $this->selcomApi('pep-screening', $json_request, $user_id);
        $this->selcomApiRequestResponse($user_id, 'pep-screening', $json_request, json_encode($check_name));
        $user_model = User::find($user_id);
        if ($check_name['resultcode'] == 200) {
            $user_model->is_pep_scan = 'Yes';
        } else {
            $user_model->is_pep_scan = 'No';
        }
        $user_model->save();

        //$param_client['client_id'] = $client_id;
        $param_client['externalId'] = $external_id;
        $param_client['firstname'] = $first_name;
        $param_client['lastname'] = $last_name;
        $param_client['language'] = $language;
        $param_client['msisdn'] = $country_code . $mobile_number;
        $param_client['dob'] = $dob;
        $param_client['email'] = $email;
        $param_client['gender'] = ($gender == 'M') ? "MALE" : "FEMALE"; //($gender=='MALE')?"M":"F";
        $param_client['active'] = "1";
        $param_client['referralCode'] = $referral_code;
        $param_client['location.city'] = $city_name;
        $param_client['location.street'] = $address;
        $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
        $param_client['location.country'] = "TZ";
        $client_json_request = json_encode($param_client);

        /**
         * Check client details in selcome
         */
        if (!empty($client_id)) {
            $check_client = $this->selcomApi('client/' . $client_id, $client_json_request, $user_id, "GET");
            $client_result = $check_client['result'];
            $client_data = $check_client['data'];
        } else {
            $client_result = 'FAIL';
            $client_data = array();
        }
        if ($client_result != 'SUCCESS' && empty($client_data)) {
            $create_client = $this->selcomApi('client', $client_json_request, $user_id);
            $this->selcomApiRequestResponse($user_id, 'client_add', $client_json_request, json_encode($create_client));
            $json_arr = $create_client;

            if (!empty($json_arr)) {
                if ($json_arr['resultcode'] == 200) {
                    $data = $json_arr['data'];
                    if (!empty($data)) {
                        $client_id = $data[0]['clientId'];
                        $accountNo = $data[0]['accountNo'];
                        $referralCode = $data[0]['referralCode'];
                        $data_param['client_id'] = $client_id;
                        $data_param['referral_code'] = $referralCode;
                        $this->recordAddEdit('users', $data_param, 'id', $user_id);

                        /**
                         * Add User account details getting from selcom
                         */
                        $userAccounts = new UserAccounts();
                        $userAccounts->user_id = $user_id;
                        $userAccounts->account_number = $accountNo;
                        $userAccounts->quickrewards_balance = "0.00";
                        $userAccounts->status = "1";
                        $userAccounts->save();

                        /**
                         * Add multiple account balance added from here
                         */
                        $currencies = Currencies::all()->sortBy("id");
                        foreach ($currencies as $val) {
                            $user_account_id = $userAccounts->id;
                            $currency_id = $val['id'];
                            $qry = DB::table('account_balances')->select('id')->where(array('currency_id' => $currency_id, 'user_account_id' => $user_account_id))->first();
                            if (empty($qry)) {
                                $createAccount = new AccountBalances();
                                $createAccount->user_account_id = $user_account_id;
                                $createAccount->currency_id = $currency_id;
                                $createAccount->account_balance = "0.00";
                                $createAccount->created_at = date("Y-m-d H:i:s");
                                $createAccount->updated_at = date("Y-m-d H:i:s");
                                $createAccount->save();
                            }
                        }

                        /**
                         * Create a client identity
                         */
                        if ($document_id == 1) {
                            $documentKey = $document_number;
                            $document_description = "passport ocr scrore 80";
                        } else {
                            $documentKey = $document_number;
                            $document_description = "NIDA ocr scrore 80";
                        }
                        $external_id_identity = rand(1000, 9999) . substr(time(), -7);
                        //$documentKey = time().rand('111','999');
                        $identity_param['externalId'] = $external_id_identity;
                        $identity_param['documentTypeId'] = $document_id;
                        $identity_param['documentKey'] = $documentKey;
                        $identity_param['description'] = $document_description;
                        $identity_json_request = json_encode($identity_param);
                        $create_identity = $this->selcomApi('client/' . $client_id . '/identity', $identity_json_request, $user_id);

                        $this->selcomApiRequestResponse($user_id, 'identity', $identity_json_request, json_encode($create_identity));

                        $identity_result = $create_identity['result'];
                        if ($identity_result == 'SUCCESS' && $create_identity['resultcode'] == '200') {
                            $identity_data = $create_identity['data'];
                            $resourceId = $identity_data[0]['resourceId'];

                            /**
                             * Document upload
                             */
                            $doc_upload_path = config('custom.upload.user.document_path');
                            //$doc_file_path = storage_path('app') . '/public/' . $doc_upload_path . "/" . $document_name;

                            $doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $document_name;

                            $type = pathinfo($doc_file_path, PATHINFO_EXTENSION);
                            $data = file_get_contents($doc_file_path);
                            $base64 = base64_encode($data);

                            $external_id_document = rand(1000, 9999) . substr(time(), -7);
                            $doc_param['externalId'] = $external_id_document;
                            $doc_param['name'] = $document_name;
                            $doc_param['fileName'] = $document_name;
                            $doc_param['imageType'] = 'image/' . $type;
                            $doc_param['imageData'] = $base64;
                            $doc_json_request = json_encode($doc_param);

                            try {
                                $upload_doc_identity = $this->selcomApi('client-identity/' . $resourceId . '/document', $doc_json_request, $user_id);
                                if ($upload_doc_identity['resultcode'] != '200') {
                                    return $upload_doc_identity;
                                }

                                $this->selcomApiRequestResponse($user_id, 'client-identity', "", json_encode($upload_doc_identity));
                                $doc_result = $upload_doc_identity['result'];

                                $profile_path = config('custom.upload.user.profile');
                                //$profile_file_path = storage_path('app') . '/public/' . $profile_path . "/" . $profile_picture;
                                $profile_file_path = env('S3_BUCKET_URL') . 'user/' . $selfie_picture;

                                $profile_img_type = pathinfo($profile_file_path, PATHINFO_EXTENSION);
                                $data_profile_pic = file_get_contents($profile_file_path);
                                $base64Profile = base64_encode($data_profile_pic);
                                $external_id_profile = rand(1000, 9999) . substr(time(), -7);
                                $doc_param_profile['externalId'] = $external_id_profile;
                                $doc_param_profile['imageType'] = 'image/' . $profile_img_type;
                                $doc_param_profile['imageData'] = $base64Profile;
                                $profile_json_request = json_encode($doc_param_profile);
                                $create_profile_pic = $this->selcomApi('client/' . $client_id . '/image', $profile_json_request, $user_id, 'POST');

                                $this->selcomApiRequestResponse($user_id, 'image-upload', $profile_json_request, json_encode($create_profile_pic));

                                if ($nationality_id != '1') {

                                    if (!empty($resident_permit)) {
                                        $doc_permit_upload_path = config('custom.upload.user.document_permits');
                                        $doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $resident_permit;

                                        $type = pathinfo($doc_file_path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($doc_file_path);
                                        $base64 = base64_encode($data);

                                        $support_doc_param['externalId'] = $external_id;
                                        $support_doc_param['name'] = $resident_permit;
                                        $support_doc_param['fileName'] = $resident_permit;
                                        $support_doc_param['description'] = "Resident Permit";
                                        $support_doc_param['imageType'] = 'image/' . $type;
                                        $support_doc_param['imageData'] = $base64;
                                        $doc_json_request = json_encode($support_doc_param);
                                        $upload_doc_identity = $this->selcomApi('client/' . $client_id . '/document', $doc_json_request, $user_id);

                                        $this->selcomApiRequestResponse($user_id, 'Resident Permit', "", json_encode($upload_doc_identity));
                                    }

                                    if (!empty($work_permit)) {
                                        $doc_permit_upload_path = config('custom.upload.user.document_permits');
                                        $work_permit_doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $work_permit;

                                        $type = pathinfo($work_permit_doc_file_path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($work_permit_doc_file_path);
                                        $base64 = base64_encode($data);

                                        $support_doc_param['externalId'] = $external_id;
                                        $support_doc_param['name'] = $work_permit;
                                        $support_doc_param['fileName'] = $work_permit;
                                        $support_doc_param['description'] = "Work Permit";
                                        $support_doc_param['imageType'] = 'image/' . $type;
                                        $support_doc_param['imageData'] = $base64;
                                        $doc_json_request = json_encode($support_doc_param);
                                        $upload_doc_identity = $this->selcomApi('client/' . $client_id . '/document', $doc_json_request, $user_id);

                                        $this->selcomApiRequestResponse($user_id, 'Work Permit', "", json_encode($upload_doc_identity));
                                    }
                                }

                                /* Create VCN */
                                $api_url = 'vcn/create';
                                $param_vcn['msisdn'] = $country_code . $mobile_number;
                                $param_vcn['account'] = $accountNo;
                                $param_vcn['first_name'] = $first_name;
                                $param_vcn['last_name'] = $last_name;
                                $param_vcn['gender'] = strtoupper($gender);
                                $param_vcn['dob'] = date("dmY", strtotime($dob));
                                $param_vcn['address'] = $address;
                                $param_vcn['city'] = $city_name;
                                $param_vcn['pin'] = '1234';
                                $param_vcn['nationality'] = 'TZ';
                                $param_vcn['vendor'] = env('SELCOM_VENDOR');
                                $param_vcn['transid'] = rand(1000, 9999) . substr(time(), -7);
                                $vcnresult = $this->selcomDevApi($api_url, $param_vcn, 'true');

                                $this->selcomApiRequestResponse($user_id, 'VCN', json_encode($param_vcn), json_encode($vcnresult));

                                if ($vcnresult['resultcode'] == '000') {

                                    $vcn_card_data = $vcnresult['data'];
                                    $card_id = $vcn_card_data[0]['card_id'];
                                    $masked_card = $vcn_card_data[0]['masked_card'];
                                    $vcn_url = $vcn_card_data[0]['vcn_url'];

                                    $qry = new LinkCards();
                                    $qry->user_id = $user_id;
                                    $qry->card_serial_number = $card_id;
                                    $qry->card_id = $card_id;
                                    $qry->status = "1";
                                    $qry->card_number = $masked_card;
                                    $qry->card_token = "";
                                    $qry->vcn_url = $vcn_url;
                                    $qry->card_name = "ITEM";
                                    $qry->card_type = "1";
                                    $qry->expiry = "";
                                    $qry->save();

                                    return $vcnresult;
                                } else {
                                    $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                                    return json_decode($catch_response, true);
                                }
                            } catch (Exception $e) {
                                $err = json_encode($e->getMessage());

                                $this->selcomApiRequestResponse($user_id, 'try-catch', $doc_json_request, $err);

                                $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                                return json_decode($catch_response, true);
                            }
                        } else {
                            $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                            return json_decode($catch_response, true);
                            //return $create_identity;
                        }
                    }
                } else {
                    return $json_arr;
                    // $error_message = $json_arr['message'];
                    // return $this->sendError('0', $error_message, array(), '200');
                }
            } else {
                //return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                return $json_arr;
            }
        } else {
            /* $create_client = $this->selcomApi('client/'.$client_id, $client_json_request, $user_id,"GET");
            print_r($create_client);
            exit; */
            return $check_client;
            /* if(!empty($check_client)){
                $error_message = $check_client['message'];
                return $this->sendError('0', $error_message, array(), '200');
            }else{
                return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            } */
        }
    }

    public function selcomOnboardingApisAraLite($user_id, $param)
    {
        $user = User::find($user_id);
        $namearr = explode(" ", $user->name);
        $first_name = $user->first_name; //$namearr[0];
        //$first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $user->last_name; //$namearr[1];
        //$last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);
        $language = $param['language_code'];
        $login_pin = $param['login_pin'];
        $dob = $user->dob;
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $email = $param['email'];
        $gender = $user->gender;
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $client_id = trim($user->client_id);
        $document_id = $user->document_id;
        $document_name = $user->document_file_name;
        $document_number = $user->document_number;
        $nationality_id = $user->nationality_id;
        $resident_permit = $user->resident_permit;
        $work_permit = $user->work_permit;
        $user_type = $user->user_type;
        $profile_picture = $user->profile_picture;
        $selfie_picture = $user->selfie_picture;
        $address = $param['address'];
        $city_id = $param['city_id'];
        if (!empty($city_id)) {
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        } else {
            $city_name = "";
        }
        $latitude = $param['latitude'];
        $longitude = $param['longitude'];
        $referral_code = $param['referral_code'];

        //$param_client['client_id'] = $client_id;
        $param_client['externalId'] = $external_id;
        $param_client['firstname'] = $first_name;
        $param_client['lastname'] = $last_name;
        $param_client['language'] = $language;
        $param_client['msisdn'] = $country_code . $mobile_number;
        $param_client['dob'] = $dob;
        $param_client['email'] = $email;
        $param_client['gender'] = ($gender == 'M') ? "MALE" : "FEMALE"; //($gender=='MALE')?"M":"F";
        $param_client['active'] = "1";
        $param_client['referralCode'] = $referral_code;
        $param_client['location.city'] = $city_name;
        $param_client['location.street'] = $address;
        $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
        $param_client['location.country'] = "TZ";
        $param_client['temporaryPIN'] = $login_pin;
        $client_json_request = json_encode($param_client);

        /**
         * Check client details in selcome
         */
        if (!empty($client_id)) {
            $check_client = $this->selcomApi('client/' . $client_id, $client_json_request, $user_id, "GET");
            $client_result = $check_client['result'];
            $client_data = $check_client['data'];
        } else {
            $client_result = 'FAIL';
            $client_data = array();
        }
        if ($client_result != 'SUCCESS' && empty($client_data)) {
            $create_client = $this->selcomApi('client-lite', $client_json_request, $user_id);
            $this->selcomApiRequestResponse($user_id, 'client_add', $client_json_request, json_encode($create_client));
            $json_arr = $create_client;

            if (!empty($json_arr)) {
                if ($json_arr['resultcode'] == 200) {
                    $data = $json_arr['data'];
                    if (!empty($data)) {
                        $client_id = $data[0]['clientId'];
                        $accountNo = $data[0]['accountNo'];
                        $referralCode = $data[0]['referralCode'];
                        $data_param['client_id'] = $client_id;
                        $data_param['referral_code'] = $referralCode;
                        $this->recordAddEdit('users', $data_param, 'id', $user_id);

                        /**
                         * Add User account details getting from selcom
                         */
                        $userAccounts = new UserAccounts();
                        $userAccounts->user_id = $user_id;
                        $userAccounts->account_number = $accountNo;
                        $userAccounts->quickrewards_balance = "0.00";
                        $userAccounts->status = "1";
                        $userAccounts->save();

                        /**
                         * Add multiple account balance added from here
                         */
                        $currencies = Currencies::all()->sortBy("id");
                        foreach ($currencies as $val) {
                            $user_account_id = $userAccounts->id;
                            $currency_id = $val['id'];
                            $qry = DB::table('account_balances')->select('id')->where(array('currency_id' => $currency_id, 'user_account_id' => $user_account_id))->first();
                            if (empty($qry)) {
                                $createAccount = new AccountBalances();
                                $createAccount->user_account_id = $user_account_id;
                                $createAccount->currency_id = $currency_id;
                                $createAccount->account_balance = "0.00";
                                $createAccount->created_at = date("Y-m-d H:i:s");
                                $createAccount->updated_at = date("Y-m-d H:i:s");
                                $createAccount->save();
                            }
                        }

                        /**
                         * Create a client identity
                         */
                        if ($document_id == 1) {
                            $documentKey = $document_number;
                            $document_description = "passport ocr scrore 80";
                        } else {
                            $documentKey = $document_number;
                            $document_description = "NIDA ocr scrore 80";
                        }
                        $external_id_identity = rand(1000, 9999) . substr(time(), -7);
                        //$documentKey = time().rand('111','999');
                        $identity_param['externalId'] = $external_id_identity;
                        $identity_param['documentTypeId'] = $document_id;
                        $identity_param['documentKey'] = $documentKey;
                        $identity_param['description'] = $document_description;
                        $identity_json_request = json_encode($identity_param);
                        $create_identity = $this->selcomApi('client/' . $client_id . '/identity', $identity_json_request, $user_id);

                        $this->selcomApiRequestResponse($user_id, 'identity', $identity_json_request, json_encode($create_identity));

                        $identity_result = $create_identity['result'];
                        if ($identity_result == 'SUCCESS' && $create_identity['resultcode'] == '200') {
                            $identity_data = $create_identity['data'];
                            $resourceId = $identity_data[0]['resourceId'];

                            /**
                             * Document upload
                             */
                            $doc_upload_path = config('custom.upload.user.document_path');
                            //$doc_file_path = storage_path('app') . '/public/' . $doc_upload_path . "/" . $document_name;

                            $doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $document_name;

                            $type = pathinfo($doc_file_path, PATHINFO_EXTENSION);
                            $data = file_get_contents($doc_file_path);
                            $base64 = base64_encode($data);

                            $external_id_document = rand(1000, 9999) . substr(time(), -7);
                            $doc_param['externalId'] = $external_id_document;
                            $doc_param['name'] = $document_name;
                            $doc_param['fileName'] = $document_name;
                            $doc_param['imageType'] = 'image/' . $type;
                            $doc_param['imageData'] = $base64;
                            $doc_json_request = json_encode($doc_param);

                            try {
                                $upload_doc_identity = $this->selcomApi('client-identity/' . $resourceId . '/document', $doc_json_request, $user_id);
                                if ($upload_doc_identity['resultcode'] != '200') {
                                    return $upload_doc_identity;
                                }

                                $this->selcomApiRequestResponse($user_id, 'client-identity', "", json_encode($upload_doc_identity));
                                $doc_result = $upload_doc_identity['result'];

                                $profile_path = config('custom.upload.user.profile');
                                //$profile_file_path = storage_path('app') . '/public/' . $profile_path . "/" . $profile_picture;
                                $profile_file_path = env('S3_BUCKET_URL') . 'user/' . $profile_picture;

                                $profile_img_type = pathinfo($profile_file_path, PATHINFO_EXTENSION);
                                $data_profile_pic = file_get_contents($profile_file_path);
                                $base64Profile = base64_encode($data_profile_pic);
                                $external_id_profile = rand(1000, 9999) . substr(time(), -7);
                                $doc_param_profile['externalId'] = $external_id_profile;
                                $doc_param_profile['imageType'] = 'image/' . $profile_img_type;
                                $doc_param_profile['imageData'] = $base64Profile;
                                $profile_json_request = json_encode($doc_param_profile);
                                $create_profile_pic = $this->selcomApi('client/' . $client_id . '/image', $profile_json_request, $user_id, 'POST');

                                $this->selcomApiRequestResponse($user_id, 'image-upload', $profile_json_request, json_encode($create_profile_pic));

                                if ($nationality_id != '1') {

                                    if (!empty($resident_permit)) {
                                        $doc_permit_upload_path = config('custom.upload.user.document_permits');
                                        $doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $resident_permit;

                                        $type = pathinfo($doc_file_path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($doc_file_path);
                                        $base64 = base64_encode($data);

                                        $support_doc_param['externalId'] = $external_id;
                                        $support_doc_param['name'] = $resident_permit;
                                        $support_doc_param['fileName'] = $resident_permit;
                                        $support_doc_param['description'] = "Resident Permit";
                                        $support_doc_param['imageType'] = 'image/' . $type;
                                        $support_doc_param['imageData'] = $base64;
                                        $doc_json_request = json_encode($support_doc_param);
                                        $upload_doc_identity = $this->selcomApi('client/' . $client_id . '/document', $doc_json_request, $user_id);

                                        $this->selcomApiRequestResponse($user_id, 'Resident Permit', "", json_encode($upload_doc_identity));
                                    }

                                    if (!empty($work_permit)) {
                                        $doc_permit_upload_path = config('custom.upload.user.document_permits');
                                        $work_permit_doc_file_path = env('S3_BUCKET_URL') . 'documents/' . $work_permit;

                                        $type = pathinfo($work_permit_doc_file_path, PATHINFO_EXTENSION);
                                        $data = file_get_contents($work_permit_doc_file_path);
                                        $base64 = base64_encode($data);

                                        $support_doc_param['externalId'] = $external_id;
                                        $support_doc_param['name'] = $work_permit;
                                        $support_doc_param['fileName'] = $work_permit;
                                        $support_doc_param['description'] = "Work Permit";
                                        $support_doc_param['imageType'] = 'image/' . $type;
                                        $support_doc_param['imageData'] = $base64;
                                        $doc_json_request = json_encode($support_doc_param);
                                        $upload_doc_identity = $this->selcomApi('client/' . $client_id . '/document', $doc_json_request, $user_id);

                                        $this->selcomApiRequestResponse($user_id, 'Work Permit', "", json_encode($upload_doc_identity));
                                    }
                                }

                                /* Create VCN */
                                $api_url = 'vcn/create';
                                $param_vcn['msisdn'] = $country_code . $mobile_number;
                                $param_vcn['account'] = $accountNo;
                                $param_vcn['first_name'] = $first_name;
                                $param_vcn['last_name'] = $last_name;
                                $param_vcn['gender'] = strtoupper($gender);
                                $param_vcn['dob'] = date("dmY", strtotime($dob));
                                $param_vcn['address'] = $address;
                                $param_vcn['city'] = $city_name;
                                $param_vcn['pin'] = '1234';
                                $param_vcn['nationality'] = 'TZ';
                                $param_vcn['vendor'] = env('SELCOM_VENDOR');
                                $param_vcn['transid'] = rand(1000, 9999) . substr(time(), -7);
                                $vcnresult = $this->selcomDevApi($api_url, $param_vcn, 'true');

                                $this->selcomApiRequestResponse($user_id, 'VCN', json_encode($param_vcn), json_encode($vcnresult));

                                if ($vcnresult['resultcode'] == '000') {

                                    $vcn_card_data = $vcnresult['data'];
                                    $card_id = $vcn_card_data[0]['card_id'];
                                    $masked_card = $vcn_card_data[0]['masked_card'];
                                    $vcn_url = $vcn_card_data[0]['vcn_url'];

                                    $qry = new LinkCards();
                                    $qry->user_id = $user_id;
                                    $qry->card_serial_number = $card_id;
                                    $qry->card_id = $card_id;
                                    $qry->status = "1";
                                    $qry->card_number = $masked_card;
                                    $qry->card_token = "";
                                    $qry->vcn_url = $vcn_url;
                                    $qry->card_name = "ITEM";
                                    $qry->card_type = "1";
                                    $qry->expiry = "";
                                    $qry->save();

                                    return $vcnresult;
                                } else {
                                    $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                                    return json_decode($catch_response, true);
                                }
                            } catch (Exception $e) {
                                $err = json_encode($e->getMessage());

                                $this->selcomApiRequestResponse($user_id, 'try-catch', $doc_json_request, $err);

                                $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                                return json_decode($catch_response, true);
                            }
                        } else {
                            $catch_response = '{"reference":null,"externalId":"20200407002","resultcode":"200","result":"SUCCESS","message":"SUCCESS","data":[]}';
                            return json_decode($catch_response, true);
                            //return $create_identity;
                        }
                    }
                } else {
                    return $json_arr;
                    // $error_message = $json_arr['message'];
                    // return $this->sendError('0', $error_message, array(), '200');
                }
            } else {
                //return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                return $json_arr;
            }
        } else {
            /* $create_client = $this->selcomApi('client/'.$client_id, $client_json_request, $user_id,"GET");
            print_r($create_client);
            exit; */
            return $check_client;
            /* if(!empty($check_client)){
                $error_message = $check_client['message'];
                return $this->sendError('0', $error_message, array(), '200');
            }else{
                return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            } */
        }
    }

    /**
     * Selcom third party api call here
     * @return array list
     */
    public function selcomDevApi($api_url, $param, $is_post = true)
    {
        //$url = "http://3.227.121.40:8123/v1/" . $api_url;
        $url = env('SELCOM_URL_V2') . $api_url;

        if ($is_post != true) {
            $url = $url . '?' . http_build_query($param);
        }

        $authorization = base64_encode(env('SELCOM_API_KEY'));

        $timestamp = date('c'); //2019-02-26T09:30:46+03:00

        $signed_fields = $this->getSignedFields($param);

        $digest = $this->computeSignature($param, $signed_fields, $timestamp, env('SELCOM_API_SECRET'));

        $json = json_encode($param);

        try {

            $headers = array(
                "Content-type: application/json;charset=\"utf-8\"",
                "Accept: application/json",
                "Cache-Control: no-cache",
                "Authorization: SELCOM $authorization",
                "Digest-Method: HS256",
                "Digest: $digest",
                "Timestamp: $timestamp",
                "Signed-Fields: $signed_fields",
            );

            // echo $url;
            // print_r($headers);
            // die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            if ($is_post == true) {
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                return $err;
                //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
            } else {
                $json_arr = json_decode($response, true);
                if (!empty($json_arr)) {
                    return $json_arr;
                } else {
                    return $err;
                    //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
                }
            }
        } catch (Exception $e) {
            return $err = json_encode($e->getMessage());
            //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
        }
    }

    public function selcomDevApi1($api_url, $param, $is_post = true)
    {
        $url = "http://3.227.121.40:8123/v1/" . $api_url;

        if ($is_post != true) {
            echo $url = $url . '?' . http_build_query($param);
        }

        $authorization = base64_encode(env('SELCOM_API_KEY'));

        $timestamp = date('c'); //2019-02-26T09:30:46+03:00

        $signed_fields = $this->getSignedFields($param);

        $digest = $this->computeSignature($param, $signed_fields, $timestamp, env('SELCOM_API_SECRET'));

        $json = json_encode($param);

        try {

            $headers = array(
                "Content-type: application/json;charset=\"utf-8\"",
                "Accept: application/json",
                "Cache-Control: no-cache",
                "Authorization: SELCOM $authorization",
                "Digest-Method: HS256",
                "Digest: $digest",
                "Timestamp: $timestamp",
                "Signed-Fields: $signed_fields",
            );

            // echo $url;
            // print_r($headers);
            // die;
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);

            if ($is_post == true) {
                echo "c";
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
            }

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_TIMEOUT, 90);

            $response = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if ($err) {
                return $err;
                //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
            } else {
                $json_arr = json_decode($response, true);
                if (!empty($json_arr)) {
                    return $json_arr;
                } else {
                    return $err;
                    //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
                }
            }
        } catch (Exception $e) {
            return $err = json_encode($e->getMessage());
            //return $this->sendError('0', trans('message.selcom_api_error'), $err, '200');
        }
    }

    public function computeSignature($parameters, $signed_fields, $request_timestamp, $api_secret)
    {
        error_reporting(E_ALL & ~E_NOTICE);
        $fields_order = explode(',', $signed_fields);
        $sign_data = "timestamp=$request_timestamp";

        foreach ($fields_order as $key) {
            $key_arr = explode(".", $key);
            $tkey = "";
            if (count($key_arr) == 1) {
                $sign_data .= "&$key=" . $parameters[$key];
            } else if (count($key_arr) == 2) {
                $tkey = $key_arr[0] . "." . $key_arr[1];
                $sign_data .= "&$key=" . $parameters[$key_arr[0]][$key_arr[1]];
            } else {
                throw Exception("Request dimention not supported");
            }
        }

        return base64_encode(hash_hmac('sha256', $sign_data, $api_secret, true));
    }

    public function getSignedFields($parameters)
    {
        $signed_fields_arr = array();
        foreach ($parameters as $key => $value) {
            array_push($signed_fields_arr, $key);
        }

        $signed_fields = implode(",", $signed_fields_arr);
        return $signed_fields;
    }

    /**
     * Update account balance
     */
    public function araAvaBalance($user_id)
    {
        $user = User::find($user_id);
        $client_id = $user->client_id;
        $accounts = $this->selcomApi('client/' . $client_id . '/accounts', array(), $user_id, "GET");
        if (!empty($accounts)) {
            $resultcode = $accounts['resultcode'];
            $result = $accounts['result'];
            $account_data = $accounts['data'];
            if (!empty($account_data) && $resultcode == 200) {
                foreach ($account_data as $val) {
                    $accountBalance = $val['accountBalance'];
                    $availableBalance = $val['availableBalance'];
                    $currency = $val['currency'];

                    $currencies = Currencies::where('currency_code', '=', $currency)->first();
                    if (!empty($currencies)) {
                        $currency_id = $currencies['id'];

                        $useraccount = UserAccounts::where('user_id', '=', $user_id)->first();
                        if (!empty($useraccount)) {
                            $user_account_id = $useraccount->id;

                            DB::table('account_balances')->where('currency_id', $currency_id)->where('user_account_id', $user_account_id)->update(['account_balance' => $availableBalance]);
                        }
                    }
                    return $availableBalance;
                }
            }
        }
    }

    /**
     * Update qwikrewards balance 
     */
    public function qwikrewardsBalance($user_id)
    {
        $user = User::find($user_id);
        $client_id = $user->client_id;

        $accounts = $this->selcomApi('client/' . $client_id . '/qwikrewards', array(), $user_id, "GET");
        if (!empty($accounts)) {
            $resultcode = $accounts['resultcode'];
            $result = $accounts['result'];
            if ($resultcode == 200) {
                $balance = $accounts['data'][0]['balance'];
                DB::table('user_accounts')->where('user_id', $user_id)->update(['quickrewards_balance' => $balance]);

                return $balance;
            }
        }
    }

    /**
     * Update qwikrewards balance 
     */
    public function stashBalance($user_id)
    {
        $user = User::find($user_id);
        $client_id = $user->client_id;

        $accounts = $this->selcomApi('client/' . $client_id . '/stash-info', array(), $user_id, "GET");
        $json_request = 'client/' . $client_id . '/stash-info';
        $this->selcomApiRequestResponse($user_id, 'stash-balance', $json_request, json_encode($accounts));

        if (!empty($accounts)) {

            $resultcode = $accounts['resultcode'];

            if ($resultcode == 200) {

                $accountBalance = "";

                if (isset($accounts['data'])) {

                    if (isset($accounts['data'][0])) {

                        if (isset($accounts['data'][0]['accountBalance'])) {
                            $accountBalance = $accounts['data'][0]['accountBalance'];

                            DB::table('stashes')->where('user_id', $user_id)->update(['stash_balance' => $accountBalance]);

                            return $accountBalance;
                        }
                    }
                }
            }
        }
    }

    /**
     * Update all currency account balance
     */
    public function currencyAvaBalance($user_id)
    {
        $user = User::find($user_id);
        $client_id = $user->client_id;
        $accounts = $this->selcomApi('client/' . $client_id . '/forex-account-balance', array(), $user_id, "GET");
        if (!empty($accounts)) {
            $resultcode = $accounts['resultcode'];
            $account_data = $accounts['data'];
            if (!empty($account_data) && $resultcode == 200) {
                foreach ($account_data as $val) {
                    $availableBalance = $val['balance'];
                    $currency = $val['currency'];

                    $currencies = Currencies::where('currency_code', '=', $currency)->first();
                    if (!empty($currencies)) {
                        $currency_id = $currencies['id'];

                        $useraccount = UserAccounts::where('user_id', '=', $user_id)->first();
                        if (!empty($useraccount)) {
                            $user_account_id = $useraccount->id;

                            DB::table('account_balances')->where('currency_id', $currency_id)->where('user_account_id', $user_account_id)->update(['account_balance' => $availableBalance]);
                        }
                    }
                }
            }
        }
    }

    public function sendPuchNotification($deviceType, $deviceToken, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "ARA")
    {
        //$fields = "notificationId"; 

        //$totalNotifications = 10;
        //$devicetoken[] = $deviceToken;
        //$desc = $notificationText;
        $fields = "notificationId";
        $devicetoken[] = $deviceToken;
        $desc = $notificationText;

        // Set POST variables 
        $url = 'https://fcm.googleapis.com/fcm/send';
        //$message = array("message" => $desc);
        $message = array("message" => $desc, 'title' => $title, 'click_action' => "FLUTTER_NOTIFICATION_CLICK", 'status' => 'done');
        //$message = $desc;

        //echo $totalNotifications;
        $notificationArray = array(
            'badge' => $totalNotifications,
            'body' => $desc,
            'sound' => 'default',
            'title' => $title,
        );

        /* if ($deviceType == 'Iphone') {
            $fields = array(
                'registration_ids' => $deviceToken,
                'data' => $message,
                'notification' => $notificationArray,
            );
        } else {
            $fields = array(
                'registration_ids' => $deviceToken,
                'data' => $message,                
            );
        } */

        if ($deviceType == 'Iphone') {
            $fields = array(
                'registration_ids' => $devicetoken,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        } else {
            $fields = array(
                'registration_ids' => $devicetoken,
                'data' => $message,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        }

        $fieldsJson = json_encode($fields);
        $fieldsJson = str_replace('\"', '', $fieldsJson);

        if ($deviceType == 'Iphone') {
            /* New update code for ios device */
            //$fields='{"priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "id": "1", "status": "done","body": "'.$notificationText.'","title": "'.$title.'"}, "to": "'.$deviceToken.'"}';

            $fieldsJson =  '{"to":"' . $deviceToken . '","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
        } else {
            /* New update code for android device */
            //$fields='{"notification": {"body": "'.$notificationText.'","title": "'.$title.'"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "id": "1", "status": "done"}, "to": "'.$deviceToken.'"}';

            $fieldsJson =  '{"to":"' . $deviceToken . '","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
        }

        $headers = array(
            'Authorization: key=' . env('GOOGLE_API_KEY'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsJson);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        DB::table('api_logs')->insert([
            'user_id' => '0',
            'api_name' => 'Notification - without',
            'request_data' => $fieldsJson,
            'response_data' => $result,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        return $result;
    }

    public function sendPuchNotificationWithData($deviceType, $deviceToken, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "ARA", $array_obj = "")
    {
        //$fields = "notificationId"; 

        //$totalNotifications = 10;
        //$devicetoken[] = $deviceToken;
        //$desc = $notificationText;
        $fields = "notificationId";
        $devicetoken[] = $deviceToken;
        $desc = $notificationText;
        $type = '';
        $card_id = '';
        if (!empty($array_obj)) {
            $type = (string)$array_obj['type'];

            $card_id = '';
            if (isset($array_obj['card_id'])) {
                $card_id = $array_obj['card_id'];
            }

            $user_id = '';
            if (isset($array_obj['user_id'])) {
                $user_id = $array_obj['user_id'];
            }

            $request_id = '';
            if (isset($array_obj['request_id'])) {
                $request_id = $array_obj['request_id'];
            }
        }
        // Set POST variables 
        $url = 'https://fcm.googleapis.com/fcm/send';
        //$message = array("message" => $desc);
        $message = array("message" => $desc, 'title' => $title, 'click_action' => "FLUTTER_NOTIFICATION_CLICK", 'status' => 'done', 'type' => $type, 'card_id' => $card_id);
        //$message = $desc;


        //echo $totalNotifications;
        $notificationArray = array(
            'badge' => $totalNotifications,
            'body' => $desc,
            'sound' => 'default',
            'title' => $title,
            'type' => $type,
            'card_id' => $card_id,
        );
        //print_r($notificationArray);
        /* if ($deviceType == 'Iphone') {
            $fields = array(
                'registration_ids' => $deviceToken,
                'data' => $message,
                'notification' => $notificationArray,
            );
        } else {
            $fields = array(
                'registration_ids' => $deviceToken,
                'data' => $message,                
            );
        } */

        if ($deviceType == 'Iphone') {
            $fields = array(
                'registration_ids' => $devicetoken,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        } else {
            $fields = array(
                'registration_ids' => $devicetoken,
                'data' => $message,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        }

        $fieldsJson = json_encode($fields);
        $fieldsJson = str_replace('\"', '', $fieldsJson);

        if ($deviceType == 'Iphone') {
            /* New update code for ios device */
            //$fields='{"priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "id": "1", "status": "done","body": "'.$notificationText.'","title": "'.$title.'"}, "to": "'.$deviceToken.'"}';
            $fieldsJson =  '{"to":"' . $deviceToken . '","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","type":"' . $type . '","card_id":"' . $card_id . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","type":"' . $type . '","card_id":"' . $card_id . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
        } else {
            /* New update code for android device */
            //$fields='{"notification": {"body": "'.$notificationText.'","title": "'.$title.'"}, "priority": "high", "data": {"click_action": "FLUTTER_NOTIFICATION_CLICK", "id": "1", "status": "done"}, "to": "'.$deviceToken.'"}';
            $fieldsJson =  '{"to":"' . $deviceToken . '","content_available": true,"mutable_content":true,"priority":"high","data":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","type":"' . $type . '","card_id":"' . $card_id . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"},"notification":{"body":"' . mb_convert_encoding($notificationText, 'HTML-ENTITIES', "UTF-8") . '","sound":true,"title":"' . $title . '","type":"' . $type . '","card_id":"' . $card_id . '","click_action":"FLUTTER_NOTIFICATION_CLICK","priority":"high"}}';
        }

        $headers = array(
            'Authorization: key=' . env('GOOGLE_API_KEY'),
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsJson);
        $result = curl_exec($ch);
        if ($result === FALSE) {

            DB::table('api_logs')->insert([
                'user_id' => '0',
                'api_name' => 'Notification-Curl Failed',
                'request_data' => $fieldsJson,
                'response_data' => $result,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s")
            ]);

            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);

        DB::table('api_logs')->insert([
            'user_id' => '0',
            'api_name' => 'Notification',
            'request_data' => $fieldsJson,
            'response_data' => $result,
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s")
        ]);

        return $result;
    }

    /**
     * Get user ip address from header
     */
    public function getIpAddress()
    {
        $headers = apache_request_headers();
        $headers = array_change_key_case($headers, CASE_LOWER);
        if (isset($headers['user_ipaddress'])) {
            $user_ipaddress = $headers['user_ipaddress'];
        } else {
            $user_ipaddress = "0";
        }
        return $user_ipaddress;
    }

    # SORT ARRAY BY KEY
    function aasort(&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        asort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = array_values($ret);
        return $array;
    }

    # SORT ARRAY BY KEY DESC
    public function asortReverse(&$array, $key)
    {
        $sorter = array();
        $ret = array();
        reset($array);
        foreach ($array as $ii => $va) {
            $sorter[$ii] = $va[$key];
        }
        arsort($sorter);
        foreach ($sorter as $ii => $va) {
            $ret[$ii] = $array[$ii];
        }
        $array = array_values($ret);
        return $array;
    }

    public function encrypt($data)
    {
        $this->ivLength = openssl_cipher_iv_length(env("CIPHERMETHOD"));

        $decodedKey = base64_decode(env("SECRET"));

        $iv = base64_encode(openssl_random_pseudo_bytes($this->ivLength));
        $iv = substr($iv, 0, $this->ivLength);

        $encryptedData = openssl_encrypt($data, env("CIPHERMETHOD"), $decodedKey, 0, $iv);

        return base64_encode($encryptedData . env("SEPARATOR") . $iv);
    }

    public function decrypt(string $data)
    {
        $this->ivLength = openssl_cipher_iv_length(env("CIPHERMETHOD"));

        $decodedKey = base64_decode(env("SECRET"));

        list($encryptedData, $iv) = explode(env("SEPARATOR"), base64_decode($data), 2);

        $iv = substr($iv, 0, $this->ivLength);

        return openssl_decrypt($encryptedData, env("CIPHERMETHOD"), $decodedKey, 0, $iv);
    }

    public function check_password_samenumber($password)
    {
        $password_arr = str_split($password);

        $lenght = strlen($password);
        $sum = 0;
        for ($i = 0; $i < $lenght; $i++) {
            $sum += $password_arr[$i];
        }

        if ($sum == $lenght * $password_arr[1]) {
            return true;
        }

        return false;
    }

    /* public function check_password_sequence($password, $max) {
        $j = 1;
        $lenght = strlen($password);
        for($i = 0; $i < $lenght; $i++) {
            if(isset($password[$i+1]) && ord($password[$i]) + 1 === ord($password[$i+1])) {
                $j++;
            } else {
                $j = 0;
            }
    
            if($j === $max) {
                return true;
            }
        }
    
        return false;
    } */

    public function check_password_sequence($str, $n_chained_expected = "6")
    {
        $chained = 1;

        for ($i = 1; $i < strlen($str); $i++) {
            if ($str[$i] == ($str[$i - 1] + 1)) {
                $chained++;
                if ($chained >= $n_chained_expected)
                    return true;
            } else {
                $chained = 1;
            }
        }
        return false;
    }

    public function stsToken()
    {
        //$credentials = new Aws\Credentials\Credentials(env('S3_ACCESS_KEY_ID1'), env('S3_SECRET_ACCESS_KEY1'));
        $stsoptions = [
            'region' => env('S3_DEFAULT_REGION'),
            'version' => 'latest',
            'signature_version' => 'v4'
        ];
        $stsClient = new StsClient($stsoptions);
        $result = $stsClient->getSessionToken();
        $result_arr = $result->toArray();
        return $result_arr;
        //return array();
        //$response['aws_sts_response'] = $result_arr;
    }

    public function getImageUsingSts($image, $sts_arr)
    {

        //$result = unserialize($sts_arr);
        if (!empty($sts_arr)) {
            $result = unserialize($sts_arr);
        } else {
            $stsoptions = [
                'region' => env('S3_DEFAULT_REGION'),
                'version' => 'latest',
                'signature_version' => 'v4'
            ];
            $stsClient = new StsClient($stsoptions);
            $result = $stsClient->getSessionToken();
            $result = $result->toArray();
        }

        $options = [
            'region' => env('S3_DEFAULT_REGION'),
            'version' => 'latest',
            'credentials' =>  [
                'key'    => $result['Credentials']['AccessKeyId'],
                'secret' => $result['Credentials']['SecretAccessKey'],
                'token'  => $result['Credentials']['SessionToken']
            ]
        ];

        $s3Client = new S3Client($options);
        //Get a command to GetObject
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => env('AWS_BUCKET'),
            'Key'    => $image
        ]);

        //The period of availability
        $request = $s3Client->createPresignedRequest($cmd, '+15 minutes');
        $signedUrl = (string)$request->getUri();
        return $signedUrl;
    }
}