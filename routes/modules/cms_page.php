<?php

Route::group(
    [
        'namespace' => 'CmsPages',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Cms Pages
        /******************************************/
        
        Route::get('{panel}/cms_pages', 'CmsPagesController@index')->name('admin.cms_pages.index');
        Route::get('{panel}/cms_pages/create', 'CmsPagesController@createEdit')->name('admin.cms_pages.create');
        Route::get('{panel}/cms_pages/edit/{id}', 'CmsPagesController@createEdit')->name('admin.cms_pages.edit');
        Route::post('{panel}/cms_pages/store', 'CmsPagesController@store')->name('admin.cms_pages.store');
        Route::get('{panel}/cms_pages/show/{id}', 'CmsPagesController@show')->name('admin.cms_pages.show');
        Route::delete('{panel}/cms_pages/destroy/{id}', 'CmsPagesController@destroy')->name('admin.cms_pages.destroy');
        
        // ajax
        Route::get('{panel}/cms_pages/index_json', 'CmsPagesController@index_json')->name('admin.cms_pages.index_json');
    }
);