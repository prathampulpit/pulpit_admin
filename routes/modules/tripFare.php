<?php

Route::group(
    [
        'namespace' => 'TripFare',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // tripFare
        /******************************************/

        Route::get('{panel}/tripFare', 'TripFareController@index')->name('admin.tripFare.index');
        Route::get('{panel}/tripFare/create', 'TripFareController@createEdit')->name('admin.tripFare.create');
        Route::get('{panel}/tripFare/edit/{id}', 'TripFareController@createEdit')->name('admin.tripFare.edit');
        Route::post('{panel}/tripFare/store', 'TripFareController@store')->name('admin.tripFare.store');
        Route::get('{panel}/tripFare/show/{id}', 'TripFareController@show')->name('admin.tripFare.show');
        Route::post('{panel}/tripFare/destroy/{id?}', 'TripFareController@destroy')->name('admin.tripFare.destroy');
        Route::post('{panel}/tripFare/changeStatus', 'TripFareController@changeStatus')->name('admin.tripFare.changesStatus');
        Route::post('{panel}/tripFare/resetAttempt', 'TripFareController@resetAttempt')->name('admin.tripFare.resetAttempt');
        Route::post('{panel}/tripFare/resetOtpAttempt', 'TripFareController@resetOtpAttempt')->name('admin.tripFare.resetOtpAttempt');  
        Route::post('{panel}/tripFare/changeUssdStatus', 'TripFareController@changeUssdStatus')->name('admin.tripFare.changeUssdStatus');
        Route::post('{panel}/tripFare/toggle-status/{id}', 'TripFareController@toggleStatus')->name('admin.tripFare.toggle_status');
        Route::post('{panel}/tripFare/toggle-referal-status/{id}', 'TripFareController@toggleReferalStatus')->name('admin.tripFare.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/tripFare/users_json', 'TripFareController@index_json')->name('admin.tripFare.index_json');
    }
);
