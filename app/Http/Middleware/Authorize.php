<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Http\Middleware\App;
use Illuminate\Support\Str;

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
        $model = getModelNameFromRoute($request);
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
}
