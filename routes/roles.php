<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', 'RoleController@index');
    Route::get('/{id}', 'RoleController@show');
    Route::post('/', 'RoleController@store');
    Route::put('/{id}', 'RoleController@update');
    Route::post('/{role_id}/permissions', 'RoleController@addPermission');
    Route::delete('/{role_id}/permissions/{permission_id}', 'RoleController@revokePermission');
    Route::post('/{id}/permissions', 'RoleController@syncPermission');
});

