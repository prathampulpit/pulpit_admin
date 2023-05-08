<?php

Route::group(
    [
        'namespace' => 'OfflineCustomer',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/offlineCustomer', 'OfflineCustomerController@index')->name('admin.offlineCustomer.index');
        Route::get('{panel}/offlineCustomer/create', 'OfflineCustomerController@createEdit')->name('admin.offlineCustomer.create');
        Route::get('{panel}/offlineCustomer/edit/{id}', 'OfflineCustomerController@createEdit')->name('admin.offlineCustomer.edit');
        Route::post('{panel}/offlineCustomer/store', 'OfflineCustomerController@store')->name('admin.offlineCustomer.store');
        Route::get('{panel}/offlineCustomer/show/{id}', 'OfflineCustomerController@show')->name('admin.offlineCustomer.show');
        Route::post('{panel}/offlineCustomer/destroy/{id?}', 'OfflineCustomerController@destroy')->name('admin.offlineCustomer.destroy');
        Route::post('{panel}/offlineCustomer/changeStatus', 'OfflineCustomerController@changeStatus')->name('admin.offlineCustomer.changesStatus');
        Route::post('{panel}/offlineCustomer/resetAttempt', 'OfflineCustomerController@resetAttempt')->name('admin.offlineCustomer.resetAttempt');
        Route::post('{panel}/offlineCustomer/resetOtpAttempt', 'OfflineCustomerController@resetOtpAttempt')->name('admin.offlineCustomer.resetOtpAttempt');  
        Route::post('{panel}/offlineCustomer/changeUssdStatus', 'OfflineCustomerController@changeUssdStatus')->name('admin.offlineCustomer.changeUssdStatus');
        Route::post('{panel}/offlineCustomer/toggle-status/{id}', 'OfflineCustomerController@toggleStatus')->name('admin.offlineCustomer.toggle_status');
        Route::post('{panel}/offlineCustomer/toggle-referal-status/{id}', 'OfflineCustomerController@toggleReferalStatus')->name('admin.offlineCustomer.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/offlineCustomer/users_json', 'OfflineCustomerController@index_json')->name('admin.offlineCustomer.index_json');
    }
);
