<?php

Route::group(
    [
        'namespace' => 'SubscriptionPlans',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Subscription Plans
        /******************************************/

        Route::get('{panel}/subscriptionPlans', 'SubscriptionPlansController@index')->name('admin.subscriptionPlans.index');
        Route::get('{panel}/subscriptionPlans/create', 'SubscriptionPlansController@createEdit')->name('admin.subscriptionPlans.create');
        Route::get('{panel}/subscriptionPlans/edit/{id}', 'SubscriptionPlansController@createEdit')->name('admin.subscriptionPlans.edit');
        Route::post('{panel}/subscriptionPlans/store', 'SubscriptionPlansController@store')->name('admin.subscriptionPlans.store');
        Route::get('{panel}/subscriptionPlans/show/{id}', 'SubscriptionPlansController@show')->name('admin.subscriptionPlans.show');
        Route::post('{panel}/subscriptionPlans/destroy/{id?}', 'SubscriptionPlansController@destroy')->name('admin.subscriptionPlans.destroy');
        Route::post('{panel}/subscriptionPlans/changeStatus', 'SubscriptionPlansController@changeStatus')->name('admin.subscriptionPlans.changesStatus');
        Route::post('{panel}/subscriptionPlans/resetAttempt', 'SubscriptionPlansController@resetAttempt')->name('admin.subscriptionPlans.resetAttempt');
        Route::post('{panel}/subscriptionPlans/resetOtpAttempt', 'SubscriptionPlansController@resetOtpAttempt')->name('admin.subscriptionPlans.resetOtpAttempt');  
        Route::post('{panel}/subscriptionPlans/changeUssdStatus', 'SubscriptionPlansController@changeUssdStatus')->name('admin.subscriptionPlans.changeUssdStatus');
        Route::post('{panel}/subscriptionPlans/toggle-status/{id}', 'SubscriptionPlansController@toggleStatus')->name('admin.subscriptionPlans.toggle_status');
        Route::post('{panel}/subscriptionPlans/toggle-referal-status/{id}', 'SubscriptionPlansController@toggleReferalStatus')->name('admin.subscriptionPlans.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/subscriptionPlans/users_json', 'SubscriptionPlansController@index_json')->name('admin.subscriptionPlans.index_json');
    }
);
