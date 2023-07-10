<?php
/*
 * Routes restricted to the central/landlord API
 */
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::group(['namespace' => '\App\Http\Controllers'], function () {
    Route::get('documentation', 'DocumentationController@index')->name('documentation');

    //Authenticated routes...
    Route::group([
        'middleware' => [
            'auth:api',
            'authorize',
        ],
    ], function () {
        require base_path().'/routes/central/tenants.php';
        require base_path().'/routes/users.php';
        require base_path().'/routes/user-data.php';
        require base_path().'/routes/roles.php';
    });
});
