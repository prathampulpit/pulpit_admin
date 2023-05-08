<?php

Route::group(
    [
        'namespace' => 'Topics',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        /******************************************/
        // Dispute transaction topics Pages
        /******************************************/
        
        Route::get('{panel}/topics', 'TopicsController@index')->name('admin.topics.index');
        Route::get('{panel}/topics/create', 'TopicsController@createEdit')->name('admin.topics.create');
        Route::get('{panel}/topics/edit/{id}', 'TopicsController@createEdit')->name('admin.topics.edit');
        Route::post('{panel}/topics/store', 'TopicsController@store')->name('admin.topics.store');
        Route::get('{panel}/topics/show/{id}', 'TopicsController@show')->name('admin.topics.show');
        Route::delete('{panel}/topics/destroy/{id}', 'TopicsController@destroy')->name('admin.topics.destroy');
        Route::post('{panel}/topics/toggle-status/{id}', 'TopicsController@toggleStatus')->name('admin.topics.toggle_status');
        Route::post('{panel}/topics/toggle-setdefault/{id}', 'TopicsController@toggleSetdefault')->name('admin.topics.toggle-setdefault');
        
        // ajax
        Route::get('{panel}/topics/index_json', 'TopicsController@index_json')->name('admin.topics.index_json');
        
        Route::post('{panel}/topics/delete/logo', 'TopicsController@delete_icon')->name('admin.topics.delete_icon');
    }
);