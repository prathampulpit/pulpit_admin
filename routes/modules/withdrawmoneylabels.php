<?php

Route::group(
    [
        'namespace' => 'WithdrawMoneyLabels',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // AddMoneyLabels Pages
        /******************************************/
        
        Route::get('{panel}/withdrawmoneylabels', 'WithdrawMoneyLabelsController@index')->name('admin.withdrawmoneylabels.index');
        Route::get('{panel}/withdrawmoneylabels/create', 'WithdrawMoneyLabelsController@createEdit')->name('admin.withdrawmoneylabels.create');
        Route::get('{panel}/withdrawmoneylabels/edit/{id}', 'WithdrawMoneyLabelsController@createEdit')->name('admin.withdrawmoneylabels.edit');
        Route::post('{panel}/withdrawmoneylabels/store', 'WithdrawMoneyLabelsController@store')->name('admin.withdrawmoneylabels.store');
        Route::get('{panel}/withdrawmoneylabels/show/{id}', 'WithdrawMoneyLabelsController@show')->name('admin.withdrawmoneylabels.show');
        Route::delete('{panel}/withdrawmoneylabels/destroy/{id}', 'WithdrawMoneyLabelsController@destroy')->name('admin.withdrawmoneylabels.destroy');
        Route::post('{panel}/withdrawmoneylabels/toggle-status/{id}', 'WithdrawMoneyLabelsController@toggleStatus')->name('admin.withdrawmoneylabels.toggle_status');
        Route::post('{panel}/withdrawmoneylabels/toggle-setdefault/{id}', 'WithdrawMoneyLabelsController@toggleSetdefault')->name('admin.withdrawmoneylabels.toggle-setdefault');
        
        // ajax
        Route::get('{panel}/withdrawmoneylabels/index_json', 'WithdrawMoneyLabelsController@index_json')->name('admin.withdrawmoneylabels.index_json');
        
        Route::post('{panel}/withdrawmoneylabels/delete/logo', 'WithdrawMoneyLabelsController@delete_icon')->name('admin.withdrawmoneylabels.delete_icon');
    }
);