<?php

Route::group(
        [
            'namespace' => 'Messages',
            'middleware' => 'auth',
            'where' => ['panel' => 'super-admin']
        ],
        function () {


            /*             * *************************************** */
            // Messages Content
            /*             * *************************************** */

            Route::get('{panel}/messages', 'MessagesController@index')->name('admin.messages.index');

            Route::get('{panel}/messages/create', 'MessagesController@createEdit')->name('admin.messages.create');
            Route::get('{panel}/messages/edit/{id}', 'MessagesController@createEdit')->name('admin.messages.edit');
            Route::post('{panel}/messages/store', 'MessagesController@store')->name('admin.messages.store');
            Route::get('{panel}/messages/show/{id}', 'MessagesController@show')->name('admin.messages.show');
            Route::post('{panel}/messages/destroy/{id?}', 'MessagesController@destroy')->name('admin.messages.destroy'); 
            
//        // ajax
            Route::get('{panel}/messages/index_json', 'MessagesController@index_json')->name('admin.messages.index_json');
 
        }
);
