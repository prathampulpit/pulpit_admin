<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use App\Repositories\UserRepository;
use App\Repositories\ReferRequestsRepository;
use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Models\UserAccounts;
use App\Models\Countries;
use App\Models\ApiLogs;
use App\Models\ReferFriends;
use App\Models\ReferRequests;
use App\Models\Devices;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Validator;
use Storage;
use Carbon\Carbon;
use App;
use DB;

class UserController extends BaseController
{
    protected $userRepository;
    protected $referRequestsRepository;

    public function __construct(
        UserRepository $userRepository,
        ReferRequestsRepository $referRequestsRepository
    ) {
        $this->userRepository = $userRepository;
        $this->referRequestsRepository = $referRequestsRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");

        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        } else {
            $this->user_id = "";
        }
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    public function userDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'country_code' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];

        $params['user_id'] = $user_id;
        $users = $this->userRepository->getByParams($params);

        $response = array();
        $success = $users;
        $success['user_id'] = (string) $user_id;
        $response[] = $success;

        return $this->sendResponse('1', $response, trans('message.user_register'));
    }

    /**
     * Edit email id
     * 
     * @return json array
     */
    public function editProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $user_id = $input['user_id'];
        if (isset($input['email'])) {
            $email = $input['email'];
        } else {
            $email = '';
        }

        $user = User::where('id', '!=', $user_id)->where('email', '=', $email)->where('email', '!=', '')->where('type', '=', 'user')->select('id', 'name')->first();
        if (empty($user)) {
            $file = $request->file('profile_picture');
            if (!empty($file)) {
                //Move Uploaded File
                $old_profile_picture = $user['profile_picture'];
                if (!empty($old_profile_picture)) {
                    $old_file_name = 'user/' . $old_profile_picture;
                    Storage::disk('s3')->delete($old_file_name);
                }

                $filename = rand('111', '999') . time() . $file->getClientOriginalName();
                $filePath = 'user/' . $filename;
                Storage::disk('s3')->put($filePath, file_get_contents($file));
            } else {
                $filename = "";
            }

            /**
             * Edit email id here
             */
            $edit = User::find($user_id);
            $edit->email = $email;
            if (!empty($filename)) {
                $edit->profile_picture = $filename;
            }
            $edit->save();

            $user = User::select(DB::raw('name,first_name,last_name,email,address,country_code,mobile_number,CONVERT(register_step, CHAR(50)) as register_step, CONVERT(nationality_id, CHAR(50)) as nationality_id, CONVERT(document_id, CHAR(50)) as document_id, CONVERT(type_of_work_permit, CHAR(50)) as type_of_work_permit,referral_code, resident_permit, work_permit, CONVERT(document_attempt, CHAR(50)) as document_attempt, profile_picture, CONVERT(city_id, CHAR(50)) as city_id, CONVERT(is_manually_verified, CHAR(50)) as is_manually_verified, created_at as account_opening_date, is_biometric_enable, currency_enable, user_status, sts_token'))->find($user_id);

            $response = array();
            $success = $user;
            $success['user_id'] = (string) $user_id;
            $success['name'] = $user->name;
            $success['first_name'] = $user->first_name;
            $success['last_name'] = $user->last_name;
            $success['address'] = $user->address;
            $success['email'] = $email;
            $success['city_id'] = $user->city_id;

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

            $sts_token = $user->sts_token;

            $profile_path = config('custom.upload.user.profile');
            //$success['profile_picture'] = env('S3_BUCKET_URL') . 'user/' . $user->profile_picture;

            $profile_picture = $this->getImageUsingSts('user/' . $user->profile_picture, $sts_token);
            $success['profile_picture'] = $profile_picture;

            if ($user->nationality_id == '2') {
                $resident_permit = $user->resident_permit;
                $work_permit = $user->work_permit;
                $uploadPath = config('custom.upload.user.document_permits');

                /* $user['resident_permit'] = env('S3_BUCKET_URL') . 'documents/' . $uploadPath . "/" . $resident_permit;
                $user['work_permit'] = env('S3_BUCKET_URL') . 'documents/' . $uploadPath . "/" . $work_permit; */

                $resident_permit = $this->getImageUsingSts('documents/' . $resident_permit, $sts_token);
                $work_permit = $this->getImageUsingSts('documents/' . $work_permit, $sts_token);
                $user['resident_permit'] = $resident_permit;
                $user['work_permit'] = $work_permit;
            } else {
                $user['resident_permit'] = "";
                $user['work_permit'] = "";
            }
            $success['is_profile_complete'] = "1";
            $success['document_attempt'] = "0";
            $useraccounts = UserAccounts::where("user_id", "=", $user_id)->first();
            $success['account_number'] = $useraccounts['account_number'];
            $success['is_biometric_enable'] = $user->is_biometric_enable;
            $success['currency_enable'] = $user->currency_enable;
            $success['user_status'] = (string)$user->user_status;
            $response[] = $success;
            return $this->sendResponse('1', $response, trans('message.record_update'));
        } else {
            return $this->sendError('0', trans('message.email_exit'), $validator->errors(), '200');
        }
    }

    /**
     * Edit client details in selcome
     * 
     * @return json array
     */
    public function editClient(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        /* print_r($input);
        exit; */
        $user_id = $input['user_id'];
        $first_name = $input['first_name'];
        $last_name = $input['last_name'];
        $gender = strtoupper($input['gender']);
        $msisdn = $input['msisdn'];
        $language = $input['language'];
        $email = $input['email'];
        $dob = $input['dob'];
        $city_name = $input['city_name'];
        $address = $input['address'];
        $latitude = $input['latitude'];
        $longitude = $input['longitude'];
        $client_id = $input['client_id'];
        $notes = $input['notes'];
        $user_status = $input['user_status'];

        $external_id = rand(1000, 9999) . substr(time(), -7);
        $param_client['externalId'] = $external_id;
        $param_client['firstname'] = $first_name;
        $param_client['lastname'] = $last_name;
        $param_client['language'] = $language;
        $param_client['msisdn'] = $msisdn;
        $param_client['dob'] = $dob;
        $param_client['email'] = $email;
        $param_client['gender'] = $gender; //($gender=='MALE')?"M":"F";
        $param_client['active'] = "1";
        $param_client['location.city'] = $city_name;
        $param_client['location.street'] = $address;
        $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
        $param_client['location.country'] = "TZ";
        $param_client['notes'] = $notes;
        $param_client['active'] = $user_status;
        $client_json_request = json_encode($param_client);
        $accounts = $this->selcomApi('client/' . $client_id, $client_json_request, $user_id, "PUT");

        $this->selcomApiRequestResponse($user_id, 'client/' . $client_id, $client_json_request, json_encode($accounts));
        return $accounts;
    }

    /**
     * Check contact list
     * @return json array
     */
    public function checkUserContactList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_numbers' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $apilog = new ApiLogs();
        $apilog->user_id = '0';
        $apilog->api_name = 'checkUserContactList-Request';
        $apilog->request_data = json_encode($_POST);
        $apilog->response_data = 'NA';
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $user_id = $input['user_id'];
        $mobile_numbers = $input['mobile_numbers'];
        $mobile_numbers = json_decode($mobile_numbers, true);

        $user = User::find($user_id);
        $sts_token = $user['sts_token'];

        $response = array();
        foreach ($mobile_numbers as $val) {
            if (isset($val['name'])) {
                $name = $val['name'];
            } else {
                $name = "";
            }
            $mobile_number = $val['number'];

            /* $check_country_code = substr($mobile_number,0,3);
            if($check_country_code == '255'){
                $mobile_number = substr($mobile_number, 3);
            } */

            $is_valid_record = '0';
            $check_code = substr($mobile_number, 0, 3);
            if ($check_code == '255') {
                $check_str_length = strlen($mobile_number);
                if ($check_str_length == '12') {
                    $is_valid_record = '1';
                    $mobile_number = substr($mobile_number, 3);
                }
            } else {

                $check_str_length = strlen($mobile_number);
                if ($check_str_length == '10') {
                    $check_code = substr($mobile_number, 0, 1);
                    if ($check_code == '0') {
                        $mobile_number = substr($mobile_number, 1);
                    }
                } else {
                    $check_code = substr($mobile_number, 0, 2);
                    if ($check_code == '06' || $check_code == '07') {
                        $check_str_length = strlen($mobile_number);
                        if ($check_str_length == '11') {
                            $is_valid_record = '1';
                            $mobile_number = substr($mobile_number, 2);
                        }
                    } else {
                        $mobile_number = ltrim($mobile_number, '0');
                    }
                }
            }

            $users = User::where('mobile_number', '=', $mobile_number)->whereIn('user_status', [1, 3])->first();
            if (!empty($users)) {
                $country_code = $users['country_code'];
                $to_user_id = $users['id'];

                $flag_path = config('custom.upload.flags');
                $country = Countries::where('dial_code', '=', $country_code)->first();
                $d['flag'] = env('APP_URL') . '/storage/' . $flag_path . "/" . $country['flag'];
                $d['iso_code'] = $country['iso_code'];
                $d['country_code'] = $country_code;
                //$d['profile_picture'] = env('S3_BUCKET_URL') . 'user/' . $users['profile_picture'];
                $d['profile_picture'] = $this->getImageUsingSts('user/' . $users['profile_picture'], $sts_token);
                $d['number'] = $val['number'];
                $d['name'] = $name;
                $d['is_show'] = '0';
                if ($country_code == '255' || $country_code == '254' || $country_code == '216') {
                    $d['is_show'] = '1';
                }
                $d['show_country_flag'] = '1';

                $request = ReferRequests::where('to_user_id', $to_user_id)->where('from_user_id', $user_id)->first();

                $d['request_status'] = '';
                if (!empty($request)) {
                    $d['request_status'] = 'Requested';
                }
                $d['is_ara_app_user'] = 'Yes';
                $response[] = $d;
            }
        }

        if (!empty($response)) {
            $json_response = $this->aasort($response, 'name');
        } else {
            $json_response = array();
        }

        return $this->sendResponse('1', $json_response, trans('message.no_contact_found'));
    }

    /**
     * Ara user contact list
     * @return json array
     */
    public function checkAllUserContactList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_numbers' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $apilog = new ApiLogs();
        $apilog->user_id = '0';
        $apilog->api_name = 'alluserContactList-Request';
        $apilog->request_data = json_encode($_POST);
        $apilog->response_data = 'NA';
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $user_id = $input['user_id'];
        $mobile_numbers = $input['mobile_numbers'];
        $mobile_numbers = json_decode($mobile_numbers, true);

        $response = array();
        foreach ($mobile_numbers as $val) {
            if (isset($val['name'])) {
                $name = $val['name'];
            } else {
                $name = "";
            }
            $mobile_number = $val['number'];

            $is_valid_record = '0';
            $check_code = substr($mobile_number, 0, 3);
            if ($check_code == '255') {
                $check_str_length = strlen($mobile_number);
                if ($check_str_length == '12') {
                    $is_valid_record = '1';
                    $mobile_number = substr($mobile_number, 3);
                }
            } else {

                $check_str_length = strlen($mobile_number);
                if ($check_str_length == '10') {
                    $check_code = substr($mobile_number, 0, 1);
                    if ($check_code == '0') {
                        $mobile_number = substr($mobile_number, 1);
                    }
                } else {
                    $check_code = substr($mobile_number, 0, 2);
                    if ($check_code == '06' || $check_code == '07') {
                        $check_str_length = strlen($mobile_number);
                        if ($check_str_length == '11') {
                            $is_valid_record = '1';
                            $mobile_number = substr($mobile_number, 2);
                        }
                    } else {
                        $mobile_number = ltrim($mobile_number, '0');
                    }
                }
            }

            if ($is_valid_record == '1') {
                $users = User::where('mobile_number', '=', $mobile_number)->whereIn('user_status', [1, 3])->first();
                /* if (!empty($users)) {
                    $country_code = $users['country_code'];
                    $is_ara_app_user = 'Yes';
                }else{
                    $country_code = "255";
                    $is_ara_app_user = 'No';
                } */
                if (empty($users)) {
                    $country_code = "255";
                    $is_ara_app_user = 'No';

                    $flag_path = config('custom.upload.flags');
                    $country = Countries::where('dial_code', '=', $country_code)->first();
                    $d['flag'] = env('APP_URL') . '/storage/' . $flag_path . "/" . $country['flag'];
                    $d['iso_code'] = $country['iso_code'];
                    $d['country_code'] = $country_code;
                    $d['profile_picture'] = env('S3_BUCKET_URL') . 'user/';
                    $d['number'] = $val['number'];
                    $d['name'] = $name;
                    $d['is_show'] = '0';
                    if ($country_code == '255' || $country_code == '254' || $country_code == '216') {
                        $d['is_show'] = '1';
                    }
                    $d['show_country_flag'] = '1';
                    $d['is_ara_app_user'] = $is_ara_app_user;
                    $response[] = $d;
                }
            }
        }
        return $this->sendResponse('1', $response, trans('message.no_contact_found'));
    }

    /**
     * Check contact list
     * @return json array
     */
    public function checkReferralNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_number' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $mobile_number = $input['mobile_number'];

        $users = User::where('mobile_number', '=', $mobile_number)->where('user_status', '=', '1')->first();
        if (empty($users)) {
            return $this->sendResponse('0', array(), trans('message.wrong_referral_number'));
        }
        return $this->sendResponse('1', array(), trans('message.success'));
        /* $external_id = rand(1000,9999).substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['msisdn'] = '255'.$mobile_number;
        $trans_json_request = json_encode($trans_param);
        $url = 'onboarding/new/referal-verify';
        $account_result = $this->selcomApi($url, $trans_json_request, $this->user_id);
        
        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($account_result));

        if($account_result['resultcode'] == '200')
        {
            return $this->sendResponse('1', array(), trans('message.success'));
        }else{
            return $this->sendError('0', $account_result['message'], array(), '200');
        } */
    }

    /**
     * refer-a-friend
     * @return json array
     */
    public function referAFriend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_number' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];
        $mobile_number = $input['mobile_number'];
        $mobile_number_arr = explode(",", $mobile_number);

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['msisdn'] = $mobile_number_arr;
        $trans_json_request = json_encode($trans_param);
        $url = 'client/' . $client_id . '/refer-a-friend';
        $account_result = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($account_result));

        if ($account_result['resultcode'] == '200') {
            $selcom_data = $account_result['data'];
            $referrals_left = $selcom_data[0]['referrals_left'];

            $is_send = '0';
            foreach ($mobile_number_arr as $val) {
                $check = ReferFriends::where('mobile_number', $val)->first();
                if (empty($check)) {
                    $model = new ReferFriends();
                    $model->user_id = $this->user_id;
                    $model->mobile_number = $val;
                    $model->save();

                    $is_send = '1';
                }
            }

            if ($is_send == '0') {
                return $this->sendError('0', trans('message.request_already_send'), array(), '200');
            }

            $settings = Settings::find('1');

            $response = array();
            $total_refer = ReferFriends::where('user_id', $this->user_id)->count();
            $remaining_refer_limit = $settings['maximum_referral_request_limit'] - $total_refer;
            $d['remaining_refer_limit'] = $referrals_left;

            $maximum_referral_request_message = $settings['maximum_referral_request_message_' . $language_code];
            $maximum_referral_request_message = str_ireplace("#COUNT#", $referrals_left, $maximum_referral_request_message);
            $d['remaining_refer_message'] = $maximum_referral_request_message;
            $response[] = $d;

            return $this->sendResponse('1', $response, $account_result['message']);
        } else {
            return $this->sendError('0', $account_result['message'], array(), '200');
        }
    }

    /**
     * Refer request
     * @return json array
     */
    public function sendReferRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'mobile_number' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $mobile_number = $input['mobile_number'];
        $mobile_number_arr = explode(",", $mobile_number);

        $loginuser = User::find($this->user_id);
        $login_mobile_number = $loginuser['mobile_number'];

        $is_request_send = '0';

        foreach ($mobile_number_arr as $val) {

            $check_code = substr($val, 0, 3);
            if ($check_code == '255') {
                $check_str_length = strlen($val);
                if ($check_str_length == '12') {
                    $is_valid_record = '1';
                    $mobile_number_without_code = substr($val, 3);
                }
            } else {

                $check_str_length = strlen($val);
                if ($check_str_length == '10') {
                    $check_code = substr($val, 0, 1);
                    if ($check_code == '0') {
                        $mobile_number_without_code = substr($val, 1);
                    }
                } else {
                    $check_code = substr($val, 0, 2);
                    if ($check_code == '06' || $check_code == '07') {
                        $check_str_length = strlen($val);
                        if ($check_str_length == '11') {
                            $is_valid_record = '1';
                            $mobile_number_without_code = substr($val, 2);
                        }
                    } else {
                        $mobile_number_without_code = ltrim($val, '0');
                    }
                }
            }

            //$user = User::whereRaw("CONCAT_WS('',`country_code`, `mobile_number`) = $val AND user_status IN (1,3)")->first();
            $user = User::whereRaw("mobile_number = $mobile_number_without_code AND user_status IN (1,3)")->first();
            if (!empty($user)) {
                $user_id = $user['id'];
            } else {
                $user_id = 0;
            }

            $notificationText = $login_mobile_number . " sending you refer requst.";

            $check = ReferRequests::where('from_user_id', $this->user_id)->where('to_user_id', $user_id)->first();
            //$check = ReferRequests::where('to_user_id',$user_id)->first();
            if (empty($check)) {
                $is_request_send = '1';

                $insert = new ReferRequests();
                $insert->from_user_id = $this->user_id;
                $insert->to_user_id = $user_id;
                $insert->notification_text = $notificationText;
                $insert->created_at = date("Y-m-d H:i:s");
                $insert->updated_at = date("Y-m-d H:i:s");
                $insert->save();

                $device = Devices::where('user_id', '=', $user_id)->first();
                if (!empty($device)) {
                    $device_type = $device['device_type'];
                    if ($device_type == 1) {
                        $device_type = 'Android';
                    } else {
                        $device_type = 'Iphone';
                    }
                    $device_token = $device['device_token'];

                    $json_arr['type'] = '3';
                    $json_arr['user_id'] = 'NA';

                    //$this->sendPuchNotification($device_type, $device_token, $notificationText,$totalNotifications='0',$pushMessageText="", $title="ARA");

                    $this->sendPuchNotificationWithData($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "ARA", $json_arr);
                }
            }
        }

        if ($is_request_send == '1') {
            return $this->sendResponse('1', array(), trans('message.request_send'));
        } else {
            //return $this->sendResponse('0', array(), trans('message.request_already_send'));
            return $this->sendError('0', trans('message.request_already_send'), array(), '200');
        }
    }

    /**
     * Accept reject request
     * @return json array
     */
    public function acceptRejectRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'request_id' => 'required',
            'status' => 'required',
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
        $request_id = $input['request_id'];
        $status = $input['status'];
        if ($status == 1) {
            $status_name = "Accepted";
        } else {
            $status_name = "Refused";
        }

        $loginuser = User::find($this->user_id);
        $name = $loginuser['name'];

        $request = ReferRequests::find($request_id);
        $from_user_id = $request['from_user_id'];

        //$notificationText = $name." has ". $status_name. " your request";
        if ($language_code == 'en') {
            $notificationText = "Hello! " . $name . " has sent you an invite to join Ara. Go to the Ara app to complete your registration. We can’t wait to have you as our latest customer! #FreeBanking";
        } else {
            $notificationText = "Habari! " . $name . " amekutumia mualiko kujiunga na Ara. Ingia katika Ara app kukamilisha usajili wako. Tuna shahuku ya kuwa nawe kama mteja wetu mpya! #FreeBanking";
        }

        $request->status = $status;
        if ($status == 1) {
            $request->accepted_datetime = date("Y-m-d H:i:s");

            $check_record = ReferRequests::where('from_user_id', $from_user_id)->where('id', '!=', $request_id)->first();
            if (!empty($check_record)) {
                ReferRequests::where('from_user_id', $from_user_id)->where('id', '!=', $request_id)->update(['status' => '3']);
            }
        } else {
            $request->rejected_datetime = date("Y-m-d H:i:s");
        }
        $request->notification_text = $notificationText;
        $request->updated_at = date("Y-m-d H:i:s");
        $request->save();

        $device = Devices::where('user_id', '=', $from_user_id)->first();
        if (!empty($device)) {
            $device_type = $device['device_type'];
            if ($device_type == 1) {
                $device_type = 'Android';
            } else {
                $device_type = 'Iphone';
            }
            $device_token = $device['device_token'];

            $json_arr['type'] = '2';
            $json_arr['user_id'] = 'NA';

            $this->sendPuchNotificationWithData($device_type, $device_token, $notificationText, $totalNotifications = '0', $pushMessageText = "", $title = "ARA", $json_arr);
        }

        $response = array();
        $d['request_id'] = $request_id;
        $d['status'] = $status;
        $d['status_name'] = $status_name;
        $response[] = $d;
        return $this->sendResponse('1', $response, trans('message.request_update'));
    }

    /**
     * Request Lists
     * @return json array
     */
    public function referRequestLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $no_of_pagerecord = '10';
        if (isset($input['page']) && !empty($input['page'])) {
            $page_number = $input['page'];
            if ($page_number == 1) {
                $limit = $page_number;
            } else {
                $limit = $page_number * $no_of_pagerecord;
            }
        } else {
            $page_number = 0;
            $limit = 0;
        }

        $total_records = ReferRequests::where("to_user_id", $this->user_id)->count();
        $total_pages = round($total_records / $no_of_pagerecord);

        $params['to_user_id'] = $this->user_id;
        $params['offset'] = $limit;
        $params['limit'] = $no_of_pagerecord;
        $params['order_by'] = 'id';
        $params['order'] = 'desc';
        $requests = $this->referRequestsRepository->getByParams($params);
        $json_arr = array();
        if (!empty($requests)) {
            foreach ($requests as $val) {
                $d['request_id'] = $val['id'];
                $d['from_user_id'] = $val['from_user_id'];
                $user = User::find($val['from_user_id']);
                $user_name = '';
                $mobile_number = '';
                if (!empty($user)) {
                    $user_name = $user['name'];
                    if (empty($user_name)) {
                        $user_name = '+255' . $user['mobile_number'];
                    }
                    $mobile_number = '+255' . $user['mobile_number'];
                }
                $d['user_name'] = $user_name;
                $d['mobile_number'] = $mobile_number;
                $d['status'] = $val['status'];
                if ($val['status'] == '1') {
                    $d['status_name'] = "Accepted";
                } else if ($val['status'] == '0') {
                    $d['status_name'] = "Refused";
                } else if ($val['status'] == '3') {
                    $d['status_name'] = "Accepted By Other";
                } else {
                    $d['status_name'] = "Requested";
                }
                $d['created_at'] = $val['created_at'];
                $json_arr[] = $d;
            }
        }

        return $this->sendResponse('1', $json_arr, trans('message.request_lists'), 200, '0', (string)$page_number, (string)$total_pages);
    }
}