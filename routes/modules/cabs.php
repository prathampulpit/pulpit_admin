<?php

Route::group(
    [
        'namespace' => 'Cabs',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // cabs
        /******************************************/

        Route::get('{panel}/cabs', 'CabsController@index')->name('admin.cabs.index');
        Route::get('{panel}/cabs/create', 'CabsController@createEdit')->name('admin.cabs.create');
        Route::get('{panel}/cabs/edit/{id}', 'CabsController@createEdit')->name('admin.cabs.edit');
        Route::post('{panel}/cabs/store', 'CabsController@store')->name('admin.cabs.store');
        Route::get('{panel}/cabs/show/{id}', 'CabsController@show')->name('admin.cabs.show');
        Route::post('{panel}/cabs/destroy/{id?}', 'CabsController@destroy')->name('admin.cabs.destroy');
        Route::post('{panel}/cabs/changeStatus', 'CabsController@changeStatus')->name('admin.cabs.changesStatus');
        Route::post('{panel}/cabs/resetAttempt', 'CabsController@resetAttempt')->name('admin.cabs.resetAttempt');
        Route::post('{panel}/cabs/resetOtpAttempt', 'CabsController@resetOtpAttempt')->name('admin.cabs.resetOtpAttempt');  
        Route::post('{panel}/cabs/changeUssdStatus', 'CabsController@changeUssdStatus')->name('admin.cabs.changeUssdStatus');
        Route::post('{panel}/cabs/toggle-status/{id}', 'CabsController@toggleStatus')->name('admin.cabs.toggle_status');
        Route::post('{panel}/cabs/toggle-referal-status/{id}', 'CabsController@toggleReferalStatus')->name('admin.cabs.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/cabs/users_json', 'CabsController@index_json')->name('admin.cabs.index_json');
    }
);
