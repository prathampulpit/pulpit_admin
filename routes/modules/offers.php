<?php

Route::group(
    [
        'namespace' => 'Offers',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // offers
        /******************************************/    
        
        Route::get('{panel}/offers', 'OffersController@index')->name('admin.offers.index');
        Route::get('{panel}/offers/create', 'OffersController@createEdit')->name('admin.offers.create');
        Route::get('{panel}/offers/edit/{id}', 'OffersController@createEdit')->name('admin.offers.edit');
        Route::post('{panel}/offers/store', 'OffersController@store')->name('admin.offers.store');
        Route::get('{panel}/offers/show/{id}', 'OffersController@show')->name('admin.offers.show');
        Route::post('{panel}/offers/destroy/{id?}', 'OffersController@destroy')->name('admin.offers.destroy');
        Route::post('{panel}/offers/changeStatus', 'OffersController@changeStatus')->name('admin.offers.changesStatus');
        Route::post('{panel}/offers/resetAttempt', 'OffersController@resetAttempt')->name('admin.offers.resetAttempt');
        Route::post('{panel}/offers/resetOtpAttempt', 'OffersController@resetOtpAttempt')->name('admin.offers.resetOtpAttempt');  
        Route::post('{panel}/offers/changeUssdStatus', 'OffersController@changeUssdStatus')->name('admin.offers.changeUssdStatus');
        Route::post('{panel}/offers/toggle-status/{id}', 'OffersController@toggleStatus')->name('admin.offers.toggle_status');
        Route::post('{panel}/offers/toggle-referal-status/{id}', 'OffersController@toggleReferalStatus')->name('admin.offers.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/offers/users_json', 'OffersController@index_json')->name('admin.offers.index_json');
    }
);
