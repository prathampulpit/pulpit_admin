<?php

Route::group(
    [
        'namespace' => 'DeleteUser',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Admins
        /******************************************/

        Route::get('{panel}/deleteuser', 'DeleteUserController@index')->name('admin.deleteuser.index');
        Route::get('{panel}/deleteuser/create', 'DeleteUserController@createEdit')->name('admin.deleteuser.create');
        Route::get('{panel}/deleteuser/edit/{id}', 'DeleteUserController@createEdit')->name('admin.deleteuser.edit');
        Route::post('{panel}/deleteuser/store', 'DeleteUserController@store')->name('admin.deleteuser.store');
        Route::get('{panel}/deleteuser/show/{id}', 'DeleteUserController@show')->name('admin.deleteuser.show');
        Route::post('{panel}/deleteuser/destroy', 'DeleteUserController@destroy')->name('admin.deleteuser.destroy');
        Route::post('{panel}/deleteuser/changeStatus', 'DeleteUserController@changeStatus')->name('admin.deleteuser.changesStatus');

        // ajax
     //   Route::get('{panel}/admin_json', 'DeleteUserController@index_json')->name('admin.admin.index_json');

        //Change password
        Route::get('{panel}/change-password', 'DeleteUserController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'DeleteUserController@changePassword')->name('admin.changePassword.save');
    }
);