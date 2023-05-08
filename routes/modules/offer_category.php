<?php

Route::group(
    [
        'namespace' => 'OfferCategory',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // offer category
        /******************************************/

        Route::get('{panel}/offer-category', 'OfferCategoryController@index')->name('admin.offer_category.index');
        Route::get('{panel}/offer-category/create', 'OfferCategoryController@createEdit')->name('admin.offer_category.create');
        Route::get('{panel}/offer-category/edit/{id}', 'OfferCategoryController@createEdit')->name('admin.offer_category.edit');
        Route::post('{panel}/offer-category/store', 'OfferCategoryController@store')->name('admin.offer_category.store');
        Route::get('{panel}/offer-category/show/{id}', 'OfferCategoryController@show')->name('admin.offer_category.show');
        Route::delete('{panel}/offer-category/destroy/{id}', 'OfferCategoryController@destroy')->name('admin.offer_category.destroy');

        // ajax
        Route::get('{panel}/providers_json', 'OfferCategoryController@index_json')->name('admin.offer_category.index_json');

        Route::post('{panel}/offer-category/delete/icon', 'OfferCategoryController@delete_icon')->name('admin.offer_category.delete_icon');

    }
);
