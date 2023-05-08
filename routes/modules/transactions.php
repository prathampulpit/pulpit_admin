<?php

Route::group(
    [
        'namespace' => 'Transactions',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // transactions
        /******************************************/

        Route::get('{panel}/transactions', 'TransactionsController@index')->name('admin.transactions.index');
        Route::get('{panel}/transactions/create', 'TransactionsController@createEdit')->name('admin.transactions.create');
        Route::get('{panel}/transactions/edit/{id}', 'TransactionsController@createEdit')->name('admin.transactions.edit');
        Route::post('{panel}/transactions/store', 'TransactionsController@store')->name('admin.transactions.store');
        Route::get('{panel}/transactions/show/{id}', 'TransactionsController@show')->name('admin.transactions.show');
        Route::post('{panel}/transactions/destroy/{id?}', 'TransactionsController@destroy')->name('admin.transactions.destroy');
        Route::post('{panel}/transactions/changeStatus', 'TransactionsController@changeStatus')->name('admin.transactions.changesStatus');
        Route::post('{panel}/transactions/resetAttempt', 'TransactionsController@resetAttempt')->name('admin.transactions.resetAttempt');
        Route::post('{panel}/transactions/resetOtpAttempt', 'TransactionsController@resetOtpAttempt')->name('admin.transactions.resetOtpAttempt');  
        Route::post('{panel}/transactions/changeUssdStatus', 'TransactionsController@changeUssdStatus')->name('admin.transactions.changeUssdStatus');
        Route::post('{panel}/transactions/toggle-status/{id}', 'TransactionsController@toggleStatus')->name('admin.transactions.toggle_status');
        Route::post('{panel}/transactions/toggle-referal-status/{id}', 'TransactionsController@toggleReferalStatus')->name('admin.transactions.toggle_referal_status');
        
        // ajax
        Route::get('{panel}/transactions/users_json', 'TransactionsController@index_json')->name('admin.transactions.index_json');
    }
);
