<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', 'RoleController@index');
    Route::get('/{id}', 'RoleController@show');
    Route::post('/', 'RoleController@store');
    Route::put('/{id}', 'RoleController@update');
});

