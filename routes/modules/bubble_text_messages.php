<?php

Route::group(
    [
        'namespace' => 'BubbleTextMessages',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Bubble Text Messages
        /******************************************/
        
        Route::get('{panel}/bubble_text_messages', 'BubbleTextMessagesController@index')->name('admin.bubble_text_messages.index');
        Route::get('{panel}/bubble_text_messages/create', 'BubbleTextMessagesController@createEdit')->name('admin.bubble_text_messages.create');
        Route::get('{panel}/bubble_text_messages/edit/{id}', 'BubbleTextMessagesController@createEdit')->name('admin.bubble_text_messages.edit');
        Route::post('{panel}/bubble_text_messages/store', 'BubbleTextMessagesController@store')->name('admin.bubble_text_messages.store');
        Route::get('{panel}/bubble_text_messages/show/{id}', 'BubbleTextMessagesController@show')->name('admin.bubble_text_messages.show');
        Route::delete('{panel}/bubble_text_messages/destroy/{id}', 'BubbleTextMessagesController@destroy')->name('admin.bubble_text_messages.destroy');
        
        // ajax
        Route::get('{panel}/bubble_text_messages/index_json', 'BubbleTextMessagesController@index_json')->name('admin.bubble_text_messages.index_json');
    }
);