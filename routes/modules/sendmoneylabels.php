<?php

Route::group(
    [
        'namespace' => 'SendMoneyLabels',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Send Money Labels Pages
        /******************************************/
        
        Route::get('{panel}/sendmoneylabels', 'SendMoneyLabelsController@index')->name('admin.sendmoneylabels.index');
        Route::get('{panel}/sendmoneylabels/create', 'SendMoneyLabelsController@createEdit')->name('admin.sendmoneylabels.create');
        Route::get('{panel}/sendmoneylabels/edit/{id}', 'SendMoneyLabelsController@createEdit')->name('admin.sendmoneylabels.edit');
        Route::post('{panel}/sendmoneylabels/store', 'SendMoneyLabelsController@store')->name('admin.sendmoneylabels.store');
        Route::get('{panel}/sendmoneylabels/show/{id}', 'SendMoneyLabelsController@show')->name('admin.sendmoneylabels.show');
        Route::delete('{panel}/sendmoneylabels/destroy/{id}', 'SendMoneyLabelsController@destroy')->name('admin.sendmoneylabels.destroy');
        Route::post('{panel}/sendmoneylabels/toggle-status/{id}', 'SendMoneyLabelsController@toggleStatus')->name('admin.sendmoneylabels.toggle_status');
        Route::post('{panel}/sendmoneylabels/toggle-setdefault/{id}', 'SendMoneyLabelsController@toggleSetdefault')->name('admin.sendmoneylabels.toggle-setdefault');
        
        // ajax
        Route::get('{panel}/sendmoneylabels/index_json', 'SendMoneyLabelsController@index_json')->name('admin.sendmoneylabels.index_json');
        
        Route::post('{panel}/sendmoneylabels/delete/logo', 'SendMoneyLabelsController@delete_icon')->name('admin.sendmoneylabels.delete_icon');
    }
);