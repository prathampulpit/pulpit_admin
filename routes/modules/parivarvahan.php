<?php

Route::group(
    [
        'namespace' => 'Parivarvahan',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/parivarvahan', 'ParivarvahanController@index')->name('admin.parivarvahan.index');
        Route::get('{panel}/parivarvahan/create', 'ParivarvahanController@createEdit')->name('admin.parivarvahan.create');
        Route::get('{panel}/parivarvahan/edit/{id}', 'ParivarvahanController@createEdit')->name('admin.parivarvahan.edit');
        Route::post('{panel}/parivarvahan/store', 'ParivarvahanController@store')->name('admin.parivarvahan.store');
        Route::get('{panel}/parivarvahan/show/{id}', 'ParivarvahanController@show')->name('admin.parivarvahan.show');
        Route::post('{panel}/parivarvahan/destroy/{id?}', 'ParivarvahanController@destroy')->name('admin.parivarvahan.destroy');
        Route::post('{panel}/parivarvahan/changeStatus', 'ParivarvahanController@changeStatus')->name('admin.parivarvahan.changesStatus');
        Route::post('{panel}/parivarvahan/resetAttempt', 'ParivarvahanController@resetAttempt')->name('admin.parivarvahan.resetAttempt');
        Route::post('{panel}/parivarvahan/resetOtpAttempt', 'ParivarvahanController@resetOtpAttempt')->name('admin.parivarvahan.resetOtpAttempt');  
        Route::post('{panel}/parivarvahan/changeUssdStatus', 'ParivarvahanController@changeUssdStatus')->name('admin.parivarvahan.changeUssdStatus');
        Route::post('{panel}/parivarvahan/toggle-status/{id}', 'ParivarvahanController@toggleStatus')->name('admin.parivarvahan.toggle_status');
        Route::post('{panel}/parivarvahan/toggle-referal-status/{id}', 'ParivarvahanController@toggleReferalStatus')->name('admin.parivarvahan.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/parivarvahan/users_json', 'ParivarvahanController@index_json')->name('admin.parivarvahan.index_json');
    }
);
