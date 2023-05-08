<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\CardsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\QwikcashesRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\Qwikcashes;
use App\Models\UserDebits;
use App\Models\Categories;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;

class QwikcashController extends BaseController
{
    protected $cardsRepository;
    protected $userAccountRepository;
    protected $qwikcashesRepository;
    protected $userRepository;

    public function __construct(
        cardsRepository $cardsRepository,
        userAccountRepository $userAccountRepository,
        qwikcashesRepository $qwikcashesRepository,
        UserRepository $userRepository
    ) {
        $this->cardsRepository = $cardsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->qwikcashesRepository = $qwikcashesRepository;
        $this->userRepository = $userRepository;
        $this->datetime = date("Y-m-d H:i:s");

        $this->user_id = "";
        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        }
    }

    /**
     * Add card details method
     * payment type: 1, 2 ( 1 = ATM, 2 = Agent )
     * @return json array
     */
    public function qwikCash(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'payment_type' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $payment_type = $input['payment_type'];
        $amount = '0.00';

        $user = User::find($this->user_id);
        $client_id = $user->client_id;

        if ($payment_type == 1) {
            $url = 'client/' . $client_id . '/transaction-token';
            $selcom_response = $this->selcomApi($url, '', '', 'GET');
            $resultcode = $selcom_response['resultcode'];
            $result = $selcom_response['result'];
            if ($resultcode != '200' && $result != 'SUCCESS') {
                return $this->sendError('0', $selcom_response['message'], array(), '200');
            }
            $token_arr = $selcom_response['data'];
            $atm_token = $token_arr[0]['token'];
            $validity = $token_arr[0]['validity'];
        } else {
            if (empty($input['agent_code']) || empty($input['amount'])) {
                return $this->sendError('-11', trans('message.parameters_missing'), array(), '200');
            }
            $agent_code = $input['agent_code'];
            $amount = $input['amount'];
            $amount = number_format($amount, 2);
            $amount = str_replace(",", "", $amount);
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
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        if (isset($input['lat']) && !empty($input['lng'])) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } else {
            $lat = "0.00";
            $lng = "0.00";
        }

        $party_name = '';
        $ara_receipt = '';
        $trans_id = time() . rand(100000, 999999);
        $external_id = rand(1000, 9999) . substr(time(), -7);
        $user_ipaddress = $this->getIpAddress();

        $trans_insert = new Transactions();
        $trans_insert->user_id = $this->user_id;
        $trans_insert->trans_id = $external_id;
        $trans_insert->trans_type = 5;
        $trans_insert->trans_status = 0;
        $trans_insert->prev_balance = $account_balance;
        $trans_insert->receipt = '';
        if (isset($agent_code)) {
            $trans_insert->account_number = $agent_code;
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

        if ($payment_type == 1) {
            $json['atm_token'] = (string)$atm_token;
            $msg = trans('message.atm_token_success');
            $receipt = "NA";
        } else {

            $users = User::find($this->user_id);
            $client_id = $users->client_id;

            $trans_param['externalId'] = $external_id;
            $trans_param['amount'] = $amount;
            $trans_param['currency'] = 'TZS';
            $trans_param['serviceType'] = 'AGENTWITHDRAW';
            $trans_param['paymentReference'] = $agent_code;
            $trans_param['utilityCode'] = 'ARACASHOUT';
            $trans_param['categoryCode'] = 'General';
            $trans_param['category'] = 'NA';
            $trans_param['description'] = 'TZ';
            $trans_param['account'] = $account_number;
            $trans_param['service'] = '1';
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

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                if (isset($json_data[0]['araReceipt'])) {
                    $ara_receipt = $json_data[0]['araReceipt'];
                } else {
                    $ara_receipt = '';
                }
            } else {
                $ara_receipt = '';
            }

            if (isset($json_data[0]['note'])) {
                $party_name = $json_data[0]['note'];
            } else {
                $party_name = '';
            }

            $ara_balance = $this->araAvaBalance($this->user_id);
            $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($amount, 2) . '. Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);

            $receipt = $selcom_response['message'];
            //$receipt = str_ireplace("\n"," ", $receipt);
            //$receipt = 'You have withdrawn '.$currency_symbol.number_format($amount,2).' from agent name here '.date("d-m-Y H:i", strtotime($this->datetime));
        }


        /**
         * Add transaction details here
         */
        //$trans_id = time().rand(100000,999999);
        $transactions = Transactions::find($t_id);
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->ara_receipt = $ara_receipt;
        $transactions->trans_type = 5;
        $transactions->trans_status = 1;
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        $transactions->party_name = $party_name;
        $transactions->user_ipaddress = $user_ipaddress;
        $transactions->latitude = $lat;
        $transactions->longitude = $lng;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {
            /**
             * Add quick send details
             */
            $qwikcash = new Qwikcashes();
            $qwikcash->user_id = $this->user_id;
            $qwikcash->trans_id = $t_id;
            if ($payment_type == 1) {
                $qwikcash->atm_token = $atm_token;
            } else {
                $qwikcash->agent_code = $agent_code;
            }
            $qwikcash->qwikcash_type = $payment_type;
            $qwikcash->trans_amount = $amount;
            $qwikcash->created_at = $this->datetime;
            $qwikcash->updated_at = $this->datetime;
            $qwikcash->save();
            if ($qwikcash->id > 0) {
                if ($payment_type == 2) {
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

    /**
     * Find agent details using selcom API
     * 
     * @return json array
     */
    public function getAgentDetails(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'amount' => 'required',
            'agent_code' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $agent_code = $input['agent_code'];
        $amount = $input['amount'];
        $amount = number_format($amount, 2);
        $amount = str_replace(",", "", $amount);

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $account_number = $account[0]['account_number'];

        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $utility_code = "ARACASHOUT";
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=AGENTWITHDRAW&utilityCode=' . $utility_code . '&paymentReference=' . $agent_code . '&amount=' . $amount . '&externalId=' . $external_id;
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
        } else {
            $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }

        $response = array();
        $json['user_id'] = $this->user_id;
        $json['account_holder_name'] = $resultdata[0]['name'];
        $json['agent_code'] = $agent_code;
        $json['amount'] = number_format($amount, 2);
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.agent_details'));
    }
}