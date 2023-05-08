<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Devices;
use App\Models\User;
use App\UserAccounts;
use App\AccountBalances;
use App\LinkCards;
use App\Currencies;
use App\Cities;
use App\ApiLogs;
use DB;

class UpdateBalance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $user_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $user_id)
    {
        $this->user_id = $user_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user_id = $this->user_id;
        $user = User::find($user_id);
        $language_code = $user->current_language;
        $client_id = $user->client_id;



        $clientcert = "/var/www/html/arasslkey/client.crt";
        $keyfile = "/var/www/html/arasslkey/client.key";

        $api_url = 'client/' . $client_id . '/accounts';
        $endpoint_base_url = env("SELCOM_URL") . $api_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint_base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_POST => 0,
            CURLOPT_SSLCERT => $clientcert,
            CURLOPT_SSLKEYTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK " . env("AUTH_KEY"),
                "x-customer-lang: " . $language_code,
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        } else {
            $json_arr = json_decode($response, true);
            if (!empty($json_arr)) {
                $accounts = $json_arr;
                if (!empty($accounts)) {
                    $resultcode = $accounts['resultcode'];
                    $result = $accounts['result'];
                    $account_data = $accounts['data'];
                    if (!empty($account_data) && $resultcode == 200) {
                        foreach ($account_data as $val) {
                            $accountBalance = $val['accountBalance'];
                            $availableBalance = $val['availableBalance'];
                            $currency = $val['currency'];

                            $currencies = Currencies::where('currency_code', '=', $currency)->first();
                            $currency_id = $currencies['id'];

                            $useraccount = UserAccounts::where('user_id', '=', $user_id)->first();
                            $user_account_id = $useraccount->id;

                            DB::table('account_balances')->where('currency_id', $currency_id)->where('user_account_id', $user_account_id)->update(['account_balance' => $availableBalance]);
                        }
                    }
                }
            }
        }


        /* stash balance */

        $api_url = 'client/' . $client_id . '/stash-info';
        $endpoint_base_url = env("SELCOM_URL") . $api_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint_base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_POST => 0,
            CURLOPT_SSLCERT => $clientcert,
            CURLOPT_SSLKEYTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK " . env("AUTH_KEY"),
                "x-customer-lang: " . $language_code,
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        } else {
            $json_arr = json_decode($response, true);
            if (!empty($json_arr)) {
                $accounts = $json_arr;
                if (!empty($accounts)) {
                    $resultcode = $accounts['resultcode'];
                    if ($resultcode == 200) {
                        $accountBalance = $accounts['data'][0]['accountBalance'];

                        DB::table('stashes')->where('user_id', $user_id)->update(['stash_balance' => $accountBalance]);

                        return $accountBalance;
                    }
                }
            }
        }

        /* Currency AvaBalance */
        $api_url = 'client/' . $client_id . '/forex-account-balance';
        $endpoint_base_url = env("SELCOM_URL") . $api_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint_base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_POST => 0,
            CURLOPT_SSLCERT => $clientcert,
            CURLOPT_SSLKEYTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK " . env("AUTH_KEY"),
                "x-customer-lang: " . $language_code,
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        } else {
            $json_arr = json_decode($response, true);
            if (!empty($json_arr)) {
                $accounts = $json_arr;
                if (!empty($accounts)) {
                    $resultcode = $accounts['resultcode'];
                    $account_data = $accounts['data'];
                    if (!empty($account_data) && $resultcode == 200) {
                        foreach ($account_data as $val) {
                            $availableBalance = $val['balance'];
                            $currency = $val['currency'];

                            $currencies = Currencies::where('currency_code', '=', $currency)->first();
                            $currency_id = $currencies['id'];

                            $useraccount = UserAccounts::where('user_id', '=', $user_id)->first();
                            $user_account_id = $useraccount->id;

                            DB::table('account_balances')->where('currency_id', $currency_id)->where('user_account_id', $user_account_id)->update(['account_balance' => $availableBalance]);
                        }
                    }
                }
            }
        }

        /* Qwikrewards Balance */
        $api_url = 'client/' . $client_id . '/qwikrewards';
        $endpoint_base_url = env("SELCOM_URL") . $api_url;

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $endpoint_base_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_POST => 0,
            CURLOPT_SSLCERT => $clientcert,
            CURLOPT_SSLKEYTYPE => 'PEM',
            CURLOPT_SSLKEY => $keyfile,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "Authorization: DIGIBANK " . env("AUTH_KEY"),
                "x-customer-lang: " . $language_code,
                "Content-Type: application/json"
            ),
        ));
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
        } else {
            $json_arr = json_decode($response, true);
            if (!empty($json_arr)) {
                $accounts = $json_arr;
                if (!empty($accounts)) {
                    $resultcode = $accounts['resultcode'];
                    $result = $accounts['result'];
                    if ($resultcode == 200) {
                        $balance = $accounts['data'][0]['balance'];
                        DB::table('user_accounts')->where('user_id', $user_id)->update(['quickrewards_balance' => $balance]);

                        return $balance;
                    }
                }
            }
        }
    }
}
