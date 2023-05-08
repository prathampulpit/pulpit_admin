<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\CardsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\QwiksendsRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\UserCredits;
use App\Models\UserDebits;
use App\Models\Categories;
use App\Models\MastercardQr;
use App\Models\UserAccounts;
use App\Models\Currencies;
use App\Models\User;
use App\Models\Stashes;
use App\Models\StashTransactionHistory;
use App\Models\Devices;
use App\Models\Notifications;
use App\Models\QwikrewardHistories;
use App\Models\TipReferences;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class MastercardQrController extends BaseController
{
    protected $qwiksendsRepository;
    protected $userAccountRepository;

    public function __construct(
        qwiksendsRepository $qwiksendsRepository,
        userAccountRepository $userAccountRepository
    ) {
        $this->qwiksendsRepository = $qwiksendsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->datetime = date("Y-m-d H:i:s");

        $this->user_id = "";
        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        }
    }

    /**
     * Check mobile number in middleware
     * 
     * @return json array
     */
    public function checkPayNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'pay_number' => 'required',
            'category_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $category_id = $input['category_id'];
        $pay_number = $input['pay_number'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);
        if (isset($input['qwikrewards_amount'])) {
            $qwikrewards_amount = $input['qwikrewards_amount'];
            $qwikrewards_amount = str_replace(",", "", $qwikrewards_amount);
        } else {
            $qwikrewards_amount = '0';
        }

        $notes = '';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
        }

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_balance = $login_user[0]['account_balance'];
        $account_number = $login_user[0]['account_number'];

        /**
         * Check user balance 
         */
        /* if(($amount-$qwikrewards_amount) >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $categories = Categories::find($category_id);
        if (empty($categories)) {
            return $this->sendError('0', trans('message.wrong_category'), array(), '200');
        }

        /* Check Masterpass Qr */
        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=UTILITYPAYMENT&utilityCode=SELCOMPAY&paymentReference=' . $pay_number . '&amount=' . $amount . '&externalId=' . $external_id;
        $selcom_response = $this->selcomApi($url, '', '', 'GET');

        $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

        $resultcode = $selcom_response['resultcode'];
        $result = $selcom_response['result'];
        if ($resultcode != '200' && $result != 'SUCCESS') {
            $error_msg = $selcom_response['message'];
            return $this->sendError('0', $error_msg, array(), '200');
        }
        $resultdata = $selcom_response['data'];
        /* End */

        /* Check Tip Reference */
        if (isset($input['tip_reference'])) {
            $tip_reference = $input['tip_reference'];
            $tipreference = TipReferences::where('tip_reference', $tip_reference)->first();
            if (!empty($tipreference)) {
                $tip_enable = '1';
            } else {
                $tip_enable = '0';
            }
        } else {
            $tip_enable = '0';
            $tip_reference = '';
            if (isset($resultdata[0]['type'])) {
                $tip_reference = $resultdata[0]['type'];
                $tipreference = TipReferences::where('tip_reference', $tip_reference)->first();
                if (!empty($tipreference)) {
                    $tip_enable = '1';
                } else {
                    $tip_enable = '0';
                }
            }
        }
        /* End */

        $tip_amount_arr = array('0' => '500', '1' => '1000', '2' => '2000', '3' => '5000', '4' => '10000');

        $response = array();
        $json['user_id'] = $this->user_id;
        $json['pay_number'] = $pay_number;
        $json['account_holder_name'] = $resultdata[0]['name'];
        $json['category_name'] = $categories['name'];
        $json['notes'] = $notes;
        $json['fullamount'] = number_format($amount, 2);
        $json['amount'] = number_format($amount - $qwikrewards_amount, 2);
        $json['qwikrewards_amount'] = number_format($qwikrewards_amount, 2);
        $json['category_id'] = $category_id;
        $json['tip_enable'] = $tip_enable;
        $json['tip_amount_arr'] = $tip_amount_arr;
        $json['tip_receiver_number'] = $tip_reference;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.mobile_details'));
    }

    /**
     * Add card details method
     * @return json array
     */
    public function mastercardQrPayement(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'qwikrewards_amount' => 'required',
            'pay_number' => 'required',
            'category_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];

        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);
        $category_id = $input['category_id'];
        $notes = '';
        $description = 'TZ';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
            $description = $input['notes'];
        }
        $pay_number = $input['pay_number'];

        $cat = Categories::find($category_id);
        $category_code = $cat['code'];
        $category_name = $cat['name'];

        $qwikrewards_amount = $input['qwikrewards_amount'];
        $qwikrewards_amount = str_replace(",", "", $qwikrewards_amount);
        $pay_with_qwikrewards = '0';
        if ($qwikrewards_amount != '0.00') {
            $pay_with_qwikrewards = '1';
        }

        if (isset($input['lat']) && !empty($input['lng'])) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } else {
            $lat = "0.00";
            $lng = "0.00";
        }

        $stash = Stashes::where('user_id', '=', $this->user_id)->where('per_trans_percentage', '!=', 0)->first();
        if (!empty($stash)) {
            $per_trans_percentage = $stash['per_trans_percentage'];
            $stash_account_number = $stash['stash_account_number'];
            $trans_amount = $input['amount'];
            $trans_amount = str_replace(",", "", $trans_amount);
            /* if($pay_with_qwikrewards == '1'){
                $t_amount = $trans_amount - $qwikrewards_amount;
                $amount_per = ($t_amount*$per_trans_percentage)/100;
            }else{
                $amount_per = ($trans_amount*$per_trans_percentage)/100;
            } */
            $amount_per = ($trans_amount * $per_trans_percentage) / 100;
            $amount_per = number_format($amount_per);
            $amount_per = str_replace(",", "", $amount_per);
            $amount = $amount_per + $trans_amount;

            if ($amount_per > 0) {
                $stash_id = $stash['id'];
                $prev_balance = $stash['stash_balance'];
                $is_stash = 1;
            } else {
                $amount = $input['amount'];
                $trans_amount = $input['amount'];
                $trans_amount = str_replace(",", "", $trans_amount);
                $amount = str_replace(",", "", $amount);
                $is_stash = 0;
            }
        } else {
            $amount = $input['amount'];
            $trans_amount = $input['amount'];
            $trans_amount = str_replace(",", "", $trans_amount);
            $amount = str_replace(",", "", $amount);
            $is_stash = 0;
        }

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

        $pay_name = "Selcom";

        /**
         * Get qwikrewards balance
         */
        $accounts = DB::table('user_accounts')->where('user_id', '=', $this->user_id)->first();
        $qwikrewards_amount_balance = $accounts->quickrewards_balance;


        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */
        $trans_id = time() . rand(100000, 999999);


        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;
        $is_notification = $users->is_notification;
        $ara_receipt = '';
        $party_name = '';

        $trans_id = time() . rand(100000, 999999);
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $user_ipaddress = $this->getIpAddress();

        /* Transaction generate here before selcom API call */
        $trans_insert = new Transactions();
        $trans_insert->user_id = $this->user_id;
        $trans_insert->trans_id = $external_id;
        $trans_insert->category_id = $category_id;
        $trans_insert->trans_type = 6;
        $trans_insert->trans_status = 0;
        $trans_insert->trans_amount_type = '0';
        $trans_insert->prev_balance = $account_balance;
        $trans_insert->receipt = '';
        $trans_insert->account_number = $pay_number;
        $trans_insert->trans_datetime = $this->datetime;
        $trans_insert->user_ipaddress = $user_ipaddress;
        $trans_insert->latitude = $lat;
        $trans_insert->longitude = $lng;
        $trans_insert->created_at = $this->datetime;
        $trans_insert->updated_at = $this->datetime;
        $trans_insert->save();
        $t_id = $trans_insert->id;
        /* End */

        $trans_param['externalId'] = $external_id;
        $trans_param['amount'] = $trans_amount; //($trans_amount-$qwikrewards_amount);
        $trans_param['currency'] = 'TZS';
        $trans_param['serviceType'] = 'UTILITYPAYMENT';
        $trans_param['paymentReference'] = $pay_number;
        $trans_param['utilityCode'] = 'SELCOMPAY';
        $trans_param['categoryCode'] = $category_code;
        $trans_param['category'] = $category_name;
        $trans_param['description'] = $description;
        $trans_param['account'] = $account_number;
        if ($pay_with_qwikrewards == '1') {
            $trans_param['msisdn'] = $country_code . $mobile_number;
            $trans_param['sqrAmount'] = $qwikrewards_amount;
        }

        if (isset($input['tip_amount']) && isset($input['tip_receiver_number'])) {
            $trans_param['tipAmount'] = $input['tip_amount'];
            $trans_param['tipReference'] = $input['tip_receiver_number'];
        }

        if (isset($input['merchant_name']) && !empty($input['merchant_name'])) {
            $trans_param['name'] = $input['merchant_name'];
        }

        $trans_json_request = json_encode($trans_param);
        $url = 'client/' . $client_id . '/transaction';
        $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

        if ($selcom_response['resultcode'] != '200') {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
            exit;
        }
        $receipt = $selcom_response['message'];

        if (isset($selcom_response['data'])) {
            $json_data = $selcom_response['data'];
            $ara_receipt = $json_data[0]['araReceipt'];

            if (isset($json_data[0]['note'])) {
                $party_name = $json_data[0]['note'];
            } else {
                $party_name = '';
            }
        } else {
            $ara_receipt = '';
            $party_name = '';
        }

        /* if($is_stash == 1){   
            $msg = 'Your Ara account '.$account_no.' has been debited '.$currency_symbol.' '.number_format($trans_amount-$qwikrewards_amount).', '.$currency_symbol.' '.number_format($amount_per).' from the previous transaction has been added to Ara Stash balance. Updated balance '.$currency_symbol.' '.number_format($ara_balance);
        }else{
            $msg = 'Your Ara account '.$account_no.' has been debited '.$currency_symbol.' '.number_format($trans_amount-$qwikrewards_amount).'. Updated balance '.$currency_symbol.' '.number_format($ara_balance);
        } */

        //$receipt = 'Mastercard QR Payment successful Malipo yamekamilika BETPAWA Merchant# '.$pay_name.' '.$currency_symbol.number_format($amount).' TransID '.$trans_id.'Ref '.time().' Channel Tigo Pesa From 255654105525';
        $ara_receipt_stash = '';
        if ($is_stash == 1) {
            $external_id1 = rand(1000, 9999) . substr(time(), -7);
            $trans_param_stash['externalId'] = $external_id1;
            $trans_param_stash['amount'] = $amount_per;
            $trans_param_stash['currency'] = 'TZS';
            $trans_param_stash['serviceType'] = 'STASHTRANSFER';
            $trans_param_stash['paymentReference'] = $stash_account_number;
            $trans_param_stash['utilityCode'] = 'ARA2STASH';
            $trans_param_stash['categoryCode'] = 'General';
            $trans_param_stash['category'] = 'NA';
            $trans_param_stash['description'] = 'TZ';
            $trans_param_stash['account'] = $account_number;
            $trans_json_request = json_encode($trans_param_stash);
            $url = 'client/' . $client_id . '/stash-transfer';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if ($selcom_response['resultcode'] != '200') {
                if ($selcom_response['message'] != null) {
                    return $this->sendError('0', $selcom_response['message'], array(), '200');
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
                exit;
            }
            $stash_balance = $this->stashBalance($this->user_id);

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                $ara_receipt_stash = $json_data[0]['araReceipt'];
            } else {
                $ara_receipt_stash = '';
            }
        }

        $ara_balance = $this->araAvaBalance($this->user_id);
        $qwikreward_balance = '0';
        if ($pay_with_qwikrewards == '1') {
            $qwikreward_balance = $this->qwikrewardsBalance($this->user_id);
        }

        $t_amount = $trans_amount - $qwikrewards_amount;

        $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . '. Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);

        /**
         * Add transaction details here
         */
        $transactions = Transactions::find($t_id);
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->ara_receipt = $ara_receipt;
        $transactions->category_id = $category_id;
        $transactions->trans_type = 6;
        $transactions->trans_status = 1;
        $transactions->prev_balance = $account_balance;
        $transactions->party_name = $party_name;
        $transactions->receipt = $receipt;
        $transactions->account_number = $pay_number;
        $transactions->trans_datetime = $this->datetime;
        $transactions->user_ipaddress = $user_ipaddress;
        $transactions->latitude = $lat;
        $transactions->longitude = $lng;
        $transactions->created_at = $this->datetime;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {
            /**
             * Add quick send details
             */
            $master = new MastercardQr();
            $master->user_id = $this->user_id;
            $master->trans_id = $t_id;
            $master->pay_with_qwikrewards = $pay_with_qwikrewards;
            $master->qwikrewards_amount = $qwikrewards_amount;
            $master->category_id = $category_id;
            $master->trans_amount = $trans_amount;
            if (isset($input['tip_amount']) && isset($input['tip_receiver_number'])) {
                $master->tip_amount = $input['tip_amount'];
                $master->tip_reference = $input['tip_receiver_number'];
            }
            $master->notes = $notes;
            $master->created_at = $this->datetime;
            $master->updated_at = $this->datetime;
            $master->save();

            if ($master->id > 0) {

                /**
                 * User debits
                 */
                $debit = new UserDebits();
                $debit->user_id = $this->user_id;
                $debit->trans_id = $t_id;
                $debit->prev_balance = $account_balance;
                $debit->trans_amount = $trans_amount;
                $debit->created_at = $this->datetime;
                $debit->updated_at = $this->datetime;
                $debit->save();
                if ($debit->id > 0) {
                    /**
                     * Ara user balance update
                     */
                    /* $updateBalance = AccountBalances::find($account_balance_id);
                    $updateBalance->account_balance = $account_balance-($trans_amount-$qwikrewards_amount);
                    $updateBalance->updated_at = $this->datetime;
                    $updateBalance->save(); */
                }

                if ($is_stash == 1) {
                    $stash_notification_msg1 = 'You have added ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' to Ara Stash balance. Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);

                    /**
                     * Add transaction details here
                     */
                    $trans_id1 = rand(1000, 9999) . substr(time(), -7);
                    $transactions = new Transactions();
                    $transactions->user_id = $this->user_id;
                    $transactions->trans_id = $external_id1;
                    $transactions->ara_receipt = $ara_receipt_stash;
                    $transactions->trans_type = 7;
                    $transactions->trans_status = 1;
                    $transactions->prev_balance = $stash_balance;
                    $transactions->receipt = $stash_notification_msg1;
                    $transactions->category_id = $category_id;
                    $transactions->account_number = $account_number;
                    $transactions->trans_datetime = $this->datetime;
                    $transactions->created_at = $this->datetime;
                    $transactions->updated_at = $this->datetime;
                    $transactions->save();
                    if ($transactions->id > 0) {

                        /**
                         * Add Stash send details
                         */
                        $stash_trans = new StashTransactionHistory();
                        $stash_trans->trans_id = $transactions->id;
                        $stash_trans->stash_id = $stash_id;
                        $stash_trans->trans_amount = $amount_per;
                        $stash_trans->trans_type = '1';
                        $stash_trans->prev_balance = $stash_balance;
                        $stash_trans->created_at = $this->datetime;
                        $stash_trans->updated_at = $this->datetime;
                        $stash_trans->save();
                    }
                }

                if ($pay_with_qwikrewards == '1') {

                    $q_notification_msg1 = 'You have withdrawn ' . $currency_symbol . ' ' . number_format($qwikrewards_amount, 2) . ' from Qwikreward balance. Updated balance ' . $currency_symbol . ' ' . number_format($qwikreward_balance, 2);

                    /**
                     * Add transaction details here
                     */
                    $trans_id2 = time() . rand(100000, 999999);
                    $transactions = new Transactions();
                    $transactions->user_id = $this->user_id;
                    $transactions->trans_id = $trans_id2;
                    $transactions->trans_type = 8;
                    $transactions->trans_status = 1;
                    $transactions->prev_balance = $qwikreward_balance;
                    $transactions->receipt = $q_notification_msg1;
                    $transactions->account_number = $account_number;
                    $transactions->trans_datetime = $this->datetime;
                    $transactions->created_at = $this->datetime;
                    $transactions->updated_at = $this->datetime;
                    $transactions->save();
                    if ($transactions->id > 0) {
                        /**
                         * Add Qwikreward details
                         */
                        $q_trans = new QwikrewardHistories();
                        $q_trans->trans_id = $transactions->id;
                        $q_trans->trans_amount = $qwikrewards_amount;
                        $q_trans->prev_balance = $qwikreward_balance;
                        $q_trans->created_at = $this->datetime;
                        $q_trans->updated_at = $this->datetime;
                        $q_trans->save();
                    }
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

                        //$notification_msg = 'Your Ara account '.$account_no.' has been debited '.$currency_symbol.' '.number_format($t_amount).'. Updated balance '.$currency_symbol.' '.number_format($ara_balance);

                        if (isset($_POST['merchant_name'])) {
                            $merchant_name = $_POST['merchant_name'];
                        } else {
                            $merchant_name = '•••• ' . substr($pay_number, -4);
                        }

                        if ($language_code == 'en') {
                            $notification_msg = 'You have paid ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to ' . $merchant_name . ' at ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                        } else {
                            $notification_msg = 'Umelipa ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' kwenda ' . $merchant_name . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                        }

                        /* $login_result = $this->sendPuchNotification($device_type,$device_token,$notification_msg,$totalNotifications='0',$pushMessageText="","Mastercard QR");
                        $this->selcomApiRequestResponse($this->user_id, "Notiification - MastercardQr", $notification_msg, $login_result);

                        $notification_qry = new Notifications();
                        $notification_qry->user_id = $this->user_id;
                        $notification_qry->notification_type = 'transaction';
                        $notification_qry->notification_title = "MastercardQr";
                        $notification_qry->notification_text = $notification_msg;
                        $notification_qry->data_object = "NA";
                        $notification_qry->type = "Inside";
                        $notification_qry->save(); */

                        /* Stash Transaction Notification */
                        if ($is_stash == 1) {
                            //$stash_notification_msg = 'You have added '.$currency_symbol.' '.number_format($amount_per).' to Ara Stash balance. Updated balance '.$currency_symbol.' '.number_format($stash_balance);

                            if ($language_code == 'en') {
                                $stash_notification_msg = 'You have added ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' to Ara Stash balance. Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                            } else {
                                $stash_notification_msg = 'Umeweka ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' kwenye kibubu cha Ara. Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                            }

                            $login_result = $this->sendPuchNotification($device_type, $device_token, $stash_notification_msg, $totalNotifications = '0', $pushMessageText = "", "Stash");
                            $this->selcomApiRequestResponse($this->user_id, "Notification - MastercardQr", $stash_notification_msg, $login_result);

                            $notification_qry = new Notifications();
                            $notification_qry->user_id = $this->user_id;
                            $notification_qry->notification_type = 'transaction';
                            $notification_qry->notification_title = "MastercardQr - Stash Transaction";
                            $notification_qry->notification_text = $stash_notification_msg;
                            $notification_qry->data_object = "NA";
                            $notification_qry->type = "Inside";
                            $notification_qry->save();
                        }
                        /* End */
                    }
                }
                /* End */

                $json['user_id'] = $this->user_id;
                $json['trans_amount'] = number_format($t_amount, 2);
                if ($is_stash == 1) {
                    $json['stash_trans_amount'] = number_format($amount_per, 2);
                } else {
                    $json['stash_trans_amount'] = "0";
                }

                if ($pay_with_qwikrewards == '1') {
                    $json['qwikrewards_amount'] = "-" . number_format($qwikrewards_amount, 2);
                } else {
                    $json['qwikrewards_amount'] = "0";
                }
                $json['receipt'] = $receipt;
                $response[] = $json;
                return $this->sendResponse('1', $response, $msg);
            } else {
                return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
    }

    /**
     * List of other category list
     * 
     * @return json array
     */
    /* public function mastercodeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required'
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $categories = Categories::where('status','=','1')->where('type','=','1')->get();
        if($categories->isEmpty()){
            return $this->sendError('0', trans('message.blank_category'), $validator->errors(), '200');
        }
        $biller_path = config('custom.upload.category');
        $response = array();
        foreach($categories as $val){
            $d['category_id'] = (string)$val['id'];
            $d['category_name'] = $val['name'];
            $d['category_code'] = $val['code'];
            $response[] = $d;
        }
    } */
}