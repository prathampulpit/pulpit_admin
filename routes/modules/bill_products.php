<?php

Route::group(
    [
        'namespace' => 'BillProducts',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Cms Pages
        /******************************************/
        
        Route::get('{panel}/bill_products', 'BillProductsController@index')->name('admin.bill_products.index');
        Route::get('{panel}/bill_products/create', 'BillProductsController@createEdit')->name('admin.bill_products.create');
        Route::get('{panel}/bill_products/edit/{id}', 'BillProductsController@createEdit')->name('admin.bill_products.edit');
        Route::post('{panel}/bill_products/store', 'BillProductsController@store')->name('admin.bill_products.store');
        Route::get('{panel}/bill_products/show/{id}', 'BillProductsController@show')->name('admin.bill_products.show');
        Route::delete('{panel}/bill_products/destroy/{id}', 'BillProductsController@destroy')->name('admin.bill_products.destroy');
        Route::post('{panel}/bill_products/toggle-status/{id}', 'BillProductsController@toggleStatus')->name('admin.bill_products.toggle_status');
        
        // ajax
        Route::get('{panel}/bill_products/index_json', 'BillProductsController@index_json')->name('admin.bill_products.index_json');
    }
);