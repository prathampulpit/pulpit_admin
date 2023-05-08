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
use Illuminate\Support\Facades\Auth;
use Validator;
use Carbon\Carbon;
use App;
use DB;

class DigestController extends BaseController
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
        $this->user_id = $_POST['user_id'];
    }

    /**
     * Dashboard
     *
     * @return \Illuminate\Http\Response
     */
    public function digest(Request $request)
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

        $response = array();

        /**
         * Get ara account balance
         */
        $user_param['user_id'] = $this->user_id;
        $user_param['currency_id'] = '1';
        $account = $this->userAccountRepository->getUserBalance($user_param);
        $currency_symbol = $account[0]['currency_symbol'];

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
        $last_three_month = $this->transactionsRepository->getLastThreeMonthDigestByCategory($cat_req);

        $cat_pre_req['type'] = '1';
        $cat_pre_req['user_id'] = $this->user_id;
        $cat_pre_req['start_date'] = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
        $cat_pre_req['group_by'] = 'transactions.user_id';
        $cat_pre_req['order_by'] = 'total';
        $cat_pre_req['limit'] = '1';
        /* $cat_pre_month = $this->transactionsRepository->getMonthlyDigestByCategory($cat_pre_req); */
        $cat_pre_month = $this->transactionsRepository->getSingleDayDigestByCategory($cat_pre_req);
        $last_three_month_response = array();
        $i = 0;
        foreach ($last_three_month as $rec) {
            if (!empty($rec->total)) {
                $v1['avg_total_amount'] = (string)str_ireplace(",", "", number_format($rec->total / 3));
            } else {
                $v1['avg_total_amount'] = '0';
            }

            if ($cat_pre_month->isEmpty()) {
                $v1['pre_month_total_amount'] = (string)"0";
            } else {
                if (!empty($cat_pre_month[$i]->total)) {
                    $v1['pre_month_total_amount'] = (string)$cat_pre_month[$i]->total;
                } else {
                    $v1['pre_month_total_amount'] = (string)"0";
                }
            }
            $v1['category_name'] = $rec->name;
            $v1['category_id'] = (string)$rec->category_id;
            $last_three_month_response[] = $v1;
            $i++;
        }
        if (isset($v1['pre_month_total_amount']) && !empty($v1['pre_month_total_amount'])) {
            $response['info_message'] = "Yesterday you spent " . $currency_symbol . " " . number_format($v1['pre_month_total_amount']) . " which is well above your daily usual. You've also spent well over typical in Groceries so far this month";
        } else {
            $response['info_message'] = "Yesterday you spent " . $currency_symbol . " 0 which is well above your daily usual. You've also spent well over typical in Groceries so far this month";
        }

        $last_month = date("Y-m", strtotime("-1 month", strtotime(date("Y-m-d"))));
        $tot_days =  date("t", strtotime($last_month));
        $cat_by_req['type'] = '1';
        $cat_by_req['user_id'] = $this->user_id;
        $cat_by_req['start_date'] = $last_month;
        $cat_by_req['group_by'] = 'transactions.category_id';
        $cat_by_req['order_by'] = 'total';
        $cat_by_req['limit'] = '1';
        $cat_by_month = $this->transactionsRepository->getMonthlyDigestByCategory($cat_by_req);
        if (!$cat_by_month->isEmpty()) {
            $category_id = $cat_by_month[0]->category_id;
        } else {
            $category_id = 0;
            $v2['pre_day_total_amount'] = 0;
        }

        $single_req['user_id'] = $this->user_id;
        $single_req['category_id'] = $category_id;
        $single_req['start_date'] = date("Y-m-d", strtotime("-1 day", strtotime(date("Y-m-d"))));
        $single_req['group_by'] = 'transactions.category_id';
        $single_req['order_by'] = 'total';
        $single_req['limit'] = '1';
        $single_day_trans = $this->transactionsRepository->getSingleDayDigestByCategory($single_req);

        $j = 0;
        $pre_month_response = array();
        foreach ($cat_by_month as $rec1) {
            if (!empty($rec->total)) {
                $v2['avg_total_amount'] = (string)str_ireplace(",", "", number_format($rec1->total / $tot_days));
            } else {
                $v2['avg_total_amount'] = '0';
            }
            if ($single_day_trans->isEmpty()) {
                $v2['pre_day_total_amount'] = "0";
            } else {
                if (!empty($single_day_trans[$i]->total)) {
                    $v2['pre_day_total_amount'] = (string)$single_day_trans[$i]->total;
                } else {
                    $v2['pre_day_total_amount'] = "0";
                }
            }
            $v2['category_name'] = $rec1->name;
            $v2['category_id'] = (string)$rec1->category_id;
            $v2['message'] = "Yesterday you spent " . $currency_symbol . " " . number_format($v2['pre_day_total_amount']) . " which is well above your daily usual. You've also spent well over typical in Groceries so far this month";
            $pre_month_response[] = $v2;
            $j++;
        }

        $month = date("m");
        $year = date("Y");
        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);
        $end_time = strtotime("1 month", $start_time);

        $tot = 0;
        $json_data = array();
        for ($i = $start_time; $i < $end_time; $i += 86400) {
            $selected_date = date('Y-m-d', $i);
            $pre_first_date = date("Y-m-d", strtotime("-1 month", strtotime($selected_date)));

            $trans_req_first['user_id'] = $this->user_id;
            $trans_req_first['start_date'] = $pre_first_date;
            $trans_req_first['group_by'] = 'transactions.user_id';
            $trans_req_first['order_by'] = 'total';
            $pre_first_trans = $this->transactionsRepository->getSingleDayDigestByCategory($trans_req_first);
            if ($pre_first_trans->isEmpty()) {
                $first_trans_amount = "0";
            } else {
                $first_trans_amount = $pre_first_trans[0]->total;
            }

            $pre_second_month = date("Y-m-d", strtotime("-2 month", strtotime($selected_date)));
            $trans_req_second['user_id'] = $this->user_id;
            $trans_req_second['start_date'] = $pre_second_month;
            $trans_req_second['group_by'] = 'transactions.user_id';
            $trans_req_second['order_by'] = 'total';
            $pre_second_trans = $this->transactionsRepository->getSingleDayDigestByCategory($trans_req_second);
            if ($pre_second_trans->isEmpty()) {
                $second_trans_amount = "0";
            } else {
                $second_trans_amount = $pre_second_trans[0]->total;
            }

            $pre_third_month = date("Y-m-d", strtotime("-3 month", strtotime($selected_date)));
            $trans_req_third['user_id'] = $this->user_id;
            $trans_req_third['start_date'] = $pre_third_month;
            $trans_req_third['group_by'] = 'transactions.user_id';
            $trans_req_third['order_by'] = 'total';
            $pre_third_trans = $this->transactionsRepository->getSingleDayDigestByCategory($trans_req_third);
            if ($pre_third_trans->isEmpty()) {
                $third_trans_amount = "0";
            } else {
                $third_trans_amount = $pre_third_trans[0]->total;
            }

            $pre_month_amount = round(($first_trans_amount + $second_trans_amount + $third_trans_amount) / 3);
            $tot += $pre_month_amount;
            $d['amount'] = strval($tot);

            $d_arr = explode("-", $selected_date);
            $d['selected_date'] = $d_arr[2];
            $json_data[] = $d;
        }

        $month = date("m");
        $year = date("Y");
        //$start_date = "01-".$month."-".$year;
        $start_date = $year . "-" . $month . "-01";
        //$start_date = date("Y-m-d");
        $start_time = strtotime($start_date);
        /* $end_time = strtotime($start_time); */
        $end_time = strtotime(date("Y-m-d"));

        $total_amount = 0;
        $curr_json_data = array();
        //for($i=$start_time; $i<$end_time; $i+=86400){
        while (strtotime($start_date) <= strtotime(date("Y-m-d"))) {
            $curr_month_date = $start_date;
            $trans_req_first['user_id'] = $this->user_id;
            $trans_req_first['start_date'] = $curr_month_date;
            $trans_req_first['group_by'] = 'transactions.user_id';
            $trans_req_first['order_by'] = 'total';
            $trans = $this->transactionsRepository->getSingleDayDigestByCategory($trans_req_first);
            if ($trans->isEmpty()) {
                $curr_trans_amount = "0";
            } else {
                $curr_trans_amount = $trans[0]->total;
            }
            $total_amount += $curr_trans_amount;
            $d1['amount'] = strval($total_amount);
            $d_arr = explode("-", $curr_month_date);
            $d1['selected_date'] = $d_arr[2];
            $curr_json_data[] = $d1;
            $start_date = date("Y-m-d", strtotime("+1 day", strtotime($start_date)));
        }

        /* Category graph */
        $month = date("m");
        $year = date("Y");
        $start_date = "01-" . $month . "-" . $year;
        $start_time = strtotime($start_date);
        $end_time = strtotime("-3 month", $start_time);

        $cat_req['type'] = '1';
        $cat_req['user_id'] = $this->user_id;
        $cat_req['start_date'] = date("Y-m-d", $end_time);
        $cat_req['end_date'] = date("Y-m-d", strtotime(date("Y-m-d")));
        $cat_req['group_by'] = 'transactions.category_id';
        $cat_req['order_by'] = 'total';
        $cat_last_three_month = $this->transactionsRepository->getLastThreeMonthDigestByCategory($cat_req);
        $json_cat = array();
        if (!$cat_last_three_month->isEmpty()) {
            foreach ($cat_last_three_month as $val) {
                $category_id = $val->category_id;
                $amount = round($val->total / 3);

                $param['user_id'] = $this->user_id;
                $param['start_date'] = date("Y-m");
                $param['category_id'] = $category_id;
                $param['group_by'] = 'transactions.category_id';
                $param['order_by'] = 'total';
                $curr_month = $this->transactionsRepository->getMonthlyDigestByCategory($param);
                if (!$curr_month->isEmpty()) {
                    $curr_month_spending = $curr_month[0]->total;
                } else {
                    $curr_month_spending = "0";
                }
                if ($amount < $curr_month_spending) {
                    $dd['category_id'] = (string)$category_id;
                    $dd['category_name'] = $val->name;
                    $dd['icon'] = env('APP_URL') . '/storage/' . $category_path . "/" . $val->icon;
                    $dd['avg_total_amount'] = (string)$amount;
                    $dd['curr_total_amount'] = (string)$curr_month_spending;

                    $dd['info_message'] = "Yesterday you spent " . $currency_symbol . " " . number_format($dd['curr_total_amount']) . " which is well above your daily usual. You've also spent well over typical in Groceries so far this month";
                    $dd['info_message_title'] = "Good news for yesterday";
                    $json_cat[] = $dd;
                }
            }
        }
        /* End */


        /**
         * Get Weekly graph
         */
        $start_time_curr_week = strtotime("-6 day", strtotime(date("Y-m-d")));
        $end_time_curr_week = strtotime(date("Y-m-d"));

        $tot_amount = "0";
        $weekly_report = array();
        for ($i = $start_time_curr_week; $i <= $end_time_curr_week; $i += 86400) {
            $selected_date_curr_w = date('Y-m-d', $i);

            $req_curr_week['user_id'] = $this->user_id;
            $req_curr_week['start_date'] = $selected_date_curr_w;
            $req_curr_week['order_by'] = 'transactions.id';
            $req_curr_week['type'] = 'my_spend';
            $req_curr_week['trans_status'] = '1';
            $req_curr_week['order'] = 'DESC';
            $trans_res_curr = $this->transactionsRepository->getMonthlyDigestTranasctions($req_curr_week);
            $tot_amount = $trans_res_curr[0]['total_trans_amount'];
            if (empty($tot_amount)) {
                $tot_amount = 0;
            }

            $w['selected_date'] = $selected_date_curr_w;
            $w['selected_day'] = date('D', strtotime($selected_date_curr_w));
            $w['tot_amount'] = (string)$tot_amount;
            $weekly_report[] = $w;
        }

        $response['info_message_title'] = "Good news for yesterday";

        $response['monthly_graph'] = $last_three_month_response;
        $response['weekly_report'] = $weekly_report;
        $response['pre_month_graph'] = $pre_month_response;
        $response['monthly_spending_graph'] = $json_data;
        $response['current_month_spending_graph'] = $curr_json_data;
        $response['spike_category_graph'] = $json_cat;
        return $this->sendResponse('1', $response, trans('message.user_register'));
    }
}