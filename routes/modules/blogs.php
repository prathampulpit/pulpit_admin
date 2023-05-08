<?php

Route::group(
        [
            'namespace' => 'Blogs',
            'middleware' => 'auth',
            'where' => ['panel' => 'super-admin']
        ],
        function () { 
            /*             * *************************************** */
            // Blogs Content
            /*             * *************************************** */

            Route::get('{panel}/blogs', 'BlogsController@index')->name('admin.blogs.index');

            Route::get('{panel}/blogs/create', 'BlogsController@createEdit')->name('admin.blogs.create');
            Route::get('{panel}/blogs/edit/{id}', 'BlogsController@createEdit')->name('admin.blogs.edit');
            Route::post('{panel}/blogs/store', 'BlogsController@store')->name('admin.blogs.store');
            Route::get('{panel}/blogs/show/{id}', 'BlogsController@show')->name('admin.blogs.show');
            Route::post('{panel}/blogs/destroy/{id?}', 'BlogsController@destroy')->name('admin.blogs.destroy');

 //        // ajax
            Route::get('{panel}/blogs/index_json', 'BlogsController@index_json')->name('admin.blogs.index_json'); 
        }
);
