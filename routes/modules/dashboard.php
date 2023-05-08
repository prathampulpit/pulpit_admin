<?php

Route::group(
    [
        'namespace' => 'Dashboard',
        'middleware' => ['auth'],
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        Route::get('/{panel}', 'DashboardController@index')->name('admin.dashboard.index');
        Route::get('/{panel}/dashboard/{filter?}', 'DashboardController@index')->name('admin.dashboard.index');
        Route::post('/{panel}/dashboard', 'DashboardController@index')->name('admin.dashboard.filter');
        // ajax
        Route::get('{panel}/dashboard/users_json', 'DashboardController@index_json')->name('admin.dashboard.index_json');
        Route::get('{panel}/dashboard/show/{id}', 'DashboardController@show')->name('admin.dashboard.userShow');
    }
);