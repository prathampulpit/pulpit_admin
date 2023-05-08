<?php

Route::group(
    [
        'namespace' => 'ReferralMasters',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // referralMasters
        /******************************************/

        Route::get('{panel}/referralMasters', 'ReferralMastersController@index')->name('admin.referralMasters.index');
        Route::get('{panel}/referralMasters/create', 'ReferralMastersController@createEdit')->name('admin.referralMasters.create');
        Route::get('{panel}/referralMasters/edit/{id}', 'ReferralMastersController@createEdit')->name('admin.referralMasters.edit');
        Route::post('{panel}/referralMasters/store', 'ReferralMastersController@store')->name('admin.referralMasters.store');
        Route::get('{panel}/referralMasters/show/{id}', 'ReferralMastersController@show')->name('admin.referralMasters.show');
        Route::post('{panel}/referralMasters/destroy/{id?}', 'ReferralMastersController@destroy')->name('admin.referralMasters.destroy');
        Route::post('{panel}/referralMasters/changeStatus', 'ReferralMastersController@changeStatus')->name('admin.referralMasters.changesStatus');
        Route::post('{panel}/referralMasters/resetAttempt', 'ReferralMastersController@resetAttempt')->name('admin.referralMasters.resetAttempt');
        Route::post('{panel}/referralMasters/resetOtpAttempt', 'ReferralMastersController@resetOtpAttempt')->name('admin.referralMasters.resetOtpAttempt');  
        Route::post('{panel}/referralMasters/changeUssdStatus', 'ReferralMastersController@changeUssdStatus')->name('admin.referralMasters.changeUssdStatus');
        Route::post('{panel}/referralMasters/toggle-status/{id}', 'ReferralMastersController@toggleStatus')->name('admin.referralMasters.toggle_status');
        Route::post('{panel}/referralMasters/toggle-referal-status/{id}', 'ReferralMastersController@toggleReferalStatus')->name('admin.referralMasters.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/referralMasters/users_json', 'ReferralMastersController@index_json')->name('admin.referralMasters.index_json');
    }
);
