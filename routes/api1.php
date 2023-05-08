<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Register controller */

Route::group(['middleware' => ['authapi']], function () {
    Route::post('v1/register', 'API\V_1\RegisterController@register');
    Route::post('v1/login', 'API\V_1\RegisterController@login');
});
/* End */

/* Onbording module APIs */
Route::post('v1/documentUpload', 'API\V_1\RegisterController@documentUpload');
Route::post('v1/uploadOcrCardDocument', 'API\V_1\RegisterController@uploadOcrCardDocument');
Route::post('v1/updateRegisterDetails', 'API\V_1\RegisterController@updateRegisterDetails');
Route::post('v1/verifyDocument', 'API\V_1\RegisterController@verifyDocument');
Route::post('v1/uploadWorkPermitDocuments', 'API\V_1\RegisterController@uploadWorkPermitDocuments');
Route::post('v1/updateDeviceDetails', 'API\V_1\RegisterController@updateDeviceDetails');
Route::post('v1/updateLoginPin', 'API\V_1\RegisterController@updateLoginPin');
Route::post('v1/checkOtp', 'API\V_1\RegisterController@checkOtp');
Route::post('v1/updateRegisterStep', 'API\V_1\RegisterController@updateRegisterStep');
Route::post('v1/detachAccount', 'API\V_1\RegisterController@detachAccount');
/* End */
Route::post('v1/demo', 'API\V_1\DemoController@demo');

Route::group(['middleware' => ['auth:api', 'authapi']], function () {
    /* Start user controller */
    Route::post('v1/userDetails', 'API\V_1\UserController@userDetails');
    Route::post('v1/editProfile', 'API\V_1\UserController@editProfile');
    /* End */

    /* Start card controller */
    Route::post('v1/addCard', 'API\V_1\CardController@addCard');
    Route::post('v1/cardLists', 'API\V_1\CardController@cardLists');
    Route::post('v1/removeCard', 'API\V_1\CardController@removeCard');
    Route::post('v1/checkCardDetails', 'API\V_1\CardController@checkCardDetails');
    Route::post('v1/pullFund', 'API\V_1\CardController@pullFund');
    Route::post('v1/linkCard', 'API\V_1\CardController@linkCard');
    Route::post('v1/suspendCard', 'API\V_1\CardController@suspendCard');
    Route::post('v1/cancelCard', 'API\V_1\CardController@cancelCard');
    Route::post('v1/checkSerialNumber', 'API\V_1\CardController@checkSerialNumber');
    Route::post('v1/changeCardPin', 'API\V_1\CardController@changeCardPin');
    /* End */

    /* Start qwiksend controller */
    Route::post('v1/qwiksend', 'API\V_1\QwiksendsController@qwiksend');
    Route::post('v1/checkMobileNumber', 'API\V_1\QwiksendsController@checkMobileNumber');
    Route::post('v1/checkBankAccount', 'API\V_1\QwiksendsController@checkBankAccount');
    Route::post('v1/checkBankBalance', 'API\V_1\QwiksendsController@checkBankBalance');
    Route::post('v1/checkWalletNumber', 'API\V_1\QwiksendsController@checkWalletNumber');
    Route::post('v1/araToOtherCountry', 'API\V_1\QwiksendsController@araToOtherCountry');
    Route::post('v1/araToOtherCountryValidation', 'API\V_1\QwiksendsController@araToOtherCountryValidation');
    /* End */

    /* Start Qwikcash controller */
    Route::post('v1/getAgentDetails', 'API\V_1\QwikcashController@getAgentDetails');
    Route::post('v1/qwikcash', 'API\V_1\QwikcashController@qwikcash');
    /* End */

    /* Start Currency Balances */
    Route::post('v1/currencyBalances', 'API\V_1\CurrencyBalancesController@currencyBalances');
    Route::post('v1/convertCurrencyRate', 'API\V_1\CurrencyBalancesController@convertCurrencyRate');
    Route::post('v1/checkCurrencyTransferBalance', 'API\V_1\CurrencyBalancesController@checkCurrencyTransferBalance');
    Route::post('v1/currencyTransfer', 'API\V_1\CurrencyBalancesController@currencyTransfer');
    Route::post('v1/araBalance', 'API\V_1\CurrencyBalancesController@araBalance');
    /* End */

    /* Start Bill Payment */
    Route::post('v1/billPaymentProducts', 'API\V_1\BillPaymentController@billPaymentProducts');
    Route::post('v1/frequentlyPaidProducts', 'API\V_1\BillPaymentController@frequentlyPaidProducts');
    Route::post('v1/topupLists', 'API\V_1\BillPaymentController@topupLists');
    Route::post('v1/checkBillPayment', 'API\V_1\BillPaymentController@checkBillPayment');
    Route::post('v1/billPaymentTransaction', 'API\V_1\BillPaymentController@billPaymentTransaction');
    Route::post('v1/categoryList', 'API\V_1\BillPaymentController@categoryList');
    /* End */

    /* Start All Transactions */
    Route::post('v1/allTransactions', 'API\V_1\TransactionController@allTransactions');
    Route::post('v1/sendEmailStatements', 'API\V_1\TransactionController@sendEmailStatements');
    Route::post('v1/disputeTransaction', 'API\V_1\TransactionController@disputeTransaction');
    Route::post('v1/updateCategoryInTransaction', 'API\V_1\TransactionController@updateCategoryInTransaction');
    /* End */

    /* Start Mastercard Qr */
    Route::post('v1/checkPayNumber', 'API\V_1\MastercardQrController@checkPayNumber');
    Route::post('v1/mastercardQrPayement', 'API\V_1\MastercardQrController@mastercardQrPayement');
    /* End */

    /* Start Notification */
    Route::post('v1/notificationLists', 'API\V_1\NotificationsController@notificationLists');
    Route::post('v1/removeNotification', 'API\V_1\NotificationsController@removeNotification');
    Route::post('v1/removeAllNotification', 'API\V_1\NotificationsController@removeAllNotification');
    /* End */

    /* Start Dashboard */
    Route::post('v1/dashboard', 'API\V_1\DashboardController@dashboard');
    /* End */

    /* Start Locator */
    Route::post('v1/locator', 'API\V_1\LocatorController@locator');
    /* End */

    /* Digest */
    Route::post('v1/digest', 'API\V_1\DigestController@digest');
    /* End */

    /* Stash */
    Route::post('v1/addStash', 'API\V_1\StashesController@addStash');
    Route::post('v1/stashGraph', 'API\V_1\StashesController@stashGraph');
    /* End */

    /* Setting Controller */
    Route::post('v1/addContactsDetails', 'API\V_1\SettingsController@addContactsDetails');
    Route::post('v1/changeLanguage', 'API\V_1\SettingsController@changeLanguage');
    Route::post('v1/notificationSetting', 'API\V_1\SettingsController@notificationSetting');
    Route::post('v1/changeLoginPin', 'API\V_1\SettingsController@changeLoginPin');
    Route::post('v1/verifyLoginPin', 'API\V_1\SettingsController@verifyLoginPin');
    /* End */
});

/* Get Selcom  */
Route::post('v1/getBillPaymentProductFromSelcom', 'API\V_1\BillPaymentController@getBillPaymentProductFromSelcom');
Route::post('v1/checkConfigApiVersion', 'API\V_1\ConfigController@checkConfigApiVersion');
Route::post('v1/getBankListFromSelcom', 'API\V_1\ConfigController@getBankListFromSelcom');
/* End */

Route::post('v1/getReportByCategory', 'API\V_1\BillPaymentController@getReportByCategory');
Route::post('v1/digestTransactions', 'API\V_1\TransactionController@digestTransactions');
Route::post('callback/v1/sendPushNotification', 'API\V_1\ApiClientController@sendPushNotification');
Route::post('v1/billPaymentProductLists', 'API\V_1\ConfigController@billPaymentProductLists');
Route::post('v1/categoryWalletBanksAppConfig', 'API\V_1\ConfigController@categoryWalletBanksAppConfig');
Route::post('v1/otherCountryAppConfig', 'API\V_1\ConfigController@otherCountryAppConfig');
Route::post('v1/cityAppConfig', 'API\V_1\ConfigController@cityAppConfig');
Route::post('v1/countryAppConfig', 'API\V_1\ConfigController@countryAppConfig');
Route::post('v1/nationalityIdentificationConfig', 'API\V_1\ConfigController@nationalityIdentificationConfig');
Route::post('v1/supportTopicsConfig', 'API\V_1\ConfigController@supportTopicsConfig');