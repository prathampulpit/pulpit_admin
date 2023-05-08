<?php

Route::group(
    [
        'namespace' => 'Register',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::post('{panel}/register/history-comment', 'RegisterController@history_comment')->name('admin.register.history_comment');


        Route::get('{panel}/register', 'RegisterController@index')->name('admin.register.index');
        Route::match(['GET', 'POST'], '{panel}/register', 'RegisterController@index')->name('admin.register.index');
        Route::get('{panel}/registeruser/{sub?}', 'RegisterController@index')->name('admin.register.index');
        //Route::match(['GET', 'POST'], '{panel}/register/{sub?}', 'RegisterController@index')->name('admin.register.index');

        Route::get('{panel}/register/create', 'RegisterController@createEdit')->name('admin.register.create');
        Route::post('{panel}/register/update', 'RegisterController@createUpdate')->name('admin.register.update');
        Route::post('{panel}/register-agent/update', 'RegisterController@updateAgent')->name('admin.register_agent.update');
        Route::get('{panel}/register/edit/{id}', 'RegisterController@createEdit')->name('admin.register.edit');
        Route::post('{panel}/register/store', 'RegisterController@store')->name('admin.register.store');
        
        Route::post('{panel}/register/storefirst', 'RegisterController@storefirst')->name('admin.register.storefirst');        
        Route::post('{panel}/register/storesecond', 'RegisterController@storesecond')->name('admin.register.storesecond');
        Route::post('{panel}/register/storethird', 'RegisterController@storethird')->name('admin.register.storethird');
        
        Route::get('{panel}/register/show/{id}', 'RegisterController@show')->name('admin.register.show');
        Route::delete('{panel}/register/destroy/{id}', 'RegisterController@destroy')->name('admin.register.destroy');
        Route::post('{panel}/register/changeStatus', 'RegisterController@changeStatus')->name('admin.register.changesStatus');
        Route::post('{panel}/register/resetAttempt', 'RegisterController@resetAttempt')->name('admin.register.resetAttempt');
        Route::post('{panel}/register/resetOtpAttempt', 'RegisterController@resetOtpAttempt')->name('admin.register.resetOtpAttempt');  
        Route::post('{panel}/register/changeUssdStatus', 'RegisterController@changeUssdStatus')->name('admin.register.changeUssdStatus');
        Route::post('{panel}/register/toggle-status/{id}', 'RegisterController@toggleStatus')->name('admin.register.toggle_status');
        Route::post('{panel}/register/toggle-referal-status/{id}', 'RegisterController@toggleReferalStatus')->name('admin.register.toggle_referal_status');
        Route::get('{panel}/referal_request', 'RegisterController@referal_request')->name('admin.register.referal_request');
        
        // ajax
        Route::get('{panel}/register/users_json/{sub?}', 'RegisterController@index_json')->name('admin.register.index_json');
        Route::get('{panel}/referal_request_json', 'RegisterController@referal_request_json')->name('admin.register.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'RegisterController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'RegisterController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'RegisterController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'RegisterController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'RegisterController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'RegisterController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity1', 'RegisterController@statecity')->name('admin.register.statecity');
        Route::post('statecity', 'RegisterController@statecity')->name('admin.register.statecity');
        Route::post('models1', 'RegisterController@models')->name('admin.register.models');
        Route::post('vehicleFuelType1', 'RegisterController@vehicleFuelType')->name('admin.register.vehicleFuelType');
        Route::post('vehicleColour1', 'RegisterController@vehicleColour')->name('admin.register.vehicleColour');

        Route::post('{panel}/register/documentVerification', 'RegisterController@documentVerification')->name('admin.register.documentVerification');

        Route::get('{panel}/register/otp/{id}', 'RegisterController@otp')->name('admin.register.otp');  
        Route::get('{panel}/register/resendotp/{id}', 'RegisterController@resendotp')->name('admin.register.resendotp');      
        Route::post('{panel}/register/sendotp', 'RegisterController@sendotp')->name('admin.register.sendotp');
        Route::get('{panel}/register/downloadexport', 'RegisterController@downloadexport')->name('admin.register.downloadexport');
    }
);
