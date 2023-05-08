<?php

Route::group(
    [
        'namespace' => 'DriverCumOwner',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Driver
        /******************************************/
        
        //Route::get('{panel}/driver_cum_owner', 'DriverCumOwnerController@index')->name('admin.driver_cum_owner.index');
        //Route::match(['GET', 'POST'], '{panel}/driver_cum_owner', 'DriverCumOwnerController@index')->name('admin.driver_cum_owner.index');
        

        Route::get('{panel}/driver_cum_owner', 'DriverCumOwnerController@index')->name('admin.driver_cum_owner.index');
        Route::match(['GET', 'POST'], '{panel}/driver_cum_owner', 'DriverCumOwnerController@index')->name('admin.driver_cum_owner.index');
        Route::get('{panel}/driver_cum_ownerfilter/{param?}', 'DriverCumOwnerController@index')->name('admin.driver_cum_owner.index');

        Route::get('{panel}/driver_cum_owner/create', 'DriverCumOwnerController@createEdit')->name('admin.driver_cum_owner.create');
        Route::get('{panel}/driver/edit/{id}', 'DriverCumOwnerController@createEdit')->name('admin.driver_cum_owner.edit');
        Route::post('{panel}/driver_cum_owner/store', 'DriverCumOwnerController@store')->name('admin.driver_cum_owner.store');
        Route::get('{panel}/driver_cum_owner/show/{id}', 'DriverCumOwnerController@show')->name('admin.driver_cum_owner.show');
        Route::delete('{panel}/driver_cum_owner/destroy/{id}', 'DriverCumOwnerController@destroy')->name('admin.driver_cum_owner.destroy');
        Route::post('{panel}/driver_cum_owner/changeStatus', 'DriverCumOwnerController@changeStatus')->name('admin.driver_cum_owner.changesStatus');
        Route::post('{panel}/driver_cum_owner/resetAttempt', 'DriverCumOwnerController@resetAttempt')->name('admin.driver_cum_owner.resetAttempt');
        Route::post('{panel}/driver_cum_owner/resetOtpAttempt', 'DriverCumOwnerController@resetOtpAttempt')->name('admin.driver_cum_owner.resetOtpAttempt');  
        Route::post('{panel}/driver_cum_owner/changeUssdStatus', 'DriverCumOwnerController@changeUssdStatus')->name('admin.driver_cum_owner.changeUssdStatus');
        Route::post('{panel}/driver_cum_owner/toggle-status/{id}', 'DriverCumOwnerController@toggleStatus')->name('admin.driver_cum_owner.toggle_status');
        Route::post('{panel}/driver_cum_owner/toggle-referal-status/{id}', 'DriverCumOwnerController@toggleReferalStatus')->name('admin.driver_cum_owner.toggle_referal_status');
        Route::get('{panel}/referal_request', 'DriverCumOwnerController@referal_request')->name('admin.driver_cum_owner.referal_request');
        
        // ajax
        Route::get('{panel}/driver_cum_owner/user_json/{filter?}', 'DriverCumOwnerController@index_json')->name('admin.driver_cum_owner.index_json');
        Route::get('{panel}/referal_request_json', 'DriverCumOwnerController@referal_request_json')->name('admin.driver_cum_owner.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'DriverCumOwnerController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'DriverCumOwnerController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'DriverCumOwnerController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'DriverCumOwnerController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'DriverCumOwnerController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'DriverCumOwnerController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity', 'DriverCumOwnerController@statecity')->name('admin.driver_cum_owner.statecity');
        Route::post('{panel}/getplans', 'DriverCumOwnerController@getplansbyvehicletype')->name('admin.driver_cum_owner.getplans');
        Route::post('models', 'DriverCumOwnerController@models')->name('admin.driver_cum_owner.models');
        Route::post('vehicleFuelType', 'DriverCumOwnerController@vehicleFuelType')->name('admin.driver_cum_owner.vehicleFuelType');
        Route::post('vehicleColour', 'DriverCumOwnerController@vehicleColour')->name('admin.driver_cum_owner.vehicleColour');

        Route::post('{panel}/driver_cum_owner/documentVerification', 'DriverCumOwnerController@documentVerification')->name('admin.driver_cum_owner.documentVerification');

        Route::get('{panel}/driver_cum_owner/otp/{id}', 'DriverCumOwnerController@otp')->name('admin.driver_cum_owner.otp');  
        Route::get('{panel}/driver_cum_owner/resendotp/{id}', 'DriverCumOwnerController@resendotp')->name('admin.driver_cum_owner.resendotp');      
        Route::post('{panel}/driver_cum_owner/sendotp', 'DriverCumOwnerController@sendotp')->name('admin.driver_cum_owner.sendotp');
    }
);
