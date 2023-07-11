<?php

use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'tenants'
], function () {
    Route::get('/', 'TenantController@index')->name('tenants.index');
    Route::get('/all', 'TenantController@indexAll')->name('tenants.index.all');
    Route::get('trashed', 'TenantController@indexTrashed')->name('tenants.index.trashed');
    Route::get('/{id}', 'TenantController@show')->name('tenants.show');
    Route::put('/{id}', 'TenantController@update')->name('tenants.update');
    Route::post('/', 'TenantController@store')->name('tenants.store');
    Route::patch('{id}', 'TenantController@restore')->name('tenants.restore');
    Route::delete('/{id}', 'TenantController@delete')->name('tenants.delete');
});
