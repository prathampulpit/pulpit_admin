<?php

Route::group(
    [
        'namespace' => 'Categories',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Category Pages
        /******************************************/
        
        Route::get('{panel}/category', 'CategoriesController@index')->name('admin.category.index');
        Route::get('{panel}/category/create', 'CategoriesController@createEdit')->name('admin.category.create');
        Route::get('{panel}/category/edit/{id}', 'CategoriesController@createEdit')->name('admin.category.edit');
        Route::post('{panel}/category/store', 'CategoriesController@store')->name('admin.category.store');
        Route::get('{panel}/category/show/{id}', 'CategoriesController@show')->name('admin.category.show');
        Route::delete('{panel}/category/destroy/{id}', 'CategoriesController@destroy')->name('admin.category.destroy');
        Route::post('{panel}/category/toggle-status/{id}', 'CategoriesController@toggleStatus')->name('admin.category.toggle_status');
        Route::post('{panel}/category/toggle-setdefault/{id}', 'CategoriesController@toggleSetdefault')->name('admin.category.toggle-setdefault');
        
        // ajax
        Route::get('{panel}/category/index_json', 'CategoriesController@index_json')->name('admin.category.index_json');
    }
);