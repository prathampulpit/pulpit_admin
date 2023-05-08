<?php

Route::group(
    [
        'namespace' => 'Faqs',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // faqs
        /******************************************/

        Route::get('{panel}/faqs', 'FaqsController@index')->name('admin.faqs.index');
        Route::get('{panel}/faqs/create', 'FaqsController@createEdit')->name('admin.faqs.create');
        Route::get('{panel}/faqs/edit/{id}', 'FaqsController@createEdit')->name('admin.faqs.edit');
        Route::post('{panel}/faqs/store', 'FaqsController@store')->name('admin.faqs.store');
        Route::get('{panel}/faqs/show/{id}', 'FaqsController@show')->name('admin.faqs.show');
        Route::post('{panel}/faqs/destroy/{id?}', 'FaqsController@destroy')->name('admin.faqs.destroy');
        Route::post('{panel}/faqs/changeStatus', 'FaqsController@changeStatus')->name('admin.faqs.changesStatus');
        Route::post('{panel}/faqs/resetAttempt', 'FaqsController@resetAttempt')->name('admin.faqs.resetAttempt');
        Route::post('{panel}/faqs/resetOtpAttempt', 'FaqsController@resetOtpAttempt')->name('admin.faqs.resetOtpAttempt');  
        Route::post('{panel}/faqs/changeUssdStatus', 'FaqsController@changeUssdStatus')->name('admin.faqs.changeUssdStatus');
        Route::post('{panel}/faqs/toggle-status/{id}', 'FaqsController@toggleStatus')->name('admin.faqs.toggle_status');
        Route::post('{panel}/faqs/toggle-referal-status/{id}', 'FaqsController@toggleReferalStatus')->name('admin.faqs.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/faqs/users_json', 'FaqsController@index_json')->name('admin.faqs.index_json');
    }
);
