<?php

Route::group(
    [
        'namespace' => 'AddMoneyLabels',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // AddMoneyLabels Pages
        /******************************************/
        
        Route::get('{panel}/addmoneylabels', 'AddMoneyLabelsController@index')->name('admin.addmoneylabels.index');
        Route::get('{panel}/addmoneylabels/create', 'AddMoneyLabelsController@createEdit')->name('admin.addmoneylabels.create');
        Route::get('{panel}/addmoneylabels/edit/{id}', 'AddMoneyLabelsController@createEdit')->name('admin.addmoneylabels.edit');
        Route::post('{panel}/addmoneylabels/store', 'AddMoneyLabelsController@store')->name('admin.addmoneylabels.store');
        Route::get('{panel}/addmoneylabels/show/{id}', 'AddMoneyLabelsController@show')->name('admin.addmoneylabels.show');
        Route::delete('{panel}/addmoneylabels/destroy/{id}', 'AddMoneyLabelsController@destroy')->name('admin.addmoneylabels.destroy');
        Route::post('{panel}/addmoneylabels/toggle-status/{id}', 'AddMoneyLabelsController@toggleStatus')->name('admin.addmoneylabels.toggle_status');
        Route::post('{panel}/addmoneylabels/toggle-setdefault/{id}', 'AddMoneyLabelsController@toggleSetdefault')->name('admin.addmoneylabels.toggle-setdefault');
        
        // ajax
        Route::get('{panel}/addmoneylabels/index_json', 'AddMoneyLabelsController@index_json')->name('admin.addmoneylabels.index_json');

        Route::post('{panel}/addmoneylabels/delete/logo', 'AddMoneyLabelsController@delete_icon')->name('admin.addmoneylabels.delete_icon');
    }
);