<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use App\Repositories\UserRepository;
use App\Repositories\TransactionsRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\BillpaymentProductsRepository;
use Intervention\Image\Facades\Image;
use App\Models\User;
use App\Models\PullFunds;
use App\Models\Stashes;
use App\Models\Settings;
use App\Models\Categories;
use App\Models\BubbleTextMessages;
use App\Models\BubbleTextMessageDetails;
use App\Models\ContactSupports;
use App\Models\ReferFriends;
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use DB;
use URL;
use App\Jobs\UpdateBalance;

class DashboardController extends BaseController
{
    protected $userRepository;
    protected $transactionsRepository;
    protected $billpaymentProductsRepository;
    protected $userAccountRepository;

    public function __construct(
        UserRepository $userRepository,
        UserAccountRepository $userAccountRepository,
        BillpaymentProductsRepository $billpaymentProductsRepository,
        TransactionsRepository $transactionsRepository
    ) {
        $this->transactionsRepository = $transactionsRepository;
        $this->userAccountRepository = $userAccountRepository;
        $this->billpaymentProductsRepository = $billpaymentProductsRepository;
        $this->userRepository = $userRepository;
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");

        $this->user_id = "";
        if (isset($_POST['user_id'])) {
            $this->user_id = $_POST['user_id'];
        }
    }

    /**
     * Dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard(Request $request)
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

        $input = $request->all();
        $language_code = $input['language_code'];

        //dispatch(new UpdateBalance($this->user_id));
        /* $this->araAvaBalance($this->user_id);
        $this->stashBalance($this->user_id);
        $this->currencyAvaBalance($this->user_id);
        $qwikrewards_balance = $this->qwikrewardsBalance($this->user_id); */

        $response = array();
        $json_data = array();
        $params['user_id'] = $this->user_id;
        $users = $this->userRepository->getByParams($params);
        $address = $users[0]['address'];
        $referral_code = $users[0]['referral_code'];
        $mobile_number = $users[0]['country_code'] . $users[0]['mobile_number'];

        $login_step = $users[0]['login_step'];
        if ($login_step != '3') {
            return $this->sendError('0', trans('message.selfie_verification_pending'), array(), '200');
        }

        $d['title'] = 'Add Address';
        if (empty($address)) {
            $d['is_proccess_done'] = '0';
        } else {
            $d['is_proccess_done'] = '1';
        }
        $json_data[] = $d;

        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $accounts = $this->userAccountRepository->getUserBalance($user_param);
        if (!$accounts->isEmpty()) {
            $account_balance = $accounts[0]['account_balance'];
            $qwikrewards_balance = $accounts[0]['quickrewards_balance'];
        } else {
            $account_balance = "0.00";
            $qwikrewards_balance = "0.00";
        }

        $d1['title'] = 'Fund your account';
        if ($account_balance > 0) {
            $d1['is_proccess_done'] = '1';
        } else {
            $d1['is_proccess_done'] = '0';
        }

        $json_data[] = $d1;

        $d2['title'] = 'Add money to your stash';
        $stash = Stashes::where('user_id', '=', $this->user_id)->first();
        if (empty($stash)) {
            $d2['is_proccess_done'] = '0';
        } else {
            $d2['is_proccess_done'] = '1';
        }
        $json_data[] = $d2;

        $d3['title'] = 'Add money from another bank card';
        $pullfund = PullFunds::where('user_id', '=', $this->user_id)->first();
        if (!empty($pullfund)) {
            $d3['is_proccess_done'] = '1';
        } else {
            $d3['is_proccess_done'] = '0';
        }
        $json_data[] = $d3;


        $user_details = User::find($this->user_id);
        $client_id = $user_details['client_id'];
        $start_date = date("Y-m-d", strtotime($user_details['created_at']));

        //$start_date = "2020-01-01";
        $end_date = date("Y-m-d");

        $date1 = date_create($start_date);
        $date2 = date_create($end_date);
        $diff = date_diff($date1, $date2);
        $months = $diff->format("%m");

        $lastDayThisMonth = date('Y-m-d', strtotime('last day of previous month'));

        /* Average */
        $params['user_id'] = $this->user_id;
        $params['start_date'] = date("Y-m", strtotime($start_date));
        $params['end_date'] = date("Y-m", strtotime($lastDayThisMonth));
        $params['order_by'] = 'transactions.id';
        $params['type'] = 'typical_spend';
        $params['trans_status'] = '1';
        $params['order'] = 'DESC';
        $allTransactionArr = $this->transactionsRepository->getDashboardDigestTranasctions($params);
        $total_trans_amount = $allTransactionArr[0]['total_trans_amount'];
        if ($months == 0) {
            $typical_spend = number_format($total_trans_amount / 1);
        } else {
            $typical_spend = number_format($total_trans_amount / $months);
        }

        /* Today's */
        $params1['user_id'] = $this->user_id;
        $params1['start_date'] = date("Y-m", strtotime($end_date));
        $params1['order_by'] = 'transactions.id';
        $params1['type'] = 'my_spend';
        $params1['trans_status'] = '1';
        $params1['order'] = 'DESC';
        $trans = $this->transactionsRepository->getDashboardDigestTranasctions($params1);
        $trans_amount = $trans[0]['total_trans_amount'];
        $may_spend = (string)$trans_amount;
        if (empty($may_spend)) {
            $may_spend = "0";
        }

        $typical_spend = str_replace(",", "", $typical_spend);

        $max_amount = $typical_spend * 5;
        $max_amount = str_ireplace(",", "", $max_amount);

        if ($may_spend > $max_amount) {
            $max_amount = $may_spend;
        }

        $r['typical_spend'] = $typical_spend;
        $r['my_spend'] = $may_spend;
        $r['max_amount'] = $max_amount;
        $r['trans_datetime'] = strtoupper(date("Y-m-d", strtotime($end_date)));
        $json[] = $r;

        /**
         * Category graph
         */
        $category_path = config('custom.upload.category');
        //$req['type'] = '1';
        $req['group_by'] = 'transactions.category_id';
        $req['order_by'] = 'total';
        $req['trans_date'] = date("Y-m");
        $req['user_id'] = $this->user_id;
        $cat = $this->transactionsRepository->getAllTransByCategory($req);
        $cat_id = array();
        $cat_response1 = array();
        foreach ($cat as $val) {
            if (!empty($val->total)) {
                $r1['total_amount'] = (string)$val->total;
            } else {
                $r1['total_amount'] = '0';
            }
            $r1['category_name'] = $val->name;
            $r1['mcc_code'] = $val->mcc_code;
            $r1['icon'] = env('APP_URL') . '/storage/' . $category_path . "/" . $val->icon;
            $cat_id[] = $val->id;
            $cat_response1[] = $r1;
        }

        $cat_response_count = count($cat_response1);
        if ($cat_response_count < 5) {
            if (!empty($cat_id)) {
                $cat = Categories::whereNotIn('id', $cat_id)->limit(5)->get();
                $cat_ar = array();
                foreach ($cat as $rec) {
                    $c['total_amount'] = '0';
                    $c['category_name'] = $rec['name'];
                    $c['mcc_code'] = $rec['mcc_code'];
                    $c['icon'] = env('APP_URL') . '/storage/' . $category_path . "/" . $rec['icon'];
                    $cat_arr[] = $c;
                }
            } else {
                $cat = DB::table('categories')
                    ->limit(5)
                    ->get();
                $cat_ar = array();
                foreach ($cat as $rec) {
                    $c['total_amount'] = '0';
                    $c['category_name'] = $rec->name;
                    $c['mcc_code'] = $rec->mcc_code;
                    $c['icon'] = env('APP_URL') . '/storage/' . $category_path . "/" . $rec->icon;
                    $cat_arr[] = $c;
                }
            }

            $fianl_arr = array_merge($cat_response1, $cat_arr);
        } else {
            $fianl_arr = $cat_response1;
        }

        $cat_response_all = $this->asortReverse($fianl_arr, "total_amount");
        $cat_response = array_slice($cat_response_all, 0, 5);

        /**
         * Get pre monthly graph
         */
        $month = date("m");
        $year = date("Y");
        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);
        $end_time = strtotime("+1 month", $start_time);

        $current_month = array();
        /* for($i=$start_time; $i<$end_time; $i+=86400)
        {
            $selected_date = date('Y-m-d', $i);
            $req_month['user_id'] = $this->user_id;
            $req_month['start_date'] = $selected_date; //date("Y-m-d", strtotime($selected_date));
            $req_month['order_by'] = 'transactions.id';
            $req_month['type'] = 'my_spend';
            $req_month['trans_status'] = '1';
            $req_month['order'] = 'DESC';
            $trans_res = $this->transactionsRepository->getMonthlyDigestTranasctions($req_month);
            $trans_amount = $trans_res[0]['total_trans_amount'];
            if(empty($trans_amount)){
                $trans_amount = 0;
            }
            $j['selected_date'] = $selected_date;
            $j['my_spend'] = (string)$trans_amount;
            $current_month[] = $j;
        } */

        /**
         * Get current monthly graph
         */
        $current_month = date("m");
        if ($current_month == 1) {
            $month = 12;
            $year = date("Y", strtotime("-1 year"));
        } else {
            $month = $current_month - 1;
            $year = date("Y");
        }
        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);
        $end_time = strtotime("+1 month", $start_time);

        $tot = "0";
        $previous_month = array();
        for ($i = $start_time; $i < $end_time; $i += 86400) {
            $selected_date = date('Y-m-d', $i);
            $req_month['user_id'] = $this->user_id;
            $req_month['start_date'] = $selected_date; //date("Y-m-d", strtotime($selected_date));
            $req_month['order_by'] = 'transactions.id';
            $req_month['type'] = 'my_spend';
            $req_month['trans_status'] = '1';
            $req_month['order'] = 'DESC';
            $trans_res = $this->transactionsRepository->getMonthlyDigestTranasctions($req_month);
            $trans_amount = $trans_res[0]['total_trans_amount'];
            if (empty($trans_amount)) {
                $trans_amount = 0;
            }

            $tot += $trans_amount;
            $j['selected_date'] = $selected_date;
            $j['my_spend'] = (string)$tot;
            $previous_month[] = $j;
        }

        /**
         * Get pre monthly graph
         */
        $current_month = date("m");
        $year = date("Y");
        $start_date_curr = "01-" . $current_month . "-" . $year;
        $start_time_curr = strtotime($start_date_curr);
        /* $end_time_curr = strtotime("+1 month", $start_time_curr); */
        $end_time_curr = strtotime(date("Y-m-d"));

        $total_amount = "0";
        $current_month = array();
        for ($i = $start_time_curr; $i < $end_time_curr; $i += 86400) {
            $selected_date_curr = date('Y-m-d', $i);
            $req_curr['user_id'] = $this->user_id;
            $req_curr['start_date'] = $selected_date_curr; //date("Y-m-d", strtotime($selected_date));
            $req_curr['order_by'] = 'transactions.id';
            $req_curr['type'] = 'my_spend';
            $req_curr['trans_status'] = '1';
            $req_curr['order'] = 'DESC';
            $trans_res_curr = $this->transactionsRepository->getMonthlyDigestTranasctions($req_curr);
            $tot_amount = $trans_res_curr[0]['total_trans_amount'];
            if (empty($tot_amount)) {
                $tot_amount = 0;
            }

            $total_amount += $tot_amount;

            $k['selected_date'] = $selected_date_curr;
            $k['my_spend'] = (string)$total_amount;
            $current_month[] = $k;
        }

        /**
         * Digest category graph
         */
        $month = date("m");
        $year = date("Y");
        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);
        $end_time = strtotime("-3 month", $start_time);

        $category_path = config('custom.upload.category');
        $cat_req['type'] = '1';
        $cat_req['user_id'] = $this->user_id;
        $cat_req['start_date'] = date("Y-m-d", $end_time);
        $cat_req['end_date'] = date("Y-m-d", strtotime(date("Y-m-d")));
        $cat_req['group_by'] = 'transactions.user_id';
        $cat_req['order_by'] = 'total';
        $cat_req['limit'] = '1';
        $last_three_month = $this->transactionsRepository->getLastThreeMonthDigestByCategory($cat_req);

        $cat_pre_req['type'] = '1';
        $cat_pre_req['user_id'] = $this->user_id;
        $cat_pre_req['start_date'] = date("Y-m", strtotime("-1 month", strtotime(date("Y-m-d"))));
        $cat_pre_req['group_by'] = 'transactions.user_id';
        $cat_pre_req['order_by'] = 'total';
        $cat_pre_req['limit'] = '1';
        $cat_pre_month = $this->transactionsRepository->getMonthlyDigestByCategory($cat_pre_req);
        $last_three_month_response = array();
        $i = 0;
        foreach ($last_three_month as $rec) {
            if (!empty($rec->total)) {
                $v1['avg_total_amount'] = (string)str_ireplace(",", "", number_format($rec->total / 3));
            } else {
                $v1['avg_total_amount'] = '0';
            }

            if (!$cat_pre_month->isEmpty()) {
                $v1['pre_month_total_amount'] = $cat_pre_month[$i]->total;
            } else {
                $v1['pre_month_total_amount'] = '0';
            }
            $v1['category_name'] = $rec->name;
            $last_three_month_response[] = $v1;
            $i++;
        }

        $stash = Stashes::where('user_id', '=', $this->user_id)->first();
        if (empty($stash)) {
            $stash_balance = "0";
        } else {
            $stash_balance = $stash['stash_balance'];
        }
        $response['previous_month'] = $previous_month;
        $response['current_month'] = $current_month;
        $response['graph'] = $json;
        $response['tasklist'] = $json_data;
        $response['category_graph'] = $cat_response;
        $response['account_balance'] = number_format($account_balance, 2);
        $response['stash_balance'] = number_format($stash_balance, 2);
        $response['qwikrewards_balance'] = number_format($qwikrewards_balance, 2);

        $settings = Settings::find('1');
        if ($language_code == 'en') {
            $response['bubble_text'] = $settings['bubble_text'];
        } else {
            $response['bubble_text'] = $settings['bubble_text_sw'];
        }

        $texts = DB::table('bubble_text_messages')
            ->join('bubble_text_message_details', 'bubble_text_messages.id', '=', 'bubble_text_message_details.bubble_text_message_id')
            ->select('bubble_text_messages.*')
            ->where('user_id', $this->user_id)
            ->first();
        if (!empty($texts)) {
            $expiry_date = $texts->expiry_date;
            if ($expiry_date >= date("Y-m-d")) {
                $bubble_text_en = $texts->bubble_text_en;
                $bubble_text_sw = $texts->bubble_text_sw;
                if ($language_code == 'en') {
                    $response['bubble_text'] = $bubble_text_en;
                } else {
                    $response['bubble_text'] = $bubble_text_sw;
                }
            }
        }

        $response['digest_spike'] = $settings['digest_spike'];
        //$response['refer_friend_text'] = $settings['refer_friend_text'];   
        $response['refer_friend_text'] = str_replace("#CODE#", $referral_code, $settings['refer_friend_text']);

        $response['faq'] = URL::to('/') . '/cms/faq';

        /* Contact & support icon */
        $socialmedia_path = config('custom.upload.socialmedia');
        $contact_supports = ContactSupports::where('status', '=', '1')->orderBy('order_by', 'ASC')->get();
        $json_contact_supports = array();
        foreach ($contact_supports as $v) {
            $v11['id'] = (string)$v['id'];
            $v11['name'] = $v['name'];
            $v11['link'] = $v['link'];
            $v11['icon'] = URL::to('/') . '/storage/' . $socialmedia_path . "/" . $v['icon'];
            $json_contact_supports[] = $v11;
        }
        $response['contact_support_icons'] = $json_contact_supports;

        /* Selcom API call */
        $external_id = rand(1000, 9999) . time() . rand(1000, 9999);
        $param['externalId'] = $external_id;
        $json_request = json_encode($param);
        $check_client = $this->selcomApi('client/' . $client_id, $json_request, $this->user_id, "GET");

        $total_refer = ReferFriends::where('user_id', $this->user_id)->count();
        $remaining_refer_limit = $settings['maximum_referral_request_limit'] - $total_refer;

        //$response['remaining_refer_limit'] = $remaining_refer_limit;
        if ($check_client['resultcode'] == 200) {
            $selcom_data = $check_client['data'];
            if (isset($selcom_data[0]['referralsLeft'])) {
                $remaining_refer_limit = $selcom_data[0]['referralsLeft'];
            }
            $response['remaining_refer_limit'] = (int)$remaining_refer_limit;
        } else {
            $response['remaining_refer_limit'] = $remaining_refer_limit;
        }

        $maximum_referral_request_message = $settings['maximum_referral_request_message_' . $language_code];
        $maximum_referral_request_message = str_ireplace("#COUNT#", $remaining_refer_limit, $maximum_referral_request_message);
        $response['remaining_refer_message'] = $maximum_referral_request_message;

        $maximum_referral_request_message_en = $settings['maximum_referral_request_message_en'];
        $maximum_referral_request_message_en = str_ireplace("#COUNT#", $remaining_refer_limit, $maximum_referral_request_message_en);
        $response['remaining_refer_message_en'] = $maximum_referral_request_message_en;

        $maximum_referral_request_message_sw = $settings['maximum_referral_request_message_sw'];
        $maximum_referral_request_message_sw = str_ireplace("#COUNT#", $remaining_refer_limit, $maximum_referral_request_message_sw);
        $response['remaining_refer_message_sw'] = $maximum_referral_request_message_sw;

        return $this->sendResponse('1', $response, trans('message.user_register'));
    }
}