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

// Admin Things
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function() {

    // User Management
    Route::get('user/refresh-names', 'UserController@refreshNames')->name('user.refresh-names');
    Route::delete('user/{user}/provider/{provider}', 'UserController@removeProvider')->name('user.provider-destroy');
    Route::resource('user', 'UserController', ['only' => ['index', 'edit', 'destroy']]);

    // Role Management
    Route::resource('role', 'RoleController');
    Route::group(['prefix' => 'role/{role}'], function() {
        Route::post('add-user', 'RoleController@addUser')->name('role.add-user');
        Route::delete('remove-user/{user}', 'RoleController@removeUser')->name('role.remove-user');
        Route::post('add-permission', 'RoleController@addPermission')->name('role.add-permission');
        Route::delete('remove-permission/{permission}', 'RoleController@removePermission')->name('role.remove-permission');
    });

    // Event Management
    Route::get('event/{event}/verify-destroy', 'EventController@verifyDestroy')->name('event.verify-destroy');
    Route::post('event/{event}/config', 'EventController@config')->name('event.config');
    Route::resource('event', 'EventController');
});

// User events
Route::get('event', 'EventController@index')->name('event.index');
Route::post('event/{event}/signup', 'EventController@signup')->name('event.signup');
Route::post('event/{event}/cancel', 'EventController@cancelSignup')->name('event.signup.cancel');

Route::get('/user/logins', 'UserController@logins')->name('user.logins');
Route::get('/user/settings', 'UserController@settings')->name('user.settings');
Route::post('/user/settings', 'UserController@updateSettings')->name('user.update-settings');
