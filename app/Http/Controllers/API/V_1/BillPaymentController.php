<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\BillpaymentProductsRepository;
use App\Repositories\TopupsRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\BillpaymentProducts;
use App\Models\UserDebits;
use App\Models\Categories;
use App\Models\Topups;
use App\Models\Stashes;
use App\Models\User;
use App\Models\Currencies;
use App\Models\UserAccounts;
use App\Models\StashTransactionHistory;
use App\Models\BillpaymentTransactions;
use App\Models\Devices;
use App\Models\Notifications;
use App\Models\QwikrewardHistories;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class BillPaymentController extends BaseController
{
    protected $userAccountRepository;
    protected $userRepository;
    protected $billpaymentProductsRepository;
    protected $topupsRepository;

    public function __construct(
        userAccountRepository $userAccountRepository,
        UserRepository $userRepository,
        BillpaymentProductsRepository $billpaymentProductsRepository,
        TopupsRepository $topupsRepository
    ) {
        $this->userAccountRepository = $userAccountRepository;
        $this->userRepository = $userRepository;
        $this->billpaymentProductsRepository = $billpaymentProductsRepository;
        $this->topupsRepository = $topupsRepository;
        $this->datetime = date("Y-m-d H:i:s");

        $this->user_id = "";
        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        }
    }

    /**
     * List of bill payment products
     * 
     * @return json array 
     */
    public function billPaymentProducts(Request $request)
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

        $categories = Categories::where('status', '=', '1')->get();
        if ($categories->isEmpty()) {
            return $this->sendError('0', trans('message.blank_category'), $validator->errors(), '200');
        }
        $biller_path = config('custom.upload.billerImages');

        $response = array();
        foreach ($categories as $val) {
            $d['category_id'] = (string)$val['id'];
            $d['category_name'] = $val['name'];
            $d['category_code'] = $val['code'];

            $param['category_id'] = $val['id'];
            $products = $this->billpaymentProductsRepository->getByParams($param);
            $arr = array();
            if (!$products->isEmpty()) {
                foreach ($products as $rec) {
                    $d1['billpayment_product_id'] = $rec['billpayment_product_id'];
                    $d1['icon'] = env('APP_URL') . '/storage/app/public/' . $biller_path . "/" . $rec['icon'];
                    $d1['category_id'] = $rec['category_id'];
                    $d1['product_name'] = $rec['product_name'];
                    $d1['utilitycode'] = $rec['utilitycode'];
                    $d1['ref_label'] = $rec['ref_label'];
                    $d1['minimum_amount'] = (string)$rec['minimum_amount'];
                    $d1['maximum_amount'] = (string)$rec['maximum_amount'];
                    $d1['type'] = ($rec['type'] == NULL) ? "" : $rec['type'];
                    $d1['enable_lookup'] = $rec['enable_lookup'];
                    $d1['amount_on_lookup'] = $rec['amount_on_lookup'];
                    $d1['enable_package'] = $rec['enable_package'];
                    $d1['package_levels'] = $rec['package_levels'];
                    $d1['denominations'] = unserialize($rec['denominations']);
                    $d1['display_label'] = unserialize($rec['display_label']);
                    $d1['reference_label'] = unserialize($rec['reference_label']);
                    $d1['reference_length'] = unserialize($rec['reference_length']);
                    $d1['packages'] = unserialize($rec['packages']);
                    $arr[] = $d1;
                }
            }

            $d['product_list'] = $arr;
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_products'));
    }

    /**
     * List of bill payment products
     * 
     * @return json array 
     */
    public function categoryList(Request $request)
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

        $categories = Categories::where('status', '=', '1')->get();
        if ($categories->isEmpty()) {
            return $this->sendError('0', trans('message.blank_category'), $validator->errors(), '200');
        }
        $biller_path = config('custom.upload.category');

        $response = array();
        foreach ($categories as $val) {
            $d['category_id'] = (string)$val['id'];
            $d['name'] = $val['name'];
            $d['code'] = $val['code'];
            $d['category_image'] = 'storage/app/public/' . $biller_path . "/" . $val['icon'];
            $d['status'] = $val['status'];
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_products'));
    }

    /**
     * List of bill payment products
     * 
     * @return json array 
     */
    public function frequentlyPaidProducts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $categories = Categories::where('status', '=', '1')->get();
        if ($categories->isEmpty()) {
            return $this->sendError('0', trans('message.blank_category'), $validator->errors(), '200');
        }
        $profile_path = config('custom.upload.billerImages');

        $par['group_by'] = 'billpayment_transactions.bill_payment_product_id';
        $par['limit'] = '5';
        $frequently = $this->billpaymentProductsRepository->getFrequentlyData($par);

        foreach ($frequently as $r) {
            $d1['billpayment_product_id'] = $r->billpayment_product_id;
            $d1['icon'] = env('APP_URL') . '/storage/app/public/' . $profile_path . "/" . $r->icon;
            $d1['category_id'] = $r->category_id;
            $d1['product_name'] = $r->product_name;
            $d1['utilitycode'] = $r->utilitycode;
            $d1['ref_label'] = $r->ref_label;
            $d1['minimum_amount'] = (string)$r->minimum_amount;
            $d1['maximum_amount'] = (string)$r->maximum_amount;
            $d1['type'] = ($r->type == NULL) ? "" : $r->type;
            $d1['enable_lookup'] = $r->enable_lookup;
            $d1['amount_on_lookup'] = $r->amount_on_lookup;
            $d1['enable_package'] = $r->enable_package;
            $d1['package_levels'] = $r->package_levels;
            $d1['denominations'] = unserialize($r->denominations);
            $d1['display_label'] = unserialize($r->display_label);
            $d1['reference_label'] = unserialize($r->reference_label);
            $d1['reference_length'] = unserialize($r->reference_length);
            $d1['packages'] = unserialize($r->packages);
            $response[] = $d1;
        }

        return $this->sendResponse('1', $response, trans('message.list_of_products'));
    }

    /**
     * List of available topups
     * 
     * @return json array
     */
    public function topupLists(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'bill_payment_product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $bill_payment_product_id = $input['bill_payment_product_id'];

        /**
         * Get Value Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Value';
        $value_topups = $this->topupsRepository->getByParams($param);

        $response = array();
        /* $arr = array(); */

        /* $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $bundle_topups = $this->topupsRepository->getByParams($param);
        print_r($bundle_topups);
        exit; */

        /**
         * Get Daily Internet Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Internet';
        $param['package'] = 'Daily';
        $bundle_topup_internet = $this->topupsRepository->getByParams($param);
        $d['internet'] = $bundle_topup_internet;

        /**
         * Get Daily Combo Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Combo';
        $param['package'] = 'Daily';
        $bundle_topup_combo = $this->topupsRepository->getByParams($param);
        $d['combo'] = $bundle_topup_combo;
        $sub_arr['daily'] = $d;

        /**
         * Get Weekly Combo Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Combo';
        $param['package'] = 'Weekly';
        $bundle_topup_weekly_combo = $this->topupsRepository->getByParams($param);
        $d1['combo'] = $bundle_topup_weekly_combo;

        /**
         * Get Weekly Combo Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Internet';
        $param['package'] = 'Weekly';
        $bundle_topup_weekly_internet = $this->topupsRepository->getByParams($param);
        $d1['internet'] = $bundle_topup_weekly_internet;
        $sub_arr['weekly'] = $d1;

        /**
         * Get Monthly Combo Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Combo';
        $param['package'] = 'Monthly';
        $bundle_topup_monthly_combo = $this->topupsRepository->getByParams($param);
        $d2['combo'] = $bundle_topup_monthly_combo;

        /**
         * Get Monthly Internet Bundle Topup
         */
        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Internet';
        $param['package'] = 'Monthly';
        $bundle_topup_monthly_internet = $this->topupsRepository->getByParams($param);
        $d2['internet'] = $bundle_topup_monthly_internet;
        $sub_arr['monthly'] = $d2;

        /**
         * Get Yearly Combo Bundle Topup
         */
        /* $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Combo';
        $param['package'] = 'Yearly';  
        $bundle_topup_yearly_combo = $this->topupsRepository->getByParams($param);
        $d3['combo'] = $bundle_topup_yearly_combo;

        $param['bill_payment_product_id'] = $bill_payment_product_id;
        $param['status'] = '1';
        $param['type'] = 'Bundle';
        $param['sub_type'] = 'Internet';
        $param['package'] = 'Yearly';  
        $bundle_topup_yearly_internet = $this->topupsRepository->getByParams($param);
        $d3['internet'] = $bundle_topup_yearly_internet;
        $sub_arr['yearly'] = $d3; */

        /* $arr['value_topups'] = $value_topups; 
        $arr['bundle_topups'] = $sub_arr;
        $response[] = $arr; */

        $response['value_topups'] = $value_topups;
        $response['bundle_topups'] = $sub_arr;
        return $this->sendResponse('1', $response, trans('message.list_of_topups'));
    }

    /**
     * Check bill payment details
     * 
     * @return json array
     */
    public function checkBillPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'account_no' => 'required',
            'bill_payment_product_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $bill_account_no = $input['account_no'];
        $language_code = $input['language_code'];

        if (isset($input['package_id']) && !empty($input['package_id'])) {
            $package_id = $input['package_id'];
        } else {
            $package_id = "";
        }

        if (isset($input['topup_label']) && !empty($input['topup_label'])) {
            $topup_label = $input['topup_label'];
        } else {
            $topup_label = "";
        }

        $bill_payment_product_id = $input['bill_payment_product_id'];
        $params['bill_payment_product_id'] = $bill_payment_product_id;
        $products = $this->billpaymentProductsRepository->getCategoryProdDetails($params);

        $enable_lookup = $products[0]['enable_lookup'];
        $amount_on_lookup = $products[0]['amount_on_lookup'];
        $utilitycode = $products[0]['utilitycode'];
        $category_code = $products[0]['code'];
        $no_of_transaction = $products[0]['no_of_transaction'];

        $start_date = date("Y-m-d") . " 00:00:00";
        $end_date = date("Y-m-d") . " 23:59:59";

        $qry = DB::table('billpayment_transactions');
        $qry->select('billpayment_transactions.id');
        $qry->where('billpayment_transactions.bill_payment_product_id', $bill_payment_product_id);
        $qry->where('billpayment_transactions.user_id', $this->user_id);
        $qry->whereBetween('billpayment_transactions.created_at', [$start_date, $end_date]);
        $per_day_no_of_transaction = $qry->count();
        if ($no_of_transaction < $per_day_no_of_transaction) {
            return $this->sendError('-11', trans('message.max_no_of_transaction_exist'), array(), '200');
        }

        $reference_label_arr = unserialize($products[0]['reference_label']);
        /* $reference_label_arr = json_decode($reference_label_json,true); */
        $lable = $reference_label_arr[$language_code];

        if ($enable_lookup == '1' && $amount_on_lookup == '1') {
            $amount = '';
        } else {
            $amount = $input['amount'];
            $amount = str_replace(",", "", $amount);
            //$amount = '';
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
        $account_no = '•••• ' . $arr2[1];

        $users = User::find($this->user_id);
        $client_id = $users->client_id;

        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $payment_reference = rand(1000, 9999) . time() . rand(1000, 9999);

        if ($enable_lookup == '1') {
            $url = 'client/' . $client_id . '/transaction-lookup?account=' . $account_number . '&serviceType=UTILITYPAYMENT&utilityCode=' . $utilitycode . '&paymentReference=' . $bill_account_no . '&amount=' . $amount . '&externalId=' . $external_id;

            $selcom_response = $this->selcomApi($url, '', '', 'GET');
            /* print_r($selcom_response); */

            $this->selcomApiRequestResponse($this->user_id, $url, "", json_encode($selcom_response));

            $resultcode = $selcom_response['resultcode'];
            $result = $selcom_response['result'];
            if ($resultcode != '200' && $result != 'SUCCESS') {
                $error_msg = $selcom_response['message'];
                return $this->sendError('0', $error_msg, array(), '200');
            }
            $resultdata = $selcom_response['data'];
            if (isset($resultdata[0]['amount'])) {
                $amount = $resultdata[0]['amount'];
            }

            if (isset($resultdata[0]['institution'])) {
                $d['key'] = 'Institution';
                $d['value'] = $resultdata[0]['institution'];
                $response[] = $d;
            }
        }

        /**
         * Check user balance 
         */
        /* if($amount >= $account_balance )
        {
            return $this->sendError('0', trans('message.insufficient_balance'), array(), '200');
        } */

        if ($utilitycode != 'INTLTOP') {
            if ($enable_lookup == '1') {
                $name = "";
                if (isset($resultdata[0]['name'])) {
                    $name = $resultdata[0]['name'];
                }
            } else {
                $name = "";
            }
        } else {
            $name = "";
        }

        $d['key'] = $lable;
        $d['value'] = $bill_account_no;
        if ($utilitycode == 'TOP') {
            $d['is_mobile'] = '1';
        } else {
            $d['is_mobile'] = '0';
        }
        $response[] = $d;

        /* $d['key'] = 'Biller';
        $d['value'] = $products[0]['product_name'];
        $d['is_mobile'] = '0';
        $response[] = $d; */

        if ($utilitycode != 'INTLTOP') {
            if (!empty($amount)) {
                $d['key'] = "Amount";
                $d['value'] = 'TZS ' . number_format($amount, 2);
                $d['is_mobile'] = '0';
                $response[] = $d;
            }
        } else {
            if (!empty($amount)) {
                $d['key'] = "Amount";
                $d['value'] = 'TZS ' . number_format($amount, 2);
                $d['is_mobile'] = '0';
                $response[] = $d;
            }
        }

        if ($name != '') {
            $d['key'] = "Name";
            $d['value'] = $name;
            $d['is_mobile'] = '0';
            $response[] = $d;
        }

        if (!empty($topup_label)) {
            $d['key'] = "Topup";
            $d['value'] = $topup_label;
            $d['is_mobile'] = '0';
            $response[] = $d;
        }

        if ($utilitycode == 'GEPG') {
            if (isset($selcom_response['data'][0]['desc'])) {
                $desc = $selcom_response['data'][0]['desc'];
                $d['key'] = "Desc";
                $d['value'] = $desc;
                $d['isMobile'] = '0';
                $response[] = $d;
            }
        }

        $json['reference_label'] = $response;
        if (!empty($amount)) {
            $json['amount'] = number_format($amount, 2);
        }
        if ($utilitycode == 'INTLTOP') {
            if ($enable_lookup == '1') {
                $json['operator'] = $selcom_response['data'][0]['operator'];
                $json['country'] = $selcom_response['data'][0]['country'];
                if (isset($selcom_response['data'][0]['type'])) {
                    $json['amount_type'] = $selcom_response['data'][0]['type'];
                } else {
                    if (isset($selcom_response['data'][0]['amountType'])) {
                        $json['amount_type'] = $selcom_response['data'][0]['amountType'];
                    } else {
                        $json['amount_type'] = "";
                    }
                }
                $json['packages'] = $selcom_response['data'][0]['packages'];
            }
        } elseif ($utilitycode == 'GEPG') {
            //$json['amount_type'] = "EDITABLE";
            if (isset($selcom_response['data'][0]['type'])) {
                $json['amount_type'] = $selcom_response['data'][0]['type'];
            } else {
                //$json['amount_type'] = "EDITABLE";
                if (isset($selcom_response['data'][0]['amountType'])) {
                    $json['amount_type'] = $selcom_response['data'][0]['amountType'];
                } else {
                    $json['amount_type'] = "";
                }
            }

            //$json['charge_type'] = "PERCENT"; //PERCENT, FLAT, NONE, Empty
            if (isset($selcom_response['data'][0]['chargeType'])) {
                $json['charge_type'] = $selcom_response['data'][0]['chargeType'];
            } else {
                $json['charge_type'] = "";
            }

            //$json['charge'] = "1";
            if (isset($selcom_response['data'][0]['charge'])) {
                $json['charge'] = $selcom_response['data'][0]['charge'];
            } else {
                $json['charge'] = "";
            }
        } else {
            if ($enable_lookup == '1') {
                if (isset($resultdata[0]['type'])) {
                    $json['amount_type'] = $resultdata[0]['type'];
                }
            }
        }
        $qwikrewards_balance = $this->qwikrewardsBalance($this->user_id);
        $json['qwikrewards_balance'] = (string) number_format($qwikrewards_balance, 2);
        $json['payer_name'] = (string)$name;

        //$json['pay_number'] = (string)$qwikrewards_balance;

        /* if(!empty($amount)){
            $json['amount'] = $amount;
        }else{
            $json['amount'] = "";
        } */
        return $this->sendResponse('1', $json, trans('message.list_of_products'));
    }

    /**
     * Bill Payment Transaction
     * 
     * @return json array
     */
    public function billPaymentTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
            'account_no' => 'required',
            'bill_payment_product_id' => 'required',
            'language_code' => 'required|between:2,2',
        ]);

        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        }

        $input = $request->all();
        $language_code = $input['language_code'];

        $bill_account_no = $input['account_no'];
        if (isset($input['package_id'])) {
            $package_id = $input['package_id'];
        } else {
            $package_id = "";
        }

        if (isset($input['topup_label'])) {
            $topup_label = $input['topup_label'];
        } else {
            $topup_label = "";
        }

        if (isset($input['charge_amount'])) {
            $charge_amount = $input['charge_amount'];
        } else {
            $charge_amount = '0';
        }

        if (isset($input['lat']) && !empty($input['lng'])) {
            $lat = $input['lat'];
            $lng = $input['lng'];
        } else {
            $lat = "0.00";
            $lng = "0.00";
        }

        $bill_payment_product_id = $input['bill_payment_product_id'];
        $bill = BillpaymentProducts::find($bill_payment_product_id);
        if (isset($input['category_id'])) {
            $category_id = $input['category_id'];
        } else {
            $category_id = $bill['category_id'];
        }
        $utilitycode = $bill['utilitycode'];
        $bill_product_name = $bill['name'];

        $cat = Categories::find($category_id);
        $code = $cat['code'];
        $category_name = $cat['name'];

        $qwikrewards_amount = $input['qwikrewards_amount'];
        //$qwikrewards_amount = number_format($qwikrewards_amount);
        $qwikrewards_amount = str_replace(",", "", $qwikrewards_amount);
        $pay_with_qwikrewards = '0';
        if ($qwikrewards_amount != '0.00') {
            $pay_with_qwikrewards = '1';
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
        //$account_balance = number_format($account_balance);
        //$account_balance = str_ireplace(",","", $account_balance);

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
        $trans_insert->trans_type = 3;
        $trans_insert->trans_status = 0;
        $trans_insert->trans_amount_type = '0';
        $trans_insert->prev_balance = $account_balance;
        $trans_insert->receipt = '';
        $trans_insert->account_number = $bill_account_no;
        $trans_insert->user_ipaddress = $user_ipaddress;
        $trans_insert->trans_datetime = $this->datetime;
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
        $trans_param['paymentReference'] = $bill_account_no;
        $trans_param['utilityCode'] = $utilitycode;
        $trans_param['categoryCode'] = $code;
        $trans_param['category'] = $category_name;
        $trans_param['description'] = 'TZ';
        $trans_param['account'] = $account_number;
        $trans_param['service'] = '1';
        if ($utilitycode == 'INTLTOP') {
            $trans_param['packageId'] = $package_id;
        }
        if ($pay_with_qwikrewards == '1') {
            $trans_param['msisdn'] = $country_code . $mobile_number;
            $trans_param['sqrAmount'] = $qwikrewards_amount;
        }

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
                //return $this->sendError('0', $selcom_response['message'], array(), '200');
                if ($selcom_response['message'] != null) {
                    return $this->sendError('0', $selcom_response['message'], array(), '200');
                } else {
                    return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                }
                exit;
            }

            if (isset($selcom_response['data'])) {
                $json_data = $selcom_response['data'];
                $ara_receipt_stash = $json_data[0]['araReceipt'];
            } else {
                $ara_receipt_stash = '';
            }

            $stash_balance = $this->stashBalance($this->user_id);
        }
        $ara_balance = $this->araAvaBalance($this->user_id);

        $qwikreward_balance = '0';
        if ($pay_with_qwikrewards == '1') {
            $qwikreward_balance = $this->qwikrewardsBalance($this->user_id);
        }

        $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($trans_amount, 2) . '. Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);

        /**
         * Add transaction details here
         */
        $transactions = Transactions::find($t_id);
        $transactions->user_id = $this->user_id;
        $transactions->trans_id = $external_id;
        $transactions->ara_receipt = $ara_receipt;
        $transactions->category_id = $category_id;
        $transactions->trans_type = 3;
        $transactions->trans_status = 1;
        $transactions->party_name = $party_name;
        $transactions->prev_balance = $account_balance;
        $transactions->receipt = $receipt;
        $transactions->account_number = $bill_account_no;
        $transactions->latitude = $lat;
        $transactions->longitude = $lng;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {

            /**
             * Add bill payment transaction
             */
            $billpayment = new BillpaymentTransactions();
            $billpayment->user_id = $this->user_id;
            $billpayment->trans_id = $t_id;
            $billpayment->bill_payment_product_id = $bill_payment_product_id;
            $billpayment->trans_amount = $trans_amount;
            if ($pay_with_qwikrewards == '1') {
                $billpayment->pay_with_qwikrewards = '1';
                $billpayment->qwikrewards_amount = $qwikrewards_amount;
            } else {
                $billpayment->qwikrewards_amount = '0.00';
            }
            $billpayment->package_id = $package_id;
            $billpayment->topup_label = $topup_label;
            $billpayment->created_at = $this->datetime;
            $billpayment->updated_at = $this->datetime;
            $billpayment->save();
            if ($billpayment->id > 0) {

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
                    $updateBalance->account_balance = $account_balance-$amount;
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

                    $q_notification_msg1 = 'You have withdrawn ' . $currency_symbol . ' ' . number_format($qwikrewards_amount, 2) . ' from Qwikreward balance . Updated balance ' . $currency_symbol . ' ' . number_format($qwikreward_balance, 2);

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

                        //$notification_msg = 'Your Ara account '.$account_no.' has been debited '.$currency_symbol.' '.number_format($trans_amount - $qwikrewards_amount).'. Updated balance '.$currency_symbol.' '.number_format($ara_balance);

                        //$notification_msg = 'Your Ara account '.$account_no.' has been debited '.$currency_symbol.' '.number_format($trans_amount - $qwikrewards_amount).'. Updated balance '.$currency_symbol.' '.number_format($ara_balance);

                        if ($language_code == 'en') {
                            $notification_msg = 'You paid ' . $currency_symbol . ' ' . number_format($trans_amount) . ' to ' . $bill_product_name . ' on ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Ara Receipt # ' . $ara_receipt . ' Updated balance ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                        } else {
                            $notification_msg = 'Umelipa ' . $currency_symbol . ' ' . number_format($trans_amount) . ' kwenye ' . $bill_product_name . ' saa ' . date("d-m-Y H:i", strtotime($this->datetime)) . ' Stakabadhi ya Ara # ' . $ara_receipt . ' Salio lako jipya ni ' . $currency_symbol . ' ' . number_format($ara_balance, 2);
                        }

                        /* $login_result = $this->sendPuchNotification($device_type,$device_token,$notification_msg,$totalNotifications='0',$pushMessageText="","Pay Bills");
                        $this->selcomApiRequestResponse($this->user_id, "Notification - Bill Payment", $notification_msg, $login_result);

                        $notification_qry = new Notifications();
                        $notification_qry->user_id = $this->user_id;
                        $notification_qry->notification_type = 'transaction';
                        $notification_qry->notification_title = "Bill Payment";
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
                            $this->selcomApiRequestResponse($this->user_id, "notiification - Bill Payment", $stash_notification_msg, $login_result);

                            $notification_qry = new Notifications();
                            $notification_qry->user_id = $this->user_id;
                            $notification_qry->notification_type = 'transaction';
                            $notification_qry->notification_title = "Bill Payment - Stash Transaction";
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
                $json['trans_amount'] = number_format($trans_amount, 2);
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
            }
        } else {
            return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
        }
    }

    /**
     * Get bill payement list from selcom
     * Record update in middleware database
     */
    public function getBillPaymentProductFromSelcom()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://3.227.121.40/v1/config/billpay",
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK NjI2YjU3Y2ItZGJkMC00ODcxLTkzOWItYzYzNjIwMTQ2NTM0",
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            /* echo "cURL Error #:" . $err; */
            return true;
        } else {
            $response_arr = json_decode($response, true);
            $resultcode = $response_arr['resultcode'];
            $result = $response_arr['result'];
            $data_arr = $response_arr['data'];
            foreach ($data_arr as $val) {
                $category_code = $val['categoryCode'];
                $categories = Categories::where('code', '=', $category_code)->first();
                if (empty($categories)) {
                    $sql_add = new Categories();
                    $sql_add->name = $category_code;
                    $sql_add->code = $category_code;
                    $sql_add->icon = '';
                    $sql_add->status = '1';
                    $sql_add->created_at = date("Y-m-d H:i:s");
                    $sql_add->updated_at = date("Y-m-d H:i:s");
                    $sql_add->save();
                    $category_id = $sql_add->id;
                } else {
                    $category_id = $categories['id'];
                }

                $utility_code = $val['utilityCode'];
                $name = $val['name'];
                $short_name = $val['shortName'];
                $denominations = serialize($val['denominations']);
                $displayLabel = serialize($val['displayLabel']);
                $referenceLabel = serialize($val['referenceLabel']);
                $referenceLength = serialize($val['referenceLength']);
                $minimumAmount = $val['maximumAmount'];
                $maximumAmount = $val['minimumAmount'];
                $enableLookup = $val['enableLookup'];
                $amountOnLookup = $val['amountOnLookup'];
                $enablePackage = $val['enablePackage'];
                $packageLevels = $val['packageLevels'];
                $packages = serialize($val['packages']);
                $iconName = str_replace(" ", "_", strtolower($short_name) . '.png');

                $products = BillpaymentProducts::where('short_name', '=', $short_name)->first();
                if (empty($products)) {
                    $qry = new BillpaymentProducts();
                    $qry->category_id = $category_id;
                    $qry->name = $name;
                    $qry->short_name = $short_name;
                    $qry->utilitycode = $utility_code;
                    $qry->denominations = $denominations;
                    $qry->display_label = $displayLabel;
                    $qry->reference_label = $referenceLabel;
                    $qry->reference_length = $referenceLength;
                    $qry->minimum_amount = $minimumAmount;
                    $qry->maximum_amount = $maximumAmount;
                    $qry->enable_lookup = $enableLookup;
                    $qry->amount_on_lookup = $amountOnLookup;
                    $qry->enable_package = $enablePackage;
                    $qry->package_levels = $packageLevels;
                    $qry->status = '1';
                    $qry->ref_label = $short_name;
                    $qry->packages = $packages;
                    $qry->icon = $iconName;
                    $qry->created_at = date("Y-m-d H:i:s");
                    $qry->updated_at = date("Y-m-d H:i:s");
                    $qry->save();
                } else {
                    $id = $products['id'];
                    $qry = BillpaymentProducts::find($id);
                    $qry->category_id = $category_id;
                    $qry->name = $name;
                    $qry->short_name = $short_name;
                    $qry->utilitycode = $utility_code;
                    $qry->denominations = $denominations;
                    $qry->display_label = $displayLabel;
                    $qry->reference_label = $referenceLabel;
                    $qry->reference_length = $referenceLength;
                    $qry->minimum_amount = $minimumAmount;
                    $qry->maximum_amount = $maximumAmount;
                    $qry->enable_lookup = $enableLookup;
                    $qry->amount_on_lookup = $amountOnLookup;
                    $qry->enable_package = $enablePackage;
                    $qry->package_levels = $packageLevels;
                    $qry->status = '1';
                    $qry->ref_label = $short_name;
                    $qry->packages = $packages;
                    $qry->icon = $iconName;
                    $qry->updated_at = date("Y-m-d H:i:s");
                    $qry->save();
                }
            }
            /* print_r($data_arr); */
            /* echo $response; */
        }
    }

    /**
     * Get all transaction data by category
     */
    public function getReportByCategory(Request $request)
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

        $category_path = config('custom.upload.category');

        $params['type'] = '2';
        $params['group_by'] = 'categories.id';
        $cat = $this->billpaymentProductsRepository->getAllTransByCat($params);
        foreach ($cat as $val) {
            if (!empty($val->total_bill_payment_amount)) {
                $d['total_bill_payment_amount'] = $val->total_bill_payment_amount;
            } else {
                $d['total_bill_payment_amount'] = '0';
            }
            $d['category_name'] = $val->name;
            $d['icon'] = env('APP_URL') . '/storage/app/public/' . $category_path . "/" . $val->icon;
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.report_data'));
    }
}