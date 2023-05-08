<?php

Route::group(
    [
        'namespace' => 'Transaction',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        
        /******************************************/
        // transaction
        /******************************************/

        Route::get('{panel}/transactions', 'TransactionController@index')->name('admin.transactions.index');
        Route::get('{panel}/transactions/show/{id}', 'TransactionController@show')->name('admin.transactions.show');
        
        // ajax
        Route::get('{panel}/transactions/index_json', 'TransactionController@index_json')->name('admin.transactions.index_json');
    }
);
