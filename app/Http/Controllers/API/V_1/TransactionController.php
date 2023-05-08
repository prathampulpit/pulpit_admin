<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\TransactionsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\QwikcashesRepository;
use App\Models\Transactions;
use App\Models\Categories;
use App\Models\BillpaymentProducts;
use App\Models\DisputeTransactions;
use App\Models\AccountBalances;
use App\Models\Currencies;
use App\Models\User;
use App\Models\Banks;
use App\Models\RemittanceBanks;
use App\Models\Wallets;
use App\Models\RemittanceWallets;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class TransactionController extends BaseController
{
    protected $transactionsRepository;
    protected $userAccountRepository;
    protected $qwikcashesRepository;
    protected $userRepository;

    public function __construct(
        TransactionsRepository $transactionsRepository,
        UserAccountRepository $userAccountRepository,
        QwikcashesRepository $qwikcashesRepository,
        UserRepository $userRepository
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->qwikcashesRepository = $qwikcashesRepository;
        $this->userRepository = $userRepository;
        $this->datetime = date("Y-m-d H:i:s");
        $this->user_id = $_POST['user_id'];
    }

    /**
     * List of all transaction histroy
     * 
     * @return json array
     */
    public function allTransactions(Request $request)
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

        $input = $request->all();
        //$selected_date = date("Y-m-d", strtotime($input['selected_date']));

        if (isset($input['selected_month_year'])) {
            $selected_date = date("Y-m", strtotime($input['selected_month_year']));
            $date_type = '2';
        } else {
            $selected_date = date("Y-m-d", strtotime($input['selected_date']));
            $date_type = '1';
        }

        $response = array();

        $params['user_id'] = $this->user_id;
        $params['trans_datetime'] = $selected_date;
        $params['date_type'] = $date_type;
        $params['order_by'] = 'transactions.id';
        $params['order'] = 'DESC';
        $params['trans_status'] = '1';
        $allTransactionArr = $this->transactionsRepository->getAllTranasctions($params);
        $response = array();
        foreach ($allTransactionArr as $val) {
            $credit_type = '1';
            $trans_amount_type = $val['trans_amount_type'];
            $currency = $val['currency'];
            $d['user_id'] = $this->user_id;
            $d['trans_id'] = $val['trans_id'];
            $d['ara_receipt'] = $val['ara_receipt'];
            $d['trans_status'] = $val['trans_status'];

            $d['pay_with_qwikrewards'] = "0";
            $d['qwikrewards_amount'] = "- TZS 0.00";

            $d['tip_amount'] = "- TZS 0.00";
            $d['tip_reference'] = "";

            $qwiksend_trans_type = $val['qwiksend_trans_type'];
            $account_number = $val['account_number'];

            $category_id = (!empty($val['category_id'])) ? (string)$val['category_id'] : "";
            $d['category_id'] = $category_id;
            if (!empty($category_id)) {
                $cat = Categories::find($category_id);
                $d['category_name'] = $cat['name'];
            } else {
                $d['category_name'] = "";
            }
            $trans_type = $val['trans_type'];
            $d['type'] = (string)$trans_type;

            if ($trans_type == 1) {
                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Add Money - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Add Money';
                }

                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['pull_funds_trans_amount'], 2);
            } else if ($trans_type == 3) {
                $bill_payment_product_id = $val['bill_payment_product_id'];

                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Bill Payment - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Bill Payment';
                }

                //$d['trans_type'] = 'Bill Payment';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['bill_trans_amount'], 2);
                $d['notes'] = "";

                if ($val['bill_pay_with_qwikrewards'] == '1') {
                    $d['pay_with_qwikrewards'] = (string)$val['bill_pay_with_qwikrewards'];
                    $d['qwikrewards_amount'] = '- TZS ' . (string)number_format($val['bill_qwikrewards_amount'], 2);
                } else {
                    $d['pay_with_qwikrewards'] = "0";
                    $d['qwikrewards_amount'] = "- TZS 0.00";
                }
            } else if ($trans_type == 5) {
                $qwikcash_type = $val['qwikcash_type'];
                $party_name = $val['party_name'];
                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['qwikcash_trans_amount'], 2);

                if ($qwikcash_type == '2') {
                    if (!empty($party_name)) {
                        $d['trans_type'] = 'Agent Cash Out - ' . $party_name;
                    } else {
                        $d['trans_type'] = 'Agent Cash Out';
                    }
                } else if ($qwikcash_type == '3') {
                    if (!empty($party_name)) {
                        $d['trans_type'] = 'Agent Cash In - ' . $party_name;
                    } else {
                        $d['trans_type'] = 'Agent Cash In';
                    }
                } else {
                    //$d['trans_type'] = 'ATM';

                    if (!empty($party_name)) {
                        $d['trans_type'] = 'ATM - ' . $party_name;
                    } else {
                        $d['trans_type'] = 'ATM';
                    }
                    $d['trans_amount'] = '- TZS ' . (string)number_format($val['qwikcash_trans_amount'], 2);
                }
            } else if ($trans_type == 4) {
                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Send money - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Send money';
                }
                $credit_user_id = $val['credit_user_id'];
                if ($credit_user_id != $this->user_id) {
                    $credit_type = '1';
                    $d['trans_amount'] = '- TZS ' . (string)number_format($val['qwiksend_trans_amount'], 2);
                    $d['notes'] = $val['qwiksend_notes'];
                } else {
                    $credit_type = '0';
                    $d['trans_amount'] = '+ TZS ' . (string)number_format($val['qwiksend_trans_amount'], 2);
                }

                if ($qwiksend_trans_type == 1) {

                    if ($credit_user_id != $this->user_id) {
                        $uid = $credit_user_id;
                    } else {
                        $uid = $val['user_id'];
                    }

                    $user_details = User::find($uid);
                    if (!empty($user_details)) {
                        $recipient_name = $user_details->name;
                        $recipient_number = $user_details->mobile_number;
                        $country_code = $user_details->country_code;

                        if (empty($party_name)) {
                            $d['trans_type'] = 'Send money - Ara (' . $country_code . $recipient_number . ' - ' . $recipient_name . ')';
                        } else {
                            $d['trans_type'] = 'Send money - ' . $party_name;
                        }
                    }
                }
            } else if ($trans_type == 6) {
                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Mastercard Qr - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Mastercard Qr';
                }
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['mastercard_trans_amount'], 2);

                if ($val['master_pay_with_qwikrewards'] == '1') {
                    $d['pay_with_qwikrewards'] = (string)$val['master_pay_with_qwikrewards'];
                    $d['qwikrewards_amount'] = '- TZS ' . (string)number_format($val['mastercard_qwikrewards_amount'], 2);
                } else {
                    $d['pay_with_qwikrewards'] = "0";
                    $d['qwikrewards_amount'] = "- TZS 0.00";
                }

                $d['tip_amount'] = '- TZS ' . (string)number_format($val['tip_amount'], 2);
                $d['tip_reference'] = $val['tip_reference'];

                $d['notes'] = $val['mastercard_notes'];
            } else if ($trans_type == 7) {
                $stash_trans_type = $val['stash_trans_type'];
                if ($stash_trans_type == '1') {
                    $d['trans_type'] = 'Ara to Stash';
                } else {
                    $d['trans_type'] = 'Stash to Ara';
                }
                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['stash_amount'], 2);
            } else if ($trans_type == 8) {
                $d['trans_type'] = 'Qwikreward';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['qwikreward_trans_amount'], 2);
            } else if ($trans_type == 2) {
                $d['trans_type'] = 'Currency Transfers';

                $from_account_balance_id = $val['from_account_balance_id'];
                $accountBalances = AccountBalances::find($from_account_balance_id);
                $currency_id = $accountBalances->currency_id;

                $currencies = Currencies::find($currency_id);
                $currency_code = $currencies->currency_code;

                $currency_transfers_amount = $val['currency_transfers_amount'];
                $currency_transfers_amount = str_ireplace(",", "", $currency_transfers_amount);

                if (fmod($currency_transfers_amount, 1) !== 0.00) {
                    $t_amount = $currency_transfers_amount;
                    $t_amount = number_format($t_amount, 2);
                    // your code if its decimals has a value
                } else {
                    $t_amount = number_format($currency_transfers_amount, 2);
                    // your code if the decimals are .00, or is an integer
                }
                $d['trans_amount'] = '- ' . $currency_code . ' ' . $t_amount;
            } else if ($trans_type == 9) {
                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Card - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Card';
                }

                if ($trans_amount_type == '1') {
                    $sign = '+';
                } else {
                    $sign = '-';
                }

                $d['trans_amount'] = $sign . ' ' . $currency . ' ' . (string)number_format($val['card_trans_amount'], 2);
            } else if ($trans_type == 10) {
                $party_name = $val['party_name'];
                if (!empty($party_name)) {
                    $d['trans_type'] = 'Add Money - ' . $party_name;
                } else {
                    $d['trans_type'] = 'Add Money';
                }

                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['mwallet_trans_amount'], 2);
            }

            if ($credit_type == '1') {
                $notification_text = $val['notification_text'];
                if (!empty($notification_text)) {
                    $d['receipt'] = $notification_text;
                } else {
                    $d['receipt'] = $val['receipt'];
                }

                if ($qwiksend_trans_type == 1) {
                    if ($val['credit_user_id'] == $this->user_id) {
                        $opposite_party_name = $val['opposite_party_name'];
                        $opposite_notification_text = $val['opposite_notification_text'];
                        if (!empty($opposite_notification_text)) {
                            $d['receipt'] = $opposite_notification_text;
                        }

                        if (!empty($opposite_party_name)) {
                            $d['trans_type'] = 'Send money - ' . $opposite_party_name;
                        }
                    }
                }
            } else {

                $trans_datetime = date("d-m-Y H:i", strtotime($val['trans_datetime']));

                $credit_user_id = $val['credit_user_id'];
                $debit_user_id = $val['debit_user_id'];
                $user_details = User::find($debit_user_id);
                $name = $user_details->name;

                $user_param['user_id'] = $this->user_id;
                $user_param['currency_id'] = '1';
                $account = $this->userAccountRepository->getUserBalance($user_param);
                $currency_symbol = $account[0]['currency_symbol'];
                $user_account_number = $account[0]['account_number'];
                //$arr2 = str_split($user_account_number, 4);
                //$account_no = '•••• '.$arr2[1];
                $account_no = '•••• ' . substr($user_account_number, -4);

                $msg = $name . ' sent TZS ' . (string)number_format($val["qwiksend_trans_amount"], 2) . ' to your Ara Account ' . $account_no . ' on ' . $trans_datetime;
                $d['receipt'] = $msg;
            }
            $d['trans_datetime'] = date("F d, Y", strtotime($val['trans_datetime']));
            $response[] = $d;
        }

        /* $response[] = $json; */
        return $this->sendResponse('1', $response, trans('message.all_transaction'));
    }

    public function allTransactions1(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'selected_date' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $selected_date = date("Y-m-d", strtotime($input['selected_date']));

        $response = array();

        $params['user_id'] = $this->user_id;
        $params['trans_datetime'] = $selected_date;
        $params['order_by'] = 'transactions.id';
        $params['order'] = 'DESC';
        $allTransactionArr = $this->transactionsRepository->getAllTranasctions($params);
        $response = array();
        foreach ($allTransactionArr as $val) {
            $credit_type = '1';
            $d['user_id'] = $this->user_id;
            $d['trans_id'] = $val['trans_id'];
            $d['ara_receipt'] = $val['ara_receipt'];

            $d['pay_with_qwikrewards'] = "0";
            $d['qwikrewards_amount'] = "- TZS 0.00";

            $d['tip_amount'] = "- TZS 0.00";
            $d['tip_reference'] = "";
            $qwiksend_trans_type = $val['qwiksend_trans_type'];
            $account_number = $val['account_number'];

            $category_id = (!empty($val['category_id'])) ? (string)$val['category_id'] : "";
            $d['category_id'] = $category_id;
            if (!empty($category_id)) {
                $cat = Categories::find($category_id);
                $d['category_name'] = $cat['name'];
            } else {
                $d['category_name'] = "";
            }
            $trans_type = $val['trans_type'];
            if ($trans_type == 1) {
                $d['trans_type'] = 'Add Money';
                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['pull_funds_trans_amount']);
            } else if ($trans_type == 3) {
                $bill_payment_product_id = $val['bill_payment_product_id'];
                $d['trans_type'] = 'Bill Payment';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['bill_trans_amount']);
                $d['notes'] = "";

                if ($val['bill_pay_with_qwikrewards'] == '1') {
                    $d['pay_with_qwikrewards'] = $val['bill_pay_with_qwikrewards'];
                    $d['qwikrewards_amount'] = '- TZS ' . (string)number_format($val['bill_qwikrewards_amount']);
                } else {
                    $d['pay_with_qwikrewards'] = "0";
                    $d['qwikrewards_amount'] = "- TZS 0.00";
                }
            } else if ($trans_type == 5) {
                $qwikcash_type = $val['qwikcash_type'];

                if ($qwikcash_type == '2') {
                    $d['trans_type'] = 'Agent Cash Out';
                } else if ($qwikcash_type == '3') {
                    $d['trans_type'] = 'Agent Cash In';
                } else {
                    $d['trans_type'] = 'ATM';
                }
                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['qwikcash_trans_amount']);
            } else if ($trans_type == 4) {
                $d['trans_type'] = 'Send money';
                $credit_user_id = $val['credit_user_id'];
                if ($credit_user_id != $this->user_id) {
                    $credit_type = '1';
                    $d['trans_amount'] = '- TZS ' . (string)number_format($val['qwiksend_trans_amount']);
                    $d['notes'] = $val['qwiksend_notes'];
                } else {
                    $credit_type = '0';
                    $d['trans_amount'] = '+ TZS ' . (string)number_format($val['qwiksend_trans_amount']);
                }

                if ($qwiksend_trans_type == 1) {
                    if ($credit_user_id != $this->user_id) {
                        $user_details = User::find($credit_user_id);
                        if (!empty($user_details)) {
                            $recipient_name = $user_details->name;
                            $recipient_number = $user_details->mobile_number;
                            $country_code = $user_details->country_code;
                            $d['trans_type'] = 'Send money - Ara (' . $country_code . $recipient_number . ' - ' . $recipient_name . ')';
                        }
                    } else {
                        $user_details = User::find($this->user_id);
                        if (!empty($user_details)) {
                            $recipient_name = $user_details->name;
                            $recipient_number = $user_details->mobile_number;
                            $country_code = $user_details->country_code;
                            $d['trans_type'] = 'Send money - Ara (' . $country_code . $recipient_number . ' - ' . $recipient_name . ')';
                        }
                    }
                }
            } else if ($trans_type == 6) {
                $d['trans_type'] = 'Mastercard Qr';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['mastercard_trans_amount']);

                if ($val['master_pay_with_qwikrewards'] == '1') {
                    $d['pay_with_qwikrewards'] = $val['master_pay_with_qwikrewards'];
                    $d['qwikrewards_amount'] = '- TZS ' . (string)number_format($val['mastercard_qwikrewards_amount']);
                } else {
                    $d['pay_with_qwikrewards'] = "0";
                    $d['qwikrewards_amount'] = "- TZS 0.00";
                }

                $d['tip_amount'] = '- TZS ' . (string)number_format($val['tip_amount']);
                $d['tip_reference'] = $val['tip_reference'];

                $d['notes'] = $val['mastercard_notes'];
            } else if ($trans_type == 7) {
                $stash_trans_type = $val['stash_trans_type'];
                if ($stash_trans_type == '1') {
                    $d['trans_type'] = 'Ara to Stash';
                } else {
                    $d['trans_type'] = 'Stash to Ara';
                }
                $d['trans_amount'] = '+ TZS ' . (string)number_format($val['stash_amount']);
            } else if ($trans_type == 8) {
                $d['trans_type'] = 'Qwikreward';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['qwikreward_trans_amount']);
            } else if ($trans_type == 2) {
                $d['trans_type'] = 'Currency Transfers';

                $from_account_balance_id = $val['from_account_balance_id'];
                $accountBalances = AccountBalances::find($from_account_balance_id);
                $currency_id = $accountBalances->currency_id;

                $currencies = Currencies::find($currency_id);
                $currency_code = $currencies->currency_code;

                $currency_transfers_amount = $val['currency_transfers_amount'];
                $currency_transfers_amount = str_ireplace(",", "", $currency_transfers_amount);

                if (fmod($currency_transfers_amount, 1) !== 0.00) {
                    $t_amount = $currency_transfers_amount;
                    $t_amount = number_format($t_amount, 2);
                    // your code if its decimals has a value
                } else {
                    $t_amount = number_format($currency_transfers_amount);
                    // your code if the decimals are .00, or is an integer
                }
                $d['trans_amount'] = '- ' . $currency_code . ' ' . $t_amount;
            } else if ($trans_type == 9) {
                $d['trans_type'] = 'Card';
                $d['trans_amount'] = '- TZS ' . (string)number_format($val['card_trans_amount']);
            }

            if ($credit_type == '1') {
                $d['receipt'] = $val['receipt'];
            } else {

                $trans_datetime = date("d-m-Y H:i", strtotime($val['trans_datetime']));

                $credit_user_id = $val['credit_user_id'];
                $debit_user_id = $val['debit_user_id'];
                $user_details = User::find($debit_user_id);
                $name = $user_details->name;

                $user_param['user_id'] = $this->user_id;
                $user_param['currency_id'] = '1';
                $account = $this->userAccountRepository->getUserBalance($user_param);
                $currency_symbol = $account[0]['currency_symbol'];
                $user_account_number = $account[0]['account_number'];
                //$arr2 = str_split($user_account_number, 4);
                //$account_no = '•••• '.$arr2[1];
                $account_no = '•••• ' . substr($user_account_number, -4);

                $msg = $name . ' sent TZS ' . (string)number_format($val["qwiksend_trans_amount"]) . ' to your Ara Account ' . $account_no . ' on ' . $trans_datetime;
                $d['receipt'] = $msg;
            }
            $d['trans_datetime'] = date("F d, Y", strtotime($val['trans_datetime']));
            $response[] = $d;
        }

        /* $response[] = $json; */
        return $this->sendResponse('1', $response, trans('message.all_transaction'));
    }

    /**
     * List of all transaction histroy
     * 
     * @return json array
     */
    public function sendEmailStatements(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'selected_date' => 'required',
            'email' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $email = $input['email'];
        $selected_date = $input['selected_date'];
        $str = str_ireplace(',', '","', $selected_date);

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $trans_id = time() . rand(100000, 999999);
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $trans_param['email'] = $email;
        $trans_param['months'] = '["' . $str . '"]';

        $trans_json_request = json_encode($trans_param);
        $url = 'client/' . $client_id . '/email-statement';
        $selcom_response = $this->selcomApi($url, $trans_json_request, $this->user_id);

        $this->selcomApiRequestResponse($this->user_id, $url, $trans_json_request, json_encode($selcom_response));

        if ($selcom_response['resultcode'] != '200') {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
            exit;
        }
        $error_message = $selcom_response['message'];

        $d['user_id'] = $this->user_id;
        $response[] = $d;
        return $this->sendResponse('1', $response, $error_message);
    }

    /**
     * List of all transaction histroy
     * 
     * @return json array
     */
    public function digestTransactions(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'selected_date' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $selected_date = date("Y-m-d", strtotime($input['selected_date']));

        $response = array();

        $params['user_id'] = $this->user_id;
        $params['trans_datetime'] = $selected_date;
        $params['order_by'] = 'transactions.id';
        $params['order'] = 'DESC';
        $allTransactionArr = $this->transactionsRepository->getDigestTranasctions($params);
        /* print_r($allTransactionArr); */
        $response = array();
        foreach ($allTransactionArr as $val) {
            $d['user_id'] = $this->user_id;
            $d['total_trans_amount'] = 'TZS ' . number_format($val['total_trans_amount']);
            $d['trans_datetime'] = strtoupper(date("F d, Y", strtotime($val['trans_datetime'])));
            $response[] = $d;
        }

        /* $response[] = $json; */
        return $this->sendResponse('1', $response, trans('message.all_transaction'));
    }

    /**
     * Dispute Transaction
     * 
     * @return true & false with message
     */
    public function disputeTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'transaction_reference' => 'required',
            'description' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $transaction_reference = $input['transaction_reference'];
        $description = $input['description'];

        $qry = new DisputeTransactions();
        $qry->user_id = $this->user_id;
        $qry->trans_id = $transaction_reference;
        $qry->description = $description;
        $qry->status = '2';
        $qry->created_at = $this->datetime;
        $qry->updated_at = $this->datetime;
        $qry->save();

        return $this->sendResponse('1', array(), trans('message.dispute_transaction'));
    }

    /**
     * Update category in transaction history
     * 
     * @return true & json response
     */
    public function updateCategoryInTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'category_id' => 'required',
            'trans_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $trans_id = $input['trans_id'];
        $category_id = $input['category_id'];
        $trans_details = Transactions::where("trans_id", "=", $trans_id)->first();
        if (!empty($trans_details)) {
            $t_id = $trans_details['id'];
            $trans_type = $trans_details['trans_type'];

            if ($trans_type == 4) {
                $data_update['category_id'] = $category_id;
                DB::table('qwiksends')->where('trans_id', $t_id)->update($data_update);
            } else if ($trans_type == 6) {
                $data_update['category_id'] = $category_id;
                DB::table('mastercard_qrs')->where('trans_id', $t_id)->update($data_update);
            }
            $trans_update = Transactions::find($t_id);
            $trans_update->category_id = $category_id;
            $trans_update->save();
            return $this->sendResponse('1', array(), trans('message.record_update'));
        } else {
            return $this->sendError('0', trans('message.trans_failed'), array(), '200');
        }
    }
}