<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\CardsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\QwiksendsRepository;
use App\Models\User;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\Qwiksends;
use App\Models\UserCredits;
use App\Models\UserDebits;
use App\Models\Banks;
use App\Models\RemittanceBanks;
use App\Models\RemittanceWallets;
use App\Models\Categories;
use App\Models\Wallets;
use App\Models\RemittanceCountries;
use App\Models\UserAccounts;
use App\Models\Devices;
use App\Models\Notifications;
use App\Models\Stashes;
use App\Models\StashTransactionHistory;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use DB;

class QwiksendsController extends BaseController
{
    protected $cardsRepository;
    protected $userAccountRepository;
    protected $qwiksendsRepository;
    protected $userRepository;

    public function __construct(
        cardsRepository $cardsRepository,
        userAccountRepository $userAccountRepository,
        qwiksendsRepository $qwiksendsRepository,
        UserRepository $userRepository
    ) {
        $this->cardsRepository = $cardsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->qwiksendsRepository = $qwiksendsRepository;
        $this->userRepository = $userRepository;
        $this->datetime = date("Y-m-d H:i:s");

        $this->user_id = "";
        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        }
    }

    /**
     * Add card details method
     * payment type: 1, 2 and 3 ( 1 = Ara to Ara, 2 = Other Bank and 3 = Mobile Wallet )
     * @return json array
     */
    public function qwikSend(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'category_id' => 'required',
            'payment_type' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];

        $payment_type = $input['payment_type']; /* 1=Ara to Ara, 2=Other bank, 3=Wallet */
        $amount = $input['amount'];
        $amount = number_format($amount, 2);
        $amount = str_replace(",", "", $amount);
        $category_id = $input['category_id'];
        $notes = '';
        $description = 'TZ';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
            $description = $input['notes'];
        }

        if (isset($input['lat']) && !empty($input['lng'])) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } else {
            $lat = "0.00";
            $lng = "0.00";
        }

        $cat = Categories::find($category_id);
        $category_code = $cat['code'];
        $category_name = $cat['name'];

        $stash = Stashes::where('user_id', '=', $this->user_id)->where('per_trans_percentage', '!=', 0)->first();
        if (!empty($stash)) {
            $per_trans_percentage = $stash['per_trans_percentage'];
            $stash_account_number = $stash['stash_account_number'];
            $trans_amount = $input['amount'];
            $trans_amount = str_replace(",", "", $trans_amount);
            $amount_per = ($trans_amount * $per_trans_percentage) / 100;
            $amount_per = number_format($amount_per);
            $amount_per = str_replace(",", "", $amount_per);
            $amount = $amount_per + $trans_amount;

            $stash_id = $stash['id'];
            $prev_balance = $stash['stash_balance'];
            if ($amount_per > 0) {
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

        if ($payment_type == 2) {
            if (empty($input['bank_id']) || empty($input['account_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $bank_id = $input['bank_id'];
            $bank = Banks::find($bank_id);
            if (empty($bank)) {
                return $this->sendError('0', trans('message.wrong_bank_account_number'), $validator->errors(), '200');
            }
            $bank_name = $bank['bank_name'];
            $utility_code = $bank['utility_code'];
            $account_number = $input['account_number'];
            $ac_number = $input['account_number'];
            $to_user_id = 0;
        } else if ($payment_type == 3) {
            if (empty($input['mobile_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }

            $account_number = $input['mobile_number'];
            $wallet_id = "0"; //$input['wallet_id'];
            /* $wallet = Wallets::find($wallet_id);
            if(empty($wallet)){
                return $this->sendError('0', trans('message.wrong_wallet'), $validator->errors(), '200');
            } */
            $wallet_name = ""; //$wallet['wallet_name'];
            $utility_code = "CASHIN"; //$wallet['utility_code'];
            $to_user_id = 0;
            $bank_id = 0;

            $ac_number = $input['mobile_number'];
        } else {
            $bank_id = 0;
            if (empty($input['mobile_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $mobile_number = $input['mobile_number'];

            $ac_number = $input['mobile_number'];

            $check_str_length = strlen($mobile_number);
            if ($check_str_length > 12) {
                $mobile_number = $mobile_number;
            } else {
                if ($check_str_length == '10') {
                    $mobile_number = ltrim($mobile_number, '0');
                } else if ($check_str_length == '12') {
                    $mobile_number = substr($mobile_number, 3);
                }
            }
            $params['mobile_number'] = $mobile_number;
            $params['currency_id'] = 1;
            $users = $this->userAccountRepository->checkMobileOrAccountNumber($params);

            if ($users->isEmpty()) {
                //return $this->sendError('0', trans('message.wrong_mobile'), array(), '200');

                $to_name = "NA";
                $to_account_balance = "0";
                $to_user_account_no = "";
                $to_account_balance_id = "0";
                $toaccount_no = '0';

                $userexits = User::where('mobile_number', $mobile_number)->first();
                if (!empty($userexits)) {
                    $to_user_id = $userexits->id;
                } else {
                    $check_country_code = substr($mobile_number, 0, 3);
                    if ($check_country_code == '255') {
                        $mobile_number = substr($mobile_number, 3);
                    }
                    $input1['mobile_number'] = $mobile_number;
                    $input1['country_code'] = "255";
                    $input1['register_step'] = 1;
                    $user = User::create($input1);
                    $to_user_id = $user->id;
                }
            } else {
                $to_user_id = $users[0]['user_id'];
                $to_name = $users[0]['name'];
                $to_user_param['user_id'] = $to_user_id;
                $to_user_param['currency_id'] = '1';
                $to_user_arr = $this->userAccountRepository->getUserBalance($to_user_param);
                $to_account_balance = $to_user_arr[0]['account_balance'];
                $to_user_account_no = $to_user_arr[0]['account_number'];
                $to_account_balance_id = $to_user_arr[0]['account_balance_id'];

                $toaccount_no = '•••• ' . substr($to_user_account_no, -4);
            }
        }

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $user_account_number = $account[0]['account_number'];
        $account_balance = $account[0]['account_balance'];
        $account_balance_id = $account[0]['account_balance_id'];
        $currency_symbol = $account[0]['currency_symbol'];
        if ($payment_type != 2) {
            $account_number = $account[0]['account_number'];
        }
        //$arr2 = str_split($account_number, 4);
        //$account_no = '•••• '.$arr2[1];
        //$account_no = '•••• '.substr($account_number, -4);
        $account_no = '•••• ' . substr($user_account_number, -4);

        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $trans_id = time() . rand(100000, 999999);

        $users = User::find($this->user_id);
        $name = $users->name;
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $is_notification = $users->is_notification;

        $trans_id = time() . rand(100000, 999999);
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $user_ipaddress = $this->getIpAddress();

        /* Transaction generate here before selcom API call */
        $trans_insert = new Transactions();
        $trans_insert->user_id = $this->user_id;
        $trans_insert->trans_id = $external_id;
        $trans_insert->category_id = $category_id;
        $trans_insert->trans_type = 4;
        $trans_insert->trans_status = 0;
        $trans_insert->trans_amount_type = '0';
        $trans_insert->receipt = '';
        if (isset($ac_number)) {
            $trans_insert->account_number = $ac_number;
        } else {
            $trans_insert->account_number = $account_number;
        }
        $trans_insert->trans_datetime = $this->datetime;
        $trans_insert->user_ipaddress = $user_ipaddress;
        $trans_insert->latitude = $lat;
        $trans_insert->longitude = $lng;
        $trans_insert->created_at = $this->datetime;
        $trans_insert->updated_at = $this->datetime;
        $trans_insert->save();
        $t_id = $trans_insert->id;
        /* End */

        $ara_receipt = '';
        if ($payment_type == 3) {
            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $trans_amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'ARA2WALLET';
            $trans_param['paymentReference'] = $input['mobile_number'];
            $trans_param['utilityCode'] = $utility_code;
            $trans_param['categoryCode'] = $category_code;
            $trans_param['category'] = $category_name;
            $trans_param['description'] = $description;
            $trans_param['account'] = $user_account_number;
            $trans_param['service'] = '1';
            if (isset($input['payer_name']) && !empty($input['payer_name'])) {
                $trans_param['name'] = $input['payer_name'];
            }
            $trans_json_request = json_encode($trans_param);
            $url = 'client/' . $client_id . '/transaction';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if ($selcom_response['resultcode'] != '200') {
                return $this->sendError('0', $selcom_response['message'], array(), '200');
                exit;
            }

            $ara_balance = $this->araAvaBalance($this->user_id);
            //$receipt = str_ireplace("\n"," ", $selcom_response['message']);
            $receipt = $selcom_response['message'];

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                //$ara_receipt = $json_data[0]['araReceipt'];
                if (isset($json_data[0]['araReceipt'])) {
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }

                if (isset($json_data[0]['note'])) {
                    $party_name = $json_data[0]['note'];
                } else {
                    $party_name = '';
                }
            } else {
                $ara_receipt = '';
                $party_name = '';
            }
        } else if ($payment_type == 2) {
            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $trans_amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'QWIKSEND';
            $trans_param['paymentReference'] = $input['account_number'];
            $trans_param['utilityCode'] = $utility_code;
            $trans_param['categoryCode'] = $category_code;
            $trans_param['category'] = $category_name;
            $trans_param['description'] = $description;
            $trans_param['account'] = $user_account_number;
            $trans_param['service'] = '1';
            if (isset($input['payer_name']) && !empty($input['payer_name'])) {
                $trans_param['name'] = $input['payer_name'];
            }
            $trans_json_request = json_encode($trans_param);
            $url = 'client/' . $client_id . '/transaction';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if ($selcom_response['resultcode'] != '200') {
                return $this->sendError('0', $selcom_response['message'], array(), '200');
                exit;
            }
            $ara_balance = $this->araAvaBalance($this->user_id);

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                //$ara_receipt = $json_data[0]['araReceipt'];
                if (isset($json_data[0]['araReceipt'])) {
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }

                if (isset($json_data[0]['note'])) {
                    $party_name = $json_data[0]['note'];
                } else {
                    $party_name = '';
                }
            } else {
                $ara_receipt = '';
                $party_name = '';
            }
        } else {
            $mobile_number = $input['mobile_number'];
            $check_str_length = strlen($mobile_number);
            if ($check_str_length > '12') {
                $mobile_number = $mobile_number;
            } else {
                if ($check_str_length == '10') {
                    $mobile_number = ltrim($mobile_number, '0');
                    $mobile_number = '255' . $mobile_number;
                } else if ($check_str_length == '12') {
                    $mobile_number = $mobile_number;
                } else if ($check_str_length == '9') {
                    $mobile_number = '255' . $mobile_number;
                } else {
                    $mobile_number = $mobile_number;
                }
            }

            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $trans_amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'ARA2ARA';
            $trans_param['paymentReference'] =  $mobile_number;
            $trans_param['utilityCode'] = 'ARA2ARA';
            $trans_param['categoryCode'] = $category_code;
            $trans_param['category'] = $category_name;
            $trans_param['description'] = $description;
            $trans_param['account'] = $user_account_number;
            $trans_param['service'] = '1';
            $trans_json_request = json_encode($trans_param);
            $url = 'client/' . $client_id . '/fund-transfer';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if ($selcom_response['resultcode'] != '200') {
                return $this->sendError('0', $selcom_response['message'], array(), '200');
                exit;
            }

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                //$ara_receipt = $json_data[0]['araReceipt'];
                if (isset($json_data[0]['araReceipt'])) {
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }

                if (isset($json_data[0]['note'])) {
                    $party_name = $json_data[0]['note'];
                } else {
                    $party_name = '';
                }
            } else {
                $ara_receipt = '';
                $party_name = '';
            }
        }

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
            $trans_param_stash['account'] = $user_account_number;
            $trans_json_request = json_encode($trans_param_stash);
            $url = 'client/' . $client_id . '/stash-transfer';
            $selcom_response1 = $this->selcomApi($url, $trans_json_request, $this->user_id);

            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response1));

            if ($selcom_response1['resultcode'] != '200') {
                if ($selcom_response1['message'] != null) {
                    return $this->sendError('0', $selcom_response1['message'], array(), '200');
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
                exit;
            }
            $stash_balance = $this->stashBalance($this->user_id);

            if (isset($selcom_response1['data'])) {
                $json_data = $selcom_response1['data'];
                $ara_receipt_stash = $json_data[0]['araReceipt'];
            } else {
                $ara_receipt_stash = '';
            }
        }
        $ara_balance = $this->araAvaBalance($this->user_id);

        $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . '. Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);

        if ($payment_type == 1) {

            if ($toaccount_no == '0') {
                $receipt = $selcom_response['message'];
            } else {
                $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to Ara account ' . $toaccount_no . ' ' . $to_name . ' ' . date("d-m-Y H:i", strtotime($this->datetime));
            }
        } else if ($payment_type == 2) {
            //$receipt = 'You have sent '.$currency_symbol.' '.number_format($trans_amount).' to '.$bank_name.' Account '.$account_number.' account holder name '.date("d-m-Y H:i", strtotime($this->datetime));

            $receipt = $selcom_response['message'];
            //$receipt = $ara_receipt.' Confirmed. You have sent '.$currency_symbol.' '.number_format($trans_amount).' to Bank of India (TZ) '.$party_name.' on '.date("d-m-Y H:i", strtotime($this->datetime)).'. Updated balance is '.$currency_symbol.' '.number_format($ara_balance).'. Thank you for using Ara.';

        } else if ($payment_type == 3) {
            //$receipt = 'You have sent '.$currency_symbol.number_format($amount).' to '.$wallet_name.' +255'.$account_number.' account holder name '.date("d-m-Y H:i", strtotime($this->datetime));
        }



        /**
         * Add transaction details here
         */
        $trans_id = time() . rand(100000, 999999);
        $transactions = Transactions::find($t_id);
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->ara_receipt = $ara_receipt;
        $transactions->category_id = $category_id;
        $transactions->trans_type = 4;
        $transactions->trans_status = 1;
        $transactions->party_name = $party_name;
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        if (isset($ac_number)) {
            $transactions->account_number = $ac_number;
        } else {
            $transactions->account_number = $account_number;
        }
        $transactions->user_ipaddress = $user_ipaddress;
        $transactions->latitude = $lat;
        $transactions->longitude = $lng;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {
            /**
             * Add quick send details
             */
            $qwikSends = new Qwiksends();
            $qwikSends->user_id = $this->user_id;
            $qwikSends->trans_id = $t_id;
            if ($payment_type == 1) {
                $qwikSends->to_user_id = $to_user_id;
            } else if ($payment_type == 2) {
                $qwikSends->bank_id = $bank_id;
            } else if ($payment_type == 3) {
                $qwikSends->wallet_id = $wallet_id;
            }
            $qwikSends->type = $payment_type;
            $qwikSends->category_id = $category_id;
            $qwikSends->trans_amount = $trans_amount;
            $qwikSends->notes = $notes;
            $qwikSends->created_at = $this->datetime;
            $qwikSends->updated_at = $this->datetime;
            $qwikSends->save();

            if ($qwikSends->id > 0) {

                /**
                 * User debits
                 */
                $debit = new UserDebits();
                $debit->user_id = $this->user_id;
                $debit->trans_id = $t_id;
                $debit->prev_balance = $account_balance;
                $debit->trans_amount = $amount;
                $debit->created_at = $this->datetime;
                $debit->updated_at = $this->datetime;
                $debit->save();
                if ($debit->id > 0) {
                    /**
                     * Ara user balance update
                     */
                    /* $updateBalance = AccountBalances::find($account_balance_id);
                    $updateBalance->account_balance = $ara_balance;
                    $updateBalance->updated_at = $this->datetime;
                    $updateBalance->save(); */
                }

                if ($payment_type == 1) {
                    /**
                     * User credit
                     */
                    $credit = new UserCredits();
                    $credit->user_id = $to_user_id;
                    $credit->trans_id = $t_id;
                    $credit->prev_balance = $to_account_balance;
                    $credit->trans_amount = $amount;
                    $credit->created_at = $this->datetime;
                    $credit->updated_at = $this->datetime;
                    $credit->save();
                    if ($credit->id > 0) {
                        if ($toaccount_no != '0') {
                            /**
                             * Ara user balance update for opposite user
                             */
                            /* $toUpdateBalance = AccountBalances::find($to_account_balance_id);
                            $toUpdateBalance->account_balance = $to_account_balance+$amount;
                            $toUpdateBalance->updated_at = $this->datetime;
                            $toUpdateBalance->save(); */
                        }
                    }
                }

                if ($is_stash == 1) {
                    $stash_notification_msg1 = 'You have added ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' to Ara Stash balance. Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);

                    /**
                     * Add transaction details here
                     */
                    $trans_id1 = time() . rand(100000, 999999);
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

                /* Send push notification */
                if ($is_notification == 'Yes') {
                    $device = Devices::where('user_id', '=', $this->user_id)->first();
                    if (!empty($device)) {
                        $device_type = $device['device_type'];
                        if ($device_type == 1) {
                            $device_type = 'Android';
                        } else {
                            $device_type = 'Iphone';
                        }
                        $device_token = $device['device_token'];

                        if ($payment_type == 1) {

                            if ($toaccount_no != '0') {
                                $op_users = User::find($to_user_id);
                                $opname = $op_users->name;

                                if ($language_code == 'en') {
                                    $notification_txt_msg = 'You have sent ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to ' . $opname . ' at ' . date("d-m-Y H:i", strtotime($this->datetime));
                                } else {
                                    $notification_txt_msg = 'Umetuma ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' kwenda ' . $opname . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime));
                                }
                            } else {

                                $mobile_number = $input['mobile_number'];
                                $check_str_length = strlen($mobile_number);
                                if ($check_str_length > '12') {
                                    $to_mobile_number = '•••• ' . substr($mobile_number, -4);
                                } else {
                                    $to_mobile_number = '•••• ' . substr($mobile_number, -4);
                                }

                                if ($language_code == 'en') {
                                    $notification_txt_msg = 'You have sent ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to ' . $to_mobile_number . ' at ' . date("d-m-Y H:i", strtotime($this->datetime)) . '  Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                                } else {
                                    $notification_txt_msg = 'Umetuma ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' kwenda ' . $to_mobile_number . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                                }
                            }
                        } else if ($payment_type == 2) {
                            if ($language_code == 'en') {
                                $notification_txt_msg = 'You have sent ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to ' . $bank_name . ' at ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                            } else {
                                $notification_txt_msg = 'Umetuma ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' kwenda ' . $bank_name . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                            }
                        } else {

                            if ($language_code == 'en') {
                                $notification_txt_msg = 'You have sent ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' to ' . $wallet_name . ' at ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                            } else {
                                $notification_txt_msg = 'Umetuma ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . ' kwenda ' . $wallet_name . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                            }
                        }

                        /* $login_result = $this->sendPuchNotification($device_type,$device_token,$notification_txt_msg,$totalNotifications='0',$pushMessageText="","Qwiksend");
                        $this->selcomApiRequestResponse($this->user_id, "Notification - ARA to ARA", $notification_txt_msg, $login_result);

                        $notification_qry = new Notifications();
                        $notification_qry->user_id = $this->user_id;
                        $notification_qry->notification_type = 'transaction';
                        $notification_qry->notification_title = "Qwiksend";
                        $notification_qry->notification_text = $notification_txt_msg;
                        $notification_qry->data_object = "NA";
                        $notification_qry->type = "Inside";
                        $notification_qry->save(); */
                    }

                    /* Stash Transaction Notification */
                    if ($is_stash == 1) {

                        if ($language_code == 'en') {
                            $stash_notification_msg = 'You have added ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' to Ara Stash balance. Updated balance ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                        } else {
                            $stash_notification_msg = 'Umeweka ' . $currency_symbol . ' ' . number_format($amount_per, 2) . ' kwenye kibubu cha Ara. Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($stash_balance, 2);
                        }

                        $login_result = $this->sendPuchNotification($device_type, $device_token, $stash_notification_msg, $totalNotifications = '0', $pushMessageText = "", "Stash");
                        $this->selcomApiRequestResponse($this->user_id, "notiification - Qwiksend - Stash", $stash_notification_msg, $login_result);

                        $notification_qry = new Notifications();
                        $notification_qry->user_id = $this->user_id;
                        $notification_qry->notification_type = 'transaction';
                        $notification_qry->notification_title = "Qwiksend - Stash";
                        $notification_qry->notification_text = $stash_notification_msg;
                        $notification_qry->data_object = "NA";
                        $notification_qry->type = "Inside";
                        $notification_qry->save();
                    }
                    /* End */
                }

                if ($payment_type == 1) {
                    if ($toaccount_no != '0') {

                        $op_users = User::find($to_user_id);
                        $is_notification_op = $op_users->is_notification;

                        if ($is_notification_op == 'Yes') {
                            $device1 = Devices::where('user_id', '=', $to_user_id)->first();
                            if (!empty($device1)) {
                                $device_type1 = $device1['device_type'];
                                if ($device_type1 == 1) {
                                    $device_type1 = 'Android';
                                } else {
                                    $device_type1 = 'Iphone';
                                }
                                $device_token1 = $device1['device_token'];

                                $trans_datetime = date("d-m-Y H:i", strtotime($this->datetime));

                                if ($language_code == 'en') {
                                    $msg1 = $name . ' sent you TZS ' . (string)number_format($trans_amount, 2) . ' to your Ara Account ' . $toaccount_no . ' on ' . $trans_datetime;
                                } else {
                                    $msg1 = $name . ' amekutumia TZS ' . (string)number_format($trans_amount, 2) . ' kwenye akaunti yako ya Ara Account ' . $toaccount_no . ' saa ' . $trans_datetime;
                                }

                                $oposit_result = $this->sendPuchNotification($device_type1, $device_token1, $msg1, $totalNotifications = '0', $pushMessageText = "", "Qwiksend");
                                $this->selcomApiRequestResponse($to_user_id, "notiification - ARA to ARA oposite", $msg1, $oposit_result);

                                $notification_qry = new Notifications();
                                $notification_qry->user_id = $to_user_id;
                                $notification_qry->notification_type = 'transaction';
                                $notification_qry->notification_title = "Qwiksend";
                                $notification_qry->notification_text = $msg1;
                                $notification_qry->data_object = "NA";
                                $notification_qry->type = "Inside";
                                $notification_qry->save();
                            }
                        }
                    }
                }
                /* End */

                $json['user_id'] = $this->user_id;
                $json['trans_amount'] = number_format($trans_amount, 2);
                if ($is_stash == 1) {
                    $json['stash_trans_amount'] = number_format($amount_per, 2);
                } else {
                    $json['stash_trans_amount'] = "0";
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

    public function araToOtherCountry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'country_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $payment_type = $input['payment_type']; /* Bank, Card, Wallet */
        $amount = $input['amount'];
        $amount = number_format($amount, 2);
        $amount = str_replace(",", "", $amount);

        $country_id = $input['country_id'];
        if ($payment_type == 'Bank') {
            if (empty($input['bank_id']) || empty($input['account_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $bank_id = $input['bank_id'];
            $bank = RemittanceBanks::find($bank_id);
            if (empty($bank)) {
                return $this->sendError('0', trans('message.wrong_bank_account_number'), $validator->errors(), '200');
            }
            $bank_name = $bank['bank_name'];
            $utility_code = $bank['utility_code'];
            $account_number = $input['account_number'];
            $to_user_id = 0;
            $type = '4';
        } else if ($payment_type == 'Wallet') {
            if (empty($input['wallet_id']) || empty($input['mobile_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $account_number = $input['mobile_number'];
            $wallet_id = $input['wallet_id'];
            $wallet = RemittanceWallets::find($wallet_id);
            if (empty($wallet)) {
                return $this->sendError('0', trans('message.wrong_wallet'), $validator->errors(), '200');
            }
            $wallet_name = $wallet['wallet_name'];
            $utility_code = $wallet['utility_code'];
            $to_user_id = 0;
            $bank_id = 0;
            $type = '5';
        } else {
            $wallet_id = 0;
            $to_user_id = 0;
            $bank_id = 0;
            $card_number = $input['card_number'];
            $account_number = $card_number;
            $type = '6';
        }

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $user_account_number = $account[0]['account_number'];
        $account_balance_id = $account[0]['account_balance_id'];
        $currency_symbol = $account[0]['currency_symbol'];
        /* if($type == 6){
            $account_number = $account[0]['account_number'];
        } */
        /* $arr2 = str_split($account_number, 4);
        $account_no = '•••• '.$arr2[1]; */
        $account_no = '•••• ' . substr($user_account_number, -4);

        /**
         * Check user balance 
         */
        if ($amount >= $account_balance) {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        }

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        if ($payment_type == 'Wallet' || $payment_type == 'Bank') {

            if ($payment_type == 'Bank') {
                $paymentReference = $input['account_number'];
            } else {
                $paymentReference = $input['mobile_number'];
            }

            $external_id = rand(1000, 9999) . substr(time(), -7);
            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'ARA2REMIT';
            $trans_param['paymentReference'] = $paymentReference;
            $trans_param['utilityCode'] = $utility_code;
            $trans_param['categoryCode'] = 'General';
            $trans_param['category'] = 'NA';
            $trans_param['description'] = 'TZ';
            $trans_param['account'] = $user_account_number;
            $trans_param['service'] = '1';
            if (isset($input['payer_name']) && !empty($input['payer_name'])) {
                $trans_param['name'] = $input['payer_name'];
            }
            $trans_json_request = json_encode($trans_param);
            $url = 'client/' . $client_id . '/transaction';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);
            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if (!empty($selcom_response)) {
                if ($selcom_response['resultcode'] != '200') {
                    return $this->sendError('0', $selcom_response['message'], array(), '200');
                    exit;
                }
            } else {
                $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }

            $ara_balance = $this->araAvaBalance($this->user_id);
        } else if ($payment_type == 'Card') {
            $paymentReference = $input['card_number'];

            $external_id = rand(1000, 9999) . substr(time(), -7);
            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'ARA2CARD';
            $trans_param['paymentReference'] = $paymentReference;
            $trans_param['utilityCode'] = 'CARDCASHIN';
            $trans_param['categoryCode'] = 'General';
            $trans_param['category'] = 'NA';
            $trans_param['description'] = 'TZ';
            $trans_param['account'] = $user_account_number;
            $trans_param['service'] = '1';
            if (isset($input['payer_name']) && !empty($input['payer_name'])) {
                $trans_param['name'] = $input['payer_name'];
            }
            $trans_json_request = json_encode($trans_param);
            $url = 'client/' . $client_id . '/transaction';
            $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);
            $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

            if (!empty($selcom_response)) {
                if ($selcom_response['resultcode'] != '200') {
                    return $this->sendError('0', $selcom_response['message'], array(), '200');
                    exit;
                }
            } else {
                $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }

            $ara_balance = $this->araAvaBalance($this->user_id);
        }

        /* if($payment_type == 'Wallet' || $payment_type == 'Bank'){
            $msg = 'Your ARA account '.$account_no.' has been debited '.$currency_symbol.number_format($amount).' New balance: '.$currency_symbol.number_format($ara_balance);
        }else{
            $msg = 'Your ARA account '.$account_no.' has been debited '.$currency_symbol.number_format($amount).' New balance: '.$currency_symbol.number_format($account_balance-$amount);
        } */
        $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($amount) . '. Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance);

        if ($type == '4') {
            $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($amount) . ' to bank ' . $account_number . ' ' . date("d-m-Y H:i", strtotime($this->datetime));
        } else if ($type == '5') {
            $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($amount) . ' to ' . $wallet_name . ' Account ' . $account_number . ' ' . date("d-m-Y H:i", strtotime($this->datetime));
        } else if ($type == '6') {
            $cno = strlen($card_number) - 4;
            $c_number = '•••• ' . substr($card_number, $cno);
            $receipt = 'You have sent ' . $currency_symbol . ' ' . number_format($amount) . ' to card ' . $c_number . ' ' . date("d-m-Y H:i", strtotime($this->datetime));
        }

        /**
         * Add transaction details here
         */
        $trans_id = time() . rand(100000, 999999);
        $transactions = new Transactions();
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $trans_id;
        $transactions->trans_type = 4;
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
             * Add quick send details
             */
            $qwikSends = new Qwiksends();
            $qwikSends->user_id = $this->user_id;
            $qwikSends->trans_id = $transactions->id;
            if ($type == '4') {
                $qwikSends->bank_id = $bank_id;
            } else if ($type == '5') {
                $qwikSends->wallet_id = $wallet_id;
            } else if ($payment_type == '6') {
                $qwikSends->card_number = $card_number;
            }
            $qwikSends->country_id = $country_id;
            $qwikSends->type = $type;
            $qwikSends->category_id = 0;
            $qwikSends->trans_amount = $amount;
            $qwikSends->notes = 'NA';
            $qwikSends->created_at = $this->datetime;
            $qwikSends->updated_at = $this->datetime;
            $qwikSends->save();

            if ($qwikSends->id > 0) {

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
                    /* $updateBalance = AccountBalances::find($account_balance_id);
                    $updateBalance->account_balance = $account_balance-$amount;
                    $updateBalance->updated_at = $this->datetime;
                    $updateBalance->save(); */
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
    }

    public function araToOtherCountryValidation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'payment_type' => 'required',
            'country_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $payment_type = $input['payment_type']; /* Bank, Card, Wallet */
        $json['payment_type'] = $payment_type;
        $amount = $input['amount'];
        $amount = number_format($amount, 2);
        $amount = str_replace(",", "", $amount);
        $json['amount'] = $amount;

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $country_id = $input['country_id'];
        $country = RemittanceCountries::find($country_id);
        $json['country_name'] = $country['country_name'];
        $json['country_id'] = $country_id;
        if ($payment_type == 'Bank') {
            if (empty($input['bank_id']) || empty($input['account_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $bank_id = $input['bank_id'];
            $json['bank_id'] = $bank_id;
            $bank = RemittanceBanks::find($bank_id);
            if (empty($bank)) {
                return $this->sendError('0', trans('message.wrong_bank_account_number'), $validator->errors(), '200');
            }
            $json['bank_name'] = $bank['bank_name'];
            $json['account_number'] = $input['account_number'];
            $utility_code = $bank['utility_code'];
            $to_user_id = 0;
            $type = '4';
        } else if ($payment_type == 'Wallet') {
            if (empty($input['wallet_id']) || empty($input['mobile_number'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $json['mobile_number'] = $input['mobile_number'];
            $wallet_id = $input['wallet_id'];
            $json['wallet_id'] = $wallet_id;
            $wallet = RemittanceWallets::find($wallet_id);
            if (empty($wallet)) {
                return $this->sendError('0', trans('message.wrong_wallet'), $validator->errors(), '200');
            }
            $json['wallet_name'] = $wallet['wallet_name'];
            $utility_code = $wallet['utility_code'];
            $to_user_id = 0;
            $bank_id = 0;
            $type = '5';
        } else {
            $wallet_id = 0;
            $to_user_id = 0;
            $bank_id = 0;
            $json['card_number'] = $input['card_number'];
            $type = '6';
        }

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $user_account_number = $account[0]['account_number'];
        $account_balance_id = $account[0]['account_balance_id'];
        $currency_symbol = $account[0]['currency_symbol'];

        /**
         * Check user balance 
         */
        if ($amount >= $account_balance) {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        }

        $account_holder_name = "";
        if ($payment_type == 'Wallet' || $payment_type == 'Bank') {

            $external_id = rand(1000, 9999) . time() . rand(1000, 9999);

            if ($payment_type == 'Wallet') {
                $paymentReference = $input['mobile_number'];
            } else {
                $paymentReference = $input['account_number'];
            }

            $url = 'client/' . $client_id . '/transaction-lookup?account=' . $user_account_number . '&serviceType=ARA2REMIT&utilityCode=' . $utility_code . '&paymentReference=' . $paymentReference . '&amount=' . $amount . '&externalId=' . $external_id;
            $selcom_response = $this->selcomApi($url, '', '', 'GET');

            $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

            if (!empty($selcom_response)) {
                $resultcode = $selcom_response['resultcode'];
                $result = $selcom_response['result'];
                if ($resultcode != '200' && $result != 'SUCCESS') {
                    $error_msg = $selcom_response['message'];
                    return $this->sendError('0', $error_msg, array(), '200');
                }
                $resultdata = $selcom_response['data'];
                $account_holder_name = $resultdata[0]['name'];
                if (empty($account_holder_name)) {
                    $account_holder_name = "";
                }
            } else {
                $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }
        }

        $json['user_id'] = $this->user_id;
        $json['account_holder_name'] = $account_holder_name;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.ara_to_other_country_details'));
    }

    /**
     * Check mobile number in middleware
     * 
     * @return json array
     */
    public function checkMobileNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'mobile_number' => 'required',
            'category_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $category_id = $input['category_id'];
        $mobile_number = $input['mobile_number'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);
        $notes = '';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
        }

        $check_str_length = strlen($mobile_number);
        if ($check_str_length > 12) {

            $mobile_number = $mobile_number;
            $params['mobile_number'] = $mobile_number;
            $params['currency_id'] = 1;
            $users = $this->userAccountRepository->checkMobileOrAccountNumber($params);
            if ($users->isEmpty()) {
                return $this->sendError('0', trans('message.account_number'), array(), '200');
            }
            $is_non_ara_account_number = '0';
        } else {
            if ($check_str_length == '10') {
                $mobile_number = ltrim($mobile_number, '0');
                //$mobile_number = '255'.$mobile_number;
            } else if ($check_str_length == '12') {
                //$mobile_number = $mobile_number;
                $mobile_number = substr($mobile_number, 3);
            }
            /* $check_country_code = substr($mobile_number,0,3);
            if($check_country_code == '255'){
                $mobile_number = substr($mobile_number, 3);
            } */

            /* $users1 = User::where('mobile_number',$mobile_number)->first();
            $is_non_ara_account_number = '0';
            if (empty($users1)) {
                $is_non_ara_account_number = '1';
            } */

            $params['mobile_number'] = $mobile_number;
            $params['currency_id'] = 1;
            $users = $this->userAccountRepository->checkMobileOrAccountNumber($params);
            $is_non_ara_account_number = '0';
            if ($users->isEmpty()) {
                $is_non_ara_account_number = '1';
            }
        }

        /* $params['mobile_number'] = $mobile_number;
        $params['currency_id'] = 1;
        $users = $this->userAccountRepository->checkMobileOrAccountNumber($params); */
        /* if ($users->isEmpty()) {
            return $this->sendError('0', trans('message.wrong_mobile'), array(), '200');
        } */

        $mobile_number = $input['mobile_number'];
        $check_str_length = strlen($mobile_number);
        if ($check_str_length > '12') {
            $mobile_number = $mobile_number;
        } else {
            if ($check_str_length == '10') {
                $mobile_number = ltrim($mobile_number, '0');
                $mobile_number = '255' . $mobile_number;
            } else if ($check_str_length == '12') {
                $mobile_number = $mobile_number;
            } else if ($check_str_length == '9') {
                $mobile_number = '255' . $mobile_number;
            } else {
                $mobile_number = $mobile_number;
            }
        }

        $json['mobile_number'] = $mobile_number;
        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_balance = $login_user[0]['account_balance'];

        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $categories = Categories::find($category_id);
        if (empty($categories)) {
            return $this->sendError('0', trans('message.wrong_category'), array(), '200');
        }

        if ($is_non_ara_account_number == '1') {
            $params1['user_id'] = $this->user_id;
            $params1['currency_id'] = 1;
            $login_user = $this->userAccountRepository->getUserBalance($params1);
            $account_number = $login_user[0]['account_number'];

            $users1 = User::find($this->user_id);
            $client_id = $users1->client_id;

            $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
            $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=ARA2ARA&utilityCode=ARA2ARA&paymentReference=' . $mobile_number . '&amount=' . $amount . '&externalId=' . $external_id;
            $selcom_response = $this->selcomApi($url, '', '', 'GET');

            $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

            $resultcode = $selcom_response['resultcode'];
            $result = $selcom_response['result'];
            if ($resultcode != '200' && $result != 'SUCCESS') {
                $error_msg = $selcom_response['message'];
                return $this->sendError('0', $error_msg, array(), '200');
            }
        }

        $response = array();
        $json['user_id'] = $this->user_id;
        if ($is_non_ara_account_number == '1') {
            $json['account_holder_name'] = "";
        } else {
            if ($users->isEmpty()) {
                $json['account_holder_name'] = "";
            } else {
                $json['account_holder_name'] = $users[0]['name'] == NULL ? "" : $users[0]['name'];
            }
        }
        $json['category_name'] = $categories['name'];
        $json['notes'] = $notes;
        $json['amount'] = number_format($amount, 2);
        $json['category_id'] = $category_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.mobile_details'));
    }

    /**
     * Check mobile number in middleware
     * 
     * @return json array
     */
    public function checkWalletNumber(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'mobile_number' => 'required',
            'category_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $category_id = $input['category_id'];
        $wallet_id = "0"; //$input['wallet_id'];
        $mobile_number = $input['mobile_number'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);

        $notes = '';
        if (isset($input['notes']) && !empty($input['notes'])) {
            $notes = $input['notes'];
        }

        /**
         * Check user balance 
         */
        /* if($amount >= 20000 )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $categories = Categories::find($category_id);
        if (empty($categories)) {
            return $this->sendError('0', trans('message.wrong_category'), array(), '200');
        }

        /* $wallets = Wallets::find($wallet_id);
        if (empty($wallets)) {
            return $this->sendError('0', trans('message.wrong_wallet'), array(), '200');
        } */
        $utility_code = "CASHIN";  //$wallets['utility_code'];

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_number = $login_user[0]['account_number'];
        $account_balance = $login_user[0]['account_balance'];

        /**
         * Check user balance 
         */
        /* if( $amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

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
        if (isset($resultdata[0]['operator'])) {
            $json['operator'] = $resultdata[0]['operator'];
        } else {
            $json['operator'] = "";
        }
        $json['category_name'] = $categories['name'];
        $json['wallet_name'] = "CASHIN"; //$wallets['wallet_name'];
        $json['mobile_number'] = $mobile_number;
        $json['wallet_id'] = $wallet_id;
        $json['notes'] = $notes;
        $json['amount'] = number_format($amount, 2);
        $json['category_id'] = $category_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.mobile_details'));
    }

    /**
     * Check bank account number
     * 
     * @return json array
     */
    public function checkBankAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'bank_id' => 'required',
            'account_number' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $bank_id = $input['bank_id'];
        $account_number = $input['account_number'];
        //$amount = $input['amount'];

        $bank = Banks::find($bank_id);
        if (empty($bank)) {
            return $this->sendError('0', trans('message.wrong_bank_account_number'), $validator->errors(), '200');
        }
        $utility_code = $bank['utility_code'];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $user_account_number = $account[0]['account_number'];

        $amount = '100';
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=QWIKSEND&utilityCode=' . $utility_code . '&paymentReference=' . $account_number . '&amount=' . $amount . '&externalId=' . $external_id;
        $selcom_response = $this->selcomApi($url, '', '', 'GET');

        $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

        if (!empty($selcom_response)) {
            $resultcode = $selcom_response['resultcode'];
            $result = $selcom_response['result'];
            if ($resultcode != '200' && $result != 'SUCCESS') {
                $error_msg = $selcom_response['message'];
                return $this->sendError('0', $error_msg, array(), '200');
            }
            $resultdata = $selcom_response['data'];
            $account_holder_name = $resultdata[0]['name'];
            if (empty($account_holder_name)) {
                $account_holder_name = "NA";
            }
        } else {
            $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }

        $response = array();
        $json['bank_id'] = (string)$bank['id'];
        $json['bank_name'] = $bank['bank_name'];
        $json['account_number'] = $account_number;
        $json['account_holder_name'] = $account_holder_name;
        $json['user_id'] = $this->user_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.bank_details'));
    }

    /**
     * Check bank account number
     * 
     * @return json array
     */
    public function checkBankBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'bank_id' => 'required',
            'account_number' => 'required',
            'amount' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $bank_id = $input['bank_id'];
        $account_number = $input['account_number'];
        $amount = $input['amount'];
        $amount = str_replace(",", "", $amount);

        if (isset($input['account_holder_name'])) {
            $account_holder_name = $input['account_holder_name'];
        } else {
            $account_holder_name = "";
        }

        if (isset($input['lookup_enabled'])) {
            $lookup_enabled = $input['lookup_enabled'];
        } else {
            $lookup_enabled = "";
        }

        $bank = Banks::find($bank_id);
        if (empty($bank)) {
            return $this->sendError('0', trans('message.wrong_bank_account_number'), $validator->errors(), '200');
        }

        $params1['user_id'] = $this->user_id;
        $params1['currency_id'] = 1;
        $login_user = $this->userAccountRepository->getUserBalance($params1);
        $account_balance = $login_user[0]['account_balance'];

        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $response = array();
        $json['bank_id'] = (string)$bank['id'];
        $json['bank_name'] = $bank['bank_name'];
        $json['account_number'] = $account_number;
        $json['amount'] = number_format($amount, 2);
        //$json['account_holder_name'] = "NA"
        $json['account_holder_name'] = $account_holder_name;
        $json['lookup_enabled'] = $lookup_enabled;;
        $json['user_id'] = $this->user_id;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.bank_details'));
    }
}