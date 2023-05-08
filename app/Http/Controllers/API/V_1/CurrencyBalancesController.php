<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\UserAccountRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\UserCredits;
use App\Models\UserDebits;
use App\Models\Currencies;
use App\Models\CurrencyTransfers;
use App\Models\LinkCards;
use App\Models\ForexRates;
use App\Models\Stashes;
use App\Models\User;
use App\Models\Settings;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class CurrencyBalancesController extends BaseController
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

        if (isset($_POST['language_code']) && !empty($_POST['language_code'])) {
            $language_code = $_POST['language_code'];
        } else {
            $language_code = 'en';
        }
        App::setLocale($language_code);
    }

    /**
     * Add card details method
     * 
     * @return json array
     */
    public function currencyBalances(Request $request)
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
        $language_code = $input['language_code'];

        $params['user_id'] = $this->user_id;
        $accounts = $this->userAccountRepository->checkMobileOrAccountNumber($params);
        if ($accounts->isEmpty()) {
            return $this->sendError('-11', trans('message.wrong_user'), array(), '200');
        }

        $currency = config('custom.upload.currency');
        $json = array();
        foreach ($accounts as $val) {
            $d['account_id'] = (string)$val['account_id'];
            $d['account_number'] = $val['account_number'];
            $d['mobile_number'] = $val['mobile_number'];
            $d['account_balance'] = number_format($val['account_balance'], 2);
            $d['account_balance_id'] = (string)$val['account_balance_id'];
            $d['currency_symbol'] = $val['currency_symbol'];
            $d['currency_code'] = $val['currency_code'];
            $d['user_id'] = $this->user_id;
            if ($val['currency_code'] == 'TZS') {
                if ($language_code == 'en') {
                    $d['label'] = 'Ara Available Balance';
                } else {
                    $d['label'] = 'Salio la akaunti ya Ara';
                }
            } else {
                $d['label'] = $val['currency_code'];
            }
            if (empty($val['icon'])) {
                $d['icon'] = '';
            } else {
                $d['icon'] = env('APP_URL') . '/storage/' . $currency . "/" . $val['icon'];
            }
            $json[] = $d;
        }

        $qry = DB::table('link_cards');
        $qry->whereRaw("(user_id = '" . $this->user_id . "') AND ( ( type = 'Physical' AND status != '0') OR (type = 'Virtual' AND status != '0')) ");
        $linkcards = $qry->get();

        //$linkcards = LinkCards::where('user_id','=', $this->user_id)->where('status','=','1')->get();
        $arr = array();
        foreach ($linkcards as $val) {
            $r['card_id'] = (string)$val->id;
            $r['serial_number'] = $val->card_serial_number;
            $r['card_number'] = $val->card_number;
            $r['card_name'] = $val->card_name;
            $r['type'] = $val->type;
            $r['expiry'] = "";
            $r['cvv'] = "";
            $r['status'] = (string)$val->status;
            $r['is_atm_access'] = $val->is_atm_access;
            $r['currency_enable'] = (string)$val->currency_enable;
            $r['local_account_fallback'] = (string)$val->local_account_fallback;
            $arr[] = $r;
        }

        $response['balances'] = $json;
        $response['cards'] = $arr;

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_number = $account[0]['account_number'];

        $users = User::find($this->user_id);
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;

        /* $api_url = 'vcn/show';
        $param['msisdn'] = $country_code.$mobile_number;
        $param['account'] = $account_number;
        $param['requestid'] = rand(1000,9999).substr(time(), -7);
        $selcom_response = $this->selcomDevApi($api_url, $param, 'true');
        if($selcom_response['resultcode'] != '000')
        {
            return $this->sendError('0', $selcom_response['message'], array(), '200');
            exit;
        }
        $selcom_data = $selcom_response['data'];
        $vcn_url = $selcom_data[0]['vcn_url']; */
        $response['vcn_url'] = "";

        return $this->sendResponse('1', $response, trans('message.list_of_currency'));
    }

    /**
     * Currency convert
     * 
     * @return json array  
     */
    public function convertCurrencyRate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'from_account_balance_id' => 'required',
            'to_account_balance_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $from_account_balance_id = $input['from_account_balance_id'];
        $to_account_balance_id = $input['to_account_balance_id'];

        $response = array();
        $fromaccountBalancesDetails = AccountBalances::find($from_account_balance_id);
        $from_currency_id = $fromaccountBalancesDetails['currency_id'];

        $fromcurrencies = Currencies::find($from_currency_id);
        $json['from_currency_name'] = $fromcurrencies['currency_name'];
        $json['from_currency_symbol'] = $fromcurrencies['currency_symbol'];
        $json['from_currency_code'] = $fromcurrencies['currency_code'];

        $toaccountBalancesDetails = AccountBalances::find($to_account_balance_id);
        $to_currency_id = $toaccountBalancesDetails['currency_id'];

        $tocurrencies = Currencies::find($to_currency_id);
        $json['to_currency_name'] = $tocurrencies['currency_name'];
        $json['to_currency_symbol'] = $tocurrencies['currency_symbol'];
        $json['to_currency_code'] = $tocurrencies['currency_code'];

        $forexRatesQry = ForexRates::where('base_currency', '=', $tocurrencies['currency_code'])->where('other_currency', '=', $fromcurrencies['currency_code'])->first();
        if (!empty($forexRatesQry)) {
            $rate = $forexRatesQry['forex_rate'];
        } else {
            $rate = "1.00";
        }

        $json['user_id'] = $this->user_id;
        $json['rate'] = (string)$rate;

        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.list_of_currency'));
    }


    /**
     * Currency transfer
     * 
     * @return json array
     */
    public function checkCurrencyTransferBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'from_account_balance_id' => 'required',
            'to_account_balance_id' => 'required',
            'from_trans_amount' => 'required',
            'to_trans_amount' => 'required',
            'currency_conversation_rate' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }
        $input = $request->all();

        $trans_id = time() . rand(100000, 999999);
        $from_account_balance_id = $input['from_account_balance_id'];
        $to_account_balance_id = $input['to_account_balance_id'];

        $from_trans_amount = $input['from_trans_amount'];
        $from_trans_amount = str_replace(",", "", $from_trans_amount);

        $to_trans_amount = $input['to_trans_amount'];
        $to_trans_amount = str_replace(",", "", $to_trans_amount);

        $currency_conversation_rate = $input['currency_conversation_rate'];

        $fromaccountBalancesDetails = AccountBalances::find($from_account_balance_id);
        $from_currency_id = $fromaccountBalancesDetails['currency_id'];

        $toaccountBalancesDetails = AccountBalances::find($to_account_balance_id);
        $to_currency_id = $toaccountBalancesDetails['currency_id'];
        $to_account_balance = $toaccountBalancesDetails['account_balance'];

        $fromcurrencies = Currencies::find($from_currency_id);
        $from_currency_name = $fromcurrencies['currency_name'];
        $from_currency_symbol = $fromcurrencies['currency_symbol'];
        $from_currency_code = $fromcurrencies['currency_code'];
        if ($from_currency_code == 'TZS') {
            $from_label = 'Ara Available Balance';
        } else {
            $from_label = $from_currency_code;
        }

        $tocurrencies = Currencies::find($to_currency_id);
        $to_currency_name = $tocurrencies['currency_name'];
        $to_currency_symbol = $tocurrencies['currency_symbol'];
        $to_currency_code = $tocurrencies['currency_code'];
        if ($to_currency_code == 'TZS') {
            $to_label = 'Ara Available Balance';
        } else {
            $to_label = $to_currency_code;
        }


        /**
         * Get from ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = $from_currency_id;
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $currency_symbol = $account[0]['currency_symbol'];
        $account_number = $account[0]['account_number'];
        $arr2 = str_split($account_number, 4);
        //$account_no = '•••• '.$arr2[1];
        $account_no = '•••• ' . substr($account_number, -4);

        /**
         * Check user balance 
         */
        /* if($from_trans_amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $response = array();
        $json['user_id'] = $this->user_id;
        $json['from_account_balance_id'] = $from_account_balance_id;
        $json['to_account_balance_id'] = $to_account_balance_id;
        $json['from_trans_amount'] = $from_trans_amount;
        $json['to_trans_amount'] = $to_trans_amount;
        $json['currency_conversation_rate'] = $currency_conversation_rate;
        $json['from_label'] = $from_label;
        $json['to_label'] = $to_label;
        $json['from_currency_name'] = $from_currency_name;
        $json['from_currency_code'] = $from_currency_code;
        $json['from_currency_symbol'] = $from_currency_symbol;
        $json['to_currency_name'] = $to_currency_name;
        $json['to_currency_code'] = $to_currency_code;
        $json['to_currency_symbol'] = $to_currency_symbol;
        $response[] = $json;
        return $this->sendResponse('1', $response, trans('message.list_of_currency'));
    }

    /**
     * Currency transfer
     * 
     * @return json array
     */
    public function currencyTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'from_account_balance_id' => 'required',
            'to_account_balance_id' => 'required',
            'from_trans_amount' => 'required',
            'to_trans_amount' => 'required',
            'currency_conversation_rate' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }
        $input = $request->all();

        $trans_id = time() . rand(100000, 999999);
        $from_account_balance_id = $input['from_account_balance_id'];
        $to_account_balance_id = $input['to_account_balance_id'];

        $from_trans_amount = $input['from_trans_amount'];
        $from_trans_amount = str_replace(",", "", $from_trans_amount);

        $to_trans_amount = $input['to_trans_amount'];
        $to_trans_amount = str_replace(",", "", $to_trans_amount);

        $currency_conversation_rate = $input['currency_conversation_rate'];

        $fromaccountBalancesDetails = AccountBalances::find($from_account_balance_id);
        $from_currency_id = $fromaccountBalancesDetails['currency_id'];

        $toaccountBalancesDetails = AccountBalances::find($to_account_balance_id);
        $to_currency_id = $toaccountBalancesDetails['currency_id'];
        $to_account_balance = $toaccountBalancesDetails['account_balance'];

        $fromcurrencies = Currencies::find($from_currency_id);
        $from_currency_name = $fromcurrencies['currency_name'];
        $from_currency_symbol = $fromcurrencies['currency_symbol'];
        $from_currency_code = $fromcurrencies['currency_code'];

        $tocurrencies = Currencies::find($to_currency_id);
        $to_currency_name = $tocurrencies['currency_name'];
        $to_currency_symbol = $tocurrencies['currency_symbol'];
        $to_currency_code = $tocurrencies['currency_code'];

        /**
         * Get from ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = $from_currency_id;
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $account_balance = $account[0]['account_balance'];
        $currency_symbol = $account[0]['currency_symbol'];
        $account_number = $account[0]['account_number'];
        $arr2 = str_split($account_number, 4);
        //$account_no = '•••• '.$arr2[1];
        $account_no = '•••• ' . substr($account_number, -4);

        /**
         * Check user balance 
         */
        /* if($from_trans_amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        $users = User::find($this->user_id);
        $client_id = $users->client_id;
        $country_code = $users->country_code;
        $mobile_number = $users->mobile_number;
        $ara_receipt = '';

        $user_ipaddress = $this->getIpAddress();

        if (isset($input['lat']) && !empty($input['lng'])) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } else {
            $lat = "0.00";
            $lng = "0.00";
        }

        $utilityCode = $from_currency_code . '2' . $to_currency_code;
        $external_id = rand(1000, 9999) . substr(time(), -7);

        $trans_insert = new Transactions();
        $trans_insert->user_id = $this->user_id;
        $trans_insert->trans_id = $external_id;
        $trans_insert->trans_type = 2;
        $trans_insert->trans_status = 0;
        $trans_insert->prev_balance = $account_balance;
        $trans_insert->receipt = '';
        $trans_insert->account_number = $account_number;
        $trans_insert->trans_datetime = $this->datetime;
        $trans_insert->user_ipaddress = $user_ipaddress;
        $trans_insert->latitude = $lat;
        $trans_insert->longitude = $lng;
        $trans_insert->created_at = $this->datetime;
        $trans_insert->updated_at = $this->datetime;
        $trans_insert->save();
        $t_id = $trans_insert->id;

        $trans_param['externalId'] = $external_id;
        $trans_param['amount'] = $from_trans_amount;
        $trans_param['currency'] = $from_currency_code;
        $trans_param['serviceType'] = 'FOREXTRANSFER';
        $trans_param['paymentReference'] = $to_currency_code;
        $trans_param['utilityCode'] = $utilityCode;
        $trans_param['categoryCode'] = 'General';
        $trans_param['category'] = 'NA';
        $trans_param['description'] = 'TZ';
        $trans_param['account'] = $account_number;
        $trans_param['msisdn'] = $country_code . $mobile_number;
        $trans_param['forexRate'] = $currency_conversation_rate;
        $trans_json_request = json_encode($trans_param);
        $url = 'client/' . $client_id . '/forex-transfer';
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

        /* if(isset($selcom_response['data'])){
            $json_data = $selcom_response['data'];
            $ara_receipt = $json_data[0]['araReceipt'];
        }else{
            $ara_receipt = '';
        } */

        $ara_balance = $this->araAvaBalance($this->user_id);
        $this->currencyAvaBalance($this->user_id);

        if (fmod($from_trans_amount, 1) !== 0.00) {
            $t_amount = $from_trans_amount;
            $t_amount = number_format($t_amount, 2);
            // your code if its decimals has a value
        } else {
            $t_amount = number_format($from_trans_amount);
            // your code if the decimals are .00, or is an integer
        }

        $updated_balance = number_format($account_balance - $from_trans_amount, 2);
        $updated_balance = str_ireplace(",", "", $updated_balance);
        if (fmod($updated_balance, 1) !== 0.00) {
            $updated_balance = number_format($updated_balance, 2);
            $updated_balance = str_ireplace(",", "", $updated_balance);
        } else {
            $updated_balance = number_format($updated_balance);
            $updated_balance = str_ireplace(",", "", $updated_balance);
        }

        $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . $t_amount . '. Updated balance ' . $currency_symbol . ' ' . number_format($updated_balance, 2);

        $receipt = 'You have successfully transfered ' . $from_currency_code . ' ' . $t_amount . ' from your Ara ' . $from_currency_name . ' balance to Ara ' . $to_currency_name . ' sub-account';

        $transactions = Transactions::find($t_id);
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->trans_type = 2;
        $transactions->trans_status = 1;
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        $transactions->account_number = $account_number;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {

            /**
             * Store CurrencyTransfers table details
             */
            $currencyTransfers = new CurrencyTransfers();
            $currencyTransfers->user_id = $this->user_id;
            $currencyTransfers->trans_id = $t_id;
            $currencyTransfers->from_account_balance_id = $from_account_balance_id;
            $currencyTransfers->to_account_balance_id = $to_account_balance_id;
            $currencyTransfers->from_trans_amount = $from_trans_amount;
            $currencyTransfers->to_trans_amount = $to_trans_amount;
            $currencyTransfers->currency_conversation_rate = $currency_conversation_rate;
            $currencyTransfers->created_at = $this->datetime;
            $currencyTransfers->updated_at = $this->datetime;
            $currencyTransfers->save();

            if ($currencyTransfers->id > 0) {
                /**
                 * User debits
                 */
                $debit = new UserDebits();
                $debit->user_id = $this->user_id;
                $debit->trans_id = $t_id;
                $debit->prev_balance = $account_balance;
                $debit->trans_amount = $from_trans_amount;
                $debit->created_at = $this->datetime;
                $debit->updated_at = $this->datetime;
                $debit->save();
                if ($debit->id > 0) {
                    /**
                     * Ara user balance update
                     */
                    /* $updateBalance = AccountBalances::find($from_account_balance_id);
                    $updateBalance->account_balance = $account_balance-$from_trans_amount;
                    $updateBalance->updated_at = $this->datetime;
                    $updateBalance->save(); */
                }

                /**
                 * User credit
                 */
                $credit = new UserCredits();
                $credit->user_id = $to_trans_amount;
                $credit->trans_id = $t_id;
                $credit->prev_balance = $to_account_balance;
                $credit->trans_amount = $to_trans_amount;
                $credit->created_at = $this->datetime;
                $credit->updated_at = $this->datetime;
                $credit->save();
                if ($credit->id > 0) {
                    /**
                     * Ara user balance update for opposite user
                     */
                    /* $toUpdateBalance = AccountBalances::find($to_account_balance_id);
                    $toUpdateBalance->account_balance = $to_account_balance+$to_trans_amount;
                    $toUpdateBalance->updated_at = $this->datetime;
                    $toUpdateBalance->save(); */
                }

                $json['user_id'] = $this->user_id;
                $json['receipt'] = $receipt;
                $response[] = $json;
                return $this->sendResponse('1', $response, $msg);
            } else {
                return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
            }
        }
    }

    /**
     * Ara balance
     * 
     * @return json array
     */
    public function araBalance(Request $request)
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

        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $accounts = $this->userAccountRepository->getUserBalance($user_param);
        $response = array();
        /* foreach($accounts as $key=>$val){
            $response[$key] = $val;
            $response[$key]['user_id'] = $this->user_id;
            $stash = Stashes::where('user_id','=',$this->user_id)->first();
            if(empty($stash)){
                $response[0]['recurring_percentage'] = "0";
                $response[0]['stash_balance'] = "0";
            }else{
                $response[0]['recurring_percentage'] = (string)$stash['per_trans_percentage'];
                $response[0]['stash_balance'] = (string)number_format($stash['stash_balance'],2);
            }
        }

        $setting = Settings::find('1');
        $response[0]['ara_to_other_country'] = (string)$setting['ara_to_other_country']; */

        foreach ($accounts as $val) {
            $d['user_id'] = $this->user_id;
            $d['account_id'] = $val['account_id'];
            $d['account_number'] = $val['account_number'];
            $d['quickrewards_balance'] = number_format($val['quickrewards_balance'], 2);
            $d['account_balance'] = number_format($val['account_balance'], 2);
            $d['account_balance_id'] = $val['account_balance_id'];
            $d['currency_code'] = $val['currency_code'];
            $d['currency_symbol'] = $val['currency_symbol'];
            $d['icon'] = $val['icon'];
            $d['recurring_percentage'] = $val['recurring_percentage'];
            $stash = Stashes::where('user_id', '=', $this->user_id)->first();
            if (empty($stash)) {
                $d['recurring_percentage'] = "0";
                $d['stash_balance'] = "0";
            } else {
                $d['recurring_percentage'] = (string)$stash['per_trans_percentage'];
                $d['stash_balance'] = (string)number_format($stash['stash_balance'], 2);
            }
            $setting = Settings::find('1');
            $d['ara_to_other_country'] = (string)$setting['ara_to_other_country'];
            $response[] = $d;
        }

        return $this->sendResponse('1', $response, trans('message.list_of_currency'));
    }
}