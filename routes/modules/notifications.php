<?php

Route::group(
    [
        'namespace' => 'Notifications',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // notifications
        /******************************************/

        Route::get('{panel}/notifications', 'NotificationsController@index')->name('admin.notifications.index');
        Route::get('{panel}/notifications/create', 'NotificationsController@createEdit')->name('admin.notifications.create');
        Route::get('{panel}/notifications/edit/{id}', 'NotificationsController@createEdit')->name('admin.notifications.edit');
        Route::post('{panel}/notifications/store', 'NotificationsController@store')->name('admin.notifications.store');
        Route::get('{panel}/notifications/show/{id}', 'NotificationsController@show')->name('admin.notifications.show');
        Route::post('{panel}/notifications/destroy/{id?}', 'NotificationsController@destroy')->name('admin.notifications.destroy');
        Route::post('{panel}/notifications/changeStatus', 'NotificationsController@changeStatus')->name('admin.notifications.changesStatus');
        Route::post('{panel}/notifications/resetAttempt', 'NotificationsController@resetAttempt')->name('admin.notifications.resetAttempt');
        Route::post('{panel}/notifications/resetOtpAttempt', 'NotificationsController@resetOtpAttempt')->name('admin.notifications.resetOtpAttempt');  
        Route::post('{panel}/notifications/changeUssdStatus', 'NotificationsController@changeUssdStatus')->name('admin.notifications.changeUssdStatus');
        Route::post('{panel}/notifications/toggle-status/{id}', 'NotificationsController@toggleStatus')->name('admin.notifications.toggle_status');
        Route::post('{panel}/notifications/toggle-referal-status/{id}', 'NotificationsController@toggleReferalStatus')->name('admin.notifications.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/notifications/users_json', 'NotificationsController@index_json')->name('admin.notifications.index_json');
    }
);
