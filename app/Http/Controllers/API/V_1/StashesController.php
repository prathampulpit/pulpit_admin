<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\CardsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\StashRepository;
use App\Models\Cards;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\Qwiksends;
use App\Models\UserCredits;
use App\Models\UserDebits;
use App\Models\Stashes;
use App\Models\User;
use App\Models\StashTransactionHistory;
use App\Models\Devices;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;

class StashesController extends BaseController
{
    protected $cardsRepository;
    protected $userAccountRepository;
    protected $stashRepository;
    protected $userRepository;

    public function __construct(
        cardsRepository $cardsRepository,
        userAccountRepository $userAccountRepository,
        stashRepository $stashRepository,
        UserRepository $userRepository
    ) {
        $this->cardsRepository = $cardsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->stashRepository = $stashRepository;
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
    public function addStash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'stash_method' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];

        $stash_method = $input['stash_method']; /* 1=Once Off, 2=Recurring */
        if ($stash_method == 1) {
            if (!isset($input['amount']) && empty($input['amount'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $amount = $input['amount'];
            $amount = number_format($amount, 2);
            $amount = str_replace(",", "", $amount);

            if (!isset($input['trans_type']) && empty($input['trans_type'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $trans_type = $input['trans_type']; /* 1 = ARA to Stash 2 = Stash to ARA */
        } else {
            if (!isset($input['per_trans_percentage']) && empty($input['per_trans_percentage'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $per_trans_percentage = $input['per_trans_percentage'];
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

        /**
         * Check user balance 
         */
        if ($stash_method == 1) {
            /* if($amount >= $account_balance )
            {
                return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
            } */

            /* if($trans_type == 1){
                $msg = 'You have added '.$currency_symbol.' '.number_format($amount).' to Ara Stash balance. Updated balance '.$currency_symbol.' '.number_format($account_balance-$amount);

                $receipt = 'You have sent '.$currency_symbol.' '.number_format($amount).' to stash '.date("d-m-Y H:i", strtotime($this->datetime));
            }else{
                $msg = 'You have withdrawn '.$currency_symbol.' '.number_format($amount).' from Ara Stash balance. Updated balance '.$currency_symbol.' '.number_format($account_balance+$amount);

                $receipt = 'You have sent '.$currency_symbol.' '.number_format($amount).' to Ara '.date("d-m-Y H:i", strtotime($this->datetime));
            } */
        }

        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;
        $is_notification = $users->is_notification;

        $stash = Stashes::where('user_id', '=', $this->user_id)->first();
        if (empty($stash)) {

            $stashAccountNumber = 0;
            $accounts = $this->selcomApi('client/' . $client_id . '/stash-info', array(), $this->user_id, "GET");
            if (!empty($accounts)) {
                $resultcode = $accounts['resultcode'];
                if ($resultcode == 200) {
                    if (isset($accounts['data'][0]['stashAccountNumber'])) {
                        $stashAccountNumber = $accounts['data'][0]['stashAccountNumber'];
                    }
                }
            }

            $insert = new Stashes();
            $insert->user_id = $this->user_id;
            $insert->stash_balance = 0;
            $insert->stash_method = $stash_method;
            if ($stash_method == '2') {
                $insert->per_trans_percentage = $per_trans_percentage;
            }
            $insert->created_at = $this->datetime;
            $insert->updated_at = $this->datetime;
            $insert->stash_account_number = $stashAccountNumber;
            $insert->save();
            $stash_id = $insert->id;
            $prev_balance = 0;
        } else {
            //$per_trans_percentage = $stash['per_trans_percentage'];

            $stashAccountNumber = $stash['stash_account_number'];
            $accounts = $this->selcomApi('client/' . $client_id . '/stash-info', array(), $this->user_id, "GET");
            if (!empty($accounts)) {
                $resultcode = $accounts['resultcode'];
                if ($resultcode == 200) {
                    if (isset($accounts['data'][0]['stashAccountNumber'])) {
                        $stashAccountNumber = $accounts['data'][0]['stashAccountNumber'];
                    }
                }
            }

            $stash_id = $stash['id'];
            $prev_balance = $stash['stash_balance'];

            if (empty($prev_balance)) {
                $prev_balance = 0;
            }
            $update = Stashes::find($stash_id);
            $update->stash_method = $stash_method;
            if ($stash_method == '2') {
                $update->per_trans_percentage = $per_trans_percentage;
            }
            $update->stash_account_number = $stashAccountNumber;
            $update->save();
        }

        $ara_receipt = '';

        if ($stash_method == '1') {
            if ($trans_type == 2) {
                /* if($amount >= $prev_balance)
                {
                    return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
                } */
            }

            if ($stash_method == '1') {
                $trans_id = time() . rand(100000, 999999);
                $external_id = rand(1000, 9999) . substr(time(), -7);
                $trans_param['externalId'] = $external_id;
                $trans_param['amount'] = $amount;
                $trans_param['currency'] = 'TZS';
                $trans_param['serviceType'] = 'STASHTRANSFER';
                $trans_param['paymentReference'] = $stashAccountNumber;
                if ($trans_type == '1') {
                    $trans_param['utilityCode'] = 'ARA2STASH';
                } else {
                    $trans_param['utilityCode'] = 'STASH2ARA';
                }
                $trans_param['categoryCode'] = 'General';
                $trans_param['category'] = 'NA';
                $trans_param['description'] = 'TZ';
                $trans_param['account'] = $account_number;
                $trans_json_request = json_encode($trans_param);
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
            }

            if ($trans_type != '1') {
                if (isset($selcom_response['data'])) {
                    $json_data = $selcom_response['data'];
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }
            } else {
                if (isset($selcom_response['data'])) {
                    $json_data = $selcom_response['data'];
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }
            }

            $stash_balance = $this->stashBalance($this->user_id);
            $ara_balance = $this->araAvaBalance($this->user_id);

            if ($trans_type == 1) {
                $msg = 'You have added ' . $currency_symbol . ' ' . number_format($amount, 2) . ' to Ara Stash balance. Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);

                $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($amount, 2) . ' to stash ' . date("d-m-Y H:i", strtotime($this->datetime));
            } else {
                $msg = 'You have withdrawn ' . $currency_symbol . ' ' . number_format($amount, 2) . ' from Ara Stash balance. Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);

                $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($amount) . ' to Ara ' . date("d-m-Y H:i", strtotime($this->datetime));
            }


            $user_ipaddress = $this->getIpAddress();

            if (isset($input['lat']) && !empty($input['lng'])) {
                $lat = $input['lat'];
                $lng = $input['lng'];
            } else {
                $lat = "0.00";
                $lng = "0.00";
            }

            /**
             * Add transaction details here
             */
            $trans_id = time() . rand(100000, 999999);
            $transactions = new Transactions();
            $transactions->user_id = $this->user_id;
            $transactions->trans_id = $external_id;
            $transactions->ara_receipt = $ara_receipt;
            $transactions->trans_type = 7;
            $transactions->trans_status = 1;
            $transactions->prev_balance = $stash_balance;
            $transactions->receipt = $receipt;
            $transactions->account_number = $account_number;
            $transactions->trans_datetime = $this->datetime;
            $transactions->user_ipaddress = $user_ipaddress;
            $transactions->latitude = $lat;
            $transactions->longitude = $lng;
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
                $stash_trans->trans_amount = $amount;
                $stash_trans->trans_type = $trans_type;
                $stash_trans->prev_balance = $prev_balance;
                $stash_trans->created_at = $this->datetime;
                $stash_trans->updated_at = $this->datetime;
                $stash_trans->save();

                if ($stash_trans->id > 0) {

                    if ($trans_type == 1) {
                        /**
                         * User debits
                         */
                        $debit = new UserDebits();
                        $debit->user_id = $this->user_id;
                        $debit->trans_id = $transactions->id;
                        $debit->prev_balance = $account_balance;
                        $debit->trans_amount = $amount;
                        $debit->created_at = $this->datetime;
                        $debit->updated_at = $this->datetime;
                        $debit->save();
                        if ($debit->id > 0) {
                            /**
                             * Ara user balance update
                             */
                            $updateBalance = AccountBalances::find($account_balance_id);
                            $updateBalance->account_balance = $ara_balance;
                            $updateBalance->updated_at = $this->datetime;
                            $updateBalance->save();

                            /**
                             * Update stash balance
                             */
                            $update_stash = Stashes::find($stash_id);
                            $update_stash->stash_balance = $stash_balance;
                            $update_stash->save();
                        }
                    } else {

                        /**
                         * User Credit
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
                            $updateBalance = AccountBalances::find($account_balance_id);
                            $updateBalance->account_balance = $ara_balance;
                            $updateBalance->updated_at = $this->datetime;
                            $updateBalance->save();

                            /**
                             * Update stash balance
                             */
                            $update_stash = Stashes::find($stash_id);
                            $update_stash->stash_balance = $stash_balance;
                            $update_stash->save();
                        }
                    }

                    if ($stash_method == 1) {

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

                                if ($stash_method == '1') {
                                    if ($trans_type == 1) {
                                        if ($language_code == 'sw') {
                                            $msg = 'Umeweka ' . $currency_symbol . ' ' . number_format($amount, 2) . ' kwenye kibubu cha Ara. Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                                        }
                                    } else {
                                        if ($language_code == 'sw') {
                                            $msg = 'Umetoa ' . $currency_symbol . ' ' . number_format($amount, 2) . ' kutoka kwenye kibubu. Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                                        }
                                    }
                                }

                                $login_result = $this->sendPuchNotification($device_type, $device_token, $msg, $totalNotifications = '0', $pushMessageText = "", "Stash");
                                $this->selcomApiRequestResponse($this->user_id, "Notification - Stash", $msg, $login_result);

                                $notification_qry = new Notifications();
                                $notification_qry->user_id = $this->user_id;
                                $notification_qry->notification_type = 'transaction';
                                $notification_qry->notification_title = "Stash";
                                $notification_qry->notification_text = $msg;
                                $notification_qry->data_object = "NA";
                                $notification_qry->type = "Inside";
                                $notification_qry->save();
                            }
                        }
                    }

                    $json['user_id'] = $this->user_id;
                    $json['receipt'] = $receipt;
                    $response[] = $json;
                    return $this->sendResponse('1', $response, $msg);
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
            } else {
                return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }
        } else {
            $response = array();

            if ($per_trans_percentage == '0') {
                if ($language_code == 'en') {
                    $msg = "You have successfully disabled savings to your Stash";
                } else {
                    $msg = "Umeacha kuweka akiba kwenye kibubu chako.";
                }
            } else {
                //$msg = "Stash ".$per_trans_percentage."% of each transaction added successfully.";
                if ($language_code == 'en') {
                    $msg = "You have chosen to save " . $per_trans_percentage . "% of each transaction to your Stash";
                } else {
                    $msg = "Umechagua kuweka akiba ya " . $per_trans_percentage . "% kwenye kibubu chako kwa kila muamala.";
                }
            }
            return $this->sendResponse('1', $response, $msg);
        }
    }

    /**
     * Stash graph
     * @return json array
     */
    public function stashGraph(Request $request)
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

        $json_arr = array();
        for ($i = 0; $i <= 11; $i++) {
            $selected_date = date('Y-m', strtotime("-$i month"));

            $params['user_id'] = $this->user_id;
            $params['selected_date'] = $selected_date;
            $stash_history = $this->stashRepository->getAllTrans($params);
            if (!$stash_history->isEmpty()) {
                $amount = $stash_history[0]->total;
                if (empty($amount)) {
                    $amount = "0";
                }
            } else {
                $amount = "0";
            }

            $d['amount'] = (string)$amount;
            $d['selected_date'] = $selected_date;
            $json_arr[] = $d;
        }
        $response = $this->stashRepository->aasort($json_arr, 'selected_date');

        return $this->sendResponse('1', $response, trans('message.all_transaction'));
    }
}