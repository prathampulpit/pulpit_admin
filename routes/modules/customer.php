<?php
use Illuminate\Support\Facades\Route;

Route::group(
    [
        'namespace' => 'Customer',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // customers
        /******************************************/

        // Route::get('{panel}/customers', 'CustomerController@index')->name('admin.customers.index');

        Route::get('{panel}/customers', 'CustomerController@index')->name('admin.customers.index');
        Route::get('{panel}/customersfilter/{filter?}', 'CustomerController@index')->name('admin.customers.index');
        Route::post('{panel}/customersfilter', 'CustomerController@index')->name('admin.customers.customeFilter');


        Route::get('{panel}/customers/create', 'CustomerController@createEdit')->name('admin.customers.create');
        Route::get('{panel}/customers/edit/{id}', 'CustomerController@createEdit')->name('admin.customers.edit');
        Route::post('{panel}/customers/store', 'CustomerController@store')->name('admin.customers.store');
        Route::post('{panel}/customers/changeImage', 'CustomerController@changeImage')->name('admin.customers.changeImage');
        Route::post('{panel}/customers/deleteImage', 'CustomerController@deleteImage')->name('admin.customers.deleteImage');
        
        Route::get('{panel}/customers/show/{id}', 'CustomerController@show')->name('admin.customers.show');
        Route::delete('{panel}/customers/destroy/{id}', 'CustomerController@destroy')->name('admin.customers.destroy');
        Route::post('{panel}/customers/changeStatus', 'CustomerController@changeStatus')->name('admin.customers.changesStatus');
        Route::post('{panel}/customers/resetAttempt', 'CustomerController@resetAttempt')->name('admin.customers.resetAttempt');
        Route::post('{panel}/customers/resetOtpAttempt', 'CustomerController@resetOtpAttempt')->name('admin.customers.resetOtpAttempt');  
        Route::post('{panel}/customers/changeUssdStatus', 'CustomerController@changeUssdStatus')->name('admin.customers.changeUssdStatus');
        Route::post('{panel}/customers/toggle-status/{id}', 'CustomerController@toggleStatus')->name('admin.customers.toggle_status');
        Route::post('{panel}/customers/toggle-referal-status/{id}', 'CustomerController@toggleReferalStatus')->name('admin.customers.toggle_referal_status');
        Route::get('{panel}/referal_request', 'CustomerController@referal_request')->name('admin.customers.referal_request');
        
        // ajax
        Route::get('{panel}/customers/users_json/{filter?}', 'CustomerController@index_json')->name('admin.customers.index_json');
        Route::get('{panel}/referal_request_json', 'CustomerController@referal_request_json')->name('admin.customers.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'CustomerController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'CustomerController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'CustomerController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'CustomerController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'CustomerController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'CustomerController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity1', 'CustomerController@statecity')->name('admin.customers.statecity');
        Route::post('models1', 'CustomerController@models')->name('admin.customers.models');
        Route::post('vehicleFuelType1', 'CustomerController@vehicleFuelType')->name('admin.customers.vehicleFuelType');
        Route::post('vehicleColour1', 'CustomerController@vehicleColour')->name('admin.customers.vehicleColour');

        Route::post('{panel}/customers/documentVerification', 'CustomerController@documentVerification')->name('admin.customers.documentVerification');
        
        //Route::get('{panel}/customers/updateApprovedRequest', 'CustomerController@updateApprovedRequest')->name('admin.customers.updateApprovedRequest');

        Route::get('{panel}/customers/otp/{id}', 'CustomerController@otp')->name('admin.customers.otp');  
        Route::get('{panel}/customers/resendotp/{id}', 'CustomerController@resendotp')->name('admin.customers.resendotp');      
        Route::post('{panel}/customers/sendotp', 'CustomerController@sendotp')->name('admin.customers.sendotp');
    }
);
