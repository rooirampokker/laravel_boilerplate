<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/
Route::prefix(
    'v1'
)->namespace(
    '\App\Http\Controllers\api\v1'
)->group(function () {
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
        require 'roles.php';
    });
});
