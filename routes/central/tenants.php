<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'tenants'], function () {
    Route::get('/', 'TenantController@index');
    Route::get('/{id}', 'TenantController@show');
    Route::post('/', 'TenantController@store');
    Route::put('/{id}', 'TenantController@update');
    Route::delete('/{id}', 'TenantController@delete');
});
