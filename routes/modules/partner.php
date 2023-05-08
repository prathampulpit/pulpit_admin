<?php

Route::group(
    [
        'namespace' => 'Partner',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // partner
        /******************************************/

        Route::get('{panel}/partner', 'PartnerController@index')->name('admin.partner.index');
        Route::get('{panel}/partner/create', 'PartnerController@createEdit')->name('admin.partner.create');
        Route::get('{panel}/partner/edit/{id}', 'PartnerController@createEdit')->name('admin.partner.edit');
        Route::post('{panel}/partner/store', 'PartnerController@store')->name('admin.partner.store');
        Route::get('{panel}/partner/show/{id}', 'PartnerController@show')->name('admin.partner.show');
        Route::delete('{panel}/partner/destroy/{id}', 'PartnerController@destroy')->name('admin.partner.destroy');
        Route::post('{panel}/partner/changeStatus', 'PartnerController@changeStatus')->name('admin.partner.changesStatus');
        Route::post('{panel}/partner/resetAttempt', 'PartnerController@resetAttempt')->name('admin.partner.resetAttempt');
        Route::post('{panel}/partner/resetOtpAttempt', 'PartnerController@resetOtpAttempt')->name('admin.partner.resetOtpAttempt');  
        Route::post('{panel}/partner/changeUssdStatus', 'PartnerController@changeUssdStatus')->name('admin.partner.changeUssdStatus');
        Route::post('{panel}/partner/toggle-status/{id}', 'PartnerController@toggleStatus')->name('admin.partner.toggle_status');
        Route::post('{panel}/partner/toggle-referal-status/{id}', 'PartnerController@toggleReferalStatus')->name('admin.partner.toggle_referal_status');
        Route::get('{panel}/referal_request', 'PartnerController@referal_request')->name('admin.partner.referal_request');
        
        // ajax
        Route::get('{panel}/partner/user_json', 'PartnerController@index_json')->name('admin.partner.index_json');
        Route::get('{panel}/referal_request_json', 'PartnerController@referal_request_json')->name('admin.partner.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'PartnerController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'PartnerController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'PartnerController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'PartnerController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'PartnerController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'PartnerController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity', 'PartnerController@statecity')->name('admin.partner.statecity');
        Route::post('models', 'PartnerController@models')->name('admin.partner.models');
        Route::post('vehicleFuelType', 'PartnerController@vehicleFuelType')->name('admin.partner.vehicleFuelType');
        Route::post('vehicleColour', 'PartnerController@vehicleColour')->name('admin.partner.vehicleColour');

        Route::post('{panel}/partner/documentVerification', 'PartnerController@documentVerification')->name('admin.partner.documentVerification');

        Route::get('{panel}/partner/otp/{id}', 'PartnerController@otp')->name('admin.partner.otp');  
        Route::get('{panel}/partner/resendotp/{id}', 'PartnerController@resendotp')->name('admin.partner.resendotp');      
        Route::post('{panel}/partner/sendotp', 'PartnerController@sendotp')->name('admin.partner.sendotp');
    }
);
