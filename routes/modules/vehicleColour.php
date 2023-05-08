<?php

Route::group(
    [
        'namespace' => 'VehicleColour',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/vehicleColour', 'VehicleColourController@index')->name('admin.vehicleColour.index');
        Route::get('{panel}/vehicleColour/create', 'VehicleColourController@createEdit')->name('admin.vehicleColour.create');
        Route::get('{panel}/vehicleColour/edit/{id}', 'VehicleColourController@createEdit')->name('admin.vehicleColour.edit');
        Route::post('{panel}/vehicleColour/store', 'VehicleColourController@store')->name('admin.vehicleColour.store');
        Route::get('{panel}/vehicleColour/show/{id}', 'VehicleColourController@show')->name('admin.vehicleColour.show');
        Route::post('{panel}/vehicleColour/destroy/{id?}', 'VehicleColourController@destroy')->name('admin.vehicleColour.destroy');
        Route::post('{panel}/vehicleColour/changeStatus', 'VehicleColourController@changeStatus')->name('admin.vehicleColour.changesStatus');
        Route::post('{panel}/vehicleColour/resetAttempt', 'VehicleColourController@resetAttempt')->name('admin.vehicleColour.resetAttempt');
        Route::post('{panel}/vehicleColour/resetOtpAttempt', 'VehicleColourController@resetOtpAttempt')->name('admin.vehicleColour.resetOtpAttempt');  
        Route::post('{panel}/vehicleColour/changeUssdStatus', 'VehicleColourController@changeUssdStatus')->name('admin.vehicleColour.changeUssdStatus');
        Route::post('{panel}/vehicleColour/toggle-status/{id}', 'VehicleColourController@toggleStatus')->name('admin.vehicleColour.toggle_status');
        Route::post('{panel}/vehicleColour/toggle-referal-status/{id}', 'VehicleColourController@toggleReferalStatus')->name('admin.vehicleColour.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/vehicleColour/users_json', 'VehicleColourController@index_json')->name('admin.vehicleColour.index_json');
    }
);
