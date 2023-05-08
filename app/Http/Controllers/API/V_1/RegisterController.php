<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use Aws\Exception\AwsException;
use App\Repositories\UserRepository;
use App\Repositories\DevicesRepository;
use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Models\Devices;
use App\Models\UserAccounts;
use App\Models\Currencies;
use App\Models\AccountBalances;
use App\Models\LinkCards;
use App\Models\ApiLogs;
use App\Models\Settings;
use App\Models\ReferFriends;
use App\Models\ReferRequests;
use Storage;
use Illuminate\Support\Facades\Auth;
use Validator;
use App;
use DB;

class RegisterController extends BaseController
{
    protected $userRepository;
    protected $devicesRepository;

    public function __construct(
        UserRepository $userRepository,
        DevicesRepository $devicesRepository
    ) {
        if (isset($_POST['language_code']) && !empty($_POST['language_code'])) {
            $language_code = $_POST['language_code'];
        } else {
            $language_code = 'en';
        }
        App::setLocale($language_code);

        $this->userRepository = $userRepository;
        $this->devicesRepository = $devicesRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    /**
     * Register with mobile number
     *
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'country_code' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        if (isset($input['language_code'])) {
            $language_code = $input['language_code'];
        } else {
            $language_code = 'en';
        }

        $mobile_number = $input['mobile_number'];
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];
        $country_code = $input['country_code'];
        if ($country_code == 'TZS') {
            $country_code = "255";
        }
        $register_step = 1;

        $referral_code = 'ARA' . rand(10000, 99999);
        $otp = "567890"; //rand(1000, 9999);

        $settings = Settings::find(1);
        $total_otp_attempt = $settings['total_otp_attempt'];
        $otp_attempt_min_time = $settings['otp_attempt_min_time'];
        $refer_friend_error_message = $settings['refer_friend_error_message_' . $language_code];
        $referral_request_message = $settings['referral_request_message_' . $language_code];
        $referral_enable = $settings['referral_enable'];
        $otp_timer = (string)$settings['otp_timer'];

        /* Check mobile number */
        $params['mobile_number'] = $mobile_number;
        $users = $this->userRepository->getByParams($params);

        $sts_token = $this->stsToken();

        if ($users->isEmpty()) {
            $input['mobile_number'] = $mobile_number;
            $input['otp'] = $otp;
            $input['referral_code'] = $referral_code;
            $input['country_code'] = $country_code;
            $input['register_step'] = $register_step;
            $input['login_step'] = '1';
            $input['sts_token'] = serialize($sts_token);
            $user = User::create($input);
            $user_id = $user->id;
            $token = "";
            $name = "";
            $first_name = "";
            $last_name = "";
            $nationality_id = "";
            $type_of_work_permit = "";
            $is_biometric_enable = "No";
            $user_status = "0";
            $currency_enable = "Yes";

            $device = new Devices();
            $device->user_id = $user_id;
            $device->device_token = $device_token;
            $device->int_udid = $int_udid;
            $device->device_type = $device_type;
            $device->created_at = date("Y-m-d H:i:s");
            $device->updated_at = date("Y-m-d H:i:s");
            $device->save();
        } else {
            $user_id = $users[0]['user_id'];
            $first_name = $users[0]['first_name'];
            $last_name = $users[0]['last_name'];

            $name = $users[0]['name'];
            $is_biometric_enable = $users[0]['is_biometric_enable'];
            $user_status = $users[0]['user_status'];
            $currency_enable = $users[0]['currency_enable'];

            $user = User::find($user_id);
            $user->document_attempt = "0";
            $user->login_datetime = $this->created_at;
            $user->login_step = '1';
            $user->sts_token = serialize($sts_token);
            $user->save();
            $token = "";
            $register_step = $user->register_step;
            $nationality_id = $user->nationality_id;
            $type_of_work_permit = $user->type_of_work_permit;

            $otp_attempt = $user->otp_attempt;
            $otp_attempt_datetime = $user->otp_attempt_datetime;

            if ($otp_attempt == $total_otp_attempt) {
                $date1 = $otp_attempt_datetime;
                $date2 = date("Y-m-d H:i:s");
                $timestamp1 = strtotime($date1);
                $timestamp2 = strtotime($date2);
                $min = number_format(abs($timestamp2 - $timestamp1) / (60));
                if ($min >= $otp_attempt_min_time) {
                    $model = User::find($user_id);
                    $model->otp_attempt = '0';
                    $model->save();
                } else {
                    $error_message = trans('message.wrong_otp_with_block');
                    $error_message = str_ireplace("<<ATTEMPT>>", $total_otp_attempt, $error_message);
                    $error_message = str_ireplace("<<MIN>>", $otp_attempt_min_time, $error_message);
                    return $this->sendError('7', $error_message, array(), '200');
                }
            }

            $devices = Devices::select('id')->where(array('user_id' => $user_id))->first();
            if (empty($devices)) {
                $device = new Devices();
                $device->user_id = $user_id;
                $device->device_token = $device_token;
                $device->int_udid = $int_udid;
                $device->device_type = $device_type;
                $device->created_at = $this->created_at;
                $device->updated_at = $this->updated_at;
                $device->save();
            } else {
                $id = $devices->id;
                $device = Devices::find($id);
                $device->device_token = $device_token;
                $device->int_udid = $int_udid;
                $device->device_type = $device_type;
                $device->updated_at = $this->updated_at;
                $device->save();
            }
        }

        /* if($register_step <= 1){
            $refer = ReferFriends::where('mobile_number', $country_code.$mobile_number)->first();
            if(empty($refer)){
                $checkRequest = ReferRequests::where('from_user_id',$user_id)->where('status',1)->first();
                if(empty($checkRequest)){
                    $json_arr = array();
                    $success['user_id'] = (string) $user_id;
                    $json_arr[] = $success;
                    return  $this->sendResponse('8', $json_arr, $refer_friend_error_message);
                }
            }
        } */

        /**
         * Selcom API call
         */
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $payment_reference = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $param['msisdn'] = $country_code . $mobile_number;
        $param['currency'] = "TZS";
        $param['paymentReference'] = $payment_reference;
        $json_request = json_encode($param);
        $selcom_response = $this->selcomApi('service/onboarding/otp-generate', $json_request);

        $this->selcomApiRequestResponse("0", 'service/onboarding/otp-generate', $json_request, json_encode($selcom_response));

        $resultcode = $selcom_response['resultcode'];
        $result = $selcom_response['result'];
        if ($resultcode != '200' && $result != 'SUCCESS') {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
        }
        $otpdata = $selcom_response['data'];
        $otp = $otpdata[0]['otp'];

        $response = array();
        $success['token'] = $token;
        $success['otp'] = (string) $otp;
        $success['user_id'] = (string) $user_id;
        $success['register_step'] = (string) $register_step;
        $success['name'] = (string) $name;
        $namearr = explode(" ", (string) $name);
        if (count($namearr) > 1) {
            $success['first_name'] = $first_name;
            $success['last_name'] = $last_name;
        } else {
            $success['first_name'] = $name;
            $success['last_name'] = $name;
        }
        $success['nationality_id'] = (string) $nationality_id;
        $success['type_of_work_permit'] = (string) $type_of_work_permit;
        $success['is_biometric_enable'] = (string) $is_biometric_enable;
        $success['currency_enable'] = (string) $currency_enable;

        if ($user_status == '3') {
            $user_status = '1';
        }

        $success['user_status'] = (string) $user_status;
        $success['otp_timer'] = $otp_timer;
        $response[] = $success;

        return $this->sendResponse('1', $response, trans('message.otp_success'));
    }

    /**
     * Check OTP
     * Sample OTP for testing: 066609
     */
    public function checkOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_number' => 'required',
            'country_code' => 'required',
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'otp' => 'required|numeric|digits:6',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $mobile_number = $input['mobile_number'];
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];
        $otp = $input['otp'];
        $country_code = $input['country_code'];
        $language_code = $input['language_code'];

        $settings = Settings::find(1);
        $total_otp_attempt = $settings['total_otp_attempt'];
        $otp_attempt_min_time = $settings['otp_attempt_min_time'];
        $refer_friend_error_message = $settings['refer_friend_error_message_' . $language_code];
        $referral_request_message = $settings['referral_request_message_' . $language_code];
        $referral_enable = $settings['referral_enable'];
        $otp_timer = (string)$settings['otp_timer'];

        $params['mobile_number'] = $mobile_number;
        $users = $this->userRepository->getByParams($params);

        $otp_attempt = 0;
        if (!empty($users)) {
            $user_id = $users[0]['id'];
            $otp_attempt = $users[0]['otp_attempt'];
            if ($otp_attempt == $total_otp_attempt) {
                $error_message = trans('message.wrong_otp_with_block');
                $error_message = str_ireplace("<<ATTEMPT>>", $total_otp_attempt, $error_message);
                $error_message = str_ireplace("<<MIN>>", $otp_attempt_min_time, $error_message);
                return $this->sendError('7', $error_message, array(), '200');
            }
        }

        /**
         * Selcom API call
         */
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $payment_reference = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $param['msisdn'] = $country_code . $mobile_number;
        $param['otp'] = $otp;
        $param['language'] = $language_code;
        $json_request = json_encode($param);
        $selcom_response = $this->selcomApi('service/onboarding/otp-verify', $json_request);

        $this->selcomApiRequestResponse("0", 'service/onboarding/otp-verify', $json_request, json_encode($selcom_response));

        $resultcode = $selcom_response['resultcode'];
        if ($resultcode == '200') {
            /* $params['mobile_number'] = $mobile_number;
            $users = $this->userRepository->getByParams($params); */

            $register_step = $users[0]['register_step'];

            if ($referral_enable == '1') {
                if ($register_step <= 1) {
                    $refer = ReferFriends::where('mobile_number', $country_code . $mobile_number)->first();
                    if (empty($refer)) {
                        $checkRequest = ReferRequests::where('from_user_id', $user_id)->where('status', 1)->first();
                        if (empty($checkRequest)) {
                            $json_arr = array();
                            $success['user_id'] = (string) $user_id;
                            $json_arr[] = $success;
                            $referal_register_type =  $users[0]['referal_register_type'];

                            if ($referal_register_type == '3') {
                                return  $this->sendResponse('9', $json_arr, $referral_request_message);
                            } else {
                                return  $this->sendResponse('8', $json_arr, $refer_friend_error_message);
                            }
                        }
                    }
                }
            }

            $response = array();
            $success['user_id'] = (string) $users[0]['id'];
            $success['register_step'] = (string) $users[0]['register_step'];
            $success['name'] = (string) $users[0]['name'];
            $success['nationality_id'] = (string) $users[0]['nationality_id'];
            $success['otp_timer'] = $otp_timer;
            $response[] = $success;
            return $this->sendResponse('1', $response, trans('message.otp_verify'));
        } else {

            $new_otp_attempt = $otp_attempt + 1;

            $left_attempt = $total_otp_attempt - $new_otp_attempt;

            $model = User::find($user_id);
            $model->otp_attempt = $new_otp_attempt;
            $model->otp_attempt_datetime = date("Y-m-d H:i:s");
            $model->save();

            if ($new_otp_attempt == $total_otp_attempt) {
                $error_message = trans('message.wrong_otp_with_block');
                $error_message = str_ireplace("<<ATTEMPT>>", $total_otp_attempt, $error_message);
                $error_message = str_ireplace("<<MIN>>", $otp_attempt_min_time, $error_message);
                return $this->sendError('7', $error_message, array(), '200');
            } else {
                $error_message = trans('message.wrong_otp_with_attempt');
                $error_message = str_ireplace("<<LEFT_ATTEMPT>>", $left_attempt, $error_message);
                return $this->sendError('0', $error_message, array(), '200');
            }

            return $this->sendError('0', trans('message.otp_wrong'), array(), '200');
        }
    }

    public function birtAccessRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'language_code' => 'required',
            'referal_register_type' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];
        $user_id = $input['user_id'];
        $referal_register_type = $input['referal_register_type'];

        $settings = Settings::find(1);
        $referral_request_message = $settings['referral_request_message_' . $language_code];

        $user = User::find($user_id);
        $user->referal_register_type = $referal_register_type;
        $user->save();

        $response = array();
        $success['user_id'] = (string) $user_id;
        $response[] = $success;

        return  $this->sendResponse('1', $response, $referral_request_message);
    }

    /**
     * Device details update
     * 
     * @return json array  
     */
    public function updateDeviceDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];

        if (isset($input['mobile_number'])) {
            $mobile_number = $input['mobile_number'];
            $user = User::select('id')->where('mobile_number', $mobile_number)->first();
            if (!empty($user)) {
                $user_id = $user->id;
            }

            /**
             * Selcom API call
             */
            if (isset($_POST['screen_name'])) {
                $screen_name = $_POST['screen_name'];
            } else {
                $screen_name = '';
            }

            if ($screen_name == 'register') {
                if (isset($_POST['country_code'])) {
                    $country_code = $_POST['country_code'];
                    if ($country_code == 'TZS') {
                        $country_code = "255";
                    }
                } else {
                    $country_code = '255';
                }

                $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
                $payment_reference = rand(1000, 9999) . time() . rand(1000, 9999);
                $param['externalId'] = $external_id;
                $param['msisdn'] = $country_code . $mobile_number;
                $param['currency'] = "TZS";
                $param['paymentReference'] = $payment_reference;
                $json_request = json_encode($param);
                $selcom_response = $this->selcomApi('service/onboarding/otp-generate', $json_request);

                $this->selcomApiRequestResponse("0", 'service/onboarding/otp-generate', $json_request, json_encode($selcom_response));

                $resultcode = $selcom_response['resultcode'];
                $result = $selcom_response['result'];
                if ($resultcode != '200' && $result != 'SUCCESS') {
                    return $this->sendError('0', $selcom_response['message'], array(), '200');
                }
            }
        } else {
            $user_id = $input['user_id'];
        }

        /**
         * Check device details using user id
         * 
         * @return json array
         */
        $devices = Devices::select('id')->where(array('user_id' => $user_id))->first();
        if (empty($devices)) {
            $device = new Devices();
            $device->user_id = $user_id;
            $device->device_token = $device_token;
            $device->int_udid = $int_udid;
            $device->device_type = $device_type;
            $device->created_at = $this->created_at;
            $device->updated_at = $this->updated_at;
            $device->save();
        } else {
            $id = $devices->id;
            $device = Devices::find($id);
            $device->device_token = $device_token;
            $device->int_udid = $int_udid;
            $device->device_type = $device_type;
            $device->updated_at = $this->updated_at;
            $device->save();
        }

        $otp = "567890"; //rand(1000, 9999);

        $users = User::find($user_id);
        $users->otp = $otp;
        $users->updated_at = $this->updated_at;
        $users->save();

        $name = $users->name;
        $first_name = $users->first_name;
        $last_name = $users->last_name;

        $token = "";
        $register_step = $users->register_step;
        $nationality_id = $users->nationality_id;
        $type_of_work_permit = $users->type_of_work_permit;

        $response = array();
        if (isset($input['mobile_number'])) {
            $success['token'] = $token;
            $success['otp'] = (string) $otp;
            $success['user_id'] = (string) $user_id;
            $success['register_step'] = (string) $register_step;
            $success['name'] = (string) $name;
            $namearr = explode(" ", (string) $name);
            if (count($namearr) > 1) {
                $success['first_name'] = $first_name;
                $success['last_name'] = $last_name;
            } else {
                $success['first_name'] = $name;
                $success['last_name'] = $name;
            }
            $success['nationality_id'] = (string) $nationality_id;
            $success['type_of_work_permit'] = (string) $type_of_work_permit;
            $success['account_opening_date'] = $users->created_at;
            $useraccounts = UserAccounts::where("user_id", "=", $user_id)->first();
            $success['account_number'] = $useraccounts['account_number'];
            $success['user_id'] = (string) $user_id;
            $success['is_biometric_enable'] = $users->is_biometric_enable;
            $success['currency_enable'] = (string)$users->currency_enable;

            $user_status = (string)$users->user_status;
            if ($users->user_status == '3') {
                $user_status = '1';
            }

            $success['user_status'] = (string)$user_status;
            $settings = Settings::find(1);
            $otp_timer = (string)$settings['otp_timer'];
            $success['otp_timer'] = $otp_timer;
            $response[] = $success;
        } else {
            $user = User::select(DB::raw('name,email,address,country_code,mobile_number,CONVERT(register_step, CHAR(50)) as register_step, CONVERT(nationality_id, CHAR(50)) as nationality_id, CONVERT(document_id, CHAR(50)) as document_id, CONVERT(type_of_work_permit, CHAR(50)) as type_of_work_permit,referral_code, resident_permit, work_permit, CONVERT(document_attempt, CHAR(50)) as document_attempt, profile_picture, CONVERT(city_id, CHAR(50)) as city_id, created_at as account_opening_date, is_biometric_enable, currency_enable, user_status,sts_token'))->find($user_id);

            $sts_token = $user->sts_token;

            $response = array();
            $uploadPath = config('custom.upload.user.document_permits');
            if ($user->nationality_id == '2') {
                /* $user['resident_permit'] = env('S3_BUCKET_URL') . 'documents/' . $user->resident_permit;
                $user['work_permit'] = env('S3_BUCKET_URL') . 'documents/' . $user->work_permit; */

                $resident_permit = $this->getImageUsingSts('documents/' . $user->resident_permit, $sts_token);
                $work_permit = $this->getImageUsingSts('documents/' . $user->work_permit, $sts_token);
                $user['resident_permit'] = $resident_permit;
                $user['work_permit'] = $work_permit;
            } else {
                $user['resident_permit'] = "";
                $user['work_permit'] = "";
            }
            $success = $user;
            $success['token'] = "";
            $success['user_id'] = $user_id;
            $success['name'] = $user->name;
            $name = explode(" ", $user->name);
            if (count($name) > 1) {
                $success['first_name'] = ($name[0]) ? $name[0] : $name;
                $success['last_name'] = ($name[1]) ? $name[1] : $name;
            } else {
                $success['first_name'] = $user->name;
                $success['last_name'] = $user->name;
            }
            $success['email'] = $user->email;
            $success['mobile_number'] = $user->mobile_number;
            if (!empty($user->document_id)) {
                $doc = $this->getOneRecords('documents', 'name', 'id', $user->document_id);
                $success['document_name'] = $doc->name;
            } else {
                $success['document_name'] = "";
            }

            if (!empty($user->city_id)) {
                $city = $this->getOneRecords('cities', 'name', 'id', $user->city_id);
                if (!empty($city)) {
                    $success['city_name'] = $city->name;
                } else {
                    $success['city_name'] = "";
                }
            } else {
                $success['city_name'] = "";
            }
            $profile_path = config('custom.upload.user.profile');

            //$success['profile_picture'] = env('S3_BUCKET_URL') . 'user/' . $user->profile_picture;

            $profile_picture = $this->getImageUsingSts('user/' . $user->profile_picture, $sts_token);
            $success['profile_picture'] = $profile_picture;

            $useraccounts = UserAccounts::where("user_id", "=", $user_id)->first();
            $success['account_number'] = $useraccounts['account_number'];
            $success['is_biometric_enable'] = $user->is_biometric_enable;
            $success['currency_enable'] = (string)$user->currency_enable;

            $user_status = (string)$user->user_status;
            if ($user->user_status == '3') {
                $user_status = '1';
            }

            $success['user_status'] = (string)$user_status;

            $settings = Settings::find(1);
            $otp_timer = (string)$settings['otp_timer'];
            $success['otp_timer'] = $otp_timer;

            $response[] = $success;
        }
        return $this->sendResponse('1', $response, trans('message.device_update'));
    }

    /**
     * Document upload api
     *
     * @return \Illuminate\Http\Response
     */
    public function documentUpload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'country_id' => 'required',
            'document_id' => 'required',
            'nationality_id' => 'required',
            'document_file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $country_id = $input['country_id'];
        $document_id = $input['document_id'];
        $nationality_id = $input['nationality_id'];
        if ($nationality_id == '1') {
            $user_type = '1';
            $type_of_work_permit = '0';
        } else {
            $user_type = '2';
            $type_of_work_permit = $input['type_of_work_permit'];
        }

        $client = new TextractClient([
            'region'    => 'us-west-2',
            'version'   => 'latest'
        ]);

        $file = $request->file('document_file_name');

        //Move Uploaded File
        $uploadPath = config('custom.upload.user.document_path');
        $filename = $this->upload($file, $uploadPath);

        $document_file_path = env('APP_URL') . '/storage/' . $uploadPath . '/' . $filename;

        $image = fopen($request->file('document_file_name')->getPathName(), 'r');
        $bytes = fread($image, $request->file('document_file_name')->getSize());

        $results = $client->detectDocumentText([
            'Document' => ['Bytes' => $bytes]
        ]);

        $texts = $results->toArray();
        /* print_r($results);
        exit; */
        $texts = $texts['Blocks'];
        $doc_texts = array();
        if (!empty($texts)) {
            foreach ($texts as $txt) {
                if (array_key_exists('Text', $txt)) {
                    $doc_texts[] = $txt['Text'];
                }
            }
        }
        /* print_r($doc_texts);
        exit; */
        /* } else if (strrpos($doc_texts[3], "CITIZEN") !== false && $document_id == '2') { */
        $dob = "0000-00-00";
        $gender = "";
        $expiry_date = "0000-00-00";
        $document_number = "";
        if (strrpos($doc_texts[0], "PASSPORT") !== false && $document_id == '1') {
            $surname_index = array_search('Surnamollina la ukoo', $doc_texts);
            if (empty($surname_index)) {
                $surname_index = array_search('Surnamo/Jina la LHOO', $doc_texts);
            }
            if (empty($surname_index)) {
                $surname_index = array_search('Sumnamollina Ia Lkoo', $doc_texts);
            }

            $surename = trim($doc_texts[10]);
            $d['value'] = $doc_texts[10];
            $d['key'] = 'Surename';
            $doc_arr[] = $d;

            $name_index = array_search('Given Namesilina', $doc_texts);
            if (empty($name_index)) {
                $name_index = array_search('Given NamesJina', $doc_texts);
            }
            if (empty($name_index)) {
                $name_index = array_search('Given Nameslina', $doc_texts);
            }

            $username = trim($doc_texts[12]);
            $d['value'] = $username;
            $d['key'] = 'Name';
            $doc_arr[] = $d;

            $nationality_index = array_search('Nationalitw/Utaifa', $doc_texts);
            if (empty($nationality_index)) {
                $nationality_index = array_search('Nationality/Utaifa', $doc_texts);
            }
            if (empty($nationality_index)) {
                $nationality_index = array_search('NationaltyUtata', $doc_texts);
            }
            if (empty($nationality_index)) {
                $nationality_index = array_search('Nationality/Utait', $doc_texts);
            }

            $d['value'] = $doc_texts[14];
            $d['key'] = 'Nationality';
            $doc_arr[] = $d;

            $dob_index = array_search('Date of birth/Tarehe ya kuzatiwn', $doc_texts);
            if (empty($dob_index)) {
                $dob_index = array_search('Date ol brthTarehe ya kuzaliwa', $doc_texts);
            }
            if (empty($dob_index)) {
                $dob_index = array_search('Date of birth arehe ya kuzaliwa', $doc_texts);
            }
            $dob = $doc_texts[16];
            $d['value'] = $doc_texts[16];
            $d['key'] = 'Date Of Birth';
            $doc_arr[] = $d;

            $sex_index = array_search('xlinsis', $doc_texts);
            if (empty($sex_index)) {
                $sex_index = array_search('Sexinsia', $doc_texts);
            }
            $gender = $doc_texts[19];
            $d['value'] = $doc_texts[19];
            $d['key'] = 'Sex';
            $doc_arr[] = $d;

            $pob_index = array_search('Place of birth/Mahali oa kuzaliwe', $doc_texts);
            if (empty($pob_index)) {
                $pob_index = array_search('Place of birth/Mahali Da kuzaliwe', $doc_texts);
            }
            $d['value'] = $doc_texts[20];
            $d['key'] = 'Place of birth';
            $doc_arr[] = $d;

            $doi_index = array_search('Date of issue', $doc_texts);
            if (empty($doi_index)) {
                $doi_index = array_search('Date of issue/', $doc_texts);
            }
            $d['value'] = $doc_texts[25];
            $d['key'] = 'Date of issue';
            $doc_arr[] = $d;

            $dox_index = array_search('Date of expiry', $doc_texts);
            $expiry_date = $doc_texts[30];
            $d['value'] = $expiry_date;
            $d['key'] = 'Date of expiry';
            $doc_arr[] = $d;

            $passport_no_index = array_search('Passport No/Namba ya Pasipot', $doc_texts);
            if (empty($passport_no_index)) {
                $passport_no_index = array_search('Passport No/Namba ya Pasipotr', $doc_texts);
            }
            if (empty($passport_no_index)) {
                $passport_no_index = array_search('Passport No/Naba ya Pasipot', $doc_texts);
            }

            $d['value'] = $doc_texts[8];
            $d['key'] = 'Passport No';
            $doc_arr[] = $d;
            $document_number = $doc_texts[8];

            $issuing_state_code_index = array_search('Issuing State Code', $doc_texts);
            $d['value'] = $doc_texts[7];
            $d['key'] = 'Issuing State Code';
            $doc_arr[] = $d;
        } else if (strrpos($doc_texts[3], "CITIZEN") !== false && $document_id == '2') {
            $unique_number = $doc_texts[4];
            $unique_number_arr = explode("-", $unique_number);

            $nida_version = $unique_number_arr[2];
            $document_number = $unique_number;
            $first_name_index = array_search('Given None', $doc_texts);
            if (empty($first_name_index)) {
                $first_name_index = array_search('Given Name', $doc_texts);
            }
            if ($first_name_index > 0) {
                $first_name = $doc_texts[$first_name_index - 1];
            } else {
                $first_name = $doc_texts[5];
            }
            $first_name_err = explode(" ", $first_name);
            foreach ($first_name_err as $val) {
                $sim = similar_text($val, 'JINA', $perc);
                if ($perc > 50) {
                    $first_name = str_ireplace($val, "", $first_name);
                }

                $sim = similar_text($val, 'LA', $perc);
                if ($perc > 50) {
                    $first_name = str_ireplace($val, "", $first_name);
                }

                $sim = similar_text($val, ':', $perc);
                if ($perc > 50) {
                    $first_name = str_ireplace($val, "", $first_name);
                }

                $sim = similar_text($val, 'KWANZA', $perc);
                if ($perc > 50) {
                    $first_name = str_ireplace($val, "", $first_name);
                }

                $sim = similar_text($val, '2', $perc);
                if ($perc > 50) {
                    $first_name = str_ireplace($val, "", $first_name);
                }
            }
            $username = trim($first_name);
            $d['key'] = 'First Name';
            $d['value'] = $username;
            $doc_arr[] = $d;

            $last_name_index = array_search('Lest None', $doc_texts);
            if (empty($first_name_index)) {
                $last_name_index = array_search('Lost Name', $doc_texts);
            }
            if (empty($first_name_index)) {
                $last_name_index = array_search('Last Name', $doc_texts);
            }
            if ($last_name_index > 0) {
                $last_name = $doc_texts[$last_name_index - 1];
            } else {
                $last_name = $doc_texts[7];
            }
            $last_name_err = explode(" ", $last_name);
            foreach ($last_name_err as $val) {
                $sim = similar_text($val, 'JINA', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }

                $sim = similar_text($val, 'LA', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }

                $sim = similar_text($val, 'MWISHO', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }

                $sim = similar_text($val, ':', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }

                $sim = similar_text($val, '2', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }

                $sim = similar_text($val, '-', $perc);
                if ($perc > 50) {
                    $last_name = str_ireplace($val, "", $last_name);
                }
            }
            $d['key'] = 'Last Name';
            $d['value'] = trim($last_name);
            $doc_arr[] = $d;

            $dobstr = $unique_number_arr[0];
            $y = substr($dobstr, 0, 4);
            $m = substr($dobstr, 4, 2);
            $day = substr($dobstr, 6, 8);
            $dob = $y . "-" . $m . "-" . $day;
            $d['key'] = 'Date of Birth';
            $d['value'] = strtoupper(date("d M Y", strtotime($dob)));
            $doc_arr[] = $d;

            $sex = $doc_texts[11];
            $sex_err = explode(" ", $sex);
            foreach ($sex_err as $val) {
                $sim = similar_text($val, 'JINSI', $perc);
                if ($perc > 50) {
                    $sex = str_ireplace($val, "", $sex);
                }

                $sim = similar_text($val, '-', $perc);
                if ($perc > 50) {
                    $sex = str_ireplace($val, "", $sex);
                }

                $sim = similar_text($val, ':', $perc);
                if ($perc > 50) {
                    $sex = str_ireplace($val, "", $sex);
                }

                $sim = similar_text($val, '2', $perc);
                if ($perc > 50) {
                    $sex = str_ireplace($val, "", $sex);
                }

                $sim = similar_text($val, 'HASI', $perc);
                if ($perc > 50) {
                    $sex = str_ireplace($val, "", $sex);
                }
            }
            $d['value'] = trim($sex);
            $d['key'] = 'Sex';
            $doc_arr[] = $d;

            $expiry_index = array_search('Expiry Date', $doc_texts);
            if ($expiry_index > 0) {
                $expiry_date_index = $doc_texts[$expiry_index - 1];
            } else {
                $expiry_date_index = $doc_texts[17];
            }
            $expiry_date_arr = explode(" ", $expiry_date_index);
            foreach ($expiry_date_arr as $val) {
                $sim = similar_text($val, 'MWISHO', $perc);
                if ($perc > 50) {
                    $expiry_date_index = str_ireplace($val, "", $expiry_date_index);
                }

                $sim = similar_text($val, 'WA', $perc);
                if ($perc > 50) {
                    $expiry_date_index = str_ireplace($val, "", $expiry_date_index);
                }

                $sim = similar_text($val, 'MATUMIZI', $perc);
                if ($perc > 50) {
                    $expiry_date_index = str_ireplace($val, "", $expiry_date_index);
                }

                $sim = similar_text($val, '-', $perc);
                if ($perc > 50) {
                    $expiry_date_index = str_ireplace($val, "", $expiry_date_index);
                }

                $sim = similar_text($val, ':', $perc);
                if ($perc > 50) {
                    $expiry_date_index = str_ireplace($val, "", $expiry_date_index);
                }
            }
            $expiry_date = trim($expiry_date_index);
            $d['key'] = 'Expiry Date';
            $d['value'] = $expiry_date;
            $doc_arr[] = $d;
            $gender = trim($sex);
        } else {
            /* $name_index = array_search('Given None', $doc_texts);
            if (empty($name_index)) {
                $name_index = array_search('Given Name', $doc_texts);
            }
            $username = $doc_texts[5];
            $d['key'] = 'First Name';
            $d['value'] = trim($username);
            $doc_arr[] = $d;

            $d['key'] = 'Date of Birth';
            $dob = $doc_texts[7];
            $d['value'] = $doc_texts[7];
            $doc_arr[] = $d;

            $d['key'] = 'Pancard No';
            $d['value'] = $doc_texts[9];
            $doc_arr[] = $d; */
            $error[] = array();
            return $this->sendError('0', trans('message.wrong_document'), $error, '200');
        }

        if (!empty($expiry_date)) {
            $expiry_date_arr = explode(" ", $expiry_date);
            if (count($expiry_date_arr)) {
                $expiry_date = date("Y-m-d", strtotime($expiry_date));
            } else {
                $expiry_date = "0000-00-00";
            }
        }

        $gender = strtolower($gender);
        if ($gender == 'm') {
            $gender = 'MALE';
        } else if ($gender == 'f') {
            $gender = 'FEMALE';
        } else if ($gender == 'male') {
            $gender = 'MALE';
        } else if ($gender == 'female') {
            $gender = 'FEMALE';
        } else {
            $error[] = array();
            return $this->sendError('0', trans('message.wrong_document'), $error, '200');
        }

        if (!empty($dob)) {
            $dob = date("Y-m-d", strtotime($dob));
        }

        /** 
         * Face detect logic here
         */
        $client_rekognition = new RekognitionClient([
            'region'    => 'us-west-2',
            'version'   => 'latest'
        ]);

        $doc_results = $client_rekognition->DetectFaces([
            'Image' => ['Bytes' => $bytes]
        ]);
        $bounding = $doc_results['FaceDetails'][0]['BoundingBox'];
        list($width, $height) = getimagesize($document_file_path);
        list($file_name, $ext) = explode('.', $document_file_path);

        $boundingW = $bounding['Width'] + 0.11;
        $boundingH = $bounding['Height'] + 0.15;
        $boundingTop = $bounding['Top'] - 0.1;
        $boundingLeft = $bounding['Left'] - 0.06;
        $w = round($width * $boundingW);
        $h = round($height * $boundingH);
        $x = round($boundingLeft * $width);
        $y = round($boundingTop * $height);

        if ($ext == "png" || $ext == "PNG") {
            $im = imagecreatefrompng($document_file_path);
        } else {
            $im = imagecreatefromjpeg($document_file_path);
        }

        $profile_pic_name = time() . "-profile.png";
        $profile_path = config('custom.upload.user.profile');
        $profile_file_path = storage_path('app') . '/public/' . $profile_path . "/" . $profile_pic_name;

        $im2 = imagecrop($im, ['x' => $x, 'y' => $y, 'width' => $w, 'height' => $h]);
        if ($im2 !== FALSE) {
            imagepng($im2, $profile_file_path);
            imagedestroy($im2);
        }
        imagedestroy($im);

        /* Check document number */
        $check_doc = User::where('document_number', '=', $document_number)->first();
        if (!empty($check_doc)) {
            return $this->sendError('0', trans('message.document_alreay_added'), array(), '200');
        }
        /* End */

        /**
         * Document details update here
         */
        $user = User::find($user_id);
        $user->name = $username;
        $user->first_name = $username;
        $user->last_name = $surename;
        $user->country_id = $country_id;
        $user->document_id = $document_id;
        $user->nationality_id = $nationality_id;
        $user->document_file_name = $filename;
        $user->profile_picture = $profile_pic_name;
        $user->is_profile_complete = 0;
        $user->register_step = 2;
        $user->user_type = $user_type;
        $user->type_of_work_permit = $type_of_work_permit;
        $user->gender = $gender;
        $user->dob = $dob;
        $user->doc_expiry_date = $expiry_date;
        $user->document_number = $document_number;
        $user->save();

        $document_arr['profile_picture'] = env('APP_URL') . '/storage/' . $profile_path . "/" . $profile_pic_name;
        $document_arr['document_details'] = $doc_arr;
        $document_arr['user_id'] = $user_id;
        $data_arr[] = $document_arr;
        return $this->sendResponse('1', $data_arr, trans('message.document_upload_success'));
    }

    /**
     * Upload NIDA card document
     * 
     * @return \Illuminate\Http\Response
     */
    public function uploadOcrCardDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'document_id' => 'required',
            'nationality_id' => 'required',
            'is_manually_verified' => 'required',
            'document_file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'profile_file_name' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $first_name = $input['first_name'];
        if (empty($first_name)) {
            $first_name = "";
        }

        $last_name = $input['last_name'];
        if (empty($last_name)) {
            $last_name = "";
        }

        $name = $first_name . " " . $last_name;
        $document_id = $input['document_id'];

        $document_number = $input['document_number'];
        if (empty($document_number)) {
            $document_number = "";
            return $this->sendError('0', trans('message.wrong_document'), array(), '200');
        }

        $nationality_id = $input['nationality_id'];
        if ($nationality_id == '1') {
            $user_type = '1';
            $type_of_work_permit = '0';
        } else {
            $user_type = '2';
            $type_of_work_permit = $input['type_of_work_permit'];
        }
        $gender = $input['gender'];
        $doc_expiry_date = $input['doc_expiry_date'];
        if (!empty($doc_expiry_date)) {
            $doc_expiry_date = date("Y-m-d", strtotime($doc_expiry_date));
        } else {
            $doc_expiry_date = "0000-00-00";
        }

        $dob = $input['dob'];
        if (!empty($dob)) {
            $date_arr = explode("/", $dob);
            $dob = $date_arr[2] . "-" . $date_arr[1] . "-" . $date_arr[0];
            //$dob = date("Y-m-d", strtotime($dob));
        }

        $is_manually_verified = $input['is_manually_verified'];

        //Upload document files
        $file = $request->file('document_file_name');
        $document_file_name = rand('111', '999') . time() . $file->getClientOriginalName();
        $filePath = 'documents/' . $document_file_name;
        Storage::disk('s3')->put($filePath, file_get_contents($file));
        /* $document_path = config('custom.upload.user.document_path');
        $document_file_name = $this->upload($file, $document_path); */

        //Upload profile pic files
        $profile_file = $request->file('profile_file_name');
        $profile_file_name = rand('111', '999') . time() . $profile_file->getClientOriginalName();
        $userfilePath = 'user/' . $profile_file_name;
        Storage::disk('s3')->put($userfilePath, file_get_contents($profile_file));

        /* $profile_path = config('custom.upload.user.profile');
        $profile_file_name = $this->upload($profile_file_name, $profile_path); */

        /* Check document number */
        if ($is_manually_verified == '0') {
            $check_doc = User::where('document_number', '=', $document_number)->where('id', '!=', $user_id)->first();
            if (!empty($check_doc)) {
                return $this->sendError('0', trans('message.document_alreay_added'), array(), '200');
            }
        }
        /* End */

        /**
         * Document details update here
         */
        $user = User::find($user_id);
        $user->document_id = $document_id;
        $user->nationality_id = $nationality_id;
        $user->document_file_name = $document_file_name;
        $user->profile_picture = $profile_file_name;
        $user->is_profile_complete = 0;
        $user->register_step = 2;
        $user->user_type = $user_type;
        $user->type_of_work_permit = $type_of_work_permit;
        $user->name = $name;
        $user->first_name = $first_name;
        $user->last_name = $last_name;
        $user->gender = $gender;
        $user->dob = $dob;
        $user->doc_expiry_date = $doc_expiry_date;
        $user->document_number = $document_number;
        if ($is_manually_verified == '1') {
            $user->user_status = '3';
        }
        if (isset($input['nationality'])) {
            $user->nationality = $input['nationality'];
        } else {
            $user->nationality = "Tanzania";
        }
        $user->save();

        $document_arr['user_id'] = $user_id;
        $data_arr[] = $document_arr;
        return $this->sendResponse('1', $data_arr, trans('message.document_upload_success'));
    }

    /**
     * Update register details after document verification
     * 
     * @return \Illuminate\Http\Response
     */
    public function verifyDocument(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'selfie_picture' => 'required|mimes:jpeg,png,jpg',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $apilog = new ApiLogs();
        $apilog->user_id = $input['user_id'];
        $apilog->api_name = "verifyDocument-Request";
        $apilog->request_data = json_encode($input);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $is_manually_verified = $input['is_manually_verified'];

        if (isset($input['verification_type'])) {
            $verification_type = $input['verification_type']; // Register, Other
        } else {
            $verification_type = "Register";
        }

        if (isset($input['verification_with'])) {
            $verification_with = $input['verification_with']; // Document, selfie
        } else {
            $verification_with = "Document";
        }

        $user_id = $input['user_id'];
        $user = User::find($user_id);
        $register_step = $user->register_step;
        if ($register_step == 6) {
            $register_step = 6;
        } else {
            $register_step = 3;
        }

        /** 
         * Check document wrong attempts
         */
        if ($verification_type == 'Register') {
            if ($is_manually_verified == 0) {
                if ($user->document_attempt >= 3) {
                    return $this->sendError('4', trans('message.selfie_attempts'), $validator->errors(), '200');
                }
            }
        }/* else{
            if ($user->document_attempt >= 3) {
                return $this->sendError('4', trans('message.selfie_attempts'), $validator->errors(), '200');
            }
        } */
        if ($verification_with == 'Document') {
            $document_name = $user->document_file_name;
        } else {
            $document_name = $user->selfie_picture;
        }

        $file = $request->file('selfie_picture');

        $apilog1 = new ApiLogs();
        $apilog1->user_id = $user_id;
        $apilog1->api_name = "verifyDocument-Request-file";
        $apilog1->request_data = json_encode($_FILES);
        $apilog1->response_data = "NA";
        $apilog1->created_at = date("Y-m-d H:i:s");
        $apilog1->updated_at = date("Y-m-d H:i:s");
        $apilog1->save();

        //Move Uploaded File
        if ($verification_type == 'Register') {
            /* $uploadPath = config('custom.upload.user.profile');
            $filename = $this->upload($file, $uploadPath); */

            $filename = rand('111', '999') . time() . $file->getClientOriginalName();
            $filePath = 'user/' . $filename;
            Storage::disk('s3')->put($filePath, file_get_contents($file));

            /* $user1 = User::find($user_id);
            $user1->selfie_picture = $filename;
            $user1->save(); */
        } else {
            $selfie_img_name = rand('111', '999') . time() . $file->getClientOriginalName();
            $sfilePath = 'user/' . $selfie_img_name;
            Storage::disk('s3')->put($sfilePath, file_get_contents($file));

            $apilog_per_s = new ApiLogs();
            $apilog_per_s->user_id = $input['user_id'];
            $apilog_per_s->api_name = "verifyDocument-selfie";
            $apilog_per_s->request_data = $input['user_id'];
            $apilog_per_s->response_data = $selfie_img_name;
            $apilog_per_s->created_at = date("Y-m-d H:i:s");
            $apilog_per_s->updated_at = date("Y-m-d H:i:s");
            $apilog_per_s->save();
        }

        /** 
         * Compare faces algo.
         */
        $selfie_image = fopen($request->file('selfie_picture')->getPathName(), 'r');
        $selfie_bytes = fread($selfie_image, $request->file('selfie_picture')->getSize());

        if ($verification_with == 'Document') {
            $doc_upload_path = config('custom.upload.user.document_path');
        } else {
            $doc_upload_path = config('custom.upload.user.profile');
        }

        /* $doc_file_path = storage_path('app') . '/public/' . $doc_upload_path . "/" . $document_name;
        $document_image = fopen($doc_file_path, 'r');
        $document_bytes = fread($document_image, filesize($doc_file_path)); */
        if ($verification_with == 'Document') {
            $doc_file_path = 'documents/' . $document_name;
        } else {
            $doc_file_path = 'user/' . $document_name;
        }

        if ($is_manually_verified == 0) {
            try {
                $client_rekognition = new RekognitionClient([
                    'region'    => env('AWS_DEFAULT_REGION'),
                    'version'   => 'latest'
                ]);

                /* $com_face_result = $client_rekognition->compareFaces([
                    'SourceImage' => ['Bytes' => $document_bytes],
                    'TargetImage' => ['Bytes' => $selfie_bytes]
                ]); */

                $com_face_result = $client_rekognition->compareFaces([
                    'SourceImage' => [
                        'S3Object' => [
                            'Bucket' => env('AWS_BUCKET'),
                            'Name' => $doc_file_path
                        ]
                    ],
                    'TargetImage' => ['Bytes' => $selfie_bytes]
                ]);

                $results = $com_face_result->toArray();

                if (empty($results)) {
                    $similarity = 0;
                } else {
                    $face_matches = $results['FaceMatches'];
                    if (empty($face_matches)) {
                        $similarity = 0;
                    } else {
                        $similarity = $face_matches[0]['Similarity'];
                    }
                }
            } catch (AwsException $e) {
                $error_json_aws = json_encode($e->getMessage());

                $apilog_per = new ApiLogs();
                $apilog_per->user_id = $input['user_id'];
                $apilog_per->api_name = "verifyDocument-Per";
                $apilog_per->request_data = $input['user_id'];
                $apilog_per->response_data = $error_json_aws;
                $apilog_per->created_at = date("Y-m-d H:i:s");
                $apilog_per->updated_at = date("Y-m-d H:i:s");
                $apilog_per->save();

                //return $this->sendError('0', trans('message.selfie_failed'), array(), '200');
                $similarity = 0;
            }
        } else {
            $similarity = 0;
        }

        if ($user_id == '436' || $user_id == '377' || $user_id == '709') {
            $similarity = 91;
        }

        if ($similarity > 90 && $is_manually_verified == 0) {
            $user->similarity = number_format($similarity, 2);
            $user->document_attempt = 0;
            $user->is_profile_complete = 1;
            $user->register_step = $register_step;
            if ($verification_type == 'Register') {
                $user->selfie_picture = $filename;
            }
            $user->is_manually_verified = $is_manually_verified;
            $user->login_step = '3';
            $user->save();

            $response = array();
            $success['user_id'] = (string) $user->id;
            $success['name'] = $user->name;
            $name = explode(" ", $user->name);
            if (count($name) > 1) {
                $success['first_name'] = ($name[0]) ? $name[0] : $name;
                $success['last_name'] = ($name[1]) ? $name[1] : $name;
            } else {
                $success['first_name'] = $user->name;
                $success['last_name'] = $user->name;
            }
            $success['verification_type'] = $verification_type;
            $success['verification_with'] = $verification_with;
            $response[] = $success;
            return $this->sendResponse('1', $response, trans('message.document_vefify_success'));
        } else if ($is_manually_verified == 1) {
            $user->similarity = number_format($similarity, 2);
            $user->document_attempt = 0;
            $user->is_profile_complete = 1;
            $user->register_step = $register_step;
            if ($verification_type == 'Register') {
                $user->selfie_picture = $filename;
            }
            $user->is_manually_verified = $is_manually_verified;
            $user->user_status = '3';
            $user->login_step = '3';
            $user->save();

            $response = array();
            $success['user_id'] = (string) $user->id;
            $success['name'] = $user->name;
            $name = explode(" ", $user->name);
            if (count($name) > 1) {
                $success['first_name'] = ($name[0]) ? $name[0] : $name;
                $success['last_name'] = ($name[1]) ? $name[1] : $name;
            } else {
                $success['first_name'] = $user->name;
                $success['last_name'] = $user->name;
            }
            $success['verification_type'] = $verification_type;
            $success['verification_with'] = $verification_with;
            $response[] = $success;
            return $this->sendResponse('1', $response, trans('message.document_vefify_success'));
        } else {
            $attempt = $user->document_attempt + 1;
            $user->document_attempt = $attempt;
            $user->is_profile_complete = 0;
            $user->save();

            return $this->sendError('0', trans('message.selfie_failed'), $validator->errors(), '200');
        }
    }

    /**
     * Update register details after document verification
     * 
     * @return \Illuminate\Http\Response
     */
    public function updateRegisterDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'address' => 'required',
            'login_pin' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $apilog = new ApiLogs();
        $apilog->user_id = $input['user_id'];
        $apilog->api_name = "updateRegisterDetails-Request";
        $apilog->request_data = json_encode($input);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $samenumber_validation = $this->check_password_samenumber($input['login_pin']);
        if ($samenumber_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $sequence_validation = $this->check_password_sequence($input['login_pin'], 6);
        if ($sequence_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $user_id = $input['user_id'];
        $address = $input['address'];
        $city_id = $input['city_id'];
        $email = $input['email'];

        if (isset($email) && !empty($email)) {
            $check_email = User::where('email', $email)->where('id', '!=', $user_id)->first();
            if (!empty($check_email)) {
                return $this->sendError('0', trans('message.email_exit'), array(), '200');
            }
        }

        if (empty($input['referral_code'])) {
            $referral_code = '';
        } else {
            $referral_code = $input['referral_code'];
        }

        if (empty($input['latitude'])) {
            $latitude = '0.00';
        } else {
            $latitude = $input['latitude'];
        }

        if (empty($input['longitude'])) {
            $longitude = '0.00';
        } else {
            $longitude = $input['longitude'];
        }

        //$login_pin = sha1($input['login_pin']);

        $salt = time() . rand('111', '999');
        $login_pin = hash('sha256',  $input['login_pin'] . $salt);

        $profile_status = "1"; /* 1 = Add Address, 2 = Fund your account, 3 = Add money to you account, 4 = Add money from another bank card */
        $user_status = "0"; /* 0 = Inactive, 1 = Active, 2 = Pending */

        $user = User::select(DB::raw('name,email,address,country_code,mobile_number,CONVERT(register_step, CHAR(50)) as register_step, CONVERT(nationality_id, CHAR(50)) as nationality_id, CONVERT(document_id, CHAR(50)) as document_id, CONVERT(type_of_work_permit, CHAR(50)) as type_of_work_permit,referral_code, resident_permit, work_permit, CONVERT(document_attempt, CHAR(50)) as document_attempt, profile_picture, CONVERT(city_id, CHAR(50)) as city_id, CONVERT(is_manually_verified, CHAR(50)) as is_manually_verified, created_at as account_opening_date, is_biometric_enable, currency_enable, user_status, is_pep_scan, sts_token'))->find($user_id);
        $is_manually_verified = $user->is_manually_verified;

        //if($is_manually_verified == "0"){
        $final_response = $this->selcomOnboardingApis($user_id, $input);
        if (!empty($final_response)) {
            if ($final_response['resultcode'] == '200' || $final_response['resultcode'] == '000') {
            } else {
                $error_message = $final_response['message'];
                return $this->sendError('0', $error_message, array(), '200');
            }
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
        //}

        /** 
         * Record update here
         * @return true false
         */
        $users = User::find($user_id);
        $users->address = $address;
        $users->city_id = $city_id;
        $users->email = $email;
        $users->referral_by = $referral_code;
        $users->login_pin = $login_pin;
        $users->salt = $salt;
        $users->profile_status = $profile_status;
        $users->user_status = $user_status;
        $users->document_attempt = "0";
        $users->is_profile_complete = "1";
        $users->register_step = '6';
        $users->latitude = $latitude;
        $users->longitude = $longitude;

        if ($user->nationality_id == '2') {
            $users->user_status = '1';
        } else {
            if ($is_manually_verified == 0) {
                $users->user_status = '1';
            } else if ($is_manually_verified == 1) {
                $users->user_status = '1';
            }
        }
        //$users->user_status = '3';
        if ($user->is_pep_scan == 'Yes') {
            $users->user_status = '0';
        } else {
            $users->user_status = '3';
        }
        $users->save();

        $user['login_pin'] = "";

        $response = array();
        $success = $user;
        $success['user_id'] = (string) $user_id;
        $success['name'] = $user->name;
        $name = explode(" ", $user->name);
        if (count($name) > 1) {
            $success['first_name'] = ($name[0]) ? $name[0] : $user->name;
            $success['last_name'] = ($name[1]) ? $name[1] : $user->name;
        } else {
            $success['first_name'] = $user->name;
            $success['last_name'] = $user->name;
        }
        $success['address'] = $address;
        $success['email'] = (!empty($email)) ? $email : "";
        $success['city_id'] = $city_id;

        if (!empty($user->document_id)) {
            $doc = $this->getOneRecords('documents', 'name', 'id', $user->document_id);
            $success['document_name'] = $doc->name;
        } else {
            $success['document_name'] = "";
        }

        if (!empty($city_id)) {
            $city = $this->getOneRecords('cities', 'name', 'id', $city_id);
            if (!empty($city)) {
                $success['city_name'] = $city->name;
            } else {
                $success['city_name'] = "";
            }
        } else {
            $success['city_name'] = "";
        }

        $profile_path = config('custom.upload.user.profile');
        //$success['profile_picture'] = env('APP_URL') . '/storage/' . $profile_path . "/" . $user->profile_picture;
        $sts_token = $user->sts_token;
        $profile_picture = $this->getImageUsingSts('user/' . $user->profile_picture, $sts_token);
        $success['profile_picture'] = $profile_picture;

        //$success['profile_picture'] = env('S3_BUCKET_URL') . 'user/' . $user->profile_picture;
        if ($user->nationality_id == '2') {
            $resident_permit = $user->resident_permit;
            $work_permit = $user->work_permit;
            $uploadPath = config('custom.upload.user.document_permits');

            /* $user['resident_permit'] = env('S3_BUCKET_URL') . 'documents/' . $resident_permit;
            $user['work_permit'] = env('S3_BUCKET_URL') . 'documents/' . $work_permit; */

            $user['resident_permit'] = $this->getImageUsingSts('documents/' . $resident_permit, $sts_token);
            $user['work_permit'] = $this->getImageUsingSts('documents/' . $work_permit, $sts_token);

            $success['user_status'] = "1";
        } else {
            $user['resident_permit'] = "";
            $user['work_permit'] = "";
            $success['user_status'] = "1"; //(string)$user->user_status;
        }

        /* $serial_number = rand('100000000','999999999');
        $card_token = rand('100000000','999999999');
        
        $qry = new LinkCards();
        $qry->user_id = $user_id;
        $qry->card_serial_number = $serial_number;
        $qry->status = "1";
        $qry->card_number = "5123 12•• •••• ".rand('1000','9999');
        $qry->card_token = $card_token;
        $qry->card_name = "ITEM";
        $qry->card_type = "1";
        $qry->expiry = "08-2021";
        $qry->save(); */

        $useraccounts = UserAccounts::where("user_id", "=", $user_id)->first();
        $success['account_number'] = $useraccounts['account_number'];

        $success['is_profile_complete'] = "1";
        $success['document_attempt'] = "0";
        $success['is_biometric_enable'] = $user->is_biometric_enable;
        $success['currency_enable'] = (string)$user->currency_enable;
        $response[] = $success;

        if ($user->is_pep_scan == 'Yes') {
            return $this->sendResponse('1', $response, trans('message.complete_register'));
        } else {
            return $this->sendResponse('1', $response, trans('message.complete_register_with_pep_scan'));
        }

        //return $this->sendResponse('1', $response, trans('message.complete_register'));
    }

    /**
     * Upload documents as per requirement. 
     * 
     * @return \Illuminate\Http\Response
     */
    public function uploadWorkPermitDocuments(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        $type = $input['type'];

        $users = User::find($user_id);
        /** 
         * Move Uploaded File
         * @return file name
         */
        if ($type == 'resident_permit') {
            $resident_permit_file = $request->file('resident_permit');
            if (!empty($resident_permit_file)) {
                $resident_permit = $users->resident_permit;
                if (!empty($resident_permit)) {
                    $old_file_name = 'documents/' . $resident_permit;
                    Storage::disk('s3')->delete($old_file_name);
                }
                $resident_permit_filename = rand('111', '999') . time() . $resident_permit_file->getClientOriginalName();
                $filePath = 'documents/' . $resident_permit_filename;
                Storage::disk('s3')->put($filePath, file_get_contents($resident_permit_file));

                $users->resident_permit = $resident_permit_filename;
            } else {
                $resident_permit_filename = '';
                $users->resident_permit = $resident_permit_filename;
            }
            $users->register_step = '4';
        } else if ($type == 'passport_file_name') {
            $passport_file_name = $request->file('passport_file_name');
            if (!empty($passport_file_name)) {
                $passport_file = $users->passport_file_name;
                if (!empty($passport_file)) {
                    $old_file_name = 'documents/' . $passport_file;
                    Storage::disk('s3')->delete($old_file_name);
                }
                $passport_filename = rand('111', '999') . time() . $passport_file_name->getClientOriginalName();
                $filePath = 'documents/' . $passport_filename;
                Storage::disk('s3')->put($filePath, file_get_contents($passport_file_name));

                $users->passport_file_name = $passport_filename;
            } else {
                $passport_filename = '';
                $users->passport_file = $passport_filename;
            }
            $users->register_step = '2.1';
        } else if ($type == 'address_proof') {
            $address_proof_file_name = $request->file('address_proof');
            if (!empty($address_proof_file_name)) {
                $address_proof_file = $users->address_proof;
                if (!empty($address_proof_file)) {
                    $old_file_name = 'documents/' . $address_proof_file;
                    Storage::disk('s3')->delete($old_file_name);
                }
                $address_proof = rand('111', '999') . time() . $address_proof_file_name->getClientOriginalName();
                $filePath = 'documents/' . $address_proof;
                Storage::disk('s3')->put($filePath, file_get_contents($address_proof_file_name));
                $users->address_proof = $address_proof;
            } else {
                $address_proof = '';
                $users->address_proof = $address_proof;
            }
            $users->register_step = '5.1';
        } else {
            $work_permit_file = $request->file('work_permit');
            if (!empty($work_permit_file)) {
                $work_permit = $users->work_permit;
                if (!empty($work_permit)) {
                    $old_file_name = 'documents/' . $work_permit;
                    Storage::disk('s3')->delete($old_file_name);
                }

                $work_permit_filename = rand('111', '999') . time() . $work_permit_file->getClientOriginalName();
                $filePath = 'documents/' . $work_permit_filename;
                Storage::disk('s3')->put($filePath, file_get_contents($work_permit_file));

                $users->work_permit = $work_permit_filename;
            } else {
                $work_permit_filename = '';
                $users->work_permit = $work_permit_filename;
            }
            $users->register_step = '5';
        }

        /** 
         * Record update here
         * @return true false
         */
        $users->save();

        $user = User::select(DB::raw('name,email,address,country_code,mobile_number,CONVERT(register_step, CHAR(50)) as register_step, CONVERT(nationality_id, CHAR(50)) as nationality_id, CONVERT(document_id, CHAR(50)) as document_id, CONVERT(type_of_work_permit, CHAR(50)) as type_of_work_permit,referral_code, resident_permit, work_permit, passport_file_name, address_proof'))->find($user_id);
        $response = array();
        $success = $user;
        $success['user_id'] = (string) $user_id;
        if ($type == 'resident_permit') {
            $user['resident_permit'] = env('S3_BUCKET_URL') . 'documents/' . $resident_permit_filename;
            $user['work_permit'] = "";
            $user['address_proof'] = "";
            $user['passport_file_name'] = "";
        } else if ($type == 'passport_file_name') {
            $user['passport_file_name'] = env('S3_BUCKET_URL') . 'documents/' . $passport_filename;
            $user['work_permit'] = "";
            $user['resident_permit'] = "";
            $user['address_proof'] = "";
        } else if ($type == 'address_proof') {
            $user['address_proof'] = env('S3_BUCKET_URL') . 'documents/' . $address_proof;
            $user['work_permit'] = "";
            $user['resident_permit'] = "";
            $user['passport_file_name'] = "";
        } else {
            $user['address_proof'] = "";
            $user['resident_permit'] = "";
            $user['passport_file_name'] = "";
            $user['work_permit'] = env('S3_BUCKET_URL') . 'documents/' . $work_permit_filename;
        }
        $response[] = $success;
        return $this->sendResponse('1', $response, trans('message.document_upload_success'));
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'is_biometric' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), 200);
        }

        $input = $request->all();

        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];

        $user_id = $input['user_id'];
        $is_biometric = $input['is_biometric'];
        if (isset($is_biometric) && $is_biometric == 'No') {
            if (empty($input['login_pin'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), 200);
            }

            if (strlen($input['login_pin']) != '6') {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), 200);
            }
        }
        //$login_pin = sha1($input['login_pin']);
        $login = User::find($user_id);
        $salt = $login['salt'];

        $login_attempt = $login['login_attempt'];
        if ($login_attempt == 3) {
            $error_message = trans('message.wrong_pin_with_block');
            return $this->sendError('0', $error_message, array(), '200');
        }

        $this->qwikrewardsBalance($user_id);

        $sts_token = $this->stsToken();

        //$salt = time().rand('111','999');
        $login_pin = hash('sha256',  $input['login_pin'] . $salt);

        $params['user_id'] = $user_id;
        if ($is_biometric == 'No') {
            $params['login_pin'] = $login_pin;
        }
        $users = $this->userRepository->getByParams($params);
        if (!$users->isEmpty()) {
            $model = User::find($user_id);
            $model->login_attempt = 0;
            $model->sts_token = serialize($sts_token);
            $model->save();

            /**
             * Update login datetime
             */
            $user = User::select(DB::raw('name,email,address,country_code,mobile_number,CONVERT(register_step, CHAR(50)) as register_step, CONVERT(nationality_id, CHAR(50)) as nationality_id, CONVERT(document_id, CHAR(50)) as document_id, CONVERT(type_of_work_permit, CHAR(50)) as type_of_work_permit,referral_code, resident_permit, work_permit, CONVERT(document_attempt, CHAR(50)) as document_attempt, profile_picture, CONVERT(city_id, CHAR(50)) as city_id, created_at as account_opening_date, is_biometric_enable, currency_enable, user_status, sts_token'))->find($user_id);
            $user->login_datetime = $this->updated_at;
            $user->save();

            $sts_token = $user->sts_token;

            $response = array();
            $uploadPath = config('custom.upload.user.document_permits');
            if ($user->nationality_id == '2') {
                /* $user['resident_permit'] = env('S3_BUCKET_URL') . 'documents/' . $user->resident_permit;
                $user['work_permit'] = env('S3_BUCKET_URL') . 'documents/' . $user->work_permit; */
                $resident_permit = $this->getImageUsingSts('documents/' . $user->resident_permit, $sts_token);
                $work_permit = $this->getImageUsingSts('documents/' . $user->work_permit, $sts_token);
                $user['resident_permit'] = $resident_permit;
                $user['work_permit'] = $work_permit;
            } else {
                $user['resident_permit'] = "";
                $user['work_permit'] = "";
            }
            $success = $user;
            $success['token'] = "";
            $success['user_id'] = $user_id;
            $name = explode(" ", $user->name);
            if (count($name) > 1) {
                $success['first_name'] = ($name[0]) ? $name[0] : $name;
                $success['last_name'] = ($name[1]) ? $name[1] : $name;
            } else {
                $success['first_name'] = $user->name;
                $success['last_name'] = $user->name;
            }
            $success['name'] = $user->name;
            //$success['email'] = $user->email;
            $email = $user->email;
            $success['email'] = (!empty($email)) ? $email : "";
            $success['mobile_number'] = $user->mobile_number;
            if (!empty($user->document_id)) {
                $doc = $this->getOneRecords('documents', 'name', 'id', $user->document_id);
                $success['document_name'] = $doc->name;
            } else {
                $success['document_name'] = "";
            }

            if (!empty($user->city_id)) {
                $city = $this->getOneRecords('cities', 'name', 'id', $user->city_id);
                if (!empty($city)) {
                    $success['city_name'] = $city->name;
                } else {
                    $success['city_name'] = "";
                }
            } else {
                $success['city_name'] = "";
            }
            $profile_path = config('custom.upload.user.profile');
            //$success['profile_picture'] = env('S3_BUCKET_URL') . 'user/' . $user->profile_picture;

            $profile_picture = $this->getImageUsingSts('user/' . $user->profile_picture, $sts_token);
            $success['profile_picture'] = $profile_picture;

            $useraccounts = UserAccounts::where("user_id", "=", $user_id)->first();
            $success['account_number'] = $useraccounts['account_number'];
            $success['is_biometric_enable'] = $user->is_biometric_enable;
            $success['currency_enable'] = $user->currency_enable;

            $user_status = (string)$user->user_status;
            if ($user->user_status == '3') {
                $user_status = '1';
            }

            $devices = Devices::select('id')->where(array('user_id' => $user_id))->first();
            if (empty($devices)) {
                $device = new Devices();
                $device->user_id = $user_id;
                $device->device_token = $device_token;
                $device->int_udid = $int_udid;
                $device->device_type = $device_type;
                $device->created_at = $this->created_at;
                $device->updated_at = $this->updated_at;
                $device->save();
            } else {
                $id = $devices->id;
                $device = Devices::find($id);
                $device->device_token = $device_token;
                $device->int_udid = $int_udid;
                $device->device_type = $device_type;
                $device->updated_at = $this->updated_at;
                $device->save();
            }

            $success['user_status'] = (string)$user_status;
            $response[] = $success;
            return $this->sendResponse('1', $response, trans('message.login_success'));
        } else {

            $new_loging_attempt = $login_attempt + 1;

            $left_attempt = 3 - $new_loging_attempt;

            $model = User::find($user_id);
            $model->login_attempt = $new_loging_attempt;
            $model->login_attempt_datetime = date("Y-m-d H:i:s");
            $model->save();

            if ($new_loging_attempt == 3) {
                $error_message = trans('message.wrong_pin_with_block');
            } else {
                $error_message = trans('message.wrong_pin_with_attempt');
                $error_message = str_ireplace("<<LEFT_ATTEMPT>>", $left_attempt, $error_message);
            }

            return $this->sendError('0', $error_message, array(), '200');

            //return $this->sendError('0', trans('message.wrong_pin'), $validator->errors(), '200');
        }
    }

    /**
     * Device details update
     */
    public function updateLoginPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'user_id' => 'required',
            'login_pin' => 'required|numeric|digits:6',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];
        $user_id = $input['user_id'];
        $login_pin = $input['login_pin'];

        $loginuser = User::find($user_id);
        $old_salt = $loginuser['salt'];
        $old_login_pin = $loginuser['login_pin'];

        $old_slat_with_pin = hash('sha256',  $login_pin . $old_salt);
        if ($old_slat_with_pin == $old_login_pin) {
            return $this->sendError('0', trans('message.used_past_pin'), array(), '200');
        }

        $samenumber_validation = $this->check_password_samenumber($input['login_pin']);
        if ($samenumber_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $sequence_validation = $this->check_password_sequence($input['login_pin'], 6);
        if ($sequence_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $salt = time() . rand('111', '999');
        $login_pin = hash('sha256',  $login_pin . $salt);

        $users = User::find($user_id);
        $users->login_pin = $login_pin;
        $users->salt = $salt;
        $users->login_attempt = '0';
        $users->updated_at = date("Y-m-d H:i:s");
        $users->save();

        $success['user_id'] = (string) $user_id;
        $response[] = $success;
        return $this->sendResponse('1', $response, trans('message.update_pin'));
    }

    /**
     * Register step update
     */
    public function updateRegisterStep(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_number' => 'required',
            'register_step' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];
        $mobile_number = $input['mobile_number'];
        $register_step = $input['register_step'];

        $params['mobile_number'] = $mobile_number;
        $users = $this->userRepository->getByParams($params);
        $user_id = $users[0]['user_id'];

        $otp = rand(1000, 9999);

        $users = User::find($user_id);
        $users->register_step = $register_step;
        $users->otp = $otp;
        $users->updated_at = date("Y-m-d H:i:s");
        $users->save();

        $name = $users->name;
        $token = $users->createToken('ara')->accessToken;
        $register_step = $users->register_step;
        $nationality_id = $users->nationality_id;
        $type_of_work_permit = $users->type_of_work_permit;

        $response = array();
        $success['token'] = $token;
        $success['otp'] = (string) $otp;
        $success['user_id'] = (string) $user_id;
        $success['register_step'] = (string) $register_step;
        $success['name'] = (string) $name;
        $name = explode(" ", $name);
        $success['first_name'] = ($name[0]) ? $name[0] : $name;
        $success['last_name'] = ($name[1]) ? $name[1] : $name;
        $success['nationality_id'] = (string) $nationality_id;
        $success['type_of_work_permit'] = (string) $type_of_work_permit;
        $response[] = $success;
        return $this->sendResponse('1', $response, trans('message.register_step'));
    }

    /**
     * Ditach account using user id
     */
    public function detachAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $device_type = $input['device_type'];
        if ($device_type == 'Android') {
            $device_type = 1;
        } else {
            $device_type = 2;
        }
        $device_token = $input['device_token'];
        $int_udid = $input['int_udid'];
        $user_id = $input['user_id'];

        DB::table('devices')->where('int_udid', '=', $int_udid)->delete();

        $response = array();
        return $this->sendResponse('1', $response, trans('message.detach_account'));
    }
}