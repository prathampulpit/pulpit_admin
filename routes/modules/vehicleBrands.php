<?php

Route::group(
    [
        'namespace' => 'VehicleBrands',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/vehicleBrands', 'VehicleBrandsController@index')->name('admin.vehicleBrands.index');
        Route::get('{panel}/vehicleBrands/create', 'VehicleBrandsController@createEdit')->name('admin.vehicleBrands.create');
        Route::get('{panel}/vehicleBrands/edit/{id}', 'VehicleBrandsController@createEdit')->name('admin.vehicleBrands.edit');
        Route::post('{panel}/vehicleBrands/store', 'VehicleBrandsController@store')->name('admin.vehicleBrands.store');
        Route::get('{panel}/vehicleBrands/show/{id}', 'VehicleBrandsController@show')->name('admin.vehicleBrands.show');
        Route::post('{panel}/vehicleBrands/destroy/{id?}', 'VehicleBrandsController@destroy')->name('admin.vehicleBrands.destroy');
        Route::post('{panel}/vehicleBrands/changeStatus', 'VehicleBrandsController@changeStatus')->name('admin.vehicleBrands.changesStatus');
        Route::post('{panel}/vehicleBrands/resetAttempt', 'VehicleBrandsController@resetAttempt')->name('admin.vehicleBrands.resetAttempt');
        Route::post('{panel}/vehicleBrands/resetOtpAttempt', 'VehicleBrandsController@resetOtpAttempt')->name('admin.vehicleBrands.resetOtpAttempt');  
        Route::post('{panel}/vehicleBrands/changeUssdStatus', 'VehicleBrandsController@changeUssdStatus')->name('admin.vehicleBrands.changeUssdStatus');
        Route::post('{panel}/vehicleBrands/toggle-status/{id}', 'VehicleBrandsController@toggleStatus')->name('admin.vehicleBrands.toggle_status');
        Route::post('{panel}/vehicleBrands/toggle-referal-status/{id}', 'VehicleBrandsController@toggleReferalStatus')->name('admin.vehicleBrands.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/vehicleBrands/users_json', 'VehicleBrandsController@index_json')->name('admin.vehicleBrands.index_json');
    }
);
