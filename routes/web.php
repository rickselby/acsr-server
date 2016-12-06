<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication
Route::group(['prefix' => 'auth', 'namespace' => 'Auth'], function() {
    Route::get('logout', 'AuthController@logout')->name('auth.logout');
    Route::get('{provider}/callback', 'AuthController@handleProviderCallback')->name('auth.callback');
    Route::get('{provider}', 'AuthController@redirectToProvider')->name('auth');
    Route::delete('{provider}', 'AuthController@destroy')->name('auth.destroy');
});

Route::get('/user/logins', 'UserController@logins')->name('user.logins');
