<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => '\App\Http\Controllers'], function () {
  //Open routes...
  Route::match(array('GET', 'POST'), 'user/login', 'UserController@login')->name('user/login');
  Route::group(['prefix' => 'password'], function () {
      Route::post('create', 'PasswordResetController@create');
      Route::get('find/{token}', 'PasswordResetController@find');
      Route::post('reset', 'PasswordResetController@reset');
  });

  //Authenticated routes...
  Route::group([
      'middleware' => [
          'auth:api',
          'authorize'
      ],
    ], function () {
        require 'users.php';
        require 'user-data.php';
  });


});
