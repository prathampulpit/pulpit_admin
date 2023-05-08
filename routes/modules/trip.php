<?php

use App\Http\Controllers\Trip\TripController;

Route::group(
        [
            'namespace' => 'Trip',
            'middleware' => 'auth',
            'where' => ['panel' => 'super-admin']
        ],
        function () {
            /*             * *************************************** */
            // users
            /*             * *************************************** */

            Route::get('/trip/delete/{id}', 'TripController@Trip_data_delete')->name('admin.trip.delete');
            Route::get('/super-admin/trip/deleted_data/{manually_trip}', 'TripController@trip_delete')->name('trip_delete');
            Route::match(array('GET', 'POST'), '/admin/trip/filter/{id}', 'TripController@trip_filter')->name('trip_filter');
            Route::match(array('GET', 'POST'), '/admin/trip/filter/{id}/{trip_for}', 'TripController@trip_filter')->name('trip_filter');
            Route::get('{panel}/trip/stateCode', 'TripController@stateCode')->name('admin.trip.stateCode');
            Route::get('{panel}/trip/km_location', 'TripController@km_location')->name('admin.km_location');

            Route::get('{panel}/trip', 'TripController@index')->name('admin.trip.index');
            Route::get('{panel}/customer-trip', 'TripController@customer_trip')->name('admin.customer_trip.index');
            Route::get('{panel}/completetrip/{param?}', 'TripController@index')->name('admin.trip.index');
            Route::post('{panel}/trip/create/data', 'TripController@tripCreate')->name('admin.trip.tripCreate');
            Route::get('{panel}/trip/create', 'TripController@createEdit')->name('admin.trip.create');
            Route::get('{panel}/trip/edit/{id}', 'TripController@createEdit')->name('admin.trip.edit');
            Route::post('{panel}/trip/store', 'TripController@store')->name('admin.trip.store');
            Route::post('/trip/store1', 'TripController@store1')->name('store');
            Route::get('{panel}/trip/show/{id}', 'TripController@show')->name('admin.trip.show');
            Route::match(array('GET', 'POST'), '{panel}/trip/destroy/{id?}', 'TripController@destroy')->name('admin.trip.destroy');
            Route::post('{panel}/trip/changeStatus', 'TripController@changeStatus')->name('admin.trip.changesStatus');
            Route::post('{panel}/trip/resetAttempt', 'TripController@resetAttempt')->name('admin.trip.resetAttempt');
            Route::post('{panel}/trip/resetOtpAttempt', 'TripController@resetOtpAttempt')->name('admin.trip.resetOtpAttempt');
            Route::post('{panel}/trip/changeUssdStatus', 'TripController@changeUssdStatus')->name('admin.trip.changeUssdStatus');
            Route::post('{panel}/trip/toggle-status/{id}', 'TripController@toggleStatus')->name('admin.trip.toggle_status');
            Route::post('{panel}/trip/toggle-referal-status/{id}', 'TripController@toggleReferalStatus')->name('admin.trip.toggle_referal_status');
            // ajax
            Route::get('{panel}/trip/users_json/{param?}', 'TripController@index_json')->name('admin.trip.index_json');
            Route::get('{panel}/trip/customer_json/{param?}', 'TripController@index_json_customer')->name('admin.trip.index_json_customer');

            // ------------------- Trip Offline ----------------------------
            Route::get('{panel}/customer-trip-offline', 'TripController@customer_trip_offline')->name('admin.customer_trip.customer_trip_offline');
            Route::get('{panel}/trip/customer-trip-offline/show/{id}', 'TripController@customer_trip_offline_details')->name('admin.customer_trip.customer_trip_offline_details');
            
            // ajax
            Route::get('{panel}/trip/customer_trip_offline_json', 'TripController@customer_trip_offline_json')->name('admin.trip.customer_trip_offline_json');
            Route::post('{panel}/trip/customer_trip_offline/update_status', 'TripController@customer_trip_offline_update_status')->name('admin.trip.customer_trip_offline.update_status');
            
            
            // ------------------- Trip Offline ----------------------------
            Route::get('{panel}/customer-trip-offline-visitor', 'TripController@customer_trip_offline_visitor')->name('admin.customer_trip.customer_trip_offline_visitor');
            Route::get('{panel}/trip/customer-trip-offline-visitor/show/{id}', 'TripController@customer_trip_offline_visitor_details')->name('admin.customer_trip.customer_trip_offline_visitor_details');
            
            // ajax
            Route::get('{panel}/trip/customer_trip_offline_visitor_json', 'TripController@customer_trip_offline_visitor_json')->name('admin.trip.customer_trip_offline_visitor_json');
            Route::post('{panel}/trip/customer_trip_offline_visitor/update_status', 'TripController@customer_trip_offline_visitor_update_status')->name('admin.trip.customer_trip_offline_visitor.update_status');
            
            
            Route::get('{panel}/trip/checkFraud', 'TripController@checkFraud')->name('admin.trip.checkFraud');
            Route::post('{panel}/trip/fraud_store', 'TripController@fraud_store')->name('admin.trip.fraud_store');
         }
);
