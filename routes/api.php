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
Route::post('v1/documentScanResult', 'API\V_1\RegisterController@documentScanResult');
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
    Route::post('v1/referAFriend', 'API\V_1\UserController@referAFriend');
    Route::post('v1/referRequestLists', 'API\V_1\UserController@referRequestLists');
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
    Route::post('v1/getVcnUrl', 'API\V_1\CardController@getVcnUrl');
    Route::post('v1/getPhysicalCard', 'API\V_1\CardController@getPhysicalCard');
    Route::post('v1/createVcn', 'API\V_1\CardController@createVcn');
    Route::post('v1/checkMobileMoneyNumber', 'API\V_1\CardController@checkMobileMoneyNumber');
    Route::post('v1/mobileMoneyTrans', 'API\V_1\CardController@mobileMoneyTrans');
    Route::post('v1/checkMobileMoneyTransStatus', 'API\V_1\CardController@checkMobileMoneyTransStatus');
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
    Route::post('v1/biometricSetting', 'API\V_1\SettingsController@biometricSetting');
    Route::post('v1/getStateList', 'API\V_1\SettingsController@getStateList');
    Route::post('v1/currencyEnableSetting', 'API\V_1\SettingsController@currencyEnableSetting');
    Route::post('v1/atmAccessSetting', 'API\V_1\SettingsController@atmAccessSetting');
    /* End */
});

Route::post('v1/allTransactions1', 'API\V_1\TransactionController@allTransactions1');
//Route::post('v1/digest', 'API\V_1\DigestController@digest');
//Route::post('v1/checkMobileNumber', 'API\V_1\QwiksendsController@checkMobileNumber');

/* API Clients */
Route::post('callback/v1/sendPushNotification', 'API\V_1\ApiClientController@sendPushNotification');
Route::post('callback/v1/pinHash', 'API\V_1\ApiClientController@pinHash');
Route::post('callback/v1/register', 'API\V_1\ApiClientController@register');
Route::post('callback/v1/changePinHash', 'API\V_1\ApiClientController@changePinHash');
Route::post('callback/v1/blockProfile', 'API\V_1\ApiClientController@blockProfile');
Route::post('callback/v1/changeCustomerLanguage', 'API\V_1\ApiClientController@changeCustomerLanguage');
Route::post('callback/v1/resetPin', 'API\V_1\ApiClientController@resetPin');
Route::post('callback/v1/setPin', 'API\V_1\ApiClientController@setPin');
Route::post('callback/v1/addBubbleText', 'API\V_1\ApiClientController@addBubbleText');
Route::post('callback/v1/mobileMoneyCallBack', 'API\V_1\ApiClientController@mobileMoneyCallBack');
Route::post('callback/v1/updateDocument', 'API\V_1\ApiClientController@updateDocument');
/* End */

/* Get Selcom  */
Route::get('v1/getBillPaymentProductFromSelcom', 'API\V_1\ConfigController@getBillPaymentProductFromSelcom');
Route::post('v1/checkConfigApiVersion', 'API\V_1\ConfigController@checkConfigApiVersion');
Route::get('v1/getBankListFromSelcom', 'API\V_1\ConfigController@getBankListFromSelcom');
Route::get('v1/getWalletListFromSelcom', 'API\V_1\ConfigController@getWalletListFromSelcom');
Route::get('v1/getCategoryListFromSelcom', 'API\V_1\ConfigController@getCategoryListFromSelcom');
/* End */

Route::post('v1/getReportByCategory', 'API\V_1\BillPaymentController@getReportByCategory');
Route::post('v1/digestTransactions', 'API\V_1\TransactionController@digestTransactions');

Route::post('v1/billPaymentProductLists', 'API\V_1\ConfigController@billPaymentProductLists');
Route::post('v1/categoryWalletBanksAppConfig', 'API\V_1\ConfigController@categoryWalletBanksAppConfig');
Route::post('v1/otherCountryAppConfig', 'API\V_1\ConfigController@otherCountryAppConfig');
Route::post('v1/cityAppConfig', 'API\V_1\ConfigController@cityAppConfig');
Route::post('v1/countryAppConfig', 'API\V_1\ConfigController@countryAppConfig');
Route::post('v1/nationalityIdentificationConfig', 'API\V_1\ConfigController@nationalityIdentificationConfig');
Route::post('v1/supportTopicsConfig', 'API\V_1\ConfigController@supportTopicsConfig');
Route::get('v1/getForexRate', 'API\V_1\ConfigController@getForexRate');
Route::get('v1/storeLocators', 'API\V_1\ConfigController@storeLocators');
Route::get('v1/removeAttempt', 'API\V_1\ConfigController@removeAttempt');
Route::post('v1/disputeTransactionTopicsConfig', 'API\V_1\ConfigController@disputeTransactionTopicsConfig');

Route::get('v1/cities/search', 'API\V_1\ConfigController@citySearch')
    ->name('api.cities.search');

Route::get('v1/users/search', 'API\V_1\ConfigController@userSearch')
    ->name('api.users.search');

Route::post('v1/editClient', 'API\V_1\UserController@editClient');
Route::post('v1/createVcnForWeb', 'API\V_1\CardController@createVcnForWeb');
Route::post('v1/checkReferralNumber', 'API\V_1\UserController@checkReferralNumber');

/* Demo function */
Route::post('v1/demofunction', 'API\V_1\ConfigController@demoFunc');
Route::post('v1/checkReferralNumber', 'API\V_1\UserController@checkReferralNumber');
Route::post('v1/checkUserContactList', 'API\V_1\UserController@checkUserContactList');
Route::post('v1/sendReferRequest', 'API\V_1\UserController@sendReferRequest');
Route::post('v1/acceptRejectRequest', 'API\V_1\UserController@acceptRejectRequest');
Route::post('v1/checkAllUserContactList', 'API\V_1\UserController@checkAllUserContactList');
Route::post('v1/birtAccessRequest', 'API\V_1\RegisterController@birtAccessRequest');

/* Update selcom balance using cronjob */
Route::get('v1/cronAraBalanceUpdate', 'API\V_1\ConfigController@cronAraBalanceUpdate');
Route::get('v1/cronStashBalanceUpdate', 'API\V_1\ConfigController@cronStashBalanceUpdate');
Route::get('v1/cronCurrencyBalanceUpdate', 'API\V_1\ConfigController@cronCurrencyBalanceUpdate');
Route::get('v1/cronQwikrewardsBalanceUpdate', 'API\V_1\ConfigController@cronQwikrewardsBalanceUpdate');
/* End */
Route::get('v1/updateUserAraAvaBalance/{user_id}', 'API\V_1\BaseController@araAvaBalance');