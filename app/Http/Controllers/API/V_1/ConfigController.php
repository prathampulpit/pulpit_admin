<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use App\Repositories\UserRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\BillpaymentProductsRepository;
use App\Repositories\TopupsRepository;
use App\Repositories\TransactionsRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\BillpaymentProducts;
use App\Models\UserDebits;
use App\Models\UserCredits;
use App\Models\Qwikcashes;
use App\Models\Countries;
use App\Models\Cities;
use App\Models\User;
use App\Models\RemittanceCountries;
use App\Models\RemittanceBanks;
use App\Models\RemittanceWallets;
use App\Models\Categories;
use App\Models\Wallets;
use App\Models\Banks;
use App\Models\AppconfigVersions;
use App\Models\ForexRates;
use App\Models\Devices;
use App\Models\BubbleTextMessages;
use App\Models\BubbleTextMessageDetails;
use App\Models\Locators;
use App\Models\AddMoneyLabels;
use App\Models\SendMoneyLabels;
use App\Models\WithdrawMoneyLabels;
use App\Models\Settings;
use App\Models\Topics;
use Mail;
use App\Mail\ContactUsEmail;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class ConfigController extends BaseController
{
    protected $userAccountRepository;
    protected $userRepository;
    protected $billpaymentProductsRepository;
    protected $topupsRepository;
    protected $transactionsRepository;

    public function __construct(
        userAccountRepository $userAccountRepository,
        UserRepository $userRepository,
        BillpaymentProductsRepository $billpaymentProductsRepository,
        TopupsRepository $topupsRepository,
        TransactionsRepository $transactionsRepository
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->userRepository = $userRepository;
        $this->billpaymentProductsRepository = $billpaymentProductsRepository;
        $this->topupsRepository = $topupsRepository;
        $this->datetime = date("Y-m-d H:i:s");
    }

    /**
     * Check config version
     * 
     * @return json array
     */
    public function checkConfigApiVersion(Request $request)
    {
        $billPayment = AppconfigVersions::where('api_name', '=', 'billPayment')->first();
        $billPaymentVersion = $billPayment['version'];
        $d['billPayment'] = (string)$billPaymentVersion;

        $otherCountry = AppconfigVersions::where('api_name', '=', 'otherCountry')->first();
        $otherCountryVersion = $otherCountry['version'];
        $d['otherCountry'] = (string)$otherCountryVersion;

        $categoryWalletBanks = AppconfigVersions::where('api_name', '=', 'categoryWalletBanks')->first();
        $categoryWalletBanksVersion = $categoryWalletBanks['version'];
        $d['categoryWalletBanksVersion'] = (string)$categoryWalletBanksVersion;

        $city = AppconfigVersions::where('api_name', '=', 'city')->first();
        $cityVersion = $city['version'];
        $d['city'] = (string)$cityVersion;

        $nationalityIdentification = AppconfigVersions::where('api_name', '=', 'nationalityIdentification')->first();
        $nationalityIdentificationVersion = $nationalityIdentification['version'];
        $d['nationalityIdentification'] = (string)$nationalityIdentificationVersion;

        $supportTopics = AppconfigVersions::where('api_name', '=', 'supportTopics')->first();
        $supportTopicsVersion = $supportTopics['version'];
        $d['supportTopics'] = (string)$supportTopicsVersion;

        $disputeTransactionTopics = AppconfigVersions::where('api_name', '=', 'disputeTransactionTopics')->first();
        $disputeTransactionTopicsVersion = $disputeTransactionTopics['version'];
        $d['disputeTransactionTopics'] = (string)$disputeTransactionTopicsVersion;

        $response[] = $d;
        return $this->sendResponse('1', $response, trans('message.version'));
    }

    /**
     * List of Dispute Transaction Topics
     * 
     * @return json array
     */
    public function disputeTransactionTopicsConfig(Request $request)
    {
        /* $response = array (
            0 => 
            array (
              'topic_id' => '1',
              'display_label' => 
              array (
                'en' => 'Goods or service not provided',
                'sw' => 'Goods or service not provided',
              ),
            ),
            1 => 
            array (
              'topic_id' => '2',
              'display_label' => 
              array (
                'en' => 'Duplicate transaction',
                'sw' => 'Duplicate transaction',
              ),
            ),
            2 => 
            array (
              'topic_id' => '3',
              'display_label' => 
              array (
                'en' => 'Incorrect debit amount',
                'sw' => 'Incorrect debit amount',
              ),
            ),
        ); */
        $topics = Topics::where('type', '=', 'Dispute Transaction')->where('status', '=', '1')->get();
        $response = array();
        if (!empty($topics)) {
            foreach ($topics as $val) {
                $d['topic_id'] = (string)$val['id'];
                $d1['en'] = $val['name_en'];
                $d1['sw'] = $val['name_sw'];
                $d['display_label'] = $d1;
                $response[] = $d;
            }
        }
        return $this->sendResponse('1', $response, trans('message.config_list'));
    }

    /**
     * Get bill payement list from selcom
     * Record update in middleware database
     */
    public function getBillPaymentProductFromSelcom()
    {
        /* Bill Payment Category */
        $curl = 'config/utilitycategories';
        $cat_response_arr = $this->selcomApi($curl, '', '', 'GET');

        $cat_resultcode = $cat_response_arr['resultcode'];
        $cat_data_arr = $cat_response_arr['data'];

        $is_change1 = 0;
        if ($cat_resultcode == '200') {
            foreach ($cat_data_arr as $rec) {
                $category_name = $rec['name'];
                $category_code = $rec['code'];
                $status = $rec['status'];
                $displayLabel = $rec['displayLabel'];
                $categories = Categories::where('code', '=', $category_code)->first();
                if (empty($categories)) {
                    $sql_add = new Categories();
                    $sql_add->name = $category_name;
                    $sql_add->name_sw = $category_name;
                    $sql_add->code = $category_code;
                    $sql_add->icon = '';
                    $sql_add->display_label = serialize($displayLabel);
                    $sql_add->status = $status;
                    $sql_add->created_at = date("Y-m-d H:i:s");
                    $sql_add->updated_at = date("Y-m-d H:i:s");
                    $sql_add->save();
                    $is_change1 = 1;
                } else {
                    $category_id = $categories['id'];
                    $sql_edit = Categories::find($category_id);
                    $sql_edit->code = $category_code;
                    $sql_edit->status = $status;
                    $sql_edit->display_label = serialize($displayLabel);
                    $sql_edit->updated_at = date("Y-m-d H:i:s");
                    $sql_edit->save();
                }
            }
        }

        if ($is_change1 == 1) {
            $catcheckversion = appconfigVersions::where('id', '=', '3')->first();
            if (!empty($catcheckversion)) {
                $oldversion1 = $catcheckversion['version'];
                $newversion1 = $oldversion1 + 1;
                $data_update1['version'] = $newversion1;
                $this->recordAddEdit('appconfig_versions', $data_update1, 'id', '3');
            }
        }


        /* Bill Payment Config here */
        $url = 'config/billpay';
        $response_arr = $this->selcomApi($url, '', '', 'GET');

        $resultcode = $response_arr['resultcode'];
        $result = $response_arr['result'];
        $data_arr = $response_arr['data'];
        $is_change = 0;
        foreach ($data_arr as $val) {
            $category_code1 = $val['categoryCode'];
            $categories1 = Categories::where('code', '=', $category_code1)->first();
            if (empty($categories1)) {
                $sql_add = new Categories();
                $sql_add->name = $category_code1;
                $sql_add->code = $category_code1;
                $sql_add->icon = '';
                $sql_add->status = '1';
                $sql_add->created_at = date("Y-m-d H:i:s");
                $sql_add->updated_at = date("Y-m-d H:i:s");
                $sql_add->save();
                $category_id = $sql_add->id;
            } else {
                $category_id = $categories1['id'];
            }

            $selcom_id = $val['id'];
            $utility_code = $val['utilityCode'];
            $name = $val['name'];
            $short_name = $val['shortName'];
            $denominations = serialize($val['denominations']);
            $displayLabel = serialize($val['displayLabel']);
            $referenceLabel = serialize($val['referenceLabel']);
            $referenceLength = serialize($val['referenceLength']);
            $referenceType = $val['referenceType'];
            $minimumAmount = $val['maximumAmount'];
            $maximumAmount = $val['minimumAmount'];
            $enableLookup = $val['enableLookup'];
            $amountOnLookup = $val['amountOnLookup'];
            $enablePackage = $val['enablePackage'];
            $packageLevels = $val['packageLevels'];
            $packages = serialize($val['packages']);
            if (!empty($val['valueTopupLabel'])) {
                $value_topup_label = serialize($val['valueTopupLabel']);
            } else {
                $value_topup_label = serialize(array());
            }
            if (!empty($val['bundleTopupLabel'])) {
                $bundle_topup_label = serialize($val['bundleTopupLabel']);
            } else {
                $bundle_topup_label = serialize(array());
            }

            if (!empty($val['displayDesc'])) {
                $display_sub_label = serialize($val['displayDesc']);
            } else {
                $display_sub_label = serialize(array());
            }


            if (!empty($val['displayName'])) {
                $display_name = serialize($val['displayName']);
            } else {
                $display_name = serialize(array());
            }

            $iconName = str_replace(" ", "_", strtolower($short_name) . '.png');

            $products = BillpaymentProducts::where('selcom_id', '=', $selcom_id)->first();
            if (empty($products)) {
                $qry = new BillpaymentProducts();
                $qry->category_id = $category_id;
                $qry->selcom_id = $selcom_id;
                $qry->name = $name;
                $qry->short_name = $short_name;
                $qry->utilitycode = $utility_code;
                $qry->denominations = $denominations;
                $qry->display_name = $display_name;
                $qry->display_label = $displayLabel;
                $qry->display_sub_label = $display_sub_label;
                $qry->reference_label = $referenceLabel;
                $qry->reference_length = $referenceLength;
                $qry->reference_type = $referenceType;
                $qry->minimum_amount = $minimumAmount;
                $qry->maximum_amount = $maximumAmount;
                $qry->enable_lookup = $enableLookup;
                $qry->amount_on_lookup = $amountOnLookup;
                $qry->enable_package = $enablePackage;
                $qry->package_levels = $packageLevels;
                $qry->status = '1';
                $qry->ref_label = $short_name;
                $qry->packages = $packages;
                $qry->value_topup_label = $value_topup_label;
                $qry->bundle_topup_label = $bundle_topup_label;
                $qry->icon = $iconName;
                $qry->created_at = date("Y-m-d H:i:s");
                $qry->updated_at = date("Y-m-d H:i:s");
                $qry->save();
                $is_change = 1;
            } else {
                $id = $products['id'];
                $qry = BillpaymentProducts::find($id);
                $qry->category_id = $category_id;
                $qry->selcom_id = $selcom_id;
                $qry->name = $name;
                $qry->short_name = $short_name;
                $qry->utilitycode = $utility_code;
                $qry->denominations = $denominations;
                $qry->display_name = $display_name;
                $qry->display_label = $displayLabel;
                $qry->display_sub_label = $display_sub_label;
                $qry->reference_label = $referenceLabel;
                $qry->reference_length = $referenceLength;
                $qry->reference_type = $referenceType;
                $qry->minimum_amount = $minimumAmount;
                $qry->maximum_amount = $maximumAmount;
                $qry->enable_lookup = $enableLookup;
                $qry->amount_on_lookup = $amountOnLookup;
                $qry->enable_package = $enablePackage;
                $qry->package_levels = $packageLevels;
                $qry->status = '1';
                $qry->ref_label = $short_name;
                $qry->packages = $packages;
                $qry->value_topup_label = $value_topup_label;
                $qry->bundle_topup_label = $bundle_topup_label;
                $qry->updated_at = date("Y-m-d H:i:s");
                $qry->save();
            }
        }

        if ($is_change == 1) {
            $checkversion = appconfigVersions::where('api_name', '=', 'billPayment')->first();
            if (!empty($checkversion)) {
                $oldversion = $checkversion['version'];
                $newversion = $oldversion + 1;
                $data_update['version'] = $newversion;
                $this->recordAddEdit('appconfig_versions', $data_update, 'api_name', 'billPayment');
            }
        }
        /* print_r($data_arr); */
        /* echo $response; */
    }

    /**
     * Get bill category list from selcom
     * Record update in middleware database
     */
    public function getCategoryListFromSelcom()
    {
        $url = 'config/utilitycategories';
        $response_arr = $this->selcomApi($url, '', '', 'GET');

        $resultcode = $response_arr['resultcode'];
        $data_arr = $response_arr['data'];

        $is_change = 0;
        if ($resultcode == '200') {
            foreach ($data_arr as $val) {
                $category_name = $val['name'];
                $category_code = $val['code'];
                $status = $val['status'];
                $categories = Categories::where('code', '=', $category_code)->first();
                if (empty($categories)) {
                    $sql_add = new Categories();
                    $sql_add->name = $category_name;
                    $sql_add->name_sw = $category_name;
                    $sql_add->code = $category_code;
                    $sql_add->icon = '';
                    $sql_add->status = $status;
                    $sql_add->created_at = date("Y-m-d H:i:s");
                    $sql_add->updated_at = date("Y-m-d H:i:s");
                    $sql_add->save();
                    $is_change = 1;
                } else {
                    $category_id = $categories['id'];
                    $sql_edit = Categories::find($category_id);
                    $sql_edit->code = $category_code;
                    $sql_edit->status = $status;
                    $sql_edit->updated_at = date("Y-m-d H:i:s");
                    $sql_edit->save();
                }
            }
        }

        if ($is_change == 1) {
            $checkversion = appconfigVersions::where('id', '=', '3')->first();
            if (!empty($checkversion)) {
                $oldversion = $checkversion['version'];
                $newversion = $oldversion + 1;
                $data_update['version'] = $newversion;
                $this->recordAddEdit('appconfig_versions', $data_update, 'id', '3');
            }
        }
    }

    /**
     * List of bill payment products
     * 
     * @return json array 
     */
    public function billPaymentProductLists(Request $request)
    {
        /* $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_type' => 'required',
            'int_udid' => 'required',
            'device_token' => 'required',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('-11', trans('message.parameters_missing'), $validator->errors(), '200');
        } */

        $categories = Categories::where('status', '=', '1')->get();
        if ($categories->isEmpty()) {
            return $this->sendError('0', trans('message.blank_category'), array(), '200');
        }
        $biller_path = config('custom.upload.billerImages');

        $response = array();
        foreach ($categories as $val) {
            $d['category_id'] = (string)$val['id'];

            $category_name = strtolower($val['name']);
            $d['category_name'] = ucfirst($category_name);

            //$d['category_name'] = $val['name'];
            $d['category_code'] = $val['code'];
            $d['category_display_label'] = unserialize($val['display_label']);

            $param['category_id'] = $val['id'];
            $param['status'] = '1';
            $products = $this->billpaymentProductsRepository->getByParams($param);
            $arr = array();
            if (!$products->isEmpty()) {
                foreach ($products as $rec) {
                    $d1['billpayment_product_id'] = $rec['billpayment_product_id'];
                    //$d1['icon'] = env('APP_URL') . '/storage/' . $biller_path . "/" . $rec['icon'];
                    $d1['icon'] = 'storage/' . $biller_path . "/" . $rec['icon'];
                    $d1['category_id'] = $rec['category_id'];
                    $d1['product_name'] = $rec['product_name'];
                    $d1['utilitycode'] = $rec['utilitycode'];
                    $d1['ref_label'] = $rec['ref_label'];
                    $d1['reference_type'] = $rec['reference_type'];
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
                    $d1['value_topup_label'] = unserialize($rec['value_topup_label']);
                    $d1['bundle_topup_label'] = unserialize($rec['bundle_topup_label']);
                    $d1['display_name'] = unserialize($rec['display_name']);
                    $d1['display_sub_label'] = unserialize($rec['display_sub_label']);
                    $arr[] = $d1;
                }
            }

            $d['product_list'] = $arr;
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_products'));
    }

    /**
     * List of Wallet, category and banks
     * 
     * @return json array 
     */
    public function categoryWalletBanksAppConfig(Request $request)
    {
        $categories = Categories::where('status', '=', '1')->orderBy('name', 'ASC')->get();
        if ($categories->isEmpty()) {
            return $this->sendError('0', trans('message.blank_category'), array(), '200');
        }
        $biller_path = config('custom.upload.category');

        $response = array();
        $json_category = array();
        foreach ($categories as $val) {
            $d['category_id'] = (string)$val['id'];
            //$d['name'] = $val['name'];
            $category_name = strtolower($val['name']);
            $d['name'] = ucfirst($category_name);

            $category_name_sw = strtolower($val['name_sw']);
            $d['name_sw'] = ucfirst($category_name_sw);

            $d['code'] = $val['code'];
            $d['category_image'] = 'storage/' . $biller_path . "/" . $val['icon'];
            $d['status'] = (string)$val['status'];
            $d['is_default'] = (string)$val['is_default'];
            $d['mcc_code'] = $val['mcc_code'];
            $json_category[] = $d;
        }
        $arr['category'] = $json_category;

        $wallets = Wallets::where('status', '=', '1')->get();
        $json_wallets = array();
        if (!empty($wallets)) {
            foreach ($wallets as $rec) {
                $d1['wallet_id'] = (string)$rec['id'];
                $d1['wallet_name'] = $rec['wallet_name'];
                $d1['short_name'] = $rec['short_name'];
                $d1['utility_code'] = $rec['utility_code'];
                $d1['wallet_code'] = $rec['wallet_code'];
                $d1['ref_label'] = $rec['ref_label'];
                $d1['status'] = (string)$rec['status'];
                $d1['minimum_amount'] = (string)$rec['minimum_amount'];
                $d1['maximum_amount'] = (string)$rec['maximum_amount'];
                $d1['addmoney_enabled'] = (string)$rec['addmoney_enabled'];
                $d1['addmoney_push_enabled'] = (string)$rec['addmoney_push_enabled'];
                $d1['addmoney_instruction'] = unserialize($rec['addmoney_instruction']);
                $d1['icon'] = 'storage/mobilemoney/' . $rec['icon'];
                $json_wallets[] = $d1;
            }
        }
        $arr['wallet'] = $json_wallets;

        $banks = Banks::where('status', '=', '1')->orderBy('bank_name', 'asc')->get();
        $json_banks = array();
        if (!empty($banks)) {
            foreach ($banks as $rec) {
                $d2['bank_id'] = (string)$rec['id'];
                $d2['bank_name'] = $rec['bank_name'];
                $d2['short_name'] = $rec['short_name'];
                $d2['utility_code'] = $rec['utility_code'];
                $d2['lookup_enabled'] = (string)$rec['lookup_enabled'];
                $d2['reference_length'] = unserialize($rec['reference_length']);
                $d2['reference_type'] = $rec['reference_type'];
                $json_banks[] = $d2;
            }
        }
        $arr['bank'] = $json_banks;

        /* Add money labels config */
        $addmoney_path = config('custom.upload.addmoney');
        $addMoneyLabels = AddMoneyLabels::where('status', '=', '1')->get();
        $json_add_money_labels = array();
        if (!empty($addMoneyLabels)) {
            foreach ($addMoneyLabels as $rec) {
                $d3['id'] = (string)$rec['id'];
                $d3['display_name'] = $rec['display_name'];
                $d3['display_name_sw'] = $rec['display_name_sw'];
                $d3['sub_title'] = $rec['sub_title'];
                $d3['sub_title_sw'] = $rec['sub_title_sw'];
                $d3['code'] = $rec['code'];
                $d3['icon'] = 'storage/' . $addmoney_path . "/" . $rec['icon'];

                $r['en'] = $rec['display_name'];
                $r['sw'] = $rec['display_name_sw'];
                $d3['display_name_arr'] = $r;

                $r1['en'] = $rec['sub_title'];
                $r1['sw'] = $rec['sub_title_sw'];
                $d3['sub_title_arr'] = $r1;

                $json_add_money_labels[] = $d3;
            }
        }
        $arr['add_money_labels'] = $json_add_money_labels;
        /* End */

        /* Send money labels config */
        $sendmoney_path = config('custom.upload.sendmoney');
        $sendMoneyLabels = SendMoneyLabels::where('status', '=', '1')->get();
        $json_send_money_labels = array();
        foreach ($sendMoneyLabels as $rec) {
            $d3['id'] = (string)$rec['id'];
            $d3['display_name'] = $rec['display_name'];
            $d3['display_name_sw'] = $rec['display_name_sw'];
            $d3['sub_title'] = $rec['sub_title'];
            $d3['sub_title_sw'] = $rec['sub_title_sw'];
            $d3['code'] = $rec['code'];
            $d3['icon'] = 'storage/' . $sendmoney_path . "/" . $rec['icon'];

            $r['en'] = $rec['display_name'];
            $r['sw'] = $rec['display_name_sw'];
            $d3['display_name_arr'] = $r;

            $r1['en'] = $rec['sub_title'];
            $r1['sw'] = $rec['sub_title_sw'];
            $d3['sub_title_arr'] = $r1;

            $json_send_money_labels[] = $d3;
        }
        $arr['send_money_labels'] = $json_send_money_labels;
        /* End */

        /* Withdraw money labels config */
        $withdrawmoney_path = config('custom.upload.withdrawmoney');
        $withdrawMoneyLabels = WithdrawMoneyLabels::where('status', '=', '1')->get();
        $json_withdraw_money_labels = array();
        foreach ($withdrawMoneyLabels as $rec) {
            $d3['id'] = (string)$rec['id'];
            $d3['display_name'] = $rec['display_name'];
            $d3['display_name_sw'] = $rec['display_name_sw'];
            $d3['sub_title'] = $rec['sub_title'];
            $d3['sub_title_sw'] = $rec['sub_title_sw'];
            $d3['code'] = $rec['code'];
            $d3['icon'] = 'storage/' . $withdrawmoney_path . "/" . $rec['icon'];

            $r['en'] = $rec['display_name'];
            $r['sw'] = $rec['display_name_sw'];
            $d3['display_name_arr'] = $r;

            $r1['en'] = $rec['sub_title'];
            $r1['sw'] = $rec['sub_title_sw'];
            $d3['sub_title_arr'] = $r1;

            $json_withdraw_money_labels[] = $d3;
        }
        $arr['withdraw_money_labels'] = $json_withdraw_money_labels;
        /* End */

        $settings = Settings::find(1);
        $arr['maximum_share_number_limit'] = (string)$settings['maximum_share_number_limit'];
        $arr['maximum_referral_request_limit'] = (string)$settings['maximum_referral_request_limit'];
        $arr['referral_instruction_en'] = (string)$settings['referral_instruction_en'];
        $arr['referral_instruction_sw'] = (string)$settings['referral_instruction_sw'];
        $arr['referral_instruction_title_en'] = (string)$settings['referral_instruction_title_en'];
        $arr['referral_instruction_title_sw'] = (string)$settings['referral_instruction_title_sw'];
        $arr['referral_welcome_message_en'] = (string)$settings['referral_welcome_message_en'];
        $arr['referral_welcome_message_sw'] = (string)$settings['referral_welcome_message_sw'];
        $arr['referral_enable'] = (string)$settings['referral_enable'];

        $arr['referral_request_screen_title_en'] = (string)$settings['referral_request_screen_title_en'];
        $arr['referral_request_screen_title_sw'] = (string)$settings['referral_request_screen_title_sw'];

        $arr['referral_request_screen_content_en'] = (string)$settings['referral_request_screen_content_en'];
        $arr['referral_request_screen_content_sw'] = (string)$settings['referral_request_screen_content_sw'];

        $arr['no_contact_register_error_message_en'] = (string)$settings['no_contact_register_error_message_en'];
        $arr['no_contact_register_error_message_sw'] = (string)$settings['no_contact_register_error_message_sw'];

        $arr['contact_list_screen_message_en'] = (string)$settings['contact_list_screen_message_en'];
        $arr['contact_list_screen_message_sw'] = (string)$settings['contact_list_screen_message_sw'];

        $arr['refer_a_friend_success_message_en'] = (string)$settings['refer_a_friend_success_message_en'];
        $arr['refer_a_friend_success_message_sw'] = (string)$settings['refer_a_friend_success_message_sw'];

        $arr['max_no_of_physical_cards'] = (string)$settings['max_no_of_physical_cards'];

        $response = $arr;
        return $this->sendResponse('1', $response, trans('message.list_of_products'));
    }

    /**
     * List of other country wallet and bank details
     * 
     * @return json array 
     */
    public function otherCountryAppConfig(Request $request)
    {
        $countries = RemittanceCountries::all();
        $response = array();
        foreach ($countries as $rec) {
            $d['id'] = (string)$rec['id'];
            $d['name'] = $rec['country_name'];
            $d['short_name'] = $rec['short_name'];
            $d['code'] = $rec['code'];

            $banks = RemittanceBanks::where('country_code', '=', $rec['code'])->get();
            $json_banks = array();
            foreach ($banks as $val) {
                $d1['id'] = (string)$val['id'];
                $d1['name'] = $val['bank_name'];
                $d1['short_name'] = $val['short_name'];
                $d1['country_code'] = $val['country_code'];
                $d1['utility_code'] = $val['utility_code'];
                $json_banks[] = $d1;
            }

            $wallets = RemittanceWallets::where('country_code', '=', $rec['code'])->get();
            $json_wallets = array();
            foreach ($wallets as $val1) {
                $d2['id'] = (string)$val1['id'];
                $d2['name'] = $val1['wallet_name'];
                $d2['short_name'] = $val1['short_name'];
                $d2['utility_code'] = $val1['utility_code'];
                $json_wallets[] = $d2;
            }
            $arr['wallets'] = $json_wallets;
            $arr['banks'] = $json_banks;
            $d['destinations'] = $arr;
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_othercountry_config'));
    }

    /**
     * List of other country wallet and bank details
     * 
     * @return json array 
     */
    public function cityAppConfig(Request $request)
    {
        $cities = Cities::where("status", "=", "1")->orderBy('population', 'desc')->get();
        $response = array();
        foreach ($cities as $rec) {
            $d['id'] = (string)$rec['id'];
            $d['title'] = $rec['name'];
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_othercountry_config'));
    }

    public function citySearch(Request $request)
    {
        $cities = Cities::where('name', 'LIKE', '%' . $request->input('term', '') . '%')
            ->get(['id', 'name as text']);
        return ['results' => $cities];
    }

    public function userSearch(Request $request)
    {
        $users = User::where('name', 'LIKE', '%' . $request->input('term', '') . '%')->where('role_id', '=', '0')
            ->get(['id', 'name as text']);
        return ['results' => $users];
    }

    /**
     * List of other country wallet and bank details
     * 
     * @return json array 
     */
    public function countryAppConfig(Request $request)
    {
        $countries = Countries::where("status", "=", "1")->get();
        $response = array();
        foreach ($countries as $rec) {
            $d['country_id'] = (string)$rec['id'];
            $d['country_name'] = $rec['country'];
            $d['iso_code'] = $rec['iso_code'];
            $d['code'] = $rec['code'];
            $response[] = $d;
        }
        return $this->sendResponse('1', $response, trans('message.list_of_othercountry_config'));
    }

    /**
     * List of nationality and Identification
     * 
     * @return json array
     */
    public function nationalityIdentificationConfig(Request $request)
    {
        $response = array(
            'nationality' =>
            array(
                0 =>
                array(
                    'id' => '1',
                    'title' => 'Tanzanian National',
                    'is_identification_required' => true,
                ),
                1 =>
                array(
                    'id' => '2',
                    'title' => 'Tanzanian Resident (Foreign National)',
                    'is_identification_required' => false,
                ),
            ),
            'identification' =>
            array(
                0 =>
                array(
                    'id' => '6',
                    'title' => 'NIDA',
                ),
                /* 1 => 
              array (
                'id' => '2',
                'title' => 'Passport',
              ), */
            ),
        );
        return $this->sendResponse('1', $response, trans('message.list_of_nationality_identification_config'));
    }

    /**
     * List of nationality and Identification
     * 
     * @return json array
     */
    public function supportTopicsConfig(Request $request)
    {
        /* $response = array (
            0 => 
            array (
              'topic_id' => '1',
              'display_label' => 
              array (
                'en' => 'Transaction issue',
                'sw' => 'Transaction issue',
              ),
            ),
            1 => 
            array (
              'topic_id' => '2',
              'display_label' => 
              array (
                'en' => 'Card is not working',
                'sw' => 'Card is not working',
              ),
            ),
            2 => 
            array (
              'topic_id' => '3',
              'display_label' => 
              array (
                'en' => 'Other issue',
                'sw' => 'Other issue',
              ),
            ),
        ); */
        $topics = Topics::where('type', '=', 'Contact and Support')->where('status', '=', '1')->get();
        $response = array();
        if (!empty($topics)) {
            foreach ($topics as $val) {
                $d['topic_id'] = (string)$val['id'];
                $d1['en'] = $val['name_en'];
                $d1['sw'] = $val['name_sw'];
                $d['display_label'] = $d1;
                $response[] = $d;
            }
        }
        return $this->sendResponse('1', $response, trans('message.list_of_support_topics_config'));
    }

    /**
     * Get bank list from selcom
     * 
     * @return json array
     */
    public function getBankListFromSelcom()
    {
        $url = 'config/banks';
        $response_arr = $this->selcomApi($url, '', '', 'GET');
        /* print_r($response_arr); */
        if (!empty($response_arr)) {
            $resultcode = $response_arr['resultcode'];
            if ($resultcode == '200') {
                $bank_arr = $response_arr['data'];
                $is_change = 0;
                if (!empty($bank_arr)) {
                    foreach ($bank_arr as $val) {
                        $name = $val['name'];
                        $shortName = $val['shortName'];
                        $utilityCode = $val['utilityCode'];
                        $status = $val['status'];
                        $lookupEnabled = $val['lookupEnabled'];

                        $reference_length = serialize($val['referenceLength']);
                        $reference_type = $val['referenceType'];

                        $check = Banks::where('utility_code', '=', $utilityCode)->first();
                        if (empty($check)) {
                            $qry = new Banks();
                            $qry->bank_name = $name;
                            $qry->short_name = $shortName;
                            $qry->utility_code = $utilityCode;
                            $qry->lookup_enabled = $lookupEnabled;
                            $qry->status = $status;
                            $qry->reference_length = $reference_length;
                            $qry->reference_type = $reference_type;
                            $qry->save();
                            $is_change = 1;
                        } else {
                            $id = $check['id'];
                            $qry = Banks::find($id);
                            $qry->bank_name = $name;
                            $qry->short_name = $shortName;
                            $qry->utility_code = $utilityCode;
                            $qry->lookup_enabled = $lookupEnabled;
                            $qry->status = $status;
                            $qry->reference_length = $reference_length;
                            $qry->reference_type = $reference_type;
                            $qry->save();
                        }
                    }

                    if ($is_change == 1) {
                        $checkversion = appconfigVersions::where('api_name', '=', 'categoryWalletBanks')->first();
                        if (!empty($checkversion)) {
                            $oldversion = $checkversion['version'];
                            $newversion = $oldversion + 1;
                            $data_update['version'] = $newversion;
                            $this->recordAddEdit('appconfig_versions', $data_update, 'api_name', 'categoryWalletBanks');
                        }
                    }
                }
            }
        }
    }

    /**
     * Get wallet list from selcom
     * 
     * @return json array
     */
    public function getWalletListFromSelcom()
    {
        $url = 'config/mobilewallets';
        $response_arr = $this->selcomApi($url, '', '', 'GET');

        /* print_r($response_arr); 
        exit; */

        if (!empty($response_arr)) {
            $resultcode = $response_arr['resultcode'];
            if ($resultcode == '200') {
                $bank_arr = $response_arr['data'];
                $is_change = 0;
                if (!empty($bank_arr)) {
                    foreach ($bank_arr as $val) {
                        $name = $val['name'];
                        $shortName = $val['shortName'];
                        $utilityCode = $val['utilityCode'];
                        $status = $val['status'];
                        $maximumAmount = $val['maximumAmount'];
                        $minimumAmount = $val['minimumAmount'];

                        $addmoneyEnabled = $val['addmoneyEnabled'];
                        $addmoneyPushEnabled = $val['addmoneyPushEnabled'];
                        $addmoneyInstruction = serialize($val['addmoneyInstruction']);

                        $check = Wallets::where('utility_code', '=', $utilityCode)->first();
                        if (empty($check)) {
                            $qry = new Wallets();
                            $qry->wallet_name = $name;
                            $qry->short_name = $shortName;
                            $qry->utility_code = $utilityCode;
                            $qry->minimum_amount = $minimumAmount;
                            $qry->maximum_amount = $maximumAmount;
                            $qry->status = $status;
                            $qry->addmoney_enabled = $addmoneyEnabled;
                            $qry->addmoney_push_enabled = $addmoneyPushEnabled;
                            $qry->addmoney_instruction = $addmoneyInstruction;
                            $qry->save();
                            $is_change = 1;
                        } else {
                            $id = $check['id'];
                            $qry = Wallets::find($id);
                            //$qry->wallet_name = $name;
                            $qry->short_name = $shortName;
                            $qry->utility_code = $utilityCode;
                            $qry->minimum_amount = $minimumAmount;
                            $qry->maximum_amount = $maximumAmount;
                            $qry->addmoney_enabled = $addmoneyEnabled;
                            $qry->addmoney_push_enabled = $addmoneyPushEnabled;
                            $qry->addmoney_instruction = $addmoneyInstruction;
                            $qry->status = $status;
                            $qry->save();
                        }
                    }

                    if ($is_change == 1) {
                        $checkversion = appconfigVersions::where('api_name', '=', 'categoryWalletBanks')->first();
                        if (!empty($checkversion)) {
                            $oldversion = $checkversion['version'];
                            $newversion = $oldversion + 1;
                            $data_update['version'] = $newversion;
                            $this->recordAddEdit('appconfig_versions', $data_update, 'api_name', 'categoryWalletBanks');
                        }
                    }
                }
            }
        }
    }

    /* 
    * Get fotex rate 
    * @return json array
    */
    public function getForexRate()
    {

        $url = 'config/forex';
        $response_arr = $this->selcomApi($url, '', '', 'GET');
        $resultcode = $response_arr['resultcode'];
        $result = $response_arr['result'];

        if ($resultcode == '200' && $result == 'SUCCESS') {
            $arr = $response_arr['data'];

            //print_r($arr);

            foreach ($arr as $val) {
                $baseCurrency = $val['baseCurrency'];
                $otherCurrency = $val['otherCurrency'];
                $forexRate = $val['forexRate'];

                $qry = ForexRates::where('base_currency', $baseCurrency)->where('other_currency', $otherCurrency)->first();
                if (!empty($qry)) {
                    /* echo $qry['base_currency']."==".$qry['other_currency']."==".$forexRate;
                    echo "\n"; */
                    $id = $qry['id'];
                    $update = ForexRates::find($id);
                    $update->forex_rate = $forexRate;
                    $update->updated_at = date("Y-m-d H:i:s");
                    $update->save();
                } else {
                    $add = new ForexRates();
                    $add->forex_rate = $forexRate;
                    $add->base_currency = $baseCurrency;
                    $add->other_currency = $otherCurrency;
                    $add->created_at = date("Y-m-d H:i:s");
                    $add->updated_at = date("Y-m-d H:i:s");
                    $add->save();
                }
            }
        }
    }

    /**
     * Store Locator Details
     * @return json array
     */
    public function storeLocators()
    {
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $json_request = json_encode($param);
        $atm_arr = $this->selcomApi('config/atm-locations', $json_request, '48', "GET");
        $json_atm = array();
        if (!empty($atm_arr)) {
            foreach ($atm_arr['data'] as $val) {
                $latitude = $val['gpsLat'];
                $longitude = $val['gpsLong'];
                $status = $val['status'];
                $name = $val['name'];
                $address = $val['address'];
                $phone_number = $val['phoneNumber'];

                $locator = Locators::where('latitude', $latitude)->where('longitude', $longitude)->first();
                if (!empty($locator)) {
                    $id = $locator['id'];
                    $edit = Locators::find($id);
                    $edit->type = '1';
                    $edit->status = $status;
                    $edit->name = $name;
                    $edit->address = $address;
                    $edit->phone_number = $phone_number;
                    $edit->latitude = $latitude;
                    $edit->longitude = $longitude;
                    $edit->save();
                } else {
                    $add = new Locators();
                    $add->type = '1';
                    $add->status = $status;
                    $add->name = $name;
                    $add->address = $address;
                    $add->phone_number = $phone_number;
                    $add->latitude = $latitude;
                    $add->longitude = $longitude;
                    $add->save();
                }
            }
        }

        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $json_request = json_encode($param);
        $agent_arr = $this->selcomApi('config/agent-locations', $json_request, '48', "GET");
        $json_atm = array();
        if (!empty($agent_arr)) {
            foreach ($agent_arr['data'] as $val) {
                $latitude = $val['gpsLat'];
                $longitude = $val['gpsLong'];
                $status = $val['status'];
                $name = $val['name'];
                $address = $val['address'];
                $phone_number = $val['phoneNumber'];

                $locator = Locators::where('latitude', $latitude)->where('longitude', $longitude)->first();
                if (!empty($locator)) {
                    $id = $locator['id'];
                    $edit = Locators::find($id);
                    $edit->type = '2';
                    $edit->status = $status;
                    $edit->name = $name;
                    $edit->address = $address;
                    $edit->phone_number = $phone_number;
                    $edit->latitude = $latitude;
                    $edit->longitude = $longitude;
                    $edit->save();
                } else {
                    $add = new Locators();
                    $add->type = '2';
                    $add->status = $status;
                    $add->name = $name;
                    $add->address = $address;
                    $add->phone_number = $phone_number;
                    $add->latitude = $latitude;
                    $add->longitude = $longitude;
                    $add->save();
                }
            }
        }
    }

    /**
     * Remove login attempt after 24 hours
     * @return true false
     */
    public function removeAttempt()
    {
        $users = User::where('login_attempt', '=', '3')->get(['id', 'login_attempt', 'login_attempt_datetime']);

        if (!empty($users)) {
            foreach ($users as $val) {
                $id = $val['id'];
                $login_attempt_datetime = $val['login_attempt_datetime'];

                $date1 = $login_attempt_datetime;
                $date2 = date("Y-m-d H:i:s");
                $timestamp1 = strtotime($date1);
                $timestamp2 = strtotime($date2);
                $hour = number_format(abs($timestamp2 - $timestamp1) / (60 * 60));
                if ($hour >= '25') {
                    $model = User::find($id);
                    $model->login_attempt = '0';
                    $model->save();
                }
            }
        }
    }

    /* 
    * Demo
    * @return json array
    */
    public function demoFunc()
    {

        $url = 'config/billpay';
        //$url = 'config/utilitycategories';
        $url = 'client/551/linkedcards';
        $url = '/config/utilitycategories';
        $response_arr = $this->selcomApi($url, '', '', 'GET');
        print_r($response_arr);
        exit;

        /* $external_id = rand(1000,9999).time().rand(1000,9999);
        $param['account'] = "3401000002623";
        $param['amount'] = "100";
        $param['msisdn'] = "255682852526";
        $param['externalId'] = $external_id;
        $param['utilityCode'] = "VMCASHIN";
        echo $json_request = json_encode($param);
        $atm_arr = $this->selcomApiTest('client/551/mwallet-push-ussd',$json_request,'436',"POST");
        print_r($atm_arr); */
        exit;

        /* $title = "ARA TESTING";
        $deviceType = "Android";
        $totalNotifications = "1";
        $notificationText = "This is testing message";
        $fields = "notificationId";
        $devicetoken[] = "frUpe82km07rr0iK5Jqq0u:APA91bEHCL4IKHZPlER3WxjWtCPKhGRhI-fUmLdK19iqlV1MSeoQ2JzUVHfZ6_yMz2rlQqIGQXtseODOuKKslZIwEaKSYjjUFWil5QGiRfJz4FfOjO0ksY6zzTABbuyakXFSt9jazcKl";
        $desc = $notificationText;

        // Set POST variables 
        $url = 'https://fcm.googleapis.com/fcm/send';
        //$message = array("message" => $desc);
        $message = array("message" => $desc, 'title' => $title, 'click_action' => "FLUTTER_NOTIFICATION_CLICK", 'status' => 'done');
        //$message = $desc;
        
        //echo $totalNotifications;
        $notificationArray = array(
            'badge' => $totalNotifications,
            'body' => $desc,
            'sound' => 'default',
            'title' => $title,
        );
    
        if ($deviceType == 'Iphone') {
            $fields = array(
                'registration_ids' => $devicetoken,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        } else {
            $fields = array(
                'registration_ids' => $devicetoken,
                'data' => $message,
                'notification' => $notificationArray,
                'priority' => 'high',
            );
        }

        $fieldsJson = json_encode($fields);
        $fieldsJson = str_replace('\"', '', $fieldsJson);

        $headers = array(
            'Authorization: key=AAAACCkPHLU:APA91bH8KJk0ahiXINQ1FDZeHuyLk2-cGjeIJtJAh_XFgaN--gPVbkmYhQSGtOMjNzzVrxvKzzumW5qclNs0dqk8HgrgQqiBhbtxPCwoRtofhwljcG6wV25FVlRPB4fWHTY6cqEF8lOa',
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fieldsJson);
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Curl failed: ' . curl_error($ch));
        }
        curl_close($ch);
        echo $result;

        exit; */
        /* $external_id = rand(1000,9999).substr(time(), -7);
                
        $user = User::find('213');
        $client_id = $user->client_id;
        $profile_picture = $user->profile_picture;

        $profile_path = config('custom.upload.user.profile');
        $profile_file_path = storage_path('app') . '/public/' . $profile_path . "/" . $profile_picture;

        $profile_img_type = pathinfo($profile_file_path, PATHINFO_EXTENSION);
        $data = file_get_contents($profile_file_path);
        $base64Profile = base64_encode($data);
        $external_id_profile = rand(1000,9999).substr(time(), -7);
        $doc_param_profile['externalId'] = $external_id_profile;
        $doc_param_profile['imageType'] = 'image/'.$profile_img_type;
        $doc_param_profile['imageData'] = $base64Profile;
        echo $doc_json_request = json_encode($doc_param_profile);
        echo "\n";
        $upload_doc_identity = $this->selcomApiTest('client/'.$client_id.'/image', $doc_json_request, '213', 'POST');
        print_r($upload_doc_identity);
        exit;

        $namearr = explode(" ", $user->name);
        $first_name = $namearr[0];
        $first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $namearr[1];
        $last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $gender = $user->gender;
        $email = $user->email;
        $city_id = $user->city_id;
        if(!empty($city_id)){
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        }else{
            $city_name = "";
        }
        $language = "en";
        $dob = $user->dob;
        $address = $user->address;
        $latitude = $user->latitude;
        $longitude = $user->longitude;
        
        $param_client['externalId'] = $external_id;
        $param_client['firstname'] = $first_name;
        $param_client['lastname'] = $last_name;
        $param_client['language'] = $language;
        $param_client['msisdn'] = $country_code.$mobile_number;
        $param_client['dob'] = $dob;
        $param_client['email'] = $email;
        $param_client['gender'] = "MALE"; //($gender=='MALE')?"M":"F";
        $param_client['active'] = "1";
        $param_client['location.city'] = $city_name;
        $param_client['location.street'] = $address;
        $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
        $param_client['location.country'] = "TZ";
        echo $client_json_request = json_encode($param_client);
        echo "\n";
        551
        //echo 'client/'.$client_id;
        $accounts = $this->selcomApiTest('client/'.$client_id, $client_json_request, '100', "PUT");
        print_r($accounts);
        exit; */

        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $param['street'] = "";
        $param['addressLine1'] = "Swastik cross road";
        $param['city'] = "Dodoma";
        $param['region'] = "";
        $param['country'] = "Tanzania";
        $json_request = json_encode($param);
        $atm_arr = $this->selcomApi('/client/551/current-address', $json_request, '436', "POST");
        print_r($atm_arr);
        exit;

        $api_url = 'vcn/status';
        $param['msisdn'] = "255711410410";
        $param['account'] = "3401000001708";
        $selcom_response = $this->selcomDevApi1($api_url, $param, false);
        print_r($selcom_response);
        exit;

        /* $url = 'config/forex';
        $response_arr = $this->selcomApiTest($url,'','','GET');
        print_r($response_arr);
        exit; */

        //$a = $this->araAvaBalance('143');
        $user = User::find('154');
        $client_id = $user->client_id;

        $accounts = $this->selcomApi('client/' . $client_id . '/accounts', array(), '143', "GET");
        print_r($accounts);
        exit;

        /* $external_id = rand(1000,9999).substr(time(), -7);
        $user = User::find('100');
        $client_id = $user->client_id;
        $namearr = explode(" ", $user->name);
        $first_name = $namearr[0];
        $first_name = preg_replace('/[^A-Za-z0-9]/', '', $first_name);
        $last_name = $namearr[1];
        $last_name = preg_replace('/[^A-Za-z0-9]/', '', $last_name);
        $country_code = $user->country_code;
        $mobile_number = $user->mobile_number;
        $gender = $user->gender;
        $email = $user->email;
        $city_id = $user->city_id;
        if(!empty($city_id)){
            $city = Cities::find($city_id);
            $city_name = $city['name'];
        }else{
            $city_name = "";
        }
        $language = $_POST['language_code'];
        $dob = $user->dob;
        $address = $user->address;
        $latitude = $user->latitude;
        $longitude = $user->longitude;

        $param_client['externalId'] = $external_id;
        $param_client['firstname'] = $first_name;
        $param_client['lastname'] = $last_name;
        $param_client['language'] = $language;
        $param_client['msisdn'] = $country_code.$mobile_number;
        $param_client['dob'] = $dob;
        $param_client['email'] = $email;
        $param_client['gender'] = "MALE"; //($gender=='MALE')?"M":"F";
        $param_client['active'] = "1";
        // $param_client['location.city'] = $city_name;
        // $param_client['location.street'] = $address;
        // $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
        // $param_client['location.country'] = "TZ";
        echo $client_json_request = json_encode($param_client);
        
        echo 'client/'.$client_id;
        $accounts = $this->selcomApiTest('client/'.$client_id, array(), '100', "PUT");
        print_r($accounts); */
        /* print_r($accounts);
        if(!empty($accounts)){
            $resultcode = $accounts['resultcode'];
            $result = $accounts['result'];
            if($resultcode == 200){
                $balance = $accounts['data'][0]['balance'];
                DB::table('user_accounts')->where('user_id', $user_id)->update(['quickrewards_balance' => $balance]);

                return $balance;
            }
        } */

        /* echo $qwikrewards_balance = $this->qwikrewardsBalance('124'); */
        //exit;

        /* $trans_id = time().rand(100000,999999);
        $external_id = rand(1000,9999).substr(time(), -7);
        $trans_param['externalId'] = $external_id;
        $trans_param['amount'] = '100';
        $trans_param['currency'] = 'TZS';
        $trans_param['serviceType'] = 'STASHTRANSFER';
        $trans_param['paymentReference'] = "3401100002994";
        $trans_param['utilityCode'] = 'ARA2STASH';
        $trans_param['categoryCode'] = 'General';
        $trans_param['category'] = 'NA';
        $trans_param['description'] = 'TZ';
        $trans_param['account'] = "3401000002987";
        $trans_json_request = json_encode($trans_param);
        $url = 'client/128/stash-transfer';
        $selcom_response = $this->selcomApiTest($url,$trans_json_request,'100');
        print_r($selcom_response);
        exit; */
        /* echo $this->araAvaBalance('132');
        exit; */
        /* $device = Devices::where('user_id','=','101')->first();
        if(!empty($device)){
            $device_type = $device['device_type'];
            if($device_type == 1){
                $device_type = 'Android';
            }else{
                $device_type = 'Iphone';
            }
            $device_token = $device['device_token'];
            $msg = "This is test message. Please ignore it.";
            $r = $this->sendPuchNotification($device_type,$device_token,$msg,$totalNotifications='0',$pushMessageText="");
            print_r($r);
            exit;
        }
        exit; */
        /* $mailData = [
            'full_name' => "Piyush",
            'email' => "piyush@moweb.com",
            'mobile' => "966396369",
            'description' => "This is testing message",
        ];

        $email = "piyush.prajapati@moweb.com";
        Mail::to($email)->send(new ContactUsEmail($mailData));
        exit; */

        $url = 'config/forex';
        $response_arr = $this->selcomApiTest($url, '', '', 'GET');
        //print_r($response_arr);
        /* exit; */

        $resultcode = $response_arr['resultcode'];
        $result = $response_arr['result'];
        if ($resultcode == '200' && $result == 'SUCCESS') {
            $arr = $response_arr['data'];
            foreach ($arr as $val) {
                $baseCurrency = $val['baseCurrency'];
                $otherCurrency = $val['otherCurrency'];
                $forexRate = $val['forexRate'];

                $qry = ForexRates::where('base_currency', $baseCurrency)->where('other_currency', $otherCurrency)->first();
                if (!empty($qry)) {
                    /* echo $qry['base_currency']."==".$qry['other_currency']."==".$forexRate;
                    echo "\n"; */
                    $id = $qry['id'];
                    $update = ForexRates::find($id);
                    $update->forex_rate = $forexRate;
                    $update->updated_at = date("Y-m-d H:i:s");
                    $update->save();
                } else {
                    $add = new ForexRates();
                    $add->forex_rate = $forexRate;
                    $add->base_currency = $baseCurrency;
                    $add->other_currency = $otherCurrency;
                    $add->created_at = date("Y-m-d H:i:s");
                    $add->updated_at = date("Y-m-d H:i:s");
                    $add->save();
                }
            }
        }

        print_r($response_arr);
        exit;
    }

    public function cronAraBalanceUpdate()
    {
        $users = User::select('id')->where('type', '=', 'user')->whereIn('user_status', [1, 3])->get();
        foreach ($users as $val) {
            $user_id = $val['id'];
            $this->araAvaBalance($user_id);
        }
    }

    public function cronStashBalanceUpdate()
    {
        $users = User::select('id')->where('type', '=', 'user')->whereIn('user_status', [1, 3])->get();
        foreach ($users as $val) {
            $user_id = $val['id'];
            $this->stashBalance($user_id);
        }
    }

    public function cronCurrencyBalanceUpdate()
    {
        $users = User::select('id')->where('type', '=', 'user')->whereIn('user_status', [1, 3])->get();
        foreach ($users as $val) {
            $user_id = $val['id'];
            $this->currencyAvaBalance($user_id);
        }
    }

    public function cronQwikrewardsBalanceUpdate()
    {
        $users = User::select('id')->where('type', '=', 'user')->whereIn('user_status', [1, 3])->get();
        foreach ($users as $val) {
            $user_id = $val['id'];
            $this->qwikrewardsBalance($user_id);
        }
    }
}