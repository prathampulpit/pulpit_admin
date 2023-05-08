<?php

Route::group(
    [
        'namespace' => 'Setting',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        
        /******************************************/
        // transaction
        /******************************************/
        Route::get('{panel}/settings', 'SettingsController@index')->name('admin.settings.index');
        Route::post('{panel}/settings/store', 'SettingsController@store')->name('admin.settings.store');
        Route::get('{panel}/settings/edit/{id}', 'SettingsController@createEdit')->name('admin.settings.edit');
        Route::post('{panel}/settings/changeReferralStatus', 'SettingsController@changeReferralStatus')->name('admin.settings.changeReferralStatus');
        // ajax
        Route::get('{panel}/settings/index_json', 'SettingsController@index_json')->name('admin.settings.index_json');
    }
);
