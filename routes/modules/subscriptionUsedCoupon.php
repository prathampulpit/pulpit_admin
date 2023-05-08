<?php

Route::group(
    [
        'namespace' => 'SubscriptionUsedCoupon',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/
        
        Route::get('{panel}/subscriptionUsedCoupon', 'SubscriptionUsedCouponController@index')->name('admin.subscriptionUsedCoupon.index');
        Route::get('{panel}/subscriptionUsedCoupon/create', 'SubscriptionUsedCouponController@createEdit')->name('admin.subscriptionUsedCoupon.create');
        Route::get('{panel}/subscriptionUsedCoupon/edit/{id}', 'SubscriptionUsedCouponController@createEdit')->name('admin.subscriptionUsedCoupon.edit');
        Route::post('{panel}/subscriptionUsedCoupon/store', 'SubscriptionUsedCouponController@store')->name('admin.subscriptionUsedCoupon.store');
        Route::get('{panel}/subscriptionUsedCoupon/show/{id}', 'SubscriptionUsedCouponController@show')->name('admin.subscriptionUsedCoupon.show');
        Route::post('{panel}/subscriptionUsedCoupon/destroy/{id?}', 'SubscriptionUsedCouponController@destroy')->name('admin.subscriptionUsedCoupon.destroy');
        Route::post('{panel}/subscriptionUsedCoupon/changeStatus', 'SubscriptionUsedCouponController@changeStatus')->name('admin.subscriptionUsedCoupon.changesStatus');
        Route::post('{panel}/subscriptionUsedCoupon/resetAttempt', 'SubscriptionUsedCouponController@resetAttempt')->name('admin.subscriptionUsedCoupon.resetAttempt');
        Route::post('{panel}/subscriptionUsedCoupon/resetOtpAttempt', 'SubscriptionUsedCouponController@resetOtpAttempt')->name('admin.subscriptionUsedCoupon.resetOtpAttempt');  
        Route::post('{panel}/subscriptionUsedCoupon/changeUssdStatus', 'SubscriptionUsedCouponController@changeUssdStatus')->name('admin.subscriptionUsedCoupon.changeUssdStatus');
        Route::post('{panel}/subscriptionUsedCoupon/toggle-status/{id}', 'SubscriptionUsedCouponController@toggleStatus')->name('admin.subscriptionUsedCoupon.toggle_status');
        Route::post('{panel}/subscriptionUsedCoupon/toggle-referal-status/{id}', 'SubscriptionUsedCouponController@toggleReferalStatus')->name('admin.subscriptionUsedCoupon.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/subscriptionUsedCoupon/users_json', 'SubscriptionUsedCouponController@index_json')->name('admin.subscriptionUsedCoupon.index_json');
    }
);
