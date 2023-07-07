<?php

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['namespace' => '\App\Http\Controllers'], function () {
  //Open routes...
    Route::match(array('GET', 'POST'), 'users/login', 'UserController@login')->name('users/login');

    require 'passwords.php';

  //Authenticated routes...
    Route::group([
      'middleware' => [
          InitializeTenancyByDomain::class,
          PreventAccessFromCentralDomains::class,
      ],
    ], function () {
        require 'users.php';
        require 'user-data.php';
        require 'roles.php';
    });
});
