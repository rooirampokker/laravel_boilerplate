<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Middleware\App;

class Authorize
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $controller = $request->route()->action["uses"];
        $model = $this->getModelName($request);
        $controllerAndMethod = preg_split("/[@]/", $controller);

        if (empty($controllerAndMethod)) {
            return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
        }
        $user = $request->user();

        if (!empty($user) && $user->can($controllerAndMethod[1], app($model))) {
            return $next($request);
        }

        return response()->json(['error' => __('auth.unauthorized')], httpStatusCode('UNAUTHORISED'));
    }

    private function getModelName($request) {
        $routePrefix = $request->route()->getPrefix(); //to be used as model
        $controllerAndMethod = explode("/", $routePrefix);
        $model = ucfirst(end($controllerAndMethod));

        return "App\Models\\".$model;
    }
}
