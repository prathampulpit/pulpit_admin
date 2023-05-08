<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

  /*
Route::get('/sign-up', 'Landing\SignUpController@index');


Route::get('/', 'Landing\LandingController@index');
Route::get('/rider/search_vehical', 'Landing\LandingController@search_vehical');
Route::get('/rider/search_vehical_type', 'Landing\LandingController@search_vehical_type');
Route::get('/rider/booked_trip', 'Landing\LandingController@booked_trip');

Route::get('/cancelation_refund', 'Landing\LandingController@cancelation_refund');
Route::get('/terms_condition', 'Landing\LandingController@terms_condition');
Route::get('/privacy_policy', 'Landing\LandingController@privacy_policy');

Route::get('/driver', 'Landing\DriverController@index');
Route::get('/business', 'Landing\BusinessController@index');

Route::get('/blog', 'Landing\BlogController@index');
Route::get('/blog/detail', 'Landing\BlogController@detail');
*/
Route::get('/', function () {
    return redirect('super-admin/dashboard');
});

Route::get('/check-url', function () {
    return app('url')->asset($path . '/asset', $secure);
})->name('welcome');

Route::get('/home', function () {
    return redirect('super-admin/dashboard');
})->name('home');

Route::get('/link', function () {
    Artisan::call('storage:link');
});
Route::get('/route-cache', function () {
    Artisan::call('route:cache');
});

Route::get('cms/{slug}/{lang?}', 'CmsPagesController@index')->name('cms');
Route::get('updateApprovedRequest', 'CronController@updateApprovedRequest')->name('updateApprovedRequest');
Route::get('checkDeletedStatusUsers', 'CronController@checkDeletedStatusUsers')->name('checkDeletedStatusUsers');
Route::get('users_approved', 'CronController@users_approved')->name('users_approved');
