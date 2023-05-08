<?php

Route::group(
    [
        'namespace' => 'VehicleTypes',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/
        
        Route::get('{panel}/vehicleTypes', 'VehicleTypesController@index')->name('admin.vehicleTypes.index');
        Route::get('{panel}/vehicleTypes/create', 'VehicleTypesController@createEdit')->name('admin.vehicleTypes.create');
        Route::get('{panel}/vehicleTypes/edit/{id}', 'VehicleTypesController@createEdit')->name('admin.vehicleTypes.edit');
        Route::post('{panel}/vehicleTypes/store', 'VehicleTypesController@store')->name('admin.vehicleTypes.store');
        Route::get('{panel}/vehicleTypes/show/{id}', 'VehicleTypesController@show')->name('admin.vehicleTypes.show');
        Route::post('{panel}/vehicleTypes/destroy/{id?}', 'VehicleTypesController@destroy')->name('admin.vehicleTypes.destroy');
        Route::post('{panel}/vehicleTypes/changeStatus', 'VehicleTypesController@changeStatus')->name('admin.vehicleTypes.changesStatus');
        Route::post('{panel}/vehicleTypes/resetAttempt', 'VehicleTypesController@resetAttempt')->name('admin.vehicleTypes.resetAttempt');
        Route::post('{panel}/vehicleTypes/resetOtpAttempt', 'VehicleTypesController@resetOtpAttempt')->name('admin.vehicleTypes.resetOtpAttempt');  
        Route::post('{panel}/vehicleTypes/changeUssdStatus', 'VehicleTypesController@changeUssdStatus')->name('admin.vehicleTypes.changeUssdStatus');
        Route::post('{panel}/vehicleTypes/toggle-status/{id}', 'VehicleTypesController@toggleStatus')->name('admin.vehicleTypes.toggle_status');
        Route::post('{panel}/vehicleTypes/toggle-referal-status/{id}', 'VehicleTypesController@toggleReferalStatus')->name('admin.vehicleTypes.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/vehicleTypes/users_json', 'VehicleTypesController@index_json')->name('admin.vehicleTypes.index_json');
    }
);
