<?php

Route::group(
    [
        'namespace' => 'Admin',
        'middleware' => 'auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {


        /******************************************/
        // Admins
        /******************************************/

        Route::get('{panel}/admin', 'AdminController@index')->name('admin.admin.index');
        Route::get('{panel}/admin/create', 'AdminController@createEdit')->name('admin.admin.create');
        Route::get('{panel}/admin/edit/{id}', 'AdminController@createEdit')->name('admin.admin.edit');
        Route::post('{panel}/admin/store', 'AdminController@store')->name('admin.admin.store');
        Route::get('{panel}/admin/show/{id}', 'AdminController@show')->name('admin.admin.show');
        Route::delete('{panel}/admin/destroy/{id?}', 'AdminController@destroy')->name('admin.admin.destroy');
        Route::post('{panel}/admin/changeStatus', 'AdminController@changeStatus')->name('admin.admin.changesStatus');

        // ajax
        Route::get('{panel}/admin_json', 'AdminController@index_json')->name('admin.admin.index_json');

        //Change password
        Route::get('{panel}/change-password', 'AdminController@showChangePasswordForm')->name('admin.changePassword');
        Route::post('save-change-password', 'AdminController@changePassword')->name('admin.changePassword.save');
        Route::post('save-user-change-password', 'AdminController@changePasswordUser')->name('admin.changeuserpassword.save');

        //Change profile
        Route::get('change-profile', 'AdminController@showChangeProfileForm')->name('admin.changeProfile');
        Route::post('save-change-profile', 'AdminController@changeUserProfile')->name('admin.changeProfile.save');
        Route::post('upload-profile-image', 'AdminController@uploadProfile')->name('admin.changeProfile.uploadprofile');
    }
);
