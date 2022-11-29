<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => '\App\Http\Controllers'], function () {
  //Open routes...
  Route::match(array('GET', 'POST'), 'user/login', 'UserController@login')->name('user/login');

  Route::post('password/create', 'PasswordResetController@create');
  Route::get('password/find/{token}', 'PasswordResetController@find');
  Route::post('password/reset', 'PasswordResetController@reset');
  //Authenticated routes...
  Route::group(['middleware' => 'auth:api'], function () {
    Route::resource('user', 'UserController');
    Route::put('user/{id}/restore', 'UserController@restore')->name('user/restore');
  });
});
