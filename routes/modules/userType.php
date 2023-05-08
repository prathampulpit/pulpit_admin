<?php

Route::group(
    [
        'namespace' => 'UserType',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/userType', 'UserTypeController@index')->name('admin.userType.index');
        Route::get('{panel}/userType/create', 'UserTypeController@createEdit')->name('admin.userType.create');
        Route::get('{panel}/userType/edit/{id}', 'UserTypeController@createEdit')->name('admin.userType.edit');
        Route::post('{panel}/userType/store', 'UserTypeController@store')->name('admin.userType.store');
        Route::get('{panel}/userType/show/{id}', 'UserTypeController@show')->name('admin.userType.show');
        Route::post('{panel}/userType/destroy/{id?}', 'UserTypeController@destroy')->name('admin.userType.destroy');
        Route::post('{panel}/userType/changeStatus', 'UserTypeController@changeStatus')->name('admin.userType.changesStatus');
        Route::post('{panel}/userType/resetAttempt', 'UserTypeController@resetAttempt')->name('admin.userType.resetAttempt');
        Route::post('{panel}/userType/resetOtpAttempt', 'UserTypeController@resetOtpAttempt')->name('admin.userType.resetOtpAttempt');  
        Route::post('{panel}/userType/changeUssdStatus', 'UserTypeController@changeUssdStatus')->name('admin.userType.changeUssdStatus');
        Route::post('{panel}/userType/toggle-status/{id}', 'UserTypeController@toggleStatus')->name('admin.userType.toggle_status');
        Route::post('{panel}/userType/toggle-referal-status/{id}', 'UserTypeController@toggleReferalStatus')->name('admin.userType.toggle_referal_status');
        Route::get('{panel}/referal_request', 'UserTypeController@referal_request')->name('admin.userType.referal_request');
        
        // ajax
        Route::get('{panel}/users_json', 'UserTypeController@index_json')->name('admin.userType.index_json');
    }
);
