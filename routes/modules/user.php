<?php

Route::group(
    [
        'namespace' => 'User',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/users', 'UserController@index')->name('admin.users.index');
        Route::get('{panel}/users/create', 'UserController@createEdit')->name('admin.users.create');
        Route::get('{panel}/users/edit/{id}', 'UserController@createEdit')->name('admin.users.edit');
        Route::post('{panel}/users/store', 'UserController@store')->name('admin.users.store');
        Route::post('{panel}/users/changeImage', 'UserController@changeImage')->name('admin.users.changeImage');
        Route::post('{panel}/users/deleteImage', 'UserController@deleteImage')->name('admin.users.deleteImage');
        
        Route::get('{panel}/users/show/{id}', 'UserController@show')->name('admin.users.show');
        Route::delete('{panel}/users/destroy/{id}', 'UserController@destroy')->name('admin.users.destroy');
        Route::post('{panel}/users/changeStatus', 'UserController@changeStatus')->name('admin.users.changesStatus');
        Route::post('{panel}/users/resetAttempt', 'UserController@resetAttempt')->name('admin.users.resetAttempt');
        Route::post('{panel}/users/resetOtpAttempt', 'UserController@resetOtpAttempt')->name('admin.users.resetOtpAttempt');  
        Route::post('{panel}/users/changeUssdStatus', 'UserController@changeUssdStatus')->name('admin.users.changeUssdStatus');
        Route::post('{panel}/users/toggle-status/{id}', 'UserController@toggleStatus')->name('admin.users.toggle_status');
        Route::post('{panel}/users/toggle-referal-status/{id}', 'UserController@toggleReferalStatus')->name('admin.users.toggle_referal_status');
        Route::get('{panel}/referal_request', 'UserController@referal_request')->name('admin.users.referal_request');
        
        // ajax
        Route::get('{panel}/users/users_json', 'UserController@index_json')->name('admin.users.index_json');
        Route::get('{panel}/referal_request_json', 'UserController@referal_request_json')->name('admin.users.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'UserController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'UserController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'UserController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'UserController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'UserController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'UserController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity1', 'UserController@statecity')->name('admin.users.statecity');
        Route::post('models1', 'UserController@models')->name('admin.users.models');
        Route::post('vehicleFuelType1', 'UserController@vehicleFuelType')->name('admin.users.vehicleFuelType');
        Route::post('vehicleColour1', 'UserController@vehicleColour')->name('admin.users.vehicleColour');

        Route::post('{panel}/users/documentVerification', 'UserController@documentVerification')->name('admin.users.documentVerification');
        
        //Route::get('{panel}/users/updateApprovedRequest', 'UserController@updateApprovedRequest')->name('admin.users.updateApprovedRequest');

        Route::get('{panel}/users/otp/{id}', 'UserController@otp')->name('admin.users.otp');  
        Route::get('{panel}/users/resendotp/{id}', 'UserController@resendotp')->name('admin.users.resendotp');      
        Route::post('{panel}/users/sendotp', 'UserController@sendotp')->name('admin.users.sendotp');
        
        
     }
);
