<?php

Route::group(
    [
        'namespace' => 'Cms',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Cms Content
        /******************************************/

        Route::get('{panel}/cms', 'CmsController@index')->name('admin.cms.index');
        Route::get('{panel}/cms/create', 'CmsController@createEdit')->name('admin.cms.create');
        Route::get('{panel}/cms/edit/{id}', 'CmsController@createEdit')->name('admin.cms.edit');
        Route::post('{panel}/cms/store', 'CmsController@store')->name('admin.cms.store');
        Route::get('{panel}/cms/show/{id}', 'CmsController@show')->name('admin.cms.show');
        Route::delete('{panel}/cms/destroy/{id}', 'CmsController@destroy')->name('admin.cms.destroy');

        // ajax
        Route::get('{panel}/cms_json', 'CmsController@index_json')->name('admin.cms.index_json');

        /******************************************/
        // Faqs
        /******************************************/

        Route::get('{panel}/faqs', 'FaqController@index')->name('admin.faqs.index');
        Route::get('{panel}/faqs/create', 'FaqController@createEdit')->name('admin.faqs.create');
        Route::get('{panel}/faqs/edit/{id}', 'FaqController@createEdit')->name('admin.faqs.edit');
        Route::post('{panel}/faqs/store', 'FaqController@store')->name('admin.faqs.store');
        Route::get('{panel}/faqs/show/{id}', 'FaqController@show')->name('admin.faqs.show');
        Route::delete('{panel}/faqs/destroy/{id}', 'FaqController@destroy')->name('admin.faqs.destroy');

        // ajax
        Route::get('{panel}/faqs_json', 'FaqController@index_json')->name('admin.faqs.index_json');

    }
);
