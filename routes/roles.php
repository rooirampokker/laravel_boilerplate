<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', 'RoleController@index');
    Route::get('/{id}', 'RoleController@show');
    Route::post('/', 'RoleController@store');
    Route::put('/{id}', 'RoleController@update');
    Route::group([
        'prefix' => '/{role_id}/permissions'
    ], function() {
    Route::post('', 'RoleController@addPermission');
    Route::post('/sync', 'RoleController@syncPermission')->name('roles.syncPermission');
    Route::delete('/{permission_id}', 'RoleController@revokePermission');
    });
});
