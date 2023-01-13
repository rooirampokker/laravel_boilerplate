<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'users'
], function() {
    Route::get('/', 'UserController@index')->name('users.index');
    Route::get('/all', 'UserController@indexAll')->name('users.indexAll');
    Route::get('trashed', 'UserController@indexTrashed')->name('users.index.trashed');
    Route::post('/', 'UserController@store')->name('users.store');
    Route::post('/{id}/roles', 'UserController@addRole')->name('users.addRole');
    Route::delete('/{id}/roles', 'UserController@removeRole')->name('users.removeRole');
    //Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout')->name('users.logout');
    Route::patch('{id}', 'UserController@restore')->name('users.restore');
    Route::put('{id}', 'UserController@update')->name('users.update');
    Route::get('{id}', 'UserController@show')->name('users.show');
    Route::delete('{id}', 'UserController@delete')->name('users.delete');

});
