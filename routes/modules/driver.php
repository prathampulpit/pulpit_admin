<?php

Route::group(
        [
            'namespace' => 'Driver',
            'middleware' => 'auth',
            'where' => ['panel' => 'super-admin']
        ],
        function () {


            /*             * *************************************** */
            // Driver
            /*             * *************************************** */

            //Route::get('{panel}/driver', 'DriverController@index')->name('admin.driver.index');
            //Route::get('{panel}/driversearch/{param?}', 'DriverController@index')->name('admin.driver.index');
            //Route::match(['GET', 'POST'], '{panel}/driver', 'DriverController@index')->name('admin.driver.index');
            Route::post('{panel}/driver/update', 'DriverController@driverUpdate')->name('admin.driver.update');
            Route::get('{panel}/driver', 'DriverController@index')->name('admin.driver.index');
            Route::match(['GET', 'POST'], '{panel}/driversearch', 'DriverController@index')->name('admin.driver.index');
            Route::get('{panel}/driversearch/{filter?}', 'DriverController@index')->name('admin.driver.index');

            Route::post('{panel}/driver/authorized/{id}/{data?}', 'DriverController@driverAuthorized')->name('admin.driver.authorized');
            Route::post('{panel}/driver/authorized', 'DriverController@driverAuthorizedStatus')->name('admin.driver.authorized.status');

            Route::get('{panel}/driver/create', 'DriverController@createEdit')->name('admin.driver.create');
            Route::get('{panel}/driver/edit/{id}', 'DriverController@createEdit')->name('admin.driver.edit');
            Route::post('{panel}/driver/store', 'DriverController@store')->name('admin.driver.store');
            Route::post('{panel}/driver/driverEdit', 'DriverController@driverEdit')->name('admin.driver_edit');
            Route::get('{panel}/driver/show/{id}', 'DriverController@show')->name('admin.driver.show');
            Route::delete('{panel}/driver/destroy/{id}', 'DriverController@destroy')->name('admin.driver.destroy');
            Route::post('{panel}/driver/changeStatus', 'DriverController@changeStatus')->name('admin.driver.changesStatus');
            Route::post('{panel}/driver/resetAttempt', 'DriverController@resetAttempt')->name('admin.driver.resetAttempt');
            Route::post('{panel}/driver/resetOtpAttempt', 'DriverController@resetOtpAttempt')->name('admin.driver.resetOtpAttempt');
            Route::post('{panel}/driver/changeUssdStatus', 'DriverController@changeUssdStatus')->name('admin.driver.changeUssdStatus');
            Route::post('{panel}/driver/toggle-status/{id}', 'DriverController@toggleStatus')->name('admin.driver.toggle_status');
            Route::post('{panel}/driver/toggle-referal-status/{id}', 'DriverController@toggleReferalStatus')->name('admin.driver.toggle_referal_status');
            Route::get('{panel}/referal_request', 'DriverController@referal_request')->name('admin.driver.referal_request');

            // ajax
            Route::get('{panel}/driver/user_json/{param?}', 'DriverController@index_json')->name('admin.driver.index_json');
            Route::get('{panel}/referal_request_json', 'DriverController@referal_request_json')->name('admin.driver.referal_request_json');

            //Change password
            Route::get('{panel}/change-password', 'DriverController@showChangePasswordForm')->name('admin.changePassword');
            Route::post('save-change-password', 'DriverController@changePassword')->name('admin.changePassword.save');
            Route::post('save-user-change-password', 'DriverController@changePasswordUser')->name('admin.changeuserpassword.save');

            //Change profile
            Route::get('change-profile', 'DriverController@showChangeProfileForm')->name('admin.changeProfile');
            Route::post('save-change-profile', 'DriverController@changeUserProfile')->name('admin.changeProfile.save');
            Route::post('upload-profile-image', 'DriverController@uploadProfile')->name('admin.changeProfile.uploadprofile');

            Route::post('statecity', 'DriverController@statecity')->name('admin.driver.statecity');
            Route::post('models', 'DriverController@models')->name('admin.driver.models');
            Route::post('vehicleFuelType', 'DriverController@vehicleFuelType')->name('admin.driver.vehicleFuelType');
            Route::post('vehicleColour', 'DriverController@vehicleColour')->name('admin.driver.vehicleColour');

            Route::post('{panel}/driver/documentVerification', 'DriverController@documentVerification')->name('admin.driver.documentVerification');

            Route::get('{panel}/driver/otp/{id}', 'DriverController@otp')->name('admin.driver.otp');
            Route::get('{panel}/driver/resendotp/{id}', 'DriverController@resendotp')->name('admin.driver.resendotp');
            Route::post('{panel}/driver/sendotp', 'DriverController@sendotp')->name('admin.driver.sendotp');

            Route::post('{panel}/driver/manageDriver', 'DriverController@manageDriver')->name('admin.driver.manageDriver');
            Route::post('{panel}/driver/getDrivingLicenceDetails', 'DriverController@getDrivingLicenceDetails')->name('admin.driver.getDrivingLicenceDetails');
            Route::post('{panel}/driver/save', 'DriverController@save')->name('admin.driver.save');
        }
);
