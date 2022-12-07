<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'user-data'
], function () {
    Route::get('/', 'UserDataController@index')->name('user-data.index');
    Route::post('/', 'UserDataController@store')->name('user-data.store');
    Route::get('/{id}', 'UserDataController@show')->name('user-data.show');
    Route::put('/{id}', 'UserDataController@update')->name('user-data.update');
    Route::delete('/{id}', 'UserDataController@destroy')->name('user-data.destroy');
});
