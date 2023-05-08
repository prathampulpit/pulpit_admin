<?php

Route::group(
    [
        'namespace' => 'ClaimedRewards',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/claimedRewards', 'ClaimedRewardsController@index')->name('admin.claimedRewards.index');
        Route::get('{panel}/claimedRewards/create', 'ClaimedRewardsController@createEdit')->name('admin.claimedRewards.create');
        Route::get('{panel}/claimedRewards/edit/{id}', 'ClaimedRewardsController@createEdit')->name('admin.claimedRewards.edit');
        Route::post('{panel}/claimedRewards/store', 'ClaimedRewardsController@store')->name('admin.claimedRewards.store');
        Route::get('{panel}/claimedRewards/show/{id}', 'ClaimedRewardsController@show')->name('admin.claimedRewards.show');
        Route::post('{panel}/claimedRewards/destroy/{id?}', 'ClaimedRewardsController@destroy')->name('admin.claimedRewards.destroy');
        Route::post('{panel}/claimedRewards/changeStatus', 'ClaimedRewardsController@changeStatus')->name('admin.claimedRewards.changesStatus');
        Route::post('{panel}/claimedRewards/resetAttempt', 'ClaimedRewardsController@resetAttempt')->name('admin.claimedRewards.resetAttempt');
        Route::post('{panel}/claimedRewards/resetOtpAttempt', 'ClaimedRewardsController@resetOtpAttempt')->name('admin.claimedRewards.resetOtpAttempt');  
        Route::post('{panel}/claimedRewards/changeUssdStatus', 'ClaimedRewardsController@changeUssdStatus')->name('admin.claimedRewards.changeUssdStatus');
        Route::post('{panel}/claimedRewards/toggle-status/{id}', 'ClaimedRewardsController@toggleStatus')->name('admin.claimedRewards.toggle_status');
        Route::post('{panel}/claimedRewards/toggle-referal-status/{id}', 'ClaimedRewardsController@toggleReferalStatus')->name('admin.claimedRewards.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/claimedRewards/users_json', 'ClaimedRewardsController@index_json')->name('admin.claimedRewards.index_json');
    }
);
