<?php

Route::group(
    [
        'namespace' => 'Travel',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Travel
        /******************************************/

        //Route::any('{panel}/travel', 'TravelController@index')->name('admin.travel.index');
        //Route::post('{panel}/travelsearch', 'TravelController@search')->name('admin.travel.search');
        //Route::match(['GET', 'POST'], '{panel}/travel', 'TravelController@index')->name('admin.travel.index');
        Route::get('{panel}/travel', 'TravelController@index')->name('admin.travel.index');
        Route::match(['GET', 'POST'], '{panel}/travelfilter', 'TravelController@index')->name('admin.travel.index');
        Route::get('{panel}/travelfilter/{filter?}', 'TravelController@index')->name('admin.travel.index');
        
        Route::get('{panel}/travel/create', 'TravelController@createEdit')->name('admin.travel.create');
        Route::get('{panel}/travel/edit/{id}', 'TravelController@createEdit')->name('admin.travel.edit');
        Route::post('{panel}/travel/store', 'TravelController@store')->name('admin.travel.store');
        Route::get('{panel}/travel/show/{id}', 'TravelController@show')->name('admin.travel.show');
        Route::get('{panel}/travel/show_v2/{id}', 'TravelController@show_v2')->name('admin.travel.show_v2');
        Route::delete('{panel}/travel/destroy/{id}', 'TravelController@destroy')->name('admin.travel.destroy');
        Route::post('{panel}/travel/changeStatus', 'TravelController@changeStatus')->name('admin.travel.changesStatus');
        Route::post('{panel}/travel/resetAttempt', 'TravelController@resetAttempt')->name('admin.travel.resetAttempt');
        Route::post('{panel}/travel/resetOtpAttempt', 'TravelController@resetOtpAttempt')->name('admin.travel.resetOtpAttempt');  
        Route::post('{panel}/travel/changeUssdStatus', 'TravelController@changeUssdStatus')->name('admin.travel.changeUssdStatus');
        Route::post('{panel}/travel/toggle-status/{id}', 'TravelController@toggleStatus')->name('admin.travel.toggle_status');
        Route::post('{panel}/travel/toggle-referal-status/{id}', 'TravelController@toggleReferalStatus')->name('admin.travel.toggle_referal_status');
        Route::get('{panel}/referal_request', 'TravelController@referal_request')->name('admin.travel.referal_request');
        
        // ajax
        Route::get('{panel}/travel/user_json/{filter?}', 'TravelController@index_json')->name('admin.travel.index_json');
        Route::get('{panel}/referal_request_json', 'TravelController@referal_request_json')->name('admin.travel.referal_request_json');
        
        //Change password
        Route::get('{panel}/change-password', 'TravelController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'TravelController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'TravelController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'TravelController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'TravelController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'TravelController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity', 'TravelController@statecity')->name('admin.travel.statecity');
        Route::post('models', 'TravelController@models')->name('admin.travel.models');
        Route::post('vehicleFuelType', 'TravelController@vehicleFuelType')->name('admin.travel.vehicleFuelType');
        Route::post('vehicleColour', 'TravelController@vehicleColour')->name('admin.travel.vehicleColour');
        Route::post('{panel}/getplans', 'TravelController@getplansbyvehicletype')->name('admin.travel.getplans');
        Route::post('{panel}/travel/documentVerification', 'TravelController@documentVerification')->name('admin.travel.documentVerification');

        Route::get('{panel}/travel/otp/{id}', 'TravelController@otp')->name('admin.travel.otp');  
        Route::get('{panel}/travel/resendotp/{id}', 'TravelController@resendotp')->name('admin.travel.resendotp');      
        Route::post('{panel}/travel/sendotp', 'TravelController@sendotp')->name('admin.travel.sendotp');
        
        
        Route::get('{panel}/travel/verify_personal_document/{user_id}/{status}', 'TravelController@verify_personal_document')->name('admin.travel.verify_personal_document');
        Route::get('{panel}/travel/verify_vehicle_document/{id}/{status}', 'TravelController@verify_vehicle_document')->name('admin.travel.verify_vehicle_document');
        Route::get('{panel}/travel/verify_driver_document/{id}/{status}', 'TravelController@verify_driver_document')->name('admin.travel.verify_driver_document');
        
    }
);
