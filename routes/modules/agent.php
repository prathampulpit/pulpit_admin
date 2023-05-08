<?php

Route::group(
    [
        'namespace' => 'Agent',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Agent
        /******************************************/
        Route::post('{panel}/agent/update', 'AgentController@createUpdate')->name('admin.agent.update');
        Route::post('{panel}/agentDetail/update', 'AgentController@updateAgent')->name('admin.agentDetail.update');
        //Route::get('{panel}/agent', 'AgentController@index')->name('admin.agent.index');
        //Route::match(['GET', 'POST'], '{panel}/agent', 'AgentController@index')->name('admin.agent.index');

  
        Route::get('{panel}/agent', 'AgentController@index')->name('admin.agent.index');
        Route::get('{panel}/agentfilter/{filter?}', 'AgentController@index')->name('admin.agent.index');

        Route::get('{panel}/agent/create', 'AgentController@createEdit')->name('admin.agent.create');
        Route::get('{panel}/agent/edit/{id}', 'AgentController@createEdit')->name('admin.agent.edit');
        Route::post('{panel}/agent/store', 'AgentController@store')->name('admin.agent.store');
        Route::get('{panel}/agent/show/{id}', 'AgentController@show')->name('admin.agent.show');
        Route::delete('{panel}/agent/destroy/{id}', 'AgentController@destroy')->name('admin.agent.destroy');
        Route::post('{panel}/agent/changeStatus', 'AgentController@changeStatus')->name('admin.agent.changesStatus');
        Route::post('{panel}/agent/resetAttempt', 'AgentController@resetAttempt')->name('admin.agent.resetAttempt');
        Route::post('{panel}/agent/resetOtpAttempt', 'AgentController@resetOtpAttempt')->name('admin.agent.resetOtpAttempt');
        Route::post('{panel}/agent/changeUssdStatus', 'AgentController@changeUssdStatus')->name('admin.agent.changeUssdStatus');
        Route::post('{panel}/agent/toggle-status/{id}', 'AgentController@toggleStatus')->name('admin.agent.toggle_status');
        Route::post('{panel}/agent/toggle-referal-status/{id}', 'AgentController@toggleReferalStatus')->name('admin.agent.toggle_referal_status');
        Route::get('{panel}/referal_request', 'AgentController@referal_request')->name('admin.agent.referal_request');

        // ajax
        Route::get('{panel}/agent/user_json/{filter?}', 'AgentController@index_json')->name('admin.agent.index_json');
        Route::get('{panel}/referal_request_json', 'AgentController@referal_request_json')->name('admin.agent.referal_request_json');

        //Change password
        Route::get('{panel}/change-password', 'AgentController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'AgentController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'AgentController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'AgentController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'AgentController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'AgentController@uploadProfile')->name('admin.changeProfile.uploadprofile');

        Route::post('statecity', 'AgentController@statecity')->name('admin.agent.statecity');
        Route::post('models', 'AgentController@models')->name('admin.agent.models');
        Route::post('vehicleFuelType', 'AgentController@vehicleFuelType')->name('admin.agent.vehicleFuelType');
        Route::post('vehicleColour', 'AgentController@vehicleColour')->name('admin.agent.vehicleColour');

        Route::post('{panel}/agent/documentVerification', 'AgentController@documentVerification')->name('admin.agent.documentVerification');

        Route::get('{panel}/agent/otp/{id}', 'AgentController@otp')->name('admin.agent.otp');
        Route::get('{panel}/agent/resendotp/{id}', 'AgentController@resendotp')->name('admin.agent.resendotp');
        Route::post('{panel}/agent/sendotp', 'AgentController@sendotp')->name('admin.agent.sendotp');

        Route::post('{panel}/reset', 'AgentController@resetProfile')->name('admin.users.reset');
    }
);
