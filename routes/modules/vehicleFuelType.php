<?php

Route::group(
    [
        'namespace' => 'VehicleFuelType',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/
        
        Route::get('{panel}/vehicleFuelType', 'VehicleFuelTypeController@index')->name('admin.vehicleFuelType.index');
        Route::get('{panel}/vehicleFuelType/create', 'VehicleFuelTypeController@createEdit')->name('admin.vehicleFuelType.create');
        Route::get('{panel}/vehicleFuelType/edit/{id}', 'VehicleFuelTypeController@createEdit')->name('admin.vehicleFuelType.edit');
        Route::post('{panel}/vehicleFuelType/store', 'VehicleFuelTypeController@store')->name('admin.vehicleFuelType.store');
        Route::get('{panel}/vehicleFuelType/show/{id}', 'VehicleFuelTypeController@show')->name('admin.vehicleFuelType.show');
        Route::post('{panel}/vehicleFuelType/destroy/{id?}', 'VehicleFuelTypeController@destroy')->name('admin.vehicleFuelType.destroy');
        Route::post('{panel}/vehicleFuelType/changeStatus', 'VehicleFuelTypeController@changeStatus')->name('admin.vehicleFuelType.changesStatus');
        Route::post('{panel}/vehicleFuelType/resetAttempt', 'VehicleFuelTypeController@resetAttempt')->name('admin.vehicleFuelType.resetAttempt');
        Route::post('{panel}/vehicleFuelType/resetOtpAttempt', 'VehicleFuelTypeController@resetOtpAttempt')->name('admin.vehicleFuelType.resetOtpAttempt');  
        Route::post('{panel}/vehicleFuelType/changeUssdStatus', 'VehicleFuelTypeController@changeUssdStatus')->name('admin.vehicleFuelType.changeUssdStatus');
        Route::post('{panel}/vehicleFuelType/toggle-status/{id}', 'VehicleFuelTypeController@toggleStatus')->name('admin.vehicleFuelType.toggle_status');
        Route::post('{panel}/vehicleFuelType/toggle-referal-status/{id}', 'VehicleFuelTypeController@toggleReferalStatus')->name('admin.vehicleFuelType.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/vehicleFuelType/users_json', 'VehicleFuelTypeController@index_json')->name('admin.vehicleFuelType.index_json');
    }
);
