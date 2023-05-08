<?php

Route::group(
        [
            'namespace' => 'Vehicles',
            'middleware' => 'auth',
            'where' => ['panel' => 'super-admin']
        ],
        function () {


            /*             * *************************************** */
            // users
            /*             * *************************************** */

            Route::get('{panel}/vehicles', 'VehiclesController@index')->name('admin.vehicles.index');
            Route::get('{panel}/vehiclesfilter/{filter?}', 'VehiclesController@index')->name('admin.vehicles.index');
            Route::post('{panel}/vehiclesfilter', 'VehiclesController@index')->name('admin.vehicles.customeFilter');
            Route::get('{panel}/vehicles/create', 'VehiclesController@createEdit')->name('admin.vehicles.create');
            Route::get('{panel}/vehicles/create/{id}', 'VehiclesController@create')->name('admin.vehicles.create.user');
            Route::get('{panel}/vehicles/edit/{id}', 'VehiclesController@createEdit')->name('admin.vehicles.edit');
            Route::post('{panel}/vehicles/store', 'VehiclesController@store')->name('admin.vehicles.store');
            Route::get('{panel}/vehicles/show/{id}', 'VehiclesController@show')->name('admin.vehicles.show');
            Route::post('{panel}/vehicles/destroy/{id?}', 'VehiclesController@destroy')->name('admin.vehicles.destroy');
            Route::post('{panel}/vehicles/changeStatus', 'VehiclesController@changeStatus')->name('admin.vehicles.changesStatus');
            Route::post('{panel}/vehicles/resetAttempt', 'VehiclesController@resetAttempt')->name('admin.vehicles.resetAttempt');
            Route::post('{panel}/vehicles/resetOtpAttempt', 'VehiclesController@resetOtpAttempt')->name('admin.vehicles.resetOtpAttempt');
            Route::post('{panel}/vehicles/changeUssdStatus', 'VehiclesController@changeUssdStatus')->name('admin.vehicles.changeUssdStatus');
            Route::post('{panel}/vehicles/toggle-status/{id}', 'VehiclesController@toggleStatus')->name('admin.vehicles.toggle_status');
            Route::post('{panel}/vehicles/toggle-referal-status/{id}', 'VehiclesController@toggleReferalStatus')->name('admin.vehicles.toggle_referal_status');
            Route::post('{panel}/vehicles/vehicleBrandModels', 'VehiclesController@vehicleBrandModels')->name('admin.vehicleBrandModels.store');
            Route::get('{panel}/vehicles/vehicleBrandModelsSelect', 'VehiclesController@vehicleBrandModelsSelect')->name('admin.vehicleBrandModels.select');
            Route::get('{panel}/vehicles/vehicleTypeBrandSelect', 'VehiclesController@vehicleTypeBrandSelect')->name('admin.vehicleTypeBrand.select');

            // ajax
            Route::get('{panel}/vehicles/users_json', 'VehiclesController@index_json')->name('admin.vehicles.index_json');

            Route::get('{panel}/vehicles/manage/{user_id}', 'VehiclesController@manage')->name('admin.vehicles.manage');
        }
);
