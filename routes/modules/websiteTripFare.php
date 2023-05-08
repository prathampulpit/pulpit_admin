<?php

Route::group(
    [
        'namespace' => 'WebsiteTripFare',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // WebsiteTripFare Local
        /******************************************/

        Route::get('{panel}/websiteTripFare', 'WebsiteTripFareController@index')->name('admin.websiteTripFare.index');
        Route::get('{panel}/websiteTripFare/create', 'WebsiteTripFareController@createEdit')->name('admin.websiteTripFare.create');
        Route::get('{panel}/websiteTripFare/edit/{id}', 'WebsiteTripFareController@createEdit')->name('admin.websiteTripFare.edit');
        Route::post('{panel}/websiteTripFare/store', 'WebsiteTripFareController@store')->name('admin.websiteTripFare.store');
        Route::get('{panel}/websiteTripFare/show/{id}', 'WebsiteTripFareController@show')->name('admin.websiteTripFare.show');
        Route::post('{panel}/websiteTripFare/destroy/{id?}', 'WebsiteTripFareController@destroy')->name('admin.websiteTripFare.destroy');
        Route::post('{panel}/websiteTripFare/changeStatus', 'WebsiteTripFareController@changeStatus')->name('admin.websiteTripFare.changesStatus');
        Route::post('{panel}/websiteTripFare/resetAttempt', 'WebsiteTripFareController@resetAttempt')->name('admin.websiteTripFare.resetAttempt');
        Route::post('{panel}/websiteTripFare/resetOtpAttempt', 'WebsiteTripFareController@resetOtpAttempt')->name('admin.websiteTripFare.resetOtpAttempt');  
        Route::post('{panel}/websiteTripFare/changeUssdStatus', 'WebsiteTripFareController@changeUssdStatus')->name('admin.websiteTripFare.changeUssdStatus');
        Route::post('{panel}/websiteTripFare/toggle-status/{id}', 'WebsiteTripFareController@toggleStatus')->name('admin.websiteTripFare.toggle_status');
        Route::post('{panel}/websiteTripFare/toggle-referal-status/{id}', 'WebsiteTripFareController@toggleReferalStatus')->name('admin.websiteTripFare.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/websiteTripFare/users_json', 'WebsiteTripFareController@index_json')->name('admin.websiteTripFare.index_json');
        
        
        
        /******************************************/
        // WebsiteTripFare Rental
        /******************************************/

        Route::get('{panel}/websiteRentalTripFare', 'WebsiteTripFareRentalController@index')->name('admin.websiteRentalTripFare.index');
        Route::get('{panel}/websiteRentalTripFare/create', 'WebsiteTripFareRentalController@createEdit')->name('admin.websiteRentalTripFare.create');
        Route::get('{panel}/websiteRentalTripFare/edit/{id}', 'WebsiteTripFareRentalController@createEdit')->name('admin.websiteRentalTripFare.edit');
        Route::post('{panel}/websiteRentalTripFare/store', 'WebsiteTripFareRentalController@store')->name('admin.websiteRentalTripFare.store');
        Route::get('{panel}/websiteRentalTripFare/show/{id}', 'WebsiteTripFareRentalController@show')->name('admin.websiteRentalTripFare.show');
        Route::post('{panel}/websiteRentalTripFare/destroy/{id?}', 'WebsiteTripFareRentalController@destroy')->name('admin.websiteRentalTripFare.destroy');
        Route::post('{panel}/websiteRentalTripFare/changeStatus', 'WebsiteTripFareRentalController@changeStatus')->name('admin.websiteRentalTripFare.changesStatus'); 
        
        // ajax
        Route::get('{panel}/websiteRentalTripFare/users_json', 'WebsiteTripFareRentalController@index_json')->name('admin.websiteRentalTripFare.index_json');
        
        
        /******************************************/
        // WebsiteTripFare OutStation
        /******************************************/

        Route::get('{panel}/websiteOutStationTripFare', 'WebsiteTripFareOutStationController@index')->name('admin.websiteOutStationTripFare.index');
        Route::get('{panel}/websiteOutStationTripFare/create', 'WebsiteTripFareOutStationController@createEdit')->name('admin.websiteOutStationTripFare.create');
        Route::get('{panel}/websiteOutStationTripFare/edit/{id}', 'WebsiteTripFareOutStationController@createEdit')->name('admin.websiteOutStationTripFare.edit');
        Route::post('{panel}/websiteOutStationTripFare/store', 'WebsiteTripFareOutStationController@store')->name('admin.websiteOutStationTripFare.store');
        Route::get('{panel}/websiteOutStationTripFare/show/{id}', 'WebsiteTripFareOutStationController@show')->name('admin.websiteOutStationTripFare.show');
        Route::post('{panel}/websiteOutStationTripFare/destroy/{id?}', 'WebsiteTripFareOutStationController@destroy')->name('admin.websiteOutStationTripFare.destroy');
        Route::post('{panel}/websiteOutStationTripFare/changeStatus', 'WebsiteTripFareOutStationController@changeStatus')->name('admin.websiteOutStationTripFare.changesStatus'); 
        
        // ajax
        Route::get('{panel}/websiteOutStationTripFare/users_json', 'WebsiteTripFareOutStationController@index_json')->name('admin.websiteOutStationTripFare.index_json');
    }
);
