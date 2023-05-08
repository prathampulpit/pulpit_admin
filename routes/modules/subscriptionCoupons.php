<?php

Route::group(
    [
        'namespace' => 'SubscriptionCoupons',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/
        
        Route::get('{panel}/subscriptionCoupons', 'SubscriptionCouponsController@index')->name('admin.subscriptionCoupons.index');
        Route::get('{panel}/subscriptionCoupons/create', 'SubscriptionCouponsController@createEdit')->name('admin.subscriptionCoupons.create');
        Route::get('{panel}/subscriptionCoupons/edit/{id}', 'SubscriptionCouponsController@createEdit')->name('admin.subscriptionCoupons.edit');
        Route::post('{panel}/subscriptionCoupons/store', 'SubscriptionCouponsController@store')->name('admin.subscriptionCoupons.store');
        Route::get('{panel}/subscriptionCoupons/show/{id}', 'SubscriptionCouponsController@show')->name('admin.subscriptionCoupons.show');
        Route::post('{panel}/subscriptionCoupons/destroy/{id?}', 'SubscriptionCouponsController@destroy')->name('admin.subscriptionCoupons.destroy');
        Route::post('{panel}/subscriptionCoupons/changeStatus', 'SubscriptionCouponsController@changeStatus')->name('admin.subscriptionCoupons.changesStatus');
        Route::post('{panel}/subscriptionCoupons/resetAttempt', 'SubscriptionCouponsController@resetAttempt')->name('admin.subscriptionCoupons.resetAttempt');
        Route::post('{panel}/subscriptionCoupons/resetOtpAttempt', 'SubscriptionCouponsController@resetOtpAttempt')->name('admin.subscriptionCoupons.resetOtpAttempt');  
        Route::post('{panel}/subscriptionCoupons/changeUssdStatus', 'SubscriptionCouponsController@changeUssdStatus')->name('admin.subscriptionCoupons.changeUssdStatus');
        Route::post('{panel}/subscriptionCoupons/toggle-status/{id}', 'SubscriptionCouponsController@toggleStatus')->name('admin.subscriptionCoupons.toggle_status');
        Route::post('{panel}/subscriptionCoupons/toggle-referal-status/{id}', 'SubscriptionCouponsController@toggleReferalStatus')->name('admin.subscriptionCoupons.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/subscriptionCoupons/users_json', 'SubscriptionCouponsController@index_json')->name('admin.subscriptionCoupons.index_json');
        
    }
);
