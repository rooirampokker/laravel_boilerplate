<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'tenants'
], function () {
    Route::get('/', 'TenantController@index')->name('tenants.index');
    Route::get('/{id}', 'TenantController@show')->name('tenants.show');
    Route::put('/{id}', 'TenantController@update')->name('tenants.update');
    Route::post('/', 'TenantController@store')->name('tenants.store');
    Route::delete('/{id}', 'TenantController@delete')->name('tenants.delete');
});
