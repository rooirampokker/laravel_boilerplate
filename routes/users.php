<?php
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user'
], function() {
    Route::get('/', 'UserController@index')->name('user.index');
    Route::get('trashed', 'UserController@indexTrashed')->name('user.index.trashed');
    Route::post('/', 'UserController@store')->name('user.store');
    //Route::post('login', 'UserController@login');
    Route::get('logout', 'UserController@logout')->name('user.logout');
    Route::patch('{id}', 'UserController@restore')->name('user.restore');
    Route::put('{id}', 'UserController@update')->name('user.update');
    Route::get('{id}', 'UserController@show')->name('user.show');
    Route::delete('{id}', 'UserController@delete')->name('user.delete');

});
