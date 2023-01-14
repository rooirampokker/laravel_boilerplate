<?php
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'roles'], function () {
    Route::get('/', 'RoleController@index');
});

