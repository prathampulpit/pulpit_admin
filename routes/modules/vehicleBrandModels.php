<?php

Route::group(
    [
        'namespace' => 'VehicleBrandModels',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/vehicleBrandModels', 'VehicleBrandModelsController@index')->name('admin.vehicleBrandModels.index');
        Route::get('{panel}/vehicleBrandModels/create', 'VehicleBrandModelsController@createEdit')->name('admin.vehicleBrandModels.create');
        Route::get('{panel}/vehicleBrandModels/edit/{id}', 'VehicleBrandModelsController@createEdit')->name('admin.vehicleBrandModels.edit');
        Route::post('{panel}/vehicleBrandModels/store', 'VehicleBrandModelsController@store')->name('admin.vehicleBrandModels.store');
        Route::get('{panel}/vehicleBrandModels/show/{id}', 'VehicleBrandModelsController@show')->name('admin.vehicleBrandModels.show');
        Route::post('{panel}/vehicleBrandModels/destroy/{id?}', 'VehicleBrandModelsController@destroy')->name('admin.vehicleBrandModels.destroy');
        Route::post('{panel}/vehicleBrandModels/changeStatus', 'VehicleBrandModelsController@changeStatus')->name('admin.vehicleBrandModels.changesStatus');
        Route::post('{panel}/vehicleBrandModels/resetAttempt', 'VehicleBrandModelsController@resetAttempt')->name('admin.vehicleBrandModels.resetAttempt');
        Route::post('{panel}/vehicleBrandModels/resetOtpAttempt', 'VehicleBrandModelsController@resetOtpAttempt')->name('admin.vehicleBrandModels.resetOtpAttempt');  
        Route::post('{panel}/vehicleBrandModels/changeUssdStatus', 'VehicleBrandModelsController@changeUssdStatus')->name('admin.vehicleBrandModels.changeUssdStatus');
        Route::post('{panel}/vehicleBrandModels/toggle-status/{id}', 'VehicleBrandModelsController@toggleStatus')->name('admin.vehicleBrandModels.toggle_status');
        Route::post('{panel}/vehicleBrandModels/toggle-referal-status/{id}', 'VehicleBrandModelsController@toggleReferalStatus')->name('admin.vehicleBrandModels.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/vehicleBrandModels/users_json', 'VehicleBrandModelsController@index_json')->name('admin.vehicleBrandModels.index_json');
    }
);
