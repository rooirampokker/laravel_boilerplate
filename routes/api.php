<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::group(['namespace' => '\App\Http\Controllers'], function () {
  //Open routes...
  Route::match(array('GET', 'POST'), 'users/login', 'UserController@login')->name('users/login');
  Route::get('documentation', 'DocumentationController@index')->name('documentation');

  require 'passwords.php';

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
