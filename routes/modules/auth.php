<?php

Route::group(
    [
        'namespace' => 'Auth',
        'where' => ['panel' => 'super-admin']
    ],
    function () {
        Route::get('/{panel}/login', 'LoginController@getLogin')->name('login');
        Route::post('/{panel}/login', 'LoginController@postLogin')->name('validate-login');
        Route::get('/{panel}/logout', 'LoginController@getLogout')->name('logout');
    }
);

Route::group(
    [
        'namespace' => 'Auth'
    ],
    function () {

        // activate users
        Route::get('users/activate/{token}', 'ActivateController@activate')->name('admin.users.activate');
        Route::post('users/activate', 'ActivateController@userPostActivate')->name('admin.users.post_activate');

        // forgot password
        if (config('custom.auth.forgot_password')) {

            Route::get('password/remind', 'ForgotPasswordController@forgotPassword')->name('password-reset-form');
            Route::post('password/remind', 'ForgotPasswordController@sendPasswordReminder')->name('password-reset-form-post');
            Route::get('password/reset/{token}', 'ForgotPasswordController@getReset')->name('password-reset-link');
            Route::post('password/reset', 'ForgotPasswordController@postReset')->name('password-reset-link-post');

            Route::get('forgot-password', 'ForgotPasswordController@userForgotPassword')->name('user-password-reset-form');
            Route::post('user/password/remind', 'ForgotPasswordController@userSendPasswordReminder')->name('user-password-reset-form-post');
            Route::get('profile/user/password/reset/{token}', 'ForgotPasswordController@userGetReset')->name('user-get-password-reset-link');
            Route::post('user/password/reset', 'ForgotPasswordController@userPostReset')->name('user-password-reset-link-post');
        }
    }
);
