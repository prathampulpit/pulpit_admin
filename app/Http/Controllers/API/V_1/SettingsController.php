<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\UserAccountRepository;
use App\Models\Contacts;
use App\Models\User;
use App\Models\States;
use App\Models\UserAccounts;
use App\Models\LinkCards;
use Mail;
use App\Mail\ContactUsEmail;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;

class SettingsController extends BaseController
{
    protected $userAccountRepository;
    protected $userRepository;

    public function __construct(
        userAccountRepository $userAccountRepository,
        UserRepository $userRepository
    ) {
        $this->userAccountRepository = $userAccountRepository;
        $this->userRepository = $userRepository;
        $this->datetime = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * Add stashes details method
     * Stashes method: 1, 2 
     * 1 = Once Off and 2 = Recurring
     * @return json array
     */
    public function addContactsDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'topic' => 'required',
            'subject' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $topic = $input['topic'];
        $subject = $input['subject'];
        $message = $input['message'];

        $users = User::find($this->user_id);
        $name = $users->name;
        $email = $users->email;
        $mobile_number = $users->mobile_number;

        $account = UserAccounts::where('user_id', $this->user_id)->first();
        $account_number = $account['account_number'];

        $insert = new Contacts();
        $insert->user_id = $this->user_id;
        $insert->topic = $topic;
        $insert->subject = $subject;
        $insert->message = $message;
        $insert->created_at = $this->datetime;
        $insert->updated_at = $this->datetime;
        $insert->save();


        $mailData = [
            'full_name' => $name,
            'topic' => $topic,
            'subject' => $subject,
            'description' => $message,
            'email' => $email,
            'mobile_number' => $mobile_number,
            'account_number' => $account_number,
        ];

        $email = "hello@ara.co.tz";
        //$email = "piyush.prajapati@moweb.com";
        Mail::to($email)->send(new ContactUsEmail($mailData));

        return $this->sendResponse('1', array(), trans('message.contact_added'));
    }

    /**
     * Change current lang
     * 
     * @return json array
     */
    public function changeLanguage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'current_language' => 'required|between:2,2'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $current_language = $input['current_language'];
        $user = User::find($this->user_id);
        $user->current_language = $current_language;
        $user->save();

        $json['user_id'] = $this->user_id;
        $json['current_language'] = $current_language;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.lang_change'));
    }

    /**
     * Change current lang
     * 
     * @return json array
     */
    public function notificationSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'is_notification' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $is_notification = $input['is_notification'];
        $user = User::find($this->user_id);
        $user->is_notification = $is_notification;
        $user->save();

        $json['user_id'] = $this->user_id;
        $json['is_notification'] = $is_notification;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.notification_change'));
    }

    /**
     * Change login pin
     * 
     * @return json array
     */
    public function changeLoginPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'login_pin' => 'required|numeric|digits:6',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        //$login_pin = sha1($input['login_pin']);

        $samenumber_validation = $this->check_password_samenumber($input['login_pin']);
        if ($samenumber_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $sequence_validation = $this->check_password_sequence($input['login_pin'], 6);
        if ($sequence_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $salt = time() . rand('111', '999');
        $login_pin = hash('sha256',  $input['login_pin'] . $salt);

        $users = User::find($this->user_id);
        $users->login_pin = $login_pin;
        $users->salt = $salt;
        $users->is_temporary_pin = '0';
        $users->save();

        $json['user_id'] = $this->user_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.login_pin_change'));
    }

    /**
     * Change login pin
     * 
     * @return json array
     */
    public function verifyLoginPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'login_pin' => 'required|numeric|digits:6',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();

        $samenumber_validation = $this->check_password_samenumber($input['login_pin']);
        if ($samenumber_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $sequence_validation = $this->check_password_sequence($input['login_pin'], 6);
        if ($sequence_validation == true) {
            return $this->sendError('0', trans('message.wrong_pin_format'), array(), '200');
        }

        $login_pin = sha1($input['login_pin']);

        $users = User::where('id', '=', $this->user_id)->where('login_pin', '=', $login_pin)->first();
        if (empty($users)) {
            return $this->sendError('0', trans('message.wrong_pin'), $validator->errors(), '200');
        }

        $json['user_id'] = $this->user_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.verify_pin'));
    }

    /**
     * Change biometric flag
     * 
     * @return json array
     */
    public function biometricSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'is_biometric_enable' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $is_biometric_enable = $input['is_biometric_enable'];
        $user = User::find($this->user_id);
        $user->is_biometric_enable = $is_biometric_enable;
        $user->save();

        $json['user_id'] = $this->user_id;
        $json['is_biometric_enable'] = $is_biometric_enable;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.biometric_status_change'));
    }

    /**
     * Currency enable flag
     * 
     * @return json array
     */
    public function currencyEnableSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'currency_enable' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $currency_enable = $input['currency_enable'];

        $local_account_fallback = 'No';
        if (isset($input['local_account_fallback'])) {
            $local_account_fallback = $input['local_account_fallback'];
        }

        $localAccountFallback = '0';
        if ($local_account_fallback == 'Yes') {
            $localAccountFallback = '1';
        }

        $card_id = '0';
        if (isset($input['card_id'])) {
            $card_id = $input['card_id'];
        }

        $cards = LinkCards::find($card_id);
        if (!empty($cards)) {
            $card_number = $cards['card_number'];
        } else {
            $card_number = '-';
        }

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $status = 0;
        $localAccountFallback = 0;
        if ($currency_enable == 'Yes') {
            $status = 1;
            $localAccountFallback = 1;
        }
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['status'] = $status;
        $trans_param['maskedCardNo'] = $card_number;
        $trans_param['localAccountFallback'] = $localAccountFallback;
        $trans_json_request = json_encode($trans_param);

        $url = 'client/' . $client_id . '/update-multicurrency-flag';
        $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

        if ($selcom_response['resultcode'] != '200') {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
            exit;
        }

        $user = User::find($this->user_id);
        $user->currency_enable = $currency_enable;
        $user->save();

        $json['user_id'] = $this->user_id;
        $json['currency_enable'] = $currency_enable;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.currency_enable_status_change'));
    }

    /**
     * ATM access flag Yes, No
     * 
     * @return json array
     */
    public function atmAccessSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'is_atm_access' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $is_atm_access = $input['is_atm_access'];
        $card_id = $input['card_id'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $cards = LinkCards::find($card_id);
        $plasticSerial = $cards['card_serial_number'];

        $status = 0;
        if ($is_atm_access == 'Yes') {
            $status = 1;
        }
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['status'] = $status;
        $trans_param['plasticSerial'] = $plasticSerial;

        $trans_json_request = json_encode($trans_param);

        $url = 'client/' . $client_id . '/update-atm-surcharge-flag';
        $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

        if ($selcom_response['resultcode'] != '200') {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
            exit;
        }

        $model = LinkCards::find($card_id);
        $model->is_atm_access = $is_atm_access;
        $model->save();

        /* $user = User::find($this->user_id);
        $user->is_atm_access = $is_atm_access;
        $user->save(); */

        $json['user_id'] = $this->user_id;
        $json['is_atm_access'] = $is_atm_access;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.atm_access_status_change'));
    }

    /**
     * Get State list as per country id
     * 
     * @return json array
     */
    public function getStateList(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'country_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $country_id = $input['country_id'];

        $response = array();
        $states = States::where('country_id', '=', $country_id)->get();
        if (!empty($states)) {
            foreach ($states as $val) {
                $d['state_id'] = (string)$val['id'];
                $d['country_id'] = (string)$val['country_id'];
                $d['name'] = $val['name'];
                $response[] = $d;
            }
        }
        return $this->sendResponse('1', $response, "list");
    }
}