<?php

Route::group(
    [
        'namespace' => 'PolygonRecords',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // users
        /******************************************/

        Route::get('{panel}/polygonRecords', 'PolygonRecordsController@index')->name('admin.polygonRecords.index');
        Route::get('{panel}/polygonRecords/create', 'PolygonRecordsController@createEdit')->name('admin.polygonRecords.create');
        Route::get('{panel}/polygonRecords/edit/{id}', 'PolygonRecordsController@createEdit')->name('admin.polygonRecords.edit');
        Route::post('{panel}/polygonRecords/store', 'PolygonRecordsController@store')->name('admin.polygonRecords.store');
        Route::get('{panel}/polygonRecords/show/{id}', 'PolygonRecordsController@show')->name('admin.polygonRecords.show');
        Route::post('{panel}/polygonRecords/destroy/{id?}', 'PolygonRecordsController@destroy')->name('admin.polygonRecords.destroy');
        Route::post('{panel}/polygonRecords/changeStatus', 'PolygonRecordsController@changeStatus')->name('admin.polygonRecords.changesStatus');
        Route::post('{panel}/polygonRecords/resetAttempt', 'PolygonRecordsController@resetAttempt')->name('admin.polygonRecords.resetAttempt');
        Route::post('{panel}/polygonRecords/resetOtpAttempt', 'PolygonRecordsController@resetOtpAttempt')->name('admin.polygonRecords.resetOtpAttempt');  
        Route::post('{panel}/polygonRecords/changeUssdStatus', 'PolygonRecordsController@changeUssdStatus')->name('admin.polygonRecords.changeUssdStatus');
        Route::post('{panel}/polygonRecords/toggle-status/{id}', 'PolygonRecordsController@toggleStatus')->name('admin.polygonRecords.toggle_status');
        Route::post('{panel}/polygonRecords/toggle-referal-status/{id}', 'PolygonRecordsController@toggleReferalStatus')->name('admin.polygonRecords.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/polygonRecords/users_json', 'PolygonRecordsController@index_json')->name('admin.polygonRecords.index_json');
    }
);
