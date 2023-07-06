<?php

/**
 * Default guard that Laravel uses to give the service authentication in the system
 * Allows Super-admin access to everything
 */

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Route;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
          Gate::before(function ($user) {
            return $user->hasRole('super-admin') ? true : null;
          });

        Route::group([
            'as' => 'passport.',
            'middleware' => [
                'universal',
                InitializeTenancyByDomain::class, // Use tenancy initialization middleware of your choice
            ],
            'prefix' => config('passport.path', 'oauth'),
            'namespace' => 'Laravel\Passport\Http\Controllers',
        ], function () {
            $this->loadRoutesFrom(base_path() . "/vendor/laravel/passport/routes/web.php");
        });
    }

    public function register()
    {
        Passport::ignoreRoutes();
    }
}
