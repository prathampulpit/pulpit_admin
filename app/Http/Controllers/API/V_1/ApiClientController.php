<?php

namespace App\Http\Controllers\API\V_1;

use Illuminate\Http\Request;
use App\Http\Controllers\API\V_1\BaseController as BaseController;
use Aws\Rekognition\RekognitionClient;
use Aws\Textract\TextractClient;
use App\Repositories\UserRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\BillpaymentProductsRepository;
use App\Repositories\TopupsRepository;
use App\Models\Transactions;
use App\Models\AccountBalances;
use App\Models\Devices;
use App\Models\UserDebits;
use App\Models\UserCredits;
use App\Models\Qwikcashes;
use App\Models\TransactionLogs;
use App\Models\Notifications;
use App\Models\User;
use App\Models\Cities;
use App\Models\BillpaymentTransactions;
use App\Models\ApiLogs;
use App\Models\CardTransactions;
use App\Models\PullFunds;
use App\Models\Qwiksends;
use App\Models\Banks;
use App\Models\BubbleTextMessages;
use App\Models\BubbleTextMessageDetails;
use App\Models\LinkCards;
use App\Models\UserAccounts;
use Illuminate\Support\Facades\Auth;
use Validator;
use Storage;
use Carbon\Carbon;
use App;
use Illuminate\Support\Facades\DB;

class ApiClientController extends BaseController
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
    }

    /**
     * Bill Payment Transaction
     * 
     * @return json array
     */
    public function sendPushNotification(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "sendPushNotification - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'client_id' => 'required',
                    'notification_type' => 'required',
                    'notification_title' => 'required',
                    'notification_text' => 'required',
                    'data_object' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $apilog = new ApiLogs();
                $apilog->user_id = "0";
                $apilog->api_name = "sendPushNotification - API Client - Body";
                $apilog->request_data = $inputjson;
                $apilog->response_data = "NA";
                $apilog->created_at = date("Y-m-d H:i:s");
                $apilog->updated_at = date("Y-m-d H:i:s");
                $apilog->save();

                $client_id = $input['client_id'];
                $notification_type = $input['notification_type']; /* PROMOTIONAL, TRANSACTION */
                $notification_title = $input['notification_title'];
                $notification_text = $input['notification_text'];

                if ($notification_type == 'TRANSACTION' || $notification_type == 'TRANSACTIONAL') {

                    //$data_object = json_decode($input['data_object'],true);

                    $data_object = $input['data_object'];

                    $notify_user = 1;
                    $ara_receipt = 0;
                    $narration = "";
                    $currency = "TZS";
                    if (isset($data_object)) {
                        if (isset($data_object['notify_user'])) {
                            $notify_user = $data_object['notify_user'];
                        }

                        if (isset($data_object['ara_receipt'])) {
                            $ara_receipt = $data_object['ara_receipt'];
                        }

                        if (isset($data_object['narration'])) {
                            $narration = $data_object['narration'];
                        }

                        if (isset($data_object['currency'])) {
                            $currency = $data_object['currency'];
                        }
                    }

                    $trans_id = $data_object['trans_id'];

                    $check_trans = Transactions::where('trans_id', $trans_id)->first();
                    if (!empty($check_trans)) {
                        return $this->sendApiClientError('0', trans('message.trans_id_exit'), array(), '401');
                    }

                    $trans_amount = $data_object['trans_amount'];
                    $trans_amount = str_ireplace(",", "", $trans_amount);
                    $trans_amount_type = $data_object['trans_amount_type']; /* CREDIT, DEBIT */
                    $trans_type = $data_object['trans_type']; /* ATM, AGENT, BILLPAYMENT */
                    if (!isset($data_object['category_code']) && empty($data_object['category_code'])) {
                        $category_code = '';
                    } else {
                        $category_code = $data_object['category_code'];
                    }

                    if (!isset($data_object['utilitycode']) && empty($data_object['utilitycode'])) {
                        $utility_code = '';
                    } else {
                        $utility_code = $data_object['utilitycode'];
                    }

                    $account_number = $data_object['account_number'];
                    $trans_datetime = $data_object['trans_datetime'];
                    $prev_balance = $data_object['balance'];
                    $prev_balance = str_ireplace(",", "", $prev_balance);

                    $param_user['client_id'] = $client_id;
                    $user_details = $this->userRepository->getByParams($param_user);
                    if (empty($user_details)) {
                        return $this->sendApiClientError('0', trans('message.wrong_client_id'), array(), '400');
                    }
                    $this->user_id = $user_details[0]['user_id'];

                    $users = User::find($this->user_id);
                    $is_notification = $users->is_notification;

                    /* Update ara balance */
                    $useraccount = UserAccounts::where('user_id', '=', $this->user_id)->first();
                    $user_account_id = $useraccount->id;

                    DB::table('account_balances')->where('currency_id', '=', '1')->where('user_account_id', $user_account_id)->update(['account_balance' => $prev_balance]);

                    if ($trans_type == 'BILLPAYMENT') {
                        $param['category_code'] = $category_code;
                        $param['utilitycode'] = $utility_code;
                        $bill = $this->billpaymentProductsRepository->getCategoryProdDetails($param);
                        $bill_payment_product_id = $bill[0]['billpayment_product_id'];
                        $category_id = $bill[0]['category_id'];
                        $topup_label = $bill[0]['topup_label'];
                    } else {
                        $bill_payment_product_id = '0';
                        $category_id = '0';
                        $topup_label = '0';
                    }
                    $amount = str_replace(",", "", $trans_amount);

                    /**
                     * Store transaction request log details
                     */
                    $trans_log = new TransactionLogs();
                    $trans_log->trans_id = $trans_id;
                    $trans_log->user_id = $this->user_id;
                    $trans_log->transaction_response = $inputjson;
                    $trans_log->created_at = $this->datetime;
                    $trans_log->updated_at = $this->datetime;
                    $trans_log->save();

                    $notification_qry = new Notifications();
                    $notification_qry->user_id = $this->user_id;
                    $notification_qry->notification_type = 'transaction';
                    $notification_qry->notification_title = $notification_title;
                    $notification_qry->notification_text = $notification_text;
                    $notification_qry->data_object = json_encode($input['data_object']);
                    $notification_qry->save();

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

                    $msg = 'Your Ara account ' . $account_no . ' has been debited ' . $currency_symbol . ' ' . number_format($amount) . '. Updated balance: ' . $currency_symbol . ' ' . number_format($prev_balance - $amount);

                    $receipt = 'Account Number ' . $account_no . ' Reference ' . $trans_id . ' Amount ' . $currency_symbol . ' ' . number_format($trans_amount);

                    if ($trans_type == 'BILLPAYMENT') {
                        $type = '3';
                    } else if ($trans_type == 'WALLET2ARA' || $trans_type == 'PULLFUNDS') {
                        $type = '1';
                    } else if ($trans_type == 'WALLET2BANK' || $trans_type == 'TRANSFERS') {
                        $type = '4';
                    } else if ($trans_type == 'CARD') {
                        $type = '9';
                    } else if ($trans_type == 'ATM' || $trans_type == 'AGENT_DEPOSIT' || $trans_type == 'AGENT_WITHDRAW' || $trans_type == 'AGENT') {
                        $type = '5';
                    } else {
                        $type = '0';
                    }

                    $new_prev_balance = $prev_balance - $amount;

                    $original_externalId = '0';
                    if ($trans_type != 'MWALLET') {
                        $tid = '';
                        $t_type = '';
                        if (isset($data_object['original_externalId'])) {
                            $original_externalId = $data_object['original_externalId'];
                            if (!empty($original_externalId)) {
                                $check_trans = Transactions::where('trans_id', $original_externalId)->first();
                                if (!empty($check_trans)) {
                                    $tid = $check_trans['id'];
                                    $exiting_trans_type = $check_trans['trans_type'];
                                }
                            }
                        }

                        if ($trans_amount_type == 'CREDIT') {
                            $a_type = '1';
                        } else {
                            $a_type = '0';
                        }

                        if (!empty($tid)) {

                            $is_notification_text_change = '1';
                            if ($exiting_trans_type == '4') {
                                $qwiksend_trans = Qwiksends::where('trans_id', '=', $tid)->first();
                                if (!empty($qwiksend_trans)) {
                                    $qwiksend_trans_id = $qwiksend_trans['id'];
                                    $qwiksend_trans_type = $qwiksend_trans['type'];
                                    $to_user_id = $qwiksend_trans['to_user_id'];
                                    if ($qwiksend_trans_type == '1') {
                                        $op_users = User::find($to_user_id);
                                        $op_user_client_id = $op_users->client_id;
                                        if ($client_id == $op_user_client_id) {
                                            $qwiksend_trans_update = Qwiksends::find($qwiksend_trans_id);
                                            $qwiksend_trans_update->opposite_party_name = $narration;
                                            $qwiksend_trans_update->opposite_notification_text = $notification_text;
                                            $qwiksend_trans_update->save();

                                            $is_notification_text_change = '0';
                                        }
                                    }
                                }
                            }

                            $trans_model = Transactions::find($tid);
                            if (!empty($ara_receipt)) {
                                $trans_model->ara_receipt = $ara_receipt;
                            }

                            if ($is_notification_text_change == '1') {
                                if (!empty($narration)) {
                                    $trans_model->party_name = $narration;
                                }
                                $trans_model->notification_text = $notification_text;
                            }
                            $trans_model->selcom_trans_id = $trans_id;
                            $trans_model->trans_amount_type = $a_type;
                            $trans_model->currency = $currency;
                            $trans_model->updated_at = $this->datetime;
                            $trans_model->save();

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

                                    if ($notify_user == 1) {
                                        if (isset($data_object['notification_obj_type']) && !empty($data_object['notification_obj_type']) && isset($data_object['trans_status']) && isset($data_object['notification_obj_id'])) {

                                            if ($data_object['trans_status'] == '0') {
                                                $notification_obj_id = $data_object['notification_obj_id'];

                                                $linkcards = LinkCards::where('card_id', '=', $notification_obj_id)->first();
                                                if (!empty($linkcards)) {
                                                    $card_id = $linkcards->card_id;
                                                } else {
                                                    $card_id = $notification_obj_id;
                                                }

                                                $json_arr['type'] = 'ATM';
                                                $json_arr['card_id'] = $card_id;

                                                $notification_result = $this->sendPuchNotificationWithData($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title, $json_arr);
                                            } else {
                                                $notification_result = $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);
                                            }
                                        } else {
                                            $notification_result = $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);
                                        }

                                        $apilog_notify = new ApiLogs();
                                        $apilog_notify->user_id = $this->user_id;
                                        $apilog_notify->api_name = "Send Push Notification - Response";
                                        $apilog_notify->request_data = "NA";
                                        $apilog_notify->response_data = $notification_result;
                                        $apilog_notify->created_at = date("Y-m-d H:i:s");
                                        $apilog_notify->updated_at = date("Y-m-d H:i:s");
                                        $apilog_notify->save();
                                    }
                                }
                            }

                            $response = array();
                            return $this->sendApiClientResponse('1', $response, trans('message.transaction_notification_success'));
                        } else {

                            /**
                             * Add transaction details here
                             */
                            $transactions = new Transactions();
                            $transactions->user_id = $this->user_id;
                            $transactions->trans_id = $trans_id;
                            $transactions->ara_receipt = $ara_receipt;
                            $transactions->original_externalId = $original_externalId;
                            $transactions->party_name = $narration;
                            $transactions->currency = $currency;
                            $transactions->category_id = $category_id;
                            $transactions->trans_type = $type;
                            $transactions->trans_status = 1;
                            $transactions->prev_balance = $new_prev_balance;
                            $transactions->receipt = $notification_text;
                            $transactions->notification_text = $notification_text;
                            $transactions->is_outside = '1';
                            $transactions->trans_amount_type = $a_type;
                            $transactions->account_number = $account_number;
                            $transactions->trans_datetime = $this->datetime;
                            $transactions->created_at = $this->datetime;
                            $transactions->updated_at = $this->datetime;
                            $transactions->save();
                            if ($transactions->id > 0) {

                                if (!empty($utility_code) && $trans_type == 'BILLPAYMENT') {
                                    /**
                                     * Add bill payment transaction
                                     */
                                    $billpayment = new BillpaymentTransactions();
                                    $billpayment->user_id = $this->user_id;
                                    $billpayment->trans_id = $transactions->id;
                                    $billpayment->bill_payment_product_id = $bill_payment_product_id;
                                    $billpayment->trans_amount = $amount;
                                    $billpayment->topup_label = $topup_label;
                                    $billpayment->created_at = $this->datetime;
                                    $billpayment->updated_at = $this->datetime;
                                    $billpayment->save();
                                }

                                if ($trans_type == 'WALLET2ARA' || $trans_type == 'PULLFUNDS') {
                                    /**
                                     * Add Pull Fund data
                                     */
                                    $pullFund = new PullFunds();
                                    $pullFund->user_id = $this->user_id;
                                    $pullFund->trans_id = $transactions->id;
                                    $pullFund->card_id = "0";
                                    $pullFund->trans_amount = $amount;
                                    $pullFund->created_at = $this->datetime;
                                    $pullFund->updated_at = $this->datetime;
                                    $pullFund->save();
                                }

                                if ($trans_type == 'WALLET2BANK' || $trans_type == 'TRANSFERS') {
                                    if ($trans_type == 'WALLET2BANK') {
                                        $utilitycode = $input['utilitycode'];
                                        $bank = Banks::where('utility_code', $utilitycode)->first();
                                        if (!empty($bank)) {
                                            $bank_id = $bank['id'];
                                        } else {
                                            $bank_id = 0;
                                        }
                                        $qwiksend_type = '2';
                                    } else {
                                        $utilitycode = '';
                                        $qwiksend_type = '1';
                                        $bank_id = 0;
                                    }

                                    /**
                                     * Add quick send details
                                     */
                                    $qwikSends = new Qwiksends();
                                    $qwikSends->user_id = $this->user_id;
                                    $qwikSends->trans_id = $transactions->id;
                                    $qwikSends->bank_id = $bank_id;
                                    $qwikSends->type = $qwiksend_type;
                                    $qwikSends->category_id = 0;
                                    $qwikSends->trans_amount = $trans_amount;
                                    $qwikSends->notes = "";
                                    $qwikSends->created_at = $this->datetime;
                                    $qwikSends->updated_at = $this->datetime;
                                    $qwikSends->save();
                                }

                                if ($trans_type == 'CARD') {
                                    $card = new CardTransactions();
                                    $card->user_id = $this->user_id;
                                    $card->trans_id = $transactions->id;
                                    $card->trans_amount = $amount;
                                    $card->created_at = $this->datetime;
                                    $card->updated_at = $this->datetime;
                                    $card->save();
                                }

                                if ($trans_type == 'ATM' || $trans_type == 'AGENT' || $trans_type == 'AGENT_WITHDRAW' || $trans_type == 'AGENT_DEPOSIT') {
                                    if ($trans_type == 'ATM') {
                                        $payment_type = '1';
                                    } else if ($trans_type == 'AGENT' || $trans_type == 'AGENT_WITHDRAW') {
                                        $payment_type = '2';
                                    } else if ($trans_type == 'AGENT_DEPOSIT') {
                                        $payment_type = '3';
                                    }

                                    /**
                                     * Add quick send details
                                     */
                                    $qwikcash = new Qwikcashes();
                                    $qwikcash->user_id = $this->user_id;
                                    $qwikcash->trans_id = $transactions->id;
                                    $qwikcash->atm_token = "0";
                                    $qwikcash->agent_code = "0";
                                    $qwikcash->qwikcash_type = $payment_type;
                                    $qwikcash->trans_amount = $amount;
                                    $qwikcash->created_at = $this->datetime;
                                    $qwikcash->updated_at = $this->datetime;
                                    $qwikcash->save();
                                }

                                if ($trans_amount_type == 'DEBIT') {
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
                                }

                                if ($trans_amount_type == 'CREDIT') {
                                    /**
                                     * User credit
                                     */
                                    $credit = new UserCredits();
                                    $credit->user_id = $this->user_id;
                                    $credit->trans_id = $transactions->id;
                                    $credit->prev_balance = $prev_balance;
                                    $credit->trans_amount = $amount;
                                    $credit->created_at = $this->datetime;
                                    $credit->updated_at = $this->datetime;
                                    $credit->save();
                                }

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

                                        if ($notify_user == 1) {

                                            if (isset($data_object['notification_obj_type']) && !empty($data_object['notification_obj_type']) && isset($data_object['trans_status']) && isset($data_object['notification_obj_id'])) {
                                                if ($data_object['trans_status'] == '0') {
                                                    $notification_obj_id = $data_object['notification_obj_id'];

                                                    $linkcards = LinkCards::where('card_id', '=', $notification_obj_id)->first();
                                                    if (!empty($linkcards)) {
                                                        $card_id = $linkcards->card_id;
                                                    } else {
                                                        $card_id = $notification_obj_id;
                                                    }

                                                    $json_arr['type'] = 'ATM';
                                                    $json_arr['card_id'] = $card_id;

                                                    $notification_result = $this->sendPuchNotificationWithData($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title, $json_arr);
                                                } else {
                                                    $notification_result = $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);
                                                }
                                            } else {
                                                $notification_result = $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);
                                            }

                                            $apilog_notify = new ApiLogs();
                                            $apilog_notify->user_id = $this->user_id;
                                            $apilog_notify->api_name = "Send Push Notification - Response";
                                            $apilog_notify->request_data = "NA";
                                            $apilog_notify->response_data = $notification_result;
                                            $apilog_notify->created_at = date("Y-m-d H:i:s");
                                            $apilog_notify->updated_at = date("Y-m-d H:i:s");
                                            $apilog_notify->save();
                                        }
                                    }
                                }

                                $response = array();
                                return $this->sendApiClientResponse('1', $response, trans('message.transaction_notification_success'));
                            } else {
                                return $this->sendApiClientError('0', trans('message.selcom_api_error'), array(), '400');
                            }
                        }
                    } else {

                        $check_trans = Transactions::where('trans_id', $trans_id)->first();
                        if (empty($check_trans)) {
                            return $this->sendApiClientError('0', trans('message.trans_id_exit'), array(), '401');
                        }

                        $id = $check_trans['id'];
                        $transactions = Transactions::find($id);
                        $transactions->trans_status = 1;
                        if (!empty($ara_receipt)) {
                            $transactions->ara_receipt = $ara_receipt;
                        }

                        if (!empty($narration)) {
                            $transactions->party_name = $narration;
                        }

                        $transactions->selcom_trans_id = $trans_id;
                        $transactions->notification_text = $notification_text;
                        $transactions->updated_at = $this->datetime;
                        $transactions->save();

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

                                if ($notify_user == 1) {
                                    $notification_result = $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);

                                    $apilog_notify = new ApiLogs();
                                    $apilog_notify->user_id = $this->user_id;
                                    $apilog_notify->api_name = "Send Push Notification - Response";
                                    $apilog_notify->request_data = "NA";
                                    $apilog_notify->response_data = $notification_result;
                                    $apilog_notify->created_at = date("Y-m-d H:i:s");
                                    $apilog_notify->updated_at = date("Y-m-d H:i:s");
                                    $apilog_notify->save();
                                }
                            }
                        }
                    }
                } else {
                    $param_user['client_id'] = $client_id;
                    $user_details = $this->userRepository->getByParams($param_user);
                    if (empty($user_details)) {
                        return $this->sendApiClientError('0', trans('message.wrong_client_id'), array(), '400');
                    }
                    $this->user_id = $user_details[0]['user_id'];

                    $users = User::find($this->user_id);
                    $is_notification = $users->is_notification;

                    /**
                     * Store transaction request log details
                     */
                    $trans_log = new TransactionLogs();
                    $trans_log->trans_id = '0';
                    $trans_log->user_id = '0';
                    $trans_log->transaction_response = $inputjson;
                    $trans_log->created_at = $this->datetime;
                    $trans_log->updated_at = $this->datetime;
                    $trans_log->save();

                    /**
                     * Submit push notification details
                     */
                    $users = User::where('register_step', '=', '6')->where('id', '=', $this->user_id)->get();
                    foreach ($users as $val) {
                        $user_id = $val['id'];
                        $notification_qry = new Notifications();
                        $notification_qry->user_id = $user_id;
                        $notification_qry->notification_type = 'promotional';
                        $notification_qry->notification_title = $notification_title;
                        $notification_qry->notification_text = $notification_text;
                        $notification_qry->data_object = "NA";
                        $notification_qry->save();
                    }
                    $response = array();

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

                            //if($notify_user =='1'){
                            $this->sendPuchNotification($device_type, $device_token, $notification_text, $totalNotifications = '0', $pushMessageText = "", $notification_title);
                            //}
                        }
                    }
                    return $this->sendApiClientResponse('1', $response, trans('message.promotional_notification_success'));
                }
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function pinHash(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "pinHash - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'random_string' => 'required',
                    'msisdn' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $apilog = new ApiLogs();
                $apilog->user_id = "0";
                $apilog->api_name = "pinHash - API Client - Body";
                $apilog->request_data = $inputjson;
                $apilog->response_data = "NA";
                $apilog->created_at = date("Y-m-d H:i:s");
                $apilog->updated_at = date("Y-m-d H:i:s");
                $apilog->save();

                if (isset($input['current_language']) && !empty($input['current_language'])) {
                    App::setLocale($input['current_language']);
                }

                $random_string = $input['random_string'];
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $login_attempt = $users['login_attempt'];
                if ($login_attempt == 3) {
                    return $this->sendApiClientError('3', trans('message.wrong_pin_with_block'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                if ($users['is_temporary_pin'] == '1') {
                    $is_temporary_pin = $users['is_temporary_pin'];
                    $temporary_pin_created_date = $users['temporary_pin_created_date'];
                    if ($is_temporary_pin == 1) {
                        $t1 = strtotime(date("Y-m-d H:i:s"));
                        $t2 = strtotime($temporary_pin_created_date);
                        $diff = $t1 - $t2;
                        $hours = $diff / (60 * 60);
                        if ($hours >= 1) {
                            return $this->sendApiClientError('2', trans('message.temp_pin_expired'), array(), '400');
                        }
                    }
                    $is_temporary_pin = $users['is_temporary_pin'];
                } else {
                    $is_temporary_pin = $users['is_temporary_pin'];
                }

                /**
                 * Get ara account balance
                 */
                $user_param['user_id'] = $users['id'];
                $user_param['currency_id'] = '1';
                $account = $this->userAccountRepository->getUserBalance($user_param);
                $account_number = $account[0]['account_number'];

                $login_pin = $users['login_pin'];
                $salt = $users['salt'];
                $current_language = $users['current_language'];
                $first_name = $users['first_name'];
                $last_name = $users['last_name'];
                $client_id = $users['client_id'];

                $response = array();
                $d['salt'] = $salt;
                $d['hash'] = hash('sha256',  $login_pin . $random_string);
                $d['is_temporary_pin'] = $is_temporary_pin;
                $d['current_language'] = $current_language;
                $d['first_name'] = $first_name;
                $d['last_name'] = $last_name;
                $d['account_number'] = $account_number;
                $d['client_id'] = $client_id;
                $response[] = $d;

                return $this->sendApiClientResponse('1', $response, trans('message.pin_hash'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function register(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "Register - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $inputjson = json_encode($input);
        $apilog_boday = new ApiLogs();
        $apilog_boday->user_id = "0";
        $apilog_boday->api_name = "Register - API Client - Body";
        $apilog_boday->request_data = $inputjson;
        $apilog_boday->response_data = "NA";
        $apilog_boday->created_at = date("Y-m-d H:i:s");
        $apilog_boday->updated_at = date("Y-m-d H:i:s");
        $apilog_boday->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'mobile_number' => 'required',
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required',
                    'dob' => 'required',
                    'document_number' => 'required',
                    'document_id' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $mobile_number = $input['mobile_number'];
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

                $salt = time() . rand('111', '999');

                $country_code = $input['country_code'];
                $mobile_number = $mobile_number;
                $first_name = $input['first_name'];
                $last_name = $input['last_name'];
                $email = $input['email'];
                $login_pin = rand('111111', '999999'); //$input['login_pin'];
                $dob = $input['dob'];
                $document_number = $input['document_number'];
                $document_id = $input['document_id'];
                $document_file = $input['document_file'];
                $profile_pic = $input['profile_pic'];
                $resident_permit = $input['resident_permit'];
                $work_permit = $input['work_permit'];
                $address_proof = $input['address_proof'];
                $address = $input['address'];
                $city = $input['city'];
                $latitude = $input['latitude'];
                $longitude = $input['longitude'];
                $current_language = strtolower($input['current_language']);
                if (isset($input['gender'])) {
                    $gender = $input['gender'];
                } else {
                    $gender = 'MALE';
                }
                $input['language_code'] = $current_language;

                $cities = Cities::where('name', $city)->first();
                if (!empty($cities)) {
                    $city_id = $cities['id'];
                } else {
                    $city_id = '';
                }
                $input['city_id'] = $city_id;

                $input['referral_code'] = "";
                $input['login_pin'] = $login_pin;

                if (isset($input['agent_id'])) {
                    $agent_id = $input['agent_id'];
                } else {
                    $agent_id = '';
                }

                if (isset($input['agent_name'])) {
                    $agent_name = $input['agent_name'];
                } else {
                    $agent_name = '';
                }

                if (isset($input['agent'])) {
                    $agent = $input['agent'];
                } else {
                    $agent = '';
                }

                $users = User::where('mobile_number', $mobile_number)->first();
                if (!empty($users)) {
                    $register_step = $users['register_step'];
                    if ($register_step == '6') {
                        $user_id = "";
                        return $this->sendApiClientError('0', trans('message.user_exit'), array(), '400');
                    } else {
                        $user_id = $users['id'];
                    }
                } else {
                    $user_id = "";
                }

                if ($user_id != "") {
                    $check_email = User::where('email', $email)->where('id', '!=', $user_id)->first();
                } else {
                    $check_email = User::where('email', $email)->first();
                }
                if (!empty($check_email)) {
                    return $this->sendApiClientError('0', trans('message.email_exit'), array(), '400');
                }

                if (!empty($document_file)) {
                    $document_imagename = time() . rand('111', '999') . '.png';
                    $document_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $document_file));
                    $document_file_path = 'documents/' . $document_imagename;
                    $r = Storage::disk('s3')->put($document_file_path, $document_image);
                }

                if (!empty($profile_pic)) {
                    $profile_imagename = time() . rand('111', '999') . '.png';
                    $profile_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $profile_pic));
                    $profile_file_path = 'user/' . $profile_imagename;
                    $r = Storage::disk('s3')->put($profile_file_path, $profile_image);

                    /* Selfie upload */
                    $selfie_imagename = time() . rand('111', '999') . '.png';
                    $selfie_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $profile_pic));
                    $selfie_file_path = 'user/' . $selfie_imagename;
                    $r = Storage::disk('s3')->put($selfie_file_path, $selfie_image);
                    /* End */
                }

                if (!empty($resident_permit)) {
                    $resident_imagename = time() . rand('111', '999') . '.png';
                    $resident_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $resident_permit));
                    $resident_file_path = 'documents/' . $resident_imagename;
                    $r = Storage::disk('s3')->put($resident_file_path, $resident_image);
                }

                if ($user_id == "") {
                    $user = new User();
                } else {
                    $user = User::find($user_id);
                }
                $register_step = 6;
                $user->mobile_number = $mobile_number;
                $user->country_code = $country_code;
                $user->first_name = $first_name;
                $user->last_name = $last_name;
                $user->name = $first_name . ' ' . $last_name;
                $user->email = $email;

                $user->dob = $dob;
                $user->login_pin = hash('sha256',  $login_pin . $salt);
                $user->salt = $salt;
                $user->address = $address;
                $user->gender = $gender;
                $user->document_number = $document_number;
                $user->document_id = $document_id;
                if (!empty($document_file)) {
                    $user->document_file_name = $document_imagename;
                }
                if (!empty($profile_pic)) {
                    $user->profile_picture = $profile_imagename;
                }
                if (!empty($profile_pic)) {
                    $user->selfie_picture = $selfie_imagename;
                }
                if (!empty($resident_permit)) {
                    $user->resident_permit = $resident_imagename;
                }
                $user->city_id = $city_id;
                $user->current_language = $current_language;
                $user->ussd_enable = '1';
                $user->is_temporary_pin = '1';
                $user->register_step = '6';
                $user->is_ara_lite = '1';
                $user->nationality_id = '1';
                $user->temporary_pin_created_date = date("Y-m-d H:i:s");
                $user->created_at = date("Y-m-d H:i:s");
                $user->updated_at = date("Y-m-d H:i:s");
                $user->agent_id = $agent_id;
                $user->agent_name = $agent_name;
                $user->agent = $agent;
                $user->save();

                if ($user_id == "") {
                    $user_id = $user->id;
                }

                $final_response = $this->selcomOnboardingApisAraLite($user_id, $input);
                if (!empty($final_response)) {
                    if ($final_response['resultcode'] == '200' || $final_response['resultcode'] == '000') {

                        /* $model = User::find($user_id);
                        $client_id = $model->client_id;

                        $response = array();
                        $d['temporary_pin'] = $client_id.'_'.$login_pin;
                        $response[] = $d; */

                        $model = User::find($user_id);
                        $model->user_status = '3';
                        $model->save();

                        return $this->sendApiClientResponse('1', array(), trans('message.complete_register'));
                    } else {
                        $error_message = $final_response['message'];
                        return $this->sendApiClientError('0', $error_message, array(), '400');
                    }
                } else {
                    //return $this->sendError('0', trans('message.selcom_api_error'), array(), '200');
                    return $this->sendApiClientError('0', trans('message.selcom_api_error'), array(), '400');
                }
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function updateDocument(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "UpdateDocument - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $inputjson = json_encode($input);
        $apilog_boday = new ApiLogs();
        $apilog_boday->user_id = "0";
        $apilog_boday->api_name = "UpdateDocument - API Client - Body";
        $apilog_boday->request_data = $inputjson;
        $apilog_boday->response_data = "NA";
        $apilog_boday->created_at = date("Y-m-d H:i:s");
        $apilog_boday->updated_at = date("Y-m-d H:i:s");
        $apilog_boday->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                    'filename' => 'required',
                    'file_type' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);
                $filename = $input['filename'];
                $file_type = $input['file_type']; //selfie, id_card
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }
                $user_id = $users['id'];
                /* $profile_imagename = time().rand('111','999').'.png';
                $profile_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '',$filename));
                $profile_file_path = 'user/'.$profile_imagename;
                $r = Storage::disk('s3')->put($profile_file_path, $profile_image); */

                /* Selfie upload */
                if ($file_type == 'selfie') {
                    $selfie_imagename = time() . rand('111', '999') . '.png';
                    $selfie_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $filename));
                    $selfie_file_path = 'user/' . $selfie_imagename;
                    $r = Storage::disk('s3')->put($selfie_file_path, $selfie_image);
                }
                /* End */

                if ($file_type == 'id_card') {
                    $document_imagename = time() . rand('111', '999') . '.png';
                    $document_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $filename));
                    $document_file_path = 'documents/' . $document_imagename;
                    $r = Storage::disk('s3')->put($document_file_path, $document_image);
                }

                $model = User::find($user_id);
                if ($file_type == 'id_card') {
                    if (!empty($filename)) {
                        $model->document_file_name = $document_imagename;
                    }
                }

                if ($file_type == 'selfie') {
                    if (!empty($filename)) {
                        $model->selfie_picture = $selfie_imagename;
                    }
                }
                $model->save();

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.document_upload_success'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function changePinHash(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "ChanhePinHash - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $inputjson = json_encode($input);
        $apilog_body = new ApiLogs();
        $apilog_body->user_id = "0";
        $apilog_body->api_name = "ChanhePinHash - API Client - Body";
        $apilog_body->request_data = $inputjson;
        $apilog_body->response_data = "NA";
        $apilog_body->created_at = date("Y-m-d H:i:s");
        $apilog_body->updated_at = date("Y-m-d H:i:s");
        $apilog_body->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                    'old_pin' => 'required',
                    'new_pin' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                /* $random_string = $input['random_string']; */
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $is_temporary_pin = $users['is_temporary_pin'];
                $temporary_pin_created_date = $users['temporary_pin_created_date'];
                if ($is_temporary_pin == 1) {
                    $t1 = strtotime(date("Y-m-d H:i:s"));
                    $t2 = strtotime($temporary_pin_created_date);
                    $diff = $t1 - $t2;
                    $hours = $diff / (60 * 60);
                    if ($hours >= 1) {
                        return $this->sendApiClientError('2', trans('message.temp_pin_expired'), array(), '400');
                    }
                }

                $login_attempt = $users['login_attempt'];
                if ($login_attempt == 3) {
                    return $this->sendApiClientError('3', trans('message.wrong_pin_with_block'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                $user_id = $users['id'];
                $oldsalt = $users['salt'];
                $old_pin = $input['old_pin'];
                $old_login_pin = hash('sha256',  $old_pin . $oldsalt);

                $checkpin = User::where('login_pin', $old_login_pin)->first();
                if (empty($checkpin)) {
                    return $this->sendApiClientError('0', trans('message.login_pin_not_match'), array(), '400');
                }

                $salt = time() . rand('111', '999');
                $login_pin = $input['new_pin'];

                $model = User::find($user_id);
                $model->salt = $salt;
                $model->login_pin = hash('sha256',  $login_pin . $salt);
                $model->is_temporary_pin = '0';
                $model->save();

                $response = array();
                /* $d['salt'] = $salt;
                $d['hash'] = hash('sha256',  $login_pin . $random_string);
                $response[] = $d; */

                return $this->sendApiClientResponse('1', $response, trans('message.login_pin_change'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function blockProfile(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "blockProfile - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $apilog = new ApiLogs();
                $apilog->user_id = "0";
                $apilog->api_name = "blockProfile - API Client - Body";
                $apilog->request_data = $inputjson;
                $apilog->response_data = "NA";
                $apilog->created_at = date("Y-m-d H:i:s");
                $apilog->updated_at = date("Y-m-d H:i:s");
                $apilog->save();

                /* $random_string = $input['random_string']; */
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                $user_id = $users['id'];
                $model = User::find($user_id);
                $model->login_attempt = 3;
                $model->login_attempt_datetime = date("Y-m-d H:i:s");
                $model->save();

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.wrong_pin_with_block'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function changeCustomerLanguage(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "changeCustomerLanguage - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $inputjson = json_encode($input);
        $apilog_body = new ApiLogs();
        $apilog_body->user_id = "0";
        $apilog_body->api_name = "changeCustomerLanguage - API Client - Body";
        $apilog_body->request_data = $inputjson;
        $apilog_body->response_data = "NA";
        $apilog_body->created_at = date("Y-m-d H:i:s");
        $apilog_body->updated_at = date("Y-m-d H:i:s");
        $apilog_body->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                    'current_language' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                /* $random_string = $input['random_string']; */
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                $city_id = $users['city_id'];
                $cities = Cities::where('id', $city_id)->first();
                if (!empty($cities)) {
                    $city_name = $cities['name'];
                } else {
                    $city_name = '';
                }

                $user_id = $users['id'];
                $first_name = $users['first_name'];
                $last_name = $users['last_name'];

                $language = $input['current_language'];
                if ($input['current_language'] == '1') {
                    $language = "en";
                } else if ($input['current_language'] == '2') {
                    $language = "sw";
                }
                App::setLocale($language);

                //$language = $users['current_language'];
                $msisdn = $users['country_code'] . "" . $users['mobile_number'];
                $dob = $users['dob'];
                $email = $users['email'];
                $gender = $users['gender'];
                $address = $users['address'];
                $latitude = $users['latitude'];
                $longitude = $users['longitude'];
                $client_id = $users['client_id'];

                $external_id = rand(1000, 9999) . substr(time(), -7);
                $param_client['externalId'] = $external_id;
                $param_client['firstname'] = $first_name;
                $param_client['lastname'] = $last_name;
                $param_client['language'] = $language;
                $param_client['msisdn'] = $msisdn;
                $param_client['dob'] = $dob;
                $param_client['email'] = $email;
                $param_client['gender'] = $gender; //($gender=='MALE')?"M":"F";
                $param_client['active'] = "1";
                $param_client['location.city'] = $city_name;
                $param_client['location.street'] = $address;
                $param_client['location.gpsCoordinates'] = "$latitude,$longitude";
                $param_client['location.country'] = "TZ";
                $param_client['notes'] = "ara lite change language";
                $client_json_request = json_encode($param_client);
                $accounts = $this->selcomApi('client/' . $client_id, $client_json_request, $user_id, "PUT");
                $this->selcomApiRequestResponse($user_id, 'client/' . $client_id, $client_json_request, json_encode($accounts));

                $user_id = $users['id'];
                $model = User::find($user_id);
                $model->current_language = strtolower($language);
                $model->save();

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.current_language_change'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function resetPin(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "resetPin - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $input = $request->all();
        $inputjson = json_encode($input);
        $apilog_body = new ApiLogs();
        $apilog_body->user_id = "0";
        $apilog_body->api_name = "resetPin - API Client - Body";
        $apilog_body->request_data = $inputjson;
        $apilog_body->response_data = "NA";
        $apilog_body->created_at = date("Y-m-d H:i:s");
        $apilog_body->updated_at = date("Y-m-d H:i:s");
        $apilog_body->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                    'selfie_image' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $selfie_image = $input['selfie_image'];

                /* if (preg_match('/base64/', $selfie_image)) {
                }else{
                    return $this->sendApiClientError('0', trans('message.valid_base64_img'), array(), '400');
                } */

                /* $random_string = $input['random_string']; */
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                $doc_file_path = 'user/' . $users['selfie_picture'];
                //$doc_file_path = 'documents/1141601358351crop_8471.jpeg';
                $client_rekognition = new RekognitionClient([
                    'region'    => env('AWS_DEFAULT_REGION'),
                    'version'   => 'latest'
                ]);

                $selfie_image = $input['selfie_image'];
                $selfie_imagename = 'selfie_ara_lite' . time() . rand('111', '999') . '.png';
                $selfie_image = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $selfie_image));
                $selfie_file_path = 'documents/' . $selfie_imagename;
                $r = Storage::disk('s3')->put($selfie_file_path, $selfie_image);

                $com_face_result = $client_rekognition->compareFaces([
                    'SourceImage' => [
                        'S3Object' => [
                            'Bucket' => env('AWS_BUCKET'),
                            'Name' => $doc_file_path
                        ]
                    ],
                    'TargetImage' => [
                        'S3Object' => [
                            'Bucket' => env('AWS_BUCKET'),
                            'Name' => $selfie_file_path
                        ]
                    ]
                ]);

                $results = $com_face_result->toArray();

                if (empty($results)) {
                    $similarity = 0;
                } else {
                    $face_matches = $results['FaceMatches'];
                    if (empty($face_matches)) {
                        $similarity = 0;
                    } else {
                        $similarity = $face_matches[0]['Similarity'];
                    }
                }

                Storage::disk('s3')->delete($selfie_file_path);

                if ($similarity < 90) {
                    return $this->sendApiClientError('0', trans('message.selfie_failed'), array(), '400');
                }

                $user_id = $users['id'];
                $client_id = $users['client_id'];
                $ussd_enable = $users['ussd_enable'];
                $language = $users['current_language'];
                $mobile_number = $users['mobile_number'];

                $login_pin = rand('111111', '999999');
                $salt = time() . rand('111', '999');

                $msisdn = '255' . $mobile_number;
                $param_client['msisdn'] = $msisdn;
                $param_client['language'] = $language;
                $param_client['temporaryPIN'] = $login_pin;
                $client_json_request = json_encode($param_client);
                $accounts = $this->selcomApi('client/' . $client_id . '/aralite-reset-pin', $client_json_request, $user_id, "POST");
                $this->selcomApiRequestResponse($user_id, 'client/' . $client_id . '/aralite-reset-pin', $client_json_request, json_encode($accounts));

                $model = User::find($user_id);
                $model->login_pin = hash('sha256',  $login_pin . $salt);
                $model->salt = $salt;
                $model->is_temporary_pin = '1';
                $model->temporary_pin_created_date = date("Y-m-d H:i:s");
                $model->save();

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.document_vefify_success'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    public function setPin(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "SetPin - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'msisdn' => 'required',
                    'pin' => 'required',
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $apilog = new ApiLogs();
                $apilog->user_id = "0";
                $apilog->api_name = "SetPin - API Client - Body";
                $apilog->request_data = $inputjson;
                $apilog->response_data = "NA";
                $apilog->created_at = date("Y-m-d H:i:s");
                $apilog->updated_at = date("Y-m-d H:i:s");
                $apilog->save();

                /* $random_string = $input['random_string']; */
                $msisdn = $input['msisdn'];
                $check_str_length = strlen($msisdn);
                if ($check_str_length > 12) {
                    $msisdn = $msisdn;
                } else {
                    if ($check_str_length == '10') {
                        $msisdn = ltrim($msisdn, '0');
                    } else if ($check_str_length == '12') {
                        $msisdn = substr($msisdn, 3);
                    }
                }

                $users = User::where('mobile_number', $msisdn)->first();
                if (empty($users)) {
                    return $this->sendApiClientError('5', trans('message.user_not_register'), array(), '400');
                }

                $is_temporary_pin = $users['is_temporary_pin'];
                $temporary_pin_created_date = $users['temporary_pin_created_date'];
                if ($is_temporary_pin == 0) {
                    return $this->sendApiClientError('6', trans('message.temp_pin_change'), array(), '400');
                }

                $login_attempt = $users['login_attempt'];
                if ($login_attempt == 3) {
                    return $this->sendApiClientError('3', trans('message.wrong_pin_with_block'), array(), '400');
                }

                $ussd_enable = $users['ussd_enable'];
                if ($ussd_enable == 0) {
                    return $this->sendApiClientError('4', trans('message.ussd_disble'), array(), '400');
                }

                $user_id = $users['id'];

                $salt = time() . rand('111', '999');
                $login_pin = $input['pin'];

                $model = User::find($user_id);
                $model->salt = $salt;
                $model->login_pin = hash('sha256',  $login_pin . $salt);
                $model->is_temporary_pin = '0';
                //$model->temporary_pin_created_date = date("Y-m-d H:i:s");
                $model->save();

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.login_pin_change'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    /**
     * Add bubble txt
     * 
     * @return json array
     */
    public function addBubbleText(Request $request)
    {
        $headers = apache_request_headers();

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "Add BubbleText - API Client - Header";
        $apilog->request_data = json_encode($headers);
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        if (!empty($headers['api_key']) && !empty($headers['digest']) && !empty($headers['request_timestamp'])) {
            $api_key = $headers['api_key'];
            $digest = $headers['digest'];
            $request_timestamp = $headers['request_timestamp'];

            /* Generate digest from our end */
            $api_digest = md5($request_timestamp . env('API_SECRET')) . sha1(sha1($request_timestamp . env('API_KEY') . env('API_SECRET'), true));

            if ($api_digest != $digest) {
                return $this->sendApiClientError('0', trans('message.auth_fail'), array(), '401');
            }
            /* End */

            if ($api_key == env('API_KEY')) {
                $validator = Validator::make($request->all(), [
                    'bubble_text_en' => 'required',
                    'bubble_text_sw' => 'required',
                    'expiry_date' => 'required',
                    'client_id' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
                }

                $input = $request->all();
                $inputjson = json_encode($input);

                $apilog = new ApiLogs();
                $apilog->user_id = "0";
                $apilog->api_name = "Add BubbleText - API Client - Body";
                $apilog->request_data = $inputjson;
                $apilog->response_data = "NA";
                $apilog->created_at = date("Y-m-d H:i:s");
                $apilog->updated_at = date("Y-m-d H:i:s");
                $apilog->save();

                $client_id = $input['client_id'];
                $bubble_text_en = $input['bubble_text_en'];
                $bubble_text_sw = $input['bubble_text_sw'];
                $expiry_date = $input['expiry_date'];

                $insert = new BubbleTextMessages();
                $insert->bubble_text_en = $bubble_text_en;
                $insert->bubble_text_sw = $bubble_text_sw;
                $insert->expiry_date = $expiry_date;
                $insert->status = '1';
                $insert->created_at = date("Y-m-d H:i:s");
                $insert->updated_at = date("Y-m-d H:i:s");
                $insert->save();

                $message_id = $insert->id;

                $client_id_arr = explode(",", $client_id);

                foreach ($client_id_arr as $val) {
                    $users = User::where('client_id', '=', $val)->first();
                    $user_id = $users['id'];

                    $model = BubbleTextMessageDetails::where('user_id', '=', $user_id)->first();
                    if (empty($model)) {
                        $insert = new BubbleTextMessageDetails();
                        $insert->user_id = $user_id;
                        $insert->bubble_text_message_id = $message_id;
                        $insert->created_at = date("Y-m-d H:i:s");
                        $insert->updated_at = date("Y-m-d H:i:s");
                        $insert->save();

                        $pid = $insert->id;
                    } else {
                        $pid = $model['id'];
                        $update = BubbleTextMessageDetails::find($pid);
                        $update->bubble_text_message_id = $message_id;
                        $update->updated_at = date("Y-m-d H:i:s");
                        $update->save();
                    }
                }

                $response = array();
                return $this->sendApiClientResponse('1', $response, trans('message.bubble_added'));
            } else {
                return $this->sendApiClientError('0', trans('message.wrong_apikey'), array(), '400');
            }
        } else {
            return $this->sendApiClientError('-11', trans('message.header_missing'), array(), '400');
        }
    }

    /**
     * Mobile Money Call Back
     * 
     * @return json array
     */
    public function mobileMoneyCallBack(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'externalId' => 'required',
            'resultcode' => 'required',
            'msisdn' => 'required',
            'amount' => 'required',
            'message' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendApiClientError('-11', trans('message.parameters_missing'), array(), '400');
        }

        $input = $request->all();
        $inputjson = json_encode($input);

        $apilog = new ApiLogs();
        $apilog->user_id = "0";
        $apilog->api_name = "Mobile Money - Callback - Body";
        $apilog->request_data = $inputjson;
        $apilog->response_data = "NA";
        $apilog->created_at = date("Y-m-d H:i:s");
        $apilog->updated_at = date("Y-m-d H:i:s");
        $apilog->save();

        $externalId = $input['externalId'];
        $resultcode = $input['resultcode'];
        $msisdn = $input['msisdn'];
        $amount = $input['amount'];
        $message = $input['message'];

        $check_trans = Transactions::where('trans_id', $externalId)->first();
        if (empty($check_trans)) {
            return $this->sendApiClientError('0', trans('message.trans_id_exit'), array(), '401');
        }

        $id = $check_trans['id'];

        /**
         * Add transaction details here
         */
        $transactions = Transactions::find($id);
        $transactions->trans_status = 1;
        $transactions->updated_at = $this->datetime;
        $transactions->save();
        if ($transactions->id > 0) {
            $response = array();
            return $this->sendApiClientResponse('1', $response, $message);
        } else {
            return $this->sendApiClientError('0', trans('message.selcom_api_error'), array(), '400');
        }
    }
}