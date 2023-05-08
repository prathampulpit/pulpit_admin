<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use App\Repositories\CardsRepository;
use App\Repositories\UserAccountRepository;
use Intervention\Image\Facades\Image;
use App\Models\Cards;
use App\Models\Transactions;
use App\Models\PullFunds;
use App\Models\AccountBalances;
use App\Models\UserCredits;
use App\Models\LinkCards;
use App\Models\User;
use App\Models\Cities;
use App\Models\Settings;
use App\Models\Devices;
use App\Models\Notifications;
use App\Models\IssueTickets;
use App\Models\Categories;
use App\Models\Wallets;
use App\Models\MobileMoneyTransactions;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use URL;
use DB;

class CardController extends BaseController
{
    protected $cardsRepository;

    public function __construct(
        cardsRepository $cardsRepository,
        userAccountRepository $userAccountRepository
    ) {
        $this->cardsRepository = $cardsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->datetime = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * Add card details method
     * 
     * @return json array
     */
    public function addCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_number' => 'required',
            'expiry_month' => 'required',
            'expiry_year' => 'required',
            'cvv' => 'required',
            'card_type' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $card_number = $input['card_number'];
        $card_type = $input['card_type'];

        if (empty($input['first_name'])) {
            $first_name = "";
        } else {
            $first_name = $input['first_name'];
        }

        if (empty($input['last_name'])) {
            $last_name = "";
        } else {
            $last_name = $input['last_name'];
        }

        if (empty($input['postalcode'])) {
            $postalcode = "12345";
        } else {
            $postalcode = $input['postalcode'];
        }
        if (empty($input['address'])) {
            $address = "";
        } else {
            $address = $input['address'];
        }
        if (empty($input['country'])) {
            $country = "";
        } else {
            $country = $input['country'];
        }
        if (empty($input['city'])) {
            $city = "";
        } else {
            $city = $input['city'];
        }
        if (empty($input['state'])) {
            $state = "NA";
        } else {
            $state = $input['state'];
        }

        $account_id = '1';

        $param['user_id'] = $this->user_id;
        $accounts = $this->userAccountRepository->getByParams($param);
        if ($accounts->isEmpty()) {
            return $this->sendError('0', trans('message.wrong_user'), $validator->errors(), '200');
        }
        $account_id = $accounts[0]['account_id'];

        $settings = Settings::find('1');
        $minimum_funds_for_add_card = $settings['minimum_funds_for_add_card'];

        $params['card_number'] = $card_number;
        $params['user_id'] = $this->user_id;
        $params['status'] = '1';
        $card_details = $this->cardsRepository->getByParams($params);
        if ($card_details->isEmpty()) {
            /* $model = new cards();
            $model->user_id = $this->user_id; 
            $model->card_number = $card_number;
            $model->card_token = ""; 
            $model->card_name = "Selcom"; 
            $model->card_type = $card_type;
            $model->account_id = $account_id;            
            $model->nick_name = $nick_name;
            $model->status = '1';
            $model->created_at = $this->datetime;
            $model->updated_at = $this->datetime;
            $model->save(); */
            $users = User::find($this->user_id);
            $client_id = $users->client_id;
            $country_code = $users->country_code;
            $mobile_number = $users->mobile_number;
            $email = $users->email;

            $external_id = rand(1000, 9999) . substr(time(), -7);
            $trans_param['externalId'] = $external_id;
            $trans_param['billing.email'] = $email;
            $trans_param['amount'] = $minimum_funds_for_add_card;
            $trans_param['msisdn'] = $country_code . $mobile_number;
            $trans_param['billing.firstname'] = $first_name;
            $trans_param['billing.lastname'] = $last_name;
            $trans_param['billing.postalcode'] = $postalcode;
            $trans_param['billing.city'] = $city;
            $trans_param['billing.address'] = $city;
            $trans_param['cardBin'] = $card_number;
            /* if($country == 'CA'){
                $trans_param['billing.state_or_region'] = $state;
            }else{
                $trans_param['billing.state'] = $state;       
            } */
            $trans_param['billing.state'] = $state;
            $trans_param['billing.country'] = $country;
            //$trans_param['redirectUrl'] = base64_encode(URL::to('/')."/card#success"); //redirect url on success
            //$trans_param['failureUrl'] = base64_encode(URL::to('/')."/card#failure"); //redirect url on failure

            $trans_param['redirectUrl'] = base64_encode("https://ara-portal.ara.co.tz/card#success"); //redirect url on success
            $trans_param['failureUrl'] = base64_encode("https://ara-portal.ara.co.tz/card#failure"); //redirect url on failure
            $trans_json_request = json_encode($trans_param);

            $url = 'client/' . $client_id . '/linkcards-session';
            $account_result = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($account_result));

            if ($account_result['resultcode'] == '200') {
                $response[] = $account_result['data'][0];
                return $this->sendResponse('1', $response, trans('message.card_added'));
            } else {
                return $this->sendError('0', $account_result['message'], array(), '200');
            }
        } else {
            return $this->sendError('0', trans('message.card_already_exit'), array(), '200');
        }
    }

    /**
     * List of cards by user id
     * 
     * @return json array 
     */
    public function cardLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $params['user_id'] = $this->user_id;
        $params['status'] = 1;

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $response = array();
        /* $card_details['user_id'] = $this->user_id;
        $card_details = $this->cardsRepository->getByParams($params);
        foreach($card_details as $key=>$val){
            $card_number = $card_details[$key]['card_number'];
            $arr2 = str_split($card_number, 4);
            $card_number = '•••• •••• •••• '.$arr2[3];
            $card_details[$key]['card_number'] = $card_number;
        } */
        //$client_id = '155';
        $api_result = $this->selcomApi('client/' . $client_id . '/linkedcards', "", $this->user_id, "GET");
        if ($api_result['resultcode'] == '200') {
            foreach ($api_result['data'] as $val) {
                $d['card_id'] = (string)$val['id'];
                $d['card_number'] = '**** ' . substr($val['masked_card'], -4);
                $d['postalcode'] = $val['billing.postalcode'];
                $d['card_name'] = $val['name'];
                $d['nick_name'] = str_ireplace(" NA", "", $val['name']);
                $d['card_token'] = $val['card_token'];
                $d['expiry'] = $val['expiry'];
                if ($val['card_type'] == '001') {
                    $d['card_type'] = "Visa";
                } else if ($val['card_type'] == '002') {
                    $d['card_type'] = "Mastercard";
                } else if ($val['card_type'] == '003') {
                    $d['card_type'] = "American Express";
                } else if ($val['card_type'] == '004') {
                    $d['card_type'] = "Discover";
                } else if ($val['card_type'] == '005') {
                    $d['card_type'] = "Dinners club";
                } else if ($val['card_type'] == '033') {
                    $d['card_type'] = "Visa Electron";
                } else {
                    $d['card_type'] = $val['card_type'];
                }
                $response[] = $d;
            }
        }

        return $this->sendResponse('1', $response, trans('message.list_of_cards'));
    }

    /**
     * Soft remove card details
     * 
     * @return json array
     */
    public function removeCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $card_id = $input['card_id'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;

        /* $params['card_id'] = $card_id;
        $params['status'] = 1;
        $card_details = $this->cardsRepository->getByParams($params);
        if($card_details->isEmpty()){
            return $this->sendError('-11', trans('message.wrong_card'), $validator->errors(), '200');
        } */

        /* $card_no = '';
        $card_arr = explode(",", $card_id);
        foreach($card_arr as $val){
            $card = Cards::find($val);
            $card->status = 2;
            $card->updated_at = $this->datetime;
            $card->save();

            $card_number = $card['card_number'];
            $arr2 = str_split($card_number, 4);
            $card_no .= '•••• •••• '.$arr2[2].', ';
        }
        
        $msg = trans('message.card_remove');
        $msg = str_replace("##CARDNUMBER##",$card_no, $msg); */

        $card_no = '';
        $card_arr = explode(",", $card_id);
        foreach ($card_arr as $val) {
            $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
            $param['externalId'] = $external_id;
            $param['id'] = $card_id;
            $param['msisdn'] = $country_code . $mobile_number;
            $json_request = json_encode($param);

            $url = 'client/' . $client_id . '/delete-linked-card';
            $api_result = $this->selcomApi($url, $json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $json_request, json_encode($api_result));
        }

        if ($api_result['resultcode'] == '200') {
            $msg = $api_result['message'];
            $json['user_id'] = $this->user_id;
            $response[] = $json;
            return $this->sendResponse('1', $response, $msg);
        } else {
            return $this->sendError('0', $api_result['message'], array(), '200');
        }
    }

    /**
     * Check card details method befor pull fund call
     * 
     * @return json array
     */
    public function checkCardDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'amount' => 'required',
            'card_number' => 'required',
            'card_type' => 'required',
            'card_token' => 'required',
            'card_name' => 'required',
            'postalcode' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $card_id = $input['card_id'];
        $amount = $input['amount'];

        /* $params['card_id'] = $card_id;
        $params['status'] = 1;
        $card_details = $this->cardsRepository->getByParams($params);
        if($card_details->isEmpty()){
            return $this->sendError('-11', trans('message.wrong_card'), $validator->errors(), '200');
        } */

        $response = array();
        $json['card_id'] = $input['card_id'];
        $json['card_number'] = $input['card_number'];
        $json['card_type'] = $input['card_type'];
        $json['card_token'] = $input['card_token'];
        $json['card_name'] = $input['card_name'];
        $json['postalcode'] = $input['postalcode'];
        $json['amount'] = $amount;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.card_details'));
    }

    /**
     * Add fund for ara account balance getting from other bank card 
     * Add transaction details
     * Update account balance 
     * 
     * @return json array
     */
    public function pullFund(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'amount' => 'required',
            'card_number' => 'required',
            'card_token' => 'required',
            'card_name' => 'required',
            'postalcode' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];

        $card_id = $input['card_id'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);
        $card_no = $input['card_number'];
        $card_type = $input['card_type'];
        $card_token = $input['card_token'];
        $card_name = $input['card_name'];
        $postalcode = $input['postalcode'];

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $account_balance_id = $account[0]['account_balance_id'];
        $currency_symbol = $account[0]['currency_symbol'];
        $account_number = $account[0]['account_number'];
        $arr2 = str_split($account_number, 4);
        //$account_no = '•••• '.$arr2[1];
        $account_no = '•••• ' . substr($account_number, -4);

        $users = User::find($this->user_id);
        $address = $users->address;
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;
        $email = $users->email;
        $is_notification = $users->is_notification;

        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['billing.email'] = $email;
        $trans_param['amount'] = $amount;
        $trans_param['msisdn'] = $country_code . $mobile_number;
        $trans_param['billing.firstname'] = $card_name;
        $trans_param['billing.lastname'] = 'NA';
        $trans_param['billing.postalcode'] = $postalcode;
        $trans_param['billing.city'] = 'NA';
        $trans_param['billing.address'] = $address;
        $trans_param['billing.state'] = 'NA';
        $trans_param['billing.country'] = 'TZ';
        $trans_param['cardToken'] = $card_token;
        $trans_json_request = json_encode($trans_param);

        $url = 'client/' . $client_id . '/linkedcards-payment';
        $pull_fund_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($pull_fund_response));

        if ($pull_fund_response['resultcode'] != '200') {
            return $this->sendError('0', $pull_fund_response['message'], array(), '200');
            exit;
        }

        /* if(isset($pull_fund_response['data'])){
            $json_data = $pull_fund_response['data'];
            $ara_receipt = $json_data[0]['araReceipt'];
        }else{
            $ara_receipt = '';
        } */

        $ara_balance = $this->araAvaBalance($this->user_id);

        $update_balance = $account_balance + $amount;
        $msg = 'Your Ara account ' . $account_no . ' has been credited TZS ' . number_format($amount) . '. Updated balance ' . $currency_symbol . ' ' . number_format($update_balance);

        $receipt = 'You have pulled funds ' . $currency_symbol . ' ' . number_format($amount) . ' from ' . $card_type . ' ' . $card_no . ' ' . date("d-m-Y H:i", strtotime($this->datetime));

        $transactions = new Transactions();
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->trans_type = 1;
        $transactions->trans_status = 1;
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        $transactions->account_number = $account_number;
        $transactions->trans_datetime = $this->datetime;
        $transactions->created_at = $this->datetime;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {

            /**
             * Add Pull Fund data
             */
            $pullFund = new PullFunds();
            $pullFund->user_id = $this->user_id;
            $pullFund->trans_id = $transactions->id;
            $pullFund->card_id = $card_id;
            $pullFund->trans_amount = $amount;
            $pullFund->created_at = $this->datetime;
            $pullFund->updated_at = $this->datetime;
            $pullFund->save();

            /**
             * User credit
             */
            $credit = new UserCredits();
            $credit->user_id = $this->user_id;
            $credit->trans_id = $transactions->id;
            $credit->prev_balance = $account_balance;
            $credit->trans_amount = $amount;
            $credit->created_at = $this->datetime;
            $credit->updated_at = $this->datetime;
            $credit->save();
            if ($credit->id > 0) {
                /**
                 * Ara user balance update
                 */
                /* $updateBalance = AccountBalances::find($account_balance_id);
                $updateBalance->account_balance = $account_balance+$amount; 
                $updateBalance->updated_at = $this->datetime;
                $updateBalance->save(); */
            }

            if ($is_notification == 'Yes') {
                /* Send push notification */
                $device = Devices::where('user_id', '=', $this->user_id)->first();
                if (!empty($device)) {
                    $device_type = $device['device_type'];
                    if ($device_type == 1) {
                        $device_type = 'Android';
                    } else {
                        $device_type = 'Iphone';
                    }
                    $device_token = $device['device_token'];

                    if ($language_code == 'en') {
                        $notification_msg = 'You have added ' . $currency_symbol . ' ' . number_format($amount, 2) . ' in your Ara account ' . $account_no . ' on ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Updated balance ' . $currency_symbol . ' ' . number_format($update_balance, 2);
                    } else {
                        $notification_msg = 'Umeweka ' . $currency_symbol . ' ' . number_format($amount, 2) . ' kwenye akaunti yako ya Ara ' . $account_no . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Updated balance ' . $currency_symbol . ' ' . number_format($update_balance, 2);
                    }

                    /* $login_result = $this->sendPuchNotification($device_type,$device_token,$notification_msg,$totalNotifications='0',$pushMessageText="","Pull Funds");
                    $this->selcomApiRequestResponse($this->user_id, "Notification - Pull Funds", $notification_msg, $login_result);
                    
                    $notification_qry = new Notifications();
                    $notification_qry->user_id = $this->user_id;
                    $notification_qry->notification_type = 'transaction';
                    $notification_qry->notification_title = "Pull Funds";
                    $notification_qry->notification_text = $notification_msg;
                    $notification_qry->data_object = "NA";
                    $notification_qry->type = "Inside";
                    $notification_qry->save(); */
                }
            }

            $json['user_id'] = $this->user_id;
            $json['receipt'] = $receipt;
            $response[] = $json;
            return $this->sendResponse('1', $response, $msg);
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
    }

    /**
     * Check serial number
     * @return true false
     */
    public function checkSerialNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'serial_number' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $serial_number = $input['serial_number'];
        /* $linkcards = LinkCards::where('card_serial_number','=',$serial_number)->where('type','=','Physical')->first();
        if(!empty($linkcards)){
            return $this->sendError('0', trans('message.card_linked'), array(), '200');
        } */

        $json['serial_number'] = $serial_number;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.card_details'));
    }

    /**
     * Link physical card
     * @return json array
     */
    public function linkCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'serial_number' => 'required',
            'card_pin' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $serial_number = $input['serial_number'];
        if (isset($input['cvv'])) {
            $cvv = $input['cvv'];
        } else {
            $cvv = "";
        }

        $card_pin = $input['card_pin'];
        $encrpted_pin = $this->encrypt($card_pin);

        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_number = $account[0]['account_number'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;
        $first_name = $users->first_name;
        $last_name = $users->last_name;

        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['serialNo'] = $serial_number;
        $trans_param['pin'] = $encrpted_pin;
        $trans_param['firstName'] = $first_name;
        $trans_param['lastName'] = $last_name;
        if (!empty($cvv)) {
            $trans_param['cvv'] = $cvv;
        }
        $trans_json_request = json_encode($trans_param);

        $url = 'client/' . $client_id . '/link-physical-card';
        $api_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($api_response));

        if ($api_response['resultcode'] != '200') {
            return $this->sendError('0', $api_response['message'], array(), '200');
            exit;
        }

        $mask_card = "";
        $card_result = $api_response['data'];
        if (isset($card_result[0]['masked_card'])) {
            $mask_card = $card_result[0]['masked_card'];
        }

        $card_id = $card_result[0]['card_id'];

        //$encrpted_pin = $this->encrypt($card_pin);
        $api_url = 'vcn/set-pin';
        $param['msisdn'] = $country_code . $mobile_number;
        $param['account'] = $account_number;
        $param['card_id'] = $card_id;
        $param['encrpted_pin'] = $encrpted_pin;
        $param['newpin'] = $encrpted_pin;
        $selcom_response = $this->selcomDevApi($api_url, $param, true);
        //$this->selcomApiRequestResponse($this->user_id, 'vcn/set-pin', json_encode($param), json_encode($selcom_response)); 

        /* $linkcards = LinkCards::where('card_serial_number','=',$serial_number)->where('type','=','Physical')->first();
        if(empty($linkcards)){ */
        //$cvv = rand('100','999');
        $card_number = $mask_card;

        $qry = new LinkCards();
        $qry->user_id = $this->user_id;
        $qry->card_serial_number = $serial_number;
        $qry->card_id = $card_id;
        $qry->status = "1";
        $qry->card_number = $card_number;
        $qry->card_token = "";
        $qry->card_name = "ITEM";
        $qry->card_type = "1";
        $qry->type = "Physical";
        $qry->cvv = "";
        $qry->expiry = "";
        $qry->save();

        $response = array();
        $json['card_id'] = (string)$qry->id;
        $json['serial_number'] = $serial_number;
        $json['card_number'] = $card_number;
        $json['card_name'] = "";
        $json['expiry'] = "";
        $json['type'] = "Physical";
        $json['cvv'] = (string)$cvv;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.card_link_active'));
        /* }else{
            return $this->sendError('0', trans('message.card_linked'), array(), '200');
        } */
    }

    /**
     * Get VCN URL from selcom
     * @return json array
     */
    public function getVcnUrl(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $type = $input['type'];
        $card_id = $input['card_id'];

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_number = $account[0]['account_number'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;

        $linkcards = LinkCards::where('user_id', '=', $this->user_id)->where('status', '=', '1')->where('type', '=', $type)->where('id', '=', $card_id)->first();
        if (!empty($linkcards)) {
            $card_id = $linkcards->card_id;

            $api_url = 'vcn/show';
            $param['msisdn'] = $country_code . $mobile_number;
            $param['account'] = $account_number;
            $param['card_id'] = $card_id;
            $param['requestid'] = rand(1000, 9999) . substr(time(), -7);
            $selcom_response = $this->selcomDevApi($api_url, $param, 'true');

            $this->selcomApiRequestResponse($this->user_id, $api_url, json_encode($param), json_encode($selcom_response));

            if ($selcom_response['resultcode'] != '000') {
                return $this->sendError('0', $selcom_response['message'], array(), '200');
                exit;
            }
            $selcom_data = $selcom_response['data'];
            $vcn_url = $selcom_data[0]['vcn_url'];

            $response['vcn_url'] = $vcn_url;
            return $this->sendResponse('1', $response, trans('message.card_details'));
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
    }

    /**
     * Change pin using card id
     * @return true false
     */
    public function changeCardPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'card_pin' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        return $this->sendResponse('1', array(), trans('message.change_card_pin'));
    }

    /**
     * Suspend Card
     * @return true false
     */
    public function suspendCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'status' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $status = $input['status'];
        $card_id = $input['card_id'];

        $cards = LinkCards::find($card_id);
        $type = $cards['type'];
        $selcom_card_id = $cards['card_id'];

        $user = User::find($this->user_id);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];

        if ($status == '1') {
            $api_url = 'vcn/changestatus';
            $param_vcn_status['msisdn'] = $country_code . $mobile_number;
            $param_vcn_status['account'] = $account_number;
            $param_vcn_status['card_id'] = $selcom_card_id;
            $param_vcn_status['status'] = 'UNBLOCK';
            $param_vcn_status['requestid'] = rand(1000, 9999) . substr(time(), -7);
            $vcnresultstatus = $this->selcomDevApi($api_url, $param_vcn_status, 'true');

            $this->selcomApiRequestResponse($this->user_id, 'vcn/changestatus', json_encode($param_vcn_status), json_encode($vcnresultstatus));

            if ($vcnresultstatus['resultcode'] == '000') {
                LinkCards::where('status', '2')->where('id', $card_id)->update(['status' => 1]);
            } else {
                return $this->sendError('0', $vcnresultstatus['message'], $validator->errors(), '200');
            }
        } else {
            $api_url = 'vcn/changestatus';
            $param_vcn_status['msisdn'] = $country_code . $mobile_number;
            $param_vcn_status['account'] = $account_number;
            $param_vcn_status['card_id'] = $selcom_card_id;
            $param_vcn_status['status'] = 'BLOCK';
            $param_vcn_status['requestid'] = rand(1000, 9999) . substr(time(), -7);
            $vcnresultstatus = $this->selcomDevApi($api_url, $param_vcn_status, 'true');

            $this->selcomApiRequestResponse($this->user_id, 'vcn/changestatus', json_encode($param_vcn_status), json_encode($vcnresultstatus));

            if ($vcnresultstatus['resultcode'] == '000') {
                LinkCards::where('status', '1')->where('id', $card_id)->update(['status' => 2]);
            } else {
                return $this->sendError('0', $vcnresultstatus['message'], $validator->errors(), '200');
            }
        }
        return $this->sendResponse('1', array(), trans('message.suspend_card'));
    }

    /**
     * Cancel Card
     * 
     * @return update new Virtual card 
     */
    public function cancelCard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'card_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $card_id = $input['card_id'];
        $user_id = $input['user_id'];

        $cards = LinkCards::find($card_id);
        $type = $cards['type'];
        $selcom_card_id = $cards['card_id'];

        $user = User::find($user_id);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $city_id = $user->city_id;
        $address = $user->address;
        $gender = $user->gender;
        $dob = $user->dob;
        if (!empty($city_id)) {
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        } else {
            $city_name = "";
        }
        $namearr = explode(" ", $user->name);
        $first_name = $namearr[0];
        $first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $namearr[1];
        $last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];

        /* VCN change status */
        $api_url = 'vcn/changestatus';
        $param_vcn_status['msisdn'] = $country_code . $mobile_number;
        $param_vcn_status['account'] = $account_number;
        $param_vcn_status['card_id'] = $selcom_card_id;
        $param_vcn_status['status'] = 'SUSPEND';
        $param_vcn_status['requestid'] = rand(1000, 9999) . substr(time(), -7);
        $vcnresultstatus = $this->selcomDevApi($api_url, $param_vcn_status, 'true');

        $this->selcomApiRequestResponse($user_id, 'vcn/changestatus', json_encode($param_vcn_status), json_encode($vcnresultstatus));

        if ($vcnresultstatus['resultcode'] == '000') {
            LinkCards::where('id', $card_id)->update(['status' => 0]);

            if ($type == 'Virtual') {
                /* Create VCN */
                $api_url = 'vcn/create';
                $param_vcn['msisdn'] = $country_code . $mobile_number;
                $param_vcn['account'] = $account_number;
                $param_vcn['first_name'] = $first_name;
                $param_vcn['last_name'] = $last_name;
                $param_vcn['gender'] = strtoupper($gender);
                $param_vcn['dob'] = date("dmY", strtotime($dob));
                $param_vcn['address'] = $address;
                $param_vcn['city'] = $city_name;
                $param_vcn['nationality'] = 'TZ';
                $param_vcn['pin'] = '1234';
                $param_vcn['vendor'] = env('SELCOM_VENDOR');
                $param_vcn['transid'] = rand(1000, 9999) . substr(time(), -7);
                $vcnresult = $this->selcomDevApi($api_url, $param_vcn, 'true');

                $this->selcomApiRequestResponse($user_id, 'VCN', json_encode($param_vcn), json_encode($vcnresult));

                if ($vcnresult['resultcode'] == '000') {

                    $vcn_card_data = $vcnresult['data'];
                    $card_id = $vcn_card_data[0]['card_id'];
                    $masked_card = $vcn_card_data[0]['masked_card'];

                    $qry = new LinkCards();
                    $qry->user_id = $user_id;
                    $qry->card_serial_number = $card_id;
                    $qry->card_id = $card_id;
                    $qry->status = "1";
                    $qry->card_number = $masked_card;
                    $qry->card_token = "";
                    $qry->card_name = "";
                    $qry->card_type = "1";
                    $qry->expiry = "";
                    $qry->save();
                    return $this->sendResponse('1', array(), trans('message.cancel_card'));
                } else {
                    return $this->sendError('0', $vcnresult['message'], $validator->errors(), '200');
                }
            } else {
                return $this->sendResponse('1', array(), trans('message.cancel_card'));
            }
        } else {
            return $this->sendError('0', $vcnresultstatus['message'], $validator->errors(), '200');
        }

        /* $cvv = rand('100','999');
        $serial_number = rand('100000000','999999999');
        $card_number = "1111 12•• •••• ".rand('1000','9999');

        $qry = new LinkCards();
        $qry->user_id = $this->user_id;
        $qry->card_serial_number = $serial_number;
        $qry->status = "1";
        $qry->card_number = $card_number;
        $qry->card_token = rand('100000000000','999999999999');
        $qry->card_name = "SELCOM";
        $qry->card_type = "1";
        $qry->type = "Virtual";
        $qry->cvv = $cvv;
        $qry->expiry = "08-2022";
        $qry->save();

        return $this->sendResponse('1', array(), trans('message.cancel_card')); */
    }

    /**
     * Get physical card details
     * 
     * @return json array
     */
    public function getPhysicalCard(Request $request)
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

        $qry = DB::table('link_cards');
        $qry->whereRaw("user_id = '" . $this->user_id . "' AND type = 'Physical' AND status = '1'");
        $linkcards = $qry->get();
        $response = array();
        if (!empty($linkcards)) {
            foreach ($linkcards as $val) {
                $r['card_id'] = (string)$val->id;
                $r['serial_number'] = $val->card_serial_number;
                $r['card_number'] = $val->card_number;
                $r['card_name'] = $val->card_name;
                $r['type'] = $val->type;
                $r['expiry'] = "";
                $r['cvv'] = "";
                $r['status'] = (string)$val->status;
                $response[] = $r;
            }
        }
        return $this->sendResponse('1', $response, trans('message.card_details'));
    }

    /**
     * Create VCN Card
     * 
     * @return success Or Failed 
     */
    public function createVcn(Request $request)
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
        $user_id = $input['user_id'];

        $user = User::find($user_id);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $city_id = $user->city_id;
        $address = $user->address;
        $gender = $user->gender;
        $dob = $user->dob;
        if (!empty($city_id)) {
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        } else {
            $city_name = "";
        }
        $namearr = explode(" ", $user->name);
        $first_name = $namearr[0];
        $first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $namearr[1];
        $last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];

        /* Create VCN */
        $api_url = 'vcn/create';
        $param_vcn['msisdn'] = $country_code . $mobile_number;
        $param_vcn['account'] = $account_number;
        $param_vcn['first_name'] = $first_name;
        $param_vcn['last_name'] = $last_name;
        $param_vcn['gender'] = strtoupper($gender);
        $param_vcn['dob'] = date("dmY", strtotime($dob));
        $param_vcn['address'] = $address;
        $param_vcn['city'] = $city_name;
        $param_vcn['nationality'] = 'TZ';
        $param_vcn['pin'] = '1234';
        $param_vcn['vendor'] = env('SELCOM_VENDOR');
        $param_vcn['transid'] = rand(1000, 9999) . substr(time(), -7);
        $vcnresult = $this->selcomDevApi($api_url, $param_vcn, 'true');

        $this->selcomApiRequestResponse($user_id, 'VCN', json_encode($param_vcn), json_encode($vcnresult));

        if ($vcnresult['resultcode'] == '000') {

            $vcn_card_data = $vcnresult['data'];
            $card_id = $vcn_card_data[0]['card_id'];
            $masked_card = $vcn_card_data[0]['masked_card'];

            $qry = new LinkCards();
            $qry->user_id = $user_id;
            $qry->card_serial_number = $card_id;
            $qry->card_id = $card_id;
            $qry->status = "1";
            $qry->card_number = $masked_card;
            $qry->card_token = "";
            $qry->card_name = "";
            $qry->card_type = "1";
            $qry->expiry = "";
            $qry->save();
            return $this->sendResponse('1', array(), trans('message.add_vcn_card'));
        } else {

            $message = $vcnresult['message'];
            $oldTicket = IssueTickets::where('user_id', $user_id)->where('status', 'Open')->first();
            if (empty($oldTicket)) {

                $ticket_id = rand('111', '999') . time();

                $insert = new IssueTickets();
                $insert->user_id = $user_id;
                $insert->ticket_id = $ticket_id;
                $insert->module = 'Card';
                $insert->error_message = $message;
                $insert->save();

                $error_msg = trans('message.ticket_created');
                $error_msg = str_ireplace("<<tiket_id>>", $ticket_id, $error_msg);

                return $this->sendError('0', $error_msg, array(), '200');
            } else {
                return $this->sendError('0', trans('message.ticket_already_generated'), array(), '200');
            }
        }
    }

    /**
     * Create VCN Card using web panel
     * 
     * @return success Or Failed 
     */
    public function createVcnForWeb(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        /* $data['masked_card'] = "535976XXXXXX2165";
        return $this->sendResponse('1', $data, trans('message.add_vcn_card')); */

        $input = $request->all();
        $user_id = $input['user_id'];

        $user = User::find($user_id);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $city_id = $user->city_id;
        $address = $user->address;
        $gender = $user->gender;
        $dob = $user->dob;
        if (!empty($city_id)) {
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        } else {
            $city_name = "";
        }
        $namearr = explode(" ", $user->name);
        $first_name = $namearr[0];
        $first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $namearr[1];
        $last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];

        /* Create VCN */
        $api_url = 'vcn/create';
        $param_vcn['msisdn'] = $country_code . $mobile_number;
        $param_vcn['account'] = $account_number;
        $param_vcn['first_name'] = $first_name;
        $param_vcn['last_name'] = $last_name;
        $param_vcn['gender'] = strtoupper($gender);
        $param_vcn['dob'] = date("dmY", strtotime($dob));
        $param_vcn['address'] = $address;
        $param_vcn['city'] = $city_name;
        $param_vcn['nationality'] = 'TZ';
        $param_vcn['pin'] = '1234';
        $param_vcn['vendor'] = env('SELCOM_VENDOR');
        $param_vcn['transid'] = rand(1000, 9999) . substr(time(), -7);
        $vcnresult = $this->selcomDevApi($api_url, $param_vcn, 'true');

        $this->selcomApiRequestResponse($user_id, 'VCN', json_encode($param_vcn), json_encode($vcnresult));

        if ($vcnresult['resultcode'] == '000') {

            $vcn_card_data = $vcnresult['data'];
            $card_id = $vcn_card_data[0]['card_id'];
            $masked_card = $vcn_card_data[0]['masked_card'];

            $qry = new LinkCards();
            $qry->user_id = $user_id;
            $qry->card_serial_number = $card_id;
            $qry->card_id = $card_id;
            $qry->status = "1";
            $qry->card_number = $masked_card;
            $qry->card_token = "";
            $qry->card_name = "";
            $qry->card_type = "1";
            $qry->expiry = "";
            $qry->save();
            return $this->sendResponse('1', array(), trans('message.add_vcn_card'));
        } else {
            $error_msg = $vcnresult['message'];
            return $this->sendError('0', $error_msg, array(), '200');
        }
    }

    /**
     * Check mobile number in middleware
     * 
     * @return json array
     */
    public function checkMobileMoneyNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'mobile_number' => 'required',
            'wallet_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $category_id = "0";
        $wallet_id = $input['wallet_id'];
        $mobile_number = $input['mobile_number'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);

        $notes = '';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
        }

        /* $categories = Categories::find($category_id);
        if (empty($categories)) {
            return $this->sendError('0', trans('message.wrong_category'), array(), '200');
        } */

        $wallets = Wallets::find($wallet_id);
        if (empty($wallets)) {
            return $this->sendError('0', trans('message.wrong_wallet'), array(), '200');
        }
        $utility_code = $wallets['utility_code'];

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=ARA2WALLET&utilityCode=' . $utility_code . '&paymentReference=' . $mobile_number . '&amount=' . $amount . '&externalId=' . $external_id;
        $selcom_response = $this->selcomApi($url, '', '', 'GET');

        $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

        $resultcode = $selcom_response['resultcode'];
        $result = $selcom_response['result'];
        if ($resultcode != '200' && $result != 'SUCCESS') {
            $error_msg = $selcom_response['message'];
            return $this->sendError('0', $error_msg, array(), '200');
        }
        $resultdata = $selcom_response['data'];

        $response = array();
        $json['user_id'] = $this->user_id;
        $json['account_holder_name'] = $resultdata[0]['name'];
        /* $json['category_name'] = $categories['name']; */
        $json['wallet_name'] = $wallets['wallet_name'];
        $json['mobile_number'] = $mobile_number;
        $json['wallet_id'] = $wallet_id;
        $json['notes'] = $notes;
        $json['amount'] = number_format($amount);
        /* $json['category_id'] = $category_id; */
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.mobile_details'));
    }

    /**
     * Mobile money transaction
     * 
     * @return success with json data
     */
    public function mobileMoneyTrans(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'utility_code' => 'required',
            'mobile_number' => 'required',
            'wallet_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $wallet_id = $input['wallet_id'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);
        $mobile_number = $input['mobile_number'];
        $utility_code = $input['utility_code'];

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $account_balance_id = $account[0]['account_balance_id'];
        $currency_symbol = $account[0]['currency_symbol'];
        $account_number = $account[0]['account_number'];
        $arr2 = str_split($account_number, 4);
        //$account_no = '•••• '.$arr2[1];
        $account_no = '•••• ' . substr($account_number, -4);

        $users = User::find($this->user_id);
        $address = $users->address;
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        //$mobile_number = $users->mobile_number;
        $email = $users->email;
        $is_notification = $users->is_notification;

        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['amount'] = $amount;
        $trans_param['msisdn'] = $mobile_number;
        $trans_param['utilityCode'] = $utility_code;
        $trans_param['account'] = $account_number;
        $trans_json_request = json_encode($trans_param);

        $url = 'client/' . $client_id . '/mwallet-push-ussd';
        $pull_fund_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($pull_fund_response));

        if ($pull_fund_response['resultcode'] != '200') {
            return $this->sendError('0', $pull_fund_response['message'], array(), '200');
            exit;
        }

        if (isset($pull_fund_response['data'])) {
            $json_data = $pull_fund_response['data'];
            if (isset($json_data[0]['araReceipt'])) {
                $ara_receipt = $json_data[0]['araReceipt'];
            } else {
                $ara_receipt = '';
            }
        } else {
            $ara_receipt = '';
        }

        $ara_balance = $this->araAvaBalance($this->user_id);

        $msg = 'Your Ara account ' . $account_no . ' has been credited TZS ' . number_format($amount, 2) . '. Updated balance: ' . $currency_symbol . ' ' . number_format($ara_balance, 2);

        $receipt = 'You have add funds ' . $currency_symbol . ' ' . number_format($amount) . ' from ' . $account_number . ' ' . date("d-m-Y H:i", strtotime($this->datetime));

        $transactions = new Transactions();
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->ara_receipt = $ara_receipt;
        $transactions->trans_type = 10;
        $transactions->trans_status = 2;
        if (isset($input['payer_name']) && !empty($input['payer_name'])) {
            $transactions->party_name = $input['payer_name'];
        }
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        $transactions->account_number = $account_number;
        $transactions->trans_datetime = $this->datetime;
        $transactions->created_at = $this->datetime;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {

            /**
             * Add Pull Fund data
             */
            $pullFund = new MobileMoneyTransactions();
            $pullFund->user_id = $this->user_id;
            $pullFund->trans_id = $transactions->id;
            $pullFund->wallet_id = $wallet_id;
            $pullFund->trans_amount = $amount;
            $pullFund->created_at = $this->datetime;
            $pullFund->updated_at = $this->datetime;
            $pullFund->save();

            /**
             * User credit
             */
            $credit = new UserCredits();
            $credit->user_id = $this->user_id;
            $credit->trans_id = $transactions->id;
            $credit->prev_balance = $account_balance;
            $credit->trans_amount = $amount;
            $credit->created_at = $this->datetime;
            $credit->updated_at = $this->datetime;
            $credit->save();
            if ($credit->id > 0) {
                /**
                 * Ara user balance update
                 */
                /* $updateBalance = AccountBalances::find($account_balance_id);
                $updateBalance->account_balance = $account_balance+$amount; 
                $updateBalance->updated_at = $this->datetime;
                $updateBalance->save(); */
            }

            if ($is_notification == 'Yes') {
                /* Send push notification */
                $device = Devices::where('user_id', '=', $this->user_id)->first();
                if (!empty($device)) {
                    $device_type = $device['device_type'];
                    if ($device_type == 1) {
                        $device_type = 'Android';
                    } else {
                        $device_type = 'Iphone';
                    }
                    $device_token = $device['device_token'];

                    $notification_msg = 'You have added ' . $currency_symbol . ' ' . number_format($amount, 2) . ' in your Ara account ' . $account_no . ' on ' . date("d-m-Y H:i", strtotime($this->datetime));

                    /* $login_result = $this->sendPuchNotification($device_type,$device_token,$notification_msg,$totalNotifications='0',$pushMessageText="","Add Money");
                    $this->selcomApiRequestResponse($this->user_id, "Notification - Add Money", $notification_msg, $login_result);
                    
                    $notification_qry = new Notifications();
                    $notification_qry->user_id = $this->user_id;
                    $notification_qry->notification_type = 'transaction';
                    $notification_qry->notification_title = "Add Money";
                    $notification_qry->notification_text = $notification_msg;
                    $notification_qry->data_object = "NA";
                    $notification_qry->type = "Inside";
                    $notification_qry->save(); */
                }
            }

            $json['user_id'] = $this->user_id;
            $json['receipt'] = $receipt;
            $json['external_id'] = $external_id;
            $response[] = $json;
            return $this->sendResponse('1', $response, $msg);
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
    }

    /**
     * Check Mobile money transaction status using external id
     * 
     * @return success with json data
     */
    public function checkMobileMoneyTransStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'external_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $external_id = $input['external_id'];

        $trans = Transactions::where('original_externalId', '=', $external_id)->where('is_outside', '=', '1')->first();
        if (!empty($trans)) {
            $response = array();
            $json['user_id'] = $this->user_id;
            $json['ara_receipt'] = $trans['ara_receipt'];
            $json['receipt'] = $trans['notification_text'];
            $json['notification_text'] = $trans['notification_text'];
            $json['trans_status'] = (string)$trans['trans_status'];
            $response[] = $json;
            if ($trans['trans_status'] == '1') {
                return $this->sendResponse('1', $response, trans('message.mobile_money_order_success'));
            } else {
                return $this->sendResponse('1', $response, trans('message.mobile_money_order_failed'));
            }
        } else {
            $response = array();
            $json['user_id'] = $this->user_id;
            $json['trans_status'] = (string)'2';
            $response[] = $json;
            return $this->sendResponse('1', $response, trans('message.mobile_money_order_pending'));
        }
    }
}