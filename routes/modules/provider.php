<?php

Route::group(
    [
        'namespace' => 'Provider',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // providers
        /******************************************/

        Route::get('{panel}/providers', 'ProviderController@index')->name('admin.providers.index');
        Route::get('{panel}/providers/create', 'ProviderController@createEdit')->name('admin.providers.create');
        Route::get('{panel}/providers/edit/{id}', 'ProviderController@createEdit')->name('admin.providers.edit');
        Route::post('{panel}/providers/store', 'ProviderController@store')->name('admin.providers.store');
        Route::get('{panel}/providers/show/{id}', 'ProviderController@show')->name('admin.providers.show');
        Route::delete('{panel}/providers/destroy/{id}', 'ProviderController@destroy')->name('admin.providers.destroy');

        // ajax
        Route::get('{panel}/index_json', 'ProviderController@index_json')->name('admin.providers.index_json');

        Route::post('{panel}/providers/delete/logo', 'ProviderController@delete_logo')->name('admin.providers.delete_logo');

    }
);
